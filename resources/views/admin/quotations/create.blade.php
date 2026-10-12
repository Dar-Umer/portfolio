@extends('layouts.admin')

@section('page_title', 'Create New Quotation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="mb-3">
            <a href="{{ route('admin.quotations.index') }}" class="btn btn-sm btn-light border">
                <i class="fas fa-arrow-left me-1"></i> Back to Quotations
            </a>
        </div>

        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <div class="rounded p-2 bg-primary-subtle text-primary" style="font-size: 14px;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Create Commercial Quotation</h5>
                    <p class="text-muted small mb-0">Specify client profile, deliverables schedule, itemized costs, and tax breakdown.</p>
                </div>
            </div>

            <div class="card-body p-4">
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger py-2 small mb-4">
                        <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Please resolve the following errors:</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.quotations.store') }}">
                    @csrf

                    <!-- Client & Project Link -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-building text-primary"></i> Client Association & Scope
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Target Client <span class="text-danger">*</span></label>
                            <select name="client_profile_id" class="form-select" required>
                                <option value="">Select Client...</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_profile_id', request('client_id')) == $c->id ? 'selected' : '' }}>
                                        {{ $c->company_name ?? $c->user->name }} ({{ $c->client_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Linked Project (Optional)</label>
                            <select name="project_id" class="form-select">
                                <option value="">Standalone Scope / Proposal</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ old('project_id', request('project_id')) == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->project_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Quotation Dates & Currency -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-calendar-check text-warning"></i> Validity & Pricing Currency
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Offer Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Currency <span class="text-danger">*</span></label>
                            <input type="text" name="currency" class="form-control" value="INR" required>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-list-check text-success"></i> Billable Deliverables & Line Items
                    </div>

                    <!-- Item Row 1 -->
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Deliverable Description</label>
                                <input type="text" name="items[0][description]" class="form-control" value="Core Backend Architecture & API Development" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Qty</label>
                                <input type="number" step="0.01" name="items[0][quantity]" class="form-control font-monospace" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Unit Price (₹)</label>
                                <input type="number" step="0.01" name="items[0][unit_price]" class="form-control font-monospace" value="35000.00" required>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small fw-semibold">Discount</label>
                                <input type="number" step="0.01" name="items[0][discount]" class="form-control font-monospace" value="0.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Tax Rate (%)</label>
                                <input type="number" step="0.01" name="items[0][tax_rate]" class="form-control font-monospace" value="18.00">
                            </div>
                        </div>
                    </div>

                    <!-- Item Row 2 -->
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="row g-2">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold">Deliverable Description</label>
                                <input type="text" name="items[1][description]" class="form-control" value="Client Portal UI & GitHub Webhook Setup">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Qty</label>
                                <input type="number" step="0.01" name="items[1][quantity]" class="form-control font-monospace" value="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Unit Price (₹)</label>
                                <input type="number" step="0.01" name="items[1][unit_price]" class="form-control font-monospace" value="15000.00">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small fw-semibold">Discount</label>
                                <input type="number" step="0.01" name="items[1][discount]" class="form-control font-monospace" value="0.00">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold">Tax Rate (%)</label>
                                <input type="number" step="0.01" name="items[1][tax_rate]" class="form-control font-monospace" value="18.00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Client Scope Notes</label>
                        <textarea name="client_notes" class="form-control" rows="2" placeholder="Specific technical requirements or notes for the customer...">{{ old('client_notes') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Commercial Terms & Conditions</label>
                        <textarea name="terms_and_conditions" class="form-control" rows="2">50% Advance on project kickoff, 50% upon deployment and delivery approval. Valid for 30 calendar days.</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('admin.quotations.index') }}" class="btn btn-light border">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-check-circle me-1"></i> Generate Quotation Proposal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
