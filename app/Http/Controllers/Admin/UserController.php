<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role   = $request->get('role');
        $status = $request->get('status');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $rolesList = Role::pluck('name')->toArray();
        if (empty($rolesList)) {
            $rolesList = User::roles();
        }

        // Dashboard Stats
        $stats = [
            'total'    => User::count(),
            'active'   => User::where('status', 'active')->count(),
            'teachers' => User::where('role', User::ROLE_TEACHER)->count(),
            'staff'    => User::whereIn('role', [User::ROLE_STAFF, User::ROLE_ACCOUNTANT, User::ROLE_RECEPTIONIST])->count(),
            'students' => User::where('role', User::ROLE_STUDENT)->count(),
            'parents'  => User::where('role', User::ROLE_PARENT)->count(),
        ];

        return view('pages.admin.users.index', compact('users', 'rolesList', 'stats', 'search', 'role', 'status'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string',
            'status'   => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|string',
            'status'   => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return redirect()->back()->with('success', "User status updated to {$newStatus}.");
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own logged-in account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
