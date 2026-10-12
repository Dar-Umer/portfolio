@extends('layouts.admin')

@section('page_title', 'Analytics, Reports & Exports')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Executive Analytics & Reports</h4>
        <p class="text-muted small mb-0">Aggregate business intelligence, ticket SLA performance, revenue realization, and CSV exports.</p>
    </div>
    <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-file-export me-1"></i> Export Data (CSV)
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2">
            <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', 'invoices') }}"><i class="fas fa-file-invoice text-success me-2"></i> Export Invoices Ledger</a></li>
            <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', 'payments') }}"><i class="fas fa-money-bill-wave text-primary me-2"></i> Export Payment Receipts</a></li>
            <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', 'tickets') }}"><i class="fas fa-ticket text-warning me-2"></i> Export Support Tickets</a></li>
        </ul>
    </div>
</div>

<!-- High-Level KPI Summary -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card-stat">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total Invoiced</span>
                    <h3 class="fw-bold text-dark mt-2 mb-1">₹{{ number_format($stats['total_invoiced'], 2) }}</h3>
                    <span class="text-muted small">Gross commercial billing</span>
                </div>
                <div class="stat-icon-wrapper" style="background: var(--app-primary-subtle); color: var(--app-primary);">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat border-green">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Collections Realized</span>
                    <h3 class="fw-bold text-success mt-2 mb-1">₹{{ number_format($stats['total_collected'], 2) }}</h3>
                    <span class="text-success small fw-medium">100% verified allocations</span>
                </div>
                <div class="stat-icon-wrapper" style="background: var(--app-success-subtle); color: var(--app-success);">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat border-red">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Outstanding Receivables</span>
                    <h3 class="fw-bold text-danger mt-2 mb-1">₹{{ number_format($stats['total_receivables'], 2) }}</h3>
                    <span class="text-danger small fw-medium">Client balances due</span>
                </div>
                <div class="stat-icon-wrapper" style="background: var(--app-danger-subtle); color: var(--app-danger);">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat border-info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">SLA Compliance</span>
                    <h3 class="fw-bold text-primary mt-2 mb-1">{{ $stats['total_tickets'] - $stats['sla_breached_tickets'] }} / {{ $stats['total_tickets'] }}</h3>
                    <span class="text-muted small">Tickets on target</span>
                </div>
                <div class="stat-icon-wrapper" style="background: var(--app-info-subtle); color: var(--app-info);">
                    <i class="fas fa-shield-halved"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Revenue Realization Table -->
<div class="card border-0 shadow-sm bg-white mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
        <i class="fas fa-chart-line text-success"></i>
        <h6 class="fw-bold mb-0 text-dark">Monthly Realization Trend (Last 6 Months)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Reporting Month</th>
                    <th class="text-end">Invoiced (INR)</th>
                    <th class="text-end">Collections Realized (INR)</th>
                    <th style="min-width: 220px;">Realization Ratio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyRevenue as $rev)
                    @php
                        $rate = $rev['invoiced'] > 0 ? round(($rev['collected'] / $rev['invoiced']) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td><span class="fw-semibold text-dark">{{ $rev['month'] }}</span></td>
                        <td class="text-end font-monospace">₹{{ number_format($rev['invoiced'], 2) }}</td>
                        <td class="text-end text-success fw-bold font-monospace">₹{{ number_format($rev['collected'], 2) }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress flex-grow-1" style="height: 6px; background-color: #e2e8f0; border-radius: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ min(100, $rate) }}%; border-radius: 4px;"></div>
                                </div>
                                <span class="fw-semibold small font-monospace" style="font-size: 11px; width: 45px;">{{ $rate }}%</span>
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
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-layer-group text-primary"></i> Projects by Lifecycle Stage
            </h6>
            <div class="d-flex flex-column gap-2 small">
                @forelse($projectsByStatus as $status => $count)
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border">
                        <span class="fw-medium text-dark">{{ $status }}</span>
                        <span class="badge badge-soft-primary">{{ $count }}</span>
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
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-triangle-exclamation text-warning"></i> Tickets by Priority
            </h6>
            <div class="d-flex flex-column gap-2 small">
                @forelse($ticketsByPriority as $priority => $count)
                    @php
                        $pBadge = match($priority) {
                            'Critical' => 'badge-soft-danger',
                            'High' => 'badge-soft-warning',
                            'Normal' => 'badge-soft-info',
                            default => 'badge-soft-dark'
                        };
                    @endphp
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border">
                        <span class="fw-medium text-dark">{{ $priority }}</span>
                        <span class="badge {{ $pBadge }}">
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
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-diagram-next text-info"></i> Tickets by State
            </h6>
            <div class="d-flex flex-column gap-2 small">
                @forelse($ticketsByStatus as $statusName => $count)
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border">
                        <span class="fw-medium text-dark">{{ $statusName }}</span>
                        <span class="badge badge-soft-info">{{ $count }}</span>
                    </div>
                @empty
                    <div class="text-muted text-center py-3">No tickets recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
