@extends('layouts.admin')

@section('page_title', 'GitHub Integrations')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">GitHub Repository Integrations</h4>
        <p class="text-muted small mb-0">Connect client repositories to track releases, automated audit tags, and branch synchronization.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="rounded p-2 bg-light text-dark" style="font-size: 16px;">
                    <i class="fab fa-github"></i>
                </div>
                <h6 class="fw-bold mb-0 text-dark">Connect Project Repository</h6>
            </div>
            <p class="text-muted small mb-3">Link client deliverables to Git repositories to monitor releases and sync engineering commit tags.</p>

            <form method="POST" action="{{ route('admin.github.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Target Project <span class="text-danger">*</span></label>
                    <select name="project_id" class="form-select" required>
                        <option value="">Select Project...</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('project_id', request('project_id')) == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->project_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Owner / Org <span class="text-danger">*</span></label>
                        <input type="text" name="repo_owner" class="form-control" placeholder="e.g. laravel" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Repository Name <span class="text-danger">*</span></label>
                        <input type="text" name="repo_name" class="form-control" placeholder="e.g. framework" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Default Branch <span class="text-danger">*</span></label>
                    <input type="text" name="default_branch" class="form-control font-monospace" value="main" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Personal Access Token (PAT)</label>
                    <input type="password" name="access_token" class="form-control font-monospace" placeholder="ghp_xxxxxxxxxxxx">
                    <span class="text-muted mt-1 d-block" style="font-size: 11px;">Optional for public repos. Stored securely in encrypted vault.</span>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fab fa-github me-1"></i> Connect Repository
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-code-branch text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark">Connected Repositories ({{ $repositories->count() }})</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Repository</th>
                            <th>Latest Tag</th>
                            <th>Sync State</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repositories as $repo)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $repo->project->name }}</div>
                                    <div class="text-muted font-monospace" style="font-size: 11px;">{{ $repo->project->project_number }}</div>
                                </td>
                                <td>
                                    <a href="{{ $repo->repo_url }}" target="_blank" class="fw-semibold text-dark text-decoration-none small">
                                        <i class="fab fa-github me-1 text-muted"></i> {{ $repo->repo_owner }}/{{ $repo->repo_name }}
                                    </a>
                                    <div class="text-muted font-monospace" style="font-size: 11px;">branch: <code>{{ $repo->default_branch }}</code></div>
                                </td>
                                <td>
                                    @if($repo->latest_release_tag)
                                        <span class="badge badge-soft-success font-monospace">{{ $repo->latest_release_tag }}</span>
                                    @else
                                        <span class="text-muted small font-monospace">None</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $repo->last_sync_status === 'success' ? 'badge-soft-success' : 'badge-soft-dark' }}">
                                        {{ ucfirst($repo->last_sync_status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.github.sync', $repo) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border py-1 px-2" title="Trigger Synchronize">
                                            <i class="fas fa-rotate text-primary me-1"></i> Sync
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fab fa-github fa-3x opacity-25 mb-3 d-block"></i>
                                    <h6 class="fw-bold text-dark mb-1">No Repositories Linked</h6>
                                    <p class="small text-muted mb-0">Connect a GitHub repository to begin tracking releases.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
