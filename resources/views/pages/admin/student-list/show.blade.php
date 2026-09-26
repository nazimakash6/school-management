@extends('layouts.app')

@section('title', 'Student Profile - ' . $student->full_name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admission-detail.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/student-list-detail.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('js/admission-detail.js') }}"></script>
@endpush

@section('content')
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
                        <div><strong>Adm No:</strong> {{ $student->admission_no }}</div>
                        <div><strong>Roll No:</strong> {{ $student->roll_no ?: '-' }}</div>
                        <div><strong>Class:</strong> {{ $student->studentClass ? $student->studentClass->name : ($student->class_name ?: 'N/A') }} (Sec {{ strtoupper($student->section_name ?: 'A') }})</div>
                        <div><strong>Session:</strong> {{ $student->admission && $student->admission->academicSession ? $student->admission->academicSession->session_name : '2026-2027' }}</div>
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
                                    <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-secondary">
                                        <i class="bi bi-house-door"></i> Dashboard
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('student-list.index') }}" class="text-decoration-none text-secondary">
                                        Student List
                                    </a>
                                </li>
                                <li class="breadcrumb-item active fw-medium text-primary" aria-current="page">
                                    Student Profile
                                </li>
                            </ol>
                        </nav>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <h2 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2 fs-3">
                                {{ $student->full_name }}
                            </h2>
                            @php
                                $status = strtolower($student->status ?: 'active');
                                $statusClass = match($status) {
                                    'active' => 'status-active',
                                    'pending' => 'status-pending',
                                    'inactive' => 'status-inactive',
                                    'rejected' => 'status-rejected',
                                    default => 'status-active'
                                };
                                $photo = $student->student_photo ?: ($student->admission ? $student->admission->student_photo : null);
                                $fatherName = $student->father_name ? ucwords(strtolower($student->father_name)) : ($student->admission && $student->admission->father_name ? ucwords(strtolower($student->admission->father_name)) : null);
                                $motherName = $student->mother_name ? ucwords(strtolower($student->mother_name)) : ($student->admission && $student->admission->mother_name ? ucwords(strtolower($student->admission->mother_name)) : null);
                                $guardianName = $student->guardian_name ? ucwords(strtolower($student->guardian_name)) : ($student->parent_name ? ucwords(strtolower($student->parent_name)) : ($student->admission && $student->admission->guardian_name ? ucwords(strtolower($student->admission->guardian_name)) : null));
                                $guardianContact = $student->guardian_primary_mobile_no ?: ($student->parent_contact ?: ($student->admission ? $student->admission->guardian_primary_mobile_no : null));
                                $guardianRelation = $student->guardian_relation ?: ($student->admission ? $student->admission->guardian_relation : 'Guardian');
                                $cnicBform = $student->cnic_bform ?: ($student->admission ? $student->admission->cnic_bform : null);
                                $currentAddr = $student->current_address ?: ($student->address ?: ($student->admission ? $student->admission->current_address : null));
                                $permAddr = $student->permanent_address ?: ($student->admission ? $student->admission->permanent_address : null);
                                $admDate = $student->admission_date ?: ($student->admission ? $student->admission->admission_date : null);
                                $dob = $student->date_of_birth ?: ($student->admission ? $student->admission->date_of_birth : null);

                                $totFee = ($student->monthly_fee ?? 0) + ($student->quarterly_fee ?? 0) + ($student->annual_fee ?? 0) + ($student->registration_fee ?? 0);
                                if ($totFee == 0 && $student->admission) {
                                    $totFee = ($student->admission->monthly_fee ?? 0) + ($student->admission->quarterly_fee ?? 0) + ($student->admission->annual_fee ?? 0) + ($student->admission->registration_fee ?? 0);
                                }
                                $disc = (float) ($student->scholarship_discount ?: ($student->admission ? $student->admission->scholarship_discount : 0));
                                $netFee = max($totFee - $disc, 0);
                            @endphp
                            <span class="status-pill {{ $statusClass }}">
                                <span class="pulse-dot"></span>
                                {{ ucfirst($status) }}
                            </span>

                            @if ($student->is_confirmed || ($student->admission && $student->admission->is_confirmed))
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold fs-7 d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-patch-check-fill"></i> Confirmed Student
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <a href="{{ route('student-list.index') }}" class="btn btn-modern btn-modern-light">
                            <i class="bi bi-arrow-left"></i>
                            <span>Back to List</span>
                        </a>

                        <a href="{{ route('student-list.print', $student->id) }}" target="_blank" class="btn btn-modern btn-modern-light">
                            <i class="bi bi-printer"></i>
                            <span>Print Profile</span>
                        </a>

                        <a href="{{ route('student-list.edit', $student->id) }}" class="btn btn-modern btn-modern-primary">
                            <i class="bi bi-pencil-square"></i>
                            <span>Edit Student</span>
                        </a>
                    </div>
                </div>
            </div>

            @if (session('status') || session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('status') ?: session('success') }}
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
                            <i data-lucide="notebook-text"></i>
                        </div>
                        <div>
                            <div class="metric-title">Admission No</div>
                            <h4 class="metric-value text-primary">{{ $student->admission_no }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-purple">
                            <i data-lucide="building"></i>
                        </div>
                        <div>
                            <div class="metric-title">Class & Section</div>
                            <h4 class="metric-value">
                                {{ $student->studentClass ? $student->studentClass->name : ($student->class_name ?: 'N/A') }}
                                @if ($student->section_name)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-7 rounded-pill ms-1">
                                        Sec {{ strtoupper($student->section_name) }}
                                    </span>
                                @endif
                                @if ($student->group_name || $student->group || ($student->admission && ($student->admission->group_name || $student->admission->group)))
                                    <span class="badge bg-info-subtle text-info border border-info-subtle fs-7 rounded-pill ms-1">
                                        {{ $student->group_name ?: ($student->group ?: ($student->admission->group_name ?: $student->admission->group)) }}
                                    </span>
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-green">
                            <i data-lucide="credit-card"></i>
                        </div>
                        @php
                            $sPlanName = $student->fee_plan ?: ($student->admission ? $student->admission->fee_plan : 'Monthly');
                            $sPlanFee = 0;
                            $sPlanLower = strtolower(trim($sPlanName));
                            if (in_array($sPlanLower, ['monthly', 'month'])) {
                                $sPlanFee = $student->monthly_fee ?? 0;
                                $formattedSPlan = 'Monthly';
                            } elseif (in_array($sPlanLower, ['quarterly', 'quarter'])) {
                                $sPlanFee = $student->quarterly_fee ?? 0;
                                $formattedSPlan = 'Quarterly';
                            } elseif (in_array($sPlanLower, ['six-monthly', 'six monthly', 'six_monthly', 'six month', '6 months'])) {
                                $sPlanFee = $student->six_monthly_fee ?? 0;
                                $formattedSPlan = 'Six Monthly';
                            } elseif (in_array($sPlanLower, ['annual', 'annually', 'year', 'yearly'])) {
                                $sPlanFee = $student->annual_fee ?? 0;
                                $formattedSPlan = 'Annual';
                            } else {
                                $sPlanFee = $student->monthly_fee ?: ($student->quarterly_fee ?: ($student->six_monthly_fee ?: ($student->annual_fee ?: 0)));
                                $formattedSPlan = ucfirst($sPlanName);
                            }
                            $sDisc = $student->scholarship_discount ?? 0;
                            $netFee = max($sPlanFee - $sDisc, 0);
                            $siblingsList = $student->siblings;
                        @endphp
                        <div class="w-100">
                            <div class="metric-title d-flex justify-content-between align-items-center">
                                <span>Fee Plan / Net</span>
                                @if($siblingsList && $siblingsList->isNotEmpty())
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fs-8" title="Linked Siblings">
                                        <i class="bi bi-people-fill me-1"></i>{{ $siblingsList->count() }} {{ Str::plural('Sibling', $siblingsList->count()) }}
                                    </span>
                                @endif
                            </div>
                            <h4 class="metric-value text-success mb-0">
                                {{ $formattedSPlan }}
                                <small class="fs-7 fw-normal text-muted ms-1">(Rs. {{ number_format($netFee) }})</small>
                            </h4>
                            @if($siblingsList && $siblingsList->isNotEmpty())
                                <div class="fs-8 text-primary fw-medium mt-1 text-truncate" title="Sibling: {{ $siblingsList->map(fn($s)=>$s->full_name)->implode(', ') }}">
                                    <i class="bi bi-people-fill me-1"></i>Sibling: <strong>{{ $siblingsList->first()->full_name }}</strong>
                                    @if($siblingsList->count() > 1)
                                        <span class="text-muted">(+{{ $siblingsList->count() - 1 }} more)</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="summary-metric-card d-flex align-items-center gap-3">
                        <div class="metric-icon-wrapper metric-icon-amber">
                            <i data-lucide="calendar"></i>
                        </div>
                        <div>
                            <div class="metric-title">Admission Date</div>
                            <h4 class="metric-value">
                                {{ $admDate ? date('d M, Y', strtotime($admDate)) : 'N/A' }}
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
                                <div class="avatar-wrapper">
                                    @if ($photo)
                                        <img src="{{ asset('storage/' . $photo) }}" alt="{{ $student->full_name }}" class="avatar-img" />
                                    @else
                                        <div class="avatar-placeholder-gradient">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name ?: 'S', 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="avatar-online-badge" title="Active Student"></span>
                                </div>

                                <h3 class="student-name">{{ $student->first_name }} {{ $student->last_name }}</h3>
                                <div class="student-sub-info">
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1 fw-semibold me-1">
                                        Roll #{{ $student->roll_no ?: 'Unassigned' }}
                                    </span>
                                </div>

                                {{-- Quick Contact Actions --}}
                                <div class="quick-contact-actions">
                                    @if ($student->student_mobile_no || $guardianContact)
                                        <a href="tel:{{ $student->student_mobile_no ?: $guardianContact }}" class="quick-action-btn" title="Call Contact">
                                            <i class="bi bi-telephone-fill"></i>
                                        </a>
                                    @endif
                                    @if ($student->student_email || $student->guardian_email)
                                        <a href="mailto:{{ $student->student_email ?: $student->guardian_email }}" class="quick-action-btn" title="Send Email">
                                            <i class="bi bi-envelope-fill"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('student-list.edit', $student->id) }}" class="quick-action-btn" title="Edit Profile">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- Metadata List --}}
                            <div class="profile-meta-list">
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-qr-code"></i> Admission #</span>
                                    <span class="meta-value">{{ $student->admission_no }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-journal-bookmark-fill"></i> Class</span>
                                    <span class="meta-value">{{ $student->studentClass ? $student->studentClass->name : ($student->class_name ?: 'N/A') }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-diagram-3-fill"></i> Section</span>
                                    <span class="meta-value">{{ strtoupper($student->section_name ?: 'N/A') }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-hash"></i> Roll No</span>
                                    <span class="meta-value">{{ $student->roll_no ?: '-' }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-gender-ambiguous"></i> Gender</span>
                                    <span class="meta-value">{{ ucfirst($student->gender ?: 'male') }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-cake2-fill"></i> Age</span>
                                    <span class="meta-value">
                                        @if ($dob)
                                            {{ \Carbon\Carbon::parse($dob)->age }} Yrs
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-droplet-fill text-danger"></i> Blood Group</span>
                                    <span class="meta-value text-danger">{{ $student->blood_group ?: ($student->admission ? $student->admission->blood_group : '-') }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label"><i class="bi bi-globe-americas"></i> Religion</span>
                                    <span class="meta-value">{{ $student->religion ?: ($student->admission ? $student->admission->religion : '-') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: TAB NAVIGATION & PANELS --}}
                <div class="col-lg-9">
                    {{-- Modern Tab Navigation Bar --}}
                    <div class="modern-tabs-card">
                        <ul class="nav nav-pills nav-pills-custom nav-fill" id="studentTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overviewPane" type="button" role="tab">
                                    <i class="bi bi-person-vcard"></i>
                                    <span>Overview</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="academic-tab" data-bs-toggle="pill" data-bs-target="#academicPane" type="button" role="tab">
                                    <i class="bi bi-mortarboard"></i>
                                    <span>Academic & Fees</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="parent-tab" data-bs-toggle="pill" data-bs-target="#parentPane" type="button" role="tab">
                                    <i class="bi bi-people"></i>
                                    <span>Parents & Family</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                @php
                                    $birthCert = $student->birth_certificate ?: ($student->admission ? $student->admission->birth_certificate : null);
                                    $bformCopy = $student->bform_cnic_copy ?: ($student->admission ? $student->admission->bform_cnic_copy : null);
                                    $gCnicCopy = $student->guardian_cnic_copy ?: ($student->admission ? $student->admission->guardian_cnic_copy : null);
                                    $slCert = $student->school_leaving_certificate ?: ($student->admission ? $student->admission->school_leaving_certificate : null);
                                    $docList = [$birthCert, $bformCopy, $gCnicCopy, $slCert];
                                    $submittedCount = collect($docList)->filter(fn($d) => !empty($d))->count();
                                @endphp
                                <button class="nav-link" id="documents-tab" data-bs-toggle="pill" data-bs-target="#documentsPane" type="button" role="tab">
                                    <i class="bi bi-folder-check"></i>
                                    <span>Documents</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-1 fs-8">
                                        {{ $submittedCount }}/4
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="timeline-tab" data-bs-toggle="pill" data-bs-target="#timelinePane" type="button" role="tab">
                                    <i class="bi bi-clock-history"></i>
                                    <span>Timeline</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Panels Content --}}
                    <div class="tab-content" id="studentTabContent">
                        {{-- PANEL 1: OVERVIEW --}}
                        <div class="tab-pane fade show active" id="overviewPane" role="tabpanel">
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
                                                <div class="tile-value">{{ $student->first_name }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-person"></i> Last Name</div>
                                                <div class="tile-value">{{ $student->last_name ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-card-text"></i> CNIC / B-Form</div>
                                                <div class="tile-value">{{ $cnicBform ?: '-' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-calendar-check"></i> Date of Birth</div>
                                                <div class="tile-value">
                                                    {{ $dob ? date('d M, Y', strtotime($dob)) : '-' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-flag"></i> Nationality</div>
                                                <div class="tile-value">
                                                    {{ $student->nationality ?: ($student->admission ? $student->admission->nationality : 'Pakistani') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-telephone"></i> Student Mobile</div>
                                                <div class="tile-value">{{ $student->student_mobile_no ?: '-' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-envelope"></i> Student Email</div>
                                                <div class="tile-value">{{ $student->student_email ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-book"></i> Religion</div>
                                                <div class="tile-value">{{ $student->religion ?: ($student->admission ? $student->admission->religion : '-') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Previous School Callout --}}
                                    <div class="mt-4">
                                        <div class="callout-box">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="bi bi-building text-primary fs-5"></i>
                                                <span class="fw-bold text-dark fs-7 text-uppercase">Previous School History</span>
                                            </div>
                                            <div class="text-secondary fs-6">
                                                {{ $student->previous_school ?: ($student->admission ? $student->admission->previous_school : 'No previous school information recorded for this student.') }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Medical Notes Callout --}}
                                    @php
                                        $medNotes = $student->student_medical_notes ?: ($student->admission ? $student->admission->student_medical_notes : null);
                                    @endphp
                                    @if ($medNotes)
                                        <div class="mt-3">
                                            <div class="callout-box callout-box-warning">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <i class="bi bi-heart-pulse-fill text-warning fs-5"></i>
                                                    <span class="fw-bold text-dark fs-7 text-uppercase">Medical & Special Conditions</span>
                                                </div>
                                                <div class="text-dark fs-6 fw-medium">
                                                    {{ $medNotes }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- PANEL 2: ACADEMIC & FEES --}}
                        <div class="tab-pane fade" id="academicPane" role="tabpanel">
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
                                                <div class="tile-label"><i class="bi bi-calendar3"></i> Academic Session</div>
                                                <div class="tile-value text-primary">
                                                    {{ $student->admission && $student->admission->academicSession ? $student->admission->academicSession->session_name : 'Current Session' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-building"></i> Class Name</div>
                                                <div class="tile-value">{{ $student->studentClass ? $student->studentClass->name : ($student->class_name ?: 'N/A') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-diagram-2"></i> Section</div>
                                                <div class="tile-value">{{ strtoupper($student->section_name ?: 'N/A') }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-hash"></i> Roll Number</div>
                                                <div class="tile-value">{{ $student->roll_no ?: 'Unassigned' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-sun"></i> Class Shift</div>
                                                <div class="tile-value">{{ ucfirst($student->class_shift ?: 'Morning') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-folder-symlink"></i> Academic Group</div>
                                                <div class="tile-value">
                                                    @php
                                                        $grp = $student->group_name ?: ($student->group ?: ($student->admission ? ($student->admission->group_name ?: $student->admission->group) : null));
                                                    @endphp
                                                    @if($grp)
                                                        <span class="badge bg-info-subtle text-info border border-info-subtle fs-7 rounded-pill px-2 py-1">
                                                            {{ $grp }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-person-check"></i> Admission Type</div>
                                                <div class="tile-value">{{ ucwords(str_replace('_', ' ', $student->admission_type ?: 'Regular')) }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $acadNotes = $student->academic_notes ?: ($student->admission ? $student->admission->academic_notes : null);
                                        $admRemarks = $student->admission_remarks ?: ($student->admission ? $student->admission->admission_remarks : null);
                                    @endphp
                                    @if ($acadNotes)
                                        <div class="mt-3">
                                            <div class="callout-box callout-box-info">
                                                <div class="fw-bold text-dark mb-1 fs-7 text-uppercase">
                                                    <i class="bi bi-info-circle me-1"></i> Academic Remarks
                                                </div>
                                                <div class="text-dark">{{ $acadNotes }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($admRemarks)
                                        <div class="mt-3">
                                            <div class="callout-box callout-box-primary">
                                                <div class="fw-bold text-dark mb-1 fs-7 text-uppercase">
                                                    <i class="bi bi-chat-left-text me-1"></i> Admission Remarks
                                                </div>
                                                <div class="text-dark">{{ $admRemarks }}</div>
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
                                                        <td class="text-secondary">Fee Plan Type</td>
                                                        <td class="text-end fw-bold text-dark">
                                                            {{ $student->fee_plan ?: ($student->admission ? $student->admission->fee_plan : 'Monthly') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Monthly Fee</td>
                                                        <td class="text-end fw-bold">
                                                            Rs. {{ number_format((float) ($student->monthly_fee ?: ($student->admission ? $student->admission->monthly_fee : 0))) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Quarterly Fee</td>
                                                        <td class="text-end fw-bold">
                                                            Rs. {{ number_format((float) ($student->quarterly_fee ?: ($student->admission ? $student->admission->quarterly_fee : 0))) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Annual Fee</td>
                                                        <td class="text-end fw-bold">
                                                            Rs. {{ number_format((float) ($student->annual_fee ?: ($student->admission ? $student->admission->annual_fee : 0))) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Registration Fee</td>
                                                        <td class="text-end fw-bold">
                                                            Rs. {{ number_format((float) ($student->registration_fee ?: ($student->admission ? $student->admission->registration_fee : 0))) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-secondary">Scholarship Discount</td>
                                                        <td class="text-end fw-bold text-success">
                                                            -Rs. {{ number_format($disc) }}
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
                                                <span class="badge bg-primary text-white px-3 py-1 rounded-pill fs-7">Financial Summary</span>
                                                <i class="bi bi-graph-up-arrow fs-4 text-white-50"></i>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-white-50 text-uppercase fw-semibold tracking-wider">Gross Total Fee</small>
                                                <h3 class="fw-bold text-white mb-0">Rs. {{ number_format($totFee) }}</h3>
                                            </div>
                                            <div class="mb-4">
                                                <small class="text-white-50 text-uppercase fw-semibold tracking-wider">Applied Scholarship</small>
                                                <h4 class="fw-bold text-emerald-400 text-success mb-0">-Rs. {{ number_format($disc) }}</h4>
                                            </div>
                                        </div>

                                        <div>
                                            <hr class="border-white-15 my-3" />
                                            <div class="d-flex justify-content-between align-items-end">
                                                <div>
                                                    <small class="text-white-50 text-uppercase fw-semibold tracking-wider">Net Amount Payable</small>
                                                    <h2 class="fw-bold text-white mb-0 fs-1">Rs. {{ number_format($netFee) }}</h2>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success border border-success-subtle px-3 py-2 rounded-pill">
                                                        <i class="bi bi-check-circle me-1"></i> Active Plan
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PANEL 3: PARENTS & FAMILY --}}
                        <div class="tab-pane fade" id="parentPane" role="tabpanel">
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
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-person"></i> Father Name</div>
                                                <div class="tile-value">{{ $fatherName ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-person"></i> Mother Name</div>
                                                <div class="tile-value">{{ $motherName ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-shield-person"></i> Guardian Name</div>
                                                <div class="tile-value">{{ $guardianName ?: '-' }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-diagram-2"></i> Relation</div>
                                                <div class="tile-value">{{ ucfirst($guardianRelation) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-card-text"></i> Guardian CNIC</div>
                                                <div class="tile-value">{{ $student->guardian_cnic ?: ($student->admission ? $student->admission->guardian_cnic : '-') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-briefcase"></i> Occupation</div>
                                                <div class="tile-value">{{ $student->guardian_occupation ?: ($student->admission ? $student->admission->guardian_occupation : '-') }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-telephone-fill text-primary"></i> Primary Mobile</div>
                                                <div class="tile-value">{{ $guardianContact ?: '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-telephone"></i> Secondary Mobile</div>
                                                <div class="tile-value">{{ $student->guardian_secondary_mobile_no ?: ($student->admission ? $student->admission->guardian_secondary_mobile_no : '-') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="detail-tile">
                                                <div class="tile-label"><i class="bi bi-envelope"></i> Guardian Email</div>
                                                <div class="tile-value">{{ $student->guardian_email ?: ($student->admission ? $student->admission->guardian_email : '-') }}</div>
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
                                                {{ $currentAddr ?: 'No current address recorded.' }}
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
                                                {{ $permAddr ?: ($currentAddr ?: 'No permanent address recorded.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Transport Facility --}}
                            @php
                                $transReq = $student->transportation_required ?: ($student->admission ? $student->admission->transportation_required : 'no');
                                $transRoute = $student->transportation_route ?: ($student->admission ? $student->admission->transportation_route : null);
                            @endphp
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="section-card mb-0">
                                        <div class="section-card-header">
                                            <div class="section-title-group">
                                                <div class="section-title-icon text-success bg-success-subtle">
                                                    <i class="bi bi-bus-front-fill"></i>
                                                </div>
                                                <h5 class="section-title">Transport Facility</h5>
                                            </div>
                                        </div>
                                        <div class="section-card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="detail-tile">
                                                        <div class="tile-label"><i class="bi bi-check-circle"></i> Transport Required</div>
                                                        <div class="tile-value text-capitalize fw-bold {{ strtolower($transReq) === 'yes' ? 'text-success' : 'text-secondary' }}">
                                                            {{ ucfirst($transReq) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-tile">
                                                        <div class="tile-label"><i class="bi bi-signpost-split"></i> Transport Route</div>
                                                        <div class="tile-value">{{ $transRoute ?: 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Linked Siblings in School --}}
                            @php
                                $siblingsList = $student->siblings;
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
                                                        <th class="py-2 text-muted fw-semibold">Fee Plan</th>
                                                        <th class="py-2 text-muted fw-semibold">Net Fee</th>
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
                                                            @php
                                                                $sPlan = $sibling->fee_plan ?: 'Monthly';
                                                                $sPlanLower = strtolower(trim($sPlan));
                                                                if (in_array($sPlanLower, ['monthly', 'month'])) {
                                                                    $sAmount = $sibling->monthly_fee ?? 0;
                                                                    $sFormattedPlan = 'Monthly';
                                                                } elseif (in_array($sPlanLower, ['quarterly', 'quarter'])) {
                                                                    $sAmount = $sibling->quarterly_fee ?? 0;
                                                                    $sFormattedPlan = 'Quarterly';
                                                                } elseif (in_array($sPlanLower, ['six-monthly', 'six monthly', 'six_monthly', 'six month', '6 months'])) {
                                                                    $sAmount = $sibling->six_monthly_fee ?? 0;
                                                                    $sFormattedPlan = 'Six Monthly';
                                                                } elseif (in_array($sPlanLower, ['annual', 'annually', 'year', 'yearly'])) {
                                                                    $sAmount = $sibling->annual_fee ?? 0;
                                                                    $sFormattedPlan = 'Annual';
                                                                } else {
                                                                    $sAmount = $sibling->monthly_fee ?: ($sibling->quarterly_fee ?: ($sibling->six_monthly_fee ?: ($sibling->annual_fee ?: 0)));
                                                                    $sFormattedPlan = ucfirst($sPlan);
                                                                }
                                                                $sDisc = $sibling->scholarship_discount ?? 0;
                                                                $sNet = max($sAmount - $sDisc, 0);
                                                            @endphp
                                                            <td>
                                                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill fw-semibold">
                                                                    {{ $sFormattedPlan }}
                                                                </span>
                                                            </td>
                                                            <td class="fw-bold text-success">
                                                                Rs. {{ number_format($sNet) }}
                                                            </td>
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

                        {{-- PANEL 4: DOCUMENTS --}}
                        <div class="tab-pane fade" id="documentsPane" role="tabpanel">
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="section-title-group">
                                        <div class="section-title-icon">
                                            <i class="bi bi-file-earmark-check-fill"></i>
                                        </div>
                                        <h5 class="section-title">Required Documents Verification</h5>
                                    </div>
                                </div>
                                <div class="section-card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold text-sm mb-1">Birth Certificate</h6>
                                                    <span class="text-xs text-tertiary">Official Birth Record</span>
                                                </div>
                                                @if (!empty($birthCert))
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">Pending</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold text-sm mb-1">B-Form / CNIC Copy</h6>
                                                    <span class="text-xs text-tertiary">Student ID Document</span>
                                                </div>
                                                @if (!empty($bformCopy))
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">Pending</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold text-sm mb-1">Guardian CNIC Copy</h6>
                                                    <span class="text-xs text-tertiary">Guardian Identification</span>
                                                </div>
                                                @if (!empty($gCnicCopy))
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">Pending</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold text-sm mb-1">School Leaving Certificate</h6>
                                                    <span class="text-xs text-tertiary">Previous School Record</span>
                                                </div>
                                                @if (!empty($slCert))
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PANEL 5: TIMELINE --}}
                        <div class="tab-pane fade" id="timelinePane" role="tabpanel">
                            <div class="section-card">
                                <div class="section-card-header">
                                    <div class="section-title-group">
                                        <div class="section-title-icon">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                        <h5 class="section-title">Record Audit History</h5>
                                    </div>
                                </div>
                                <div class="section-card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Record Created</div>
                                                <div class="tile-value fw-bold text-primary">
                                                    {{ $student->created_at ? $student->created_at->format('d M, Y H:i A') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Last Updated</div>
                                                <div class="tile-value fw-bold">
                                                    {{ $student->updated_at ? $student->updated_at->format('d M, Y H:i A') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="detail-tile p-3 bg-light rounded border">
                                                <div class="tile-label text-tertiary text-xs">Record Status</div>
                                                <div class="tile-value">
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill text-capitalize">
                                                        {{ $student->status ?: 'Active' }}
                                                    </span>
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
            <div class="bottom-action-bar d-flex justify-content-between align-items-center flex-wrap gap-3 fade-up mt-4">
                <div class="d-flex align-items-center gap-2 text-secondary fs-7">
                    <i class="bi bi-shield-check text-success fs-5"></i>
                    <span>Student Record ID #{{ $student->id }} &bull; Confirmed status verified</span>
                </div>

                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <a href="{{ route('student-list.index') }}" class="btn btn-modern btn-modern-light">
                        <i class="bi bi-arrow-left"></i>
                        <span>Back</span>
                    </a>

                    <button onclick="window.print()" class="btn btn-modern btn-modern-dark">
                        <i class="bi bi-printer"></i>
                        <span>Print Profile</span>
                    </button>

                    <a href="{{ route('student-list.edit', $student->id) }}" class="btn btn-modern btn-modern-primary">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit Student</span>
                    </a>

                    <form
                        action="{{ route('student-list.destroy', $student->id) }}"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this student record?');"
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
                    Official Computer Generated Student Profile &bull; Student Record ID #{{ $student->id }} &bull; Confirmed status verified &bull; Printed on {{ date('d M, Y h:i A') }}
                </div>
            </div>
        </div>
    </div>
@endsection
