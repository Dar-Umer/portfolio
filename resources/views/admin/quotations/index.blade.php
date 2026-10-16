@extends('layouts.admin')

@section('page_title', 'Quotations & Proposals')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Commercial Proposals & Quotes</h4>
        <p class="text-muted small mb-0">Prepare commercial proposals, calculate tax rates, export PDFs, and convert accepted quotes into invoices.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.quotations.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus"></i> New Quotation
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Quote ID</th>
                    <th>Client Organization</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $q)
                    <tr>
                        <td>
                            <span class="badge badge-soft-primary font-monospace px-2 py-1 fw-bold">
                                {{ $q->quotation_number }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">
                                {{ $q->client->company_name ?? $q->client->user->name }}
                            </div>
                        </td>
                        <td><span class="text-muted small">{{ $q->issue_date->format('d M Y') }}</span></td>
                        <td><span class="text-muted small">{{ $q->expiry_date->format('d M Y') }}</span></td>
                        <td><span class="fw-bold text-dark">{{ $q->currency }} {{ number_format($q->grand_total, 2) }}</span></td>
                        <td>
                            @php
                                $qBadge = match($q->status) {
                                    'Accepted', 'Converted to Invoice' => 'badge-soft-success',
                                    'Sent' => 'badge-soft-info',
                                    'Rejected', 'Expired' => 'badge-soft-danger',
                                    default => 'badge-soft-dark'
                                };
                            @endphp
                            <span class="badge {{ $qBadge }}">
                                {{ $q->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.quotations.show', $q) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Proposal">
                                    <i class="fas fa-eye text-muted"></i>
                                </a>
                                <a href="{{ route('admin.quotations.pdf', $q) }}" class="btn btn-sm btn-light border py-1 px-2" title="Download PDF">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                </a>
                                @if(!$q->invoice)
                                    <form method="POST" action="{{ route('admin.quotations.convert', $q) }}" class="d-inline" onsubmit="return confirm('Convert quotation {{ $q->quotation_number }} into a Tax Invoice?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border py-1 px-2 text-success" title="Convert to Tax Invoice">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </button>
                                    </form>
                                @endif
                                @hasanyrole('Super Admin|Administrator')
                                    <form method="POST" action="{{ route('admin.quotations.destroy', $q) }}" class="d-inline" onsubmit="return confirm('Delete quotation {{ $q->quotation_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border py-1 px-2 text-danger" title="Delete Quotation">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endhasanyrole
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="fas fa-file-invoice fa-3x opacity-25"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Quotations Issued</h6>
                            <p class="text-muted small mb-3">No commercial quotes or client proposals drafted yet.</p>
                            <a href="{{ route('admin.quotations.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Create First Quotation
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($quotations->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $quotations->firstItem() ?? 0 }} to {{ $quotations->lastItem() ?? 0 }} of {{ $quotations->total() }} quotations</span>
            <div>{{ $quotations->links() }}</div>
        </div>
    @endif
</div>
@endsection
