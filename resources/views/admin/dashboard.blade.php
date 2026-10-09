@extends('layouts.admin')

@section('page_title', 'System Overview & Analytics')

@section('content')
    <!-- KPI Indicator Strip Cards -->
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="card-stat">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Total Customers</div>
                        <h4 class="fw-bold mb-0 text-dark mt-1">{{ $metrics['total_clients'] }}</h4>
                        <div class="text-muted mt-1" style="font-size: 11.5px;">Active CRM Profiles</div>
                    </div>
                    <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card-stat border-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Active Projects</div>
                        <h4 class="fw-bold mb-0 text-success mt-1">{{ $metrics['active_projects'] }}</h4>
                        <div class="text-muted mt-1" style="font-size: 11.5px;">{{ $metrics['completed_projects'] }} completed</div>
                    </div>
                    <div class="stat-icon-wrapper bg-success-subtle text-success">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card-stat border-yellow">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Desk Open Tickets</div>
                        <h4 class="fw-bold mb-0 text-warning mt-1">{{ $metrics['open_tickets'] }}</h4>
                        <div class="text-muted mt-1" style="font-size: 11.5px;">Active SLA monitoring</div>
                    </div>
                    <div class="stat-icon-wrapper bg-warning-subtle text-warning">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card-stat border-red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Critical Issues</div>
                        <h4 class="fw-bold mb-0 text-danger mt-1">{{ $metrics['critical_tickets'] }}</h4>
                        <div class="text-danger mt-1" style="font-size: 11.5px;">Action required</div>
                    </div>
                    <div class="stat-icon-wrapper bg-danger-subtle text-danger">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Realization Summary -->
    <div class="card mb-3 p-3">
        <div class="row g-3">
            <div class="col-md-4 border-end">
                <div class="text-muted fw-semibold" style="font-size: 11.5px; text-transform: uppercase;">Total Invoiced</div>
                <h4 class="fw-bold text-dark mb-0 mt-1">₹{{ number_format($metrics['total_invoiced'], 2) }}</h4>
                <div class="text-muted" style="font-size: 11px;">Gross billed receivables</div>
            </div>
            <div class="col-md-4 border-end">
                <div class="text-success fw-semibold" style="font-size: 11.5px; text-transform: uppercase;">Realized Payments</div>
                <h4 class="fw-bold text-success mb-0 mt-1">₹{{ number_format($metrics['total_collected'], 2) }}</h4>
                <div class="text-success" style="font-size: 11px;">Direct ledger allocations</div>
            </div>
            <div class="col-md-4">
                <div class="text-danger fw-semibold" style="font-size: 11.5px; text-transform: uppercase;">Outstanding Balances</div>
                <h4 class="fw-bold text-danger mb-0 mt-1">₹{{ number_format($metrics['outstanding_receivables'], 2) }}</h4>
                <div class="text-danger" style="font-size: 11px;">Pending customer collections</div>
            </div>
        </div>
    </div>

    <!-- SLA Tickets Table & Recent Payments -->
    <div class="row g-3">
        <!-- Tickets Desk -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-ticket-alt text-primary me-2"></i>
                        <span>Recent Support Tickets (SLA Tracked)</span>
                    </div>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-primary" style="font-size: 12px; padding: 4px 10px;">View All Tickets</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Subject</th>
                                <th>Account</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets as $ticket)
                                <tr>
                                    <td><span class="fw-semibold text-primary">{{ $ticket->ticket_number }}</span></td>
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
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-light border py-0 px-2" style="font-size: 11px;">
                                            Open
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

        <!-- Recent Realized Payments -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-receipt text-success me-2"></i>
                    <span>Recent Collections</span>
                </div>
                <div class="p-3">
                    @forelse($recentPayments as $payment)
                        <div class="d-flex justify-content-between align-items-center p-2 rounded mb-2 border bg-white">
                            <div>
                                <div class="fw-bold small text-dark">{{ $payment->receipt_number }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $payment->client->company_name ?? $payment->client->user->name }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ $payment->payment_method }} &bull; {{ $payment->payment_date->format('d M Y') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success small">+₹{{ number_format($payment->amount, 2) }}</div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 10px;">Paid</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">No collections recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
