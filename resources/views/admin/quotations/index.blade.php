@extends('layouts.admin')

@section('page_title', 'Quotations & Proposals')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted small mb-0">Prepare commercial proposals, calculate tax rates, export PDFs, and convert accepted quotes into invoices.</p>
    </div>
    <a href="{{ route('admin.quotations.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> New Quotation
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Quotation #</th>
                    <th>Client</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $q)
                    <tr>
                        <td><span class="fw-bold font-monospace text-primary">{{ $q->quotation_number }}</span></td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $q->client->company_name ?? $q->client->user->name }}</div>
                        </td>
                        <td><span class="text-muted small">{{ $q->issue_date->format('d M Y') }}</span></td>
                        <td><span class="text-muted small">{{ $q->expiry_date->format('d M Y') }}</span></td>
                        <td><span class="fw-bold">{{ $q->currency }} {{ number_format($q->grand_total, 2) }}</span></td>
                        <td>
                            @php
                                $qBadge = match($q->status) {
                                    'Accepted', 'Converted to Invoice' => 'bg-success',
                                    'Sent' => 'bg-info text-dark',
                                    'Rejected', 'Expired' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $qBadge }}">
                                {{ $q->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.quotations.show', $q) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="View Proposal"><i class="fas fa-eye"></i> View</a>
                            <a href="{{ route('admin.quotations.pdf', $q) }}" class="btn btn-sm btn-outline-danger py-1 px-2 ms-1" title="Download PDF"><i class="fas fa-file-pdf"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No quotations created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($quotations->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $quotations->links() }}
        </div>
    @endif
</div>
@endsection
