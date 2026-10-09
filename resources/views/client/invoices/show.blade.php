@extends('layouts.public')

@section('title', 'Invoice ' . $invoice->invoice_number . ' | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('client.invoices.index') }}" class="small text-muted text-decoration-none">&larr; Back to Invoices</a>
        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>

    <div class="card card-custom p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-bold mb-1">{{ $invoice->invoice_number }}</h4>
                <div class="text-muted small">Issued: {{ $invoice->issue_date->format('d M Y') }} &bull; Due: {{ $invoice->due_date->format('d M Y') }}</div>
            </div>
            <span class="badge {{ $invoice->status === 'Paid' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2">
                {{ $invoice->status }}
            </span>
        </div>

        <div class="table-responsive mb-4">
            <table class="table align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td>₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end small mb-4">
            <div class="col-md-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Subtotal:</span>
                    <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Tax Total:</span>
                    <span>₹{{ number_format($invoice->tax_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top fw-bold fs-6">
                    <span>Grand Total:</span>
                    <span>₹{{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-success">
                    <span>Amount Paid:</span>
                    <span>₹{{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between pt-1 border-top fw-bold text-danger">
                    <span>Balance Due:</span>
                    <span>₹{{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </div>

        @if($invoice->allocations->count() > 0)
            <hr>
            <h6 class="fw-bold mb-3 small text-uppercase text-muted">Receipts & Payment History</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Receipt #</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->allocations as $al)
                            <tr>
                                <td><span class="fw-semibold">{{ $al->payment->receipt_number }}</span></td>
                                <td>{{ $al->payment->payment_date->format('d M Y') }}</td>
                                <td>{{ $al->payment->payment_method }}</td>
                                <td class="text-end fw-bold text-success">₹{{ number_format($al->amount_allocated, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
