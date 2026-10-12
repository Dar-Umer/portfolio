@extends('layouts.admin')

@section('page_title', 'Create New Project')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="mb-3">
            <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-light border">
                <i class="fas fa-arrow-left me-1"></i> Back to Projects
            </a>
        </div>

        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <div class="rounded p-2 bg-primary-subtle text-primary" style="font-size: 14px;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Initiate Client Project</h5>
                    <p class="text-muted small mb-0">Links project to customer organization, sets initial budget, and initializes progress ledger.</p>
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

                @if(request('client_id'))
                    @php $selectedClient = $clients->firstWhere('id', request('client_id')); @endphp
                    @if($selectedClient)
                        <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-4">
                            <i class="fas fa-building text-info me-2"></i>
                            <div>Preselected Client: <strong>{{ $selectedClient->company_name ?? $selectedClient->user->name }}</strong> (<code>{{ $selectedClient->client_number }}</code>)</div>
                        </div>
                    @endif
                @endif

                <form method="POST" action="{{ route('admin.projects.store') }}">
                    @csrf

                    <!-- Client & Classification -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-building text-primary"></i> Client & Project Classification
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold mb-0">Client Organization <span class="text-danger">*</span></label>
                                @hasanyrole('Super Admin|Administrator')
                                    <a href="{{ route('admin.clients.create') }}" target="_blank" class="small text-decoration-none" style="font-size: 11px;">
                                        <i class="fas fa-user-plus me-1"></i> New Client?
                                    </a>
                                @endhasanyrole
                            </div>
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
                            <label class="form-label small fw-semibold">Project Domain Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="Web Application">Web Application</option>
                                <option value="SaaS Platform">SaaS Platform</option>
                                <option value="API & Microservices">API & Microservices</option>
                                <option value="DevOps & Infrastructure">DevOps & Infrastructure</option>
                                <option value="Mobile API">Mobile API</option>
                            </select>
                        </div>
                    </div>

                    <!-- Scope & Details -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-file-lines text-warning"></i> Deliverable Scope & Specifications
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Project Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Apex Automated Order Processing Portal" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Project Description & Objectives</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Outline the business objectives, key deliverables, and scope boundaries...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Timeline & Financials -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-coins text-success"></i> Schedule, Tech Stack & Commercials
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Target Delivery Date</label>
                            <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Priority Level</label>
                            <select name="priority" class="form-select" required>
                                <option value="Low">Low Priority</option>
                                <option value="Normal" selected>Normal</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Agreed Project Budget (INR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" step="0.01" name="budget" class="form-control font-monospace" value="{{ old('budget', '50000.00') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Technology Stack (Comma separated)</label>
                            <input type="text" name="technologies" class="form-control" value="{{ old('technologies', 'Laravel 12, MySQL, Bootstrap 5, Vite') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Private Internal Notes</label>
                        <textarea name="internal_notes" class="form-control" rows="2" placeholder="Private internal scope notes (never visible to client)...">{{ old('internal_notes') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-light border">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-check-circle me-1"></i> Create Project & Initialize
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
