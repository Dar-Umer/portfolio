@extends('layouts.public')

@section('title', $project->title . ' | The Umer Store')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="mb-4">
        <a href="{{ route('portfolio') }}" class="btn btn-sm btn-cyber-outline font-mono">
            &larr; Back to Portfolio
        </a>
    </div>

    <div class="row g-4 g-lg-5">
        <div class="col-lg-8">
            <span class="badge bg-dark text-info border border-info px-3 py-2 text-uppercase mb-2 font-mono">{{ $project->category->name ?? 'Enterprise Application' }}</span>
            <h1 class="display-6 fw-bold text-white mb-3">{{ $project->title }}</h1>
            <p class="lead text-light opacity-75 mb-4" style="line-height: 1.7;">{{ $project->short_description }}</p>

            <div class="card-cyber p-4 p-md-5 mb-4">
                <h5 class="fw-bold mb-3 text-white font-mono"><i class="fas fa-layer-group text-info me-2"></i> System Overview &amp; Architecture</h5>
                <div class="text-light opacity-80" style="white-space: pre-wrap; line-height: 1.8;">{{ $project->description }}</div>
            </div>

            @if($project->project_url || $project->github_url)
                <div class="d-flex flex-wrap gap-3">
                    @if($project->project_url)
                        <a href="{{ $project->project_url }}" target="_blank" rel="noopener" class="btn btn-cyber-primary px-4 py-2 font-mono">
                            <i class="fas fa-external-link-alt me-2"></i> Live Demonstration &nearr;
                        </a>
                    @endif
                    @if($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="btn btn-cyber-outline px-4 py-2 font-mono">
                            <i class="fab fa-github me-2"></i> View Repository
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card-cyber p-4">
                <h5 class="fw-bold border-bottom border-secondary border-opacity-25 pb-3 mb-3 text-white font-mono">
                    <i class="fas fa-sliders text-info me-2"></i> System Specs
                </h5>
                <div class="small">
                    <div class="mb-3">
                        <div class="text-muted font-mono" style="font-size: 11px;">CLIENT / STAKEHOLDER:</div>
                        <div class="fw-semibold text-white fs-6">{{ $project->client_name ?? 'Confidential' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted font-mono" style="font-size: 11px;">ARCHITECTURE TYPE:</div>
                        <div class="fw-semibold text-white">{{ $project->project_type }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted font-mono" style="font-size: 11px;">COMPLETION DATE:</div>
                        <div class="fw-semibold text-white">{{ $project->completion_date ? $project->completion_date->format('M Y') : 'Active Production' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted font-mono" style="font-size: 11px;">LIFECYCLE STATUS:</div>
                        <span class="badge bg-dark text-success border border-success font-mono"><i class="fas fa-check-circle me-1"></i> {{ $project->status }}</span>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted font-mono mb-2" style="font-size: 11px;">CORE TECHNOLOGIES:</div>
                        <div class="d-flex flex-wrap gap-1">
                            @if($project->technologies)
                                @foreach($project->technologies as $t)
                                    <span class="badge bg-dark text-light border border-secondary font-mono small" style="font-size: 11px;">{{ $t }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top border-secondary border-opacity-25">
                    <a href="{{ route('contact') }}" class="btn btn-sm btn-cyber-primary w-100 font-mono py-2">
                        Request Similar Build &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
