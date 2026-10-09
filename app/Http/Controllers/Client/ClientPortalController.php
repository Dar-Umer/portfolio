<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketStatus;
use App\Services\Common\DocumentNumberService;
use App\Services\Tickets\TicketWorkflowEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    private function getClientProfile()
    {
        $profile = auth()->user()->clientProfile;
        if (!$profile) {
            abort(403, 'Client profile not associated with this account.');
        }
        return $profile;
    }

    public function dashboard(): View
    {
        $client = $this->getClientProfile();

        $metrics = [
            'active_projects' => $client->projects()->whereIn('status', ['Planning', 'In Development', 'Testing', 'Client Review'])->count(),
            'open_tickets' => $client->tickets()->whereHas('status', fn($q) => $q->where('is_closed', false))->count(),
            'pending_quotations' => $client->quotations()->whereIn('status', ['Sent', 'Draft'])->count(),
            'outstanding_invoices' => $client->invoices()->where('balance_due', '>', 0)->count(),
            'outstanding_balance' => $client->invoices()->sum('balance_due'),
        ];

        $projects = $client->projects()->latest()->take(4)->get();
        $recentTickets = $client->tickets()->with('status')->latest()->take(5)->get();
        $invoices = $client->invoices()->latest()->take(5)->get();

        return view('client.dashboard', compact('client', 'metrics', 'projects', 'recentTickets', 'invoices'));
    }

    public function tickets(): View
    {
        $client = $this->getClientProfile();
        $tickets = $client->tickets()->with(['status', 'project', 'category'])->latest()->paginate(10);
        return view('client.tickets.index', compact('tickets'));
    }

    public function createTicket(): View
    {
        $client = $this->getClientProfile();
        $projects = $client->projects()->get();
        $categories = TicketCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('client.tickets.create', compact('projects', 'categories'));
    }

    public function storeTicket(Request $request): RedirectResponse
    {
        $client = $this->getClientProfile();

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'ticket_category_id' => ['nullable', 'exists:ticket_categories,id'],
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:Low,Normal,High,Critical'],
            'issue_type' => ['required', 'string'],
            'description' => ['required', 'string'],
            'steps_to_reproduce' => ['nullable', 'string'],
            'expected_result' => ['nullable', 'string'],
            'actual_result' => ['nullable', 'string'],
        ]);

        $ticketNumber = DocumentNumberService::generate('TKT', 'tickets', 'ticket_number');
        $initialStatus = TicketStatus::where('is_initial', true)->first() ?? TicketStatus::first();
        $sla = TicketWorkflowEngine::calculateSla($validated['priority']);

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'client_profile_id' => $client->id,
            'project_id' => $validated['project_id'] ?? null,
            'ticket_category_id' => $validated['ticket_category_id'] ?? null,
            'ticket_status_id' => $initialStatus->id,
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'],
            'steps_to_reproduce' => $validated['steps_to_reproduce'] ?? null,
            'expected_result' => $validated['expected_result'] ?? null,
            'actual_result' => $validated['actual_result'] ?? null,
            'first_response_due_at' => $sla['first_response_due_at'],
            'resolution_due_at' => $sla['resolution_due_at'],
        ]);

        return redirect()->route('client.tickets.show', $ticket)->with('success', "Ticket {$ticket->ticket_number} created successfully.");
    }

    public function showTicket(Ticket $ticket): View
    {
        $client = $this->getClientProfile();

        if ($ticket->client_profile_id !== $client->id) {
            abort(403, 'Unauthorized access to ticket.');
        }

        // Strict customer privacy: only non-internal comments are loaded
        $comments = $ticket->comments()
            ->where('is_internal_note', false)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('client.tickets.show', compact('ticket', 'comments'));
    }

    public function replyTicket(Request $request, Ticket $ticket): RedirectResponse
    {
        $client = $this->getClientProfile();

        if ($ticket->client_profile_id !== $client->id) {
            abort(403, 'Unauthorized access to ticket.');
        }

        $request->validate([
            'comment' => ['required', 'string'],
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'is_internal_note' => false,
        ]);

        return back()->with('success', 'Reply posted successfully.');
    }

    // Client Projects View
    public function projects(): View
    {
        $client = $this->getClientProfile();
        $projects = $client->projects()->with(['milestones' => fn($q) => $q->where('is_client_visible', true)])->latest()->paginate(10);
        return view('client.projects.index', compact('projects'));
    }

    public function showProject(Project $project): View
    {
        $client = $this->getClientProfile();

        if ($project->client_profile_id !== $client->id) {
            abort(403, 'Unauthorized access to project.');
        }

        $project->load([
            'milestones' => fn($q) => $q->where('is_client_visible', true),
            'timelineEvents' => fn($q) => $q->where('is_client_visible', true),
            'tickets.status',
        ]);

        return view('client.projects.show', compact('project'));
    }

    // Client Quotations View
    public function quotations(): View
    {
        $client = $this->getClientProfile();
        $quotations = $client->quotations()->latest()->paginate(10);
        return view('client.quotations.index', compact('quotations'));
    }

    public function showQuotation(Quotation $quotation): View
    {
        $client = $this->getClientProfile();

        if ($quotation->client_profile_id !== $client->id) {
            abort(403, 'Unauthorized access to quotation.');
        }

        $quotation->load(['items', 'project']);
        return view('client.quotations.show', compact('quotation'));
    }

    public function acceptQuotation(Quotation $quotation): RedirectResponse
    {
        $client = $this->getClientProfile();

        if ($quotation->client_profile_id !== $client->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($quotation->status !== 'Sent') {
            return back()->with('error', 'Quotation cannot be accepted in its current state.');
        }

        $quotation->update([
            'status' => 'Accepted',
            'accepted_at' => now(),
            'accepted_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Thank you! You have accepted the quotation.');
    }

    // Client Invoices View
    public function invoices(): View
    {
        $client = $this->getClientProfile();
        $invoices = $client->invoices()->latest()->paginate(10);
        return view('client.invoices.index', compact('invoices'));
    }

    public function showInvoice(Invoice $invoice): View
    {
        $client = $this->getClientProfile();

        if ($invoice->client_profile_id !== $client->id) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load(['items', 'allocations.payment']);
        return view('client.invoices.show', compact('invoice'));
    }
}
