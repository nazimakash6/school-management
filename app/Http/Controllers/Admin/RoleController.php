<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            $role->user_count = User::where('role', $role->name)->count();
            return $role;
        });

        $availablePermissions = Role::availablePermissions();

        return view('pages.admin.roles.index', compact('roles', 'availablePermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['permissions'] = $request->input('permissions', []);
        $validated['is_system']   = false;

        Role::create($validated);

        return redirect()->route('roles.index')->with('success', 'Role created successfully with permissions.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
        ]);

        $validated['permissions'] = $request->input('permissions', []);

        // Do not change slug for system roles
        if (!$role->is_system) {
            $validated['slug'] = Str::slug($validated['name']);
        } else {
            unset($validated['name']);
        }

        $role->update($validated);

        return redirect()->route('roles.index')->with('success', 'Role and permissions updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()->with('error', 'System roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
