<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Audit\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::all()->groupBy(function($item) {
            $parts = explode('_', $item->name);
            return end($parts);
        });
        $staffUsers = User::role(['Super Admin', 'Administrator', 'Developer'])->with('roles')->get();

        return view('admin.roles.index', compact('roles', 'permissions', 'staffUsers'));
    }

    public function updateRolePermissions(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'Super Admin') {
            return back()->with('error', 'Super Admin permissions cannot be modified.');
        }

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $permissions = $validated['permissions'] ?? [];
        $role->syncPermissions($permissions);

        AuditService::log('role_permissions_updated', null, "Updated permissions for role: {$role->name}");

        return back()->with('success', "Permissions for role '{$role->name}' updated successfully.");
    }

    public function storeStaff(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:Administrator,Developer'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        AuditService::log('staff_created', $user, "Created staff member {$user->name} ({$validated['role']})");

        return back()->with('success', "Staff user {$user->name} created and assigned to {$validated['role']}.");
    }
}
