@extends('layouts.public')

@section('title', $project->title . ' | The Umer Store')

@section('content')
<div class="container py-5">
    <div class="mb-3">
        <a href="{{ route('portfolio') }}" class="small text-muted text-decoration-none">&larr; Back to Portfolio</a>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <span class="badge bg-primary px-3 py-2 text-uppercase mb-2">{{ $project->category->name ?? 'Enterprise Application' }}</span>
            <h2 class="fw-bold mb-3">{{ $project->title }}</h2>
            <p class="lead text-muted">{{ $project->short_description }}</p>

            <hr class="my-4">

            <div class="card card-custom p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3">System Overview & Architecture</h5>
                <div class="text-secondary" style="white-space: pre-wrap; line-height: 1.7;">{{ $project->description }}</div>
            </div>

            @if($project->project_url || $project->github_url)
                <div class="d-flex gap-3">
                    @if($project->project_url)
                        <a href="{{ $project->project_url }}" target="_blank" class="btn btn-primary"><i class="fas fa-external-link-alt me-1"></i> Live Demonstration</a>
                    @endif
                    @if($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank" class="btn btn-outline-dark"><i class="fab fa-github me-1"></i> View Repository</a>
                    @endif
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold border-bottom pb-2 mb-3">Project Specs</h5>
                <div class="small">
                    <div class="mb-3">
                        <div class="text-muted">Client / Stakeholder:</div>
                        <div class="fw-semibold">{{ $project->client_name ?? 'Confidential' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Type:</div>
                        <div class="fw-semibold">{{ $project->project_type }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Completion Date:</div>
                        <div class="fw-semibold">{{ $project->completion_date ? $project->completion_date->format('M Y') : 'Ongoing' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Status:</div>
                        <span class="badge bg-success-subtle text-success">{{ $project->status }}</span>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted mb-1">Technologies Used:</div>
                        <div class="d-flex flex-wrap gap-1">
                            @if($project->technologies)
                                @foreach($project->technologies as $t)
                                    <span class="badge bg-light text-dark border">{{ $t }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
