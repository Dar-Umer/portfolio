@extends('layouts.admin')

@section('page_title', 'System Settings & Branding')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Global System Configuration</h4>
                <p class="text-muted small mb-0">Manage platform identity, business legal metadata, GST tax registration, and support SLA defaults.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-white overflow-hidden mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf

                    <!-- Identity & Branding -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-sliders text-primary"></i> Platform Identity & Branding
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Site / Enterprise Name <span class="text-danger">*</span></label>
                            <input type="text" name="site_name" class="form-control" value="{{ \App\Models\Setting::get('site_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Marketing Tagline</label>
                            <input type="text" name="site_tagline" class="form-control" value="{{ \App\Models\Setting::get('site_tagline') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Public Support Email <span class="text-danger">*</span></label>
                            <input type="email" name="contact_email" class="form-control" value="{{ \App\Models\Setting::get('contact_email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Helpline Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ \App\Models\Setting::get('contact_phone') }}">
                        </div>
                    </div>

                    <!-- Business Legal & Tax -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-receipt text-success"></i> Business Legal & GST Tax Profile
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold">Registered Commercial Address</label>
                            <input type="text" name="business_address" class="form-control" value="{{ \App\Models\Setting::get('business_address') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Seller GSTIN</label>
                            <input type="text" name="seller_gstin" class="form-control font-monospace text-uppercase" value="{{ \App\Models\Setting::get('seller_gstin') }}" placeholder="09ABCDE1234F1Z5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Seller State (Place of Supply)</label>
                            <input type="text" name="seller_state" class="form-control" value="{{ \App\Models\Setting::get('seller_state') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Default Currency</label>
                            <input type="text" name="default_currency" class="form-control font-monospace text-uppercase" value="{{ \App\Models\Setting::get('default_currency') }}">
                        </div>
                    </div>

                    <!-- Ticket SLA Policy -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-clock text-warning"></i> Customer Support SLA Policy
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Ticket Reopening Allowed Window (Days)</label>
                            <input type="number" name="ticket_reopen_days" class="form-control font-monospace" value="{{ \App\Models\Setting::get('ticket_reopen_days') }}">
                            <span class="text-muted mt-1 d-block" style="font-size: 11px;">Number of days client can reopen resolved tickets before a new ticket is required.</span>
                        </div>
                    </div>

                    <div class="pt-3 border-top mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-save me-1"></i> Save Platform Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
