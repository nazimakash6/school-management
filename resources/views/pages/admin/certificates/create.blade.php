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

        <form id="certForm" action="{{ route('certificates.print') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}" />

            {{-- Hidden fields populated by JS --}}
            <input type="hidden" name="student_name" id="h_student_name" />
            <input type="hidden" name="father_name" id="h_father_name" />
            <input type="hidden" name="admission_no" id="h_admission_no" />
            <input type="hidden" name="roll_no" id="h_roll_no" />
            <input type="hidden" name="date_of_birth" id="h_dob" />
            <input type="hidden" name="class_name" id="h_class_name" />
            <input type="hidden" name="section" id="h_section" />
            <input type="hidden" name="session" id="session_input" />
            <input type="hidden" name="student_photo_url" id="h_student_photo_url" />

            <div class="row g-4">
                <div class="col-lg-8">
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
                        <!-- ─── STEP 1 & 2 FOR STUDENT CERTIFICATES: SELECT ACADEMIC SESSION & CLASS ──────────────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge">1</span>
                                    Select Academic Session & Class
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark"
                                            >Academic Session <span class="text-danger">*</span></label
                                        >
                                        <select id="session_picker" class="form-select form-select-lg" required>
                                            <option value="">— Select Academic Session —</option>
                                            @foreach ($academicSessions as $sess)
                                                <option value="{{ $sess->id }}" data-name="{{ $sess->session_name }}">
                                                    Academic Session {{ $sess->session_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark"
                                            >Class <span class="text-danger">*</span></label
                                        >
                                        <select id="class_picker" class="form-select form-select-lg" required>
                                            <option value="">— Select Class —</option>
                                            @foreach ($classes as $cls)
                                                <option value="{{ $cls }}">{{ $cls }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ─── STEP 2: SEARCH & SELECT STUDENT DROPDOWN ────────────────── -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4" id="student_step" style="display: none">
                            <div
                                class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between"
                            >
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge">2</span>
                                    Select Student Dropdown
                                    <span
                                        id="class_badge"
                                        class="badge bg-primary bg-opacity-10 text-primary border ms-1"
                                    ></span>
                                </h6>
                                <span id="student_count" class="text-muted small"></span>
                            </div>
                            <div class="card-body p-4">
                                <!-- Loading spinner -->
                                <div id="students_loading" class="text-center py-3" style="display: none">
                                    <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                                    <span class="text-muted small">Loading students...</span>
                                </div>

                                <div id="student_select_wrapper" style="display: none">
                                    <!-- Search Bar for Student Dropdown -->
                                    <label class="form-label fw-semibold text-dark"
                                        >Search Student
                                        <span class="text-muted fw-normal"
                                            >(filter list by name, father name, roll #)</span
                                        ></label
                                    >
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-white"
                                            ><i data-lucide="search" style="width: 0.95rem; height: 0.95rem"></i
                                        ></span>
                                        <input
                                            type="text"
                                            id="student_search_input"
                                            class="form-control form-control-lg"
                                            placeholder="Type student name, father name, or roll no to search..."
                                        />
                                    </div>

                                    <!-- Student Dropdown Select -->
                                    <label class="form-label fw-semibold text-dark"
                                        >Select Student <span class="text-danger">*</span></label
                                    >
                                    <select
                                        id="student_dropdown"
                                        class="form-select form-select-lg student-select-box"
                                        size="5"
                                    >
                                        <option value="">— Select a student from list —</option>
                                    </select>
                                    <div class="form-text mt-1 text-muted">
                                        Click any student from the dropdown list to select.
                                    </div>
                                </div>

                                <div id="no_students" class="text-center py-4 text-muted small" style="display: none">
                                    No students found for this selection.
                                </div>
                            </div>
                        </div>

                        <!-- ─── STEP 3: SELECTED STUDENT PREVIEW ─────────────── -->
                        <div
                            class="card border-0 shadow-sm rounded-3 mb-4 border-start border-success border-4"
                            id="student_preview"
                            style="display: none"
                        >
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="step-badge" style="background: #22c55e">✓</span>
                                    Selected Student Details
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Name of Student</label>
                                        <div class="fw-bold text-dark fs-6" id="preview_name">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Father's Name</label>
                                        <div class="fw-bold text-dark fs-6" id="preview_father">—</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted small fw-semibold">Admission No.</label>
                                        <div class="fw-semibold" id="preview_admno">—</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted small fw-semibold">Roll No.</label>
                                        <div class="fw-semibold" id="preview_roll">—</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted small fw-semibold">Date of Birth</label>
                                        <div class="fw-semibold" id="preview_dob">—</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted small fw-semibold">Class & Section</label>
                                        <div class="fw-semibold" id="preview_class">—</div>
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
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 80px">
                        <div class="card-body p-4 text-center">
                            <div
                                class="mx-auto mb-3 rounded-3 d-flex align-items-center justify-content-center bg-{{ $types[$type]['color'] }} bg-opacity-10"
                                style="width: 64px; height: 64px"
                            >
                                <i
                                    data-lucide="{{ $types[$type]['icon'] }}"
                                    class="text-{{ $types[$type]['color'] }}"
                                    style="width: 1.8rem; height: 1.8rem"
                                ></i>
                            </div>
                            <h6 class="fw-bold text-dark">{{ $types[$type]['title'] }}</h6>
                            <p class="text-muted small mb-4">Noor Ul Huda Superior School</p>

                            <button
                                type="submit"
                                id="generate_btn"
                                class="btn btn-{{ $types[$type]['color'] }} btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mb-3"
                                disabled
                            >
                                <i data-lucide="printer" style="width: 1.2rem; height: 1.2rem"></i>
                                Generate PDF / Print
                            </button>
                            <p class="text-muted small mb-4">Opens in a new tab → use <kbd>Ctrl+P</kbd> to save as PDF</p>

                            <div class="text-start border rounded-3 p-3 bg-light">
                                <div class="fw-semibold text-dark small mb-2">🖨️ Printing Tips</div>
                                <ul class="text-muted small mb-0 ps-3" style="line-height: 2">
                                    <li>Use <strong>A4</strong> paper size</li>
                                    <li>Set margins to <strong>None</strong></li>
                                    <li>Enable <strong>Background Graphics</strong></li>
                                    <li>Use <strong>Portrait</strong> orientation</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
                    if (studentStep) studentStep.style.display = 'none';
                    clearStudentPreview();
                    return;
                }

                if (studentDropdown) studentDropdown.innerHTML = '';
                if (studentSelectWrapper) studentSelectWrapper.style.display = 'none';
                if (noStudents) noStudents.style.display = 'none';
                if (studentsLoading) studentsLoading.style.display = 'block';
                if (studentStep) studentStep.style.display = 'block';
                if (classBadge) classBadge.textContent = `${sessionName} — ${className}`;
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
                            return;
                        }

                        if (studentCount) studentCount.textContent = students.length + ' student' + (students.length > 1 ? 's' : '');
                        if (studentSelectWrapper) studentSelectWrapper.style.display = 'block';
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

            // ── Populate dropdown list ────────────────────────────────────
            function populateStudentDropdown(students) {
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
                placeholder.textContent = '— Click a student below to select —';
                placeholder.disabled = true;
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

                // ── Populate ALL hidden form fields ──
                document.getElementById('h_student_name').value = s.student_name || '';
                document.getElementById('h_father_name').value = s.father_name || '';
                document.getElementById('h_admission_no').value = s.admission_no || '';
                document.getElementById('h_roll_no').value = s.roll_no || '';
                document.getElementById('h_dob').value = s.date_of_birth || '';
                document.getElementById('h_class_name').value = s.class_name || '';
                document.getElementById('h_section').value = s.section_name || '';
                document.getElementById('h_student_photo_url').value = s.student_photo_url || '';

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

                // ── Update student preview card ──
                const statusStr = s.status ? ` (${s.status})` : '';
                if (document.getElementById('preview_name'))  document.getElementById('preview_name').textContent = (s.student_name || '—') + statusStr;
                if (document.getElementById('preview_father')) document.getElementById('preview_father').textContent = s.father_name || '—';
                if (document.getElementById('preview_admno'))  document.getElementById('preview_admno').textContent = s.admission_no || '—';
                if (document.getElementById('preview_roll'))   document.getElementById('preview_roll').textContent = s.roll_no || '—';
                if (document.getElementById('preview_dob'))    document.getElementById('preview_dob').textContent = s.date_of_birth || '—';
                if (document.getElementById('preview_class')) {
                    document.getElementById('preview_class').textContent =
                        (s.class_name || '') + (s.section_name ? ' — ' + s.section_name : '');
                }

                if (studentPreview) studentPreview.style.display = 'block';
                if (studentPreview) studentPreview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                if (generateBtn) generateBtn.disabled = false;

                // Highlight the selected option visually
                if (studentDropdown) studentDropdown.style.borderColor = '#22c55e';
            }

            if (studentDropdown) {
                studentDropdown.addEventListener('change', handleStudentSelect);
                studentDropdown.addEventListener('click', handleStudentSelect);
            }

            // ── Form submit guard: ensure recipient/student name is filled ─────────
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
                const studentName = document.getElementById('h_student_name')
                    ? document.getElementById('h_student_name').value.trim()
                    : '';
                if (!studentName) {
                    e.preventDefault();
                    alert('⚠️ Please select a student first before generating the certificate.');
                    if (studentStep) studentStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    return false;
                }
            });

            // ── Clear helper ─────────────────────────────────────────────
            function clearStudentPreview() {
                if (studentPreview) studentPreview.style.display = 'none';
                if (generateBtn) generateBtn.disabled = true;
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
