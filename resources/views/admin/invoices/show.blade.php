@extends('layouts.admin')

@section('page_title', 'Tax Invoice: ' . $invoice->invoice_number)

@section('content')
<!-- Header Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-light border">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">{{ $invoice->invoice_number }}</h4>
                @php
                    $invBadge = match($invoice->status) {
                        'Paid' => 'badge-soft-success',
                        'Partially Paid' => 'badge-soft-warning',
                        'Overdue' => 'badge-soft-danger',
                        default => 'badge-soft-primary'
                    };
                @endphp
                <span class="badge {{ $invBadge }}">{{ $invoice->status }}</span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Issued {{ $invoice->issue_date->format('d M Y') }} &bull; Payment Due by {{ $invoice->due_date->format('d M Y') }}
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        @if($invoice->balance_due > 0)
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="fas fa-hand-holding-dollar me-1"></i> Record Payment
            </button>
        @else
            <span class="badge badge-soft-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> Balance Fully Settled</span>
        @endif

        @hasanyrole('Super Admin|Administrator')
            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete invoice {{ $invoice->invoice_number }}? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-trash me-1"></i> Delete Invoice
                </button>
            </form>
        @endhasanyrole
    </div>
</div>

<div class="card border-0 shadow-sm p-4 bg-white mb-4">
    @php
        $siteName = \App\Models\Setting::get('site_name', config('app.name', 'The Umer Store'));
        $siteTagline = \App\Models\Setting::get('site_tagline', 'Software Systems & Enterprise Architectures');
        $sellerGstin = \App\Models\Setting::get('seller_gstin', '09ABCDE1234F1Z5');
        $sellerAddress = \App\Models\Setting::get('business_address', 'Srinagar, Jammu & Kashmir');
        $sellerState = \App\Models\Setting::get('seller_state', 'Jammu and Kashmir');
        $contactPhone = \App\Models\Setting::get('contact_phone', '+91 9622756099');
        $contactEmail = \App\Models\Setting::get('contact_email', 'contact@theumer.store');
        $currSym = $invoice->currency === 'USD' ? '$' : '₹';
    @endphp

    <!-- Issuer Header -->
    <div class="row g-4 mb-4 pb-4 border-bottom">
        <div class="col-md-6">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 11px;">Issued By</div>
            <h5 class="fw-bold text-dark mb-1">{{ $siteName }}</h5>
            <div class="small text-secondary">
                {{ $siteTagline }}<br>
                {{ $sellerAddress }} &bull; State: {{ $sellerState }}<br>
                @if($sellerGstin) GSTIN: <span class="font-monospace fw-semibold text-dark">{{ $sellerGstin }}</span> &bull; @endif
                Email: {{ $contactEmail }}
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 11px;">Tax Invoice Summary</div>
            <div class="fw-bold text-dark fs-5 font-monospace mb-1">{{ $invoice->invoice_number }}</div>
            <div class="small text-secondary">
                Date: <span class="fw-semibold text-dark">{{ $invoice->issue_date->format('d M Y') }}</span> &bull; Due: <span class="fw-semibold text-dark">{{ $invoice->due_date->format('d M Y') }}</span><br>
                Currency: <span class="fw-bold text-dark font-monospace">{{ $invoice->currency }}</span> ({{ $currSym }})
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4 pb-4 border-bottom">
        <div class="col-md-6">
            <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 11px;">Billed Organization</div>
            <h6 class="fw-bold text-dark mb-1">{{ $invoice->client_snapshot['company_name'] ?? $invoice->client->company_name }}</h6>
            <div class="small text-secondary">
                Attn: {{ $invoice->client_snapshot['name'] ?? $invoice->client->user->name }}<br>
                {{ $invoice->client_snapshot['billing_address'] ?? $invoice->client->billing_address }}<br>
                GSTIN: <span class="font-monospace fw-semibold text-dark">{{ $invoice->client_snapshot['tax_number'] ?? ($invoice->client->tax_number ?? 'Unregistered') }}</span>
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 11px;">Project Reference & Source</div>
            <h6 class="fw-bold text-dark mb-1">{{ $invoice->project->name ?? 'Custom Software Services' }}</h6>
            <div class="small text-secondary">
                @if($invoice->quotation)
                    Originated from Proposal: <a href="{{ route('admin.quotations.show', $invoice->quotation) }}" class="fw-semibold text-primary text-decoration-none font-monospace">{{ $invoice->quotation->quotation_number }}</a><br>
                @endif
                Payment Status: <span class="fw-bold {{ $invoice->status === 'Paid' ? 'text-success' : 'text-primary' }}">{{ $invoice->status }}</span>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Deliverable / Service Description</th>
                    <th class="text-center" style="width: 100px;">Qty</th>
                    <th class="text-end" style="width: 150px;">Unit Rate</th>
                    <th class="text-end" style="width: 160px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->description }}</div>
                        </td>
                        <td class="text-center font-monospace">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-end font-monospace">{{ $currSym }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end fw-bold text-dark font-monospace">{{ $currSym }}{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals Summary Grid -->
    <div class="row justify-content-end mb-4">
        <div class="col-lg-5 col-md-6">
            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Services Subtotal:</span>
                    <span class="fw-semibold text-dark font-monospace">{{ $currSym }}{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 small text-danger">
                        <span>Discount:</span>
                        <span class="font-monospace">-{{ $currSym }}{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->cgst_amount > 0)
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>CGST:</span>
                        <span class="font-monospace">{{ $currSym }}{{ number_format($invoice->cgst_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>SGST:</span>
                        <span class="font-monospace">{{ $currSym }}{{ number_format($invoice->sgst_amount, 2) }}</span>
                    </div>
                @elseif($invoice->igst_amount > 0)
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>IGST:</span>
                        <span class="font-monospace">{{ $currSym }}{{ number_format($invoice->igst_amount, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between pt-2 border-top fw-bold fs-6">
                    <span class="text-dark">Grand Invoice Total:</span>
                    <span class="text-primary font-monospace">{{ $currSym }}{{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2 pt-2 border-top small text-success">
                    <span>Payments Received:</span>
                    <span class="fw-bold font-monospace">{{ $currSym }}{{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between pt-1 border-top fw-bold text-danger">
                    <span>Remaining Balance Due:</span>
                    <span class="font-monospace">{{ $currSym }}{{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Allocations -->
    @if($invoice->allocations->count() > 0)
        <div class="border-top pt-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-receipt text-success"></i> Payment Ledger & Allocations
            </h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Receipt ID</th>
                            <th>Date Credited</th>
                            <th>Method</th>
                            <th>Transaction Reference</th>
                            <th class="text-end">Amount Settled</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->allocations as $alloc)
                            <tr>
                                <td><span class="badge badge-soft-primary font-monospace">{{ $alloc->payment->receipt_number }}</span></td>
                                <td><span class="text-muted small">{{ $alloc->payment->payment_date->format('d M Y') }}</span></td>
                                <td><span class="badge badge-soft-dark">{{ $alloc->payment->payment_method }}</span></td>
                                <td><span class="font-monospace small text-muted">{{ $alloc->payment->transaction_reference ?? '—' }}</span></td>
                                <td class="text-end fw-bold text-success font-monospace">₹{{ number_format($alloc->amount_allocated, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Modal: Record Payment -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.invoices.payment', $invoice) }}">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-hand-holding-dollar text-success me-1"></i> Record Payment for {{ $invoice->invoice_number }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount (INR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="number" step="0.01" name="amount" class="form-control font-monospace" value="{{ $invoice->balance_due }}" max="{{ $invoice->balance_due }}" required>
                        </div>
                        <span class="text-muted mt-1 d-block" style="font-size: 11px;">Max receivable balance: <strong>₹{{ number_format($invoice->balance_due, 2) }}</strong></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="Bank Transfer">Bank Transfer (NEFT/RTGS/IMPS)</option>
                            <option value="UPI">UPI / QR Code</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Cash">Cash</option>
                            <option value="Payment Gateway">Payment Gateway / Stripe</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Transaction Reference / UTR Number</label>
                        <input type="text" name="transaction_reference" class="form-control font-monospace" placeholder="e.g. UTR-9876543210">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Audit Remarks / Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Payment verification remarks..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success px-3">
                        <i class="fas fa-check me-1"></i> Confirm & Allocate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
