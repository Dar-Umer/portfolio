@extends('layouts.admin')

@section('page_title', 'Create New Client')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="mb-4">
                <a href="{{ route('admin.clients.index') }}" class="small text-muted text-decoration-none mb-2 d-inline-block">&larr; Back to Directory</a>
                <h5 class="fw-bold mb-1">Onboard New Customer Account</h5>
                <p class="text-muted small">Generates user account and client business profile with tax details.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.clients.store') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Contact Person Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Company / Organization Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="e.g. Acme Tech Labs Pvt Ltd">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address (Login Username)</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Temporary Password</label>
                        <input type="password" name="password" class="form-control" value="Welcome2026!" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">GSTIN / Tax Registration No.</label>
                        <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number') }}" placeholder="e.g. 09ABCDE1234F1Z5">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Billing Address</label>
                    <textarea name="billing_address" class="form-control" rows="2">{{ old('billing_address') }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">City</label>
                        <input type="text" name="city" class="form-control form-control-sm" value="{{ old('city') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">State</label>
                        <input type="text" name="state" class="form-control form-control-sm" value="{{ old('state', 'Uttar Pradesh') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Postal / PIN Code</label>
                        <input type="text" name="postal_code" class="form-control form-control-sm" value="{{ old('postal_code') }}">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Country</label>
                        <input type="text" name="country" class="form-control form-control-sm" value="{{ old('country', 'India') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Category</label>
                        <select name="client_category" class="form-select form-select-sm" required>
                            <option value="Standard">Standard</option>
                            <option value="Enterprise">Enterprise</option>
                            <option value="Startup">Startup</option>
                            <option value="Agency">Agency</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    Create Client Account <i class="fas fa-check ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
