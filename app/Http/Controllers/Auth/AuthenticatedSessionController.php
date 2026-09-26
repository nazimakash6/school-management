<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SecurityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $throttleKey = Str::lower($data['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Please try again in a minute.',
            ]);
        }

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], (bool) ($data['remember'] ?? false))) {
            RateLimiter::hit($throttleKey, 60);

            SecurityLogger::logAudit(null, 'auth', 'failed_login', 'Failed login attempt.', $request, [
                'email' => $data['email'],
            ]);

            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        SecurityLogger::recordLogin($user, $request);

        return redirect()->intended(route('dashboard.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        SecurityLogger::recordLogout($request->user(), $request);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }
}