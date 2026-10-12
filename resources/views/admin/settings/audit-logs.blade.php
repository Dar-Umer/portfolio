@extends('layouts.admin')

@section('page_title', 'Centralized Audit Logs & Activity History')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Centralized Audit Logs & Activity History</h4>
        <p class="text-muted small mb-0">Immutable compliance ledger recording system state mutations, ticket transitions, and financial events.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.settings.audit') }}" class="row g-2 align-items-center">
            <div class="col-lg-8 col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="action" class="form-control border-start-0 ps-2" placeholder="Filter by action name (e.g. ticket_status_transition, payment_recorded)..." value="{{ request('action') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-5 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter Logs</button>
                @if(request('action'))
                    <a href="{{ route('admin.settings.audit') }}" class="btn btn-light border" title="Reset"><i class="fas fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Actor / Identity</th>
                    <th>Audit Action</th>
                    <th>Event Description</th>
                    <th class="text-end">Origin IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>
                            <span class="text-muted small font-monospace" style="font-size: 11px;">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" 
                                     style="width: 26px; height: 26px; font-size: 10px; background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                                    {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">{{ $log->user->name ?? 'System Daemon' }}</div>
                                    @if($log->user)
                                        <div class="text-muted small" style="font-size: 10.5px;">{{ $log->user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-soft-primary font-monospace" style="font-size: 11px;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark small" style="line-height: 1.4;">{{ $log->description }}</span>
                        </td>
                        <td class="text-end">
                            <span class="badge badge-soft-dark font-monospace" style="font-size: 11px;">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fa-3x opacity-25 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark mb-1">No Audit Records</h6>
                            <p class="small text-muted mb-0">No recorded audit events match your criteria.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries</span>
            <div>{{ $logs->links() }}</div>
        </div>
    @endif
</div>
@endsection
