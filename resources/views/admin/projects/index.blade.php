@extends('layouts.admin')

@section('page_title', 'Project Directory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted small mb-0">Track project milestones, timelines, connected Git repos, and deliverable status.</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> New Project
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by Project # or Name..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Project Statuses</option>
                    <option value="Planning" {{ request('status') === 'Planning' ? 'selected' : '' }}>Planning</option>
                    <option value="In Development" {{ request('status') === 'In Development' ? 'selected' : '' }}>In Development</option>
                    <option value="Testing" {{ request('status') === 'Testing' ? 'selected' : '' }}>Testing</option>
                    <option value="Client Review" {{ request('status') === 'Client Review' ? 'selected' : '' }}>Client Review</option>
                    <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-light border text-muted" title="Reset"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Project #</th>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th style="min-width: 140px;">Progress</th>
                    <th>Budget</th>
                    <th>Milestones</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $p)
                    <tr>
                        <td><span class="fw-bold font-monospace text-primary">{{ $p->project_number }}</span></td>
                        <td>
                            <a href="{{ route('admin.projects.show', $p) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                {{ $p->name }}
                            </a>
                            <div class="text-muted small" style="font-size: 11.5px;">{{ $p->category }}</div>
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ $p->client->company_name ?? $p->client->user->name }}</div>
                        </td>
                        <td>
                            @php
                                $statusBadge = match($p->status) {
                                    'Completed' => 'bg-success',
                                    'In Development' => 'bg-primary',
                                    'Testing', 'Client Review' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ $p->status }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; background-color: #e2e8f0; border-radius: 4px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $p->progress_percentage }}%; border-radius: 4px;"></div>
                                </div>
                                <span class="fw-semibold small text-muted" style="font-size: 11px;">{{ $p->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td><span class="fw-semibold">₹{{ number_format($p->budget, 2) }}</span></td>
                        <td><span class="badge bg-light text-dark border">{{ $p->milestones->count() }} milestones</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.projects.show', $p) }}" class="btn btn-sm btn-outline-primary py-1 px-3">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No projects found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
