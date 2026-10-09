@extends('layouts.admin')

@section('page_title', 'Quotation: ' . $quotation->quotation_number)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.quotations.index') }}" class="small text-muted text-decoration-none">&larr; Back to Quotations</a>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.quotations.pdf', $quotation) }}" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        @if($quotation->status !== 'Converted to Invoice')
            <form method="POST" action="{{ route('admin.quotations.convert', $quotation) }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Convert to Invoice
                </button>
            </form>
        @else
            <span class="badge bg-success-subtle text-success p-2">Converted to Invoice</span>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $quotation->quotation_number }}</h4>
            <div class="text-muted small">Issued: {{ $quotation->issue_date->format('d M Y') }} &bull; Valid until: {{ $quotation->expiry_date->format('d M Y') }}</div>
        </div>
        <span class="badge {{ $quotation->status === 'Converted to Invoice' ? 'bg-success' : 'bg-primary' }} px-3 py-2">
            {{ $quotation->status }}
        </span>
    </div>

    <div class="row g-4 mb-4 small">
        <div class="col-md-6">
            <strong>Client Recipient:</strong><br>
            {{ $quotation->client->company_name ?? $quotation->client->user->name }}<br>
            Attn: {{ $quotation->client->user->name }}<br>
            {{ $quotation->client->billing_address }}<br>
            {{ $quotation->client->city }}, {{ $quotation->client->state }}
        </div>
        <div class="col-md-6 text-md-end">
            <strong>Project Reference:</strong><br>
            {{ $quotation->project->name ?? 'Enterprise Custom Scope' }}<br>
            Currency: <strong>{{ $quotation->currency }}</strong>
        </div>
    </div>

    <!-- Items Table -->
    <div class="table-responsive mb-4">
        <table class="table align-middle small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Deliverable</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Tax Rate</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->tax_rate }}%</td>
                        <td class="text-end fw-semibold">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="row justify-content-end small">
        <div class="col-md-4">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Subtotal:</span>
                <span>₹{{ number_format($quotation->subtotal, 2) }}</span>
            </div>
            @if($quotation->discount_amount > 0)
                <div class="d-flex justify-content-between mb-1 text-danger">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($quotation->discount_amount, 2) }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Tax Amount:</span>
                <span>₹{{ number_format($quotation->tax_total, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between pt-2 border-top fw-bold fs-6">
                <span>Grand Total:</span>
                <span class="text-primary">₹{{ number_format($quotation->grand_total, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
