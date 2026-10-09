@extends('layouts.public')

@section('title', 'My Invoices & Statements | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Invoices & Statements</h4>
        <p class="text-muted small mb-0">View tax invoices, payments made, and current account balances.</p>
    </div>

    <div class="card card-custom bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Project</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Grand Total</th>
                        <th>Paid</th>
                        <th>Balance Due</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td><span class="fw-bold">{{ $inv->invoice_number }}</span></td>
                            <td>{{ $inv->project->name ?? 'Custom Software Services' }}</td>
                            <td>{{ $inv->issue_date->format('d M Y') }}</td>
                            <td>{{ $inv->due_date->format('d M Y') }}</td>
                            <td>₹{{ number_format($inv->grand_total, 2) }}</td>
                            <td class="text-success fw-semibold">₹{{ number_format($inv->amount_paid, 2) }}</td>
                            <td class="text-danger fw-bold">₹{{ number_format($inv->balance_due, 2) }}</td>
                            <td>
                                <span class="badge {{ $inv->status === 'Paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('client.invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary">View Statement</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No invoices issued.</td>
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
</div>
@endsection
