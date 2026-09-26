<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SecurityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'roles' => User::roles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:' . implode(',', User::roles())],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => User::ACTIVE,
            'password' => $data['password'],
            'password_changed_at' => now(),
        ]);

        SecurityLogger::logAudit($user, 'auth', 'register', 'User registered successfully.', $request, [
            'role' => $user->role,
        ]);

        SecurityLogger::logActivity($user, 'registration', 'Created a new user account.', $request, [
            'role' => $user->role,
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Registration successful. Please log in to continue.');
    }
}