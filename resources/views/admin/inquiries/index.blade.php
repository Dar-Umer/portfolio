@extends('layouts.admin')

@section('page_title', 'Contact Inquiries & Leads')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Website Inquiries & Prospect Leads</h4>
        <p class="text-muted small mb-0">Incoming discovery requests submitted via the public portfolio contact portal.</p>
    </div>
    <div>
        <span class="badge badge-soft-primary px-3 py-2 fs-6">
            <i class="fas fa-envelope-open-text me-1"></i> {{ $unreadCount }} Unread {{ Str::plural('Lead', $unreadCount) }}
        </span>
    </div>
</div>

<div class="card border-0 shadow-sm p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('admin.inquiries.index') }}" class="row g-2 align-items-center">
        <div class="col-lg-6 col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-2" placeholder="Search by prospect name, email, subject, or message..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-lg-4 col-md-4">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">All Inquiries</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread Leads Only</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read / Responded</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.inquiries.index') }}" class="btn btn-light border" title="Reset"><i class="fas fa-rotate-left"></i></a>
            @endif
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Prospect Info</th>
                    <th>Subject & Message Note</th>
                    <th>Received</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr class="{{ !$inquiry->is_read ? 'table-primary bg-opacity-10 fw-semibold' : '' }}">
                        <td style="width: 110px;">
                            @if (!$inquiry->is_read)
                                <span class="badge badge-soft-danger">
                                    <span class="status-dot" style="background-color: var(--app-danger);"></span> New Lead
                                </span>
                            @else
                                <span class="badge badge-soft-dark">Read</span>
                            @endif
                        </td>
                        <td style="min-width: 200px;">
                            <div class="fw-semibold text-dark">{{ $inquiry->name }}</div>
                            <div class="text-muted small">
                                <a href="mailto:{{ $inquiry->email }}" class="text-decoration-none text-muted">
                                    <i class="fas fa-envelope me-1 opacity-75"></i> {{ $inquiry->email }}
                                </a>
                            </div>
                            @if ($inquiry->phone)
                                <div class="text-muted small">
                                    <i class="fas fa-phone-alt me-1 opacity-75"></i> {{ $inquiry->phone }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $inquiry->subject ?: 'General Inbound Inquiry' }}</div>
                            <div class="text-secondary small text-break mt-1" style="line-height: 1.5;">{{ Str::limit($inquiry->message, 150) }}</div>
                        </td>
                        <td class="text-muted font-monospace small" style="width: 160px; font-size: 11px;">
                            <div>{{ $inquiry->created_at->format('M d, Y') }}</div>
                            <div class="text-muted" style="font-size: 10.5px;">{{ $inquiry->created_at->format('H:i') }} ({{ $inquiry->created_at->diffForHumans() }})</div>
                        </td>
                        <td class="text-end text-nowrap" style="width: 200px;">
                            <div class="d-inline-flex gap-1">
                                <form method="POST" action="{{ route('admin.inquiries.markRead', $inquiry) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-light border py-1 px-2" title="{{ $inquiry->is_read ? 'Mark as Unread' : 'Mark as Read' }}">
                                        <i class="fas {{ $inquiry->is_read ? 'fa-envelope text-muted' : 'fa-envelope-open text-primary' }}"></i>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.inquiries.convertToClient', $inquiry) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success py-1 px-2" title="Convert to Client Profile">
                                        <i class="fas fa-user-plus me-1"></i> Onboard
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Delete this inquiry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-light border py-1 px-2 text-danger" title="Delete Inquiry">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-25"></i>
                            <h6 class="fw-bold text-dark mb-1">Inbox Zero</h6>
                            <p class="small text-muted mb-0">No inquiries or leads found matching your criteria.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($inquiries->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $inquiries->firstItem() ?? 0 }} to {{ $inquiries->lastItem() ?? 0 }} of {{ $inquiries->total() }} leads</span>
            <div>{{ $inquiries->links() }}</div>
        </div>
    @endif
</div>
@endsection
