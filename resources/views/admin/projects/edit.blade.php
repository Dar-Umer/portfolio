@extends('layouts.admin')

@section('page_title', 'Edit Project: ' . $project->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-sm btn-light border">
                <i class="fas fa-arrow-left me-1"></i> Back to Project Overview
            </a>
            <span class="badge badge-soft-primary font-monospace">{{ $project->project_number }}</span>
        </div>

        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <div class="rounded p-2 bg-primary-subtle text-primary" style="font-size: 14px;">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Edit Project Specifications</h5>
                    <p class="text-muted small mb-0">Update scope, allocated budget, schedule dates, tech stack, and lifecycle stage.</p>
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

                <form method="POST" action="{{ route('admin.projects.update', $project) }}">
                    @csrf
                    @method('PUT')

                    <!-- Client & Classification -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-building text-primary"></i> Client & Project Classification
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Client Organization <span class="text-danger">*</span></label>
                            <select name="client_profile_id" class="form-select" required>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_profile_id', $project->client_profile_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->company_name ?? $c->user->name }} ({{ $c->client_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Project Domain Category <span class="text-danger">*</span></label>
                            <input list="category_list" name="category" class="form-control" value="{{ old('category', $project->category) }}" placeholder="Select or type custom category..." required>
                            <datalist id="category_list">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <!-- Scope & Details -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-file-lines text-warning"></i> Deliverable Scope & Specifications
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Project Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Project Description & Objectives</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <!-- Timeline & Status -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-calendar-check text-info"></i> Schedule & Lifecycle Status
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Target Delivery Date</label>
                            <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date', $project->expected_delivery_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Actual Completion Date</label>
                            <input type="date" name="actual_completion_date" class="form-control" value="{{ old('actual_completion_date', $project->actual_completion_date?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Lifecycle Stage <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                @foreach(['Planning', 'In Development', 'Testing', 'Client Review', 'Revision Required', 'Completed', 'On Hold'] as $st)
                                    <option value="{{ $st }}" {{ old('status', $project->status) === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Priority Level <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                @foreach(['Low', 'Normal', 'High', 'Critical'] as $pr)
                                    <option value="{{ $pr }}" {{ old('priority', $project->priority) === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Deployed Version Tag</label>
                            <input type="text" name="current_deployed_version" class="form-control font-monospace" value="{{ old('current_deployed_version', $project->current_deployed_version) }}" placeholder="v1.0.4">
                        </div>
                    </div>

                    <!-- Financials & Tech Stack -->
                    <div class="d-flex align-items-center gap-2 mb-3 text-uppercase text-muted fw-bold" style="font-size: 11px; letter-spacing: 0.05em;">
                        <i class="fas fa-coins text-success"></i> Commercials & Technology Stack
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Agreed Project Budget (INR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" step="0.01" name="budget" class="form-control font-monospace" value="{{ old('budget', $project->budget) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Technologies Stack</label>
                            @php
                                $techVal = is_array($project->technologies) ? implode(', ', $project->technologies) : ($project->technologies ?? '');
                            @endphp
                            <input type="text" name="technologies" class="form-control" value="{{ old('technologies', $techVal) }}" placeholder="Laravel, Vue.js, Tailwind, MySQL">
                            <span class="text-muted" style="font-size: 11px;">Comma-separated technology stack keywords.</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Internal Notes (Staff Only)</label>
                        <textarea name="internal_notes" class="form-control" rows="2" placeholder="Private internal technical or client management notes...">{{ old('internal_notes', $project->internal_notes) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-light border">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-check-circle me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
