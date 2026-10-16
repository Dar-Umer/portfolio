@extends('layouts.public')

@section('title', 'Client Portal Dashboard | The Umer Store')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}</h3>
            <p class="text-muted small mb-0">{{ $client->company_name ?? 'Client Account' }} &bull; {{ $client->client_number }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fas fa-key me-1"></i> Change Password
            </button>
            <a href="{{ route('client.tickets.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Raise Support Ticket
            </a>
        </div>
    </div>

    <!-- Client Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="text-muted small fw-semibold">Active Projects</div>
                <h4 class="fw-bold text-primary mb-0">{{ $metrics['active_projects'] }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="text-muted small fw-semibold">Open Tickets</div>
                <h4 class="fw-bold text-warning mb-0">{{ $metrics['open_tickets'] }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="text-muted small fw-semibold">Pending Quotations</div>
                <h4 class="fw-bold text-info mb-0">{{ $metrics['pending_quotations'] }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="text-muted small fw-semibold">Outstanding Balance</div>
                <h4 class="fw-bold text-danger mb-0">₹{{ number_format($metrics['outstanding_balance'], 2) }}</h4>
            </div>
        </div>
    </div>

    <!-- Active Tickets & Invoices -->
    <div class="row g-4">
        <!-- Tickets -->
        <div class="col-lg-7">
            <div class="card card-custom bg-white">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-ticket-alt text-primary me-2"></i> My Support Tickets</h6>
                    <a href="{{ route('client.tickets.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Ticket #</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets as $tkt)
                                <tr>
                                    <td><span class="fw-bold">{{ $tkt->ticket_number }}</span></td>
                                    <td>{{ Str::limit($tkt->subject, 30) }}</td>
                                    <td><span class="badge bg-secondary">{{ $tkt->priority }}</span></td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $tkt->status->color }}">
                                            {{ $tkt->status->name }}
                                        </span>
                                    </td>
                                    <td>{{ $tkt->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('client.tickets.show', $tkt) }}" class="btn btn-sm btn-light border"><i class="fas fa-chevron-right"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No tickets filed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Invoices -->
        <div class="col-lg-5">
            <div class="card card-custom bg-white">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-success me-2"></i> Invoices & Statements</h6>
                </div>
                <div class="p-3">
                    @forelse($invoices as $inv)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <div class="fw-semibold small">{{ $inv->invoice_number }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Due: {{ $inv->due_date->format('d M Y') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold small">₹{{ number_format($inv->grand_total, 2) }}</div>
                                <span class="badge {{ $inv->status === 'Paid' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size: 0.7rem;">
                                    {{ $inv->status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">No invoices issued.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Change Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('client.profile.password.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom bg-light">
                    <h5 class="modal-title fw-bold text-dark fs-6">
                        <i class="fas fa-lock text-primary me-1"></i> Change Portal Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 small">
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">
                        <i class="fas fa-save me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
