<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::all()->map(function ($role) {
            $role->user_count = User::where('role_id', $role->id)
                ->orWhere('role', $role->name)
                ->count();
            return $role;
        });

        $availablePermissionsGrouped = Role::availablePermissionsGrouped();
        $allPermissions = Permission::orderBy('module')->orderBy('id')->get();

        return view('pages.admin.roles.index', compact('roles', 'availablePermissionsGrouped', 'allPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
        ]);

        $permissionsArray = $request->input('permissions', []);

        $role = Role::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'permissions' => $permissionsArray,
            'is_system'   => false,
        ]);

        // Sync relational DB permissions
        if (!empty($permissionsArray)) {
            $permIds = Permission::whereIn('name', $permissionsArray)->pluck('id')->toArray();
            $role->permissionsRelation()->sync($permIds);
        } else {
            $role->permissionsRelation()->sync([]);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully with permissions.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
        ]);

        $permissionsArray = $request->input('permissions', []);

        $updateData = [
            'description' => $validated['description'] ?? null,
            'permissions' => $permissionsArray,
        ];

        // Do not change name/slug for system roles
        if (!$role->is_system) {
            $updateData['name'] = $validated['name'];
            $updateData['slug'] = Str::slug($validated['name']);
        }

        $role->update($updateData);

        // Sync relational DB permissions
        $permIds = Permission::whereIn('name', $permissionsArray)->pluck('id')->toArray();
        $role->permissionsRelation()->sync($permIds);

        return redirect()->route('roles.index')->with('success', 'Role and permissions updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()->with('error', 'System roles cannot be deleted.');
        }

        $userCount = User::where('role_id', $role->id)->orWhere('role', $role->name)->count();
        if ($userCount > 0) {
            return redirect()->back()->with('error', "Cannot delete role '{$role->name}' because it is assigned to {$userCount} user(s).");
        }

        $role->permissionsRelation()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
