@extends('layouts.admin')

@section('page_title', 'System Settings & Branding')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-1">Global Platform Configuration</h5>
            <p class="text-muted small mb-4">Manage branding, business identity, GST tax profile, and operational defaults.</p>

            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf

                <!-- General -->
                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-sliders-h me-2"></i> General Identity & Branding</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Site Name</label>
                        <input type="text" name="site_name" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('site_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tagline</label>
                        <input type="text" name="site_tagline" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('site_tagline') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('contact_email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('contact_phone') }}">
                    </div>
                </div>

                <hr class="my-4">

                <!-- Business & Tax -->
                <h6 class="fw-bold text-success mb-3"><i class="fas fa-file-invoice-dollar me-2"></i> Business & GST Tax Settings</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold">Business Address</label>
                        <input type="text" name="business_address" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('business_address') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Seller GSTIN</label>
                        <input type="text" name="seller_gstin" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('seller_gstin') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Seller State (Place of Supply)</label>
                        <input type="text" name="seller_state" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('seller_state') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Default Currency</label>
                        <input type="text" name="default_currency" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('default_currency') }}">
                    </div>
                </div>

                <hr class="my-4">

                <!-- Tickets & SLA -->
                <h6 class="fw-bold text-warning mb-3"><i class="fas fa-ticket-alt me-2"></i> Ticket & SLA Defaults</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Ticket Reopening Allowed Window (Days)</label>
                        <input type="number" name="ticket_reopen_days" class="form-control form-control-sm" value="{{ \App\Models\Setting::get('ticket_reopen_days') }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    Save System Settings <i class="fas fa-save ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
