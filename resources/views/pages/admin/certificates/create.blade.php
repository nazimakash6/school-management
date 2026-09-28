@extends ('layouts.app')
@section ('title', 'Generate ' . $types[$type]['title'])

@push ('styles')
    <style>
        .step-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #6366f1;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .search-dropdown-wrap {
            position: relative;
        }
        .student-select-box {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.95rem;
        }
        .student-select-box option {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
@endpush

@section ('content')
    <div class="container-fluid px-4 py-3">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('certificates.index') }}">Certificates</a></li>
                        <li class="breadcrumb-item active">{{ $types[$type]['title'] }}</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold text-dark mb-0">Generate {{ $types[$type]['title'] }}</h1>
                <p class="text-muted small mb-0">Select Academic Session & Class → search/select student from dropdown → Generate PDF</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-lucide="arrow-left" style="width: 1rem; height: 1rem" class="me-1"></i> Back
                </a>
            </div>
        </div>

        @if ($type !== 'overall_performance')
            <form id="certForm" action="{{ route('certificates.print') }}" method="POST" target="_blank" novalidate>
                @csrf
                <input type="hidden" name="type" value="{{ $type }}" />
                <input type="hidden" name="entry_mode" id="entry_mode_input" value="manual" />

                {{-- Hidden fields for experience/appreciation fallback --}}
                @if ($type === 'experience' || $type === 'appreciation')
                    <input type="hidden" name="student_name" id="h_student_name" />
                    <input type="hidden" name="father_name" id="h_father_name" />
                    <input type="hidden" name="admission_no" id="h_admission_no" />
                    <input type="hidden" name="roll_no" id="h_roll_no" />
                    <input type="hidden" name="date_of_birth" id="h_dob" />
                    <input type="hidden" name="class_name" id="h_class_name" />
                    <input type="hidden" name="section" id="h_section" />
                    <input type="hidden" name="student_photo_url" id="h_student_photo_url" />
                @endif
                <input type="hidden" name="session" id="session_input" />
        @endif

            <div class="row g-4">
                <div class="col-12">
                    @if ($type === 'experience')
                        <!-- ─── STEP 1 FOR EXPERIENCE LETTER: SELECT STAFF MEMBER ──────────────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge">1</span>
                                    Select Staff Member / Teacher
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <!-- Search bar for Staff Dropdown -->
                                <label class="form-label fw-semibold text-dark">Search Staff Member <span class="text-muted fw-normal">(filter list by name, designation, CNIC, or ID)</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-white"><i data-lucide="search" style="width:.95rem;height:.95rem;"></i></span>
                                    <input type="text" id="staff_search_input" class="form-control form-control-lg" placeholder="Type staff name, designation, CNIC or ID to search...">
                                </div>

                                <label class="form-label fw-semibold text-dark">Select Registered Staff Member <span class="text-muted fw-normal">(Auto-fills staff particulars)</span></label>
                                <select id="staff_picker" class="form-select form-select-lg student-select-box mb-2" size="5">
                                    <option value="" disabled>— Select Staff Member from List —</option>
                                    @foreach ($staffMembers as $stf)
                                        @php
                                            $fullName = trim(($stf->first_name ?? '') . ' ' . ($stf->last_name ?? ''));
                                            $desig = $stf->formatted_designation ?: ($stf->designation ?: 'Staff');
                                        @endphp
                                        <option
                                            value="{{ $stf->id }}"
                                            data-name="{{ $fullName }}"
                                            data-father="{{ $stf->emergency_contact_name ?: '' }}"
                                            data-cnic="{{ $stf->cnic ?: '' }}"
                                            data-designation="{{ $desig }}"
                                            data-joining="{{ $stf->joining_date ? \Carbon\Carbon::parse($stf->joining_date)->format('Y-m-d') : '' }}"
                                            data-leaving="{{ $stf->leaving_date ? \Carbon\Carbon::parse($stf->leaving_date)->format('Y-m-d') : '' }}"
                                            data-photo="{{ $stf->profile_picture ? asset('storage/' . $stf->profile_picture) : '' }}"
                                        >
                                            {{ $fullName }} — {{ $desig }} ({{ $stf->staff_id ?: 'CNIC: ' . $stf->cnic }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted">
                                    Click any staff member from the dropdown list to select & auto-fill the form. You can also edit any field manually.
                                </div>
                            </div>
                        </div>

                        <!-- ─── STEP 2 FOR EXPERIENCE LETTER: PARTICULAR DETAILS ─────────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge">2</span>
                                    Experience Letter Particulars
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark"
                                            >Staff / Employee Name <span class="text-danger">*</span></label
                                        >
                                        <input
                                            type="text"
                                            name="staff_name"
                                            id="exp_staff_name"
                                            class="form-control form-control-lg"
                                            placeholder="e.g. Tariq Mehmood"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark"
                                            >S/o, D/o, W/o (Father / Spouse Name)</label
                                        >
                                        <input
                                            type="text"
                                            name="father_name"
                                            id="exp_father_name"
                                            class="form-control form-control-lg"
                                            placeholder="Father's or Spouse's Name"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">CNIC Number</label>
                                        <input
                                            type="text"
                                            name="cnic_no"
                                            id="exp_cnic_no"
                                            class="form-control form-control-lg"
                                            placeholder="e.g. 35202-1234567-1"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark"
                                            >Designation <span class="text-danger">*</span></label
                                        >
                                        <input
                                            type="text"
                                            name="designation"
                                            id="exp_designation"
                                            class="form-control form-control-lg"
                                            placeholder="e.g. Senior Teacher / Accountant"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Employed From Date</label>
                                        <input
                                            type="date"
                                            name="from_date"
                                            id="exp_from_date"
                                            class="form-control form-control-lg"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark"
                                            >Employed To Date (or Till Date)</label
                                        >
                                        <input
                                            type="date"
                                            name="to_date"
                                            id="exp_to_date"
                                            class="form-control form-control-lg"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Reference Number</label>
                                        <input
                                            type="text"
                                            name="ref_no"
                                            class="form-control form-control-lg"
                                            value="NUH/HR/{{ date('Y') }}/{{ rand(100, 999) }}"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Issue Date</label>
                                        <input
                                            type="date"
                                            name="issue_date"
                                            class="form-control form-control-lg"
                                            value="{{ date('Y-m-d') }}"
                                        />
                                    </div>
                                    <div class="col-12"><hr class="my-1" /></div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Principal Name</label>
                                        <input
                                            type="text"
                                            name="principal"
                                            class="form-control"
                                            value="Principal"
                                            placeholder="Principal's Name"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Head of Institution Name</label>
                                        <input
                                            type="text"
                                            name="head_of_institution"
                                            class="form-control"
                                            value="Head of Institution"
                                            placeholder="Head of Institution Name"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif ($type === 'appreciation')
                        <!-- ─── STEP 1 FOR APPRECIATION CERTIFICATE: SELECT RECIPIENT ──────────────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge">1</span>
                                    Select Award Recipient (Student or Teacher/Staff)
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <!-- Recipient Category Toggle -->
                                <div class="mb-4 p-3 bg-light rounded-3 border">
                                    <label class="form-label fw-bold text-dark mb-2">Select Award Category:</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="recipient_type" id="app_type_student" value="student" checked>
                                            <label class="form-check-label fw-bold text-dark" for="app_type_student">
                                                👨‍🎓 Student Award (e.g. Student of the Month / Year)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="recipient_type" id="app_type_staff" value="staff">
                                            <label class="form-check-label fw-bold text-dark" for="app_type_staff">
                                                👨‍🏫 Teacher / Staff Award (e.g. Teacher of the Month / Year)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student Selection Container -->
                                <div id="app_student_picker_wrap">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">Academic Session</label>
                                            <select id="session_picker" class="form-select form-select-lg">
                                                @foreach ($academicSessions as $sess)
                                                    <option value="{{ $sess->id }}" data-name="{{ $sess->session_name }}" {{ $loop->first ? 'selected' : '' }}>
                                                        Session {{ $sess->session_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">Select Class</label>
                                            <select id="class_picker" class="form-select form-select-lg">
                                                <option value="" selected disabled>— Select Class —</option>
                                                @foreach ($classes as $cls)
                                                    <option value="{{ $cls }}">{{ $cls }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Loading Spinner -->
                                    <div id="students_loading" class="text-center py-3" style="display: none">
                                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                                        <span class="text-muted small">Loading students...</span>
                                    </div>

                                    <!-- Student Select Container -->
                                    <div id="student_select_wrapper" style="display: none;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label class="form-label fw-semibold text-dark mb-0">Search Student</label>
                                            <span id="student_count" class="text-muted small"></span>
                                        </div>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-white"><i data-lucide="search" style="width:.95rem;height:.95rem;"></i></span>
                                            <input type="text" id="student_search_input" class="form-control form-control-lg" placeholder="Type student name, father name, or roll no to search..." />
                                        </div>
                                        
                                        <label class="form-label fw-semibold text-dark">Select Registered Student <span class="text-muted fw-normal">(Click student to auto-fill details)</span></label>
                                        <select id="student_dropdown" class="form-select form-select-lg student-select-box mb-2" size="5">
                                            <option value="" disabled>— Select Student —</option>
                                        </select>
                                    </div>

                                    <div id="no_students" class="text-center py-3 text-muted small" style="display: none">
                                        No students found for this selection.
                                    </div>
                                </div>

                                <!-- Staff Selection Container -->
                                <div id="app_staff_picker_wrap" style="display: none;">
                                    <label class="form-label fw-semibold text-dark">Search Staff / Teacher</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white"><i data-lucide="search" style="width:.95rem;height:.95rem;"></i></span>
                                        <input type="text" id="app_staff_search_input" class="form-control form-control-lg" placeholder="Type staff name or designation to search...">
                                    </div>

                                    <label class="form-label fw-semibold text-dark">Select Registered Staff Member</label>
                                    <select id="app_staff_picker" class="form-select form-select-lg student-select-box mb-2" size="5">
                                        <option value="" disabled>— Select Staff Member from List —</option>
                                        @foreach ($staffMembers as $stf)
                                            @php
                                                $fullName = trim(($stf->first_name ?? '') . ' ' . ($stf->last_name ?? ''));
                                                $desig = $stf->formatted_designation ?: ($stf->designation ?: 'Teacher');
                                            @endphp
                                            <option
                                                value="{{ $stf->id }}"
                                                data-name="{{ $fullName }}"
                                                data-father="{{ $stf->emergency_contact_name ?: '' }}"
                                                data-designation="{{ $desig }}"
                                                data-photo="{{ $stf->profile_picture ? asset('storage/' . $stf->profile_picture) : '' }}"
                                            >
                                                {{ $fullName }} — {{ $desig }} ({{ $stf->staff_id ?: 'ID: ' . $stf->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ─── STEP 2 FOR APPRECIATION CERTIFICATE: PARTICULAR DETAILS ─────────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge">2</span>
                                    Appreciation Certificate Particulars
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Award Category Preset</label>
                                        <select id="award_preset_select" class="form-select form-select-lg">
                                            <option value="STUDENT OF THE MONTH" selected>Student of the Month</option>
                                            <option value="TEACHER OF THE MONTH">Teacher of the Month</option>
                                            <option value="STUDENT OF THE YEAR">Student of the Year</option>
                                            <option value="TEACHER OF THE YEAR">Teacher of the Year</option>
                                            <option value="BEST ACADEMIC PERFORMER">Best Academic Performer</option>
                                            <option value="EXCELLENCE IN TEACHING">Excellence in Teaching</option>
                                            <option value="OUTSTANDING DEDICATION & SERVICE">Outstanding Dedication & Service</option>
                                            <option value="CUSTOM">Custom Award Title...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Award Title / Badge Text <span class="text-danger">*</span></label>
                                        <input type="text" name="award_title" id="app_award_title" class="form-control form-control-lg fw-bold text-primary" value="STUDENT OF THE MONTH" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Recipient Name <span class="text-danger">*</span></label>
                                        <input type="text" name="student_name" id="app_recipient_name" class="form-control form-control-lg" placeholder="Recipient's Full Name" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Father / Guardian / Spouse Name</label>
                                        <input type="text" name="father_name" id="app_father_name" class="form-control form-control-lg" placeholder="Father / Guardian Name" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Designation / Class</label>
                                        <input type="text" name="designation" id="app_designation" class="form-control form-control-lg" placeholder="e.g. Senior Teacher / Class 10th A" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Award Period / Session</label>
                                        <input type="text" name="award_period" id="app_award_period" class="form-control form-control-lg" placeholder="e.g. September 2026 / Session 2025-2026" value="{{ date('F Y') }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Reference Number</label>
                                        <input type="text" name="ref_no" class="form-control form-control-lg" value="NUH/AC/{{ date('Y') }}/{{ rand(100, 999) }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Issue Date</label>
                                        <input type="date" name="issue_date" class="form-control form-control-lg" value="{{ date('Y-m-d') }}" />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">Appreciation Remarks / Citation Note</label>
                                        <textarea name="remarks" rows="2" class="form-control" placeholder="Optional custom appreciation citation message...">In recognition of outstanding performance, exemplary conduct, and exceptional dedication towards academic & institutional excellence.</textarea>
                                    </div>
                                    <div class="col-12"><hr class="my-1" /></div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Teacher / Section Head Title</label>
                                        <input type="text" name="class_teacher" class="form-control" value="SECTION HEAD / TEACHER" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Principal Name</label>
                                        <input type="text" name="principal" class="form-control" value="Principal" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @if ($type === 'overall_performance')
                            <!-- ─── TAB NAVIGATION BAR (MANUAL vs DYNAMIC) ────────────────────────── -->
                            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                                <div class="card-body p-2 p-md-3">
                                    <ul class="nav nav-pills nav-justified gap-2" id="certTabList" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active fw-bold py-2.5 px-3 fs-6 d-flex align-items-center justify-content-center gap-2" id="manual-tab" data-bs-toggle="pill" data-bs-target="#tab-manual-pane" type="button" role="tab" aria-controls="tab-manual-pane" aria-selected="true">
                                                <i data-lucide="edit-3" style="width:1.2rem;height:1.2rem;"></i>
                                                Manual Form Entry
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link fw-bold py-2.5 px-3 fs-6 d-flex align-items-center justify-content-center gap-2" id="dynamic-tab" data-bs-toggle="pill" data-bs-target="#tab-dynamic-pane" type="button" role="tab" aria-controls="tab-dynamic-pane" aria-selected="false">
                                                <i data-lucide="database" style="width:1.2rem;height:1.2rem;"></i>
                                                Dynamic Auto-Fetch Entry
                                                <span class="badge bg-primary text-white ms-1 rounded-pill" style="font-size:0.75rem;">Modules DB</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-content" id="certTabContent">
                                <!-- ─── TAB 1: MANUAL FORM ENTRY (SEPARATE FORM) ────────────────────── -->
                                <div class="tab-pane fade show active" id="tab-manual-pane" role="tabpanel" aria-labelledby="manual-tab">
                                    <form id="manualCertForm" action="{{ route('certificates.print') }}" method="POST" target="_blank" novalidate>
                                        @csrf
                                        <input type="hidden" name="type" value="overall_performance" />
                                        <input type="hidden" name="entry_mode" value="manual" />
                                        <input type="hidden" name="session" id="session_input" />
                                        <input type="hidden" name="student_name" id="h_student_name" />
                                        <input type="hidden" name="father_name" id="h_father_name" />
                                        <input type="hidden" name="admission_no" id="h_admission_no" />
                                        <input type="hidden" name="roll_no" id="h_roll_no" />
                                        <input type="hidden" name="date_of_birth" id="h_dob" />
                                        <input type="hidden" name="class_name" id="h_class_name" />
                                        <input type="hidden" name="section" id="h_section" />
                                        <input type="hidden" name="student_photo_url" id="h_student_photo_url" />
                        @endif
                        <!-- ─── STEP 1: SELECT ACADEMIC SESSION, CLASS & REGISTERED STUDENT ──────────────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge">1</span>
                                    Select Academic Session, Class & Registered Student
                                </h6>
                                <span id="student_count" class="text-muted small"></span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3 align-items-end">
                                    <!-- 1. Academic Session -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark">Academic Session <span class="text-danger">*</span></label>
                                        <select id="session_picker" class="form-select form-select-lg">
                                            <option value="">— Select Academic Session —</option>
                                            @foreach ($academicSessions as $sess)
                                                <option value="{{ $sess->id }}" data-name="{{ $sess->session_name }}" {{ $loop->first ? 'selected' : '' }}>
                                                    Academic Session {{ $sess->session_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- 2. Select Class -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark">Select Class <span class="text-danger">*</span></label>
                                        <select id="class_picker" class="form-select form-select-lg">
                                            <option value="" selected>— Select Class —</option>
                                            @foreach ($classes as $cls)
                                                <option value="{{ $cls }}">{{ $cls }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- 3. Select Student Dropdown -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark">Select Student <span class="text-danger">*</span></label>
                                        <select id="student_dropdown" class="form-select form-select-lg" disabled>
                                            <option value="">— Select Class First —</option>
                                        </select>
                                    </div>

                                    <!-- Search filter bar (appears when class selected) -->
                                    <div class="col-12 mt-2" id="student_search_wrap" style="display: none;">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i data-lucide="search" style="width: 0.95rem; height: 0.95rem"></i></span>
                                            <input
                                                type="text"
                                                id="student_search_input"
                                                class="form-control"
                                                placeholder="Type student name, father name, or roll no to filter student dropdown list..."
                                            />
                                        </div>
                                    </div>

                                    <!-- Loading spinner -->
                                    <div id="students_loading" class="col-12 text-center py-2" style="display: none">
                                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                                        <span class="text-muted small">Loading students...</span>
                                    </div>

                                    <div id="no_students" class="col-12 text-center py-2 text-muted small" style="display: none">
                                        No students found for this class & session.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ─── STEP 3: SELECTED STUDENT PREVIEW ─────────────── -->
                        <!-- ─── STEP 3: STUDENT PROFILE PARTICULARS (AUTO-FILLED OR EDITABLE) ─────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-primary border-4" id="student_preview_card">
                            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge" style="background: #4f46e5">2</span>
                                    Student Particulars & Profile Information
                                </h6>
                                <span class="badge bg-primary bg-opacity-10 text-primary border">Auto-fills from dropdown or type manually</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Student Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="student_name" id="input_student_name" class="form-control form-control-lg fw-bold text-dark" placeholder="Type student full name or select from class above..." required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Father's Name</label>
                                        <input type="text" name="father_name" id="input_father_name" class="form-control form-control-lg" placeholder="Type father's name..." />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-dark">Class Name</label>
                                        <input type="text" name="class_name" id="input_class_name" class="form-control" placeholder="e.g. Class 5th" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-dark">Section</label>
                                        <input type="text" name="section" id="input_section" class="form-control" placeholder="e.g. A" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-dark">Roll Number</label>
                                        <input type="text" name="roll_no" id="input_roll_no" class="form-control" placeholder="e.g. 15" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-dark">Admission No.</label>
                                        <input type="text" name="admission_no" id="input_admission_no" class="form-control" placeholder="e.g. ADM-102" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Date of Birth</label>
                                        <input type="text" name="date_of_birth" id="input_dob" class="form-control" placeholder="e.g. 12 Oct 2015" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Student Photo URL / Path</label>
                                        <input type="text" name="student_photo_url" id="input_student_photo_url" class="form-control" placeholder="Auto-filled when student is selected from dropdown" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ─── STEP 4: ADDITIONAL FIELDS FOR STUDENT CERTIFICATES ─────────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge" id="step_num">3</span>
                                    Authority & Certificate Details
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="{{ $type === 'merit' ? 'col-md-6' : 'col-12' }}">
                                        <label class="form-label fw-semibold text-dark">Issue Date</label>
                                        <input
                                            type="date"
                                            name="issue_date"
                                            class="form-control"
                                            value="{{ date('Y-m-d') }}"
                                        />
                                    </div>

                                    @if ($type === 'school_leaving')
                                        <div class="col-12">
                                            <label class="form-label fw-semibold text-dark">Reason for Leaving</label>
                                            <textarea
                                                name="reason"
                                                class="form-control"
                                                rows="2"
                                                placeholder="e.g. Completion of studies, Transfer to another city"
                                            ></textarea>
                                        </div>
                                    @endif

                                    @if ($type === 'merit')
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark"
                                                >Achievement / Remarks</label
                                            >
                                            <input
                                                type="text"
                                                name="remarks"
                                                class="form-control"
                                                placeholder="e.g. Achieved 1st Position in Annual Examination 2026"
                                            />
                                        </div>
                                    @endif

                                    @if ($type === 'remarks_signature')
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark"
                                                >Teacher's Remarks
                                                <span class="text-muted fw-normal"
                                                    >(Optional - leave blank for handwriting lines)</span
                                                ></label
                                            >
                                            <textarea
                                                name="teacher_remarks"
                                                class="form-control"
                                                rows="3"
                                                placeholder="e.g. Hardworking student with excellent behavior in class."
                                            ></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark"
                                                >Principal's Remarks
                                                <span class="text-muted fw-normal"
                                                    >(Optional - leave blank for handwriting lines)</span
                                                ></label
                                            >
                                            <textarea
                                                name="principal_remarks"
                                                class="form-control"
                                                rows="3"
                                                placeholder="e.g. Recommended for higher studies. Keep up the good work!"
                                            ></textarea>
                                        </div>
                                    @endif

                                    @if ($type === 'overall_performance')
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-dark">Report Type</label>
                                            <select name="report_type" class="form-select">
                                                <option value="Daily">Daily Report</option>
                                                <option value="Weekly">Weekly Report</option>
                                                <option value="Monthly" selected>Monthly Progress Card</option>
                                                <option value="Term">Term / Quarterly Card</option>
                                                <option value="Other">Other / Special</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-dark">Date / Month Period</label>
                                            <input type="text" name="date_period" class="form-control" value="{{ date('F Y') }}" placeholder="e.g. September 2026 / Term 1" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold text-dark">Focus Area / Special Subject</label>
                                            <input type="text" name="focus_area" class="form-control" placeholder="e.g. All Subjects & Behaviour" value="General Academics & Character" />
                                        </div>

                                        <!-- 1. PERFORMANCE TRACKER TABLE -->
                                        <div class="col-12 mt-3">
                                            <div class="border rounded-3 p-3 bg-light">
                                                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                                    <i data-lucide="bar-chart-2" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                                                    Performance Tracker (Subject-Wise Assessment)
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered bg-white align-middle mb-0" style="font-size:0.875rem;">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th style="width:25%;">Subject / Area</th>
                                                                <th style="width:45%;" class="text-center">Rating / Grade</th>
                                                                <th style="width:30%;">Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $subjectsList = ['Quran', 'Islamic Studies', 'English', 'Urdu', 'Mathematics', 'Science', 'Social Studies', 'Computer', 'General Knowledge', 'Other'];
                                                            @endphp
                                                            @foreach($subjectsList as $sIdx => $subj)
                                                            <tr>
                                                                <td class="fw-semibold text-dark">
                                                                    {{ $subj }}
                                                                    <input type="hidden" name="performance_tracker[{{ $sIdx }}][subject]" value="{{ $subj }}">
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex justify-content-around align-items-center">
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input" type="radio" name="performance_tracker[{{ $sIdx }}][rating]" id="pt_{{ $sIdx }}_ex" value="excellent" checked>
                                                                            <label class="form-check-label text-success fw-bold" for="pt_{{ $sIdx }}_ex">★ Excellent</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input" type="radio" name="performance_tracker[{{ $sIdx }}][rating]" id="pt_{{ $sIdx }}_gd" value="good">
                                                                            <label class="form-check-label text-primary fw-semibold" for="pt_{{ $sIdx }}_gd">👍 Good</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input" type="radio" name="performance_tracker[{{ $sIdx }}][rating]" id="pt_{{ $sIdx }}_av" value="average">
                                                                            <label class="form-check-label text-warning" for="pt_{{ $sIdx }}_av">- Average</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input" type="radio" name="performance_tracker[{{ $sIdx }}][rating]" id="pt_{{ $sIdx }}_ni" value="needs_improvement">
                                                                            <label class="form-check-label text-danger" for="pt_{{ $sIdx }}_ni">! Needs Imp.</label>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="performance_tracker[{{ $sIdx }}][remarks]" class="form-control form-control-sm" placeholder="Subject remarks..." value="{{ $subj === 'Quran' ? 'Fluent recitation' : '' }}">
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 2. SKILLS & ATTRIBUTES TABLE -->
                                        <div class="col-12 mt-3">
                                            <div class="border rounded-3 p-3 bg-light">
                                                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                                    <i data-lucide="smile" style="width:1.1rem;height:1.1rem;" class="text-success"></i>
                                                    Skills & Attributes (Personal & Social Development)
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered bg-white align-middle mb-0" style="font-size:0.875rem;">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th style="width:25%;">Skill Area</th>
                                                                <th style="width:45%;" class="text-center">Rating / Grade</th>
                                                                <th style="width:30%;">Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $skillsList = ['Attention & Participation', 'Behaviour', 'Assignments', 'Punctuality', 'Neatness & Presentation', 'Confidence', 'Co-operation', 'Respect for Others'];
                                                            @endphp
                                                            @foreach($skillsList as $skIdx => $skItem)
                                                            <tr>
                                                                <td class="fw-semibold text-dark">
                                                                    {{ $skItem }}
                                                                    <input type="hidden" name="skills_attributes[{{ $skIdx }}][skill]" value="{{ $skItem }}">
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex justify-content-around align-items-center">
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input" type="radio" name="skills_attributes[{{ $skIdx }}][rating]" id="sk_{{ $skIdx }}_ex" value="excellent" checked>
                                                                            <label class="form-check-label text-success fw-bold" for="sk_{{ $skIdx }}_ex">★ Excellent</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input" type="radio" name="skills_attributes[{{ $skIdx }}][rating]" id="sk_{{ $skIdx }}_gd" value="good">
                                                                            <label class="form-check-label text-primary fw-semibold" for="sk_{{ $skIdx }}_gd">👍 Good</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input" type="radio" name="skills_attributes[{{ $skIdx }}][rating]" id="sk_{{ $skIdx }}_av" value="average">
                                                                            <label class="form-check-label text-warning" for="sk_{{ $skIdx }}_av">- Average</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mb-0">
                                                                            <input class="form-check-input" type="radio" name="skills_attributes[{{ $skIdx }}][rating]" id="sk_{{ $skIdx }}_ni" value="needs_improvement">
                                                                            <label class="form-check-label text-danger" for="sk_{{ $skIdx }}_ni">! Needs Imp.</label>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="skills_attributes[{{ $skIdx }}][remarks]" class="form-control form-control-sm" placeholder="Skill remarks..." value="Active & courteous">
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 3. LEARNING HIGHLIGHTS & AREAS TO IMPROVE -->
                                        <div class="col-md-6 mt-3">
                                            <label class="form-label fw-semibold text-dark">Learning Highlights</label>
                                            <textarea name="learning_highlights" class="form-control" rows="3" placeholder="e.g. Excellent memory in Tajweed Quran, Quick solver in Mathematics, Active team leader.">Shows great interest in Quranic recitation & Mathematics. Consistently completes home assignments on time.</textarea>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <label class="form-label fw-semibold text-dark">Areas to Improve</label>
                                            <textarea name="areas_to_improve" class="form-control" rows="3" placeholder="e.g. Focus on English handwriting & regular library reading.">Needs slight improvement in English vocabulary & handwriting neatness.</textarea>
                                        </div>

                                        <!-- 4. ATTENDANCE OVERVIEW -->
                                        <div class="col-12 mt-3">
                                            <div class="border rounded-3 p-3 bg-light">
                                                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                                    <i data-lucide="calendar" style="width:1.1rem;height:1.1rem;" class="text-info"></i>
                                                    Attendance Overview
                                                </h6>
                                                <div class="row g-2">
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold text-muted">Total Working Days</label>
                                                        <input type="number" name="attendance_total" id="att_total" class="form-control" value="25">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold text-muted">Present Days</label>
                                                        <input type="number" name="attendance_present" id="att_present" class="form-control" value="24">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold text-muted">Absent Days</label>
                                                        <input type="number" name="attendance_absent" id="att_absent" class="form-control" value="1">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small fw-semibold text-muted">Attendance %</label>
                                                        <input type="text" name="attendance_pct" id="att_pct" class="form-control fw-bold text-success" value="96%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 5. PROGRESS SUMMARY (STARS & BADGE) -->
                                        <div class="col-12 mt-3">
                                            <div class="border rounded-3 p-3 bg-light">
                                                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                                    <i data-lucide="star" style="width:1.1rem;height:1.1rem;" class="text-warning"></i>
                                                    Progress Summary (Overall Category Ratings)
                                                </h6>
                                                <div class="row g-3">
                                                    <div class="col-md-2.4 col-6">
                                                        <label class="form-label small fw-semibold text-muted">Academic Rating</label>
                                                        <select name="academic_stars" class="form-select form-select-sm">
                                                            <option value="5" selected>★★★★★ (5 Stars)</option>
                                                            <option value="4">★★★★☆ (4 Stars)</option>
                                                            <option value="3">★★★☆☆ (3 Stars)</option>
                                                            <option value="2">★★☆☆☆ (2 Stars)</option>
                                                            <option value="1">★☆☆☆☆ (1 Star)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2.4 col-6">
                                                        <label class="form-label small fw-semibold text-muted">Islamic Development</label>
                                                        <select name="islamic_stars" class="form-select form-select-sm">
                                                            <option value="5" selected>★★★★★ (5 Stars)</option>
                                                            <option value="4">★★★★☆ (4 Stars)</option>
                                                            <option value="3">★★★☆☆ (3 Stars)</option>
                                                            <option value="2">★★☆☆☆ (2 Stars)</option>
                                                            <option value="1">★☆☆☆☆ (1 Star)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2.4 col-6">
                                                        <label class="form-label small fw-semibold text-muted">Personal Development</label>
                                                        <select name="personal_stars" class="form-select form-select-sm">
                                                            <option value="5" selected>★★★★★ (5 Stars)</option>
                                                            <option value="4">★★★★☆ (4 Stars)</option>
                                                            <option value="3">★★★☆☆ (3 Stars)</option>
                                                            <option value="2">★★☆☆☆ (2 Stars)</option>
                                                            <option value="1">★☆☆☆☆ (1 Star)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2.4 col-6">
                                                        <label class="form-label small fw-semibold text-muted">Behaviour & Discipline</label>
                                                        <select name="behaviour_stars" class="form-select form-select-sm">
                                                            <option value="5" selected>★★★★★ (5 Stars)</option>
                                                            <option value="4">★★★★☆ (4 Stars)</option>
                                                            <option value="3">★★★☆☆ (3 Stars)</option>
                                                            <option value="2">★★☆☆☆ (2 Stars)</option>
                                                            <option value="1">★☆☆☆☆ (1 Star)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2.4 col-6">
                                                        <label class="form-label small fw-semibold text-muted">Co-Curricular</label>
                                                        <select name="cocurricular_stars" class="form-select form-select-sm">
                                                            <option value="5" selected>★★★★★ (5 Stars)</option>
                                                            <option value="4">★★★★☆ (4 Stars)</option>
                                                            <option value="3">★★★☆☆ (3 Stars)</option>
                                                            <option value="2">★★☆☆☆ (2 Stars)</option>
                                                            <option value="1">★☆☆☆☆ (1 Star)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold text-dark">Overall Progress Status</label>
                                                        <select name="overall_progress" class="form-select">
                                                            <option value="EXCELLENT" selected>EXCELLENT</option>
                                                            <option value="GOOD">GOOD</option>
                                                            <option value="SATISFACTORY">SATISFACTORY</option>
                                                            <option value="NEEDS IMPROVEMENT">NEEDS IMPROVEMENT</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 6. REMARKS & ACTION PLAN -->
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-semibold text-dark">Teacher's Remarks</label>
                                            <textarea name="teacher_remarks" class="form-control" rows="3" placeholder="Teacher comments...">Demonstrates exemplary academic performance and excellent moral conduct in school.</textarea>
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-semibold text-dark">Parent's Remarks</label>
                                            <textarea name="parent_remarks" class="form-control" rows="3" placeholder="Parent feedback / acknowledgment...">Very satisfied with student progress and school environment.</textarea>
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-semibold text-dark">Action Plan / Next Steps</label>
                                            <textarea name="action_plan" class="form-control" rows="3" placeholder="Next plan & goals...">Continue reading practice, participate in upcoming Tajweed competition & sports gala.</textarea>
                                        </div>

                                        <!-- 7. SIGNATURES -->
                                        <div class="col-12"><hr class="my-2" /></div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-dark">Subject Teacher</label>
                                            <input type="text" name="subject_teacher" class="form-control" value="Subject Teacher" />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-dark">Class Teacher</label>
                                            <input type="text" name="class_teacher" class="form-control" value="Class Teacher" />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-dark">Parent / Guardian</label>
                                            <input type="text" name="parent_guardian" class="form-control" value="Parent / Guardian" />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-dark">Principal Name</label>
                                            <input type="text" name="principal" class="form-control" value="Principal" />
                                        </div>
                                    @else
                                        <div class="col-12"><hr class="my-1" /></div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">Class Teacher Name</label>
                                            <input
                                                type="text"
                                                name="class_teacher"
                                                class="form-control"
                                                placeholder="Teacher's name"
                                            />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">Principal Name</label>
                                            <input
                                                type="text"
                                                name="principal"
                                                class="form-control"
                                                placeholder="Principal's name"
                                            />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- ─── BOTTOM ACTION / GENERATE PDF CARD ────────────────────────────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white border-top border-4 border-{{ $types[$type]['color'] }}">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-{{ $types[$type]['color'] }} bg-opacity-10 p-3" style="width: 54px; height: 54px; flex-shrink: 0;">
                                            <i data-lucide="{{ $types[$type]['icon'] }}" class="text-{{ $types[$type]['color'] }}" style="width: 1.6rem; height: 1.6rem"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                                Generate & Print {{ $types[$type]['title'] }}
                                            </h6>
                                            <p class="text-muted small mb-0">
                                                Opens preview in a new tab → use <kbd>Ctrl+P</kbd> to print or save as PDF. 
                                                <span class="d-none d-lg-inline text-muted ms-1">(Recommended: A4, Portrait, Margins: None, Background Graphics: Enabled)</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto justify-content-end">
                                        <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold">
                                            Cancel
                                        </a>
                                        <button
                                            type="submit"
                                            id="generate_btn"
                                            class="btn btn-{{ $types[$type]['color'] }} btn-lg px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                                            disabled
                                        >
                                            <i data-lucide="printer" style="width: 1.25rem; height: 1.25rem"></i>
                                            Generate PDF / Print
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($type === 'overall_performance')
                                    </form> <!-- End #manualCertForm -->
                                </div> <!-- End #tab-manual-pane -->

                                <!-- ─── TAB 2: DYNAMIC AUTO-FETCH ENTRY (SEPARATE FORM) ──────────────── -->
                                <div class="tab-pane fade" id="tab-dynamic-pane" role="tabpanel" aria-labelledby="dynamic-tab">
                                    <form id="dynamicCertForm" action="{{ route('certificates.print') }}" method="POST" target="_blank" novalidate>
                                        @csrf
                                        <input type="hidden" name="type" value="overall_performance" />
                                        <input type="hidden" name="entry_mode" value="dynamic" />
                                        <input type="hidden" name="session" id="dyn_session_hidden_input" />
                                    <!-- SECTION 1: SELECT ACADEMIC SESSION, CLASS & REGISTERED STUDENT -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                                        <div class="card-header bg-primary bg-opacity-10 py-3 border-bottom d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-primary">1</span>
                                                Select Academic Session, Class & Registered Student
                                            </h6>
                                            <span id="dyn_student_count" class="text-muted small"></span>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3 align-items-end">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Academic Session <span class="text-danger">*</span></label>
                                                    <select id="dyn_session_picker" class="form-select form-select-lg">
                                                        <option value="">— Select Academic Session —</option>
                                                        @foreach ($academicSessions as $sess)
                                                            <option value="{{ $sess->id }}" data-name="{{ $sess->session_name }}" {{ $loop->first ? 'selected' : '' }}>
                                                                Academic Session {{ $sess->session_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Select Class <span class="text-danger">*</span></label>
                                                    <select id="dyn_class_picker" class="form-select form-select-lg">
                                                        <option value="" selected>— Select Class —</option>
                                                        @foreach ($classes as $cls)
                                                            <option value="{{ $cls }}">{{ $cls }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <div id="dyn_student_search_wrap" style="display: none">
                                                        <label class="form-label fw-semibold text-dark">Search Student <span class="text-muted fw-normal">(by name, adm# or roll#)</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-white"><i data-lucide="search" style="width:.9rem;height:.9rem;"></i></span>
                                                            <input type="text" id="dyn_student_search_input" class="form-control" placeholder="Search student name, adm no..." />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12" id="dyn_students_loading" style="display: none">
                                                    <div class="text-center py-3 text-muted">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                        Fetching registered students for selected class...
                                                    </div>
                                                </div>
                                                <div class="col-12" id="dyn_no_students" style="display: none">
                                                    <div class="alert alert-warning mb-0 small">No students found for this class.</div>
                                                </div>
                                                <div class="col-12" id="dyn_student_select_wrapper">
                                                    <label class="form-label fw-semibold text-dark">Select Registered Student <span class="text-muted fw-normal">(Auto-fetches all module data from Database)</span></label>
                                                    <select id="dyn_student_dropdown" class="form-select form-select-lg student-select-box" size="5" disabled>
                                                        <option value="">— Select Class First —</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 2: STUDENT PARTICULARS & PROFILE INFORMATION -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                                        <div class="card-header bg-white py-3 border-bottom">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-secondary">2</span>
                                                Student Particulars & Profile Information <span class="text-muted fw-normal fs-6">(Auto-fills from dropdown or edit manually)</span>
                                            </h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-dark">Student Full Name <span class="text-danger">*</span></label>
                                                    <input type="text" id="dyn_student_name" name="student_name" class="form-control form-control-lg fw-bold text-dark" placeholder="Select student from list above..." />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-dark">Father's Name</label>
                                                    <input type="text" id="dyn_father_name" name="father_name" class="form-control form-control-lg" placeholder="Father's name..." />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Class Name</label>
                                                    <input type="text" id="dyn_class_name" name="class_name" class="form-control" placeholder="e.g. Class 5th" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Section</label>
                                                    <input type="text" id="dyn_section" name="section" class="form-control" placeholder="e.g. A" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Roll Number</label>
                                                    <input type="text" id="dyn_roll_no" name="roll_no" class="form-control" placeholder="e.g. 15" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Admission No.</label>
                                                    <input type="text" id="dyn_admission_no" name="admission_no" class="form-control" placeholder="e.g. ADM-102" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-dark">Date of Birth</label>
                                                    <input type="text" id="dyn_dob" name="date_of_birth" class="form-control" placeholder="e.g. 12 Oct 2015" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-dark">Student Photo URL / Path</label>
                                                    <input type="text" id="dyn_student_photo_url" name="student_photo_url" class="form-control" placeholder="Auto-filled when student is selected" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 3: AUTHORITY & CERTIFICATE DETAILS -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                                        <div class="card-header bg-white py-3 border-bottom">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-info">3</span>
                                                Authority & Certificate Details
                                            </h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Issue Date</label>
                                                    <input type="date" id="dyn_issue_date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Report Type</label>
                                                    <select id="dyn_report_type" name="report_type" class="form-select">
                                                        <option value="Daily">Daily Report</option>
                                                        <option value="Weekly">Weekly Report</option>
                                                        <option value="Monthly" selected>Monthly Progress Card</option>
                                                        <option value="Term">Term / Quarterly Card</option>
                                                        <option value="Other">Other / Special</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Date / Month Period</label>
                                                    <input type="text" id="dyn_date_period" name="date_period" class="form-control" value="{{ date('F Y') }}" placeholder="e.g. September 2026" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold text-dark">Focus Area / Subject</label>
                                                    <input type="text" id="dyn_focus_area" name="focus_area" class="form-control" value="General Academics & Character" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 4: PERFORMANCE TRACKER (EXAMINATION MODULE DB) -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-4 border-primary">
                                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-primary">4</span>
                                                <i data-lucide="bar-chart-2" class="text-primary me-1" style="width:1.1rem;height:1.1rem;"></i>
                                                Performance Tracker (Subject-Wise Assessment)
                                            </h6>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size:0.75rem;">
                                                <i data-lucide="database" style="width:0.8rem;height:0.8rem;" class="me-1"></i>
                                                Fetched from Examination Module DB (http://localhost:8000/examination)
                                            </span>
                                        </div>
                                        <div class="card-body p-4">
                                            <div id="dyn_exam_loading" style="display:none;" class="text-center py-4 text-muted">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                                Checking Examination database for student...
                                            </div>
                                            <div id="dyn_exam_content">
                                                <div class="alert alert-light border text-muted mb-0 small text-center py-3">
                                                    <i data-lucide="info" style="width:1.2rem;height:1.2rem;" class="me-1 text-primary"></i>
                                                    Select a registered student above to auto-fetch examination performance data from database table.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 5: SKILLS & ATTRIBUTES (SKILLS INSTITUTE DB) -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-4 border-success">
                                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-success">5</span>
                                                <i data-lucide="smile" class="text-success me-1" style="width:1.1rem;height:1.1rem;"></i>
                                                Skills & Attributes (Personal & Social Development)
                                            </h6>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.75rem;">
                                                <i data-lucide="database" style="width:0.8rem;height:0.8rem;" class="me-1"></i>
                                                Fetched from Skills Institute DB (http://localhost:8000/skills-institute)
                                            </span>
                                        </div>
                                        <div class="card-body p-4">
                                            <div id="dyn_skills_content">
                                                <div class="alert alert-light border text-muted mb-0 small text-center py-3">
                                                    <i data-lucide="info" style="width:1.2rem;height:1.2rem;" class="me-1 text-success"></i>
                                                    Select a registered student above to auto-fetch skills & personal development data from database table.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 6: STUDENT DISCIPLINE PERFORMANCE (DISCIPLINE MODULE DB) -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-4 border-warning">
                                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-warning text-dark">6</span>
                                                <i data-lucide="shield-check" class="text-warning me-1" style="width:1.1rem;height:1.1rem;"></i>
                                                Student Discipline Performance
                                            </h6>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="font-size:0.75rem;">
                                                <i data-lucide="database" style="width:0.8rem;height:0.8rem;" class="me-1"></i>
                                                Fetched from Discipline Module DB (http://localhost:8000/discipline)
                                            </span>
                                        </div>
                                        <div class="card-body p-4">
                                            <div id="dyn_discipline_content">
                                                <div class="alert alert-light border text-muted mb-0 small text-center py-3">
                                                    <i data-lucide="info" style="width:1.2rem;height:1.2rem;" class="me-1 text-warning"></i>
                                                    Select a registered student above to auto-fetch discipline performance data from database table.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 7: QURAN DEVELOPMENT & PERFORMANCE (QURAN MODULE DB) -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-4 border-info">
                                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-info">7</span>
                                                <i data-lucide="book-open" class="text-info me-1" style="width:1.1rem;height:1.1rem;"></i>
                                                Quran Development & Performance
                                            </h6>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="font-size:0.75rem;">
                                                <i data-lucide="database" style="width:0.8rem;height:0.8rem;" class="me-1"></i>
                                                Fetched from Quran Module DB (http://localhost:8000/quran-module)
                                            </span>
                                        </div>
                                        <div class="card-body p-4">
                                            <div id="dyn_quran_content">
                                                <div class="alert alert-light border text-muted mb-0 small text-center py-3">
                                                    <i data-lucide="info" style="width:1.2rem;height:1.2rem;" class="me-1 text-info"></i>
                                                    Select a registered student above to auto-fetch Quran module evaluation data from database table.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 8: REMARKS & ACTION PLAN -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                                        <div class="card-header bg-white py-3 border-bottom">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-secondary">8</span>
                                                Remarks & Future Action Plan
                                            </h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Teacher's Remarks</label>
                                                    <textarea id="dyn_teacher_remarks" name="teacher_remarks" class="form-control" rows="3" placeholder="Enter teacher remarks...">Demonstrates exemplary academic performance and excellent moral conduct in school.</textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Parent's Remarks</label>
                                                    <textarea id="dyn_parent_remarks" name="parent_remarks" class="form-control" rows="3" placeholder="Enter parent remarks...">Very satisfied with student progress and school environment.</textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Next Plan / Action Plan</label>
                                                    <textarea id="dyn_action_plan" name="action_plan" class="form-control" rows="3" placeholder="Enter next plan...">Continue reading practice, participate in upcoming Tajweed competition & sports gala.</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 9: SIGNATURES -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                                        <div class="card-header bg-white py-3 border-bottom">
                                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                <span class="step-badge bg-dark">9</span>
                                                Authority & Signatures <span class="text-muted fw-normal fs-6">(Leave blank for physical signature on print)</span>
                                            </h6>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Class Teacher Name</label>
                                                    <input type="text" id="dyn_class_teacher" name="class_teacher" class="form-control" placeholder="Leave blank or type teacher name..." />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Parent / Guardian Name</label>
                                                    <input type="text" id="dyn_parent_guardian" name="parent_guardian" class="form-control" placeholder="Leave blank or type parent name..." />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold text-dark">Principal Name</label>
                                                    <input type="text" id="dyn_principal" name="principal" class="form-control" placeholder="Leave blank or type principal name..." />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- DYNAMIC SUBMIT / ACTION CARD -->
                                    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white border-top border-4 border-primary">
                                        <div class="card-body p-4">
                                            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 p-3" style="width: 54px; height: 54px; flex-shrink: 0;">
                                                        <i data-lucide="printer" class="text-primary" style="width: 1.6rem; height: 1.6rem"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                                            Generate & Print Dynamic Report Card
                                                        </h6>
                                                        <p class="text-muted small mb-0">
                                                            Opens preview in a new tab with fetched database data → use <kbd>Ctrl+P</kbd> to print.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 w-100 w-md-auto justify-content-end">
                                                    <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold">
                                                        Cancel
                                                    </a>
                                                    <button
                                                        type="submit"
                                                        id="dyn_generate_btn"
                                                        class="btn btn-primary btn-lg px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2"
                                                        disabled
                                                    >
                                                        <i data-lucide="printer" style="width: 1.25rem; height: 1.25rem"></i>
                                                        Generate PDF / Print
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Container to dynamically inject hidden input fields -->
                                    <div id="dyn_hidden_payload_container"></div>
                                    </form> <!-- End #dynamicCertForm -->
                                </div> <!-- End #tab-dynamic-pane -->
                            </div> <!-- End #certTabContent -->
                        @endif
                    @endif
                </div>
            </div>
        @if ($type !== 'overall_performance')
        </form>
        @endif
    </div>
@endsection

@push ('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isExperienceType = @json ($type === 'experience');
            const isAppreciationType = @json ($type === 'appreciation');
            const staffPicker = document.getElementById('staff_picker');
            const expStaffName = document.getElementById('exp_staff_name');
            const expFatherName = document.getElementById('exp_father_name');
            const expCnicNo = document.getElementById('exp_cnic_no');
            const expDesignation = document.getElementById('exp_designation');
            const expFromDate = document.getElementById('exp_from_date');
            const expToDate = document.getElementById('exp_to_date');

            const sessionPicker = document.getElementById('session_picker');
            const classPicker = document.getElementById('class_picker');
            const studentStep = document.getElementById('student_step');
            const studentSelectWrapper = document.getElementById('student_select_wrapper');
            const studentDropdown = document.getElementById('student_dropdown');
            const studentSearchInput = document.getElementById('student_search_input');
            const studentsLoading = document.getElementById('students_loading');
            const noStudents = document.getElementById('no_students');
            const studentCount = document.getElementById('student_count');
            const classBadge = document.getElementById('class_badge');
            const studentPreview = document.getElementById('student_preview');
            const generateBtn = document.getElementById('generate_btn');
            const certForm = document.getElementById('certForm');
            const sessionInput = document.getElementById('session_input');

            if (isAppreciationType) {
                if (generateBtn) generateBtn.disabled = false;

                const radStudent = document.getElementById('app_type_student');
                const radStaff   = document.getElementById('app_type_staff');
                const studentWrap = document.getElementById('app_student_picker_wrap');
                const staffWrap   = document.getElementById('app_staff_picker_wrap');
                const awardPreset = document.getElementById('award_preset_select');
                const awardTitle  = document.getElementById('app_award_title');

                if (radStudent && radStaff) {
                    radStudent.addEventListener('change', function() {
                        if (this.checked) {
                            studentWrap.style.display = 'block';
                            staffWrap.style.display   = 'none';
                            if (awardPreset && (awardPreset.value === 'TEACHER OF THE MONTH' || awardPreset.value === 'TEACHER OF THE YEAR')) {
                                awardPreset.value = 'STUDENT OF THE MONTH';
                                if (awardTitle) awardTitle.value = 'STUDENT OF THE MONTH';
                            }
                        }
                    });
                    radStaff.addEventListener('change', function() {
                        if (this.checked) {
                            studentWrap.style.display = 'none';
                            staffWrap.style.display   = 'block';
                            if (awardPreset && (awardPreset.value === 'STUDENT OF THE MONTH' || awardPreset.value === 'STUDENT OF THE YEAR')) {
                                awardPreset.value = 'TEACHER OF THE MONTH';
                                if (awardTitle) awardTitle.value = 'TEACHER OF THE MONTH';
                            }
                        }
                    });
                }

                if (awardPreset && awardTitle) {
                    awardPreset.addEventListener('change', function() {
                        if (this.value !== 'CUSTOM') {
                            awardTitle.value = this.value;
                        }
                    });
                }

                const appStaffSearch = document.getElementById('app_staff_search_input');
                const appStaffPicker = document.getElementById('app_staff_picker');

                if (appStaffPicker) {
                    const origOptions = Array.from(appStaffPicker.options);
                    if (appStaffSearch) {
                        appStaffSearch.addEventListener('input', function() {
                            const q = this.value.toLowerCase().trim();
                            appStaffPicker.innerHTML = '';
                            const matching = origOptions.filter(opt => {
                                if (!opt.value) return true;
                                return opt.textContent.toLowerCase().includes(q) || (opt.dataset.name||'').toLowerCase().includes(q);
                            });
                            matching.forEach(opt => appStaffPicker.appendChild(opt.cloneNode(true)));
                        });
                    }

                    appStaffPicker.addEventListener('change', function() {
                        const opt = this.options[this.selectedIndex];
                        if (!opt || !opt.value) return;
                        const name   = opt.dataset.name || '';
                        const father = opt.dataset.father || '';
                        const desig  = opt.dataset.designation || '';
                        const photo  = opt.dataset.photo || '';

                        const recipName  = document.getElementById('app_recipient_name');
                        const recipFath  = document.getElementById('app_father_name');
                        const recipDesig = document.getElementById('app_designation');
                        const hPhotoUrl  = document.getElementById('h_student_photo_url');
                        const hName      = document.getElementById('h_student_name');

                        if (recipName) recipName.value = name;
                        if (recipFath) recipFath.value = father;
                        if (recipDesig) recipDesig.value = desig;
                        if (hName) hName.value = name;
                        if (hPhotoUrl) hPhotoUrl.value = photo;
                    });
                }
            }

            if (isExperienceType) {
                if (generateBtn) generateBtn.disabled = false;

                const staffSearchInput = document.getElementById('staff_search_input');
                const staffPicker      = document.getElementById('staff_picker');

                if (staffPicker) {
                    const originalStaffOptions = Array.from(staffPicker.options);

                    if (staffSearchInput) {
                        staffSearchInput.addEventListener('input', function () {
                            const q = this.value.toLowerCase().trim();
                            staffPicker.innerHTML = '';

                            const matching = originalStaffOptions.filter(opt => {
                                if (!opt.value) return true;
                                return opt.textContent.toLowerCase().includes(q) ||
                                       (opt.dataset.name || '').toLowerCase().includes(q) ||
                                       (opt.dataset.designation || '').toLowerCase().includes(q) ||
                                       (opt.dataset.cnic || '').toLowerCase().includes(q);
                            });

                            if (matching.length === 0 || (matching.length === 1 && !matching[0].value)) {
                                const noOpt = document.createElement('option');
                                noOpt.textContent = '— No matching staff members found —';
                                noOpt.disabled = true;
                                staffPicker.appendChild(noOpt);
                            } else {
                                matching.forEach(opt => staffPicker.appendChild(opt.cloneNode(true)));
                            }
                        });
                    }

                    function handleStaffSelect() {
                        const opt = staffPicker.options[staffPicker.selectedIndex];
                        if (!opt || !opt.value) return;

                        if (expStaffName)   expStaffName.value   = opt.dataset.name        || '';
                        if (expFatherName)  expFatherName.value  = opt.dataset.father      || '';
                        if (expCnicNo)      expCnicNo.value      = opt.dataset.cnic        || '';
                        if (expDesignation) expDesignation.value = opt.dataset.designation || '';
                        if (expFromDate)    expFromDate.value    = opt.dataset.joining     || '';
                        if (expToDate)      expToDate.value      = opt.dataset.leaving     || '';

                        const hPhotoUrl = document.getElementById('h_student_photo_url');
                        if (hPhotoUrl) hPhotoUrl.value = opt.dataset.photo || '';

                        staffPicker.style.borderColor = '#22c55e';
                    }

                    staffPicker.addEventListener('change', handleStaffSelect);
                    staffPicker.addEventListener('click',  handleStaffSelect);
                }

                certForm.addEventListener('submit', function (e) {
                    if (expStaffName && !expStaffName.value.trim()) {
                        e.preventDefault();
                        alert('⚠️ Please enter or select a Staff Member Name.');
                        expStaffName.focus();
                        return false;
                    }
                });
                return;
            }

            let allStudents = [];

            // ── Load students when session or class changes ──────────────────
            function loadStudents() {
                const sessionId = sessionPicker ? sessionPicker.value : '';
                const sessionOpt = sessionPicker ? sessionPicker.options[sessionPicker.selectedIndex] : null;
                const sessionName = sessionOpt && sessionOpt.dataset ? sessionOpt.dataset.name : '';
                const className = classPicker ? classPicker.value.trim() : '';

                if (sessionName && sessionInput) {
                    sessionInput.value = sessionName;
                }

                if (!className || !sessionId) {
                    if (studentDropdown) {
                        studentDropdown.disabled = true;
                        studentDropdown.innerHTML = '<option value="">— Select Class First —</option>';
                    }
                    const sWrap = document.getElementById('student_search_wrap');
                    if (sWrap) sWrap.style.display = 'none';
                    clearStudentPreview();
                    return;
                }

                if (studentDropdown) studentDropdown.innerHTML = '';
                if (noStudents) noStudents.style.display = 'none';
                if (studentsLoading) studentsLoading.style.display = 'block';
                if (studentSearchInput) studentSearchInput.value = '';
                clearStudentPreview();

                const url = `{{ route('certificates.students-by-class') }}?class_name=${encodeURIComponent(className)}&academic_session_id=${encodeURIComponent(sessionId)}&academic_session=${encodeURIComponent(sessionName)}`;

                fetch(url, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', Accept: 'application/json' },
                })
                    .then((r) => r.json())
                    .then((students) => {
                        if (studentsLoading) studentsLoading.style.display = 'none';
                        allStudents = students;

                        if (!students.length) {
                            if (noStudents) {
                                noStudents.innerHTML = `No students found for <strong>Session ${sessionName}</strong> in <strong>${className}</strong>.`;
                                noStudents.style.display = 'block';
                            }
                            if (studentCount) studentCount.textContent = '0 students';
                            if (studentDropdown) {
                                studentDropdown.disabled = true;
                                studentDropdown.innerHTML = '<option value="">— No students in this class —</option>';
                            }
                            return;
                        }

                        if (studentCount) studentCount.textContent = students.length + ' student' + (students.length > 1 ? 's' : '');
                        const sWrap = document.getElementById('student_search_wrap');
                        if (sWrap) sWrap.style.display = 'block';
                        if (studentDropdown) studentDropdown.disabled = false;
                        populateStudentDropdown(students);
                    })
                    .catch(() => {
                        if (studentsLoading) studentsLoading.style.display = 'none';
                        if (noStudents) {
                            noStudents.innerHTML = '<span class="text-danger">Failed to load students. Please try again.</span>';
                            noStudents.style.display = 'block';
                        }
                    });
            }

            if (sessionPicker) sessionPicker.addEventListener('change', loadStudents);
            if (classPicker) classPicker.addEventListener('change', loadStudents);

            if (sessionPicker && classPicker && sessionPicker.value && classPicker.value) {
                loadStudents();
            }

            // ── Populate dropdown list ────────────────────────────────────
            function populateStudentDropdown(students) {
                if (!studentDropdown) return;
                studentDropdown.innerHTML = '';

                if (!students.length) {
                    const opt = document.createElement('option');
                    opt.textContent = '— No matching students found —';
                    opt.disabled = true;
                    studentDropdown.appendChild(opt);
                    return;
                }

                // Placeholder option
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = `— Select Student (${students.length} found) —`;
                placeholder.disabled = true;
                placeholder.selected = true;
                studentDropdown.appendChild(placeholder);

                students.forEach((s) => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    const sectionLabel = s.section_name ? `, Sec: ${s.section_name}` : '';
                    const statusLabel = s.status ? ` (${s.status})` : '';
                    opt.textContent = `${s.student_name}${statusLabel}  —  Father: ${s.father_name || 'N/A'}  (Adm# ${s.admission_no || 'N/A'}, Roll# ${s.roll_no || 'N/A'}${sectionLabel})`;
                    opt.dataset.json = JSON.stringify(s);
                    studentDropdown.appendChild(opt);
                });
            }

            // ── Search filter ─────────────────────────────────────────────
            if (studentSearchInput) {
                studentSearchInput.addEventListener('input', function () {
                    const q = this.value.toLowerCase().trim();
                    const filtered = !q
                        ? allStudents
                        : allStudents.filter(
                              (s) =>
                                  s.student_name.toLowerCase().includes(q) ||
                                  (s.status || '').toLowerCase().includes(q) ||
                                  (s.father_name || '').toLowerCase().includes(q) ||
                                  (s.admission_no || '').toLowerCase().includes(q) ||
                                  String(s.roll_no || '')
                                      .toLowerCase()
                                      .includes(q),
                          );
                    populateStudentDropdown(filtered);
                    clearStudentPreview();
                });
            }

            // ── Student selection: listen to BOTH 'change' AND 'click' ───
            function handleStudentSelect() {
                const selectedOpt = studentDropdown.options[studentDropdown.selectedIndex];
                if (!selectedOpt || !selectedOpt.dataset || !selectedOpt.dataset.json) {
                    clearStudentPreview();
                    return;
                }

                let s;
                try {
                    s = JSON.parse(selectedOpt.dataset.json);
                } catch (e) {
                    clearStudentPreview();
                    return;
                }

                // ── Populate ALL form fields (both visible and hidden) ──
                const inpName  = document.getElementById('input_student_name');
                const inpFath  = document.getElementById('input_father_name');
                const inpAdm   = document.getElementById('input_admission_no');
                const inpRoll  = document.getElementById('input_roll_no');
                const inpDob   = document.getElementById('input_dob');
                const inpClass = document.getElementById('input_class_name');
                const inpSec   = document.getElementById('input_section');
                const inpPhoto = document.getElementById('input_student_photo_url');

                if (inpName)  inpName.value  = s.student_name || '';
                if (inpFath)  inpFath.value  = s.father_name || '';
                if (inpAdm)   inpAdm.value   = s.admission_no || '';
                if (inpRoll)  inpRoll.value  = s.roll_no || '';
                if (inpDob)   inpDob.value   = s.date_of_birth || '';
                if (inpClass) inpClass.value = s.class_name || '';
                if (inpSec)   inpSec.value   = s.section_name || '';
                if (inpPhoto) inpPhoto.value = s.student_photo_url || '';

                // Fallback for hidden fields
                if (document.getElementById('h_student_name')) document.getElementById('h_student_name').value = s.student_name || '';
                if (document.getElementById('h_father_name'))  document.getElementById('h_father_name').value  = s.father_name || '';

                if (isAppreciationType) {
                    const recipName  = document.getElementById('app_recipient_name');
                    const recipFath  = document.getElementById('app_father_name');
                    const recipDesig = document.getElementById('app_designation');
                    if (recipName)  recipName.value  = s.student_name || '';
                    if (recipFath)  recipFath.value  = s.father_name || '';
                    if (recipDesig) {
                        const rawCls = (s.class_name || '').replace(/^class\s+/i, '');
                        recipDesig.value = rawCls ? 'Class ' + rawCls + (s.section_name ? ' (' + s.section_name + ')' : '') : '';
                    }
                }

                if (generateBtn) generateBtn.disabled = false;

                // Highlight the selected option visually
                if (studentDropdown) studentDropdown.style.borderColor = '#22c55e';
            }

            if (studentDropdown) {
                studentDropdown.addEventListener('change', handleStudentSelect);
                studentDropdown.addEventListener('click', handleStudentSelect);
            }

            // Listen to direct manual typing in Student Name input
            const inpStudentName = document.getElementById('input_student_name');
            if (inpStudentName) {
                // If there's already a value on page load
                if (inpStudentName.value.trim() !== '' && generateBtn) {
                    generateBtn.disabled = false;
                }
                inpStudentName.addEventListener('input', function () {
                    if (generateBtn) {
                        generateBtn.disabled = !this.value.trim();
                    }
                });
            }

            const manualCertForm = document.getElementById('manualCertForm');
            const dynamicCertForm = document.getElementById('dynamicCertForm');

            if (manualCertForm) {
                manualCertForm.addEventListener('submit', function (e) {
                    const inpN = document.querySelector('#manualCertForm [name="student_name"]');
                    const hidN = document.getElementById('h_student_name');
                    const studentName = (inpN && inpN.value.trim()) ? inpN.value.trim() : (hidN ? hidN.value.trim() : '');

                    if (!studentName) {
                        e.preventDefault();
                        alert('⚠️ Please enter or select a Student Name before generating the certificate.');
                        if (inpN) inpN.focus();
                        return false;
                    }
                });
            }

            if (dynamicCertForm) {
                dynamicCertForm.addEventListener('submit', function (e) {
                    const dynN = document.getElementById('dyn_student_name');
                    const studentName = dynN ? dynN.value.trim() : '';

                    const dynSess = document.getElementById('dyn_session_picker');
                    const dynSessHidden = document.getElementById('dyn_session_hidden_input');
                    if (dynSess && dynSessHidden && dynSess.selectedIndex >= 0) {
                        const sessionOpt = dynSess.options[dynSess.selectedIndex];
                        dynSessHidden.value = sessionOpt && sessionOpt.dataset ? sessionOpt.dataset.name : dynSess.value;
                    }

                    if (!studentName) {
                        e.preventDefault();
                        alert('⚠️ Please select a student from the dropdown or enter student name.');
                        if (dynN) dynN.focus();
                        return false;
                    }
                });
            }

            if (certForm) {
                certForm.addEventListener('submit', function (e) {
                    if (isExperienceType) return;
                    if (isAppreciationType) {
                        const recipName = document.getElementById('app_recipient_name');
                        if (recipName && !recipName.value.trim()) {
                            e.preventDefault();
                            alert('⚠️ Please enter or select a Recipient Name for Appreciation Certificate.');
                            recipName.focus();
                            return false;
                        }
                        return;
                    }

                    const inpN = document.querySelector('[name="student_name"]');
                    const hidN = document.getElementById('h_student_name');
                    const studentName = (inpN && inpN.value.trim()) ? inpN.value.trim() : (hidN ? hidN.value.trim() : '');

                    if (!studentName) {
                        e.preventDefault();
                        alert('⚠️ Please enter or select a Student Name before generating the certificate.');
                        if (inpN) inpN.focus();
                        return false;
                    }
                });
            }

            // ── DYNAMIC TAB MODULE DATA AUTO-FETCH LOGIC ───────────────────
            const dynSessionPicker = document.getElementById('dyn_session_picker');
            const dynClassPicker = document.getElementById('dyn_class_picker');
            const dynStudentDropdown = document.getElementById('dyn_student_dropdown');
            const dynStudentSearchInput = document.getElementById('dyn_student_search_input');
            const dynStudentsLoading = document.getElementById('dyn_students_loading');
            const dynNoStudents = document.getElementById('dyn_no_students');
            const dynStudentCount = document.getElementById('dyn_student_count');
            const dynGenerateBtn = document.getElementById('dyn_generate_btn');

            let dynAllStudents = [];

            function loadDynStudents() {
                if (!dynClassPicker || !dynSessionPicker) return;
                const sessionId = dynSessionPicker.value;
                const sessionOpt = dynSessionPicker.options[dynSessionPicker.selectedIndex];
                const sessionName = sessionOpt && sessionOpt.dataset ? sessionOpt.dataset.name : '';
                const className = dynClassPicker.value.trim();

                if (!className || !sessionId) {
                    if (dynStudentDropdown) {
                        dynStudentDropdown.disabled = true;
                        dynStudentDropdown.innerHTML = '<option value="">— Select Class First —</option>';
                    }
                    const sWrap = document.getElementById('dyn_student_search_wrap');
                    if (sWrap) sWrap.style.display = 'none';
                    return;
                }

                if (dynStudentDropdown) dynStudentDropdown.innerHTML = '';
                if (dynNoStudents) dynNoStudents.style.display = 'none';
                if (dynStudentsLoading) dynStudentsLoading.style.display = 'block';
                if (dynStudentSearchInput) dynStudentSearchInput.value = '';

                const url = `{{ route('certificates.students-by-class') }}?class_name=${encodeURIComponent(className)}&academic_session_id=${encodeURIComponent(sessionId)}&academic_session=${encodeURIComponent(sessionName)}`;

                fetch(url, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', Accept: 'application/json' } })
                    .then(r => r.json())
                    .then(students => {
                        if (dynStudentsLoading) dynStudentsLoading.style.display = 'none';
                        dynAllStudents = students;

                        if (!students.length) {
                            if (dynNoStudents) {
                                dynNoStudents.innerHTML = `No students found for <strong>Session ${sessionName}</strong> in <strong>${className}</strong>.`;
                                dynNoStudents.style.display = 'block';
                            }
                            if (dynStudentCount) dynStudentCount.textContent = '0 students';
                            if (dynStudentDropdown) {
                                dynStudentDropdown.disabled = true;
                                dynStudentDropdown.innerHTML = '<option value="">— No students in this class —</option>';
                            }
                            return;
                        }

                        if (dynStudentCount) dynStudentCount.textContent = students.length + ' student' + (students.length > 1 ? 's' : '');
                        const sWrap = document.getElementById('dyn_student_search_wrap');
                        if (sWrap) sWrap.style.display = 'block';
                        if (dynStudentDropdown) dynStudentDropdown.disabled = false;
                        populateDynStudentDropdown(students);
                    })
                    .catch(() => {
                        if (dynStudentsLoading) dynStudentsLoading.style.display = 'none';
                        if (dynNoStudents) {
                            dynNoStudents.innerHTML = '<span class="text-danger">Failed to load students. Please try again.</span>';
                            dynNoStudents.style.display = 'block';
                        }
                    });
            }

            if (dynSessionPicker) dynSessionPicker.addEventListener('change', loadDynStudents);
            if (dynClassPicker) dynClassPicker.addEventListener('change', loadDynStudents);

            if (dynSessionPicker && dynClassPicker && dynSessionPicker.value && dynClassPicker.value) {
                loadDynStudents();
            }

            function populateDynStudentDropdown(students) {
                if (!dynStudentDropdown) return;
                dynStudentDropdown.innerHTML = '';

                if (!students.length) {
                    const opt = document.createElement('option');
                    opt.textContent = '— No matching students found —';
                    opt.disabled = true;
                    dynStudentDropdown.appendChild(opt);
                    return;
                }

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = `— Select Student (${students.length} found) —`;
                placeholder.disabled = true;
                placeholder.selected = true;
                dynStudentDropdown.appendChild(placeholder);

                students.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    const sectionLabel = s.section_name ? `, Sec: ${s.section_name}` : '';
                    opt.textContent = `${s.student_name}  —  Father: ${s.father_name || 'N/A'}  (Adm# ${s.admission_no || 'N/A'}, Roll# ${s.roll_no || 'N/A'}${sectionLabel})`;
                    opt.dataset.json = JSON.stringify(s);
                    dynStudentDropdown.appendChild(opt);
                });
            }

            if (dynStudentSearchInput) {
                dynStudentSearchInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    const filtered = !q ? dynAllStudents : dynAllStudents.filter(s =>
                        s.student_name.toLowerCase().includes(q) ||
                        (s.father_name || '').toLowerCase().includes(q) ||
                        (s.admission_no || '').toLowerCase().includes(q) ||
                        String(s.roll_no || '').toLowerCase().includes(q)
                    );
                    populateDynStudentDropdown(filtered);
                });
            }

            // Handles student selection in Dynamic Tab
            function handleDynStudentSelect() {
                if (!dynStudentDropdown) return;
                const selectedOpt = dynStudentDropdown.options[dynStudentDropdown.selectedIndex];
                if (!selectedOpt || !selectedOpt.dataset || !selectedOpt.dataset.json) return;

                let s;
                try { s = JSON.parse(selectedOpt.dataset.json); } catch(e) { return; }

                // Auto-fill Section 2 Profile Information
                if (document.getElementById('dyn_student_name'))      document.getElementById('dyn_student_name').value      = s.student_name || '';
                if (document.getElementById('dyn_father_name'))       document.getElementById('dyn_father_name').value       = s.father_name || '';
                if (document.getElementById('dyn_class_name'))        document.getElementById('dyn_class_name').value        = s.class_name || '';
                if (document.getElementById('dyn_section'))           document.getElementById('dyn_section').value           = s.section_name || '';
                if (document.getElementById('dyn_roll_no'))            document.getElementById('dyn_roll_no').value            = s.roll_no || '';
                if (document.getElementById('dyn_admission_no'))      document.getElementById('dyn_admission_no').value      = s.admission_no || '';
                if (document.getElementById('dyn_dob'))               document.getElementById('dyn_dob').value               = s.date_of_birth || '';
                if (document.getElementById('dyn_student_photo_url')) document.getElementById('dyn_student_photo_url').value = s.student_photo_url || '';

                if (dynGenerateBtn) dynGenerateBtn.disabled = false;
                if (generateBtn) generateBtn.disabled = false;

                // Call AJAX to fetch DB records from Examination, Skills, Discipline, Quran
                fetchDynStudentModuleDetails(s.id, s.admission_no);
            }

            if (dynStudentDropdown) {
                dynStudentDropdown.addEventListener('change', handleDynStudentSelect);
                dynStudentDropdown.addEventListener('click',  handleDynStudentSelect);
            }

            function fetchDynStudentModuleDetails(studentId, admissionNo) {
                const examLoading = document.getElementById('dyn_exam_loading');
                const examContent = document.getElementById('dyn_exam_content');
                const skillsContent = document.getElementById('dyn_skills_content');
                const disciplineContent = document.getElementById('dyn_discipline_content');
                const quranContent = document.getElementById('dyn_quran_content');
                const hiddenContainer = document.getElementById('dyn_hidden_payload_container');

                if (examLoading) examLoading.style.display = 'block';

                const url = `{{ route('certificates.student-module-details') }}?student_id=${encodeURIComponent(studentId)}&admission_no=${encodeURIComponent(admissionNo || '')}`;

                fetch(url, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', Accept: 'application/json' } })
                    .then(r => r.json())
                    .then(res => {
                        if (examLoading) examLoading.style.display = 'none';
                        if (!res || !res.success) return;

                        if (hiddenContainer) hiddenContainer.innerHTML = '';

                        // ── 1. SECTION 4: EXAMINATION PERFORMANCE TRACKER ─────────────────────
                        if (examContent) {
                            if (res.examination && res.examination.length > 0) {
                                let html = `<div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white align-middle mb-0" style="font-size:0.875rem;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Subject</th>
                                                <th class="text-center">Exam Title</th>
                                                <th class="text-center">Marks Obtained</th>
                                                <th class="text-center">Rating</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                                res.examination.forEach((ex, idx) => {
                                    const ratingBadge = ex.rating === 'excellent' ? '<span class="badge bg-success">★ Excellent</span>' :
                                                        ex.rating === 'good' ? '<span class="badge bg-primary">👍 Good</span>' :
                                                        ex.rating === 'average' ? '<span class="badge bg-warning text-dark">Average</span>' :
                                                        '<span class="badge bg-danger">! Needs Imp.</span>';

                                    html += `<tr>
                                        <td class="fw-bold text-dark">${ex.subject}</td>
                                        <td class="text-center text-muted small">${ex.exam_title}</td>
                                        <td class="text-center fw-bold">${ex.is_absent ? '<span class="text-danger">Absent</span>' : ex.marks_obtained + ' / ' + ex.total_marks + ' (' + ex.percentage + '%)'}</td>
                                        <td class="text-center">${ratingBadge}</td>
                                        <td>${ex.remarks || ''}</td>
                                    </tr>`;

                                    if (hiddenContainer) {
                                        hiddenContainer.innerHTML += `
                                            <input type="hidden" name="performance_tracker[${idx}][subject]" value="${ex.subject}">
                                            <input type="hidden" name="performance_tracker[${idx}][exam_title]" value="${ex.exam_title || ''}">
                                            <input type="hidden" name="performance_tracker[${idx}][marks_obtained]" value="${ex.marks_obtained || ''}">
                                            <input type="hidden" name="performance_tracker[${idx}][total_marks]" value="${ex.total_marks || ''}">
                                            <input type="hidden" name="performance_tracker[${idx}][percentage]" value="${ex.percentage || ''}">
                                            <input type="hidden" name="performance_tracker[${idx}][is_absent]" value="${ex.is_absent ? '1' : '0'}">
                                            <input type="hidden" name="performance_tracker[${idx}][rating]" value="${ex.rating}">
                                            <input type="hidden" name="performance_tracker[${idx}][remarks]" value="${ex.remarks || ''}">
                                        `;
                                    }
                                });
                                html += `</tbody></table></div>`;
                                examContent.innerHTML = html;
                            } else {
                                examContent.innerHTML = `<div class="alert alert-warning mb-0 small border-warning">
                                    <i data-lucide="alert-circle" style="width:1.1rem;height:1.1rem;" class="me-1"></i>
                                    No examination mark records found in database table (<strong>http://localhost:8000/examination</strong>) for this student.
                                </div>`;
                            }
                        }

                        // ── 2. SECTION 5: SKILLS & ATTRIBUTES ─────────────────────────────────
                        if (skillsContent) {
                            if (res.skills && res.skills.length > 0) {
                                let html = `<div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white align-middle mb-0" style="font-size:0.875rem;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Skill Area</th>
                                                <th>Category</th>
                                                <th class="text-center">Star Rating / Badge</th>
                                                <th>Instructor Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                                res.skills.forEach((sk, idx) => {
                                    html += `<tr>
                                        <td class="fw-bold text-dark">${sk.skill}</td>
                                        <td><span class="badge bg-light text-dark border">${sk.category}</span></td>
                                        <td class="text-center fw-bold text-warning">${sk.star_rating} ★ (${sk.badge_level})</td>
                                        <td>${sk.remarks || ''}</td>
                                    </tr>`;

                                    if (hiddenContainer) {
                                        hiddenContainer.innerHTML += `
                                            <input type="hidden" name="skills_attributes[${idx}][skill]" value="${sk.skill}">
                                            <input type="hidden" name="skills_attributes[${idx}][category]" value="${sk.category || ''}">
                                            <input type="hidden" name="skills_attributes[${idx}][star_rating]" value="${sk.star_rating || ''}">
                                            <input type="hidden" name="skills_attributes[${idx}][badge_level]" value="${sk.badge_level || ''}">
                                            <input type="hidden" name="skills_attributes[${idx}][rating]" value="${sk.rating}">
                                            <input type="hidden" name="skills_attributes[${idx}][remarks]" value="${sk.remarks || ''}">
                                        `;
                                    }
                                });
                                html += `</tbody></table></div>`;
                                skillsContent.innerHTML = html;
                            } else {
                                skillsContent.innerHTML = `<div class="alert alert-warning mb-0 small border-warning">
                                    <i data-lucide="alert-circle" style="width:1.1rem;height:1.1rem;" class="me-1"></i>
                                    No skill assessment records found in database table (<strong>http://localhost:8000/skills-institute</strong>) for this student.
                                </div>`;
                            }
                        }

                        // ── 3. SECTION 6: DISCIPLINE PERFORMANCE ──────────────────────────────
                        if (disciplineContent) {
                            if (res.discipline && res.discipline.length > 0) {
                                let html = `<div class="row g-2">`;
                                res.discipline.forEach((d, idx) => {
                                    html += `<div class="col-md-6">
                                        <div class="border rounded-3 p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold text-dark mb-0">${d.title}</h6>
                                                <span class="badge bg-warning text-dark fw-bold">${d.star_rating} ★</span>
                                            </div>
                                            <p class="text-muted small mb-1">Category: <strong>${d.category}</strong> ${d.entry_date ? '| Date: ' + d.entry_date : ''}</p>
                                            <p class="small text-dark mb-0">${d.remarks ? '<em>"' + d.remarks + '"</em>' : 'Score: ' + d.obtained + '/' + d.total_score}</p>
                                        </div>
                                    </div>`;

                                    if (hiddenContainer) {
                                        hiddenContainer.innerHTML += `
                                            <input type="hidden" name="discipline[${idx}][title]" value="${d.title || ''}">
                                            <input type="hidden" name="discipline[${idx}][category]" value="${d.category || ''}">
                                            <input type="hidden" name="discipline[${idx}][star_rating]" value="${d.star_rating || ''}">
                                            <input type="hidden" name="discipline[${idx}][obtained]" value="${d.obtained || ''}">
                                            <input type="hidden" name="discipline[${idx}][total_score]" value="${d.total_score || ''}">
                                            <input type="hidden" name="discipline[${idx}][remarks]" value="${d.remarks || ''}">
                                        `;
                                    }
                                });
                                html += `</div>`;
                                disciplineContent.innerHTML = html;
                            } else {
                                disciplineContent.innerHTML = `<div class="alert alert-warning mb-0 small border-warning">
                                    <i data-lucide="alert-circle" style="width:1.1rem;height:1.1rem;" class="me-1"></i>
                                    No discipline evaluation records found in database table (<strong>http://localhost:8000/discipline</strong>) for this student.
                                </div>`;
                            }
                        }

                        // ── 4. SECTION 7: QURAN DEVELOPMENT & PERFORMANCE ──────────────────────
                        if (quranContent) {
                            if (res.quran && res.quran.length > 0) {
                                let html = `<div class="row g-2">`;
                                res.quran.forEach((q, idx) => {
                                    html += `<div class="col-md-6">
                                        <div class="border rounded-3 p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="fw-bold text-dark mb-0">Category: ${q.category}</h6>
                                                <span class="badge bg-success">${q.status}</span>
                                            </div>
                                            <p class="text-muted small mb-1">
                                                ${q.para_no ? 'Para #: <strong>' + q.para_no + '</strong> ' : ''}
                                                ${q.surah_name ? '| Surah: <strong>' + q.surah_name + '</strong> ' : ''}
                                                ${q.ayah_range ? '(' + q.ayah_range + ')' : ''}
                                            </p>
                                            <p class="small text-dark mb-0">Score: <strong>${q.score}</strong> | Parahs Memorized: <strong>${q.total_memorized || 0}</strong> | Mistakes: <strong>${q.mistakes || 0}</strong></p>
                                            ${q.notes ? '<p class="small text-muted mb-0 mt-1">Notes: ' + q.notes + '</p>' : ''}
                                        </div>
                                    </div>`;

                                    if (hiddenContainer) {
                                        hiddenContainer.innerHTML += `
                                            <input type="hidden" name="quran[${idx}][category]" value="${q.category || ''}">
                                            <input type="hidden" name="quran[${idx}][status]" value="${q.status || ''}">
                                            <input type="hidden" name="quran[${idx}][para_no]" value="${q.para_no || ''}">
                                            <input type="hidden" name="quran[${idx}][surah_name]" value="${q.surah_name || ''}">
                                            <input type="hidden" name="quran[${idx}][score]" value="${q.score || ''}">
                                            <input type="hidden" name="quran[${idx}][total_memorized]" value="${q.total_memorized || 0}">
                                            <input type="hidden" name="quran[${idx}][mistakes]" value="${q.mistakes || 0}">
                                            <input type="hidden" name="quran[${idx}][notes]" value="${q.notes || ''}">
                                        `;
                                    }
                                });
                                html += `</div>`;
                                quranContent.innerHTML = html;
                            } else {
                                quranContent.innerHTML = `<div class="alert alert-warning mb-0 small border-warning">
                                    <i data-lucide="alert-circle" style="width:1.1rem;height:1.1rem;" class="me-1"></i>
                                    No Quran module evaluation records found in database table (<strong>http://localhost:8000/quran-module</strong>) for this student.
                                </div>`;
                            }
                        }
                    })
                    .catch(() => {
                        if (examLoading) examLoading.style.display = 'none';
                    });
            }

            if (dynGenerateBtn) {
                dynGenerateBtn.addEventListener('click', function (e) {
                    if (this.disabled) {
                        e.preventDefault();
                        alert('⚠️ Please select a student from the dropdown or enter student name.');
                        return false;
                    }
                    syncDynamicValuesToForm();
                });
            }

            // ── Clear helper ─────────────────────────────────────────────
            function clearStudentPreview() {
                if (studentDropdown) studentDropdown.style.borderColor = '';

                [
                    'h_student_name',
                    'h_father_name',
                    'h_admission_no',
                    'h_roll_no',
                    'h_dob',
                    'h_class_name',
                    'h_section',
                    'h_student_photo_url',
                ].forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
            }
        });
    </script>
@endpush
