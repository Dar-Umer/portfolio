<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\ContactMessage;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Ticket;
use App\Services\Tickets\TicketWorkflowEngine;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // Proactively flag any tickets that breached SLA deadlines
        TicketWorkflowEngine::checkAndFlagSlaBreaches();

        $metrics = [
            'total_clients' => ClientProfile::count(),
            'active_projects' => Project::whereIn('status', ['Planning', 'In Development', 'Testing', 'Client Review'])->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(),
            'open_tickets' => Ticket::whereHas('status', fn($q) => $q->where('is_closed', false))->count(),
            'critical_tickets' => Ticket::where('priority', 'Critical')->whereHas('status', fn($q) => $q->where('is_closed', false))->count(),
            'unread_inquiries' => ContactMessage::where('is_read', false)->count(),
            'pending_quotations' => Quotation::whereIn('status', ['Draft', 'Sent'])->count(),
            'total_invoiced' => Invoice::sum('grand_total'),
            'total_collected' => Payment::where('status', 'completed')->sum('amount'),
            'outstanding_receivables' => Invoice::sum('balance_due'),
        ];

        $recentTickets = Ticket::with(['client.user', 'status', 'project'])
            ->latest()
            ->take(6)
            ->get();

        $recentPayments = Payment::with(['client.user'])
            ->latest()
            ->take(5)
            ->get();

        $recentProjects = Project::with(['client.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'recentTickets', 'recentPayments', 'recentProjects'));
    }
}
