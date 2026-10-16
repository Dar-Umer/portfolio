@extends('layouts.public')

@section('title', 'Project ' . $project->name . ' | The Umer Store')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('client.projects.index') }}" class="small text-muted text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to My Projects
        </a>
        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#requestDeliverableModal">
            <i class="fas fa-plus-circle me-1"></i> Request New Deliverable / Feature
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show small" role="alert">
            <i class="fas fa-info-circle me-1"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Column -->
        <div class="col-lg-8">
            <!-- Project Overview Card -->
            <div class="card card-custom p-4 bg-white mb-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        <span class="badge bg-light text-primary border mb-1">{{ $project->category }}</span>
                        <h4 class="fw-bold mb-1 text-dark">{{ $project->name }}</h4>
                        <div class="text-muted font-monospace small">ID: {{ $project->project_number }}</div>
                    </div>
                    @php
                        $pBadge = match($project->status) {
                            'Completed' => 'bg-success',
                            'In Development' => 'bg-primary',
                            'Testing', 'Client Review' => 'bg-warning text-dark',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $pBadge }} px-3 py-2 fs-6">{{ $project->status }}</span>
                </div>

                <p class="text-secondary small mt-2 mb-3" style="line-height: 1.6;">{{ $project->description ?: 'No detailed scope summary registered.' }}</p>

                <!-- Visual Progress Bar with Stage Highlights -->
                <div class="p-3 bg-light rounded border mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span class="fw-semibold text-dark"><i class="fas fa-chart-line text-primary me-1"></i> Milestone Delivery Progress</span>
                        <span class="fw-bold text-primary font-monospace">{{ $project->progress_percentage }}% Complete</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: {{ $project->progress_percentage }}%;"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted mt-2 pt-1 border-top" style="font-size: 11px;">
                        <span><i class="fas fa-flag text-secondary me-1"></i><strong>{{ $project->milestones->count() }}</strong> Total Deliverables</span>
                        <span><i class="fas fa-check text-success me-1"></i><strong>{{ $project->milestones->where('status', 'completed')->count() }}</strong> Completed & Approved</span>
                        <span><i class="fas fa-hourglass-half text-warning me-1"></i><strong>{{ $project->milestones->where('status', '!=', 'completed')->count() }}</strong> Remaining</span>
                    </div>
                </div>
            </div>

            <!-- Deliverables & Milestones (With Interactive Detail Overlay) -->
            <div class="card card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i> Deliverable Milestones</h5>
                        <p class="text-muted small mb-0">Click any deliverable to open its detailed scope, test demos, and sign-off on completion.</p>
                    </div>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($project->milestones as $m)
                        <div class="list-group-item px-0 py-3 border-bottom deliverable-row">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <button type="button" class="btn btn-link p-0 fw-bold text-dark text-decoration-none text-start" data-bs-toggle="modal" data-bs-target="#milestoneModal{{ $m->id }}">
                                            {{ $m->title }} <i class="fas fa-up-right-from-square ms-1 text-primary" style="font-size: 11px;"></i>
                                        </button>
                                        <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;">{{ $m->deliverable_type ?? 'Feature' }}</span>
                                    </div>
                                    <div class="text-muted small mt-1" style="font-size: 11.5px;">
                                        <i class="fas fa-calendar-alt me-1 opacity-75"></i> Target: {{ $m->due_date ? $m->due_date->format('d M Y') : 'Scheduled' }}
                                        @if($m->completion_date)
                                            &bull; <i class="fas fa-check-circle text-success me-1"></i> Completed: {{ $m->completion_date->format('d M Y') }}
                                        @endif
                                        @if($m->client_approved_at)
                                            &bull; <span class="badge badge-soft-success text-success bg-success-subtle font-monospace" style="font-size: 10px;"><i class="fas fa-signature me-1"></i>Client Approved</span>
                                        @endif
                                    </div>
                                    @if($m->description)
                                        <div class="text-secondary small mt-1 text-truncate" style="max-width: 450px;">
                                            {{ Str::limit($m->description, 100) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                                    @php
                                        $mBadge = match($m->status) {
                                            'completed' => 'bg-success',
                                            'in_progress' => 'bg-primary',
                                            'revision_requested' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $mBadge }} py-2 px-2" style="font-size: 11px;">
                                        {{ ucfirst(str_replace('_', ' ', $m->status)) }} ({{ $m->progress }}%)
                                    </span>
                                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#milestoneModal{{ $m->id }}">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Detailed Overlay Modal for Milestone -->
                        <div class="modal fade" id="milestoneModal{{ $m->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-bottom bg-light">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-2 rounded bg-primary-subtle text-primary">
                                                <i class="fas fa-cube"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark mb-0">{{ $m->title }}</h5>
                                                <div class="text-muted small">Deliverable Scope & Sign-Off Verification</div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3 mb-4">
                                            <div class="col-sm-4">
                                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 10px;">Classification</div>
                                                <span class="badge bg-light text-dark border mt-1">{{ $m->deliverable_type ?? 'Feature Deliverable' }}</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 10px;">Execution Progress</div>
                                                <div class="fw-bold text-primary mt-1 font-monospace">{{ $m->progress }}% &bull; {{ ucfirst(str_replace('_', ' ', $m->status)) }}</div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 10px;">Target Delivery</div>
                                                <div class="fw-medium text-dark mt-1">{{ $m->due_date ? $m->due_date->format('d M Y') : 'Unscheduled' }}</div>
                                            </div>
                                        </div>

                                        <!-- Scope Description -->
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-dark mb-1"><i class="fas fa-file-alt text-muted me-1"></i> Detailed Scope & Description</h6>
                                            <div class="p-3 bg-light rounded border small text-secondary" style="line-height: 1.6;">
                                                {{ $m->description ?: 'No extended description recorded for this milestone.' }}
                                            </div>
                                        </div>

                                        <!-- Acceptance Criteria -->
                                        @if($m->acceptance_criteria)
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-dark mb-1"><i class="fas fa-clipboard-check text-success me-1"></i> Acceptance Criteria & Quality Checklist</h6>
                                                <div class="p-3 bg-light rounded border small text-secondary" style="line-height: 1.6; white-space: pre-line;">
                                                    {{ $m->acceptance_criteria }}
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Staging Demo URL -->
                                        @if($m->demo_url)
                                            <div class="p-3 bg-primary-subtle rounded border border-primary-subtle mb-4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold text-primary small"><i class="fas fa-external-link-alt me-1"></i> Interactive Staging / Test Build</div>
                                                        <div class="text-muted small">Verify live functionality directly on staging environment.</div>
                                                    </div>
                                                    <a href="{{ $m->demo_url }}" target="_blank" class="btn btn-sm btn-primary px-3">
                                                        Launch Test URL <i class="fas fa-arrow-right ms-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Client Approval Status / Actions -->
                                        <div class="p-3 rounded border @if($m->client_approved_at) bg-success-subtle border-success @else bg-light @endif">
                                            @if($m->client_approved_at)
                                                <div class="d-flex align-items-center gap-2 text-success">
                                                    <i class="fas fa-circle-check fa-lg"></i>
                                                    <div>
                                                        <div class="fw-bold">Deliverable Formally Approved & Signed Off</div>
                                                        <div class="small opacity-75">Approved on {{ $m->client_approved_at->format('d M Y, H:i') }}. Feedback: {{ $m->client_review_note ?: 'No extra comments' }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-stamp text-primary me-1"></i> Client Verification & Sign-Off</h6>
                                                <p class="small text-muted mb-3">Has this deliverable met your requirements? You can formally mark it as completed or request adjustments.</p>
                                                
                                                <div class="d-flex flex-wrap gap-2">
                                                    <!-- Approve Button -->
                                                    <form method="POST" action="{{ route('client.projects.milestones.approve', [$project, $m]) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm px-3 fw-semibold">
                                                            <i class="fas fa-check me-1"></i> Approve & Mark Completed
                                                        </button>
                                                    </form>

                                                    <!-- Request Revision Button (Toggles collapse) -->
                                                    <button type="button" class="btn btn-outline-danger btn-sm px-3" data-bs-toggle="collapse" data-bs-target="#revisionBox{{ $m->id }}">
                                                        <i class="fas fa-undo me-1"></i> Request Revision / Changes
                                                    </button>
                                                </div>

                                                <!-- Revision Form Collapse -->
                                                <div class="collapse mt-3" id="revisionBox{{ $m->id }}">
                                                    <form method="POST" action="{{ route('client.projects.milestones.revision', [$project, $m]) }}" class="p-3 bg-white rounded border">
                                                        @csrf
                                                        <label class="form-label small fw-semibold text-danger">What requires adjustments?</label>
                                                        <textarea name="revision_reason" class="form-control form-control-sm mb-2" rows="2" placeholder="Describe what isn't working as expected or what needs alteration..." required></textarea>
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            Submit Revision Feedback
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light py-2">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">No deliverable milestones established yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- Client Requested Features / Requirements Backlog -->
            <div class="card card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-lightbulb text-warning me-2"></i> Client Requirements & Feature Requests ({{ $project->requirements->count() }})</h5>
                        <p class="text-muted small mb-0">Custom deliverables and modifications requested by you for this project.</p>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestDeliverableModal">
                        <i class="fas fa-plus me-1"></i> Add Requirement
                    </button>
                </div>

                @forelse($project->requirements as $req)
                    <div class="p-3 rounded border bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold text-dark small">{{ $req->title }}</span>
                            @php
                                $rBadge = match($req->status) {
                                    'Planned' => 'bg-success',
                                    'Under Review' => 'bg-warning text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $rBadge }}" style="font-size: 11px;">{{ $req->status }}</span>
                        </div>
                        <p class="text-secondary small mb-2" style="line-height: 1.5;">{{ $req->details }}</p>
                        <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 11px;">
                            <span>Priority: <strong class="text-dark">{{ $req->priority }}</strong> &bull; Submitted {{ $req->created_at->diffForHumans() }}</span>
                            @if($req->staff_feedback)
                                <span class="text-primary"><i class="fas fa-reply me-1"></i> {{ $req->staff_feedback }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small border rounded bg-light">
                        <i class="fas fa-clipboard-list fa-2x opacity-25 mb-2 d-block"></i>
                        No custom requirements logged yet. Need something added to this project? Click <strong>Request New Deliverable</strong> above.
                    </div>
                @endforelse
            </div>

            <!-- Project Activity & Sign-off Timeline -->
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-history text-info me-2"></i> Project Activity Timeline</h5>
                <div class="d-flex flex-column gap-3">
                    @forelse($project->timelineEvents as $ev)
                        <div class="p-3 bg-light rounded border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-dark">{{ $ev->title }}</span>
                                <span class="text-muted" style="font-size: 0.75rem;">{{ $ev->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="small text-secondary">{{ $ev->description }}</div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">No activity recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="card card-custom p-3 bg-white mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Project Specifications</h6>
                <div class="small">
                    <div class="mb-2"><strong>Target Delivery:</strong> {{ $project->expected_delivery_date ? $project->expected_delivery_date->format('d M Y') : 'Pending' }}</div>
                    <div class="mb-2"><strong>Priority:</strong> <span class="badge bg-secondary">{{ $project->priority }}</span></div>
                    <div class="mb-2">
                        <strong>Technologies:</strong>
                        <div class="text-muted mt-1">
                            @if(is_array($project->technologies))
                                @foreach($project->technologies as $t)
                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 10px;">{{ $t }}</span>
                                @endforeach
                            @else
                                {{ $project->technologies ?: 'Standard Stack' }}
                            @endif
                        </div>
                    </div>
                    <div><strong>Active Support Tickets:</strong> <span class="badge bg-warning text-dark">{{ $project->tickets->where('closed_at', null)->count() }}</span></div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex flex-column gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#requestDeliverableModal">
                        <i class="fas fa-plus me-1"></i> Request Deliverable / Scope
                    </button>
                    <a href="{{ route('client.tickets.create') }}?project_id={{ $project->id }}" class="btn btn-light border btn-sm w-100">
                        <i class="fas fa-ticket-alt me-1 text-warning"></i> Open Technical Ticket
                    </a>
                </div>
            </div>

            <!-- Assigned Engineering Team (Client Facing) -->
            @if($project->members->isNotEmpty())
                <div class="card card-custom p-3 bg-white mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-users text-primary me-1"></i> Dedicated Project Team
                    </h6>
                    <div class="d-flex flex-column gap-2">
                        @foreach($project->members as $member)
                            <div class="p-2 border rounded bg-light d-flex align-items-center gap-2 small">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 11px;">
                                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $member->user->name }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $member->role_in_project }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Invoices for this Project -->
            @if($project->invoices->isNotEmpty())
                <div class="card card-custom p-3 bg-white mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-file-invoice text-success me-1"></i> Project Invoices
                    </h6>
                    <div class="d-flex flex-column gap-2">
                        @foreach($project->invoices as $inv)
                            <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center small">
                                <div>
                                    <a href="{{ route('client.invoices.show', $inv) }}" class="fw-bold text-dark text-decoration-none font-monospace">
                                        {{ $inv->invoice_number }}
                                    </a>
                                    <div class="text-muted" style="font-size: 11px;">{{ $inv->currency }} {{ number_format($inv->grand_total, 2) }}</div>
                                </div>
                                <span class="badge {{ $inv->status === 'Paid' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size: 10px;">
                                    {{ $inv->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Project Documents & Contracts (Client Facing) -->
            @if($project->documents->isNotEmpty())
                <div class="card card-custom p-3 bg-white mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-folder-open text-warning me-1"></i> Documents & Deliverables
                    </h6>
                    <div class="d-flex flex-column gap-2">
                        @foreach($project->documents as $doc)
                            <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center small">
                                <div class="text-truncate me-2" style="max-width: 190px;">
                                    <div class="fw-bold text-dark text-truncate">{{ $doc->title }}</div>
                                    <div class="text-muted" style="font-size: 10.5px;">{{ $doc->category }} &bull; {{ $doc->formatted_size }}</div>
                                </div>
                                <a href="{{ route('client.projects.documents.download', [$project, $doc]) }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 11px;">
                                    <i class="fas fa-download me-1"></i> Get
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Client Deliverable / Requirement Request -->
<div class="modal fade" id="requestDeliverableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('client.projects.requirements.store', $project) }}">
                @csrf
                <div class="modal-header border-bottom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded bg-primary-subtle text-primary">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Request Project Deliverable</h5>
                            <div class="text-muted small">Specify new features, alterations or deliverables you need.</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Requirement Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Add Razorpay UPI Payment Flow" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Priority / Urgency <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            <option value="Medium">Medium — Standard Sprinted Feature</option>
                            <option value="High">High — Needed for Next Release</option>
                            <option value="Critical">Critical — High Business Impact</option>
                            <option value="Low">Low — Optional / Future Phase</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Detailed Description & Needs <span class="text-danger">*</span></label>
                        <textarea name="details" class="form-control" rows="4" placeholder="Detail exactly what is needed, any third-party APIs, user journey, or desired behaviors..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">
                        <i class="fas fa-paper-plane me-1"></i> Submit Requirement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
