@extends('layouts.admin')

@section('page_title', 'Quotation: ' . $quotation->quotation_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.quotations.index') }}" class="btn btn-sm btn-light border">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">{{ $quotation->quotation_number }}</h4>
                @php
                    $qBadge = match($quotation->status) {
                        'Accepted', 'Converted to Invoice' => 'badge-soft-success',
                        'Sent' => 'badge-soft-info',
                        'Rejected', 'Expired' => 'badge-soft-danger',
                        default => 'badge-soft-dark'
                    };
                @endphp
                <span class="badge {{ $qBadge }}">{{ $quotation->status }}</span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Issued {{ $quotation->issue_date->format('d M Y') }} &bull; Valid until {{ $quotation->expiry_date->format('d M Y') }}
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.quotations.pdf', $quotation) }}" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        @if($quotation->invoice)
            <a href="{{ route('admin.invoices.show', $quotation->invoice) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-file-invoice me-1"></i> View Tax Invoice {{ $quotation->invoice->invoice_number }} &rarr;
            </a>
        @elseif($quotation->status !== 'Converted to Invoice')
            <form method="POST" action="{{ route('admin.quotations.convert', $quotation) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Convert to Invoice
                </button>
            </form>
        @else
            <span class="badge badge-soft-success p-2">Converted to Invoice</span>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm p-4 bg-white mb-4">
    <div class="row g-4 mb-4 pb-4 border-bottom">
        <div class="col-md-6">
            <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 11px;">Client Recipient</div>
            <h6 class="fw-bold text-dark mb-1">{{ $quotation->client->company_name ?? $quotation->client->user->name }}</h6>
            <div class="small text-secondary">
                Attn: {{ $quotation->client->user->name }}<br>
                {{ $quotation->client->billing_address ?? 'Address on file' }}<br>
                {{ $quotation->client->city ? $quotation->client->city . ', ' : '' }}{{ $quotation->client->state ?? 'India' }}
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 11px;">Project Scope & Commercials</div>
            <h6 class="fw-bold text-dark mb-1">{{ $quotation->project->name ?? 'Custom Technical Scope' }}</h6>
            <div class="small text-secondary">
                Currency: <span class="fw-bold text-dark font-monospace">{{ $quotation->currency }}</span><br>
                Client ID: <span class="font-monospace">{{ $quotation->client->client_number }}</span>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Deliverable / Scope Specification</th>
                    <th class="text-center" style="width: 100px;">Qty</th>
                    <th class="text-end" style="width: 150px;">Unit Price</th>
                    <th class="text-center" style="width: 110px;">Tax Rate</th>
                    <th class="text-end" style="width: 160px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                    <tr>
                        <td class="text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->description }}</div>
                        </td>
                        <td class="text-center font-monospace">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-end font-monospace">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center"><span class="badge badge-soft-dark">{{ $item->tax_rate }}%</span></td>
                        <td class="text-end fw-bold text-dark font-monospace">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals Summary Grid -->
    <div class="row justify-content-end">
        <div class="col-lg-5 col-md-6">
            <div class="p-3 bg-light rounded-3 border">
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Item Subtotal:</span>
                    <span class="fw-semibold text-dark font-monospace">₹{{ number_format($quotation->subtotal, 2) }}</span>
                </div>
                @if($quotation->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 small text-danger">
                        <span>Commercial Discount:</span>
                        <span class="font-monospace">-₹{{ number_format($quotation->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">GST / Tax Amount:</span>
                    <span class="fw-semibold text-dark font-monospace">₹{{ number_format($quotation->tax_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top fw-bold fs-6">
                    <span class="text-dark">Grand Proposal Total:</span>
                    <span class="text-primary font-monospace">₹{{ number_format($quotation->grand_total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
