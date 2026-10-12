@extends('layouts.admin')

@section('page_title', 'Onboard Client')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="mb-3">
            <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-light border">
                <i class="fas fa-arrow-left me-1"></i> Back to Directory
            </a>
        </div>

        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <div class="rounded p-2 bg-primary-subtle text-primary" style="font-size: 14px;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Onboard New Customer Account</h5>
                    <p class="text-muted small mb-0">Creates login credentials, sets business tax profiles, and initiates customer portal access.</p>
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

                <form method="POST" action="{{ route('admin.clients.store') }}">
                    @csrf

                    <!-- Organization & Contact Profile -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-building text-primary"></i> Company & Contact Identity
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Primary Contact Person <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" value="{{ old('name', request('name')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company / Brand Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', request('company_name')) }}" placeholder="e.g. Apex Innovations Pvt Ltd">
                        </div>
                    </div>

                    <!-- Authentication & Access Credentials -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-key text-warning"></i> Portal Credentials & Contact
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Login Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="client@company.com" value="{{ old('email', request('email')) }}" required>
                            <span class="text-muted" style="font-size: 11px;">The client uses this email to log in to their dashboard.</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control font-monospace" value="Welcome2026!" required>
                            <span class="text-muted" style="font-size: 11px;">Default temporary password. User can change upon login.</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Phone / WhatsApp Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210" value="{{ old('phone', request('phone')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">GSTIN / Tax ID Number</label>
                            <input type="text" name="tax_number" class="form-control font-monospace text-uppercase" value="{{ old('tax_number') }}" placeholder="e.g. 07AAAAA0000A1Z5">
                        </div>
                    </div>

                    <!-- Billing & Invoicing Address -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-map-location-dot text-info"></i> Invoicing & Regional Details
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Billing Street Address</label>
                        <textarea name="billing_address" class="form-control" rows="2" placeholder="Suite / Floor / Street Address...">{{ old('billing_address', request('notes')) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="e.g. New Delhi">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">State / Province</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', 'Uttar Pradesh') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Postal / PIN Code</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}" placeholder="e.g. 201301">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', 'India') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Client Tier / Category</label>
                            <select name="client_category" class="form-select" required>
                                <option value="Standard">Standard Account</option>
                                <option value="Enterprise">Enterprise Partner</option>
                                <option value="Startup">Early-stage Startup</option>
                                <option value="Agency">Agency / White Label</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Private Internal Staff Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Internal instructions, client background or requirements...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-light border">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-check-circle me-1"></i> Complete Onboarding
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
