@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<section class="auth-panel auth-panel--feature">
    <div class="auth-brand">
        <span class="auth-brand-kicker">Welcome Back</span>
        <h1>Login to your command center</h1>
        <p>Securely access the ERP, view dashboards, and continue with role-aware workflows.</p>
    </div>

    <div class="auth-stat-list">
        <div>
            <strong>Tracked</strong>
            <span>Login history and active sessions</span>
        </div>
        <div>
            <strong>Protected</strong>
            <span>Rate-limited sign in and account status checks</span>
        </div>
        <div>
            <strong>Audited</strong>
            <span>Security and activity events stored in the database</span>
        </div>
    </div>
</section>

<section class="auth-panel auth-panel--form">
    <div class="auth-card">
        <div class="auth-card-header">
            <h2>Login</h2>
            <p>Use your registered account to continue to the dashboard.</p>
        </div>

        @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="auth-form">
            @csrf
            <div>
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div>
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="loginPassword" class="form-control" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('loginPassword', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <label class="form-check auth-remember">
                <input class="form-check-input" type="checkbox" name="remember" value="1">
                <span class="form-check-label">Keep me signed in on this device</span>
            </label>
            <button type="submit" class="btn btn-primary auth-submit">Login</button>
        </form>

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