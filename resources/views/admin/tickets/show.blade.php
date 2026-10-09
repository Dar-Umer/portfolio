@extends('layouts.admin')

@section('page_title', 'Ticket Details: ' . $ticket->ticket_number)

@section('content')
<div class="row g-4">
    <!-- Left Column: Conversation & Details -->
    <div class="col-lg-8">
        <!-- Main Issue Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge" style="background-color: {{ $ticket->status->color }}">{{ $ticket->status->name }}</span>
                    <span class="badge {{ $ticket->priority === 'Critical' ? 'bg-danger' : 'bg-secondary' }} ms-1">{{ $ticket->priority }}</span>
                    <span class="badge bg-light text-dark border ms-1">{{ $ticket->issue_type }}</span>
                </div>
                <small class="text-muted">Raised: {{ $ticket->created_at->format('d M Y, H:i') }}</small>
            </div>
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-3">{{ $ticket->subject }}</h5>
                <div class="p-3 bg-light rounded mb-3" style="white-space: pre-wrap;">{{ $ticket->description }}</div>

                @if($ticket->steps_to_reproduce)
                    <h6 class="fw-bold mt-3 mb-1 small text-uppercase text-muted">Steps to Reproduce:</h6>
                    <div class="small p-2 bg-light rounded">{{ $ticket->steps_to_reproduce }}</div>
                @endif

                @if($ticket->expected_result || $ticket->actual_result)
                    <div class="row g-2 mt-2">
                        @if($ticket->expected_result)
                            <div class="col-md-6">
                                <span class="small fw-bold text-success">Expected:</span>
                                <div class="small p-2 bg-light rounded">{{ $ticket->expected_result }}</div>
                            </div>
                        @endif
                        @if($ticket->actual_result)
                            <div class="col-md-6">
                                <span class="small fw-bold text-danger">Actual:</span>
                                <div class="small p-2 bg-light rounded">{{ $ticket->actual_result }}</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Conversation & Internal Notes Thread -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-comments text-primary me-2"></i> Conversation & Internal Notes</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3 mb-4">
                    @forelse($ticket->comments as $comment)
                        <div class="p-3 rounded {{ $comment->is_internal_note ? 'bg-warning-subtle border border-warning' : 'bg-light border' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small">
                                    {{ $comment->user->name }}
                                    @if($comment->is_internal_note)
                                        <span class="badge bg-warning text-dark ms-2"><i class="fas fa-lock me-1"></i> Internal Staff Note</span>
                                    @else
                                        <span class="badge bg-secondary ms-1">Public Reply</span>
                                    @endif
                                </span>
                                <span class="text-muted small">{{ $comment->created_at->format('d M, H:i') }}</span>
                            </div>
                            <div class="small" style="white-space: pre-wrap;">{{ $comment->comment }}</div>
                        </div>
                    @empty
                        <div class="text-center text-muted small py-3">No replies yet.</div>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form method="POST" action="{{ route('admin.tickets.comment', $ticket) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="comment" class="form-label small fw-semibold">Add Reply / Note</label>
                        <textarea class="form-control" name="comment" rows="3" placeholder="Type response or technical note..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_internal_note" value="1" id="internalNoteCheck">
                            <label class="form-check-label small fw-semibold text-danger" for="internalNoteCheck">
                                <i class="fas fa-lock me-1"></i> Mark as Internal Note (Client will NEVER see this)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Post Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Status Transition, Assignee, SLA & Meta -->
    <div class="col-lg-4">
        <!-- Status Management Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-tasks text-info me-2"></i> Workflow & Status</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Update Status</label>
                        <select name="ticket_status_id" class="form-select form-select-sm" required>
                            @foreach($statuses as $st)
                                <option value="{{ $st->id }}" {{ $ticket->ticket_status_id === $st->id ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Reason / Comment</label>
                        <input type="text" name="reason" class="form-control form-control-sm" placeholder="e.g. Verified patch on staging">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Root Cause (if resolving)</label>
                        <input type="text" name="root_cause" class="form-control form-control-sm" placeholder="e.g. Logic fault in controller">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fix Description / Version</label>
                        <input type="text" name="fixed_version" class="form-control form-control-sm mb-2" placeholder="e.g. v1.4.2">
                        <textarea name="fix_description" class="form-control form-control-sm" rows="2" placeholder="Summary of changes..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-sm w-100">Apply Status Update</button>
                </form>
            </div>
        </div>

        <!-- Assignment Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-check text-warning me-2"></i> Assignee</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tickets.assign', $ticket) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="assigned_to_user_id" class="form-select form-select-sm">
                            <option value="">Unassigned</option>
                            @foreach($developers as $dev)
                                <option value="{{ $dev->id }}" {{ $ticket->assigned_to_user_id === $dev->id ? 'selected' : '' }}>
                                    {{ $dev->name }} ({{ $dev->roles->pluck('name')->implode(', ') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">Update Assignee</button>
                </form>
            </div>
        </div>

        <!-- SLA Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-clock text-danger me-2"></i> SLA & Targets</h6>
            </div>
            <div class="card-body small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">First Response Due:</span>
                    <span class="fw-semibold">{{ $ticket->first_response_due_at ? $ticket->first_response_due_at->format('d M, H:i') : 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Resolution Due:</span>
                    <span class="fw-semibold">{{ $ticket->resolution_due_at ? $ticket->resolution_due_at->format('d M, H:i') : 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">SLA Status:</span>
                    @if($ticket->is_sla_breached)
                        <span class="badge bg-danger">Breached</span>
                    @else
                        <span class="badge bg-success">On Track</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
