<?php

namespace Tests\Feature;

use App\Models\ClientProfile;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HttpInterfaceEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function public_home_page_and_portfolio_render_successfully(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('The Umer Store');
        $response->assertSee('Core Professional Services');

        $portfolioResponse = $this->get(route('portfolio'));
        $portfolioResponse->assertStatus(200);
        $portfolioResponse->assertSee('Enterprise ERP');
    }

    #[Test]
    public function contact_inquiry_can_be_submitted_and_managed_by_admin(): void
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

        $inquiry = ContactMessage::where('email', 'john@example.com')->first();
        $this->assertNotNull($inquiry);
        $this->assertFalse($inquiry->is_read);

        // Admin logs in and views inquiries
        $admin = User::where('email', 'admin@theumer.store')->first();
        $this->actingAs($admin);

        $listResponse = $this->get(route('admin.inquiries.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('John Consultant');

        // Admin marks inquiry as read
        $markResponse = $this->post(route('admin.inquiries.markRead', $inquiry));
        $markResponse->assertRedirect();
        $this->assertTrue($inquiry->fresh()->is_read);
    }

    #[Test]
    public function authenticated_client_can_create_and_view_support_tickets(): void
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

    #[Test]
    public function developer_role_can_access_admin_dashboard_and_tickets(): void
    {
        $dev = User::where('email', 'dev@theumer.store')->first();
        $this->actingAs($dev);

        // Developer should access admin dashboard
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Developer should view tickets
        $ticketsResponse = $this->get(route('admin.tickets.index'));
        $ticketsResponse->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_and_transition_tickets(): void
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

        // Admin transitions ticket status
        $statusResponse = $this->post(route('admin.tickets.status', $ticket), [
            'ticket_status_id' => $inProgressStatus->id,
            'reason' => 'Database engineer inspecting slow log',
        ]);

        $statusResponse->assertRedirect();
        $ticket->refresh();
        $this->assertEquals('in-progress', $ticket->status->slug);
    }

    #[Test]
    public function admin_can_update_milestone_status_and_trigger_progress_recalculation(): void
    {
        $admin = User::where('email', 'admin@theumer.store')->first();
        $client = ClientProfile::first();

        $project = Project::create([
            'client_profile_id' => $client->id,
            'project_number' => 'PRJ-2026-000200',
            'name' => 'Mobile App API',
            'slug' => 'mobile-app-api-test',
            'category' => 'API',
            'status' => 'In Development',
            'priority' => 'Normal',
            'progress_percentage' => 0,
            'budget' => 30000.00,
        ]);

        $milestone = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Authentication Module',
            'status' => 'pending',
            'progress' => 0,
            'is_client_visible' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.projects.milestones.status', [$project, $milestone]), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $milestone->refresh();
        $project->refresh();

        $this->assertEquals('completed', $milestone->status);
        $this->assertEquals(100, $project->progress_percentage);
    }

    #[Test]
    public function admin_can_view_edit_client_and_update_profile(): void
    {
        $admin = User::where('email', 'admin@theumer.store')->first();
        $client = ClientProfile::first();

        $this->actingAs($admin);

        // Edit client page renders
        $editResponse = $this->get(route('admin.clients.edit', $client));
        $editResponse->assertStatus(200);
        $editResponse->assertSee($client->client_number);

        // Update client profile
        $updateResponse = $this->put(route('admin.clients.update', $client), [
            'name' => 'Updated Client Corp Head',
            'company_name' => 'Acme Global Ventures',
            'phone' => '+91 9999888877',
            'billing_address' => 'Floor 12, Tower B, Cyber City',
            'city' => 'Gurugram',
            'state' => 'Haryana',
            'postal_code' => '122002',
            'country' => 'India',
            'tax_number' => '07AAAAA0000A1Z5',
            'client_category' => 'Enterprise',
            'account_status' => 'active',
            'notes' => 'Priority strategic partner',
        ]);

        $updateResponse->assertRedirect(route('admin.clients.show', $client));
        $client->refresh();
        $this->assertEquals('Acme Global Ventures', $client->company_name);
        $this->assertEquals('Updated Client Corp Head', $client->user->name);
    }

    #[Test]
    public function admin_can_access_all_cms_views_and_subnavigation(): void
    {
        $admin = User::where('email', 'admin@theumer.store')->first();
        $this->actingAs($admin);

        // Homepage CMS
        $homeResponse = $this->get(route('admin.cms.homepage'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Homepage Dynamic Sections');
        $homeResponse->assertSee('Services Directory');

        // Services CMS
        $servicesResponse = $this->get(route('admin.cms.services'));
        $servicesResponse->assertStatus(200);
        $servicesResponse->assertSee('Services Directory');

        // Portfolio CMS
        $portfolioResponse = $this->get(route('admin.cms.portfolio'));
        $portfolioResponse->assertStatus(200);
        $portfolioResponse->assertSee('Public Portfolio Showcase');
    }

    #[Test]
    public function admin_can_access_reports_and_export_csv(): void
    {
        $admin = User::where('email', 'admin@theumer.store')->first();
        $this->actingAs($admin);

        $reportsResponse = $this->get(route('admin.reports.index'));
        $reportsResponse->assertStatus(200);
        $reportsResponse->assertSee('Monthly Realization Trend');

        $csvResponse = $this->get(route('admin.reports.export', 'invoices'));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    #[Test]
    public function admin_can_view_project_create_page_and_submit_new_project(): void
    {
        $admin = User::where('email', 'admin@theumer.store')->first();
        $client = ClientProfile::first();
        $this->actingAs($admin);

        // 1. Projects index shows Create Project button
        $indexResponse = $this->get(route('admin.projects.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Create Project');

        // 2. Projects create page renders with preselected client
        $createResponse = $this->get(route('admin.projects.create', ['client_id' => $client->id]));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Preselected Client:');
        $createResponse->assertSee($client->client_number);

        // 3. Admin submits the project creation form
        $postResponse = $this->post(route('admin.projects.store'), [
            'client_profile_id' => $client->id,
            'name' => 'High Scale Microservices Portal',
            'category' => 'Web Application',
            'description' => 'Complete re-architecture of customer checkout pipeline',
            'start_date' => '2026-10-15',
            'expected_delivery_date' => '2026-12-15',
            'priority' => 'Critical',
            'budget' => 125000.00,
            'technologies' => 'Laravel 12, MySQL, Redis, Docker',
            'internal_notes' => 'Dedicated engineering sprint',
        ]);

        $project = Project::where('name', 'High Scale Microservices Portal')->first();
        $this->assertNotNull($project);
        $postResponse->assertRedirect(route('admin.projects.show', $project));
        $this->assertEquals($client->id, $project->client_profile_id);
        $this->assertEquals('Planning', $project->status);
        $this->assertEquals(0, $project->progress_percentage);
    }

    #[Test]
    public function admin_can_view_client_create_and_quotation_create_without_wildcard_collision(): void
    {
        $admin = User::where('email', 'admin@theumer.store')->first();
        $this->actingAs($admin);

        // 1. Client create route resolves to create view (200), not {client} wildcard (404)
        $clientCreateResponse = $this->get(route('admin.clients.create'));
        $clientCreateResponse->assertStatus(200);
        $clientCreateResponse->assertSee('Onboard New Customer Account');

        // 2. Quotation create route resolves to create view (200), not {quotation} wildcard (404)
        $quotationCreateResponse = $this->get(route('admin.quotations.create'));
        $quotationCreateResponse->assertStatus(200);
        $quotationCreateResponse->assertSee('Create Commercial Quotation');
    }
}
