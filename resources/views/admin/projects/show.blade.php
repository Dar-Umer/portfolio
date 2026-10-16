@extends('layouts.admin')

@section('page_title', 'Project: ' . $project->name)

@section('content')
<!-- Header Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-light border">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">{{ $project->name }}</h4>
                <span class="badge badge-soft-primary font-monospace">{{ $project->project_number }}</span>
                @php
                    $pBadge = match($project->status) {
                        'Completed' => 'badge-soft-success',
                        'In Development' => 'badge-soft-primary',
                        'Testing', 'Client Review' => 'badge-soft-warning',
                        default => 'badge-soft-dark'
                    };
                @endphp
                <span class="badge {{ $pBadge }}">{{ $project->status }}</span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Client: <a href="{{ route('admin.clients.show', $project->client) }}" class="fw-semibold text-decoration-none text-dark">{{ $project->client->company_name ?? $project->client->user->name }}</a>
                &bull; Category: <span class="fw-medium">{{ $project->category }}</span>
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @hasanyrole('Super Admin|Administrator')
            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-pen me-1"></i> Edit Project
            </a>
            <a href="{{ route('admin.quotations.create', ['client_id' => $project->client_profile_id, 'project_id' => $project->id]) }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-invoice me-1"></i> New Quotation
            </a>
            <a href="{{ route('admin.projects.summary.pdf', $project) }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-file-pdf me-1"></i> Summary PDF
            </a>
            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete project {{ $project->project_number }}? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
        @endhasanyrole
        <span class="badge badge-soft-primary px-3 py-2 fs-6 font-monospace">
            {{ $project->progress_percentage }}% Complete
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Specs, Milestones, and Real-time Timeline -->
    <div class="col-lg-8">
        <!-- Overview Card -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">{{ $project->name }}</h5>
                    <div class="text-muted small">Target Scope & Deliverables Overview</div>
                </div>
                <span class="badge badge-soft-{{ $project->priority === 'Critical' ? 'danger' : ($project->priority === 'High' ? 'warning' : 'dark') }}">
                    Priority: {{ $project->priority }}
                </span>
            </div>
            
            <p class="text-secondary small mb-3" style="line-height: 1.6;">
                {{ $project->description ?: 'No detailed scope description registered for this project.' }}
            </p>

            <div class="row g-3 pt-3 border-top">
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Start Date</div>
                    <div class="fw-semibold text-dark small mt-1">
                        <i class="fas fa-calendar-day text-primary me-1"></i> {{ $project->start_date ? $project->start_date->format('d M Y') : 'TBD' }}
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Target Delivery</div>
                    <div class="fw-semibold text-dark small mt-1">
                        <i class="fas fa-flag text-danger me-1"></i> {{ $project->expected_delivery_date ? $project->expected_delivery_date->format('d M Y') : 'TBD' }}
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Allocated Budget</div>
                    <div class="fw-bold text-success small mt-1">
                        ₹{{ number_format($project->budget, 2) }}
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase; font-weight: 700;">Technologies</div>
                    @php
                        $techString = is_array($project->technologies) 
                            ? implode(', ', $project->technologies) 
                            : ($project->technologies ?? '');
                    @endphp
                    <div class="small text-muted mt-1 text-truncate" title="{{ $techString }}">
                        {{ $techString ?: 'Not Specified' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Milestones Section -->
        <div class="card border-0 shadow-sm bg-white mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-flag-checkered text-warning"></i>
                    <h6 class="fw-bold mb-0 text-dark">Project Milestones ({{ $project->milestones->count() }})</h6>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                    <i class="fas fa-plus me-1"></i> Add Milestone
                </button>
            </div>
            <div class="card-body p-4">
                @forelse($project->milestones as $m)
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-3 border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold text-dark small">{{ $m->title }}</span>
                                @if($m->is_client_visible)
                                    <span class="badge badge-soft-info" style="font-size: 10px;">Client Visible</span>
                                @else
                                    <span class="badge badge-soft-dark" style="font-size: 10px;">Internal Only</span>
                                @endif
                            </div>
                            <div class="text-muted small mt-1" style="font-size: 11.5px;">
                                <i class="fas fa-clock me-1 opacity-75"></i> Due: {{ $m->due_date ? $m->due_date->format('d M Y') : 'Unscheduled' }}
                                @if($m->description)
                                    &bull; <span class="text-secondary">{{ Str::limit($m->description, 60) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form method="POST" action="{{ route('admin.projects.milestones.status', [$project, $m]) }}" class="d-inline-flex">
                                @csrf
                                <select name="status" class="form-select form-select-sm py-1 px-2" style="font-size: 11.5px; width: 120px;" onchange="this.form.submit()">
                                    <option value="pending" {{ $m->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $m->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $m->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $m->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                            <span class="badge {{ $m->status === 'completed' ? 'badge-soft-success' : ($m->status === 'in_progress' ? 'badge-soft-primary' : 'badge-soft-dark') }} font-monospace">
                                {{ $m->progress }}%
                            </span>
                            @hasanyrole('Super Admin|Administrator')
                                <button type="button" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#editMilestoneModal{{ $m->id }}" title="Edit Milestone">
                                    <i class="fas fa-pen text-primary"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.projects.milestones.destroy', [$project, $m]) }}" class="d-inline" onsubmit="return confirm('Remove milestone {{ $m->title }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger py-1 px-2" style="font-size: 11px;" title="Delete Milestone">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endhasanyrole
                        </div>
                    </div>

                    <!-- Modal: Edit Milestone -->
                    <div class="modal fade" id="editMilestoneModal{{ $m->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow-lg">
                                <form method="POST" action="{{ route('admin.projects.milestones.update', [$project, $m]) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fs-6 fw-bold text-dark">
                                            <i class="fas fa-pen text-primary me-1"></i> Edit Milestone: {{ $m->title }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 small">
                                        <div class="row g-2 mb-3">
                                            <div class="col-8">
                                                <label class="form-label fw-semibold">Milestone / Deliverable Title <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ $m->title }}" required>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label fw-semibold">Type</label>
                                                <select name="deliverable_type" class="form-select">
                                                    @foreach(['Feature', 'UI & Design', 'Integration', 'Security & QA', 'Documentation'] as $type)
                                                        <option value="{{ $type }}" {{ $m->deliverable_type === $type ? 'selected' : '' }}>{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Target Due Date</label>
                                                <input type="date" name="due_date" class="form-control" value="{{ $m->due_date?->format('Y-m-d') }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Staging / Demo URL</label>
                                                <input type="url" name="demo_url" class="form-control" value="{{ $m->demo_url }}" placeholder="https://staging.domain.com/demo">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Deliverable Scope & Overview</label>
                                            <textarea name="description" class="form-control" rows="2">{{ $m->description }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Acceptance Criteria / Sign-off Checklist</label>
                                            <textarea name="acceptance_criteria" class="form-control" rows="3">{{ $m->acceptance_criteria }}</textarea>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_client_visible" value="1" id="vis{{ $m->id }}" {{ $m->is_client_visible ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="vis{{ $m->id }}">Visible to Client on Portal</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-primary px-3">Update Milestone</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <div class="mb-2"><i class="fas fa-flag fa-2x opacity-25"></i></div>
                        <div class="small fw-semibold text-dark">No Milestones Defined</div>
                        <p class="small text-muted mb-3">Break down this project scope into delivery milestones.</p>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                            <i class="fas fa-plus me-1"></i> Add First Milestone
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Client Submitted Requirements & Deliverable Requests -->
        <div class="card border-0 shadow-sm bg-white mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-lightbulb text-warning"></i>
                    <h6 class="fw-bold mb-0 text-dark">Client Requirements & Feature Requests ({{ $project->requirements->count() }})</h6>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adminAddReqModal">
                    <i class="fas fa-plus me-1"></i> Add Requirement
                </button>
            </div>
            <div class="card-body p-4">
                @forelse($project->requirements as $req)
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-dark small">{{ $req->title }}</span>
                                    <span class="badge badge-soft-{{ $req->priority === 'Critical' ? 'danger' : ($req->priority === 'High' ? 'warning' : 'primary') }}">
                                        {{ $req->priority }} Priority
                                    </span>
                                </div>
                                <div class="text-muted small mt-1" style="font-size: 11px;">
                                    Submitted by {{ $req->user->name }} &bull; {{ $req->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $req->status === 'Planned' ? 'badge-soft-success' : 'badge-soft-dark' }}">
                                    {{ $req->status }}
                                </span>
                            </div>
                        </div>

                        <div class="small text-secondary mb-3" style="line-height: 1.5;">
                            {{ $req->details }}
                        </div>

                        @if($req->staff_feedback)
                            <div class="mb-2 p-2 rounded bg-white border text-primary small" style="font-size: 11.5px;">
                                <strong><i class="fas fa-comment-dots me-1"></i> Staff Response:</strong> {{ $req->staff_feedback }}
                            </div>
                        @endif

                        <div class="pt-2 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 11.5px;" data-bs-toggle="modal" data-bs-target="#editReqModal{{ $req->id }}">
                                    <i class="fas fa-pen text-primary me-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.projects.requirements.destroy', [$project, $req]) }}" class="d-inline" onsubmit="return confirm('Delete this requirement request?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger py-1 px-2" style="font-size: 11.5px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                            @if($req->status === 'Submitted')
                                <form method="POST" action="{{ route('admin.projects.requirements.convert', [$project, $req]) }}" class="d-inline-flex align-items-center gap-2">
                                    @csrf
                                    <input type="date" name="due_date" class="form-control form-control-sm" style="width: 135px;" title="Set target completion date">
                                    <button type="submit" class="btn btn-sm btn-primary py-1 px-2" style="font-size: 11.5px;">
                                        <i class="fas fa-check-double me-1"></i> Accept & Convert
                                    </button>
                                </form>
                            @else
                                <span class="small text-success font-monospace" style="font-size: 11px;">
                                    <i class="fas fa-link me-1"></i> Converted into sprint deliverable
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Modal: Edit Requirement -->
                    <div class="modal fade" id="editReqModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <form method="POST" action="{{ route('admin.projects.requirements.update', [$project, $req]) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom bg-light">
                                        <h5 class="modal-title fw-bold text-dark fs-6">
                                            <i class="fas fa-pen text-primary me-1"></i> Edit Client Requirement #{{ $req->id }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 small">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Requirement Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" value="{{ $req->title }}" required>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Priority</label>
                                                <select name="priority" class="form-select">
                                                    @foreach(['Low', 'Medium', 'High', 'Critical'] as $pr)
                                                        <option value="{{ $pr }}" {{ $req->priority === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Lifecycle Status</label>
                                                <select name="status" class="form-select">
                                                    @foreach(['Submitted', 'Under Review', 'Planned', 'Completed', 'Rejected'] as $st)
                                                        <option value="{{ $st }}" {{ $req->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Specification Details <span class="text-danger">*</span></label>
                                            <textarea name="details" class="form-control" rows="3" required>{{ $req->details }}</textarea>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-semibold">Staff Feedback Note (Visible to Client)</label>
                                            <textarea name="staff_feedback" class="form-control" rows="2" placeholder="e.g. Sprinted for next week release; API token received.">{{ $req->staff_feedback }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top bg-light py-2">
                                        <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-primary px-3">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-lightbulb fa-2x opacity-25 mb-2 d-block"></i>
                        No client requests submitted for this project.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Chronological Timeline Events -->
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-history text-info"></i>
                <h6 class="fw-bold mb-0 text-dark">Chronological Audit Timeline</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    @forelse($project->timelineEvents as $event)
                        <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-dark">{{ $event->title }}</span>
                                <span class="text-muted font-monospace" style="font-size: 11px;">
                                    {{ $event->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <div class="small text-secondary" style="line-height: 1.5;">{{ $event->description }}</div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-timeline fa-2x opacity-25 mb-2 d-block"></i>
                            No timeline log entries posted yet. Progress updates will append here.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status & Progress Controls, Repositories -->
    <div class="col-lg-4">
        <!-- Update Progress Card -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-sliders text-primary"></i> Stage & Progress Controller
            </h6>
            <form method="POST" action="{{ route('admin.projects.status', $project) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Lifecycle Stage</label>
                    <select name="status" class="form-select" required>
                        @foreach(['Planning', 'In Development', 'Testing', 'Client Review', 'Revision Required', 'Completed', 'On Hold'] as $st)
                            <option value="{{ $st }}" {{ $project->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-semibold mb-0">Progress Percentage</label>
                        <span class="badge badge-soft-primary font-monospace fw-bold" id="progVal">{{ $project->progress_percentage }}%</span>
                    </div>
                    <input type="range" class="form-range" name="progress_percentage" min="0" max="100" value="{{ $project->progress_percentage }}" oninput="document.getElementById('progVal').innerText = this.value + '%'">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Timeline Update Note</label>
                    <textarea name="timeline_comment" class="form-control" rows="2" placeholder="e.g. Deployed API endpoints to staging cluster..."></textarea>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="is_client_visible" value="1" checked id="visCheck">
                    <label class="form-check-label small fw-medium" for="visCheck">Expose event on customer portal</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-check-circle me-1"></i> Save Stage & Progress
                </button>
            </form>
        </div>

        <!-- Project Financial & Invoicing Health -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-receipt text-success"></i>
                    <h6 class="fw-bold mb-0 text-dark">Commercial & Billing Health</h6>
                </div>
                <a href="{{ route('admin.quotations.create', ['client_id' => $project->client_profile_id, 'project_id' => $project->id]) }}" class="small text-decoration-none fw-semibold">
                    <i class="fas fa-plus me-1"></i> New Quote
                </a>
            </div>

            @php
                $totalInvoiced = $project->invoices->sum('grand_total');
                $totalPaid = $project->invoices->sum('amount_paid');
                $balanceOutstanding = $project->invoices->sum('balance_due');
            @endphp

            <div class="p-3 bg-light rounded-3 border mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2 small">
                    <span class="text-muted">Total Invoiced:</span>
                    <span class="fw-bold font-monospace text-dark">₹{{ number_format($totalInvoiced, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 small">
                    <span class="text-muted">Paid to Date:</span>
                    <span class="fw-bold font-monospace text-success">₹{{ number_format($totalPaid, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top small">
                    <span class="fw-semibold text-dark">Outstanding Balance:</span>
                    <span class="fw-bold font-monospace {{ $balanceOutstanding > 0 ? 'text-danger' : 'text-success' }}">
                        ₹{{ number_format($balanceOutstanding, 2) }}
                    </span>
                </div>
            </div>

            @if($project->invoices->isNotEmpty())
                <div class="small fw-semibold text-uppercase text-muted mb-2" style="font-size: 10px; letter-spacing: 0.05em;">Linked Invoices ({{ $project->invoices->count() }})</div>
                <div class="d-flex flex-column gap-2 mb-3">
                    @foreach($project->invoices as $inv)
                        <div class="p-2 border rounded bg-white d-flex justify-content-between align-items-center small">
                            <div>
                                <a href="{{ route('admin.invoices.show', $inv) }}" class="fw-bold text-dark text-decoration-none font-monospace">
                                    {{ $inv->invoice_number }}
                                </a>
                                <div class="text-muted" style="font-size: 11px;">{{ $inv->currency }} {{ number_format($inv->grand_total, 2) }} &bull; {{ $inv->issue_date->format('d M') }}</div>
                            </div>
                            <span class="badge {{ $inv->status === 'Paid' ? 'badge-soft-success' : 'badge-soft-warning' }}" style="font-size: 10px;">
                                {{ $inv->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($project->quotations->isNotEmpty())
                <div class="small fw-semibold text-uppercase text-muted mb-2" style="font-size: 10px; letter-spacing: 0.05em;">Linked Proposals ({{ $project->quotations->count() }})</div>
                <div class="d-flex flex-column gap-2">
                    @foreach($project->quotations as $qtn)
                        <div class="p-2 border rounded bg-white d-flex justify-content-between align-items-center small">
                            <div>
                                <a href="{{ route('admin.quotations.show', $qtn) }}" class="fw-bold text-dark text-decoration-none font-monospace">
                                    {{ $qtn->quotation_number }}
                                </a>
                                <div class="text-muted" style="font-size: 11px;">{{ $qtn->currency }} {{ number_format($qtn->grand_total, 2) }}</div>
                            </div>
                            <span class="badge badge-soft-primary" style="font-size: 10px;">
                                {{ $qtn->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Project Team & Developer Assignments -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-users-gear text-primary"></i>
                    <h6 class="fw-bold mb-0 text-dark">Assigned Team Members</h6>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#assignMemberModal">
                    <i class="fas fa-user-plus me-1"></i> Assign
                </button>
            </div>

            @forelse($project->members as $member)
                <div class="p-2 border rounded bg-light mb-2 d-flex justify-content-between align-items-center small">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 11px;">
                            {{ strtoupper(substr($member->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $member->user->name }}</div>
                            <div class="text-muted" style="font-size: 10.5px;">{{ $member->role_in_project }}</div>
                        </div>
                    </div>
                    @hasanyrole('Super Admin|Administrator')
                        <form method="POST" action="{{ route('admin.projects.members.remove', [$project, $member]) }}" onsubmit="return confirm('Remove {{ $member->user->name }} from project team?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0" title="Remove member" style="font-size: 12px;">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </form>
                    @endhasanyrole
                </div>
            @empty
                <div class="text-center py-3 text-muted small">
                    <i class="fas fa-user-slash fa-2x opacity-25 mb-1 d-block"></i>
                    No staff or developers assigned yet.
                </div>
            @endforelse
        </div>

        <!-- Connected Git Repositories -->
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fab fa-github fa-lg text-dark"></i>
                    <h6 class="fw-bold mb-0 text-dark">Git Integration</h6>
                </div>
                <a href="{{ route('admin.github.index') }}" class="small text-decoration-none fw-semibold">Manage</a>
            </div>
            
            @forelse($project->repositories as $repo)
                <div class="p-3 border rounded-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <a href="{{ $repo->repo_url }}" target="_blank" class="fw-bold text-dark text-decoration-none small">
                                <i class="fab fa-github me-1"></i> {{ $repo->repo_owner }}/{{ $repo->repo_name }}
                                <i class="fas fa-arrow-up-right-from-square ms-1 text-muted" style="font-size: 10px;"></i>
                            </a>
                            <div class="text-muted font-monospace mt-1" style="font-size: 11px;">
                                <i class="fas fa-code-branch me-1 text-primary"></i> Branch: <code>{{ $repo->default_branch }}</code>
                            </div>
                        </div>
                        <span class="badge {{ $repo->last_sync_status === 'success' ? 'badge-soft-success' : 'badge-soft-warning' }}" style="font-size: 11px;">
                            {{ ucfirst($repo->last_sync_status ?? 'Connected') }}
                        </span>
                    </div>

                    <!-- Release Tag & Stats -->
                    <div class="p-2 bg-white rounded border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                            <span class="text-muted small" style="font-size: 11.5px;">Latest Release Tag:</span>
                            @if($repo->latest_release_tag)
                                <span class="badge badge-soft-success font-monospace fw-bold">{{ $repo->latest_release_tag }}</span>
                            @else
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 10.5px;">No GitHub Releases Yet</span>
                            @endif
                        </div>
                        
                        @php
                            $latestLog = $repo->syncLogs->first();
                            $payload = $latestLog->payload ?? [];
                        @endphp
                        @if($payload)
                            <div class="d-flex justify-content-around text-center pt-1" style="font-size: 11px;">
                                <div><i class="fas fa-star text-warning me-1"></i><strong>{{ $payload['stars'] ?? 0 }}</strong> <span class="text-muted">stars</span></div>
                                <div><i class="fas fa-code-fork text-secondary me-1"></i><strong>{{ $payload['forks'] ?? 0 }}</strong> <span class="text-muted">forks</span></div>
                                <div><i class="fas fa-circle-dot text-danger me-1"></i><strong>{{ $payload['open_issues'] ?? 0 }}</strong> <span class="text-muted">issues</span></div>
                            </div>
                        @endif

                        @if(!empty($payload['commits']))
                            <div class="mt-2 pt-2 border-top">
                                <div class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.05em;">
                                    <i class="fas fa-code-commit text-primary me-1"></i> Recent Commits ({{ count($payload['commits']) }})
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    @foreach(array_slice($payload['commits'], 0, 3) as $commit)
                                        <div class="p-1 rounded bg-light" style="font-size: 11px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <code class="text-primary fw-bold" style="font-size: 10px;">{{ $commit['sha'] }}</code>
                                                <span class="text-muted" style="font-size: 10px;">{{ $commit['date'] }}</span>
                                            </div>
                                            <div class="text-truncate text-dark fw-medium mt-1" title="{{ $commit['message'] }}">
                                                {{ $commit['message'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($repo->last_synced_at)
                            <div class="text-muted text-center pt-1 mt-1 border-top" style="font-size: 10.5px;">
                                <i class="fas fa-clock me-1 opacity-75"></i> Last synced: {{ $repo->last_synced_at->diffForHumans() }}
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.github.sync', $repo) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-white border w-100 shadow-sm text-dark fw-semibold" style="font-size: 12px;">
                            <i class="fas fa-rotate me-1 text-primary"></i> Sync Repository Now
                        </button>
                    </form>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    <i class="fab fa-github fa-2x opacity-25 mb-2 d-block"></i>
                    <div class="mb-2">No Git repository linked yet.</div>
                    <a href="{{ route('admin.github.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-dark w-100">
                        <i class="fab fa-github me-1"></i> Connect GitHub Repo
                    </a>
                </div>
            @endforelse

            @if($project->repositories->isNotEmpty())
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.github.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-light border w-100 text-muted" style="font-size: 12px;">
                        <i class="fas fa-plus me-1"></i> Connect Another Repo
                    </a>
                </div>
            @endif
        </div>

        <!-- Project Documents & Deliverable Assets -->
        <div class="card border-0 shadow-sm p-4 bg-white mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-folder-open text-warning"></i>
                    <h6 class="fw-bold mb-0 text-dark">Documents & Assets ({{ $project->documents->count() }})</h6>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                    <i class="fas fa-upload me-1"></i> Upload
                </button>
            </div>

            @forelse($project->documents as $doc)
                <div class="p-2 border rounded bg-light mb-2 d-flex justify-content-between align-items-center small">
                    <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 200px;">
                        <i class="fas fa-file-lines text-primary opacity-75"></i>
                        <div class="text-truncate">
                            <a href="{{ route('admin.projects.documents.download', [$project, $doc]) }}" class="fw-semibold text-dark text-decoration-none d-block text-truncate" title="{{ $doc->title }}">
                                {{ $doc->title }}
                            </a>
                            <div class="text-muted" style="font-size: 10px;">
                                {{ $doc->category }} &bull; {{ $doc->formatted_size }}
                                @if(!$doc->is_client_visible) &bull; <span class="text-danger">Internal</span> @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <a href="{{ route('admin.projects.documents.download', [$project, $doc]) }}" class="btn btn-sm btn-link text-primary p-0" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        @hasanyrole('Super Admin|Administrator')
                            <form method="POST" action="{{ route('admin.projects.documents.destroy', [$project, $doc]) }}" onsubmit="return confirm('Delete document {{ $doc->title }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endhasanyrole
                    </div>
                </div>
            @empty
                <div class="text-center py-3 text-muted small">
                    <i class="fas fa-folder fa-2x opacity-25 mb-1 d-block"></i>
                    No project documents or contracts uploaded yet.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal: Add Milestone -->
<div class="modal fade" id="addMilestoneModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.projects.milestone', $project) }}">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fs-6 fw-bold text-dark">
                        <i class="fas fa-flag-checkered text-warning me-1"></i> New Project Milestone
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 small">
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-semibold">Milestone / Deliverable Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Architecture Signoff & Staging Deployment" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="deliverable_type" class="form-select">
                                <option value="Feature">Feature</option>
                                <option value="UI & Design">UI & Design</option>
                                <option value="Integration">Integration</option>
                                <option value="Security & QA">Security & QA</option>
                                <option value="Documentation">Documentation</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Target Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Staging / Demo URL</label>
                            <input type="url" name="demo_url" class="form-control" placeholder="https://staging.domain.com/demo">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deliverable Scope & Overview</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Define deliverable overview..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Acceptance Criteria / Sign-off Checklist</label>
                        <textarea name="acceptance_criteria" class="form-control" rows="3" placeholder="- User can checkout via UPI&#10;- Webhook signature validated&#10;- Invoice PDF generated"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Create Deliverable Milestone</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Admin Add Requirement -->
<div class="modal fade" id="adminAddReqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.projects.requirements.store', $project) }}">
                @csrf
                <div class="modal-header border-bottom bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6">
                        <i class="fas fa-plus-circle text-primary me-1"></i> Add Client Requirement / Feature
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Requirement Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Implement Multi-Currency Razorpay Gateway" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="Medium">Medium Priority</option>
                                <option value="High">High Priority</option>
                                <option value="Critical">Critical Priority</option>
                                <option value="Low">Low Priority</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Initial Status</label>
                            <select name="status" class="form-select">
                                <option value="Submitted">Submitted</option>
                                <option value="Under Review">Under Review</option>
                                <option value="Planned">Planned</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Specification Details <span class="text-danger">*</span></label>
                        <textarea name="details" class="form-control" rows="3" placeholder="Outline specific client requirements, user stories, or acceptance rules..." required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Staff Feedback Note</label>
                        <textarea name="staff_feedback" class="form-control" rows="2" placeholder="Internal or client-facing response note..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Save Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Assign Member -->
<div class="modal fade" id="assignMemberModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.projects.members.add', $project) }}">
                @csrf
                <div class="modal-header border-bottom bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6">
                        <i class="fas fa-user-plus text-primary me-1"></i> Assign Team Member
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Staff / Developer <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Choose team member...</option>
                            @foreach($developers as $dev)
                                <option value="{{ $dev->id }}">{{ $dev->name }} ({{ $dev->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role in Project <span class="text-danger">*</span></label>
                        <input type="text" name="role_in_project" class="form-control" placeholder="e.g. Lead Full-Stack Engineer, QA Specialist, DevOps" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Assign Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Upload Document -->
<div class="modal fade" id="uploadDocModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.projects.documents.upload', $project) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6">
                        <i class="fas fa-file-arrow-up text-primary me-1"></i> Upload Project Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Document Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. System Architecture Diagram or Client Contract" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Document Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="Specification">Technical Specification</option>
                            <option value="Architecture & Design">Architecture & Design</option>
                            <option value="Contract & Agreement">Contract & Agreement</option>
                            <option value="Deliverable Asset">Deliverable Asset</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Choose File <span class="text-danger">*</span></label>
                        <input type="file" name="document" class="form-control" required>
                        <span class="text-muted" style="font-size: 11px;">Supported: PDF, ZIP, PNG, JPG, DOCX, XLSX (Max: 20MB)</span>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_client_visible" value="1" id="clientVisCheck" checked>
                        <label class="form-check-label fw-medium" for="clientVisCheck">Allow client to view & download on Client Portal</label>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
