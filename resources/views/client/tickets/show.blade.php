@extends('layouts.public')

@section('title', 'Ticket ' . $ticket->ticket_number . ' | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('client.tickets.index') }}" class="small text-muted text-decoration-none">&larr; Back to My Tickets</a>
    </div>

    <div class="row g-4">
        <!-- Main Conversation Thread -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background-color: {{ $ticket->status->color }}">{{ $ticket->status->name }}</span>
                        <span class="badge bg-secondary ms-1">{{ $ticket->priority }} Priority</span>
                        <h4 class="fw-bold text-dark mt-2 mb-1">{{ $ticket->subject }}</h4>
                        <div class="text-muted small">Ticket #{{ $ticket->ticket_number }} &bull; Opened {{ $ticket->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded mb-3" style="white-space: pre-wrap;">{{ $ticket->description }}</div>

                @if($ticket->steps_to_reproduce)
                    <div class="mb-2">
                        <strong class="small text-muted text-uppercase">Steps to Reproduce:</strong>
                        <div class="small p-2 bg-light rounded mt-1">{{ $ticket->steps_to_reproduce }}</div>
                    </div>
                @endif

                @if($ticket->resolved_at)
                    <div class="p-3 bg-success-subtle border border-success rounded mt-3">
                        <h6 class="fw-bold text-success mb-1"><i class="fas fa-check-circle me-1"></i> Resolution Summary</h6>
                        <div class="small mb-1"><strong>Fix Details:</strong> {{ $ticket->fix_description ?? 'Resolution verified.' }}</div>
                        @if($ticket->fixed_version)
                            <div class="small"><strong>Applicable Version:</strong> <span class="badge bg-success">{{ $ticket->fixed_version }}</span></div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Conversation Replies -->
            <div class="card card-custom p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-comments text-primary me-2"></i> Discussion Thread</h5>
                <div class="d-flex flex-column gap-3 mb-4">
                    @forelse($comments as $comment)
                        <div class="p-3 rounded {{ $comment->user_id === auth()->id() ? 'bg-primary-subtle ms-4' : 'bg-light me-4 border' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small">
                                    {{ $comment->user->name }}
                                    @if($comment->user->hasRole('Super Admin') || $comment->user->hasRole('Administrator'))
                                        <span class="badge bg-primary text-white ms-1">Support Engineer</span>
                                    @endif
                                </span>
                                <span class="text-muted small">{{ $comment->created_at->format('d M, H:i') }}</span>
                            </div>
                            <div class="small" style="white-space: pre-wrap;">{{ $comment->comment }}</div>
                        </div>
                    @empty
                        <div class="text-center text-muted small py-3">No messages yet.</div>
                    @endforelse
                </div>

                <!-- Reply Form -->
                <form method="POST" action="{{ route('client.tickets.reply', $ticket) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Add a Reply</label>
                        <textarea class="form-control" name="comment" rows="3" placeholder="Provide additional information or verify fix..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Post Reply <i class="fas fa-paper-plane ms-1"></i></button>
                </form>
            </div>
        </div>

        <!-- Sidebar Meta -->
        <div class="col-lg-4">
            <div class="card card-custom p-3 bg-white mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Ticket Information</h6>
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Issue Type:</span>
                        <span class="fw-semibold">{{ $ticket->issue_type }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Project:</span>
                        <span class="fw-semibold">{{ $ticket->project->name ?? 'General' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Category:</span>
                        <span class="fw-semibold">{{ $ticket->category->name ?? 'Standard' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span class="badge" style="background-color: {{ $ticket->status->color }}">{{ $ticket->status->name }}</span>
                    </div>
                </div>
            </div>

            <div class="card card-custom p-3 bg-white">
                <h6 class="fw-bold border-bottom pb-2 mb-2">Need Immediate Attention?</h6>
                <p class="small text-muted mb-0">Critical production defects are automatically routed to on-call engineering under active SLA targets.</p>
            </div>
        </div>
    </div>
</div>
@endsection
