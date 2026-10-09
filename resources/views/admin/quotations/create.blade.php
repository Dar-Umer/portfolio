@extends('layouts.admin')

@section('page_title', 'Create New Quotation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="mb-4">
                <a href="{{ route('admin.quotations.index') }}" class="small text-muted text-decoration-none mb-2 d-inline-block">&larr; Back to Quotations</a>
                <h5 class="fw-bold mb-1">Create Commercial Quotation</h5>
                <p class="text-muted small">Specify client, milestones or deliverables, and tax rates.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.quotations.store') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Client Association</label>
                        <select name="client_profile_id" class="form-select form-select-sm" required>
                            <option value="">Select Client...</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->company_name ?? $c->user->name }} ({{ $c->client_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Related Project (Optional)</label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">Standalone Quotation</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Issue Date</label>
                        <input type="date" name="issue_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Currency</label>
                        <input type="text" name="currency" class="form-control form-control-sm" value="INR" required>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold mb-3">Deliverables & Line Items</h6>

                <!-- Item Row 1 -->
                <div class="p-3 bg-light rounded mb-3 small">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Description</label>
                            <input type="text" name="items[0][description]" class="form-control form-control-sm" value="Core Backend Architecture & API Development" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Qty</label>
                            <input type="number" step="0.01" name="items[0][quantity]" class="form-control form-control-sm" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Unit Price</label>
                            <input type="number" step="0.01" name="items[0][unit_price]" class="form-control form-control-sm" value="35000.00" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-semibold">Discount</label>
                            <input type="number" step="0.01" name="items[0][discount]" class="form-control form-control-sm" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tax Rate (%)</label>
                            <input type="number" step="0.01" name="items[0][tax_rate]" class="form-control form-control-sm" value="18.00">
                        </div>
                    </div>
                </div>

                <!-- Item Row 2 -->
                <div class="p-3 bg-light rounded mb-4 small">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Description</label>
                            <input type="text" name="items[1][description]" class="form-control form-control-sm" value="Client Portal UI & GitHub Webhook Setup">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Qty</label>
                            <input type="number" step="0.01" name="items[1][quantity]" class="form-control form-control-sm" value="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Unit Price</label>
                            <input type="number" step="0.01" name="items[1][unit_price]" class="form-control form-control-sm" value="15000.00">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-semibold">Discount</label>
                            <input type="number" step="0.01" name="items[1][discount]" class="form-control form-control-sm" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tax Rate (%)</label>
                            <input type="number" step="0.01" name="items[1][tax_rate]" class="form-control form-control-sm" value="18.00">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Client Notes</label>
                    <textarea name="client_notes" class="form-control form-control-sm" rows="2" placeholder="Any specific requirements or notes for the client...">{{ old('client_notes') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Terms & Conditions</label>
                    <textarea name="terms_and_conditions" class="form-control form-control-sm" rows="2">50% Advance on project kickoff, 50% upon deployment and delivery approval. Valid for 30 calendar days.</textarea>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    Generate Quotation <i class="fas fa-check ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
