@extends('layouts.admin')

@section('page_title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<!-- Header Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-light border">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="fw-bold mb-0 text-dark">{{ $ticket->subject }}</h4>
                <span class="badge badge-soft-primary font-monospace">{{ $ticket->ticket_number }}</span>
                <span class="badge" style="background-color: {{ $ticket->status->color }}22; color: {{ $ticket->status->color }}; border: 1px solid {{ $ticket->status->color }}55;">
                    <span class="status-dot" style="background-color: {{ $ticket->status->color }};"></span>
                    {{ $ticket->status->name }}
                </span>
                @php
                    $prioBadge = match($ticket->priority) {
                        'Critical' => 'badge-soft-danger',
                        'High' => 'badge-soft-warning',
                        'Normal' => 'badge-soft-info',
                        default => 'badge-soft-dark'
                    };
                @endphp
                <span class="badge {{ $prioBadge }}">{{ $ticket->priority }}</span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Raised by <a href="{{ route('admin.clients.show', $ticket->client) }}" class="fw-semibold text-decoration-none text-dark">{{ $ticket->client->company_name ?? $ticket->client->user->name }}</a>
                &bull; Opened {{ $ticket->created_at->format('d M Y, H:i') }} ({{ $ticket->created_at->diffForHumans() }})
            </p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Issue Details & Conversation Thread -->
    <div class="col-lg-8">
        <!-- Main Issue Description -->
        <div class="card border-0 shadow-sm mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-circle-info text-primary"></i>
                    <h6 class="fw-bold mb-0 text-dark">Issue Specification & Diagnostic Info</h6>
                </div>
                <span class="badge badge-soft-dark border">{{ $ticket->issue_type ?: 'Support Request' }}</span>
            </div>
            <div class="card-body p-4">
                <div class="p-3 bg-light rounded-3 mb-3 text-dark small" style="white-space: pre-wrap; line-height: 1.6; border: 1px solid var(--app-border-subtle);">{{ $ticket->description }}</div>

                @if($ticket->steps_to_reproduce)
                    <div class="mb-3">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 11px;">Steps to Reproduce:</div>
                        <div class="p-3 bg-light rounded-3 small font-monospace text-dark" style="border: 1px solid var(--app-border-subtle);">{{ $ticket->steps_to_reproduce }}</div>
                    </div>
                @endif

                @if($ticket->expected_result || $ticket->actual_result)
                    <div class="row g-3 mt-1">
                        @if($ticket->expected_result)
                            <div class="col-md-6">
                                <div class="text-success small fw-bold text-uppercase mb-1" style="font-size: 11px;">
                                    <i class="fas fa-check-circle me-1"></i> Expected Behavior
                                </div>
                                <div class="p-3 rounded-3 small text-dark" style="background: var(--app-success-subtle); border: 1px solid var(--app-success-border);">
                                    {{ $ticket->expected_result }}
                                </div>
                            </div>
                        @endif
                        @if($ticket->actual_result)
                            <div class="col-md-6">
                                <div class="text-danger small fw-bold text-uppercase mb-1" style="font-size: 11px;">
                                    <i class="fas fa-times-circle me-1"></i> Actual Error / Behavior
                                </div>
                                <div class="p-3 rounded-3 small text-dark" style="background: var(--app-danger-subtle); border: 1px solid var(--app-danger-border);">
                                    {{ $ticket->actual_result }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Conversation & Internal Notes Thread -->
        <div class="card border-0 shadow-sm mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-comments text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark">Discussion Feed & Staff Notes ({{ $ticket->comments->count() }})</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3 mb-4">
                    @forelse($ticket->comments as $comment)
                        @if($comment->is_internal_note)
                            <div class="p-3 rounded-3 border" style="background: #fffbeb; border-color: #fde68a !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold small text-dark">{{ $comment->user->name }}</span>
                                        <span class="badge badge-soft-warning"><i class="fas fa-lock me-1"></i> Staff Internal Note</span>
                                    </div>
                                    <span class="text-muted font-monospace small" style="font-size: 11px;">{{ $comment->created_at->format('d M, H:i') }}</span>
                                </div>
                                <div class="small text-dark" style="white-space: pre-wrap; line-height: 1.5;">{{ $comment->comment }}</div>
                            </div>
                        @else
                            <div class="p-3 rounded-3 border bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold small text-dark">{{ $comment->user->name }}</span>
                                        <span class="badge badge-soft-dark">Public Reply</span>
                                    </div>
                                    <span class="text-muted font-monospace small" style="font-size: 11px;">{{ $comment->created_at->format('d M, H:i') }}</span>
                                </div>
                                <div class="small text-secondary" style="white-space: pre-wrap; line-height: 1.5;">{{ $comment->comment }}</div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center text-muted small py-4">
                            <i class="fas fa-comment-dots fa-2x opacity-25 mb-2 d-block"></i>
                            No comments or staff notes on this ticket yet.
                        </div>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form method="POST" action="{{ route('admin.tickets.comment', $ticket) }}" class="border-top pt-4">
                    @csrf
                    <div class="mb-3">
                        <label for="comment" class="form-label small fw-semibold">Compose Response or Technical Note</label>
                        <textarea class="form-control" name="comment" rows="3" placeholder="Provide troubleshooting feedback or post an engineering update..." required></textarea>
                    </div>
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_internal_note" value="1" id="internalNoteCheck">
                            <label class="form-check-label small fw-semibold text-warning-emphasis" for="internalNoteCheck">
                                <i class="fas fa-lock me-1"></i> Private internal note (hidden from customer)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fas fa-paper-plane me-1"></i> Post Response
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Status Transition, Assignee, SLA & Meta -->
    <div class="col-lg-4">
        <!-- Status Management Card -->
        <div class="card border-0 shadow-sm mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-diagram-next text-info"></i>
                <h6 class="fw-bold mb-0 text-dark">Workflow Transition</h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Next Lifecycle State</label>
                        <select name="ticket_status_id" class="form-select" required>
                            @php
                                $allowed = $ticket->status?->allowed_transitions ?? [];
                                $allowedCount = 0;
                            @endphp
                            <optgroup label="Permitted Transitions">
                                @foreach($statuses as $st)
                                    @if(in_array($st->slug, $allowed))
                                        @php $allowedCount++; @endphp
                                        <option value="{{ $st->id }}">
                                            &rarr; {{ $st->name }}
                                        </option>
                                    @endif
                                @endforeach
                                @if($allowedCount === 0)
                                    <option disabled>Terminal State (No forward steps)</option>
                                @endif
                            </optgroup>
                            <optgroup label="Current Status">
                                <option value="{{ $ticket->ticket_status_id }}" selected>
                                    &bull; {{ $ticket->status->name }} (Current)
                                </option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Transition Reason / Comment</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Patch deployed to production">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Root Cause (if resolving)</label>
                        <input type="text" name="root_cause" class="form-control" placeholder="e.g. Database constraint discrepancy">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fixed Version & Release Details</label>
                        <input type="text" name="fixed_version" class="form-control mb-2" placeholder="e.g. v2.1.0">
                        <textarea name="fix_description" class="form-control" rows="2" placeholder="Summary of engineering change..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-check me-1"></i> Apply Transition
                    </button>
                </form>
            </div>
        </div>

        <!-- Assignment Card -->
        <div class="card border-0 shadow-sm mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-user-check text-warning"></i>
                <h6 class="fw-bold mb-0 text-dark">Staff Assignment</h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.tickets.assign', $ticket) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="assigned_to_user_id" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($developers as $dev)
                                <option value="{{ $dev->id }}" {{ $ticket->assigned_to_user_id === $dev->id ? 'selected' : '' }}>
                                    {{ $dev->name }} ({{ $dev->roles->pluck('name')->implode(', ') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-user-pen me-1"></i> Update Assignee
                    </button>
                </form>
            </div>
        </div>

        <!-- SLA Details -->
        <div class="card border-0 shadow-sm mb-4 bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-clock text-danger"></i>
                <h6 class="fw-bold mb-0 text-dark">SLA Clock & Target Windows</h6>
            </div>
            <div class="card-body p-4 small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">First Response Due:</span>
                    <span class="fw-semibold font-monospace">{{ $ticket->first_response_due_at ? $ticket->first_response_due_at->format('d M, H:i') : 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Resolution Target:</span>
                    <span class="fw-semibold font-monospace">{{ $ticket->resolution_due_at ? $ticket->resolution_due_at->format('d M, H:i') : 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted">SLA Compliance:</span>
                    @if($ticket->is_sla_breached)
                        <span class="badge badge-soft-danger"><i class="fas fa-triangle-exclamation me-1"></i> Breached</span>
                    @else
                        <span class="badge badge-soft-success"><i class="fas fa-shield-check me-1"></i> Within SLA</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
