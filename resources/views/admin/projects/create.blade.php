@extends('layouts.admin')

@section('page_title', 'Create New Project')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="mb-4">
                <a href="{{ route('admin.projects.index') }}" class="small text-muted text-decoration-none mb-2 d-inline-block">&larr; Back to Projects</a>
                <h5 class="fw-bold mb-1">Create Client Project</h5>
                <p class="text-muted small">Associates project with client profile and initiates the timeline ledger.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.projects.store') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Client Association</label>
                        <select name="client_profile_id" class="form-select form-select-sm" required>
                            <option value="">Select Client...</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ old('client_profile_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->company_name ?? $c->user->name }} ({{ $c->client_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Category</label>
                        <select name="category" class="form-select form-select-sm" required>
                            <option value="Web Application">Web Application</option>
                            <option value="SaaS Platform">SaaS Platform</option>
                            <option value="API & Microservices">API & Microservices</option>
                            <option value="DevOps & Infrastructure">DevOps & Infrastructure</option>
                            <option value="Mobile API">Mobile API</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Project Title</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Acme Automated Order Processing Portal" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Project Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ old('start_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" class="form-control form-control-sm" value="{{ old('expected_delivery_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Priority</label>
                        <select name="priority" class="form-select form-select-sm" required>
                            <option value="Low">Low</option>
                            <option value="Normal" selected>Normal</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Agreed Budget (INR)</label>
                        <input type="number" step="0.01" name="budget" class="form-control form-control-sm" value="{{ old('budget', '50000.00') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Technologies (Comma separated)</label>
                        <input type="text" name="technologies" class="form-control form-control-sm" value="{{ old('technologies', 'Laravel 12, MySQL, Bootstrap 5') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Internal Notes</label>
                    <textarea name="internal_notes" class="form-control form-control-sm" rows="2" placeholder="Private internal scope details...">{{ old('internal_notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    Create Project & Initialize Timeline <i class="fas fa-check ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
