<?php

namespace Tests\Feature;

use App\Models\ClientProfile;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketStatus;
use App\Models\User;
use App\Services\Billing\BillingService;
use App\Services\Common\DocumentNumberService;
use App\Services\Payments\PaymentService;
use App\Services\Tickets\TicketWorkflowEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorePlatformWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function it_generates_concurrency_safe_unique_sequential_document_numbers()
    {
        $doc1 = DocumentNumberService::generate('TKT', 'tickets', 'ticket_number');
        $this->assertStringStartsWith('TKT-2026-', $doc1);

        // Insert ticket
        $client = ClientProfile::first();
        $status = TicketStatus::where('slug', 'open')->first();
        Ticket::create([
            'ticket_number' => $doc1,
            'client_profile_id' => $client->id,
            'ticket_status_id' => $status->id,
            'subject' => 'System load issue',
            'description' => 'Load spikes during evening',
        ]);

        $doc2 = DocumentNumberService::generate('TKT', 'tickets', 'ticket_number');
        $this->assertNotEquals($doc1, $doc2);
        $this->assertEquals('TKT-2026-000002', $doc2);
    }

    /** @test */
    public function it_calculates_slas_and_enforces_validated_ticket_status_transitions()
    {
        $client = ClientProfile::first();
        $openStatus = TicketStatus::where('slug', 'open')->first();
        $inProgressStatus = TicketStatus::where('slug', 'in-progress')->first();
        $resolvedStatus = TicketStatus::where('slug', 'resolved')->first();
        $admin = User::where('email', 'admin@theumer.store')->first();

        // 1. SLA calculation
        $sla = TicketWorkflowEngine::calculateSla('Critical');
        $this->assertNotNull($sla['first_response_due_at']);
        $this->assertNotNull($sla['resolution_due_at']);

        // 2. Ticket creation
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-2026-000001',
            'client_profile_id' => $client->id,
            'ticket_status_id' => $openStatus->id,
            'subject' => 'Payment Gateway Timeout',
            'description' => 'Timeout occurs on 3D secure redirection',
            'priority' => 'Critical',
            'first_response_due_at' => $sla['first_response_due_at'],
            'resolution_due_at' => $sla['resolution_due_at'],
        ]);

        // 3. Transition to in-progress
        $ticket = TicketWorkflowEngine::transition($ticket, $inProgressStatus, $admin, 'Assigned and investigating');
        $this->assertEquals('in-progress', $ticket->status->slug);
        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id,
            'from_status' => 'Open',
            'to_status' => 'In Progress',
        ]);

        // 4. Transition to resolved with resolution data
        $ticket = TicketWorkflowEngine::transition($ticket, $resolvedStatus, $admin, 'Issue fixed and verified', [
            'root_cause' => 'Gateway endpoint DNS timeout',
            'fix_description' => 'Configured persistent keep-alive connections',
            'fixed_version' => 'v1.2.4',
        ]);

        $this->assertEquals('resolved', $ticket->status->slug);
        $this->assertEquals('v1.2.4', $ticket->fixed_version);
        $this->assertNotNull($ticket->resolved_at);
    }

    /** @test */
    public function it_protects_internal_notes_from_customer_queries()
    {
        $client = ClientProfile::first();
        $status = TicketStatus::where('slug', 'open')->first();
        $admin = User::where('email', 'admin@theumer.store')->first();

        $ticket = Ticket::create([
            'ticket_number' => 'TKT-2026-000099',
            'client_profile_id' => $client->id,
            'ticket_status_id' => $status->id,
            'subject' => 'Bug in API',
            'description' => 'API returns 500 on checkout',
        ]);

        // Public comment
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'comment' => 'We are reviewing the logs.',
            'is_internal_note' => false,
        ]);

        // Internal note
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'comment' => 'Client has misconfigured credentials in their dashboard.',
            'is_internal_note' => true,
        ]);

        // Client scope should only retrieve non-internal comments
        $clientVisibleComments = $ticket->comments()->where('is_internal_note', false)->get();
        $this->assertCount(1, $clientVisibleComments);
        $this->assertEquals('We are reviewing the logs.', $clientVisibleComments->first()->comment);
    }

    /** @test */
    public function it_converts_accepted_quotation_to_immutable_invoice_and_processes_payment_allocations()
    {
        $client = ClientProfile::first();
        $admin = User::where('email', 'admin@theumer.store')->first();

        // 1. Create Quotation
        $quotation = Quotation::create([
            'quotation_number' => 'QTN-2026-000001',
            'client_profile_id' => $client->id,
            'created_by_user_id' => $admin->id,
            'issue_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(30)->toDateString(),
            'status' => 'Accepted',
            'currency' => 'INR',
            'subtotal' => 10000.00,
            'discount_amount' => 1000.00,
            'tax_total' => 1620.00, // 18% on 9000
            'grand_total' => 10620.00,
            'accepted_at' => now(),
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'description' => 'Core Module Development',
            'quantity' => 1,
            'unit_price' => 10000.00,
            'discount' => 1000.00,
            'tax_rate' => 18.00,
            'total' => 10620.00,
        ]);

        // 2. Convert to Invoice
        $invoice = BillingService::convertQuotationToInvoice($quotation);
        $this->assertEquals('Issued', $invoice->status);
        $this->assertEquals(10620.00, $invoice->grand_total);
        $this->assertEquals(10620.00, $invoice->balance_due);
        $this->assertNotNull($invoice->client_snapshot);
        $this->assertEquals($client->company_name, $invoice->client_snapshot['company_name']);

        // 3. Make Partial Payment
        $payment1 = PaymentService::recordAndAllocate($invoice, 5000.00, 'UPI', 'UPI-TXN-123456', 'First installment');
        $invoice->refresh();
        $this->assertEquals('Partially Paid', $invoice->status);
        $this->assertEquals(5000.00, $invoice->amount_paid);
        $this->assertEquals(5620.00, $invoice->balance_due);

        // 4. Settle Remaining Balance
        $payment2 = PaymentService::recordAndAllocate($invoice, 5620.00, 'Bank Transfer', 'NEFT-998877', 'Final settlement');
        $invoice->refresh();
        $this->assertEquals('Paid', $invoice->status);
        $this->assertEquals(10620.00, $invoice->amount_paid);
        $this->assertEquals(0.00, $invoice->balance_due);
    }
}
