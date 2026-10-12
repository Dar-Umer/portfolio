@extends('layouts.admin')

@section('page_title', 'CMS: Public Portfolio Showcase')

@section('content')
@include('admin.cms._nav')

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h6 class="fw-bold mb-3"><i class="fas fa-plus-circle text-primary me-2"></i> Showcase New Project</h6>
            <form method="POST" action="{{ route('admin.cms.portfolio.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Project Title</label>
                    <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Enterprise Cloud ERP" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="portfolio_category_id" class="form-select form-select-sm">
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Project Type</label>
                        <input type="text" name="project_type" class="form-control form-control-sm" placeholder="Web / SaaS" value="Full Stack Web" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Client Name</label>
                        <input type="text" name="client_name" class="form-control form-control-sm" placeholder="e.g. Acme Corp">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Technologies (Comma separated)</label>
                    <input type="text" name="technologies" class="form-control form-control-sm" placeholder="Laravel, Vue.js, Tailwind, MySQL">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Live URL</label>
                        <input type="url" name="project_url" class="form-control form-control-sm" placeholder="https://...">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">GitHub Repo URL</label>
                        <input type="url" name="github_url" class="form-control form-control-sm" placeholder="https://github.com/...">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Short Summary</label>
                    <textarea name="short_description" class="form-control form-control-sm" rows="2" placeholder="Brief project summary..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Detailed Description</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Case study details, challenges solved..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Add to Portfolio</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Showcased Portfolio Items</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th>Title & Type</th>
                            <th>Category</th>
                            <th>Technologies</th>
                            <th>Links</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $item->title }}</div>
                                    <div class="text-muted small">{{ $item->project_type }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary">{{ $item->category?->name ?? 'General' }}</span>
                                </td>
                                <td>
                                    @if(is_array($item->technologies))
                                        @foreach(array_slice($item->technologies, 0, 3) as $tech)
                                            <span class="badge badge-soft-dark me-1 font-monospace">{{ $tech }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if($item->project_url)
                                        <a href="{{ $item->project_url }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2 me-1" title="Live Site"><i class="fas fa-external-link-alt"></i></a>
                                    @endif
                                    @if($item->github_url)
                                        <a href="{{ $item->github_url }}" target="_blank" class="btn btn-sm btn-light border py-1 px-2" title="GitHub Repo"><i class="fab fa-github"></i></a>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.cms.portfolio.destroy', $item) }}" onsubmit="return confirm('Remove portfolio item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No portfolio projects published.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($projects->hasPages())
                <div class="p-3 border-top">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
