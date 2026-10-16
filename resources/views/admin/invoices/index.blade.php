@extends('layouts.admin')

@section('page_title', 'Tax Invoices & Billing')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Tax Invoices & Client Billing</h4>
        <p class="text-muted small mb-0">Track issued GST invoices, outstanding client balances, collection records, and download legal PDF copies.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Client Organization</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Grand Total</th>
                    <th>Collected</th>
                    <th>Balance Due</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td>
                            <span class="badge badge-soft-primary font-monospace px-2 py-1 fw-bold">
                                {{ $inv->invoice_number }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">
                                {{ $inv->client_snapshot['company_name'] ?? ($inv->client->company_name ?? $inv->client->user->name) }}
                            </div>
                        </td>
                        <td><span class="text-muted small">{{ $inv->issue_date->format('d M Y') }}</span></td>
                        <td><span class="text-muted small">{{ $inv->due_date->format('d M Y') }}</span></td>
                        <td><span class="fw-bold text-dark">₹{{ number_format($inv->grand_total, 2) }}</span></td>
                        <td><span class="text-success fw-semibold small">₹{{ number_format($inv->amount_paid, 2) }}</span></td>
                        <td>
                            @if($inv->balance_due > 0)
                                <span class="text-danger fw-bold">₹{{ number_format($inv->balance_due, 2) }}</span>
                            @else
                                <span class="text-muted small font-monospace">₹0.00</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $invBadge = match($inv->status) {
                                    'Paid' => 'badge-soft-success',
                                    'Partially Paid' => 'badge-soft-warning',
                                    'Overdue' => 'badge-soft-danger',
                                    default => 'badge-soft-primary'
                                };
                            @endphp
                            <span class="badge {{ $invBadge }}">
                                {{ $inv->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.invoices.show', $inv) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Details">
                                    <i class="fas fa-eye text-muted"></i>
                                </a>
                                <a href="{{ route('admin.invoices.pdf', $inv) }}" class="btn btn-sm btn-light border py-1 px-2" title="Download PDF">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                </a>
                                @hasanyrole('Super Admin|Administrator')
                                    <form method="POST" action="{{ route('admin.invoices.destroy', $inv) }}" class="d-inline" onsubmit="return confirm('Delete invoice {{ $inv->invoice_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border py-1 px-2 text-danger" title="Delete Invoice">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endhasanyrole
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="fas fa-file-invoice-dollar fa-3x opacity-25"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Invoices Issued</h6>
                            <p class="text-muted small mb-0">No tax invoices recorded in database yet.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $invoices->firstItem() ?? 0 }} to {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }} invoices</span>
            <div>{{ $invoices->links() }}</div>
        </div>
    @endif
</div>
@endsection
