@extends('layouts.app')
@section('title', 'User Management - EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">System Administration</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">User Management</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="users" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">System User Management</h3>
                    <p class="text-muted mb-0 fs-7">Manage login accounts for Staff, Teachers, Students, Parents & Administrators</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i data-lucide="user-plus" style="width:1rem;height:1rem;"></i>
                Add New User
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i data-lucide="check-circle" style="width:1.2rem;height:1.2rem;" class="me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i data-lucide="alert-circle" style="width:1.2rem;height:1.2rem;" class="me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small mb-1 fw-semibold">Total Users</div>
                    <div class="h4 fw-bold text-dark mb-0">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small mb-1 fw-semibold">Active Users</div>
                    <div class="h4 fw-bold text-success mb-0">{{ $stats['active'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small mb-1 fw-semibold">Teachers</div>
                    <div class="h4 fw-bold text-primary mb-0">{{ $stats['teachers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small mb-1 fw-semibold">Staff & Admin</div>
                    <div class="h4 fw-bold text-info mb-0">{{ $stats['staff'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small mb-1 fw-semibold">Students</div>
                    <div class="h4 fw-bold text-warning mb-0">{{ $stats['students'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small mb-1 fw-semibold">Parents</div>
                    <div class="h4 fw-bold text-secondary mb-0">{{ $stats['parents'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i data-lucide="search" style="width:1rem;height:1rem;"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search user by name, email or role..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="all">All Roles</option>
                        @foreach($rolesList as $r)
                            <option value="{{ $r }}" {{ $role === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all">All Status</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">Filter</button>
                    @if($search || ($role && $role !== 'all') || ($status && $status !== 'all'))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i data-lucide="rotate-ccw" style="width:1rem;height:1rem;"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Last Login</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width:40px;height:40px;font-size:.9rem;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($user->role) {
                                        'Director / Owner' => 'bg-danger',
                                        'Principal' => 'bg-primary',
                                        'Teachers' => 'bg-info',
                                        'Staff Users' => 'bg-secondary',
                                        'Student' => 'bg-warning text-dark',
                                        'Parent' => 'bg-dark',
                                        'Accountant' => 'bg-success',
                                        default => 'bg-primary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} bg-opacity-10 text-dark border px-2 py-1">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('users.toggle-status', $user->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm p-0 border-0 background-transparent" title="Click to toggle status">
                                        @if($user->status === 'active')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i data-lucide="check-circle" style="width:.75rem;height:.75rem;" class="me-1"></i> Active</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1"><i data-lucide="x-circle" style="width:.75rem;height:.75rem;" class="me-1"></i> Inactive</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-muted small">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="text-muted small">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-user"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $user->role }}"
                                        data-status="{{ $user->status }}"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal">
                                        <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-start-0" title="Delete User">
                                            <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i data-lucide="users" style="width:2.5rem;height:2.5rem;" class="mb-2 text-muted opacity-50"></i>
                                <div class="fw-semibold">No users found.</div>
                                <div class="small">Try adjusting search or role filters.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Add New User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="user-plus" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                    Add New System User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="user@school.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">User Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            @foreach($rolesList as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Create User Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="pencil" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                    Edit User Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Password <span class="text-muted fw-normal">(Leave blank to keep current)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Enter new password to change">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">User Role <span class="text-danger">*</span></label>
                        <select name="role" id="edit_role" class="form-select" required>
                            @foreach($rolesList as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', function () {
            const id     = this.dataset.id;
            const name   = this.dataset.name;
            const email  = this.dataset.email;
            const role   = this.dataset.role;
            const status = this.dataset.status;

            document.getElementById('editUserForm').action = `{{ url('users') }}/${id}`;
            document.getElementById('edit_name').value   = name;
            document.getElementById('edit_email').value  = email;
            document.getElementById('edit_role').value   = role;
            document.getElementById('edit_status').value = status;
        });
    });
});
</script>
@endpush
