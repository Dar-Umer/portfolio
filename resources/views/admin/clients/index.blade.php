@extends('layouts.admin')

@section('page_title', 'Client Directory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted small mb-0">Manage customer accounts, business metadata, and financial snapshots.</p>
    </div>
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Add New Client
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, company, email, or client #..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Account Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-light border text-muted" title="Reset"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Client #</th>
                    <th>Name / Company</th>
                    <th>Email / Phone</th>
                    <th>Category</th>
                    <th>Projects</th>
                    <th>Open Tickets</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                    <tr>
                        <td><span class="fw-bold text-primary font-monospace">{{ $c->client_number }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                    {{ substr($c->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $c->user->name }}</div>
                                    <div class="text-muted small" style="font-size: 11.5px;">{{ $c->company_name ?? 'Individual Client' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $c->user->email }}</div>
                            <div class="text-muted small" style="font-size: 11px;">{{ $c->phone ?? '—' }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $c->client_category }}</span></td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ $c->projects->count() }} Active</span></td>
                        <td>
                            @php $openTktCount = $c->tickets->where('closed_at', null)->count(); @endphp
                            <span class="badge {{ $openTktCount > 0 ? 'bg-warning-subtle text-warning' : 'bg-light text-muted border' }}">
                                {{ $openTktCount }} Open
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $c->account_status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($c->account_status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.clients.show', $c) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Profile"><i class="fas fa-eye text-muted"></i></a>
                            <a href="{{ route('admin.clients.edit', $c) }}" class="btn btn-sm btn-light border py-1 px-2 ms-1" title="Edit"><i class="fas fa-edit text-muted"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No clients found matching the query.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $clients->links() }}
        </div>
    @endif
</div>
@endsection
