@extends('layouts.public')

@section('title', 'My Projects | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">My Development Projects</h4>
        <p class="text-muted small mb-0">Track real-time progress, scheduled deliverables, and release timelines.</p>
    </div>

    <div class="row g-4">
        @forelse($projects as $proj)
            <div class="col-md-6">
                <div class="card card-custom p-4 bg-white h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-primary border">{{ $proj->category }}</span>
                        <span class="badge bg-primary">{{ $proj->status }}</span>
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ route('client.projects.show', $proj) }}" class="text-decoration-none text-dark">{{ $proj->name }}</a>
                    </h5>
                    <p class="text-muted small mb-3">{{ Str::limit($proj->description, 120) }}</p>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Development Progress</span>
                            <span class="fw-bold text-dark">{{ $proj->progress_percentage }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ $proj->progress_percentage }}%;"></div>
                        </div>
                    </div>

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center small">
                        <span class="text-muted">Code: <strong>{{ $proj->project_number }}</strong></span>
                        <a href="{{ route('client.projects.show', $proj) }}" class="fw-semibold text-primary text-decoration-none">View Timeline &rarr;</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                No active projects found under your account.
            </div>
        @endforelse
    </div>

    @if($projects->hasPages())
        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
