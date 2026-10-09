@extends('layouts.admin')

@section('page_title', 'GitHub Integrations')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h6 class="fw-bold mb-3"><i class="fab fa-github text-dark me-2"></i> Connect Project Repository</h6>
            <p class="text-muted small">Connect client projects to monitor repository tags, latest releases, and sync version logs without exposing credentials.</p>

            <form method="POST" action="{{ route('admin.github.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Target Project</label>
                    <select name="project_id" class="form-select form-select-sm" required>
                        <option value="">Select Project...</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->project_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Repo Owner</label>
                        <input type="text" name="repo_owner" class="form-control form-control-sm" placeholder="e.g. laravel" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Repo Name</label>
                        <input type="text" name="repo_name" class="form-control form-control-sm" placeholder="e.g. framework" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Default Branch</label>
                    <input type="text" name="default_branch" class="form-control form-control-sm" value="main" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Personal Access Token (Optional for public repos)</label>
                    <input type="password" name="access_token" class="form-control form-control-sm" placeholder="ghp_xxxxxxxxxxxx">
                    <span class="text-muted" style="font-size: 0.75rem;">Stored securely. Never sent to client browsers.</span>
                </div>

                <button type="submit" class="btn btn-primary btn-sm w-100">Connect Repository</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-code-branch text-primary me-2"></i> Connected Repositories</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Repository</th>
                            <th>Latest Release</th>
                            <th>Sync Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repositories as $repo)
                            <tr>
                                <td>{{ $repo->project->name }}</td>
                                <td>
                                    <a href="{{ $repo->repo_url }}" target="_blank" class="fw-semibold text-dark text-decoration-none">
                                        <i class="fab fa-github me-1"></i> {{ $repo->repo_owner }}/{{ $repo->repo_name }}
                                    </a>
                                </td>
                                <td>
                                    @if($repo->latest_release_tag)
                                        <span class="badge bg-success">{{ $repo->latest_release_tag }}</span>
                                    @else
                                        <span class="text-muted">No tag</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $repo->last_sync_status === 'success' ? 'bg-success-subtle text-success' : 'bg-secondary' }}">
                                        {{ ucfirst($repo->last_sync_status) }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.github.sync', $repo) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border" title="Trigger Synchronize">
                                            <i class="fas fa-sync-alt"></i> Sync
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No repositories connected.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
