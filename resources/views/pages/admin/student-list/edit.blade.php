@extends ('layouts.app')

@section ('title', 'Edit Student - ' . $student->full_name)

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/admission.css') }}" />
@endpush

@push ('scripts')
    <script src="{{ asset('js/admission.js') }}?v={{ time() }}"></script>
    <script>
        function initSiblingFilterAndSelection() {
            const sessionFilter = document.getElementById('sibling_session_filter');
            const classFilter = document.getElementById('sibling_class_filter');
            const searchInput = document.getElementById('sibling_search_input');
            const siblingSelect = document.getElementById('sibling_id_select');
            const countBadge = document.getElementById('sibling_count_badge');
            const siblingBanner = document.getElementById('sibling_info_banner');
            const siblingText = document.getElementById('sibling_info_text');

            if (!siblingSelect) return;

            // Cache master student list
            const masterStudents = [];
            Array.from(siblingSelect.options).forEach(opt => {
                if (!opt.value) return;
                const rawData = opt.getAttribute('data-student');
                if (rawData) {
                    try {
                        const student = JSON.parse(rawData);
                        masterStudents.push({
                            id: opt.value,
                            text: opt.textContent,
                            student: student,
                            sessionId: String(student.academic_session_id || ''),
                            className: (student.class_name || '').toLowerCase(),
                            searchText: (student.first_name + ' ' + (student.last_name || '') + ' ' + (student.roll_no || '') + ' ' + (student.admission_no || '') + ' ' + (student.father_name || '') + ' ' + (student.guardian_name || '')).toLowerCase()
                        });
                    } catch (e) {}
                }
            });

            const initialSelectedId = siblingSelect.value;

            function updateOptions() {
                const selectedSession = sessionFilter ? sessionFilter.value : '';
                const selectedClass = classFilter ? classFilter.value.toLowerCase() : '';
                const searchText = searchInput ? searchInput.value.toLowerCase().trim() : '';

                const currentVal = siblingSelect.value || initialSelectedId;

                siblingSelect.innerHTML = '<option value="">-- No Sibling Selected (Standalone Student) --</option>';

                let visibleCount = 0;

                masterStudents.forEach(item => {
                    let matchesSession = !selectedSession || item.sessionId === selectedSession;
                    let matchesClass = !selectedClass || item.className === selectedClass;
                    let matchesSearch = !searchText || item.searchText.includes(searchText);

                    if (matchesSession && matchesClass && matchesSearch) {
                        visibleCount++;
                        const newOpt = document.createElement('option');
                        newOpt.value = item.id;
                        newOpt.textContent = item.text;
                        newOpt.setAttribute('data-student', JSON.stringify(item.student));
                        if (String(item.id) === String(currentVal)) {
                            newOpt.selected = true;
                        }
                        siblingSelect.appendChild(newOpt);
                    }
                });

                if (countBadge) {
                    if (selectedSession || selectedClass || searchText) {
                        countBadge.textContent = `${visibleCount} of ${masterStudents.length} students found`;
                    } else {
                        countBadge.textContent = `${masterStudents.length} total students`;
                    }
                }
            }

            if (sessionFilter) sessionFilter.addEventListener('change', updateOptions);
            if (classFilter) classFilter.addEventListener('change', updateOptions);
            if (searchInput) searchInput.addEventListener('input', updateOptions);

            siblingSelect.addEventListener('change', function () {
                const selectedOpt = this.options[this.selectedIndex];
                if (!selectedOpt || !selectedOpt.value) {
                    if (siblingBanner) siblingBanner.classList.add('d-none');
                    return;
                }

                const rawData = selectedOpt.getAttribute('data-student');
                if (!rawData) return;

                try {
                    const student = JSON.parse(rawData);

                    if (siblingBanner && siblingText) {
                        siblingText.textContent = `Attached with Sibling: ${student.first_name} ${student.last_name || ''} (Class ${student.class_name || ''}, Roll #${student.roll_no || '-'}). Parent details populated automatically!`;
                        siblingBanner.classList.remove('d-none');
                    }

                    function setVal(name, val) {
                        if (!val) return;
                        const el = document.querySelector(`[name="${name}"]`);
                        if (el) {
                            el.value = val;
                        }
                    }

                    setVal('father_name', student.father_name);
                    setVal('father_cnic', student.father_cnic);
                    setVal('father_phone', student.father_phone);
                    setVal('father_occupation', student.father_occupation);
                    setVal('mother_name', student.mother_name);
                    setVal('mother_cnic', student.mother_cnic);
                    setVal('mother_phone', student.mother_phone);
                    setVal('mother_occupation', student.mother_occupation);
                    setVal('guardian_name', student.guardian_name);
                    setVal('guardian_relation', student.guardian_relation);
                    setVal('guardian_cnic', student.guardian_cnic);
                    setVal('guardian_occupation', student.guardian_occupation);
                    setVal('guardian_primary_mobile_no', student.guardian_primary_mobile_no);
                    setVal('guardian_secondary_mobile_no', student.guardian_secondary_mobile_no);
                    setVal('guardian_email', student.guardian_email);
                    setVal('guardian_address', student.guardian_address);
                    setVal('current_address', student.current_address);
                    setVal('permanent_address', student.permanent_address);
                    setVal('emergency_contact_name', student.emergency_contact_name);
                    setVal('emergency_contact_relation', student.emergency_contact_relation);
                    setVal('emergency_contact_mobile_no', student.emergency_contact_mobile_no);

                } catch (e) {
                    console.error("Error parsing sibling data:", e);
                }
            });

            updateOptions();

            if (siblingSelect.value) {
                siblingSelect.dispatchEvent(new Event('change'));
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initSiblingFilterAndSelection();
        });
    </script>
@endpush

@section ('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"
                            ><i data-lucide="home" style="width: 0.875rem; height: 0.875rem" class="me-1"></i
                            >Dashboard</a
                        >
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('student-list.index') }}" class="text-decoration-none text-muted"
                            >Students List</a
                        >
                    </li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Edit Student</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div
                    class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center"
                    style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)"
                >
                    <i data-lucide="user-check" style="width: 1.5rem; height: 1.5rem"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Edit Student: {{ $student->full_name }}</h3>
                    <p class="text-muted mb-0 fs-7">Update student details including personal, guardian, academic, and contact information</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a
                href="{{ route('student-list.show', $student->id) }}"
                class="btn btn-outline-info btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium"
            >
                <i data-lucide="eye" style="width: 1rem; height: 1rem"></i> View Profile
            </a>
            <a
                href="{{ route('student-list.index') }}"
                class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium"
            >
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card admission-card">
        <div class="card-body">
            <div class="admission-stepper mb-4" role="tablist" aria-label="Student edit steps">
                <button class="admission-step active" type="button" data-step-target="0" aria-current="step">
                    <span class="admission-step-number">1</span>
                    <span class="admission-step-text">Personal Information</span>
                </button>
                <button class="admission-step" type="button" data-step-target="1">
                    <span class="admission-step-number">2</span>
                    <span class="admission-step-text">Guardian Information</span>
                </button>
                <button class="admission-step" type="button" data-step-target="2">
                    <span class="admission-step-number">3</span>
                    <span class="admission-step-text">Academic Details</span>
                </button>
                <button class="admission-step" type="button" data-step-target="3">
                    <span class="admission-step-number">4</span>
                    <span class="admission-step-text">Address & Transport</span>
                </button>
            </div>

            <form
                id="studentEditForm"
                class="row g-4"
                data-validate
                data-step-form
                novalidate
                method="POST"
                action="{{ route('student-list.update', $student->id) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method ('PUT')
                {{-- STEP 1: PERSONAL INFORMATION --}}
                <div class="col-12 admission-step-panel is-active" data-step-panel="0">
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Student Identity & Personal Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Basic identity, demographics, and personal contact details.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-12">
                                <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center gap-2 mb-1.5">
                                            <i class="bi bi-people-fill text-primary fs-5"></i>
                                            <h6 class="fw-bold text-dark mb-0">Sibling Admission (Attach Brother / Sister)</h6>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-auto">Optional</span>
                                        </div>
                                        <p class="text-muted fs-7 mb-3">If this student has a brother or sister already studying in school, use the filters below to find and select them to auto-link family & address details.</p>

                                        {{-- Filter Controls Row --}}
                                        <div class="row g-2 mb-2.5">
                                            <div class="col-md-4">
                                                <label class="form-label fs-8 fw-semibold text-secondary mb-1">Filter by Academic Session</label>
                                                <select class="form-select form-select-sm" id="sibling_session_filter">
                                                    <option value="">-- All Academic Sessions --</option>
                                                    @if(isset($academicSessions))
                                                        @foreach($academicSessions as $session)
                                                            <option value="{{ $session->id }}">{{ $session->session_name ?: $session->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fs-8 fw-semibold text-secondary mb-1">Filter by Class</label>
                                                <select class="form-select form-select-sm" id="sibling_class_filter">
                                                    <option value="">-- All Classes --</option>
                                                    @if(isset($classes))
                                                        @foreach($classes as $cls)
                                                            <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fs-8 fw-semibold text-secondary mb-1">Search Sibling (Name / Roll # / Father)</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                                    <input type="text" id="sibling_search_input" class="form-control form-control-sm" placeholder="Type name, roll #, father name...">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Select Sibling Dropdown --}}
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-label fs-8 fw-semibold text-dark mb-0">Select Sibling Student</label>
                                                <span id="sibling_count_badge" class="badge bg-secondary-subtle text-dark fs-8"></span>
                                            </div>
                                            <select class="form-select" name="sibling_id" id="sibling_id_select">
                                                <option value="">-- No Sibling Selected (Standalone Student) --</option>
                                                @if(isset($existingStudents))
                                                    @foreach($existingStudents as $exStudent)
                                                        <option value="{{ $exStudent->id }}"
                                                            @selected(old('sibling_id', $student->sibling_id) == $exStudent->id)
                                                            data-student="{{ json_encode($exStudent) }}">
                                                            {{ $exStudent->full_name }} (Class: {{ $exStudent->class_name ?: 'N/A' }} - Sec {{ strtoupper($exStudent->section_name ?: 'A') }}, Roll: {{ $exStudent->roll_no ?: '-' }}) | Father: {{ $exStudent->father_name ?: ($exStudent->guardian_name ?: 'N/A') }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div id="sibling_info_banner" class="alert alert-info d-none mt-2 mb-0 py-2 px-3 fs-7">
                                            <i class="bi bi-info-circle-fill me-1"></i> <span id="sibling_info_text"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admission No <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="admission_no"
                                    value="{{ old('admission_no', $student->admission_no) }}"
                                    required
                                    readonly
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name="admission_date"
                                    value="{{ old('admission_date', optional($student->admission_date)->format('Y-m-d')) }}"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="first_name"
                                    value="{{ old('first_name', $student->first_name) }}"
                                    required
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="last_name"
                                    value="{{ old('last_name', $student->last_name) }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Student CNIC / B-Form</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="cnic_bform"
                                    value="{{ old('cnic_bform', $student->cnic_bform) }}"
                                    placeholder="e.g. 3520112345671 (13 digits)"
                                    maxlength="13"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date of Birth</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    name="date_of_birth"
                                    value="{{ old('date_of_birth', optional($student->date_of_birth)->format('Y-m-d')) }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" required>
                                    <option value="">Select gender</option>
                                    <option
                                        value="Male"
                                        @selected (in_array(strtolower((string)old('gender', $student->gender)), ['male', 'Male']))
                                    >
                                        Male
                                    </option>
                                    <option
                                        value="Female"
                                        @selected (in_array(strtolower((string)old('gender', $student->gender)), ['female', 'Female']))
                                    >
                                        Female
                                    </option>
                                    <option
                                        value="Other"
                                        @selected (in_array(strtolower((string)old('gender', $student->gender)), ['other', 'Other']))
                                    >
                                        Other
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Blood Group</label>
                                <select class="form-select" name="blood_group">
                                    <option value="">Select blood group</option>
                                    @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option
                                            value="{{ $bg }}"
                                            @selected (old('blood_group', $student->blood_group) === $bg)
                                        >
                                            {{ $bg }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Religion</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="religion"
                                    value="{{ old('religion', $student->religion) }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nationality</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="nationality"
                                    value="{{ old('nationality', $student->nationality ?: 'Pakistani') }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Student Mobile</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    name="student_mobile_no"
                                    value="{{ old('student_mobile_no', $student->student_mobile_no) }}"
                                    placeholder="e.g. 03001234567 (11 digits)"
                                    maxlength="11"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Address</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    name="student_email"
                                    value="{{ old('student_email', $student->student_email) }}"
                                    placeholder="student@example.com"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Previous School</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="previous_school"
                                    value="{{ old('previous_school', $student->previous_school) }}"
                                    placeholder="If applicable"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Medical Notes</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="student_medical_notes"
                                    value="{{ old('student_medical_notes', $student->student_medical_notes) }}"
                                    placeholder="Allergy, medication, health concerns"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Student Photo</label>
                                <input type="file" class="form-control" name="student_photo" />
                                @php
                                        $photoPath = $student->student_photo ?: ($student->admission ? $student->admission->student_photo : null);
                                    @endphp
                                @if ($photoPath)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img
                                            src="{{ asset('storage/' . $photoPath) }}"
                                            alt="Student Photo"
                                            class="rounded border"
                                            style="width: 45px; height: 45px; object-fit: cover"
                                        />
                                        <small class="text-muted">Current Photo: {{ basename($photoPath) }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button class="btn btn-primary" type="button" data-step-next>Next Step</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: GUARDIAN INFORMATION --}}
                <div class="col-12 admission-step-panel" data-step-panel="1" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Parent & Guardian Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Parents contact details, occupations, and primary guardian details.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-12">
                                <h6 class="fw-bold text-primary mb-0 border-bottom pb-2">
                                    <i class="bi bi-person-badge me-1"></i> Father Information
                                </h6>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Father Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="father_name"
                                    value="{{ old('father_name', $student->father_name) }}"
                                    required
                                />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Father CNIC</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="father_cnic"
                                    value="{{ old('father_cnic', $student->father_cnic) }}"
                                    placeholder="e.g. 3520112345671 (13 digits)"
                                    maxlength="13"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                                />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Father Phone Number</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    name="father_phone"
                                    value="{{ old('father_phone', $student->father_phone) }}"
                                    placeholder="e.g. 03001234567 (11 digits)"
                                    maxlength="11"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Father Occupation</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="father_occupation"
                                    value="{{ old('father_occupation', $student->father_occupation) }}"
                                    placeholder="Father Occupation"
                                />
                            </div>

                            <div class="col-12 mt-3">
                                <h6 class="fw-bold text-primary mb-0 border-bottom pb-2">
                                    <i class="bi bi-person-heart me-1"></i> Mother Information
                                </h6>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mother Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="mother_name"
                                    value="{{ old('mother_name', $student->mother_name) }}"
                                />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mother CNIC</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="mother_cnic"
                                    value="{{ old('mother_cnic', $student->mother_cnic) }}"
                                    placeholder="e.g. 3520112345671 (13 digits)"
                                    maxlength="13"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                                />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mother Phone Number</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    name="mother_phone"
                                    value="{{ old('mother_phone', $student->mother_phone) }}"
                                    placeholder="e.g. 03001234567 (11 digits)"
                                    maxlength="11"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mother Occupation</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="mother_occupation"
                                    value="{{ old('mother_occupation', $student->mother_occupation) }}"
                                    placeholder="Mother Occupation"
                                />
                            </div>

                            <div class="col-12 mt-3">
                                <h6 class="fw-bold text-primary mb-0 border-bottom pb-2">
                                    <i class="bi bi-shield-person me-1"></i> Guardian Details
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="guardian_name"
                                    value="{{ old('guardian_name', $student->guardian_name) }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Relation</label>
                                <select class="form-select" name="guardian_relation">
                                    <option value="">Select relation</option>
                                    @foreach (['father'=>'Father','mother'=>'Mother','brother'=>'Brother','sister'=>'Sister','uncle'=>'Uncle','aunt'=>'Aunt','grandfather'=>'Grandfather','grandmother'=>'Grandmother','other'=>'Other'] as $relVal => $relLbl)
                                        <option
                                            value="{{ $relVal }}"
                                            @selected (old('guardian_relation', strtolower((string)$student->guardian_relation)) === $relVal)
                                        >
                                            {{ $relLbl }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian CNIC</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="guardian_cnic"
                                    value="{{ old('guardian_cnic', $student->guardian_cnic) }}"
                                    placeholder="e.g. 3520112345671 (13 digits)"
                                    maxlength="13"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Primary Mobile</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    name="guardian_primary_mobile_no"
                                    value="{{ old('guardian_primary_mobile_no', $student->guardian_primary_mobile_no) }}"
                                    placeholder="e.g. 03001234567 (11 digits)"
                                    maxlength="11"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Secondary Mobile</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    name="guardian_secondary_mobile_no"
                                    value="{{ old('guardian_secondary_mobile_no', $student->guardian_secondary_mobile_no) }}"
                                    placeholder="e.g. 03001234567 (11 digits)"
                                    maxlength="11"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian Email</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    name="guardian_email"
                                    value="{{ old('guardian_email', $student->guardian_email) }}"
                                    placeholder="guardian@example.com"
                                />
                            </div>
                            <div class="col-12">
                                <label class="form-label">Guardian Address</label>
                                <textarea
                                    class="form-control"
                                    rows="2"
                                    name="guardian_address"
                                    placeholder="Guardian office or workplace address"
                                    >{{ old('guardian_address', $student->guardian_address) }}</textarea
                                >
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-secondary" type="button" data-step-prev>Previous</button>
                                <button class="btn btn-primary" type="button" data-step-next>Next Step</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: ACADEMIC DETAILS --}}
                <div class="col-12 admission-step-panel" data-step-panel="2" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Academic Placement</h5>
                            <p class="mb-0 text-sm text-tertiary">Assign class, section, and academic session.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                <select class="form-select" name="academic_session_id" required>
                                    <option value="">Select session</option>
                                    @foreach ($academicSessions as $session)
                                        <option
                                            value="{{ $session->id }}"
                                            @selected (old('academic_session_id', $student->academic_session_id) == $session->id)
                                        >
                                            {{ $session->session_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select" name="class_name" id="classSelect" required>
                                    <option value="">Select class</option>
                                    @foreach ($classes as $class)
                                        <option
                                            value="{{ $class->name }}"
                                            data-sections="{{ json_encode($class->section_names) }}"
                                            @selected (old('class_name', $student->class_name) === $class->name)
                                        >
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Section</label>
                                <select
                                    class="form-select"
                                    name="section_name"
                                    id="sectionSelect"
                                    data-pending-value="{{ old('section_name', $student->section_name) }}"
                                >
                                    <option value="">Select section</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Academic Group</label>
                                <select class="form-select" name="group_name">
                                    <option value="">Select group</option>
                                    @if (isset($groupsList))
                                        @foreach ($groupsList as $grp)
                                            <option
                                                value="{{ $grp->name }}"
                                                @selected (old('group_name', $student->group_name ?: $student->group) === $grp->name)
                                            >
                                                {{ $grp->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Roll No</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="roll_no"
                                    value="{{ old('roll_no', $student->roll_no) }}"
                                    placeholder="Auto / manual"
                                />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Shift</label>
                                <select class="form-select" name="class_shift">
                                    <option value="">Select shift</option>
                                    <option
                                        value="morning"
                                        @selected (old('class_shift', $student->class_shift) === 'morning')
                                    >
                                        Morning
                                    </option>
                                    <option
                                        value="evening"
                                        @selected (old('class_shift', $student->class_shift) === 'evening')
                                    >
                                        Evening
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Admission Type</label>
                                <select class="form-select" name="admission_type">
                                    <option value="">Select type</option>
                                    <option
                                        value="new_admission"
                                        @selected (old('admission_type', $student->admission_type) === 'new_admission')
                                    >
                                        New Admission
                                    </option>
                                    <option
                                        value="readmission"
                                        @selected (old('admission_type', $student->admission_type) === 'readmission')
                                    >
                                        Readmission
                                    </option>
                                    <option
                                        value="transfer"
                                        @selected (old('admission_type', $student->admission_type) === 'transfer')
                                    >
                                        Transfer
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Class Fee</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control"
                                    name="class_fee"
                                    value="{{ old('class_fee', $student->class_fee) }}"
                                    placeholder="e.g. 5000"
                                />
                            </div>

                            <div class="col-12">
                                <label class="form-label">Placement Notes</label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    name="academic_notes"
                                    placeholder="Assessment results, remarks, previous class record"
                                    >{{ old('academic_notes', $student->academic_notes) }}</textarea
                                >
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-secondary" type="button" data-step-prev>Previous</button>
                                <button class="btn btn-primary" type="button" data-step-next>Next Step</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: ADDRESS & TRANSPORTATION --}}
                <div class="col-12 admission-step-panel" data-step-panel="3" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Address, Transport & Status</h5>
                            <p class="mb-0 text-sm text-tertiary">Residential details, emergency contact, transport, and active status.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Current Address <span class="text-danger">*</span></label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    name="current_address"
                                    required
                                    >{{ old('current_address', $student->current_address) }}</textarea
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Permanent Address</label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    name="permanent_address"
                                    >{{ old('permanent_address', $student->permanent_address) }}</textarea
                                >
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Transport Required</label>
                                <select class="form-select" name="transportation_required">
                                    <option
                                        value="no"
                                        @selected (old('transportation_required', $student->transportation_required) === 'no')
                                    >
                                        No
                                    </option>
                                    <option
                                        value="yes"
                                        @selected (old('transportation_required', $student->transportation_required) === 'yes')
                                    >
                                        Yes
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hostel</label>
                                <select class="form-select" name="hostel">
                                    <option
                                        value=""
                                        @selected (old('hostel', $student->hostel) === '' || old('hostel', $student->hostel) === null)
                                    >
                                        Select Option
                                    </option>
                                    <option value="no" @selected (old('hostel', $student->hostel) === 'no')>No</option>
                                    <option value="yes" @selected (old('hostel', $student->hostel) === 'yes')>
                                        Yes
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emergency Relation</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="emergency_contact_relation"
                                    value="{{ old('emergency_contact_relation', $student->emergency_contact_relation) }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emergency Phone</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    name="emergency_contact_mobile_no"
                                    value="{{ old('emergency_contact_mobile_no', $student->emergency_contact_mobile_no) }}"
                                    placeholder="e.g. 03001234567 (11 digits)"
                                    maxlength="11"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Student Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option
                                        value="active"
                                        @selected (old('status', strtolower((string)$student->status))==='active')
                                    >
                                        Active
                                    </option>
                                    <option
                                        value="inactive"
                                        @selected (old('status', strtolower((string)$student->status))==='inactive')
                                    >
                                        Inactive
                                    </option>
                                    <option
                                        value="pending"
                                        @selected (old('status', strtolower((string)$student->status))==='pending')
                                    >
                                        Pending
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Admission Remarks</label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    name="admission_remarks"
                                    placeholder="Internal comments, follow-up items, special instructions"
                                    >{{ old('admission_remarks', $student->admission_remarks ?: ($student->admission ? $student->admission->admission_remarks : '')) }}</textarea
                                >
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-secondary" type="button" data-step-prev>Previous</button>
                                <button class="btn btn-primary" type="submit">Update Student Information</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 admission-form-note">
                <p class="mb-0 text-sm text-tertiary">All updated student details will be synced across the student profile and records.</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('student-list.index') }}" class="btn btn-secondary">Back To List</a>
                    <button class="btn btn-outline-primary" type="button" data-step-target="0">Go To First Step</button>
                </div>
            </div>
        </div>
    </div>

@endsection
