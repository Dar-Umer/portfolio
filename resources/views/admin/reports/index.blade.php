@extends('layouts.admin')

@section('page_title', 'Analytics, Reports & Exports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted small mb-0">Aggregate business intelligence, ticket SLA performance, revenue realization, and CSV exports.</p>
    </div>
    <div class="dropdown">
        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-file-export me-1"></i> Export Data (CSV)
        </button>
        <ul class="dropdown-menu dropdown-menu-end small">
            <li><a class="dropdown-item" href="{{ route('admin.reports.export', 'invoices') }}"><i class="fas fa-file-invoice text-success me-2"></i> Export Invoices</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.reports.export', 'payments') }}"><i class="fas fa-money-bill-wave text-primary me-2"></i> Export Payments</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.reports.export', 'tickets') }}"><i class="fas fa-ticket-alt text-warning me-2"></i> Export Tickets</a></li>
        </ul>
    </div>
</div>

<!-- High-Level KPI Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <span class="text-muted small fw-semibold text-uppercase">Total Invoiced</span>
            <h4 class="fw-bold text-dark mb-0">₹{{ number_format($stats['total_invoiced'], 2) }}</h4>
            <span class="text-muted" style="font-size: 0.75rem;">Gross commercial billing</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <span class="text-muted small fw-semibold text-uppercase">Payments Realized</span>
            <h4 class="fw-bold text-success mb-0">₹{{ number_format($stats['total_collected'], 2) }}</h4>
            <span class="text-success" style="font-size: 0.75rem;">100% verified allocations</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <span class="text-muted small fw-semibold text-uppercase">Receivables Outstanding</span>
            <h4 class="fw-bold text-danger mb-0">₹{{ number_format($stats['total_receivables'], 2) }}</h4>
            <span class="text-danger" style="font-size: 0.75rem;">Due client balances</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <span class="text-muted small fw-semibold text-uppercase">SLA Performance</span>
            <h4 class="fw-bold text-primary mb-0">{{ $stats['total_tickets'] - $stats['sla_breached_tickets'] }} / {{ $stats['total_tickets'] }}</h4>
            <span class="text-muted" style="font-size: 0.75rem;">Tickets resolved on track</span>
        </div>
    </div>
</div>

<!-- Monthly Revenue Realization Table -->
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line text-success me-2"></i> Monthly Realization Trend (Last 6 Months)</h6>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Month</th>
                    <th class="text-end">Invoiced (INR)</th>
                    <th class="text-end">Collections Realized (INR)</th>
                    <th>Realization Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyRevenue as $rev)
                    @php
                        $rate = $rev['invoiced'] > 0 ? round(($rev['collected'] / $rev['invoiced']) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td><span class="fw-bold">{{ $rev['month'] }}</span></td>
                        <td class="text-end">₹{{ number_format($rev['invoiced'], 2) }}</td>
                        <td class="text-end text-success fw-bold">₹{{ number_format($rev['collected'], 2) }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ min(100, $rate) }}%;"></div>
                                </div>
                                <span style="font-size: 0.75rem;">{{ $rate }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Operational Distributions -->
<div class="row g-4">
    <!-- Projects by Status -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <h6 class="fw-bold mb-3"><i class="fas fa-project-diagram text-primary me-2"></i> Projects by Status</h6>
            <div class="d-flex flex-column gap-2 small">
                @forelse($projectsByStatus as $status => $count)
                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                        <span class="fw-semibold">{{ $status }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </div>
                @empty
                    <div class="text-muted text-center py-3">No projects recorded.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tickets by Priority -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <h6 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Tickets by Priority</h6>
            <div class="d-flex flex-column gap-2 small">
                @forelse($ticketsByPriority as $priority => $count)
                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                        <span class="fw-semibold">{{ $priority }}</span>
                        <span class="badge {{ $priority === 'Critical' ? 'bg-danger' : ($priority === 'High' ? 'bg-warning text-dark' : 'bg-secondary') }} rounded-pill">
                            {{ $count }}
                        </span>
                    </div>
                @empty
                    <div class="text-muted text-center py-3">No tickets recorded.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tickets by Workflow State -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 bg-white h-100">
            <h6 class="fw-bold mb-3"><i class="fas fa-tasks text-info me-2"></i> Tickets by Workflow State</h6>
            <div class="d-flex flex-column gap-2 small">
                @forelse($ticketsByStatus as $statusName => $count)
                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                        <span class="fw-semibold">{{ $statusName }}</span>
                        <span class="badge bg-info-subtle text-info rounded-pill">{{ $count }}</span>
                    </div>
                @empty
                    <div class="text-muted text-center py-3">No tickets recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
