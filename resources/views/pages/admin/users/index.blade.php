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
                    <p class="text-muted mb-0 fs-7">Manage login accounts, roles & individual permission overrides for Staff, Teachers, Students & Parents</p>
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
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search user by name, email, role or CNIC..." value="{{ $search }}">
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
                            <th>Role & Overrides</th>
                            <th>Linked Entity</th>
                            <th>Status</th>
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
                                        <div class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                            {{ $user->name }}
                                            @if($user->isSuperAdmin())
                                                <span class="badge bg-danger text-white fs-9 ms-1" title="Primary Super Admin"><i data-lucide="shield-alert" style="width:0.75rem;height:0.75rem;"></i> Super Admin</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleName = $user->roleRelation?->name ?? $user->role;
                                    $badgeClass = match($roleName) {
                                        'Admin', 'Director / Owner' => 'bg-danger text-white',
                                        'Principal' => 'bg-primary text-white',
                                        'Teacher' => 'bg-info text-white',
                                        'Staff' => 'bg-secondary text-white',
                                        'Student' => 'bg-warning text-dark',
                                        'Parent' => 'bg-dark text-white',
                                        'Accountant' => 'bg-success text-white',
                                        default => 'bg-primary text-white'
                                    };
                                    $directAllowed = $user->directPermissions->where('pivot.is_granted', 1)->pluck('name')->toArray();
                                    $directDenied = $user->directPermissions->where('pivot.is_granted', 0)->pluck('name')->toArray();
                                @endphp
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge {{ $badgeClass }} border px-2 py-1 me-auto">
                                        {{ $roleName }}
                                    </span>
                                    @if(count($directAllowed) > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success fs-8">+{{ count($directAllowed) }} Custom Allowed</span>
                                    @endif
                                    @if(count($directDenied) > 0)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger fs-8">-{{ count($directDenied) }} Custom Denied</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->student)
                                    <span class="badge bg-light text-dark border">Student: {{ $user->student->full_name }} ({{ $user->student->class_name }})</span>
                                @elseif($user->staff)
                                    <span class="badge bg-light text-dark border">Staff: {{ $user->staff->full_name }} ({{ $user->staff->formatted_designation }})</span>
                                @elseif($user->father_cnic)
                                    <span class="badge bg-light text-dark border">Parent CNIC: {{ $user->father_cnic }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->isSuperAdmin())
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i data-lucide="check-circle" style="width:.75rem;height:.75rem;" class="me-1"></i> Active (Protected)</span>
                                @else
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
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-info btn-user-perms"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-role="{{ $roleName }}"
                                        data-is-super="{{ $user->isSuperAdmin() ? '1' : '0' }}"
                                        data-allowed="{{ json_encode($directAllowed) }}"
                                        data-denied="{{ json_encode($directDenied) }}"
                                        data-bs-toggle="modal" data-bs-target="#userPermissionsModal"
                                        title="Manage Individual Permissions">
                                        <i data-lucide="key" style="width:0.875rem;height:0.875rem;"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-user"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $roleName }}"
                                        data-status="{{ $user->status }}"
                                        data-student-id="{{ $user->student_id }}"
                                        data-staff-id="{{ $user->staff_id }}"
                                        data-cnic="{{ $user->father_cnic }}"
                                        data-is-super="{{ $user->isSuperAdmin() ? '1' : '0' }}"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                        title="Edit User Info">
                                        <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                                    </button>
                                    @if(auth()->id() !== $user->id && !$user->isSuperAdmin())
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="user-plus" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                    Add New System User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}" id="addUserForm">
                @csrf
                <!-- Hidden inputs for linking -->
                <input type="hidden" name="student_id" id="add_student_id_val">
                <input type="hidden" name="staff_id" id="add_staff_id_val">

                <div class="modal-body p-4">
                    <!-- 1st Field: User Role -->
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <label class="form-label fw-bold text-dark fs-6 mb-1">
                            <i data-lucide="shield" style="width:1rem;height:1rem;" class="me-1 text-primary"></i>
                            1st Field: Select User Role <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted small mb-2">Choose the role for the new user account to load entity selection options.</p>
                        <select name="role" id="add_user_role" class="form-select form-select-lg border-primary shadow-sm" required>
                            <option value="">-- Select Role --</option>
                            @foreach($rolesList as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dynamic Entity Selection Container -->
                    <!-- A. STUDENT SECTION -->
                    <div id="role_section_student" class="role-dynamic-section mb-4 p-3 rounded-3 border border-warning bg-warning bg-opacity-10" style="display: none;">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i data-lucide="graduation-cap" class="text-warning" style="width:1.1rem;height:1.1rem;"></i>
                            Student Selection (Unlinked Students)
                        </h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">2nd Field: Academic Session</label>
                                <select id="filter_student_session" class="form-select form-select-sm">
                                    <option value="">-- All Academic Sessions --</option>
                                    @foreach($academicSessions as $sess)
                                        <option value="{{ $sess->id }}">{{ $sess->session_name ?? $sess->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">3rd Field: Class</label>
                                <select id="filter_student_class" class="form-select form-select-sm">
                                    <option value="">-- All Classes --</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark small">4th Field: Select Student <span class="text-muted">(Searchable Dropdown)</span></label>
                            <div class="searchable-dropdown-wrapper position-relative" id="wrapper_student_search">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i data-lucide="search" style="width:1rem;height:1rem;"></i></span>
                                    <input type="text" id="search_student_input" class="form-control searchable-input" placeholder="Type student name, admission # or roll # to search..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-secondary searchable-clear-btn" style="display:none;" title="Clear Selection">&times;</button>
                                </div>
                                <div class="searchable-list-container position-absolute w-100 bg-white border rounded-bottom shadow-lg mt-1" style="max-height: 220px; overflow-y: auto; z-index: 1056; display: none;">
                                    <div class="list-group list-group-flush" id="list_student_options">
                                        <!-- Dynamic student items -->
                                    </div>
                                </div>
                            </div>
                            <div class="form-text text-muted small">Only unlinked students without an active user account are listed.</div>
                        </div>
                    </div>

                    <!-- B. STAFF SECTION -->
                    <div id="role_section_staff" class="role-dynamic-section mb-4 p-3 rounded-3 border border-info bg-info bg-opacity-10" style="display: none;">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                            <i data-lucide="briefcase" class="text-info" style="width:1.1rem;height:1.1rem;"></i>
                            Staff Selection (Unlinked Staff Members)
                        </h6>
                        <label class="form-label fw-semibold text-dark small">2nd Field: Select Staff Member <span class="text-muted">(Searchable Dropdown)</span></label>
                        <div class="searchable-dropdown-wrapper position-relative" id="wrapper_staff_search">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i data-lucide="search" style="width:1rem;height:1rem;"></i></span>
                                <input type="text" id="search_staff_input" class="form-control searchable-input" placeholder="Type staff name, ID or designation to search..." autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary searchable-clear-btn" style="display:none;" title="Clear Selection">&times;</button>
                            </div>
                            <div class="searchable-list-container position-absolute w-100 bg-white border rounded-bottom shadow-lg mt-1" style="max-height: 220px; overflow-y: auto; z-index: 1056; display: none;">
                                <div class="list-group list-group-flush" id="list_staff_options">
                                    <!-- Dynamic staff items -->
                                </div>
                            </div>
                        </div>
                        <div class="form-text text-muted small">Only staff members without an existing user account are listed.</div>
                    </div>

                    <!-- C. TEACHER SECTION -->
                    <div id="role_section_teacher" class="role-dynamic-section mb-4 p-3 rounded-3 border border-primary bg-primary bg-opacity-10" style="display: none;">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                            <i data-lucide="user-check" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            Teacher Selection (Unlinked Teaching Staff)
                        </h6>
                        <label class="form-label fw-semibold text-dark small">2nd Field: Select Teacher Profile <span class="text-muted">(Searchable Dropdown)</span></label>
                        <div class="searchable-dropdown-wrapper position-relative" id="wrapper_teacher_search">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i data-lucide="search" style="width:1rem;height:1rem;"></i></span>
                                <input type="text" id="search_teacher_input" class="form-control searchable-input" placeholder="Type teacher name, ID or designation to search..." autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary searchable-clear-btn" style="display:none;" title="Clear Selection">&times;</button>
                            </div>
                            <div class="searchable-list-container position-absolute w-100 bg-white border rounded-bottom shadow-lg mt-1" style="max-height: 220px; overflow-y: auto; z-index: 1056; display: none;">
                                <div class="list-group list-group-flush" id="list_teacher_options">
                                    <!-- Dynamic teacher items -->
                                </div>
                            </div>
                        </div>
                        <div class="form-text text-muted small">Only teachers without an existing user account are listed.</div>
                    </div>

                    <!-- D. PARENT SECTION -->
                    <div id="role_section_parent" class="role-dynamic-section mb-4 p-3 rounded-3 border border-secondary bg-secondary bg-opacity-10" style="display: none;">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                            <i data-lucide="users" class="text-secondary" style="width:1.1rem;height:1.1rem;"></i>
                            Parent Account - Link Student
                        </h6>
                        <label class="form-label fw-semibold text-dark small">2nd Field: Select Student to Link Parent <span class="text-muted">(Searchable Dropdown)</span></label>
                        <div class="searchable-dropdown-wrapper position-relative" id="wrapper_parent_search">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i data-lucide="search" style="width:1rem;height:1rem;"></i></span>
                                <input type="text" id="search_parent_student_input" class="form-control searchable-input" placeholder="Type student or father name, admission # to search..." autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary searchable-clear-btn" style="display:none;" title="Clear Selection">&times;</button>
                            </div>
                            <div class="searchable-list-container position-absolute w-100 bg-white border rounded-bottom shadow-lg mt-1" style="max-height: 220px; overflow-y: auto; z-index: 1056; display: none;">
                                <div class="list-group list-group-flush" id="list_parent_student_options">
                                    <!-- Dynamic parent student items -->
                                </div>
                            </div>
                        </div>
                        <div class="form-text text-muted small">Select a student to automatically populate Father/Guardian Name and CNIC.</div>
                    </div>

                    <!-- Auto-Filled User Account Details -->
                    <div class="border rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 d-flex align-items-center gap-2">
                            <i data-lucide="id-card" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            User Account Details <span class="badge bg-primary bg-opacity-10 text-primary border font-monospace ms-auto">Auto-Filled Below</span>
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="add_user_name" class="form-control" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="add_user_email" class="form-control" placeholder="user@school.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="add_user_password" class="form-control" placeholder="Minimum 6 characters" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                                <select name="status" id="add_user_status" class="form-select" required>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Parent Father/Guardian CNIC <span class="text-muted fw-normal">(Optional)</span></label>
                                <input type="text" name="father_cnic" id="add_user_father_cnic" class="form-control" placeholder="e.g. 35201-1234567-1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4">
                        <i data-lucide="check" style="width:1rem;height:1rem;" class="me-1"></i>
                        Create User Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Password <span class="text-muted fw-normal">(Leave blank to keep current)</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Enter new password to change">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">User Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit_role" class="form-select" required>
                                @foreach($rolesList as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Parent Father/Guardian CNIC</label>
                            <input type="text" name="father_cnic" id="edit_cnic" class="form-control" placeholder="e.g. 35201-1234567-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Linked Student Profile</label>
                            <select name="student_id" id="edit_student_id" class="form-select">
                                <option value="">-- None --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">{{ $st->full_name }} (Reg: {{ $st->admission_no }} - Class {{ $st->class_name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Linked Staff/Teacher Profile</label>
                            <select name="staff_id" id="edit_staff_id" class="form-select">
                                <option value="">-- None --</option>
                                @foreach($staffMembers as $sf)
                                    <option value="{{ $sf->id }}">{{ $sf->full_name }} (ID: {{ $sf->staff_id }} - {{ $sf->formatted_designation }})</option>
                                @endforeach
                            </select>
                        </div>
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

<!-- Modal: Assign Individual User Permissions (Allow / Deny Overrides) -->
<div class="modal fade" id="userPermissionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form id="userPermissionsForm" method="POST" action="" class="modal-content border-0 shadow" style="max-height: 88vh; display: flex; flex-direction: column;">
            @csrf
            <div class="modal-header border-bottom flex-shrink-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i data-lucide="key" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                    Individual Permission Overrides for: <span id="perm_user_name" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 flex-grow-1 overflow-auto" style="max-height: calc(88vh - 130px); overflow-y: auto !important;">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i data-lucide="info" style="width:1.2rem;height:1.2rem;" class="me-2"></i>
                    Role: <strong id="perm_user_role"></strong>. Here you can grant additional specific permissions to this individual user, or explicitly deny specific permissions regardless of their role.
                </div>

                <div class="row g-3">
                    @foreach($availablePermissionsGrouped as $category => $perms)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border shadow-none rounded-3 h-100">
                            <div class="card-header bg-light py-2 fw-semibold text-dark small">
                                {{ $category }}
                            </div>
                            <div class="card-body p-3">
                                @foreach($perms as $key => $label)
                                <div class="mb-2 pb-2 border-bottom last-border-0">
                                    <div class="fw-semibold text-dark small mb-1">{{ $label }}</div>
                                    <div class="btn-group btn-group-sm w-100" role="group">
                                        <input type="radio" class="btn-check perm-radio-default" name="perm_state[{{ $key }}]" id="state_def_{{ Str::slug($key) }}" value="default" checked>
                                        <label class="btn btn-outline-secondary btn-xs py-1" for="state_def_{{ Str::slug($key) }}">Default (Role)</label>

                                        <input type="radio" class="btn-check perm-radio-allow" name="perm_state[{{ $key }}]" id="state_allow_{{ Str::slug($key) }}" value="allow">
                                        <label class="btn btn-outline-success btn-xs py-1" for="state_allow_{{ Str::slug($key) }}"><i data-lucide="check" style="width:.7rem;height:.7rem;"></i> Allow</label>

                                        <input type="radio" class="btn-check perm-radio-deny" name="perm_state[{{ $key }}]" id="state_deny_{{ Str::slug($key) }}" value="deny">
                                        <label class="btn btn-outline-danger btn-xs py-1" for="state_deny_{{ Str::slug($key) }}"><i data-lucide="x" style="width:.7rem;height:.7rem;"></i> Deny</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-top bg-light flex-shrink-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fw-semibold">Save User Permission Overrides</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit User Modal
    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', function () {
            const id        = this.dataset.id;
            const name      = this.dataset.name;
            const email     = this.dataset.email;
            const role      = this.dataset.role;
            const status    = this.dataset.status;
            const studentId = this.dataset.studentId;
            const staffId   = this.dataset.staffId;
            const cnic      = this.dataset.cnic;
            const isSuper   = this.dataset.isSuper === '1';

            document.getElementById('editUserForm').action = `{{ url('users') }}/${id}`;
            document.getElementById('edit_name').value       = name;
            document.getElementById('edit_email').value      = email;
            document.getElementById('edit_role').value       = role;
            document.getElementById('edit_status').value     = status;
            document.getElementById('edit_student_id').value = studentId || '';
            document.getElementById('edit_staff_id').value   = staffId || '';
            document.getElementById('edit_cnic').value       = cnic || '';

            if (isSuper) {
                document.getElementById('edit_role').disabled = true;
                document.getElementById('edit_status').disabled = true;
            } else {
                document.getElementById('edit_role').disabled = false;
                document.getElementById('edit_status').disabled = false;
            }
        });
    });

    // Individual User Permissions Modal
    document.querySelectorAll('.btn-user-perms').forEach(btn => {
        btn.addEventListener('click', function () {
            const id      = this.dataset.id;
            const name    = this.dataset.name;
            const role    = this.dataset.role;
            const allowed = JSON.parse(this.dataset.allowed || '[]');
            const denied  = JSON.parse(this.dataset.denied || '[]');

            document.getElementById('userPermissionsForm').action = `{{ url('users') }}/${id}/permissions`;
            document.getElementById('perm_user_name').textContent = name;
            document.getElementById('perm_user_role').textContent = role;

            // Reset all permission radios to 'default'
            document.querySelectorAll('.perm-radio-default').forEach(r => r.checked = true);

            // Set allowed radios
            allowed.forEach(perm => {
                const radio = document.querySelector(`.perm-radio-allow[name="perm_state[${perm}]"]`);
                if (radio) radio.checked = true;
            });

            // Set denied radios
            denied.forEach(perm => {
                const radio = document.querySelector(`.perm-radio-deny[name="perm_state[${perm}]"]`);
                if (radio) radio.checked = true;
            });
        });
    });

    // Handle Form Submit for User Permissions Modal (Convert radios into allowed_permissions[] & denied_permissions[])
    const permForm = document.getElementById('userPermissionsForm');
    if (permForm) {
        permForm.addEventListener('submit', function (e) {
            // Remove previous hidden inputs
            permForm.querySelectorAll('input[name="allowed_permissions[]"], input[name="denied_permissions[]"]').forEach(el => el.remove());

            const radios = permForm.querySelectorAll('input[type="radio"]:checked');
            radios.forEach(r => {
                const match = r.name.match(/perm_state\[(.*?)\]/);
                if (match && match[1]) {
                    const permKey = match[1];
                    if (r.value === 'allow') {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'allowed_permissions[]';
                        input.value = permKey;
                        permForm.appendChild(input);
                    } else if (r.value === 'deny') {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'denied_permissions[]';
                        input.value = permKey;
                        permForm.appendChild(input);
                    }
                }
            });
        });
    }

    // -------------------------------------------------------------
    // ADD NEW USER MODAL DYNAMIC FORM & AUTO-FILL LOGIC
    // -------------------------------------------------------------
    const unlinkedStudents = @json($unlinkedStudents);
    const allStudentsForParent = @json($allStudentsForParent);
    const unlinkedStaff = @json($unlinkedStaff);
    const unlinkedTeachers = @json($unlinkedTeachers);
    const allUnlinkedStaff = @json($allUnlinkedStaff);

    const roleSelect = document.getElementById('add_user_role');

    // Sections
    const sectionStudent = document.getElementById('role_section_student');
    const sectionStaff   = document.getElementById('role_section_staff');
    const sectionTeacher = document.getElementById('role_section_teacher');
    const sectionParent  = document.getElementById('role_section_parent');

    // Hidden ID inputs
    const hiddenStudentId = document.getElementById('add_student_id_val');
    const hiddenStaffId   = document.getElementById('add_staff_id_val');

    // Auto-fill target inputs
    const inputName  = document.getElementById('add_user_name');
    const inputEmail = document.getElementById('add_user_email');
    const inputCnic  = document.getElementById('add_user_father_cnic');

    function resetAddUserFormSelections() {
        if (hiddenStudentId) hiddenStudentId.value = '';
        if (hiddenStaffId) hiddenStaffId.value = '';
        
        document.querySelectorAll('.searchable-input').forEach(i => i.value = '');
        document.querySelectorAll('.searchable-clear-btn').forEach(b => b.style.display = 'none');
        document.querySelectorAll('.searchable-list-container').forEach(c => c.style.display = 'none');
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            const role = this.value;

            // Hide all sections first
            document.querySelectorAll('.role-dynamic-section').forEach(sec => sec.style.display = 'none');
            resetAddUserFormSelections();

            if (role === 'Student') {
                if (sectionStudent) {
                    sectionStudent.style.display = 'block';
                    renderStudentOptions();
                }
            } else if (role === 'Teacher') {
                if (sectionTeacher) {
                    sectionTeacher.style.display = 'block';
                    renderTeacherOptions();
                }
            } else if (role === 'Parent') {
                if (sectionParent) {
                    sectionParent.style.display = 'block';
                    renderParentStudentOptions();
                }
            } else if (['Staff', 'Accountant', 'Receptionist', 'Operator', 'Director / Owner', 'Admin'].includes(role)) {
                if (sectionStaff) {
                    sectionStaff.style.display = 'block';
                    renderStaffOptions();
                }
            }
        });
    }

    // --- STUDENT SEARCHABLE DROPDOWN LOGIC ---
    const sessionFilter = document.getElementById('filter_student_session');
    const classFilter   = document.getElementById('filter_student_class');
    const studentSearchInput = document.getElementById('search_student_input');
    const studentListContainer = document.getElementById('list_student_options');

    function renderStudentOptions() {
        if (!studentListContainer) return;
        studentListContainer.innerHTML = '';

        const selectedSession = sessionFilter ? sessionFilter.value : '';
        const selectedClass   = classFilter ? classFilter.value : '';
        const query           = studentSearchInput ? studentSearchInput.value.trim().toLowerCase() : '';

        const filtered = unlinkedStudents.filter(st => {
            if (selectedSession && String(st.academic_session_id) !== String(selectedSession)) return false;
            if (selectedClass && st.class_name !== selectedClass) return false;
            if (query) {
                const fullName = `${st.first_name || ''} ${st.last_name || ''}`.toLowerCase();
                const adm = (st.admission_no || '').toLowerCase();
                const roll = (st.roll_no || '').toLowerCase();
                return fullName.includes(query) || adm.includes(query) || roll.includes(query);
            }
            return true;
        });

        if (filtered.length === 0) {
            studentListContainer.innerHTML = '<div class="p-3 text-muted small text-center">No matching unlinked students found.</div>';
            return;
        }

        filtered.slice(0, 50).forEach(st => {
            const fullName = `${st.first_name || ''} ${st.last_name || ''}`.trim();
            const email    = st.student_email || (st.admission_no ? (st.admission_no.toLowerCase() + '@school.com') : (`student${st.id}@school.com`));
            const cnic     = st.father_cnic || st.guardian_cnic || '';

            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action py-2 px-3 text-start small border-bottom';
            item.innerHTML = `
                <div class="fw-bold text-dark">${fullName}</div>
                <div class="text-muted fs-8">
                    <span class="badge bg-light text-dark border me-1">Adm: ${st.admission_no || 'N/A'}</span>
                    <span class="badge bg-light text-dark border me-1">Class: ${st.class_name || 'N/A'}</span>
                    ${st.roll_no ? '<span class="badge bg-light text-dark border">Roll: ' + st.roll_no + '</span>' : ''}
                </div>
            `;
            item.addEventListener('click', function (e) {
                e.stopPropagation();
                hiddenStudentId.value = st.id;
                studentSearchInput.value = `${fullName} (Adm: ${st.admission_no || 'N/A'} - Class ${st.class_name || 'N/A'})`;
                document.querySelector('#wrapper_student_search .searchable-clear-btn').style.display = 'inline-block';
                studentSearchInput.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'none';

                // Auto fill details
                inputName.value = fullName;
                inputEmail.value = email;
                if (cnic) inputCnic.value = cnic;
            });
            studentListContainer.appendChild(item);
        });
    }

    if (sessionFilter) sessionFilter.addEventListener('change', renderStudentOptions);
    if (classFilter) classFilter.addEventListener('change', renderStudentOptions);
    if (studentSearchInput) {
        studentSearchInput.addEventListener('focus', function () {
            renderStudentOptions();
            this.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'block';
        });
        studentSearchInput.addEventListener('input', function () {
            renderStudentOptions();
            this.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'block';
        });
    }

    // --- STAFF SEARCHABLE DROPDOWN LOGIC ---
    const staffSearchInput = document.getElementById('search_staff_input');
    const staffListContainer = document.getElementById('list_staff_options');

    function renderStaffOptions() {
        if (!staffListContainer) return;
        staffListContainer.innerHTML = '';
        const query = staffSearchInput ? staffSearchInput.value.trim().toLowerCase() : '';
        const listToUse = unlinkedStaff.length > 0 ? unlinkedStaff : allUnlinkedStaff;

        const filtered = listToUse.filter(sf => {
            if (!query) return true;
            const fullName = `${sf.first_name || ''} ${sf.last_name || ''}`.toLowerCase();
            const sid = (sf.staff_id || '').toLowerCase();
            const desig = (sf.designation || '').toLowerCase();
            return fullName.includes(query) || sid.includes(query) || desig.includes(query);
        });

        if (filtered.length === 0) {
            staffListContainer.innerHTML = '<div class="p-3 text-muted small text-center">No matching unlinked staff members found.</div>';
            return;
        }

        filtered.slice(0, 50).forEach(sf => {
            const fullName = `${sf.first_name || ''} ${sf.last_name || ''}`.trim();
            const email    = sf.email || (sf.staff_id ? (sf.staff_id.toLowerCase() + '@school.com') : (`staff${sf.id}@school.com`));
            const cnic     = sf.cnic || '';

            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action py-2 px-3 text-start small border-bottom';
            item.innerHTML = `
                <div class="fw-bold text-dark">${fullName}</div>
                <div class="text-muted fs-8">
                    <span class="badge bg-light text-dark border me-1">ID: ${sf.staff_id || 'N/A'}</span>
                    <span class="badge bg-light text-dark border me-1">Designation: ${sf.designation || 'Staff'}</span>
                </div>
            `;
            item.addEventListener('click', function (e) {
                e.stopPropagation();
                hiddenStaffId.value = sf.id;
                staffSearchInput.value = `${fullName} (ID: ${sf.staff_id || 'N/A'} - ${sf.designation || 'Staff'})`;
                document.querySelector('#wrapper_staff_search .searchable-clear-btn').style.display = 'inline-block';
                staffSearchInput.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'none';

                // Auto fill details
                inputName.value = fullName;
                inputEmail.value = email;
                if (cnic) inputCnic.value = cnic;
            });
            staffListContainer.appendChild(item);
        });
    }

    if (staffSearchInput) {
        staffSearchInput.addEventListener('focus', function () {
            renderStaffOptions();
            this.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'block';
        });
        staffSearchInput.addEventListener('input', function () {
            renderStaffOptions();
            this.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'block';
        });
    }

    // --- TEACHER SEARCHABLE DROPDOWN LOGIC ---
    const teacherSearchInput = document.getElementById('search_teacher_input');
    const teacherListContainer = document.getElementById('list_teacher_options');

    function renderTeacherOptions() {
        if (!teacherListContainer) return;
        teacherListContainer.innerHTML = '';
        const query = teacherSearchInput ? teacherSearchInput.value.trim().toLowerCase() : '';
        const listToUse = unlinkedTeachers.length > 0 ? unlinkedTeachers : allUnlinkedStaff;

        const filtered = listToUse.filter(sf => {
            if (!query) return true;
            const fullName = `${sf.first_name || ''} ${sf.last_name || ''}`.toLowerCase();
            const sid = (sf.staff_id || '').toLowerCase();
            const desig = (sf.designation || '').toLowerCase();
            return fullName.includes(query) || sid.includes(query) || desig.includes(query);
        });

        if (filtered.length === 0) {
            teacherListContainer.innerHTML = '<div class="p-3 text-muted small text-center">No matching unlinked teachers found.</div>';
            return;
        }

        filtered.slice(0, 50).forEach(sf => {
            const fullName = `${sf.first_name || ''} ${sf.last_name || ''}`.trim();
            const email    = sf.email || (sf.staff_id ? (sf.staff_id.toLowerCase() + '@school.com') : (`teacher${sf.id}@school.com`));
            const cnic     = sf.cnic || '';

            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action py-2 px-3 text-start small border-bottom';
            item.innerHTML = `
                <div class="fw-bold text-dark">${fullName}</div>
                <div class="text-muted fs-8">
                    <span class="badge bg-light text-dark border me-1">ID: ${sf.staff_id || 'N/A'}</span>
                    <span class="badge bg-light text-dark border me-1">Desig: ${sf.designation || 'Teacher'}</span>
                </div>
            `;
            item.addEventListener('click', function (e) {
                e.stopPropagation();
                hiddenStaffId.value = sf.id;
                teacherSearchInput.value = `${fullName} (ID: ${sf.staff_id || 'N/A'} - ${sf.designation || 'Teacher'})`;
                document.querySelector('#wrapper_teacher_search .searchable-clear-btn').style.display = 'inline-block';
                teacherSearchInput.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'none';

                // Auto fill details
                inputName.value = fullName;
                inputEmail.value = email;
                if (cnic) inputCnic.value = cnic;
            });
            teacherListContainer.appendChild(item);
        });
    }

    if (teacherSearchInput) {
        teacherSearchInput.addEventListener('focus', function () {
            renderTeacherOptions();
            this.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'block';
        });
        teacherSearchInput.addEventListener('input', function () {
            renderTeacherOptions();
            this.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'block';
        });
    }

    // --- PARENT SEARCHABLE DROPDOWN LOGIC ---
    const parentSearchInput = document.getElementById('search_parent_student_input');
    const parentListContainer = document.getElementById('list_parent_student_options');

    function renderParentStudentOptions() {
        if (!parentListContainer) return;
        parentListContainer.innerHTML = '';
        const query = parentSearchInput ? parentSearchInput.value.trim().toLowerCase() : '';

        const filtered = allStudentsForParent.filter(st => {
            if (!query) return true;
            const sName = `${st.first_name || ''} ${st.last_name || ''}`.toLowerCase();
            const fName = (st.father_name || '').toLowerCase();
            const gName = (st.guardian_name || '').toLowerCase();
            const adm = (st.admission_no || '').toLowerCase();
            return sName.includes(query) || fName.includes(query) || gName.includes(query) || adm.includes(query);
        });

        if (filtered.length === 0) {
            parentListContainer.innerHTML = '<div class="p-3 text-muted small text-center">No matching students found.</div>';
            return;
        }

        filtered.slice(0, 50).forEach(st => {
            const sName = `${st.first_name || ''} ${st.last_name || ''}`.trim();
            const fName = st.father_name || st.guardian_name || (sName + ' Parent');
            const fCnic = st.father_cnic || st.guardian_cnic || '';
            const pEmail = st.guardian_email || (fCnic ? (fCnic.replace(/[^0-9]/g, '') + '.parent@school.com') : (`parent.${st.id}@school.com`));

            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action py-2 px-3 text-start small border-bottom';
            item.innerHTML = `
                <div class="fw-bold text-dark">Father: ${fName}</div>
                <div class="text-muted fs-8">
                    <span>Student: ${sName} (${st.class_name || 'Class N/A'})</span>
                    ${fCnic ? '<span class="badge bg-light text-dark border ms-1">CNIC: ' + fCnic + '</span>' : ''}
                </div>
            `;
            item.addEventListener('click', function (e) {
                e.stopPropagation();
                hiddenStudentId.value = st.id;
                parentSearchInput.value = `${fName} (Student: ${sName} - Class ${st.class_name || 'N/A'})`;
                document.querySelector('#wrapper_parent_search .searchable-clear-btn').style.display = 'inline-block';
                parentSearchInput.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'none';

                // Auto fill details
                inputName.value = fName;
                inputEmail.value = pEmail;
                if (fCnic) inputCnic.value = fCnic;
            });
            parentListContainer.appendChild(item);
        });
    }

    if (parentSearchInput) {
        parentSearchInput.addEventListener('focus', function () {
            renderParentStudentOptions();
            this.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'block';
        });
        parentSearchInput.addEventListener('input', function () {
            renderParentStudentOptions();
            this.closest('.searchable-dropdown-wrapper').querySelector('.searchable-list-container').style.display = 'block';
        });
    }

    // --- CLEAR SELECTION BUTTONS LOGIC ---
    document.querySelectorAll('.searchable-clear-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const wrapper = this.closest('.searchable-dropdown-wrapper');
            const input = wrapper.querySelector('.searchable-input');
            if (input) input.value = '';
            this.style.display = 'none';
            if (hiddenStudentId) hiddenStudentId.value = '';
            if (hiddenStaffId) hiddenStaffId.value = '';

            if (inputName) inputName.value = '';
            if (inputEmail) inputEmail.value = '';
            if (inputCnic) inputCnic.value = '';
        });
    });

    // Close dropdown list container when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.searchable-dropdown-wrapper')) {
            document.querySelectorAll('.searchable-list-container').forEach(c => c.style.display = 'none');
        }
    });
});
</script>
@endpush
