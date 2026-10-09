@extends('layouts.admin')

@section('page_title', 'Tax Invoice: ' . $invoice->invoice_number)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.invoices.index') }}" class="small text-muted text-decoration-none">&larr; Back to Invoices</a>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        @if($invoice->balance_due > 0)
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="fas fa-money-bill-wave me-1"></i> Record Payment
            </button>
        @else
            <span class="badge bg-success p-2"><i class="fas fa-check-circle me-1"></i> Fully Paid</span>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $invoice->invoice_number }}</h4>
            <div class="text-muted small">Issued: {{ $invoice->issue_date->format('d M Y') }} &bull; Due: {{ $invoice->due_date->format('d M Y') }}</div>
        </div>
        <span class="badge {{ $invoice->status === 'Paid' ? 'bg-success' : 'bg-primary' }} px-3 py-2">
            {{ $invoice->status }}
        </span>
    </div>

    <div class="row g-4 mb-4 small">
        <div class="col-md-6">
            <strong>Billed Client:</strong><br>
            {{ $invoice->client_snapshot['company_name'] ?? $invoice->client->company_name }}<br>
            Attn: {{ $invoice->client_snapshot['name'] ?? $invoice->client->user->name }}<br>
            {{ $invoice->client_snapshot['billing_address'] ?? $invoice->client->billing_address }}<br>
            GSTIN: <strong>{{ $invoice->client_snapshot['tax_number'] ?? ($invoice->client->tax_number ?? 'N/A') }}</strong>
        </div>
        <div class="col-md-6 text-md-end">
            <strong>Project Reference:</strong><br>
            {{ $invoice->project->name ?? 'Custom Software Services' }}<br>
            Currency: <strong>{{ $invoice->currency }}</strong>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table align-middle small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Deliverable / Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end fw-semibold">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="row justify-content-end small mb-4">
        <div class="col-md-4">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Subtotal:</span>
                <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if($invoice->discount_amount > 0)
                <div class="d-flex justify-content-between mb-1 text-danger">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
            @endif
            @if($invoice->cgst_amount > 0)
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">CGST:</span>
                    <span>₹{{ number_format($invoice->cgst_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">SGST:</span>
                    <span>₹{{ number_format($invoice->sgst_amount, 2) }}</span>
                </div>
            @elseif($invoice->igst_amount > 0)
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">IGST:</span>
                    <span>₹{{ number_format($invoice->igst_amount, 2) }}</span>
                </div>
            @endif
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

    <!-- Payment Allocations -->
    @if($invoice->allocations->count() > 0)
        <hr>
        <h6 class="fw-bold mb-3 small text-uppercase text-muted">Allocated Payments</h6>
        <div class="table-responsive">
            <table class="table table-sm align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt #</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th class="text-end">Amount Allocated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->allocations as $alloc)
                        <tr>
                            <td><span class="fw-semibold">{{ $alloc->payment->receipt_number }}</span></td>
                            <td>{{ $alloc->payment->payment_date->format('d M Y') }}</td>
                            <td>{{ $alloc->payment->payment_method }}</td>
                            <td>{{ $alloc->payment->transaction_reference ?? '—' }}</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($alloc->amount_allocated, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal: Record Payment -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.invoices.payment', $invoice) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fs-6 fw-bold">Record Payment for {{ $invoice->invoice_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount (INR)</label>
                        <input type="number" step="0.01" name="amount" class="form-control form-control-sm" value="{{ $invoice->balance_due }}" max="{{ $invoice->balance_due }}" required>
                        <span class="text-muted" style="font-size: 0.75rem;">Max receivable balance: ₹{{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <select name="payment_method" class="form-select form-select-sm" required>
                            <option value="Bank Transfer">Bank Transfer (NEFT/RTGS/IMPS)</option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Cash">Cash</option>
                            <option value="Payment Gateway">Payment Gateway</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Reference / UTR Number</label>
                        <input type="text" name="transaction_reference" class="form-control form-control-sm" placeholder="e.g. UTR-9876543210">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Payment verification remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">Confirm & Allocate Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
