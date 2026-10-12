<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\ProjectTimelineEvent;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketStatus;
use App\Services\Audit\AuditService;
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

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'is_internal_note' => false,
        ]);

        TicketWorkflowEngine::handleCustomerReply($ticket, $comment, auth()->user());

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
            'milestones' => fn($q) => $q->where('is_client_visible', true)->orderBy('sort_order'),
            'requirements' => fn($q) => $q->latest(),
            'timelineEvents' => fn($q) => $q->where('is_client_visible', true)->latest(),
            'tickets.status',
        ]);

        return view('client.projects.show', compact('project'));
    }

    public function storeRequirement(Request $request, Project $project): RedirectResponse
    {
        $client = $this->getClientProfile();

        if ($project->client_profile_id !== $client->id) {
            abort(403, 'Unauthorized access to project.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'details' => ['required', 'string'],
            'priority' => ['required', 'in:Low,Medium,High,Critical'],
        ]);

        $requirement = $project->requirements()->create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'details' => $validated['details'],
            'priority' => $validated['priority'],
            'status' => 'Submitted',
        ]);

        // Record timeline event
        \App\Models\ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'requirement_requested',
            'title' => "Client Requested Feature: {$requirement->title}",
            'description' => "Requirement registered with {$requirement->priority} priority.",
            'is_client_visible' => true,
        ]);

        \App\Services\Audit\AuditService::log('client_requirement_submitted', $project, "Client submitted requirement: {$requirement->title}");

        return back()->with('success', 'Your deliverable requirement has been submitted. Our engineering team will review and estimate it.');
    }

    public function approveMilestone(Request $request, Project $project, \App\Models\ProjectMilestone $milestone): RedirectResponse
    {
        $client = $this->getClientProfile();

        if ($project->client_profile_id !== $client->id || $milestone->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'client_review_note' => ['nullable', 'string'],
        ]);

        $milestone->update([
            'status' => 'completed',
            'progress' => 100,
            'completion_date' => now(),
            'client_approved_at' => now(),
            'client_review_note' => $request->client_review_note ?: 'Approved by Client via Client Portal',
        ]);

        // Recalculate project progress
        $totalMilestones = $project->milestones()->count();
        if ($totalMilestones > 0) {
            $avgProgress = (int) round($project->milestones()->avg('progress'));
            $project->update(['progress_percentage' => $avgProgress]);
            if ($avgProgress === 100) {
                $project->update(['status' => 'Completed', 'actual_completion_date' => now()]);
            }
        }

        \App\Models\ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'milestone_completed',
            'title' => "Client Approved Deliverable: {$milestone->title}",
            'description' => "Customer approved milestone completion. " . ($request->client_review_note ? "Feedback: {$request->client_review_note}" : ''),
            'is_client_visible' => true,
        ]);

        \App\Services\Audit\AuditService::log('milestone_client_approved', $project, "Client approved milestone: {$milestone->title}");

        return back()->with('success', "Deliverable '{$milestone->title}' marked as Approved and Completed!");
    }

    public function requestMilestoneRevision(Request $request, Project $project, \App\Models\ProjectMilestone $milestone): RedirectResponse
    {
        $client = $this->getClientProfile();

        if ($project->client_profile_id !== $client->id || $milestone->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'revision_reason' => ['required', 'string'],
        ]);

        $milestone->update([
            'status' => 'in_progress',
            'client_review_note' => "Revision Requested: " . $request->revision_reason,
            'client_approved_at' => null,
        ]);

        \App\Models\ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'event_type' => 'revision_requested',
            'title' => "Revision Requested: {$milestone->title}",
            'description' => $request->revision_reason,
            'is_client_visible' => true,
        ]);

        \App\Services\Audit\AuditService::log('milestone_revision_requested', $project, "Client requested revision on milestone: {$milestone->title}");

        return back()->with('warning', "Revision feedback sent to engineering team for deliverable '{$milestone->title}'.");
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

        if ($quotation->project_id) {
            ProjectTimelineEvent::create([
                'project_id' => $quotation->project_id,
                'user_id' => auth()->id(),
                'event_type' => 'quotation_accepted',
                'title' => "Quotation Accepted: {$quotation->quotation_number}",
                'description' => "Quotation for {$quotation->grand_total} {$quotation->currency} accepted by client.",
                'is_client_visible' => true,
            ]);
        }

        AuditService::log('quotation_accepted_by_client', $quotation, "Client accepted quotation {$quotation->quotation_number}");

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
