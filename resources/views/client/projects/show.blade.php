@extends('layouts.public')

@section('title', 'Project ' . $project->name . ' | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('client.projects.index') }}" class="small text-muted text-decoration-none">&larr; Back to My Projects</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Project Header Card -->
            <div class="card card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-light text-primary border mb-1">{{ $project->category }}</span>
                        <h4 class="fw-bold mb-1">{{ $project->name }}</h4>
                        <div class="text-muted small">Project ID: {{ $project->project_number }}</div>
                    </div>
                    <span class="badge bg-primary px-3 py-2">{{ $project->status }}</span>
                </div>

                <p class="text-secondary small mt-3 mb-4">{{ $project->description }}</p>

                <div class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Milestone Progress</span>
                        <span class="fw-bold text-dark">{{ $project->progress_percentage }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $project->progress_percentage }}%;"></div>
                    </div>
                </div>
            </div>

            <!-- Milestones -->
            <div class="card card-custom p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-tasks text-primary me-2"></i> Deliverable Milestones</h5>
                @forelse($project->milestones as $m)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <div class="fw-semibold small">{{ $m->title }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Target: {{ $m->due_date ? $m->due_date->format('d M Y') : 'Scheduled' }}</div>
                        </div>
                        <span class="badge {{ $m->status === 'completed' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($m->status) }}</span>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">No milestones established.</div>
                @endforelse
            </div>

            <!-- Client Timeline Events -->
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold mb-3"><i class="fas fa-history text-info me-2"></i> Project Activity Timeline</h5>
                <div class="d-flex flex-column gap-3">
                    @forelse($project->timelineEvents as $ev)
                        <div class="p-3 bg-light rounded border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small">{{ $ev->title }}</span>
                                <span class="text-muted" style="font-size: 0.75rem;">{{ $ev->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="small text-secondary">{{ $ev->description }}</div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">No activity recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom p-3 bg-white mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Project Specs</h6>
                <div class="small">
                    <div class="mb-2"><strong>Target Delivery:</strong> {{ $project->expected_delivery_date ? $project->expected_delivery_date->format('d M Y') : 'Pending' }}</div>
                    <div class="mb-2"><strong>Priority:</strong> <span class="badge bg-secondary">{{ $project->priority }}</span></div>
                    <div><strong>Active Tickets:</strong> <span class="badge bg-warning text-dark">{{ $project->tickets->where('closed_at', null)->count() }}</span></div>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <a href="{{ route('client.tickets.create') }}?project_id={{ $project->id }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-plus me-1"></i> Raise Ticket on This Project
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
