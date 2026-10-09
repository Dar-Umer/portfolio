@extends('layouts.public')

@section('title', 'My Support Tickets | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Support Tickets</h4>
            <p class="text-muted small mb-0">Track defect resolutions, technical requests, and development updates.</p>
        </div>
        <a href="{{ route('client.tickets.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Support Ticket
        </a>
    </div>

    <div class="card card-custom bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Ticket #</th>
                        <th>Subject</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td><span class="fw-bold">{{ $ticket->ticket_number }}</span></td>
                            <td>
                                <a href="{{ route('client.tickets.show', $ticket) }}" class="text-dark fw-semibold text-decoration-none">
                                    {{ Str::limit($ticket->subject, 40) }}
                                </a>
                            </td>
                            <td>{{ $ticket->project->name ?? 'General' }}</td>
                            <td><span class="badge bg-secondary">{{ $ticket->priority }}</span></td>
                            <td>
                                <span class="badge" style="background-color: {{ $ticket->status->color }}">
                                    {{ $ticket->status->name }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('client.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No tickets found.</td>
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
</div>
@endsection
