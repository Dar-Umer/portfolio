@extends('layouts.admin')

@section('page_title', 'Project: ' . $project->name)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('admin.projects.index') }}" class="small text-muted text-decoration-none">&larr; Back to Projects</a>
    <span class="badge bg-primary px-3 py-2">{{ $project->status }} ({{ $project->progress_percentage }}%)</span>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Specs, Milestones, and Real-time Timeline -->
    <div class="col-lg-8">
        <!-- Overview Card -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-2">{{ $project->name }}</h5>
            <div class="text-muted small mb-3">Project #{{ $project->project_number }} &bull; Client: {{ $project->client->company_name ?? $project->client->user->name }}</div>
            <p class="text-secondary small mb-3">{{ $project->description ?? 'No project description provided.' }}</p>

            <div class="row g-2 pt-2 border-top small">
                <div class="col-md-3"><strong>Start Date:</strong> {{ $project->start_date ? $project->start_date->format('d M Y') : 'TBD' }}</div>
                <div class="col-md-3"><strong>Target Delivery:</strong> {{ $project->expected_delivery_date ? $project->expected_delivery_date->format('d M Y') : 'TBD' }}</div>
                <div class="col-md-3"><strong>Budget:</strong> ₹{{ number_format($project->budget, 2) }}</div>
                <div class="col-md-3"><strong>Priority:</strong> <span class="badge bg-secondary">{{ $project->priority }}</span></div>
            </div>
        </div>

        <!-- Milestones Section -->
        <div class="card border-0 shadow-sm bg-white mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-flag-checkered text-warning me-2"></i> Project Milestones</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                    <i class="fas fa-plus me-1"></i> Add Milestone
                </button>
            </div>
            <div class="card-body">
                @forelse($project->milestones as $m)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $m->title }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Due: {{ $m->due_date ? $m->due_date->format('d M Y') : 'Unscheduled' }} &bull; {{ $m->is_client_visible ? 'Visible to Client' : 'Internal Staff Only' }}</div>
                        </div>
                        <div>
                            <span class="badge {{ $m->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($m->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">No milestones established yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Chronological Timeline Events -->
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-info me-2"></i> Real Chronological Timeline</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($project->timelineEvents as $event)
                        <div class="p-3 bg-light rounded border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small">{{ $event->title }}</span>
                                <span class="text-muted" style="font-size: 0.75rem;">{{ $event->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="small text-secondary">{{ $event->description }}</div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">No timeline events recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status & Progress Controls, Repositories -->
    <div class="col-lg-4">
        <!-- Update Progress Card -->
        <div class="card border-0 shadow-sm p-3 bg-white mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-tasks text-primary me-2"></i> Update Progress & Status</h6>
            <form method="POST" action="{{ route('admin.projects.status', $project) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Project Stage</label>
                    <select name="status" class="form-select form-select-sm" required>
                        @foreach(['Planning', 'In Development', 'Testing', 'Client Review', 'Revision Required', 'Completed', 'On Hold'] as $st)
                            <option value="{{ $st }}" {{ $project->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Progress Percentage: <span id="progVal">{{ $project->progress_percentage }}</span>%</label>
                    <input type="range" class="form-range" name="progress_percentage" min="0" max="100" value="{{ $project->progress_percentage }}" oninput="document.getElementById('progVal').innerText = this.value">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Timeline Update Note</label>
                    <textarea name="timeline_comment" class="form-control form-control-sm" rows="2" placeholder="Describe the latest deliverable..."></textarea>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_client_visible" value="1" checked id="visCheck">
                    <label class="form-check-label small" for="visCheck">Expose in client timeline</label>
                </div>

                <button type="submit" class="btn btn-primary btn-sm w-100">Save Stage & Progress</button>
            </form>
        </div>

        <!-- Connected Git Repositories -->
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="fab fa-github me-1"></i> Git Integration</h6>
                <a href="{{ route('admin.github.index') }}" class="btn btn-sm btn-link p-0 small">Manage</a>
            </div>
            @forelse($project->repositories as $repo)
                <div class="p-2 border rounded mb-2 small">
                    <div class="fw-semibold">{{ $repo->repo_owner }}/{{ $repo->repo_name }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Branch: <code>{{ $repo->default_branch }}</code></div>
                    @if($repo->latest_release_tag)
                        <div class="mt-1"><span class="badge bg-success">Latest: {{ $repo->latest_release_tag }}</span></div>
                    @endif
                    <div class="mt-2">
                        <form method="POST" action="{{ route('admin.github.sync', $repo) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-light border w-100" style="font-size: 0.75rem;">
                                <i class="fas fa-sync-alt me-1"></i> Sync Repository
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-2 text-muted small">No Git repository linked.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal: Add Milestone -->
<div class="modal fade" id="addMilestoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.projects.milestone', $project) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fs-6 fw-bold">New Project Milestone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Milestone Title</label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Frontend Architecture Signoff" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Due Date</label>
                        <input type="date" name="due_date" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Create Milestone</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
