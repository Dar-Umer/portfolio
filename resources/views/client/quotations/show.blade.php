@extends('layouts.public')

@section('title', 'Quotation ' . $quotation->quotation_number . ' | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('client.quotations.index') }}" class="small text-muted text-decoration-none">&larr; Back to Quotations</a>
        <div class="d-flex gap-2">
            @if($quotation->status === 'Sent')
                <form method="POST" action="{{ route('client.quotations.accept', $quotation) }}">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check-circle me-1"></i> Accept Quotation
                    </button>
                </form>
            @elseif($quotation->status === 'Accepted' || $quotation->status === 'Converted to Invoice')
                <span class="badge bg-success p-2"><i class="fas fa-check me-1"></i> Accepted</span>
            @endif
        </div>
    </div>

    <div class="card card-custom p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-bold mb-1">{{ $quotation->quotation_number }}</h4>
                <div class="text-muted small">Issued: {{ $quotation->issue_date->format('d M Y') }} &bull; Valid Until: {{ $quotation->expiry_date->format('d M Y') }}</div>
            </div>
            <span class="badge bg-primary px-3 py-2">{{ $quotation->status }}</span>
        </div>

        <div class="table-responsive mb-4">
            <table class="table align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Scope Deliverable</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th class="text-end">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->items as $idx => $item)
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

        <div class="row justify-content-end small">
            <div class="col-md-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Subtotal:</span>
                    <span>₹{{ number_format($quotation->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Estimated Tax:</span>
                    <span>₹{{ number_format($quotation->tax_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top fw-bold fs-6 text-primary">
                    <span>Grand Total:</span>
                    <span>₹{{ number_format($quotation->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        @if($quotation->terms_and_conditions)
            <hr class="my-4">
            <h6 class="fw-bold small mb-2 text-uppercase text-muted">Proposal Terms</h6>
            <div class="small text-secondary">{{ $quotation->terms_and_conditions }}</div>
        @endif
    </div>
</div>
@endsection
