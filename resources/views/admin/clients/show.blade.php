@extends('layouts.admin')

@section('page_title', 'Client Profile: ' . $client->client_number)

@section('content')
<!-- Top Breadcrumb & Action Banner -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-light border">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">{{ $client->company_name ?? $client->user->name }}</h4>
                <span class="badge badge-soft-primary font-monospace">{{ $client->client_number }}</span>
                @if($client->account_status === 'active')
                    <span class="badge badge-soft-success">Active</span>
                @elseif($client->account_status === 'suspended')
                    <span class="badge badge-soft-danger">Suspended</span>
                @else
                    <span class="badge badge-soft-dark">{{ ucfirst($client->account_status) }}</span>
                @endif
            </div>
            <p class="text-muted small mb-0 mt-1">Client since {{ $client->created_at->format('M Y') }} &bull; Category: <span class="fw-semibold text-dark">{{ $client->client_category }}</span></p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-pen-to-square me-1"></i> Edit Profile
        </a>
        <a href="{{ route('admin.projects.create', ['client_id' => $client->id]) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Create Project
        </a>
        <a href="{{ route('admin.quotations.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-light border text-dark">
            <i class="fas fa-file-invoice me-1"></i> New Quotation
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Profile Card & Financial Snapshot -->
    <div class="col-lg-4">
        <!-- Client Profile Details -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="text-center mb-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold mb-3 shadow-sm"
                     style="width: 64px; height: 64px; font-size: 24px; background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);">
                    {{ strtoupper(substr($client->company_name ?? $client->user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $client->company_name ?? $client->user->name }}</h5>
                <span class="badge badge-soft-dark border">{{ $client->client_category }} Customer</span>
            </div>

            <div class="border-top pt-3">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded p-2 bg-light text-muted"><i class="fas fa-user-circle"></i></div>
                    <div>
                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Primary Contact</div>
                        <div class="fw-semibold text-dark">{{ $client->user->name }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded p-2 bg-light text-muted"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Email Address</div>
                        <div class="fw-semibold text-dark">{{ $client->user->email }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded p-2 bg-light text-muted"><i class="fas fa-phone-alt"></i></div>
                    <div>
                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Phone Number</div>
                        <div class="fw-semibold text-dark">{{ $client->phone ?? 'Not Specified' }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded p-2 bg-light text-muted"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">GSTIN / Tax ID</div>
                        <div class="font-monospace fw-semibold text-dark">{{ $client->tax_number ?? 'Unregistered' }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <div class="rounded p-2 bg-light text-muted"><i class="fas fa-map-pin"></i></div>
                    <div>
                        <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Billing Address</div>
                        <div class="small text-secondary">
                            {{ $client->billing_address ?? 'Address on file' }}<br>
                            {{ $client->city ? $client->city . ', ' : '' }}{{ $client->state ? $client->state . ', ' : '' }}{{ $client->country ?? 'India' }}
                            {{ $client->postal_code ? '(' . $client->postal_code . ')' : '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-wallet text-success"></i> Financial Snapshot
            </h6>

            <div class="p-3 rounded mb-3" style="background: var(--app-bg); border: 1px solid var(--app-border);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small">Total Billed</span>
                    <span class="fw-bold text-dark">₹{{ number_format($totalInvoiced, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small">Payments Received</span>
                    <span class="fw-bold text-success">₹{{ number_format($totalPaid, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="fw-semibold small text-danger">Outstanding Balance</span>
                    <span class="fw-bold text-danger">₹{{ number_format($outstanding, 2) }}</span>
                </div>
            </div>

            @php
                $collectionRatio = $totalInvoiced > 0 ? min(100, round(($totalPaid / $totalInvoiced) * 100)) : 100;
            @endphp
            <div>
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Payment Collection Rate</span>
                    <span class="fw-semibold text-dark">{{ $collectionRatio }}%</span>
                </div>
                <div class="progress" style="height: 6px; background-color: #e2e8f0; border-radius: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $collectionRatio }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Projects & Invoices Tables -->
    <div class="col-lg-8">
        <!-- Associated Projects -->
        <div class="card border-0 shadow-sm bg-white mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-cubes text-primary"></i>
                    <h6 class="fw-bold mb-0 text-dark">Associated Projects ({{ $client->projects->count() }})</h6>
                </div>
                <a href="{{ route('admin.projects.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> New Project
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Project #</th>
                            <th>Project Name</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Budget</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->projects as $p)
                            <tr>
                                <td><span class="badge badge-soft-primary font-monospace">{{ $p->project_number }}</span></td>
                                <td>
                                    <a href="{{ route('admin.projects.show', $p) }}" class="fw-semibold text-dark text-decoration-none">
                                        {{ $p->name }}
                                    </a>
                                    <div class="text-muted small" style="font-size: 11px;">{{ $p->category }}</div>
                                </td>
                                <td>
                                    @php
                                        $pBadge = match($p->status) {
                                            'Completed' => 'badge-soft-success',
                                            'In Development' => 'badge-soft-primary',
                                            'Testing', 'Client Review' => 'badge-soft-warning',
                                            default => 'badge-soft-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $pBadge }}">{{ $p->status }}</span>
                                </td>
                                <td style="min-width: 120px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 5px; background-color: #e2e8f0; border-radius: 3px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $p->progress_percentage }}%;"></div>
                                        </div>
                                        <span class="small fw-semibold text-muted" style="font-size: 11px;">{{ $p->progress_percentage }}%</span>
                                    </div>
                                </td>
                                <td><span class="fw-semibold">₹{{ number_format($p->budget, 2) }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.projects.show', $p) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Project">
                                        <i class="fas fa-arrow-right text-muted"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="fas fa-cubes fa-2x opacity-25"></i></div>
                                    <div class="fw-semibold text-dark mb-1">No Projects Assigned Yet</div>
                                    <p class="small text-muted mb-3">Create the first project deliverable for {{ $client->company_name ?? $client->user->name }}.</p>
                                    <a href="{{ route('admin.projects.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i> Create Project
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoices & Billing Statements -->
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice-dollar text-success"></i>
                    <h6 class="fw-bold mb-0 text-dark">Invoices & Statements ({{ $client->invoices->count() }})</h6>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Total</th>
                            <th>Balance Due</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->invoices as $inv)
                            <tr>
                                <td><span class="badge badge-soft-primary font-monospace">{{ $inv->invoice_number }}</span></td>
                                <td><span class="text-muted small">{{ $inv->issue_date->format('d M Y') }}</span></td>
                                <td><span class="text-muted small">{{ $inv->due_date->format('d M Y') }}</span></td>
                                <td><span class="fw-semibold text-dark">₹{{ number_format($inv->grand_total, 2) }}</span></td>
                                <td>
                                    @if($inv->balance_due > 0)
                                        <span class="fw-bold text-danger">₹{{ number_format($inv->balance_due, 2) }}</span>
                                    @else
                                        <span class="text-muted small">₹0.00</span>
                                    @endif
                                </td>
                                <td>
                                    @if($inv->status === 'Paid')
                                        <span class="badge badge-soft-success">Paid</span>
                                    @elseif($inv->status === 'Partially Paid')
                                        <span class="badge badge-soft-warning">Partial</span>
                                    @else
                                        <span class="badge badge-soft-danger">{{ $inv->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.invoices.show', $inv) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Invoice">
                                            <i class="fas fa-eye text-muted"></i>
                                        </a>
                                        <a href="{{ route('admin.invoices.pdf', $inv) }}" class="btn btn-sm btn-light border py-1 px-2" title="Download PDF">
                                            <i class="fas fa-file-pdf text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <div class="mb-1"><i class="fas fa-file-invoice fa-2x opacity-25"></i></div>
                                    <div class="small">No tax invoices generated yet for this client.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
