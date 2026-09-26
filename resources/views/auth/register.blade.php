@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<section class="auth-panel auth-panel--feature">
    <div class="auth-brand">
        <span class="auth-brand-kicker">School Management</span>
        <h1>Create your secure workspace</h1>
        <p>Register the first user account to access dashboards, staff tools, accounting, and academic operations.</p>
    </div>

    <div class="auth-feature-grid">
        <article>
            <h2>Security Features</h2>
            <ul>
                <li>Secure login with throttling and session protection</li>
                <li>Password management with strong password rules</li>
                <li>Role-based access control foundation</li>
                <li>Login history, session tracking, audit logs, and activity tracking</li>
            </ul>
        </article>
        <article>
            <h2>Available Roles</h2>
            <ul>
                @foreach ($roles as $role)
                <li>{{ $role }}</li>
                @endforeach
            </ul>
        </article>
    </div>
</section>

<section class="auth-panel auth-panel--form">
    <div class="auth-card">
        <div class="auth-card-header">
            <h2>Register</h2>
            <p>Start with a role-enabled account. After registration, log in to continue to the dashboard.</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="auth-form">
            @csrf
            <div>
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div>
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="">Select role</option>
                    @foreach ($roles as $role)
                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                    @endforeach
                </select>
            </div>
            <div class="auth-form-split">
                <div>
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="regPassword" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('regPassword', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="regPasswordConfirm" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('regPasswordConfirm', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary auth-submit">Create Account</button>
        </form>

        <p class="auth-switch">Already registered? <a href="{{ route('login') }}">Log in</a></p>
    </div>
</section>

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>`;
        } else {
            input.type = 'password';
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`;
        }
    }
</script>
@endpush
@endsection