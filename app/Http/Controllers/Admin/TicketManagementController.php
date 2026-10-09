<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketStatus;
use App\Models\User;
use App\Services\Audit\AuditService;
use App\Services\Tickets\TicketWorkflowEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::with(['client.user', 'project', 'status', 'category', 'assignedTo']);

        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('slug', $request->status));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();
        $statuses = TicketStatus::orderBy('sort_order')->get();

        return view('admin.tickets.index', compact('tickets', 'statuses'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'client.user',
            'project',
            'status',
            'category',
            'assignedTo',
            'comments.user',
            'statusHistories.changedBy',
            'githubLinks',
        ]);

        $statuses = TicketStatus::orderBy('sort_order')->get();
        $developers = User::role(['Super Admin', 'Administrator', 'Developer'])->get();

        return view('admin.tickets.show', compact('ticket', 'statuses', 'developers'));
    }

    public function addComment(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'comment' => ['required', 'string'],
            'is_internal_note' => ['nullable', 'boolean'],
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'is_internal_note' => $request->boolean('is_internal_note'),
        ]);

        AuditService::log(
            'ticket_reply',
            $ticket,
            $request->boolean('is_internal_note') ? "Added internal note on ticket {$ticket->ticket_number}" : "Replied publicly on ticket {$ticket->ticket_number}"
        );

        return back()->with('success', 'Comment posted successfully.');
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'ticket_status_id' => ['required', 'exists:ticket_statuses,id'],
            'reason' => ['nullable', 'string'],
            'root_cause' => ['nullable', 'string'],
            'fix_description' => ['nullable', 'string'],
            'fixed_version' => ['nullable', 'string'],
            'customer_instructions' => ['nullable', 'string'],
        ]);

        $newStatus = TicketStatus::findOrFail($request->ticket_status_id);

        try {
            TicketWorkflowEngine::transition(
                $ticket,
                $newStatus,
                auth()->user(),
                $request->reason,
                $request->only(['root_cause', 'fix_description', 'fixed_version', 'customer_instructions'])
            );

            return back()->with('success', "Ticket status updated to {$newStatus->name}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $ticket->update([
            'assigned_to_user_id' => $request->assigned_to_user_id,
        ]);

        $assignee = User::find($request->assigned_to_user_id);
        AuditService::log(
            'ticket_assignment',
            $ticket,
            $assignee ? "Assigned ticket {$ticket->ticket_number} to {$assignee->name}" : "Unassigned ticket {$ticket->ticket_number}"
        );

        return back()->with('success', 'Ticket assignment updated.');
    }
}
