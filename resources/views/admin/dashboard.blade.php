@extends('layouts.admin')

@section('page_title', 'System Overview & Executive Dashboard')

@section('content')
    <!-- Dashboard Hero Greeting -->
    <div class="card border-0 shadow-sm p-4 bg-white mb-4 position-relative overflow-hidden">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge badge-soft-success px-2 py-1"><span class="status-dot bg-success"></span> Production Active</span>
                    <span class="text-muted small">&bull; {{ date('l, d F Y') }}</span>
                </div>
                <h4 class="fw-bold text-dark mb-1">Welcome back, {{ auth()->user()->name }}</h4>
                <p class="text-muted small mb-0">Here is your consolidated operational snapshot across clients, active sprints, support tickets, and revenue realization.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-inline-flex gap-2">
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                        <i class="fas fa-plus"></i> New Project
                    </a>
                    @hasanyrole('Super Admin|Administrator')
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-light btn-sm border">
                        <i class="fas fa-chart-pie me-1"></i> Reports
                    </a>
                    @endhasanyrole
                </div>
            </div>
        </div>
    </div>

    @if(($metrics['unread_inquiries'] ?? 0) > 0)
        <!-- High-Priority Customer Inquiry Lead Banner -->
        <div class="card border-0 shadow-sm mb-4 bg-primary text-white" style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%) !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fas fa-envelope-open-text fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $metrics['unread_inquiries'] }} Unread Customer Inquiries Awaiting Response</div>
                        <div class="text-white-50 small">New prospective client leads submitted from public portfolio contact form.</div>
                    </div>
                </div>
                <a href="{{ route('admin.inquiries.index') }}" class="btn btn-light btn-sm fw-semibold text-primary px-3 shadow-sm">
                    Review Inquiries <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    @endif

    <!-- Key Performance Indicators (KPIs) -->
    <div class="row g-3 mb-4">
        <!-- Customers -->
        <div class="col-sm-6 col-xl-3">
            <div class="card-stat">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Total Customers</div>
                        <h3 class="fw-bold text-dark mt-2 mb-1">{{ $metrics['total_clients'] }}</h3>
                        <div class="text-muted small"><span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Active</span> CRM profiles</div>
                    </div>
                    <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="col-sm-6 col-xl-3">
            <div class="card-stat border-green">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Active Projects</div>
                        <h3 class="fw-bold text-success mt-2 mb-1">{{ $metrics['active_projects'] }}</h3>
                        <div class="text-muted small"><span class="fw-semibold text-dark">{{ $metrics['completed_projects'] }}</span> delivered & signoff</div>
                    </div>
                    <div class="stat-icon-wrapper bg-success-subtle text-success">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desk Open Tickets -->
        <div class="col-sm-6 col-xl-3">
            <div class="card-stat border-yellow">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Support Queue</div>
                        <h3 class="fw-bold text-warning mt-2 mb-1">{{ $metrics['open_tickets'] }}</h3>
                        <div class="text-muted small">Open SLA tracked tickets</div>
                    </div>
                    <div class="stat-icon-wrapper bg-warning-subtle text-warning">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Issues -->
        <div class="col-sm-6 col-xl-3">
            <div class="card-stat border-red">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">Critical Escalations</div>
                        <h3 class="fw-bold text-danger mt-2 mb-1">{{ $metrics['critical_tickets'] }}</h3>
                        <div class="text-danger small fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Urgent attention</div>
                    </div>
                    <div class="stat-icon-wrapper bg-danger-subtle text-danger">
                        <i class="fas fa-fire"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Realization Ledger Card -->
    <div class="card border-0 shadow-sm bg-white mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chart-line text-success"></i>
                <span class="fw-bold text-dark">Commercial Billing & Realization Health</span>
            </div>
            @php
                $collectionRate = $metrics['total_invoiced'] > 0 
                    ? round(($metrics['total_collected'] / $metrics['total_invoiced']) * 100, 1) 
                    : 0;
            @endphp
            <span class="badge badge-soft-success px-2 py-1">
                Realization Rate: {{ $collectionRate }}%
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-4 align-items-center">
                <div class="col-md-4 border-end">
                    <span class="text-muted small fw-semibold text-uppercase">Total Invoiced</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">₹{{ number_format($metrics['total_invoiced'], 2) }}</h3>
                    <div class="text-muted small mt-1">Gross commercial tax billing</div>
                </div>
                <div class="col-md-4 border-end">
                    <span class="text-success small fw-semibold text-uppercase">Realized Payments</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">₹{{ number_format($metrics['total_collected'], 2) }}</h3>
                    <div class="text-success small mt-1">100% verified ledger allocations</div>
                </div>
                <div class="col-md-4">
                    <span class="text-danger small fw-semibold text-uppercase">Receivables Outstanding</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1">₹{{ number_format($metrics['outstanding_receivables'], 2) }}</h3>
                    <div class="text-danger small mt-1">Pending client collections</div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center mb-1 small">
                    <span class="text-muted">Collection Realization Progress</span>
                    <span class="fw-bold text-success">{{ $collectionRate }}% Collected</span>
                </div>
                <div class="progress" style="height: 7px; background-color: #f1f5f9; border-radius: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $collectionRate) }}%; border-radius: 6px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Tickets & Collections Split -->
    <div class="row g-4">
        <!-- Tickets Desk -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-ticket-alt text-primary"></i>
                        <span class="fw-bold text-dark">Recent Support Tickets & SLA Performance</span>
                    </div>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-primary">
                        View All Tickets &rarr;
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Subject</th>
                                <th>Client</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets as $ticket)
                                <tr>
                                    <td><span class="fw-bold font-monospace text-primary">{{ $ticket->ticket_number }}</span></td>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 220px;">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-dark text-decoration-none">
                                                {{ $ticket->subject }}
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">{{ $ticket->client->company_name ?? $ticket->client->user->name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ticket->priority === 'Critical' ? 'bg-danger' : ($ticket->priority === 'High' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $ticket->status->color }}">
                                            {{ $ticket->status->name }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-light border py-1 px-2">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small">No tickets currently filed.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Realized Collections -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-receipt text-success"></i>
                        <span class="fw-bold text-dark">Recent Collections</span>
                    </div>
                    @hasanyrole('Super Admin|Administrator')
                    <a href="{{ route('admin.invoices.index') }}" class="small text-muted text-decoration-none">Invoices</a>
                    @endhasanyrole
                </div>
                <div class="p-3">
                    @forelse($recentPayments as $payment)
                        <div class="d-flex justify-content-between align-items-center p-3 rounded mb-2 border bg-light">
                            <div>
                                <div class="fw-bold small text-dark font-monospace">{{ $payment->receipt_number }}</div>
                                <div class="text-muted small" style="font-size: 11px;">{{ $payment->client->company_name ?? $payment->client->user->name }}</div>
                                <div class="text-muted" style="font-size: 10.5px;">{{ $payment->payment_method }} &bull; {{ $payment->payment_date->format('d M Y') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success small">+₹{{ number_format($payment->amount, 2) }}</div>
                                <span class="badge badge-soft-success" style="font-size: 10px;">Allocated</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted small">
                            <i class="fas fa-wallet fa-2x mb-2 opacity-25"></i>
                            <div>No payment collections recorded yet.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
