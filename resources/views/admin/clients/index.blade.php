@extends('layouts.admin')

@section('page_title', 'Client Directory')

@section('content')
<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Client Directory</h4>
        <p class="text-muted small mb-0">Manage customer organizations, verified business contacts, and account statuses.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-user-plus"></i> Onboard New Client
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-2 align-items-center">
            <div class="col-lg-6 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-2" placeholder="Search by name, company, email, or client #..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Account Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Accounts</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-light border" title="Reset Filters">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Clients Table Card -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Client ID</th>
                    <th>Client / Organization</th>
                    <th>Contact Info</th>
                    <th>Category</th>
                    <th>Active Projects</th>
                    <th>Open Tickets</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                    <tr>
                        <td>
                            <span class="badge badge-soft-primary font-monospace px-2 py-1 fw-bold">
                                {{ $c->client_number }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold shadow-xs text-white" 
                                     style="width: 38px; height: 38px; font-size: 14px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                                    {{ strtoupper(substr($c->company_name ?? $c->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.clients.show', $c) }}" class="fw-semibold text-decoration-none text-dark d-block">
                                        {{ $c->company_name ?? $c->user->name }}
                                    </a>
                                    <div class="text-muted small" style="font-size: 11.5px;">
                                        <i class="fas fa-user-tie me-1 opacity-75"></i> {{ $c->user->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-medium text-dark">{{ $c->user->email }}</div>
                            <div class="text-muted small" style="font-size: 11px;">
                                <i class="fas fa-phone-alt me-1 opacity-75"></i> {{ $c->phone ?? '—' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-soft-dark border">
                                {{ $c->client_category }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.clients.show', $c) }}" class="text-decoration-none">
                                <span class="badge badge-soft-primary">
                                    <i class="fas fa-cube me-1"></i> {{ $c->projects->count() }} Projects
                                </span>
                            </a>
                        </td>
                        <td>
                            @php $openTktCount = $c->tickets->whereNull('closed_at')->count(); @endphp
                            @if($openTktCount > 0)
                                <span class="badge badge-soft-warning">
                                    <i class="fas fa-circle-exclamation me-1"></i> {{ $openTktCount }} Open
                                </span>
                            @else
                                <span class="badge badge-soft-success">
                                    <i class="fas fa-check-circle me-1"></i> All Clear
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($c->account_status === 'active')
                                <span class="badge badge-soft-success">
                                    <span class="status-dot" style="background-color: var(--app-success);"></span> Active
                                </span>
                            @elseif($c->account_status === 'suspended')
                                <span class="badge badge-soft-danger">
                                    <span class="status-dot" style="background-color: var(--app-danger);"></span> Suspended
                                </span>
                            @else
                                <span class="badge badge-soft-dark">
                                    <span class="status-dot" style="background-color: #64748b;"></span> {{ ucfirst($c->account_status) }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.clients.show', $c) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Profile">
                                    <i class="fas fa-eye text-muted"></i>
                                </a>
                                <a href="{{ route('admin.clients.edit', $c) }}" class="btn btn-sm btn-light border py-1 px-2" title="Edit Profile">
                                    <i class="fas fa-pen-to-square text-muted"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="mb-3 text-muted">
                                <i class="fas fa-users-slash fa-3x opacity-25"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">No Clients Found</h6>
                            <p class="text-muted small mb-3">No client accounts match your current search or filter query.</p>
                            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-user-plus me-1"></i> Onboard New Client
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing {{ $clients->firstItem() ?? 0 }} to {{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} accounts</span>
            <div>{{ $clients->links() }}</div>
        </div>
    @endif
</div>
@endsection
