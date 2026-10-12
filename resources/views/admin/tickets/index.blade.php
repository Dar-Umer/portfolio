@extends('layouts.admin')

@section('page_title', 'Support Tickets Management')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Support Tickets Queue</h4>
        <p class="text-muted small mb-0">Manage customer technical issues, bug triage, SLA resolution clocks, and internal staff responses.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-2 align-items-center">
            <div class="col-lg-4 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-2" placeholder="Search by Ticket # or Subject..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->slug }}" {{ request('status') === $st->slug ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-3">
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low Priority</option>
                    <option value="Normal" {{ request('priority') === 'Normal' ? 'selected' : '' }}>Normal Priority</option>
                    <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High Priority</option>
                    <option value="Critical" {{ request('priority') === 'Critical' ? 'selected' : '' }}>Critical Escalations</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request('search') || request('status') || request('priority'))
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-light border" title="Reset Filters">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Client Organization</th>
                    <th>Subject & Issue</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Opened</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>
                            <span class="badge badge-soft-primary font-monospace px-2 py-1 fw-bold">
                                {{ $ticket->ticket_number }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">
                                {{ $ticket->client->company_name ?? $ticket->client->user->name }}
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="fw-semibold text-decoration-none text-dark d-block">
                                {{ Str::limit($ticket->subject, 48) }}
                            </a>
                        </td>
                        <td><span class="badge badge-soft-dark border">{{ $ticket->category->name ?? 'General' }}</span></td>
                        <td>
                            @php
                                $prioBadge = match($ticket->priority) {
                                    'Critical' => 'badge-soft-danger',
                                    'High' => 'badge-soft-warning',
                                    'Normal' => 'badge-soft-info',
                                    default => 'badge-soft-dark'
                                };
                            @endphp
                            <span class="badge {{ $prioBadge }}">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background-color: {{ $ticket->status->color }}22; color: {{ $ticket->status->color }}; border: 1px solid {{ $ticket->status->color }}55;">
                                <span class="status-dot" style="background-color: {{ $ticket->status->color }};"></span>
                                {{ $ticket->status->name }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 small text-dark">
                                <i class="fas fa-user-circle text-muted"></i>
                                <span>{{ $ticket->assignedTo->name ?? 'Unassigned' }}</span>
                            </div>
                        </td>
                        <td><span class="text-muted font-monospace small" style="font-size: 11px;">{{ $ticket->created_at->format('d M, H:i') }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary py-1 px-3">
                                Manage <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted mb-3">
                                <i class="fas fa-ticket fa-3x opacity-25"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Queue Empty</h6>
                            <p class="text-muted small mb-0">No active support tickets match your filter criteria.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} tickets</span>
            <div>{{ $tickets->links() }}</div>
        </div>
    @endif
</div>
@endsection
