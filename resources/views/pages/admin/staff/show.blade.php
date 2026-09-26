@extends('layouts.app')

@section('title', 'Staff Profile - ' . $staff->full_name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admission-detail.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/staff-detail.css') }}?v={{ time() }}" />
    <style>
        @media screen {
            .print-a4-document {
                display: none !important;
            }
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm 8mm;
            }
            html, body, .app-wrapper, .main-content, .page-content {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                position: static !important;
                overflow: visible !important;
                display: block !important;
            }
            .sidebar, .sidebar-overlay, .sidebar-brand, .notification-panel, .notification-overlay,
            header, footer, nav, .app-header, .app-sidebar, .topbar, .navbar, .admission-page-wrapper,
            .btn, .breadcrumb, .modern-tabs-card, .profile-header-card, .bottom-action-bar,
            .quick-contact-actions, .summary-metric-card, .alert {
                display: none !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .print-a4-document, .print-a4-document * {
                visibility: visible !important;
            }
            .print-a4-document {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                position: static !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="admission-page-wrapper py-4 px-3 px-md-4">
        <div class="container-fluid max-width-1600">
            {{-- ==========================================
                1. PAGE HEADER CARD WITH BREADCRUMB & ACTIONS
            =========================================== --}}
            <div class="profile-header-card fade-up mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1 fs-7">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-secondary">
                                        <i data-lucide="home" style="width:0.875rem;height:0.875rem;"></i> Dashboard
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('staff.index') }}" class="text-decoration-none text-secondary">Staff Management</a>
                                </li>
                                <li class="breadcrumb-item active fw-medium text-primary" aria-current="page">
                                    Staff Profile
                                </li>
                            </ol>
                        </nav>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <h2 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 fs-3">
                                {{ $staff->full_name }}
                            </h2>
                            @php
                                $status = strtolower($staff->status);
                                $statusClass = match($status) {
                                    'active' => 'status-active',
                                    'inactive' => 'status-inactive',
                                    default => 'status-inactive'
                                };
                            @endphp
                            <span class="status-pill {{ $statusClass }}">
                                <span class="pulse-dot"></span>
                                {{ ucfirst($staff->status) }}
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-1 fw-semibold fs-7">
                                {{ $staff->formatted_department }} • {{ $staff->formatted_designation }}
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <a href="{{ route('staff.index') }}" class="btn btn-modern btn-modern-light">
                            <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i>
                            <span>Back to List</span>
                        </a>

                        <a href="{{ route('staff-advances.create', ['staff_id' => $staff->id]) }}" class="btn btn-modern btn-modern-light">
                            <i data-lucide="coins" style="width:1rem;height:1rem;"></i>
                            <span>Issue Advance</span>
                        </a>

                        <a href="{{ route('staff.print', $staff) }}" target="_blank" class="btn btn-modern btn-modern-light">
                            <i data-lucide="printer" style="width:1rem;height:1rem;"></i>
                            <span>Print Profile</span>
                        </a>

                        <a href="{{ route('staff.edit', $staff) }}" class="btn btn-modern btn-modern-primary">
                            <i data-lucide="edit-3" style="width:1rem;height:1rem;"></i>
                            <span>Edit Staff</span>
                        </a>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ==========================================
                2. SUMMARY METRIC CARDS (4 CARDS GRID)
            =========================================== --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-blue">
                            <i data-lucide="user-check"></i>
                        </div>
                        <div>
                            <div class="metric-title">Staff ID</div>
                            <h4 class="metric-value text-primary">{{ $staff->staff_id }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-purple">
                            <i data-lucide="briefcase"></i>
                        </div>
                        <div>
                            <div class="metric-title">Department / Designation</div>
                            <h4 class="metric-value fs-6">
                                {{ $staff->formatted_designation }}
                                <small class="text-tertiary d-block text-xs fw-normal">{{ $staff->formatted_department }}</small>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-green">
                            <i data-lucide="credit-card"></i>
                        </div>
                        <div>
                            <div class="metric-title">Salary Rate</div>
                            <h4 class="metric-value text-success fs-5">
                                Rs. {{ number_format((float) $staff->salary, 2) }}
                                <small class="fs-7 fw-normal text-muted ms-1">({{ ucfirst($staff->salary_type ?: 'Monthly') }})</small>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-amber">
                            <i data-lucide="calendar"></i>
                        </div>
                        <div>
                            <div class="metric-title">Joining Date</div>
                            <h4 class="metric-value fs-6">
                                {{ $staff->joining_date ? date('d M, Y', strtotime($staff->joining_date)) : 'N/A' }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                3. MAIN CONTENT GRID (LEFT SIDEBAR + RIGHT TABS)
            =========================================== --}}
            <div class="row g-4">
                {{-- LEFT COLUMN: PROFILE SIDEBAR --}}
                <div class="col-lg-3">
                    <div class="sticky-profile-sidebar">
                        <div class="student-main-card">
                            <div class="profile-avatar-header">
                                <div class="avatar-wrapper mx-auto mb-3" style="width: 110px; height: 110px; border-radius: 50%; overflow: hidden; border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                    @if ($staff->profile_picture)
                                        <img src="{{ asset('storage/' . $staff->profile_picture) }}" alt="{{ $staff->full_name }}" class="w-100 h-100 object-fit-cover" />
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($staff->full_name) }}&background=6366f1&color=fff&size=128" alt="{{ $staff->full_name }}" class="w-100 h-100 object-fit-cover" />
                                    @endif
                                </div>

                                <h3 class="student-name text-center fw-bold fs-5 mb-1">{{ $staff->full_name }}</h3>
                                <p class="text-tertiary text-center mb-2 fs-7">{{ $staff->formatted_designation }}</p>

                                {{-- Quick Contact Actions --}}
                                <div class="quick-contact-actions d-flex justify-content-center gap-2 mb-3">
                                    @if ($staff->mobile_no)
                                        <a href="tel:{{ $staff->mobile_no }}" class="btn btn-light btn-icon-sm rounded-circle border" title="Call Mobile">
                                            <i data-lucide="phone" style="width:0.875rem;height:0.875rem;"></i>
                                        </a>
                                    @endif
                                    @if ($staff->email)
                                        <a href="mailto:{{ $staff->email }}" class="btn btn-light btn-icon-sm rounded-circle border" title="Send Email">
                                            <i data-lucide="mail" style="width:0.875rem;height:0.875rem;"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('staff.edit', $staff) }}" class="btn btn-light btn-icon-sm rounded-circle border" title="Edit Staff">
                                        <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- Metadata List --}}
                            <div class="profile-meta-list border-top pt-3">
                                <div class="meta-item d-flex justify-content-between py-2 border-bottom text-sm">
                                    <span class="meta-label text-tertiary">Staff ID</span>
                                    <span class="meta-value fw-bold">{{ $staff->staff_id }}</span>
                                </div>
                                <div class="meta-item d-flex justify-content-between py-2 border-bottom text-sm">
                                    <span class="meta-label text-tertiary">Department</span>
                                    <span class="meta-value">{{ $staff->formatted_department }}</span>
                                </div>
                                <div class="meta-item d-flex justify-content-between py-2 border-bottom text-sm">
                                    <span class="meta-label text-tertiary">Designation</span>
                                    <span class="meta-value">{{ $staff->formatted_designation }}</span>
                                </div>
                                <div class="meta-item d-flex justify-content-between py-2 border-bottom text-sm">
                                    <span class="meta-label text-tertiary">Gender</span>
                                    <span class="meta-value text-capitalize">{{ $staff->gender }}</span>
                                </div>
                                <div class="meta-item d-flex justify-content-between py-2 border-bottom text-sm">
                                    <span class="meta-label text-tertiary">Shift</span>
                                    <span class="meta-value">{{ $staff->shift ?: '—' }}</span>
                                </div>
                                <div class="meta-item d-flex justify-content-between py-2 border-bottom text-sm">
                                    <span class="meta-label text-tertiary">Employment</span>
                                    <span class="meta-value">{{ str_replace('_', ' ', ucfirst($staff->employment_type)) }}</span>
                                </div>
                                <div class="meta-item d-flex justify-content-between py-2 text-sm">
                                    <span class="meta-label text-tertiary">CNIC</span>
                                    <span class="meta-value font-monospace">{{ $staff->cnic }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: TAB NAVIGATION & PANELS --}}
                <div class="col-lg-9">
                    {{-- Tab Navigation Bar --}}
                    <div class="modern-tabs-card mb-4">
                        <ul class="nav nav-pills nav-pills-custom nav-fill" id="staffTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="personal-tab" data-bs-toggle="pill" data-bs-target="#personalPane" type="button" role="tab">
                                    <i data-lucide="user" style="width:1rem;height:1rem;" class="me-1"></i>
                                    <span>Personal Details</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="pill" data-bs-target="#contactPane" type="button" role="tab">
                                    <i data-lucide="phone-call" style="width:1rem;height:1rem;" class="me-1"></i>
                                    <span>Contact & Address</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="employment-tab" data-bs-toggle="pill" data-bs-target="#employmentPane" type="button" role="tab">
                                    <i data-lucide="briefcase" style="width:1rem;height:1rem;" class="me-1"></i>
                                    <span>Employment Info</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payroll-tab" data-bs-toggle="pill" data-bs-target="#payrollPane" type="button" role="tab">
                                    <i data-lucide="wallet" style="width:1rem;height:1rem;" class="me-1"></i>
                                    <span>Payroll & Vehicle</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="documents-tab" data-bs-toggle="pill" data-bs-target="#documentsPane" type="button" role="tab">
                                    <i data-lucide="file-text" style="width:1rem;height:1rem;" class="me-1"></i>
                                    <span>Documents & Notes</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Panels Content --}}
                    <div class="tab-content" id="staffTabContent">
                        {{-- PANEL 1: PERSONAL DETAILS --}}
                        <div class="tab-pane fade show active" id="personalPane" role="tabpanel">
                            <div class="section-card">
                                <div class="section-card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-title mb-0 fw-bold">Personal Details</h5>
                                    <span class="badge bg-light text-secondary border">Identification & Demographics</span>
                                </div>
                                <div class="section-card-body pt-3">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">First Name</div>
                                                <div class="tile-value fw-bold fs-6">{{ $staff->first_name }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Last Name</div>
                                                <div class="tile-value fw-bold fs-6">{{ $staff->last_name ?: '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Gender</div>
                                                <div class="tile-value text-capitalize fw-medium">{{ $staff->gender }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Date of Birth</div>
                                                <div class="tile-value fw-medium">{{ $staff->dob ? date('d M, Y', strtotime($staff->dob)) : '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">CNIC Number</div>
                                                <div class="tile-value font-monospace fw-bold">{{ $staff->cnic }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Marital Status</div>
                                                <div class="tile-value text-capitalize fw-medium">{{ $staff->marital_status }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Blood Group</div>
                                                <div class="tile-value text-danger fw-bold">{{ $staff->blood_group ?: '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Religion</div>
                                                <div class="tile-value fw-medium">{{ $staff->religion ?: '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Nationality</div>
                                                <div class="tile-value fw-medium">{{ $staff->nationality ?: '—' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PANEL 2: CONTACT & ADDRESS --}}
                        <div class="tab-pane fade" id="contactPane" role="tabpanel">
                            <div class="section-card mb-4">
                                <div class="section-card-header">
                                    <h5 class="section-title mb-0 fw-bold">Contact & Emergency Details</h5>
                                </div>
                                <div class="section-card-body pt-3">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Mobile Number</div>
                                                <div class="tile-value fw-bold text-primary">{{ $staff->mobile_no }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Alternate Mobile Number</div>
                                                <div class="tile-value fw-medium">{{ $staff->alternate_mobile_no ?: '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Email Address</div>
                                                <div class="tile-value fw-medium">{{ $staff->email }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Emergency Contact Name</div>
                                                <div class="tile-value fw-bold">{{ $staff->emergency_contact_name }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Emergency Contact Number</div>
                                                <div class="tile-value fw-bold text-danger">{{ $staff->emergency_contact_number }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Emergency Relation</div>
                                                <div class="tile-value fw-medium">{{ $staff->emergency_contact_relation }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body p-3">
                                                    <h6 class="fw-bold text-secondary text-xs text-uppercase mb-2">Current Address</h6>
                                                    <p class="mb-0 text-dark text-sm">{{ $staff->current_address }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body p-3">
                                                    <h6 class="fw-bold text-secondary text-xs text-uppercase mb-2">Permanent Address</h6>
                                                    <p class="mb-0 text-dark text-sm">{{ $staff->permanent_address }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PANEL 3: EMPLOYMENT DETAILS --}}
                        <div class="tab-pane fade" id="employmentPane" role="tabpanel">
                            <div class="section-card">
                                <div class="section-card-header">
                                    <h5 class="section-title mb-0 fw-bold">Employment Information</h5>
                                </div>
                                <div class="section-card-body pt-3">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Department</div>
                                                <div class="tile-value fw-bold text-primary">{{ $staff->formatted_department }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Designation</div>
                                                <div class="tile-value fw-bold">{{ $staff->formatted_designation }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Qualification</div>
                                                <div class="tile-value fw-medium">{{ $staff->qualification }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Work Experience</div>
                                                <div class="tile-value fw-medium">{{ $staff->experience ?: '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Employment Type</div>
                                                <div class="tile-value text-capitalize fw-medium">{{ str_replace('_', ' ', $staff->employment_type) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Shift</div>
                                                <div class="tile-value fw-medium">{{ $staff->shift ?: '—' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Joining Date</div>
                                                <div class="tile-value fw-bold">{{ date('d M, Y', strtotime($staff->joining_date)) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Leaving Date</div>
                                                <div class="tile-value text-danger fw-medium">{{ $staff->leaving_date ? date('d M, Y', strtotime($staff->leaving_date)) : 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Account Status</div>
                                                <div class="tile-value">
                                                    <span class="badge {{ $staff->status === 'active' ? 'badge-success' : 'badge-danger' }} text-capitalize">
                                                        {{ $staff->status }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PANEL 4: PAYROLL & VEHICLE --}}
                        <div class="tab-pane fade" id="payrollPane" role="tabpanel">
                            <div class="section-card mb-4">
                                <div class="section-card-header">
                                    <h5 class="section-title mb-0 fw-bold">Salary & Banking Information</h5>
                                </div>
                                <div class="section-card-body pt-3">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Salary Amount</div>
                                                <div class="tile-value fw-bold text-success fs-5">Rs. {{ number_format((float) $staff->salary, 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Salary Type</div>
                                                <div class="tile-value text-capitalize fw-medium">{{ $staff->salary_type ?: 'Monthly' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Bank Name</div>
                                                <div class="tile-value fw-medium">{{ $staff->bank_name ?: '—' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Account Title</div>
                                                <div class="tile-value fw-medium">{{ $staff->bank_account_title ?: '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Account Number</div>
                                                <div class="tile-value font-monospace fw-bold">{{ $staff->bank_account_number ?: '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">IBAN</div>
                                                <div class="tile-value font-monospace text-xs fw-bold">{{ $staff->iban ?: '—' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PANEL 5: DOCUMENTS & NOTES --}}
                        <div class="tab-pane fade" id="documentsPane" role="tabpanel">
                            <div class="section-card">
                                <div class="section-card-header">
                                    <h5 class="section-title mb-0 fw-bold">Documents & Remarks</h5>
                                </div>
                                <div class="section-card-body pt-3">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold text-sm mb-1">Curriculum Vitae (CV)</h6>
                                                    <span class="text-xs text-tertiary">
                                                        {{ $staff->cv ? 'Attached document available' : 'No CV uploaded' }}
                                                    </span>
                                                </div>
                                                @if ($staff->cv)
                                                    <a href="{{ asset('storage/' . $staff->cv) }}" target="_blank" class="btn btn-primary btn-sm">
                                                        <i data-lucide="download" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Download CV
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary text-white">Not Provided</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold text-sm mb-1">Profile Picture</h6>
                                                    <span class="text-xs text-tertiary">
                                                        {{ $staff->profile_picture ? 'Photo uploaded' : 'No photo uploaded' }}
                                                    </span>
                                                </div>
                                                @if ($staff->profile_picture)
                                                    <a href="{{ asset('storage/' . $staff->profile_picture) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                        <i data-lucide="eye" style="width:0.875rem;height:0.875rem;" class="me-1"></i> View Photo
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary text-white">Default Avatar</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card bg-light border-0">
                                        <div class="card-body p-3">
                                            <h6 class="fw-bold text-secondary text-xs text-uppercase mb-2">Remarks / Notes</h6>
                                            <p class="mb-0 text-dark text-sm">{{ $staff->note ?: 'No extra remarks or notes recorded.' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
        A4 SINGLE-PAGE EXECUTIVE PRINTABLE DOCUMENT
    =========================================== --}}
    <div class="print-a4-document">
        {{-- Header Banner & School Branding --}}
        <div class="print-header-banner">
            <div class="print-brand-group">
                <div class="print-logo-box">
                    <img src="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" alt="School Logo" class="print-school-logo" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
                </div>
                <div class="print-school-details">
                    <h1 class="print-school-name">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}</h1>
                    <div class="print-school-tagline">{{ $globalSchoolInfo->tagline ?? 'DISCIPLINE • EDUCATION • EXCELLENCE' }}</div>
                    <div class="print-school-contact">
                        @if(!empty($globalSchoolInfo->phone)) Phone: {{ $globalSchoolInfo->phone }} &bull; @endif
                        @if(!empty($globalSchoolInfo->email)) Email: {{ $globalSchoolInfo->email }} &bull; @endif
                        {{ $globalSchoolInfo->full_address ?? ($globalSchoolInfo->address ?? 'Main Campus, Educational Complex') }}
                    </div>
                </div>
            </div>
            <div class="print-photo-badge-wrapper">
                <div class="print-avatar-container">
                    @if ($staff->profile_picture)
                        <img src="{{ asset('storage/' . $staff->profile_picture) }}" alt="{{ $staff->full_name }}" class="print-staff-photo" />
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($staff->full_name) }}&background=1e293b&color=fff&size=150" alt="{{ $staff->full_name }}" class="print-staff-photo" />
                    @endif
                </div>
                <div class="print-status-tag {{ strtolower($staff->status) === 'active' ? 'status-tag-active' : 'status-tag-inactive' }}">
                    ● {{ strtoupper($staff->status) }}
                </div>
            </div>
        </div>

        {{-- Document Title Bar --}}
        <div class="print-doc-title-bar">
            <div class="doc-title-text">OFFICIAL STAFF PROFILE & SERVICE DOSSIER</div>
            <div class="doc-ref-text">REF: STF-{{ sprintf('%04d', $staff->id) }} &bull; PRINTED: {{ date('d-M-Y H:i A') }}</div>
        </div>

        {{-- Quick Meta Summary Strip --}}
        <div class="print-meta-summary-strip">
            <div class="summary-tile">
                <span class="summary-label">STAFF ID / CODE</span>
                <span class="summary-value text-primary font-monospace">{{ $staff->staff_id }}</span>
            </div>
            <div class="summary-tile">
                <span class="summary-label">DEPARTMENT</span>
                <span class="summary-value">{{ $staff->formatted_department }}</span>
            </div>
            <div class="summary-tile">
                <span class="summary-label">DESIGNATION</span>
                <span class="summary-value">{{ $staff->formatted_designation }}</span>
            </div>
            <div class="summary-tile">
                <span class="summary-label">JOINING DATE</span>
                <span class="summary-value">{{ $staff->joining_date ? date('d M, Y', strtotime($staff->joining_date)) : 'N/A' }}</span>
            </div>
        </div>

        {{-- 1. Personal Information --}}
        <div class="print-section-header">
            <span class="section-num">1</span>
            <span class="section-title">Personal & Identity Information</span>
        </div>
        <table class="print-grid-table">
            <tr>
                <th>Full Name</th>
                <td><strong class="text-dark">{{ $staff->full_name }}</strong></td>
                <th>Staff ID Code</th>
                <td><span class="font-monospace fw-bold text-primary">{{ $staff->staff_id }}</span></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ ucfirst($staff->gender) }}</td>
                <th>Date of Birth</th>
                <td>{{ $staff->dob ? date('d M, Y', strtotime($staff->dob)) : 'N/A' }}</td>
            </tr>
            <tr>
                <th>CNIC Number</th>
                <td><span class="font-monospace fw-bold">{{ $staff->cnic }}</span></td>
                <th>Marital Status</th>
                <td>{{ ucfirst($staff->marital_status) }}</td>
            </tr>
            <tr>
                <th>Blood Group</th>
                <td><span class="print-badge print-badge-secondary">{{ $staff->blood_group ?: 'N/A' }}</span></td>
                <th>Religion / Nationality</th>
                <td>{{ $staff->religion ?: 'Islam' }} / {{ $staff->nationality ?: 'Pakistani' }}</td>
            </tr>
        </table>

        {{-- 2. Contact & Address Information --}}
        <div class="print-section-header">
            <span class="section-num">2</span>
            <span class="section-title">Contact & Residence Details</span>
        </div>
        <table class="print-grid-table">
            <tr>
                <th>Mobile Number</th>
                <td><strong class="font-monospace">{{ $staff->mobile_no }}</strong></td>
                <th>Alternate Phone</th>
                <td>{{ $staff->alternate_mobile_no ?: 'N/A' }}</td>
            </tr>
            <tr>
                <th>Official Email</th>
                <td><span class="text-primary">{{ $staff->email }}</span></td>
                <th>Emergency Contact</th>
                <td><strong>{{ $staff->emergency_contact_name }}</strong> <small class="text-muted">({{ $staff->emergency_contact_relation }})</small></td>
            </tr>
            <tr>
                <th>Emergency Phone</th>
                <td colspan="3"><span class="font-monospace fw-bold text-danger">{{ $staff->emergency_contact_number }}</span></td>
            </tr>
            <tr>
                <th>Current Address</th>
                <td colspan="3">{{ $staff->current_address }}</td>
            </tr>
            <tr>
                <th>Permanent Address</th>
                <td colspan="3">{{ $staff->permanent_address }}</td>
            </tr>
        </table>

        {{-- 3. Employment & Professional Information --}}
        <div class="print-section-header">
            <span class="section-num">3</span>
            <span class="section-title">Employment & Academic Dossier</span>
        </div>
        <table class="print-grid-table">
            <tr>
                <th>Department</th>
                <td><strong>{{ $staff->formatted_department }}</strong></td>
                <th>Designation</th>
                <td><strong>{{ $staff->formatted_designation }}</strong></td>
            </tr>
            <tr>
                <th>Highest Qualification</th>
                <td>{{ $staff->qualification }}</td>
                <th>Total Experience</th>
                <td>{{ $staff->experience ?: 'N/A' }}</td>
            </tr>
            <tr>
                <th>Employment Type</th>
                <td><span class="print-badge print-badge-info">{{ str_replace('_', ' ', ucfirst($staff->employment_type)) }}</span></td>
                <th>Work Shift Schedule</th>
                <td>{{ $staff->shift ?: 'Morning Shift' }}</td>
            </tr>
            <tr>
                <th>Joining Date</th>
                <td>{{ $staff->joining_date ? date('d M, Y', strtotime($staff->joining_date)) : 'N/A' }}</td>
                <th>Leaving / End Date</th>
                <td>
                    @if($staff->leaving_date)
                        <span class="text-danger fw-bold">{{ date('d M, Y', strtotime($staff->leaving_date)) }}</span>
                    @else
                        <span class="text-success fw-bold">Active Service</span>
                    @endif
                </td>
            </tr>
        </table>

        {{-- 4. Payroll, Banking & Vehicle Details --}}
        <div class="print-section-header">
            <span class="section-num">4</span>
            <span class="section-title">Compensation, Banking & Transport</span>
        </div>
        <table class="print-grid-table">
            <tr>
                <th>Monthly Gross Salary</th>
                <td><strong class="text-success font-monospace fs-7">Rs. {{ number_format((float) $staff->salary, 2) }}</strong> <small class="text-muted">/ {{ ucfirst($staff->salary_type ?: 'Monthly') }}</small></td>
                <th>Bank Name</th>
                <td><strong>{{ $staff->bank_name ?: 'N/A' }}</strong></td>
            </tr>
            <tr>
                <th>Account Title</th>
                <td>{{ $staff->bank_account_title ?: 'N/A' }}</td>
                <th>Account Number</th>
                <td><span class="font-monospace fw-bold">{{ $staff->bank_account_number ?: 'N/A' }}</span></td>
            </tr>
            <tr>
                <th>IBAN Code</th>
                <td colspan="3"><span class="font-monospace text-xs">{{ $staff->iban ?: 'N/A' }}</span></td>
            </tr>
        </table>

        {{-- 5. Documents & Administrative Remarks --}}
        @if($staff->note || $staff->cv)
            <div class="print-section-header">
                <span class="section-num">5</span>
                <span class="section-title">Documents & Official Remarks</span>
            </div>
            <table class="print-grid-table">
                <tr>
                    <th>Curriculum Vitae (CV)</th>
                    <td>{{ $staff->cv ? 'Attached & Available on File' : 'Not Uploaded' }}</td>
                    <th>Profile Picture</th>
                    <td>{{ $staff->profile_picture ? 'Uploaded & Verified' : 'Default Avatar' }}</td>
                </tr>
                @if($staff->note)
                    <tr>
                        <th>Administrative Notes</th>
                        <td colspan="3" class="fst-italic text-dark">{{ $staff->note }}</td>
                    </tr>
                @endif
            </table>
        @endif

        {{-- Official Signatures Area --}}
        <div class="print-signatures-wrapper">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">Staff Member Signature</div>
                <div class="sig-subtitle">Date: _________________</div>
            </div>
            <div class="sig-block">
                <div class="sig-stamp-circle">
                    <span>OFFICIAL<br>STAMP</span>
                </div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">HR Manager / Accounts</div>
                <div class="sig-subtitle">Verification Officer</div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">Principal / Director</div>
                <div class="sig-subtitle">Authorized Signatory</div>
            </div>
        </div>

        {{-- Security Footer --}}
        <div class="print-footer-security">
            <div class="security-line">
                <span>CONFIDENTIAL &bull; EDUCORE MANAGEMENT SYSTEM</span>
                <span>VERIFIED RECORD &bull; STF-{{ $staff->staff_id }}</span>
            </div>
            <div class="timestamp-line">
                This document is an electronically generated official record printed on {{ date('F d, Y \a\t h:i A') }}. Any unauthorized alteration renders it invalid.
            </div>
        </div>
    </div>
@endsection
