@extends('layouts.admin')

@section('page_title', 'Tax Invoices & Billing')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted small mb-0">Track issued GST invoices, outstanding client balances, and payment collections.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Grand Total</th>
                    <th>Paid</th>
                    <th>Balance Due</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td><span class="fw-bold font-monospace text-primary">{{ $inv->invoice_number }}</span></td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $inv->client_snapshot['company_name'] ?? ($inv->client->company_name ?? $inv->client->user->name) }}</div>
                        </td>
                        <td><span class="text-muted small">{{ $inv->issue_date->format('d M Y') }}</span></td>
                        <td><span class="text-muted small">{{ $inv->due_date->format('d M Y') }}</span></td>
                        <td><span class="fw-bold">₹{{ number_format($inv->grand_total, 2) }}</span></td>
                        <td><span class="text-success fw-semibold">₹{{ number_format($inv->amount_paid, 2) }}</span></td>
                        <td>
                            @if($inv->balance_due > 0)
                                <span class="text-danger fw-bold">₹{{ number_format($inv->balance_due, 2) }}</span>
                            @else
                                <span class="text-muted small">₹0.00</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $inv->status === 'Paid' ? 'bg-success' : ($inv->status === 'Partially Paid' ? 'bg-warning text-dark' : 'bg-primary') }}">
                                {{ $inv->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="View Details"><i class="fas fa-eye"></i> View</a>
                            <a href="{{ route('admin.invoices.pdf', $inv) }}" class="btn btn-sm btn-outline-danger py-1 px-2 ms-1" title="Download PDF"><i class="fas fa-file-pdf"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No tax invoices recorded in database.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
