@extends('layouts.admin')

@section('page_title', 'Staff Management & Role Permissions')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--app-text-main); letter-spacing: -0.02em;">Staff Management & Access Control</h4>
        <p class="text-muted small mb-0">Provision internal staff accounts, define operational roles, and enforce RBAC permission policies.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Staff Provisioning Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="rounded p-2 bg-primary-subtle text-primary" style="font-size: 14px;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h6 class="fw-bold mb-0 text-dark">Provision Staff Member</h6>
            </div>
            <p class="text-muted small mb-3">Create internal administrative or developer team accounts with granular roles.</p>

            <form method="POST" action="{{ route('admin.roles.staff.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Staff Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="staff@theumerstore.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Initial Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control font-monospace" value="StaffPassword2026!" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Assigned Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="Administrator">Administrator / Support Staff</option>
                        <option value="Developer">Technical Developer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-1"></i> Provision Staff Account
                </button>
            </form>
        </div>

        <!-- Staff List -->
        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-users-gear text-info"></i>
                <h6 class="fw-bold mb-0 text-dark">Active Internal Staff ({{ $staffUsers->count() }})</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffUsers as $usr)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2 text-white fw-bold shadow-xs" 
                                             style="width: 28px; height: 28px; font-size: 11px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                                            {{ strtoupper(substr($usr->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold text-dark small">{{ $usr->name }}</span>
                                    </div>
                                </td>
                                <td><span class="text-muted small">{{ $usr->email }}</span></td>
                                <td>
                                    @foreach($usr->roles as $r)
                                        @php
                                            $rBadge = match($r->name) {
                                                'Super Admin' => 'badge-soft-danger',
                                                'Administrator' => 'badge-soft-primary',
                                                'Developer' => 'badge-soft-info',
                                                default => 'badge-soft-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $rBadge }}">
                                            {{ $r->name }}
                                        </span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Role Permission Matrix -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-shield-halved text-success"></i>
                <h6 class="fw-bold mb-0 text-dark">Role Permission Matrix (RBAC)</h6>
            </div>
            <div class="card-body p-4">
                @foreach($roles->where('name', '!=', 'Customer') as $role)
                    <div class="p-3 border rounded-3 mb-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Role: <span class="text-primary">{{ $role->name }}</span></h6>
                            </div>
                            <span class="badge badge-soft-dark border">{{ $role->permissions->count() }} Permissions Active</span>
                        </div>

                        @if($role->name === 'Super Admin')
                            <div class="p-2 rounded bg-white border small text-muted">
                                <i class="fas fa-lock text-warning me-1"></i> Super Admin holds master wildcard clearance across all modules and functions.
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                                @csrf
                                @method('PUT')
                                <div class="row g-2 mb-3 small">
                                    @foreach(\Spatie\Permission\Models\Permission::all() as $perm)
                                        <div class="col-md-6">
                                            <div class="form-check p-2 bg-white rounded border">
                                                <input class="form-check-input ms-0 me-2" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="p_{{ $role->id }}_{{ $perm->id }}" {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                                <label class="form-check-label font-monospace text-dark" style="font-size: 11.5px;" for="p_{{ $role->id }}_{{ $perm->id }}">
                                                    {{ $perm->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-save me-1"></i> Save {{ $role->name }} Permissions
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
