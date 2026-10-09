<?php

namespace Tests\Feature;

use App\Models\ClientProfile;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HttpInterfaceEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function public_home_page_and_portfolio_render_successfully()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('The Umer Store');
        $response->assertSee('Core Professional Services');

        $portfolioResponse = $this->get(route('portfolio'));
        $portfolioResponse->assertStatus(200);
        $portfolioResponse->assertSee('Enterprise ERP');
    }

    /** @test */
    public function contact_inquiry_can_be_submitted()
    {
        $response = $this->post(route('contact.post'), [
            'name' => 'John Consultant',
            'email' => 'john@example.com',
            'phone' => '+1 555 1234',
            'subject' => 'System Integration Inquiry',
            'message' => 'We require a new CRM and customer ticketing portal implementation.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'john@example.com',
            'subject' => 'System Integration Inquiry',
        ]);
    }

    /** @test */
    public function authenticated_client_can_create_and_view_support_tickets()
    {
        $clientUser = User::where('email', 'client@acme.com')->first();
        $category = TicketCategory::first();

        $this->actingAs($clientUser);

        // Access client dashboard
        $dashResponse = $this->get(route('client.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Acme Corporation Ltd.');

        // Submit new ticket
        $response = $this->post(route('client.tickets.store'), [
            'ticket_category_id' => $category->id,
            'subject' => 'Unable to generate monthly report',
            'priority' => 'High',
            'issue_type' => 'Bug Report',
            'description' => 'Exporting report results in blank screen',
        ]);

        $response->assertSessionHasNoErrors();
        $ticket = Ticket::where('subject', 'Unable to generate monthly report')->first();
        $this->assertNotNull($ticket);
        $response->assertRedirect(route('client.tickets.show', $ticket));

        // Client views their ticket
        $showResponse = $this->get(route('client.tickets.show', $ticket));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Unable to generate monthly report');
    }

    /** @test */
    public function admin_can_view_and_transition_tickets()
    {
        $admin = User::where('email', 'admin@theumer.store')->first();
        $client = ClientProfile::first();
        $openStatus = TicketStatus::where('slug', 'open')->first();
        $inProgressStatus = TicketStatus::where('slug', 'in-progress')->first();

        $ticket = Ticket::create([
            'ticket_number' => 'TKT-2026-000555',
            'client_profile_id' => $client->id,
            'ticket_status_id' => $openStatus->id,
            'subject' => 'Database latency anomaly',
            'description' => 'Response times degraded to 2500ms',
            'priority' => 'Critical',
        ]);

        $this->actingAs($admin);

        // Admin dashboard
        $dashResponse = $this->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('System Overview & Analytics');

        // Admin transitions ticket status
        $statusResponse = $this->post(route('admin.tickets.status', $ticket), [
            'ticket_status_id' => $inProgressStatus->id,
            'reason' => 'Database engineer inspecting slow log',
        ]);

        $statusResponse->assertRedirect();
        $ticket->refresh();
        $this->assertEquals('in-progress', $ticket->status->slug);
    }
}
