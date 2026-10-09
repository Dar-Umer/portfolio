@extends('layouts.public')

@section('title', 'Portfolio & Delivered Projects | The Umer Store')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge bg-primary px-3 py-2 text-uppercase mb-2">Systems Engineering</span>
        <h2 class="fw-bold">Delivered Systems & Case Studies</h2>
        <p class="text-muted">A collection of custom applications, SaaS platforms, and enterprise solutions.</p>
    </div>

    <div class="row g-4">
        @forelse($projects as $proj)
            <div class="col-md-6 col-lg-4">
                <div class="card card-custom h-100 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-primary border">{{ $proj->category->name ?? 'Enterprise' }}</span>
                        <span class="badge bg-success-subtle text-success">{{ $proj->status }}</span>
                    </div>
                    <h5 class="fw-bold mb-2">
                        <a href="{{ route('portfolio.detail', $proj->slug) }}" class="text-decoration-none text-dark">{{ $proj->title }}</a>
                    </h5>
                    <p class="small text-muted mb-3">{{ $proj->short_description }}</p>
                    
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @if($proj->technologies)
                            @foreach($proj->technologies as $tech)
                                <span class="badge bg-secondary-subtle text-secondary small">{{ $tech }}</span>
                            @endforeach
                        @endif
                    </div>

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="small text-muted">{{ $proj->client_name ?? 'Client Project' }}</span>
                        <a href="{{ route('portfolio.detail', $proj->slug) }}" class="small fw-semibold text-primary text-decoration-none">Details &rarr;</a>
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
        <div class="mt-4 d-flex justify-content-center">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
