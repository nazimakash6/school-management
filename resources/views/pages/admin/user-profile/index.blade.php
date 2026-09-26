@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header Card -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Account</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">My Profile</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="user" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">User Account Profile</h3>
                    <p class="text-muted mb-0 fs-7">Manage your personal credentials, view login history, and account settings</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm fw-semibold d-inline-flex align-items-center gap-1.5 px-3">
                    <i data-lucide="log-out" style="width:1rem;height:1rem;"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Profile Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="mb-3 position-relative d-inline-block mx-auto">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2563eb&color=fff&size=128" 
                         alt="{{ auth()->user()->name }}" 
                         class="rounded-circle shadow-sm border border-3 border-white"
                         style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ auth()->user()->name }}</h4>
                <div class="mb-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-semibold fs-7">
                        {{ auth()->user()->role ?: 'User' }}
                    </span>
                </div>
                <div class="text-muted fs-7 mb-4">
                    <i data-lucide="mail" style="width:0.9rem;height:0.9rem;" class="me-1"></i> {{ auth()->user()->email }}
                </div>

                <hr class="my-3">

                <div class="d-flex flex-column gap-2 text-start fs-7">
                    <div class="d-flex justify-content-between text-muted">
                        <span>Account Status:</span>
                        <span class="badge bg-success-subtle text-success text-capitalize">{{ auth()->user()->status ?: 'Active' }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted">
                        <span>Last Login:</span>
                        <span class="fw-semibold text-dark">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M d, Y h:i A') : 'Current Session' }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <form method="POST" action="{{ route('logout') }}" class="w-100">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2">
                            <i data-lucide="log-out" style="width:1.1rem;height:1.1rem;"></i>
                            <span>Sign Out of Account</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Account Information Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="shield-check" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                    <span>Account Details</span>
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Full Name</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Email Address</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">User Role</label>
                        <input type="text" class="form-control text-capitalize" value="{{ auth()->user()->role }}" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Last Login IP</label>
                        <input type="text" class="form-control font-monospace" value="{{ auth()->user()->last_login_ip ?: request()->ip() }}" readonly disabled>
                    </div>
                </div>
            </div>

            <!-- Password Management Quick Action -->
            <div class="card border-0 shadow-sm rounded-4 p-4 d-flex flex-row align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-3">
                        <i data-lucide="key-round" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Password & Security</h6>
                        <small class="text-muted">Update your account password regularly to keep your data secure</small>
                    </div>
                </div>
                <a href="{{ route('password.edit') }}" class="btn btn-outline-primary fw-semibold btn-sm px-3">
                    Change Password
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
