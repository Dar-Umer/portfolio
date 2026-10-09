@extends('layouts.admin')

@section('page_title', 'Staff Management & Role Permissions')

@section('content')
<div class="row g-4 mb-4">
    <!-- Staff Provisioning Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-user-plus text-primary me-2"></i> Provision Staff Member</h6>
            <p class="text-muted small">Create administrative personnel or developer team accounts with granular roles.</p>

            <form method="POST" action="{{ route('admin.roles.staff.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Full Name</label>
                    <input type="text" name="name" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control form-control-sm" value="StaffPassword2026!" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Assign Role</label>
                    <select name="role" class="form-select form-select-sm" required>
                        <option value="Administrator">Administrator / Support Staff</option>
                        <option value="Developer">Developer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Create Staff User</button>
            </form>
        </div>

        <!-- Staff List -->
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-users-cog text-info me-2"></i> Current Team & Staff</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffUsers as $usr)
                            <tr>
                                <td><span class="fw-semibold">{{ $usr->name }}</span></td>
                                <td>{{ $usr->email }}</td>
                                <td>
                                    @foreach($usr->roles as $r)
                                        <span class="badge {{ $r->name === 'Super Admin' ? 'bg-danger' : ($r->name === 'Administrator' ? 'bg-primary' : 'bg-secondary') }}">
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
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-shield-alt text-success me-2"></i> Role Permission Matrix</h6>
            </div>
            <div class="card-body">
                @foreach($roles->where('name', '!=', 'Customer') as $role)
                    <div class="p-3 border rounded mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Role: <span class="text-primary">{{ $role->name }}</span></h6>
                            <span class="badge bg-light text-dark border">{{ $role->permissions->count() }} Permissions Active</span>
                        </div>

                        @if($role->name === 'Super Admin')
                            <p class="small text-muted mb-0"><i class="fas fa-lock me-1"></i> Super Admin automatically retains full access across all modules.</p>
                        @else
                            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                                @csrf
                                @method('PUT')
                                <div class="row g-2 mb-3 small">
                                    @foreach(\Spatie\Permission\Models\Permission::all() as $perm)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="p_{{ $role->id }}_{{ $perm->id }}" {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="p_{{ $role->id }}_{{ $perm->id }}">
                                                    <code>{{ $perm->name }}</code>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Save Permissions for {{ $role->name }}</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
