@extends('layouts.admin')

@section('page_title', 'Client Profile: ' . $client->client_number)

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.clients.index') }}" class="small text-muted text-decoration-none">&larr; Back to Directory</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="text-center mb-3">
                <div class="bg-primary text-white d-inline-block rounded-circle p-3 mb-2">
                    <i class="fas fa-building fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-0">{{ $client->company_name ?? $client->user->name }}</h5>
                <span class="text-muted small">Client #{{ $client->client_number }}</span>
            </div>
            <hr>
            <div class="small">
                <div class="mb-2"><strong>Primary Contact:</strong> {{ $client->user->name }}</div>
                <div class="mb-2"><strong>Email:</strong> {{ $client->user->email }}</div>
                <div class="mb-2"><strong>Phone:</strong> {{ $client->phone ?? 'N/A' }}</div>
                <div class="mb-2"><strong>Tax ID / GSTIN:</strong> {{ $client->tax_number ?? 'N/A' }}</div>
                <div class="mb-2"><strong>Address:</strong> {{ $client->billing_address ?? 'N/A' }}, {{ $client->city }}, {{ $client->state }} ({{ $client->country }})</div>
                <div class="mb-2"><strong>Category:</strong> <span class="badge bg-light text-dark border">{{ $client->client_category }}</span></div>
                <div><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($client->account_status) }}</span></div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-edit me-1"></i> Edit Profile</a>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h6 class="fw-bold mb-3"><i class="fas fa-wallet text-success me-2"></i> Financial Snapshot</h6>
            <div class="small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Billed:</span>
                    <span class="fw-bold">₹{{ number_format($totalInvoiced, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payments Received:</span>
                    <span class="fw-bold text-success">₹{{ number_format($totalPaid, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top">
                    <span class="text-muted">Outstanding:</span>
                    <span class="fw-bold text-danger">₹{{ number_format($outstanding, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Projects, Tickets, Invoices Tabs -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-white mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-project-diagram text-primary me-2"></i> Associated Projects ({{ $client->projects->count() }})</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Project #</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Budget</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->projects as $p)
                            <tr>
                                <td>{{ $p->project_number }}</td>
                                <td><span class="fw-semibold">{{ $p->name }}</span></td>
                                <td><span class="badge bg-info-subtle text-info">{{ $p->status }}</span></td>
                                <td>{{ $p->progress_percentage }}%</td>
                                <td>₹{{ number_format($p->budget, 2) }}</td>
                                <td><a href="{{ route('admin.projects.show', $p) }}" class="btn btn-sm btn-light border"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-3 text-muted">No projects assigned yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice text-success me-2"></i> Invoices & Statements ({{ $client->invoices->count() }})</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Total</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->invoices as $inv)
                            <tr>
                                <td><span class="fw-bold">{{ $inv->invoice_number }}</span></td>
                                <td>{{ $inv->issue_date->format('d M Y') }}</td>
                                <td>{{ $inv->due_date->format('d M Y') }}</td>
                                <td>₹{{ number_format($inv->grand_total, 2) }}</td>
                                <td><span class="fw-bold text-danger">₹{{ number_format($inv->balance_due, 2) }}</span></td>
                                <td><span class="badge {{ $inv->status === 'Paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $inv->status }}</span></td>
                                <td><a href="{{ route('admin.invoices.show', $inv) }}" class="btn btn-sm btn-light border"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-3 text-muted">No invoices generated.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
