@extends('layouts.admin')

@section('page_title', 'Project Directory')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Projects & Delivery Milestones</h4>
        <p class="text-muted small mb-0">Track active engineering projects, sprint progress, connected Git repositories, and delivery milestones.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus"></i> Create Project
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-2 align-items-center">
            <div class="col-lg-6 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-2" placeholder="Search by Project # or title..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Project Stages</option>
                    @foreach(['Planning', 'In Development', 'Testing', 'Client Review', 'Completed'] as $stage)
                        <option value="{{ $stage }}" {{ request('status') === $stage ? 'selected' : '' }}>{{ $stage }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-light border" title="Reset">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Project ID</th>
                    <th>Project Name & Scope</th>
                    <th>Client Organization</th>
                    <th>Lifecycle Stage</th>
                    <th style="min-width: 150px;">Progress</th>
                    <th>Budget</th>
                    <th>Milestones</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $p)
                    <tr>
                        <td>
                            <span class="badge badge-soft-primary font-monospace px-2 py-1 fw-bold">
                                {{ $p->project_number }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.projects.show', $p) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                {{ $p->name }}
                            </a>
                            <div class="text-muted small" style="font-size: 11.5px;">
                                <i class="fas fa-layer-group me-1 opacity-75"></i> {{ $p->category }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-2 text-white fw-bold shadow-xs" 
                                     style="width: 28px; height: 28px; font-size: 11px; background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);">
                                    {{ strtoupper(substr($p->client->company_name ?? $p->client->user->name, 0, 1)) }}
                                </div>
                                <span class="fw-medium text-dark small">
                                    {{ $p->client->company_name ?? $p->client->user->name }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusBadge = match($p->status) {
                                    'Completed' => 'badge-soft-success',
                                    'In Development' => 'badge-soft-primary',
                                    'Testing', 'Client Review' => 'badge-soft-warning',
                                    default => 'badge-soft-dark'
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; background-color: #e2e8f0; border-radius: 4px;">
                                    <div class="progress-bar {{ $p->progress_percentage == 100 ? 'bg-success' : 'bg-primary' }}" 
                                         role="progressbar" 
                                         style="width: {{ $p->progress_percentage }}%; border-radius: 4px;"></div>
                                </div>
                                <span class="fw-semibold small text-muted font-monospace" style="font-size: 11px;">{{ $p->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td><span class="fw-semibold text-dark">₹{{ number_format($p->budget, 2) }}</span></td>
                        <td>
                            <span class="badge badge-soft-dark border">
                                <i class="fas fa-flag me-1 opacity-75"></i> {{ $p->milestones->count() }} milestones
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.projects.show', $p) }}" class="btn btn-sm btn-outline-primary py-1 px-3">
                                View <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="fas fa-diagram-project fa-3x opacity-25"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Projects Found</h6>
                            <p class="text-muted small mb-3">No active client projects match your current search or status filter.</p>
                            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Create New Project
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }} projects</span>
            <div>{{ $projects->links() }}</div>
        </div>
    @endif
</div>
@endsection
