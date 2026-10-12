@extends('layouts.admin')

@section('page_title', 'Project: ' . $project->name)

@section('content')
<!-- Header Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-light border">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">{{ $project->name }}</h4>
                <span class="badge badge-soft-primary font-monospace">{{ $project->project_number }}</span>
                @php
                    $pBadge = match($project->status) {
                        'Completed' => 'badge-soft-success',
                        'In Development' => 'badge-soft-primary',
                        'Testing', 'Client Review' => 'badge-soft-warning',
                        default => 'badge-soft-dark'
                    };
                @endphp
                <span class="badge {{ $pBadge }}">{{ $project->status }}</span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Client: <a href="{{ route('admin.clients.show', $project->client) }}" class="fw-semibold text-decoration-none text-dark">{{ $project->client->company_name ?? $project->client->user->name }}</a>
                &bull; Category: <span class="fw-medium">{{ $project->category }}</span>
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @hasanyrole('Super Admin|Administrator')
            <a href="{{ route('admin.quotations.create', ['client_id' => $project->client_profile_id, 'project_id' => $project->id]) }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-invoice me-1"></i> New Quotation
            </a>
        @endhasanyrole
        <span class="badge badge-soft-primary px-3 py-2 fs-6 font-monospace">
            {{ $project->progress_percentage }}% Complete
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Specs, Milestones, and Real-time Timeline -->
    <div class="col-lg-8">
        <!-- Overview Card -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">{{ $project->name }}</h5>
                    <div class="text-muted small">Target Scope & Deliverables Overview</div>
                </div>
                <span class="badge badge-soft-{{ $project->priority === 'Critical' ? 'danger' : ($project->priority === 'High' ? 'warning' : 'dark') }}">
                    Priority: {{ $project->priority }}
                </span>
            </div>
            
            <p class="text-secondary small mb-3" style="line-height: 1.6;">
                {{ $project->description ?: 'No detailed scope description registered for this project.' }}
            </p>

            <div class="row g-3 pt-3 border-top">
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Start Date</div>
                    <div class="fw-semibold text-dark small mt-1">
                        <i class="fas fa-calendar-day text-primary me-1"></i> {{ $project->start_date ? $project->start_date->format('d M Y') : 'TBD' }}
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Target Delivery</div>
                    <div class="fw-semibold text-dark small mt-1">
                        <i class="fas fa-flag text-danger me-1"></i> {{ $project->expected_delivery_date ? $project->expected_delivery_date->format('d M Y') : 'TBD' }}
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Allocated Budget</div>
                    <div class="fw-bold text-success small mt-1">
                        ₹{{ number_format($project->budget, 2) }}
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Technologies</div>
                    <div class="small text-muted mt-1 text-truncate" title="{{ $project->technologies }}">
                        {{ $project->technologies ?: 'Not Specified' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Milestones Section -->
        <div class="card border-0 shadow-sm bg-white mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-flag-checkered text-warning"></i>
                    <h6 class="fw-bold mb-0 text-dark">Project Milestones ({{ $project->milestones->count() }})</h6>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                    <i class="fas fa-plus me-1"></i> Add Milestone
                </button>
            </div>
            <div class="card-body p-4">
                @forelse($project->milestones as $m)
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-3 border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold text-dark small">{{ $m->title }}</span>
                                @if($m->is_client_visible)
                                    <span class="badge badge-soft-info" style="font-size: 10px;">Client Visible</span>
                                @else
                                    <span class="badge badge-soft-dark" style="font-size: 10px;">Internal Only</span>
                                @endif
                            </div>
                            <div class="text-muted small mt-1" style="font-size: 11.5px;">
                                <i class="fas fa-clock me-1 opacity-75"></i> Due: {{ $m->due_date ? $m->due_date->format('d M Y') : 'Unscheduled' }}
                                @if($m->description)
                                    &bull; <span class="text-secondary">{{ Str::limit($m->description, 60) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form method="POST" action="{{ route('admin.projects.milestones.status', [$project, $m]) }}" class="d-inline-flex">
                                @csrf
                                <select name="status" class="form-select form-select-sm py-1 px-2" style="font-size: 11.5px; width: 120px;" onchange="this.form.submit()">
                                    <option value="pending" {{ $m->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $m->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $m->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $m->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                            <span class="badge {{ $m->status === 'completed' ? 'badge-soft-success' : ($m->status === 'in_progress' ? 'badge-soft-primary' : 'badge-soft-dark') }} font-monospace">
                                {{ $m->progress }}%
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <div class="mb-2"><i class="fas fa-flag fa-2x opacity-25"></i></div>
                        <div class="small fw-semibold text-dark">No Milestones Defined</div>
                        <p class="small text-muted mb-3">Break down this project scope into delivery milestones.</p>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                            <i class="fas fa-plus me-1"></i> Add First Milestone
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Chronological Timeline Events -->
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-history text-info"></i>
                <h6 class="fw-bold mb-0 text-dark">Chronological Audit Timeline</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    @forelse($project->timelineEvents as $event)
                        <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-dark">{{ $event->title }}</span>
                                <span class="text-muted font-monospace" style="font-size: 11px;">
                                    {{ $event->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <div class="small text-secondary" style="line-height: 1.5;">{{ $event->description }}</div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-timeline fa-2x opacity-25 mb-2 d-block"></i>
                            No timeline log entries posted yet. Progress updates will append here.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status & Progress Controls, Repositories -->
    <div class="col-lg-4">
        <!-- Update Progress Card -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-sliders text-primary"></i> Stage & Progress Controller
            </h6>
            <form method="POST" action="{{ route('admin.projects.status', $project) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Lifecycle Stage</label>
                    <select name="status" class="form-select" required>
                        @foreach(['Planning', 'In Development', 'Testing', 'Client Review', 'Revision Required', 'Completed', 'On Hold'] as $st)
                            <option value="{{ $st }}" {{ $project->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-semibold mb-0">Progress Percentage</label>
                        <span class="badge badge-soft-primary font-monospace fw-bold" id="progVal">{{ $project->progress_percentage }}%</span>
                    </div>
                    <input type="range" class="form-range" name="progress_percentage" min="0" max="100" value="{{ $project->progress_percentage }}" oninput="document.getElementById('progVal').innerText = this.value + '%'">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Timeline Update Note</label>
                    <textarea name="timeline_comment" class="form-control" rows="2" placeholder="e.g. Deployed API endpoints to staging cluster..."></textarea>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="is_client_visible" value="1" checked id="visCheck">
                    <label class="form-check-label small fw-medium" for="visCheck">Expose event on customer portal</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-check-circle me-1"></i> Save Stage & Progress
                </button>
            </form>
        </div>

        <!-- Connected Git Repositories -->
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fab fa-github fa-lg text-dark"></i>
                    <h6 class="fw-bold mb-0 text-dark">Git Integration</h6>
                </div>
                <a href="{{ route('admin.github.index') }}" class="small text-decoration-none fw-semibold">Manage</a>
            </div>
            
            @forelse($project->repositories as $repo)
                <div class="p-3 border rounded-3 mb-2 bg-light">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold text-dark small">{{ $repo->repo_owner }}/{{ $repo->repo_name }}</div>
                            <div class="text-muted font-monospace mt-1" style="font-size: 11px;">
                                <i class="fas fa-code-branch me-1"></i> branch: <code>{{ $repo->default_branch }}</code>
                            </div>
                        </div>
                        @if($repo->latest_release_tag)
                            <span class="badge badge-soft-success font-monospace">{{ $repo->latest_release_tag }}</span>
                        @endif
                    </div>
                    <div class="mt-3">
                        <form method="POST" action="{{ route('admin.github.sync', $repo) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light border w-100 text-dark" style="font-size: 12px;">
                                <i class="fas fa-rotate me-1 text-primary"></i> Sync Repository Commits
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    <i class="fab fa-github fa-2x opacity-25 mb-2 d-block"></i>
                    <div class="mb-2">No Git repository linked yet.</div>
                    <a href="{{ route('admin.github.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-dark w-100">
                        <i class="fab fa-github me-1"></i> Connect GitHub Repo
                    </a>
                </div>
            @endforelse

            @if($project->repositories->isNotEmpty())
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.github.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-light border w-100 text-muted" style="font-size: 12px;">
                        <i class="fas fa-plus me-1"></i> Connect Another Repo
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Add Milestone -->
<div class="modal fade" id="addMilestoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.projects.milestone', $project) }}">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-flag-checkered text-warning me-1"></i> New Project Milestone
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Milestone Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Architecture Signoff & Staging Deployment" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deliverable Scope & Criteria</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Define completion conditions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Create Milestone</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
