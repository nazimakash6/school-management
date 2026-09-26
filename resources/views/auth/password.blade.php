@extends('layouts.guest')

@section('title', 'Password Management')

@section('content')
<section class="auth-panel auth-panel--feature">
    <div class="auth-brand">
        <span class="auth-brand-kicker">Password Management</span>
        <h1>Strengthen account security</h1>
        <p>Update your password with a strong credential. The change will be logged for audit and activity tracking.</p>
    </div>
</section>

<section class="auth-panel auth-panel--form">
    <div class="auth-card">
        <div class="auth-card-header">
            <h2>Update Password</h2>
            <p>Use a strong password with letters, numbers, mixed case, and symbols.</p>
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

        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf
            @method('PUT')
            <div>
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div>
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary auth-submit">Update Password</button>
        </form>

        <p class="auth-switch"><a href="{{ route('dashboard.index') }}">Back to dashboard</a></p>
    </div>
</section>
@endsection