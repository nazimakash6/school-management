@extends ('layouts.app')

@section ('title', 'Student Profile - ' . $admission->first_name . ' ' . $admission->last_name)

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/admission-detail.css') }}" />
@endpush

@push ('scripts')
    <script src="{{ asset('js/admission-detail.js') }}"></script>
@endpush

@section ('content')
    <div class="admission-page-wrapper py-4 px-3 px-md-4">
        <div class="container-fluid max-width-1600">
            {{-- ==========================================
            PRINT-ONLY HEADER BANNER
        =========================================== --}}
            <div class="print-only-header d-none d-print-block">
                <div class="print-header-grid">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" class="print-logo" alt="School Logo" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
                        <div>
                            <h2 class="print-school-name">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}</h2>
                            <div class="print-school-tagline">{{ $globalSchoolInfo->tagline ?? 'DISCIPLINE • EDUCATION • EXCELLENCE' }}</div>
                            <div class="print-doc-title">OFFICIAL STUDENT PROFILE RECORD</div>
                        </div>
                    </div>
                    <div class="print-meta-box">
                        <div><strong>Adm No:</strong> {{ $admission->admission_no }}</div>
                        <div><strong>Roll No:</strong> {{ $admission->roll_no ?: '-' }}</div>
                        <div><strong>Class:</strong> {{ $admission->class_name }} (Sec {{ strtoupper($admission->section_name ?: 'A') }})</div>
                        <div><strong>Session:</strong> {{ optional($admission->academicSession)->session_name ?: ($admission->academic_session ?: '2026-2027') }}</div>
                        <div><strong>Printed Date:</strong> {{ date('d/m/Y h:i A') }}</div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
            1. PAGE HEADER CARD WITH BREADCRUMB & ACTIONS
        =========================================== --}}
            <div class="profile-header-card fade-up">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-1 fs-7">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-secondary"
                                        ><i class="bi bi-house-door"></i> Dashboard</a
                                    >
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admission.index') }}" class="text-decoration-none text-secondary"
                                        >Student Admissions</a
                                    >
                                </li>
                                <li class="breadcrumb-item active fw-medium text-primary" aria-current="page">
                                    Student Profile
                                </li>
                            </ol>
                        </nav>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <h2 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 fs-3">
                                {{ $admission->student_name }}
                            </h2>
                            @php
                            $status = strtolower($admission->admission_status);
                            $statusClass = match($status) {
                                'active' => 'status-active',
                                'pending' => 'status-pending',
                                'inactive' => 'status-inactive',
                                'rejected' => 'status-rejected',
                                default => 'status-inactive'
                            };
                        @endphp
                            <span class="status-pill {{ $statusClass }}">
                                <span class="pulse-dot"></span>
                                {{ ucfirst($admission->admission_status) }}
                            </span>

                            @if ($admission->is_confirmed)
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold fs-7 d-inline-flex align-items-center gap-1"
                                >
                                    <i class="bi bi-patch-check-fill"></i> Confirmed Admission
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <a href="{{ route('admission.index') }}" class="btn btn-modern btn-modern-light">
                            <i class="bi bi-arrow-left"></i>
                            <span>Back to List</span>
                        </a>

                        <a href="{{ route('admission.print', $admission) }}" target="_blank" class="btn btn-modern btn-modern-light">
                            <i class="bi bi-printer"></i>
                            <span>Print Application</span>
                        </a>

                        <a href="{{ route('admission.edit', $admission) }}" class="btn btn-modern btn-modern-primary">
                            <i class="bi bi-pencil-square"></i>
                            <span>Edit Admission</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ==========================================
            2. SUMMARY METRIC CARDS (4 CARDS GRID)
        =========================================== --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-blue">
                            <i data-lucide="notebook-text"></i>
                        </div>
                        <div>
                            <div class="metric-title">Admission No</div>
                            <h4 class="metric-value text-primary">{{ $admission->admission_no }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-purple">
                            <i data-lucide="notebook-text"></i>
                        </div>
                        <div>
                            <div class="metric-title">Class & Section</div>
                            <h4 class="metric-value">
                                {{ $admission->class_name }}
                                @if ($admission->section_name)
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle fs-7 rounded-pill ms-1"
                                        >Sec {{ strtoupper($admission->section_name) }}</span
                                    >
                                @endif
                                @if ($admission->group_name || $admission->group)
                                    <span
                                        class="badge bg-info-subtle text-info border border-info-subtle fs-7 rounded-pill ms-1"
                                        >{{ $admission->group_name ?: $admission->group }}</span
                                    >
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-green">
                            <i data-lucide="notebook-text"></i>
                        </div>
                        @php
                        $totFee = ($admission->monthly_fee ?? 0) + ($admission->quarterly_fee ?? 0) + ($admission->annual_fee ?? 0) + ($admission->registration_fee ?? 0);
                        $disc = $admission->scholarship_discount ?? 0;
                        $netFee = max($totFee - $disc, 0);
                    @endphp
                        <div>
                            <div class="metric-title">Fee Plan / Net</div>
                            <h4 class="metric-value text-success">
                                {{ $admission->fee_plan ?: 'Standard' }}
                                <small class="fs-7 fw-normal text-muted ms-1">(Rs. {{ number_format($netFee) }})</small>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-amber">
                            <i data-lucide="notebook-text"></i>
                        </div>
                        <div>
                            <div class="metric-title">Admission Date</div>
                            <h4 class="metric-value">
                                {{ optional($admission->admission_date)->format('d M, Y') ?: 'N/A' }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
            3. MAIN CONTENT GRID (LEFT SIDEBAR + RIGHT TABS)
        =========================================== --}}
            <div class="row g-4">
                {{-- ---------------------------------------
                LEFT COLUMN: PROFILE SIDEBAR
            ---------------------------------------- --}}
                <div class="col-lg-3">
                    <div class="sticky-profile-sidebar">
                        <div class="student-main-card">
                            <div class="profile-avatar-header">
                                <div class="avatar-wrapper">
                                    @if ($admission->student_photo)
                                        <img
                                            src="{{ asset('storage/'.$admission->student_photo) }}"
                                            alt="Student Avatar"
                                            class="avatar-img"
                                        />
                                    @else
                                        <div class="avatar-placeholder-gradient">
                                            {{ strtoupper(substr($admission->first_name, 0, 1)) }}{{ strtoupper(substr($admission->last_name ?: 'S', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="avatar-online-badge" title="Active Record"></span>
                                </div>

                                <h3 class="student-name">{{ $admission->first_name }} {{ $admission->last_name }}</h3>
                                <div class="student-sub-info">
                                    <span
                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1 fw-semibold me-1"
                                    >
                                        Roll #{{ $admission->roll_no ?: 'Unassigned' }}
                                    </span>
                                </div>

                                {{-- Quick Contact Actions --}}
                                <div class="quick-contact-actions">
                                    @if ($admission->student_mobile_no || $admission->guardian_primary_mobile_no)
                                        <a
                                            href="tel:{{ $admission->student_mobile_no ?: $admission->guardian_primary_mobile_no }}"
                                            class="quick-action-btn"
                                            title="Call Contact"
                                        >
                                            <i class="bi bi-telephone-fill"></i>
                                        </a>
                                    @endif
                                    @if ($admission->student_email || $admission->guardian_email)
                                        <a
                                            href="mailto:{{ $admission->student_email ?: $admission->guardian_email }}"
                                            class="quick-action-btn"
                                            title="Send Email"
                                        >
                                            <i class="bi bi-envelope-fill"></i>
                                        </a>
                                    @endif
                                    <a
                                        href="{{ route('admission.edit', $admission) }}"
                                        class="quick-action-btn"
                                        title="Edit Profile"
                                    >
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- Metadata List --}}
                            <div class="profile-meta-list">
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-qr-code"></i> Admission #</span>
                                    <span class="meta-value">{{ $admission->admission_no }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-journal-bookmark-fill"></i> Class</span>
                                    <span class="meta-value">{{ $admission->class_name }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-diagram-3-fill"></i> Section</span>
                                    <span class="meta-value">{{ strtoupper($admission->section_name ?: 'N/A') }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-hash"></i> Roll No</span>
                                    <span class="meta-value">{{ $admission->roll_no ?: '-' }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-gender-ambiguous"></i> Gender</span>
                                    <span class="meta-value">{{ ucfirst($admission->gender) }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-cake2-fill"></i> Age</span>
                                    <span class="meta-value">
                                        @if ($admission->date_of_birth)
                                            {{ \Carbon\Carbon::parse($admission->date_of_birth)->age }} Yrs
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"
                                        ><i class="bi bi-droplet-fill text-danger"></i> Blood Group</span
                                    >
                                    <span class="meta-value text-danger">{{ $admission->blood_group ?: '-' }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-globe-americas"></i> Religion</span>
                                    <span class="meta-value">{{ $admission->religion ?: '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ---------------------------------------
                RIGHT COLUMN: TAB NAVIGATION & PANELS
            ---------------------------------------- --}}
                <div class="col-lg-9">
                    {{-- Modern Tab Navigation Bar --}}
                    <div class="modern-tabs-card">
                        <ul class="nav nav-pills nav-pills-custom nav-fill" id="admissionTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link active"
                                    id="overview-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#overviewPane"
                                    type="button"
                                    role="tab"
                                >
                                    <i class="bi bi-person-vcard"></i>
                                    <span>Overview</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="academic-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#academicPane"
                                    type="button"
                                    role="tab"
                                >
                                    <i class="bi bi-mortarboard"></i>
                                    <span>Academic & Fees</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="parent-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#parentPane"
                                    type="button"
                                    role="tab"
                                >
                                    <i class="bi bi-people"></i>
                                    <span>Parents & Family</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                @php
                                $docList = [$admission->birth_certificate, $admission->bform_cnic_copy, $admission->guardian_cnic_copy, $admission->school_leaving_certificate];
                                $submittedCount = collect($docList)->filter(fn($d) => $d === 'submitted')->count();
                            @endphp
                                <button
                                    class="nav-link"
                                    id="documents-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#documentsPane"
                                    type="button"
                                    role="tab"
                                >
                                    <i class="bi bi-folder-check"></i>
                                    <span>Documents</span>
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-1 fs-8"
                                        >{{ $submittedCount }}/4</span
                                    >
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="timeline-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#timelinePane"
                                    type="button"
                                    role="tab"
                                >
                                    <i class="bi bi-clock-history"></i>
                                    <span>Timeline</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Panels Content --}}
                    <div class="tab-content" id="admissionTabContent">
                        {{-- =========================================
                        PANEL 1: OVERVIEW
                    ========================================== --}}
                        <div class="tab-pane fade show active" id="overviewPane" role="tabpanel">
                            {{-- Personal Information Grid --}}
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="section-title-group">
                                        <div class="section-title-icon">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <h5 class="section-title">Personal Details</h5>
                                    </div>
                                    <span class="badge bg-light text-secondary border">Student Demographics</span>
                                </div>
                                <div class="section-card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-person"></i> First Name</div>
                                                <div class="tile-value">{{ $admission->first_name }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-person"></i> Last Name</div>
                                                <div class="tile-value">{{ $admission->last_name ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-card-text"></i> CNIC / B-Form
                                                </div>
                                                <div class="tile-value">{{ $admission->cnic_bform ?: '-' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-calendar-check"></i> Date of Birth
                                                </div>
                                                <div class="tile-value">
                                                    {{ optional($admission->date_of_birth)->format('d M, Y') ?: '-' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-flag"></i> Nationality</div>
                                                <div class="tile-value">
                                                    {{ $admission->nationality ?: 'Pakistani' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-telephone"></i> Student Mobile
                                                </div>
                                                <div class="tile-value">{{ $admission->student_mobile_no ?: '-' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-envelope"></i> Student Email
                                                </div>
                                                <div class="tile-value">{{ $admission->student_email ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-book"></i> Religion</div>
                                                <div class="tile-value">{{ $admission->religion ?: '-' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Previous School Callout --}}
                                    <div class="mt-4">
                                        <div class="callout-box">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="bi bi-building text-primary fs-5"></i>
                                                <span class="fw-bold text-dark fs-7 text-uppercase"
                                                    >Previous School History</span
                                                >
                                            </div>
                                            <div class="text-secondary fs-6">
                                                {{ $admission->previous_school ?: 'No previous school information recorded for this student.' }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Medical Notes Callout --}}
                                    @if ($admission->student_medical_notes)
                                        <div class="mt-3">
                                            <div class="callout-box callout-box-warning">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <i class="bi bi-heart-pulse-fill text-warning fs-5"></i>
                                                    <span class="fw-bold text-dark fs-7 text-uppercase"
                                                        >Medical & Special Conditions</span
                                                    >
                                                </div>
                                                <div class="text-dark fs-6 fw-medium">
                                                    {{ $admission->student_medical_notes }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- =========================================
                        PANEL 2: ACADEMIC & FEES
                    ========================================== --}}
                        <div class="tab-pane fade" id="academicPane" role="tabpanel">
                            {{-- Academic Record Card --}}
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="section-title-group">
                                        <div class="section-title-icon">
                                            <i class="bi bi-mortarboard-fill"></i>
                                        </div>
                                        <h5 class="section-title">Academic Enrollment Details</h5>
                                    </div>
                                </div>
                                <div class="section-card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-calendar3"></i> Academic Session
                                                </div>
                                                <div class="tile-value text-primary">
                                                    {{ optional($admission->academicSession)->session_name ?: ($admission->academic_session ?: 'Current Session') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-building"></i> Class Name</div>
                                                <div class="tile-value">{{ $admission->class_name }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-diagram-2"></i> Section</div>
                                                <div class="tile-value">
                                                    {{ strtoupper($admission->section_name ?: 'N/A') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-hash"></i> Roll Number</div>
                                                <div class="tile-value">{{ $admission->roll_no ?: 'Unassigned' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-sun"></i> Class Shift</div>
                                                <div class="tile-value">
                                                    {{ ucfirst($admission->class_shift ?: 'Morning') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-folder-symlink"></i> Academic Group</div>
                                                <div class="tile-value">
                                                    @if($admission->group_name || $admission->group)
                                                        <span class="badge bg-info-subtle text-info border border-info-subtle fs-7 rounded-pill px-2 py-1">
                                                            {{ $admission->group_name ?: $admission->group }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-person-check"></i> Admission Type
                                                </div>
                                                <div class="tile-value">
                                                    {{ ucwords(str_replace('_', ' ', $admission->admission_type ?: 'Regular')) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($admission->academic_notes)
                                        <div class="mt-3">
                                            <div class="callout-box callout-box-info">
                                                <div class="fw-bold text-dark mb-1 fs-7 text-uppercase">
                                                    <i class="bi bi-info-circle me-1"></i> Academic Remarks
                                                </div>
                                                <div class="text-dark">{{ $admission->academic_notes }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Fee Summary & Dashboard --}}
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="section-card h-100 mb-0">
                                        <div class="section-card-header">
                                            <div class="section-title-group">
                                                <div class="section-title-icon text-success bg-success-subtle">
                                                    <i class="bi bi-receipt"></i>
                                                </div>
                                                <h5 class="section-title">Fee Breakdown Table</h5>
                                            </div>
                                        </div>
                                        <div class="section-card-body">
                                            <table class="fee-summary-table">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-secondary">Fee Status</td>
                                                        <td class="text-end fw-bold">
                                                            @php
                                                                $fs = strtolower($admission->fee_status ?? 'pending');
                                                                $fsBadge = match($fs) {
                                                                    'paid' => 'bg-success',
                                                                    'partial' => 'bg-info text-dark',
                                                                    default => 'bg-warning text-dark',
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $fsBadge }}">
                                                                {{ ucfirst($fs) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Fee Plan Type</td>
                                                        <td class="text-end fw-bold text-dark">
                                                            {{ $admission->fee_plan ?: 'Monthly' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Monthly Fee</td>
                                                        <td class="text-end fw-bold">
                                                            Rs. {{ number_format($admission->monthly_fee ?? 0) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Quarterly Fee</td>
                                                        <td class="text-end fw-bold">
                                                            Rs. {{ number_format($admission->quarterly_fee ?? 0) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Annual Fee</td>
                                                        <td class="text-end fw-bold">
                                                            Rs. {{ number_format($admission->annual_fee ?? 0) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Registration Fee</td>
                                                        <td class="text-end fw-bold">
                                                            Rs. {{ number_format($admission->registration_fee ?? 0) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Scholarship Discount</td>
                                                        <td class="text-end fw-bold text-success">
                                                            -Rs. {{ number_format($admission->scholarship_discount ?? 0) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="net-payable-card h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge bg-primary text-white px-3 py-1 rounded-pill fs-7"
                                                    >Financial Summary</span
                                                >
                                                <i class="bi bi-graph-up-arrow fs-4 text-white-50"></i>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-white-50 text-uppercase fw-semibold tracking-wider"
                                                    >Gross Total Fee</small
                                                >
                                                <h3 class="fw-bold text-white mb-0">Rs. {{ number_format($totFee) }}</h3>
                                            </div>
                                            <div class="mb-4">
                                                <small class="text-white-50 text-uppercase fw-semibold tracking-wider"
                                                    >Applied Scholarship</small
                                                >
                                                <h4 class="fw-bold text-emerald-400 text-success mb-0">
                                                    -Rs. {{ number_format($disc) }}
                                                </h4>
                                            </div>
                                        </div>

                                        <div>
                                            <hr class="border-white-15 my-3" />
                                            <div class="d-flex justify-content-between align-items-end">
                                                <div>
                                                    <small
                                                        class="text-white-50 text-uppercase fw-semibold tracking-wider"
                                                        >Net Amount Payable</small
                                                    >
                                                    <h2 class="fw-bold text-white mb-0 fs-1">
                                                        Rs. {{ number_format($netFee) }}
                                                    </h2>
                                                </div>
                                                <div class="text-end">
                                                    <span
                                                        class="badge bg-success border border-success-subtle px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-check-circle me-1"></i> Active Plan</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- =========================================
                        PANEL 3: PARENTS & FAMILY
                    ========================================== --}}
                        <div class="tab-pane fade" id="parentPane" role="tabpanel">
                            {{-- Guardian Info Card --}}
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="section-title-group">
                                        <div class="section-title-icon">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <h5 class="section-title">Parent & Guardian Information</h5>
                                    </div>
                                </div>
                                <div class="section-card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-person"></i> Father Name</div>
                                                <div class="tile-value">{{ $admission->father_name ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-card-text"></i> Father CNIC</div>
                                                <div class="tile-value">{{ $admission->father_cnic ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-telephone"></i> Father Phone</div>
                                                <div class="tile-value">{{ $admission->father_phone ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-briefcase"></i> Father Occupation</div>
                                                <div class="tile-value">{{ $admission->father_occupation ?: ($admission->guardian_occupation ?: '-') }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-person-heart"></i> Mother Name</div>
                                                <div class="tile-value">{{ $admission->mother_name ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-card-text"></i> Mother CNIC</div>
                                                <div class="tile-value">{{ $admission->mother_cnic ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-telephone"></i> Mother Phone</div>
                                                <div class="tile-value">{{ $admission->mother_phone ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-briefcase"></i> Mother Occupation</div>
                                                <div class="tile-value">{{ $admission->mother_occupation ?: '-' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-shield-person"></i> Guardian Name
                                                </div>
                                                <div class="tile-value">{{ $admission->guardian_name ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-diagram-2"></i> Relation</div>
                                                <div class="tile-value">
                                                    {{ ucfirst($admission->guardian_relation ?: '-') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-card-text"></i> Guardian CNIC
                                                </div>
                                                <div class="tile-value">{{ $admission->guardian_cnic ?: '-' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-telephone-fill text-primary"></i> Primary Mobile
                                                </div>
                                                <div class="tile-value">
                                                    {{ $admission->guardian_primary_mobile_no ?: '-' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-telephone"></i> Secondary Mobile
                                                </div>
                                                <div class="tile-value">
                                                    {{ $admission->guardian_secondary_mobile_no ?: '-' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label">
                                                    <i class="bi bi-envelope"></i> Guardian Email
                                                </div>
                                                <div class="tile-value">{{ $admission->guardian_email ?: '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Residential Address Split --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="section-card h-100 mb-0">
                                        <div class="section-card-header">
                                            <div class="section-title-group">
                                                <div class="section-title-icon text-danger bg-danger-subtle">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                </div>
                                                <h5 class="section-title">Current Address</h5>
                                            </div>
                                        </div>
                                        <div class="section-card-body">
                                            <div class="p-3 bg-light rounded-3 border text-dark fs-6">
                                                {{ $admission->current_address ?: ($admission->guardian_address ?: 'No current address recorded.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="section-card h-100 mb-0">
                                        <div class="section-card-header">
                                            <div class="section-title-group">
                                                <div class="section-title-icon text-info bg-info-subtle">
                                                    <i class="bi bi-house-door-fill"></i>
                                                </div>
                                                <h5 class="section-title">Permanent Address</h5>
                                            </div>
                                        </div>
                                        <div class="section-card-body">
                                            <div class="p-3 bg-light rounded-3 border text-dark fs-6">
                                                {{ $admission->permanent_address ?: ($admission->guardian_address ?: 'No permanent address recorded.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Transport & Emergency --}}
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="section-card h-100 mb-0">
                                        <div class="section-card-header">
                                            <div class="section-title-group">
                                                <div class="section-title-icon text-success bg-success-subtle">
                                                    <i class="bi bi-bus-front-fill"></i>
                                                </div>
                                                <h5 class="section-title">Transport & Hostel Facility</h5>
                                            </div>
                                        </div>
                                        <div class="section-card-body">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom"
                                            >
                                                <span class="text-secondary">Transport Required</span>
                                                <span
                                                    class="fw-bold text-dark"
                                                    >{{ ucfirst($admission->transportation_required ?: 'No') }}</span
                                                >
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-secondary">Hostel Facility</span>
                                                <span
                                                    class="fw-bold text-dark"
                                                    >{{ ucfirst($admission->hostel ?: 'No') }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="section-card h-100 mb-0">
                                        <div class="section-card-header">
                                            <div class="section-title-group">
                                                <div class="section-title-icon text-danger bg-danger-subtle">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                </div>
                                                <h5 class="section-title">Emergency Contact</h5>
                                            </div>
                                        </div>
                                        <div class="section-card-body">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                                            >
                                                <span class="text-secondary">Contact Name</span>
                                                <span
                                                    class="fw-bold text-dark"
                                                    >{{ $admission->emergency_contact_name ?: 'N/A' }}</span
                                                >
                                            </div>
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"
                                            >
                                                <span class="text-secondary">Relation</span>
                                                <span
                                                    class="fw-bold text-dark"
                                                    >{{ $admission->emergency_contact_relation ?: 'N/A' }}</span
                                                >
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-secondary">Mobile No</span>
                                                <span
                                                    class="fw-bold text-primary"
                                                    >{{ $admission->emergency_contact_mobile_no ?: 'N/A' }}</span
                                                >
                                            </div>
                                        </div>
                                </div>
                            </div>

                            {{-- Linked Siblings in School --}}
                            @php
                                $siblingsList = $admission->siblings;
                            @endphp
                            <div class="section-card mt-4">
                                <div class="section-card-header d-flex align-items-center justify-content-between">
                                    <div class="section-title-group">
                                        <div class="section-title-icon text-primary bg-primary-subtle">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <div>
                                            <h5 class="section-title mb-0">Linked Siblings in School (Brothers / Sisters)</h5>
                                            <small class="text-muted fs-7">Students from the same family studying in this institution</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill px-3 py-1.5 fs-7">
                                        {{ $siblingsList->count() }} {{ Str::plural('Sibling', $siblingsList->count()) }}
                                    </span>
                                </div>
                                <div class="section-card-body p-0">
                                    @if($siblingsList->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0 fs-7">
                                                <thead class="bg-light-subtle">
                                                    <tr>
                                                        <th class="ps-3 py-2 text-muted fw-semibold">Sibling Name</th>
                                                        <th class="py-2 text-muted fw-semibold">Class / Section</th>
                                                        <th class="py-2 text-muted fw-semibold">Roll No</th>
                                                        <th class="py-2 text-muted fw-semibold">Relation</th>
                                                        <th class="py-2 text-muted fw-semibold">Father / Guardian</th>
                                                        <th class="pe-3 py-2 text-end fw-semibold">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($siblingsList as $sibling)
                                                        <tr>
                                                            <td class="ps-3 fw-bold text-dark">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                                        {{ strtoupper(substr($sibling->first_name, 0, 1)) }}
                                                                    </div>
                                                                    <div>
                                                                        <span>{{ $sibling->full_name }}</span>
                                                                        <br>
                                                                        <small class="text-muted fs-8">Adm #: {{ $sibling->admission_no }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                                                    {{ $sibling->class_name ?: 'N/A' }} {{ $sibling->section_name ? '('.strtoupper($sibling->section_name).')' : '' }}
                                                                </span>
                                                            </td>
                                                            <td class="fw-semibold text-secondary">#{{ $sibling->roll_no ?: '-' }}</td>
                                                            <td>
                                                                @if(strtolower($sibling->gender) === 'male')
                                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill"><i class="bi bi-gender-male me-1"></i>Brother</span>
                                                                @elseif(strtolower($sibling->gender) === 'female')
                                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill"><i class="bi bi-gender-female me-1"></i>Sister</span>
                                                                @else
                                                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill">{{ ucfirst($sibling->gender ?: 'Sibling') }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-muted">{{ $sibling->father_name ?: ($sibling->guardian_name ?: '-') }}</td>
                                                            <td class="pe-3 text-end">
                                                                <a href="{{ route('student-list.show', $sibling->id) }}" class="btn btn-outline-primary btn-sm px-2 py-1 fs-8 rounded-pill" title="View Sibling Profile">
                                                                    <i class="bi bi-eye me-1"></i> View Profile
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-4 text-center text-muted">
                                            <i class="bi bi-people fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                            <p class="mb-0 fs-7">No siblings currently attached or found for this student.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- =========================================
                        PANEL 4: DOCUMENTS
                    ========================================== --}}
                        <div class="tab-pane fade" id="documentsPane" role="tabpanel">
                            {{-- Document Progress Card --}}
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="section-title-group">
                                        <div class="section-title-icon text-success bg-success-subtle">
                                            <i class="bi bi-check2-all"></i>
                                        </div>
                                        <h5 class="section-title">Required Verification Documents</h5>
                                    </div>
                                    @php
                                    $docPercent = ($submittedCount / 4) * 100;
                                @endphp
                                    <span
                                        class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold fs-7"
                                    >
                                        {{ number_format($docPercent) }}% Completed
                                    </span>
                                </div>
                                <div class="section-card-body">
                                    <div class="progress mb-4" style="height: 10px; border-radius: 20px">
                                        <div
                                            class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                            role="progressbar"
                                            style="width: {{ $docPercent }}%"
                                        ></div>
                                    </div>

                                    <div class="row g-3">
                                        {{-- Document Item 1 --}}
                                        <div class="col-md-6">
                                            <div class="doc-checklist-item">
                                                <div class="doc-name">
                                                    <i class="bi bi-file-earmark-person fs-5 text-primary"></i>
                                                    <span>Birth Certificate</span>
                                                </div>
                                                @if ($admission->birth_certificate == 'submitted')
                                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-check-circle-fill me-1"></i> Submitted</span
                                                    >
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-x-circle me-1"></i> Pending</span
                                                    >
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Document Item 2 --}}
                                        <div class="col-md-6">
                                            <div class="doc-checklist-item">
                                                <div class="doc-name">
                                                    <i class="bi bi-card-heading fs-5 text-primary"></i>
                                                    <span>B-Form / CNIC Copy</span>
                                                </div>
                                                @if ($admission->bform_cnic_copy == 'submitted')
                                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-check-circle-fill me-1"></i> Submitted</span
                                                    >
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-x-circle me-1"></i> Pending</span
                                                    >
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Document Item 3 --}}
                                        <div class="col-md-6">
                                            <div class="doc-checklist-item">
                                                <div class="doc-name">
                                                    <i class="bi bi-person-vcard fs-5 text-primary"></i>
                                                    <span>Guardian CNIC Copy</span>
                                                </div>
                                                @if ($admission->guardian_cnic_copy == 'submitted')
                                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-check-circle-fill me-1"></i> Submitted</span
                                                    >
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-x-circle me-1"></i> Pending</span
                                                    >
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Document Item 4 --}}
                                        <div class="col-md-6">
                                            <div class="doc-checklist-item">
                                                <div class="doc-name">
                                                    <i class="bi bi-award fs-5 text-primary"></i>
                                                    <span>School Leaving Certificate</span>
                                                </div>
                                                @if ($admission->school_leaving_certificate == 'submitted')
                                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-check-circle-fill me-1"></i> Submitted</span
                                                    >
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"
                                                        ><i class="bi bi-x-circle me-1"></i> Pending</span
                                                    >
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Additional Uploaded Attachments --}}
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="section-title-group">
                                        <div class="section-title-icon">
                                            <i class="bi bi-paperclip"></i>
                                        </div>
                                        <h5 class="section-title">Uploaded File Attachments</h5>
                                    </div>
                                </div>
                                <div class="section-card-body">
                                    @php
                                    $attachments = [];
                                    if (!empty($admission->attached_documents)) {
                                        $attachments = is_array($admission->attached_documents)
                                            ? $admission->attached_documents
                                            : (json_decode($admission->attached_documents, true) ?: []);
                                    }
                                @endphp

                                    @if (count($attachments))
                                        <div class="list-group">
                                            @foreach ($attachments as $file)
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center py-3"
                                                >
                                                    <div class="d-flex align-items-center gap-3">
                                                        <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                                                        <div>
                                                            <div class="fw-semibold text-dark">
                                                                {{ basename($file) }}
                                                            </div>
                                                            <small class="text-muted">Attached Document</small>
                                                        </div>
                                                    </div>
                                                    <a
                                                        href="{{ asset('storage/'.$file) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                    >
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> View Document
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="bi bi-folder-x fs-1 opacity-50 d-block mb-2"></i>
                                            <span>No additional file attachments uploaded for this admission.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- =========================================
                        PANEL 5: TIMELINE
                    ========================================== --}}
                        <div class="tab-pane fade" id="timelinePane" role="tabpanel">
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="section-title-group">
                                        <div class="section-title-icon">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                        <h5 class="section-title">Admission Audit & History Log</h5>
                                    </div>
                                </div>
                                <div class="section-card-body">
                                    <div class="timeline-modern">
                                        {{-- Step 1: Admission Created --}}
                                        <div class="timeline-node">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content-card">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="fw-bold text-dark mb-0">Admission Registered</h6>
                                                    <small
                                                        class="text-muted"
                                                        >{{ $admission->created_at?->format('d M, Y h:i A') ?: 'N/A' }}</small
                                                    >
                                                </div>
                                                <p class="text-secondary fs-7 mb-0">
                                                    Admission record created with Admission No:
                                                    <strong class="text-primary">{{ $admission->admission_no }}</strong>
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Step 2: Admission Date --}}
                                        <div class="timeline-node">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content-card">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="fw-bold text-dark mb-0">Official Admission Date</h6>
                                                    <small
                                                        class="text-muted"
                                                        >{{ optional($admission->admission_date)->format('d M, Y') ?: 'N/A' }}</small
                                                    >
                                                </div>
                                                <p class="text-secondary fs-7 mb-0">Class: <strong>{{ $admission->class_name }}</strong> (Section: {{ strtoupper($admission->section_name ?: 'A') }})</p>
                                            </div>
                                        </div>

                                        {{-- Step 3: Last Modification --}}
                                        <div class="timeline-node">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content-card">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="fw-bold text-dark mb-0">Last Profile Update</h6>
                                                    <small
                                                        class="text-muted"
                                                        >{{ $admission->updated_at?->format('d M, Y h:i A') ?: 'N/A' }}</small
                                                    >
                                                </div>
                                                <p class="text-secondary fs-7 mb-0">
                                                    Current Admission Status:
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary px-2 py-1 fs-8"
                                                        >{{ ucfirst($admission->admission_status) }}</span
                                                    >
                                                </p>
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
            4. BOTTOM FLOATING ACTION BAR
        =========================================== --}}
            <div class="bottom-action-bar d-flex justify-content-between align-items-center flex-wrap gap-3 fade-up">
                <div class="d-flex align-items-center gap-2 text-secondary fs-7">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <span>Record ID #{{ $admission->id }} &bull; Confirmed status verified</span>
                </div>

                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <a href="{{ route('admission.index') }}" class="btn btn-modern btn-modern-light">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back</span>
                    </a>

                    <button onclick="window.print()" class="btn btn-modern btn-modern-dark">
                        <i class="bi bi-printer"></i>
                        <span>Print Profile</span>
                    </button>

                    <a href="{{ route('admission.edit', $admission) }}" class="btn btn-modern btn-modern-primary">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit Record</span>
                    </a>

                    <form
                        action="{{ route('admission.destroy', $admission) }}"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to move this admission record to trash?');"
                        class="d-inline"
                    >
                        @csrf
                        @method ('DELETE')
                        <button type="submit" class="btn btn-modern btn-modern-danger">
                            <i class="bi bi-trash"></i>
                            <span>Delete Record</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- ==========================================
            PRINT-ONLY FOOTER SIGNATURES
        =========================================== --}}
            <div class="print-only-footer d-none d-print-block">
                <div class="row text-center mt-3 pt-2">
                    <div class="col-4">
                        <div class="print-sig-line"></div>
                        <div class="print-sig-title">Parent / Guardian Signature</div>
                    </div>
                    <div class="col-4">
                        <div class="print-sig-line"></div>
                        <div class="print-sig-title">Admission Incharge Signature</div>
                    </div>
                    <div class="col-4">
                        <div class="print-sig-line"></div>
                        <div class="print-sig-title">Principal Stamp &amp; Signature</div>
                    </div>
                </div>
                <div class="print-notice-text">
                    Official Computer Generated Student Profile &bull; Record ID #{{ $admission->id }} &bull; Confirmed status verified &bull; Printed on {{ date('d M, Y h:i A') }}
                </div>
            </div>
        </div>
    </div>
@endsection
