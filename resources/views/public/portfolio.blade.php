@extends('layouts.public')

@section('title', 'Portfolio & Delivered Projects | The Umer Store')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="text-center mb-5 mx-auto" style="max-width: 650px;">
        <span class="cyber-badge mb-2">SYSTEMS ENGINEERING</span>
        <h2 class="display-6 fw-bold text-white mt-2">Delivered Systems &amp; Case Studies</h2>
        <p class="text-muted">A collection of custom applications, SaaS platforms, and enterprise solutions engineered to production scale.</p>
    </div>

    <div class="row g-4">
        @forelse($projects as $proj)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-cyber h-100 p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-dark text-info border border-info font-mono px-2 py-1">{{ $proj->category->name ?? 'Enterprise ERP' }}</span>
                        <span class="badge bg-dark text-success border border-success font-mono px-2 py-1"><i class="fas fa-check-circle me-1"></i> {{ $proj->status }}</span>
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ route('portfolio.detail', $proj->slug) }}" class="text-decoration-none text-white hover-cyan">{{ $proj->title }}</a>
                    </h5>
                    <p class="small text-muted mb-3 flex-grow-1" style="line-height: 1.6;">{{ $proj->short_description }}</p>
                    
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @if($proj->technologies)
                            @foreach($proj->technologies as $tech)
                                <span class="badge bg-dark text-light border border-secondary font-mono small" style="font-size: 11px;">{{ $tech }}</span>
                            @endforeach
                        @endif
                    </div>

                    <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center gap-2">
                        <span class="small text-muted font-mono text-truncate"><i class="fas fa-building me-1 text-info"></i> {{ $proj->client_name ?? 'Client Project' }}</span>
                        <div class="d-flex gap-2 flex-shrink-0">
                            @if($proj->project_url)
                                <a href="{{ $proj->project_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-cyber-outline py-1 px-2 font-mono" style="font-size: 11px;">
                                    Live &nearr;
                                </a>
                            @endif
                            <a href="{{ route('portfolio.detail', $proj->slug) }}" class="btn btn-sm btn-cyber-primary py-1 px-3 font-mono" style="font-size: 11px;">Details &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                No portfolio projects published currently.
            </div>
        @endforelse
    </div>

    @if($projects->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
