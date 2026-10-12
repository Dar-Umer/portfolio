@extends('layouts.admin')

@section('page_title', 'Edit Client: ' . $client->client_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-sm btn-light border">
                <i class="fas fa-arrow-left me-1"></i> Back to Client Profile
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-soft-primary font-monospace">{{ $client->client_number }}</span>
                @if($client->account_status === 'active')
                    <span class="badge badge-soft-success">Active</span>
                @elseif($client->account_status === 'suspended')
                    <span class="badge badge-soft-danger">Suspended</span>
                @else
                    <span class="badge badge-soft-dark">{{ ucfirst($client->account_status) }}</span>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <div class="rounded p-2 bg-primary-subtle text-primary" style="font-size: 14px;">
                    <i class="fas fa-user-gear"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Edit Client Profile & Business Info</h5>
                    <p class="text-muted small mb-0">Update verified contact names, company details, billing address, and account lifecycle state.</p>
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

                <form method="POST" action="{{ route('admin.clients.update', $client) }}">
                    @csrf
                    @method('PUT')

                    <!-- Company & Contact -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-building text-primary"></i> Company & Contact Identity
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Primary Contact Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $client->user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company / Organization Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $client->company_name) }}" placeholder="e.g. Acme Tech Labs Pvt Ltd">
                        </div>
                    </div>

                    <!-- Communication & Status -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-sliders text-warning"></i> Access, Tax & Lifecycle
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Account Login Email (Read-only)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                                <input type="email" class="form-control bg-light" value="{{ $client->user->email }}" readonly disabled>
                            </div>
                            <span class="text-muted" style="font-size: 11px;">Primary authentication email cannot be altered here.</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Phone / WhatsApp Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}" placeholder="+91 9876543210">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">GSTIN / Tax ID</label>
                            <input type="text" name="tax_number" class="form-control font-monospace text-uppercase" value="{{ old('tax_number', $client->tax_number) }}" placeholder="e.g. 09ABCDE1234F1Z5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tier Category</label>
                            <select name="client_category" class="form-select" required>
                                @foreach(['Standard', 'Enterprise', 'Startup', 'Agency'] as $cat)
                                    <option value="{{ $cat }}" {{ old('client_category', $client->client_category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Account Lifecycle Status</label>
                            <select name="account_status" class="form-select" required>
                                <option value="active" {{ old('account_status', $client->account_status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('account_status', $client->account_status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="archived" {{ old('account_status', $client->account_status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-map-location-dot text-info"></i> Invoicing Location & Notes
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Billing Street Address</label>
                        <textarea name="billing_address" class="form-control" rows="2">{{ old('billing_address', $client->billing_address) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $client->city) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">State</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', $client->state) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Postal / PIN Code</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $client->postal_code) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $client->country ?? 'India') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Private Internal Staff Notes</label>
                            <input type="text" name="notes" class="form-control" value="{{ old('notes', $client->notes) }}" placeholder="Internal account instructions...">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-light border">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-save me-1"></i> Save Client Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
