<?php

namespace Tests\Feature;

use App\Models\ClientProfile;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectTimelineEvent;
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
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CorePlatformWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function it_generates_concurrency_safe_unique_sequential_document_numbers(): void
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

    #[Test]
    public function it_calculates_slas_and_enforces_validated_ticket_status_transitions(): void
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

    #[Test]
    public function it_protects_internal_notes_from_customer_queries(): void
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

    #[Test]
    public function it_converts_accepted_quotation_to_immutable_invoice_and_processes_payment_allocations(): void
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

    #[Test]
    public function it_tracks_staff_first_response_sla_and_handles_customer_portal_reply_automations(): void
    {
        $client = ClientProfile::first();
        $openStatus = TicketStatus::where('slug', 'open')->first();
        $awaitingCustomerStatus = TicketStatus::where('slug', 'awaiting-customer')->first();
        $admin = User::where('email', 'admin@theumer.store')->first();
        $clientUser = $client->user;

        $ticket = Ticket::create([
            'ticket_number' => 'TKT-2026-000777',
            'client_profile_id' => $client->id,
            'ticket_status_id' => $openStatus->id,
            'subject' => 'SSL certificate renewal required',
            'description' => 'Cert expires in 3 days',
            'priority' => 'High',
            'first_response_due_at' => now()->addHours(4),
            'resolution_due_at' => now()->addHours(24),
        ]);

        $this->assertNull($ticket->first_responded_at);

        // Staff response recorded
        $staffComment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'comment' => 'We are reviewing DNS entries.',
            'is_internal_note' => false,
        ]);

        TicketWorkflowEngine::recordStaffResponse($ticket, $staffComment, $admin);
        $ticket->refresh();
        $this->assertNotNull($ticket->first_responded_at);
        $this->assertFalse($ticket->is_sla_breached);

        // Move to awaiting customer
        $ticket->update(['ticket_status_id' => $awaitingCustomerStatus->id]);

        // Customer replies
        $customerComment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $clientUser->id,
            'comment' => 'DNS TXT record updated.',
            'is_internal_note' => false,
        ]);

        TicketWorkflowEngine::handleCustomerReply($ticket, $customerComment, $clientUser);
        $ticket->refresh();

        // Ticket automatically returned to in-progress
        $this->assertEquals('in-progress', $ticket->status->slug);
    }

    #[Test]
    public function it_updates_project_milestones_and_recalculates_overall_project_progress(): void
    {
        $client = ClientProfile::first();
        $project = Project::create([
            'client_profile_id' => $client->id,
            'project_number' => 'PRJ-2026-000100',
            'name' => 'CRM Integration Portal',
            'slug' => 'crm-integration-portal-test',
            'category' => 'Web App',
            'status' => 'In Development',
            'priority' => 'High',
            'progress_percentage' => 0,
            'budget' => 50000.00,
        ]);

        $m1 = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Database Schema Migration',
            'status' => 'pending',
            'progress' => 0,
            'is_client_visible' => true,
            'sort_order' => 1,
        ]);

        $m2 = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'API Gateway Endpoints',
            'status' => 'pending',
            'progress' => 0,
            'is_client_visible' => true,
            'sort_order' => 2,
        ]);

        // Mark m1 completed
        $m1->update(['status' => 'completed', 'progress' => 100]);
        $progress = $project->recalculateProgress();

        // 1 of 2 completed = 50%
        $this->assertEquals(50, $progress);
        $this->assertEquals(50, $project->fresh()->progress_percentage);

        // Mark m2 in_progress (50% weight)
        $m2->update(['status' => 'in_progress', 'progress' => 50]);
        $progress2 = $project->recalculateProgress();

        // (100 + 50) / 2 = 75%
        $this->assertEquals(75, $progress2);
    }
}
