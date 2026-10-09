@extends('layouts.admin')

@section('page_title', 'Support Tickets Management')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by Ticket # or Subject..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->slug }}" {{ request('status') === $st->slug ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All Priorities</option>
                    <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                    <option value="Normal" {{ request('priority') === 'Normal' ? 'selected' : '' }}>Normal</option>
                    <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                    <option value="Critical" {{ request('priority') === 'Critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-light border text-muted" title="Reset"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Ticket #</th>
                    <th>Client</th>
                    <th>Subject</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Created</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td><span class="fw-bold font-monospace text-primary">{{ $ticket->ticket_number }}</span></td>
                        <td>
                            <div class="fw-medium text-dark">{{ $ticket->client->company_name ?? $ticket->client->user->name }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="fw-semibold text-decoration-none text-dark d-block">
                                {{ Str::limit($ticket->subject, 45) }}
                            </a>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $ticket->category->name ?? 'General' }}</span></td>
                        <td>
                            <span class="badge {{ $ticket->priority === 'Critical' ? 'bg-danger' : ($ticket->priority === 'High' ? 'bg-warning text-dark' : ($ticket->priority === 'Normal' ? 'bg-info text-dark' : 'bg-secondary')) }}">
                                {{ $ticket->priority }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background-color: {{ $ticket->status->color }}">
                                {{ $ticket->status->name }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle text-muted me-1"></i>
                                <span class="small">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</span>
                            </div>
                        </td>
                        <td><span class="text-muted small">{{ $ticket->created_at->format('d M, H:i') }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary py-1 px-3">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No tickets found matching criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
