@extends('layouts.public')

@section('title', 'My Quotations | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Quotations & Proposals</h4>
        <p class="text-muted small mb-0">Review project proposals, deliverables, and approve quotations.</p>
    </div>

    <div class="card card-custom bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Quotation #</th>
                        <th>Project</th>
                        <th>Issue Date</th>
                        <th>Valid Until</th>
                        <th>Estimated Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $q)
                        <tr>
                            <td><span class="fw-bold">{{ $q->quotation_number }}</span></td>
                            <td>{{ $q->project->name ?? 'Enterprise Scope' }}</td>
                            <td>{{ $q->issue_date->format('d M Y') }}</td>
                            <td>{{ $q->expiry_date->format('d M Y') }}</td>
                            <td><span class="fw-bold">₹{{ number_format($q->grand_total, 2) }}</span></td>
                            <td>
                                <span class="badge {{ $q->status === 'Accepted' || $q->status === 'Converted to Invoice' ? 'bg-success' : 'bg-primary' }}">
                                    {{ $q->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('client.quotations.show', $q) }}" class="btn btn-sm btn-outline-primary">Review & Action</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No quotations issued yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($quotations->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
