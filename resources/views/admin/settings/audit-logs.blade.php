@extends('layouts.admin')

@section('page_title', 'Centralized Audit Logs & Activity History')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.settings.audit') }}" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="action" class="form-control form-control-sm" placeholder="Filter by action name (e.g. ticket_status_transition, payment_recorded)..." value="{{ request('action') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                <a href="{{ route('admin.settings.audit') }}" class="btn btn-sm btn-light border"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Actor / User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th class="text-end">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td><span class="text-muted small font-monospace">{{ $log->created_at->format('d M Y, H:i:s') }}</span></td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $log->user->name ?? 'System Process' }}</div>
                            @if($log->user)
                                <div class="text-muted small" style="font-size: 11px;">{{ $log->user->email }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border font-monospace" style="font-size: 11px;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td><span class="text-dark">{{ $log->description }}</span></td>
                        <td class="text-end"><code class="text-muted small font-monospace">{{ $log->ip_address ?? '127.0.0.1' }}</code></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No audit log entries recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
