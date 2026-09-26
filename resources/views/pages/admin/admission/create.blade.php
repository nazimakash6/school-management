@extends ('layouts.app')

@section ('title', 'Student Admission Create')

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/admission.css') }}" />
@endpush

@push ('scripts')
    <script src="{{ asset('js/admission.js') }}?v={{ time() }}"></script>
@endpush

@push ('scripts')
    <script>
        function initFeePlan() {
            const feePlan = document.getElementById('fee_plan');

            if (!feePlan) return;

            const monthlyDiv = document.getElementById('monthlyFeeDiv');
            const quarterlyDiv = document.getElementById('quarterlyFeeDiv');
            const annualDiv = document.getElementById('annualFeeDiv');

            function toggleFeeFields() {
                monthlyDiv.classList.add('d-none');
                quarterlyDiv.classList.add('d-none');
                annualDiv.classList.add('d-none');

                switch (feePlan.value) {
                    case 'Monthly':
                        monthlyDiv.classList.remove('d-none');
                        break;

                    case 'Quarterly':
                        quarterlyDiv.classList.remove('d-none');
                        break;

                    case 'Annual':
                        annualDiv.classList.remove('d-none');
                        break;
                }
                calculateTotalFee();
            }

            function calculateTotalFee() {
                const planVal = feePlan ? feePlan.value : '';
                const monthlyFee = parseFloat(document.querySelector('[name="monthly_fee"]')?.value || 0);
                const quarterlyFee = parseFloat(document.querySelector('[name="quarterly_fee"]')?.value || 0);
                const annualFee = parseFloat(document.querySelector('[name="annual_fee"]')?.value || 0);
                const regFee = parseFloat(document.querySelector('[name="registration_fee"]')?.value || 0);
                const discount = parseFloat(document.querySelector('[name="scholarship_discount"]')?.value || 0);

                let baseFee = 0;
                if (planVal === 'Monthly') baseFee = monthlyFee;
                else if (planVal === 'Quarterly') baseFee = quarterlyFee;
                else if (planVal === 'Annual') baseFee = annualFee;
                else baseFee = monthlyFee || quarterlyFee || annualFee || 0;

                const netFee = Math.max(0, (baseFee + regFee) - discount);

                const breakdownEl = document.getElementById('fee_breakdown_text');
                const netFeeEl = document.getElementById('net_fee_calculated');

                if (breakdownEl) {
                    breakdownEl.textContent = `Base Fee: Rs. ${baseFee.toLocaleString()} + Reg Fee: Rs. ${regFee.toLocaleString()} - Scholarship: Rs. ${discount.toLocaleString()}`;
                }
                if (netFeeEl) {
                    netFeeEl.textContent = `Rs. ${netFee.toLocaleString()}`;
                }
            }

            toggleFeeFields();

            feePlan.addEventListener('change', toggleFeeFields);

            const feeInputs = ['monthly_fee', 'quarterly_fee', 'annual_fee', 'registration_fee', 'scholarship_discount'];
            feeInputs.forEach(name => {
                const input = document.querySelector(`[name="${name}"]`);
                if (input) {
                    input.addEventListener('input', calculateTotalFee);
                    input.addEventListener('change', calculateTotalFee);
                }
            });

            calculateTotalFee();
        }

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
            initFeePlan();
            initSiblingFilterAndSelection();
        });
    </script>

@endpush

@section ('content')
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admission.index') }}" class="text-decoration-none text-muted">Students</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">New Admission</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="user-plus" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Create Student Admission</h3>
                    <p class="text-muted mb-0 fs-7">Complete student registration with guardian, academic, and document details</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('admission.blank-form') }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="printer" style="width:1rem;height:1rem;"></i> Print Empty Form
            </a>
            <a href="{{ route('admission.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to List
            </a>
        </div>
    </div>

    @include ('partials.error-alerts')

    <div class="card admission-card">
        <div class="card-body">
            <div class="admission-stepper mb-4" role="tablist" aria-label="Admission steps">
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
                <button class="admission-step" type="button" data-step-target="4">
                    <span class="admission-step-number">5</span>
                    <span class="admission-step-text">Documents & Review</span>
                </button>
            </div>

            <form
                id="admissionForm"
                class="row g-4"
                data-validate
                data-step-form
                novalidate
                method="POST"
                action="{{ route('admission.store') }}"
                enctype="multipart/form-data"
            >
                @csrf
                <div class="col-12 admission-step-panel is-active" data-step-panel="0">
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Student Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Basic identity and contact details of the student.</p>
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
                                                            @selected(old('sibling_id') == $exStudent->id)
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
                                <label class="form-label">Admission No <span class="text-danger">*</span></label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="admission_no"
                                    value="{{ old('admission_no', 'ADM-' . now()->format('Y') . '-' . str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT)) }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label
                                ><input
                                    type="date"
                                    class="form-control"
                                    name="admission_date"
                                    value="{{ old('admission_date') }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Student First Name <span class="text-danger">*</span></label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="first_name"
                                    value="{{ old('first_name') }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Student Last Name</label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="last_name"
                                    value="{{ old('last_name') }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >Student CNIC / B-Form</label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="cnic_bform"
                                    value="{{ old('cnic_bform') }}"
                                    placeholder="e.g. 3520112345671 (13 digits)"
                                    maxlength="13"
                                    inputmode="numeric"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,13)"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date of Birth</label
                                ><input
                                    type="date"
                                    class="form-control"
                                    name="date_of_birth"
                                    value="{{ old('date_of_birth') }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender <span class="text-danger">*</span></label
                                ><select class="form-select" name="gender">
                                    <option value="">Select gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Blood Group</label
                                ><select class="form-select" name="blood_group">
                                    <option value="">Select blood group</option>
                                    <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Religion</label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="religion"
                                    value="{{ old('religion', 'Islam') }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nationality</label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="nationality"
                                    value="{{ old('nationality', 'Pakistani') }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Student Mobile</label
                                ><input
                                    type="tel"
                                    class="form-control"
                                    name="student_mobile_no"
                                    value="{{ old('student_mobile_no') }}"
                                    placeholder="e.g. 03001234567 (11 digits)"
                                    maxlength="11"
                                    inputmode="numeric"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Address</label
                                ><input
                                    type="email"
                                    class="form-control"
                                    name="student_email"
                                    value="{{ old('student_email') }}"
                                    placeholder="student@example.com"
                                />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Previous School</label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="previous_school"
                                    value="{{ old('previous_school') }}"
                                    placeholder="If applicable"
                                />
                            </div>
                            <div class="col-12">
                                <label class="form-label">Medical Notes</label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    placeholder="Allergy, medication, health concerns"
                                    name="student_medical_notes"
                                    >{{ old('student_medical_notes') }}</textarea
                                >
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button class="btn btn-primary" type="button" data-step-next>Next Step</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 admission-step-panel" data-step-panel="1" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Parent / Guardian Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Primary contact details for fee, attendance, and emergency communication.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-12"><h6 class="fw-bold text-primary mb-0 border-bottom pb-2"><i class="bi bi-person-badge me-1"></i> Father Information</h6></div>
                            <div class="col-md-3">
                                <label class="form-label">Father Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="father_name" value="{{ old('father_name') }}" placeholder="Father Name" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Father CNIC</label>
                                <input type="text" class="form-control" name="father_cnic" value="{{ old('father_cnic') }}" placeholder="e.g. 3520112345671 (13 digits)" maxlength="13" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,13)" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Father Phone Number</label>
                                <input type="tel" class="form-control" name="father_phone" value="{{ old('father_phone') }}" placeholder="e.g. 03001234567 (11 digits)" maxlength="11" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Father Occupation</label>
                                <input type="text" class="form-control" name="father_occupation" value="{{ old('father_occupation', old('guardian_occupation')) }}" placeholder="Father Occupation" />
                            </div>

                            <div class="col-12 mt-3"><h6 class="fw-bold text-primary mb-0 border-bottom pb-2"><i class="bi bi-person-heart me-1"></i> Mother Information</h6></div>
                            <div class="col-md-3">
                                <label class="form-label">Mother Name</label>
                                <input type="text" class="form-control" name="mother_name" value="{{ old('mother_name') }}" placeholder="Mother Name" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mother CNIC</label>
                                <input type="text" class="form-control" name="mother_cnic" value="{{ old('mother_cnic') }}" placeholder="e.g. 3520112345671 (13 digits)" maxlength="13" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,13)" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mother Phone Number</label>
                                <input type="tel" class="form-control" name="mother_phone" value="{{ old('mother_phone') }}" placeholder="e.g. 03001234567 (11 digits)" maxlength="11" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mother Occupation</label>
                                <input type="text" class="form-control" name="mother_occupation" value="{{ old('mother_occupation') }}" placeholder="Mother Occupation" />
                            </div>

                            <div class="col-12 mt-3"><h6 class="fw-bold text-primary mb-0 border-bottom pb-2"><i class="bi bi-shield-person me-1"></i> Guardian Details (If different)</h6></div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian Name</label>
                                <input type="text" class="form-control" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Guardian Name" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Relation</label>
                                <select class="form-select" name="guardian_relation">
                                    <option value="">Select relation</option>
                                    <option value="father" {{ old('guardian_relation') == 'father' ? 'selected' : '' }}>Father</option>
                                    <option value="mother" {{ old('guardian_relation') == 'mother' ? 'selected' : '' }}>Mother</option>
                                    <option value="brother" {{ old('guardian_relation') == 'brother' ? 'selected' : '' }}>Brother</option>
                                    <option value="sister" {{ old('guardian_relation') == 'sister' ? 'selected' : '' }}>Sister</option>
                                    <option value="uncle" {{ old('guardian_relation') == 'uncle' ? 'selected' : '' }}>Uncle</option>
                                    <option value="aunt" {{ old('guardian_relation') == 'aunt' ? 'selected' : '' }}>Aunt</option>
                                    <option value="grandfather" {{ old('guardian_relation') == 'grandfather' ? 'selected' : '' }}>Grandfather</option>
                                    <option value="grandmother" {{ old('guardian_relation') == 'grandmother' ? 'selected' : '' }}>Grandmother</option>
                                    <option value="other" {{ old('guardian_relation') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian CNIC</label>
                                <input type="text" class="form-control" placeholder="e.g. 3520112345671 (13 digits)" maxlength="13" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,13)" name="guardian_cnic" value="{{ old('guardian_cnic') }}" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Primary Mobile</label>
                                <input type="tel" class="form-control" name="guardian_primary_mobile_no" value="{{ old('guardian_primary_mobile_no') }}" placeholder="e.g. 03001234567 (11 digits)" maxlength="11" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Secondary Mobile</label>
                                <input type="tel" class="form-control" name="guardian_secondary_mobile_no" value="{{ old('guardian_secondary_mobile_no') }}" placeholder="e.g. 03001234567 (11 digits)" maxlength="11" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guardian Email</label>
                                <input type="email" class="form-control" name="guardian_email" value="{{ old('guardian_email') }}" placeholder="guardian@example.com" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">Guardian Address</label>
                                <textarea class="form-control" rows="2" placeholder="Guardian office or workplace address" name="guardian_address">{{ old('guardian_address') }}</textarea>
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-secondary" type="button" data-step-prev>Previous</button>
                                <button class="btn btn-primary" type="button" data-step-next>Next Step</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 admission-step-panel" data-step-panel="2" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Academic Placement</h5>
                            <p class="mb-0 text-sm text-tertiary">Assign class, section, fee package, and academic session.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-3">
                                <label class="form-label"> Academic Session <span class="text-danger">*</span> </label>

                                <select class="form-select" name="academic_session_id" id="academicSessionSelect">
                                    <option value="">Select session</option>

                                    @forelse ($academicSessions as $session)
                                        <option
                                            value="{{ $session->id }}"
                                            @selected (old('academic_session_id') == $session->id)
                                        >
                                            {{ $session->session_name }}
                                        </option>
                                    @empty
                                        <option value="">No sessions available</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select" name="class_name" id="classSelect">
                                    <option value="">Select class</option>
                                    @foreach ($classes as $class)
                                        <option
                                            value="{{ $class->name }}"
                                            data-sections="{{ json_encode($class->section_names) }}"
                                            @selected (old('class_name') === $class->name)
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
                                    data-pending-value="{{ old('section_name') }}"
                                >
                                    <option value="">Select section</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Academic Group</label>
                                <select class="form-select" name="group_name">
                                    <option value="">Select group</option>
                                    @if(isset($groupsList))
                                        @foreach($groupsList as $grp)
                                            <option value="{{ $grp->name }}" @selected(old('group_name') === $grp->name || old('group') === $grp->name)>
                                                {{ $grp->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Roll No</label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="roll_no"
                                    value="{{ old('roll_no') }}"
                                    placeholder="Auto / manual"
                                />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Shift</label
                                ><select class="form-select" name="class_shift" value="{{ old('class_shift') }}">
                                    <option value="">Select shift</option>
                                    <option value="morning" {{ old('class_shift') == 'morning' ? 'selected' : '' }}>
                                        Morning
                                    </option>
                                    <option value="evening" {{ old('class_shift') == 'evening' ? 'selected' : '' }}>
                                        Evening
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Admission Type</label
                                ><select class="form-select" name="admission_type" value="{{ old('admission_type') }}">
                                    <option value="">Select type</option>
                                    <option
                                        value="new_admission"
                                        {{ old('admission_type') == 'new_admission' ? 'selected' : '' }}
                                    >
                                        New Admission
                                    </option>
                                    <option
                                        value="readmission"
                                        {{ old('admission_type') == 'readmission' ? 'selected' : '' }}
                                    >
                                        Readmission
                                    </option>
                                    <option
                                        value="transfer"
                                        {{ old('admission_type') == 'transfer' ? 'selected' : '' }}
                                    >
                                        Transfer
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fee Status</label>
                                <select class="form-select" name="fee_status">
                                    <option value="pending" {{ old('fee_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="partial" {{ old('fee_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="paid" {{ old('fee_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fee Plan</label>
                                <select class="form-select" name="fee_plan" id="fee_plan">
                                    <option value="">Select Fee Plan</option>
                                    <option value="Monthly" {{ old('fee_plan') == 'Monthly' ? 'selected' : '' }}>
                                        Monthly
                                    </option>
                                    <option value="Quarterly" {{ old('fee_plan') == 'Quarterly' ? 'selected' : '' }}>
                                        Quarterly
                                    </option>
                                    <option value="Annual" {{ old('fee_plan') == 'Annual' ? 'selected' : '' }}>
                                        Annual
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4 d-none" id="monthlyFeeDiv">
                                <label class="form-label">Monthly Fee</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="monthly_fee"
                                    value="{{ old('monthly_fee') }}"
                                    placeholder="0"
                                />
                            </div>

                            <div class="col-md-4 d-none" id="quarterlyFeeDiv">
                                <label class="form-label">Quarterly Fee</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="quarterly_fee"
                                    value="{{ old('quarterly_fee') }}"
                                    placeholder="0"
                                />
                            </div>

                            <div class="col-md-4 d-none" id="annualFeeDiv">
                                <label class="form-label">Annual Fee</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="annual_fee"
                                    value="{{ old('annual_fee') }}"
                                    placeholder="0"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Scholarship / Discount</label
                                ><input
                                    type="number"
                                    class="form-control"
                                    name="scholarship_discount"
                                    id="scholarship_discount"
                                    value="{{ old('scholarship_discount') }}"
                                    placeholder="0"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Registration Fee</label
                                ><input
                                    type="number"
                                    class="form-control"
                                    name="registration_fee"
                                    id="registration_fee"
                                    value="{{ old('registration_fee') }}"
                                    placeholder="0"
                                />
                            </div>

                            <div class="col-12 mt-3">
                                <div class="p-3 bg-light rounded border d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <div>
                                        <span class="text-secondary d-block text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Fee Breakdown</span>
                                        <span class="fs-6 fw-bold text-dark" id="fee_breakdown_text">Base Fee: Rs. 0 + Reg Fee: Rs. 0 - Scholarship: Rs. 0</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-secondary d-block text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Net Fee Calculated</span>
                                        <span class="fs-4 fw-bold text-success" id="net_fee_calculated">Rs. 0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Placement Notes</label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    placeholder="Assessment results, remarks, previous class record"
                                    name="academic_notes"
                                    >{{ old('academic_notes') }}</textarea
                                >
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-secondary" type="button" data-step-prev>Previous</button
                                ><button class="btn btn-primary" type="button" data-step-next>Next Step</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 admission-step-panel" data-step-panel="3" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Address, Transport & Emergency</h5>
                            <p class="mb-0 text-sm text-tertiary">Residential details and emergency contact information.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Current Address <span class="text-danger">*</span></label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    name="current_address"
                                    >{{ old('current_address') }}</textarea
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Permanent Address</label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    name="permanent_address"
                                    >{{ old('permanent_address') }}</textarea
                                >
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Transport </label
                                ><select class="form-select" name="transportation_required">
                                    <option value="">Select option</option>
                                    <option value="no" {{ old('transportation_required') == 'no' ? 'selected' : '' }}>
                                        No
                                    </option>
                                    <option value="yes" {{ old('transportation_required') == 'yes' ? 'selected' : '' }}>
                                        Yes
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hostel</label>
                                <select class="form-select" name="hostel">
                                    <option value="" {{ old('hostel') === '' || old('hostel') === null ? 'selected' : '' }}>Select Option</option>
                                    <option value="no" {{ old('hostel') === 'no' ? 'selected' : '' }}>No</option>
                                    <option value="yes" {{ old('hostel') === 'yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"
                                    >Emergency Contact Name</label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="emergency_contact_name"
                                    value="{{ old('emergency_contact_name') }}"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Relation</label
                                ><input
                                    type="text"
                                    class="form-control"
                                    name="emergency_contact_relation"
                                    value="{{ old('emergency_contact_relation') }}"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Phone</label
                                ><input
                                    type="tel"
                                    class="form-control"
                                    name="emergency_contact_mobile_no"
                                    value="{{ old('emergency_contact_mobile_no') }}"
                                    placeholder="e.g. 03001234567 (11 digits)"
                                    maxlength="11"
                                    inputmode="numeric"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)"
                                />
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-secondary" type="button" data-step-prev>Previous</button
                                ><button class="btn btn-primary" type="button" data-step-next>Next Step</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 admission-step-panel" data-step-panel="4" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Documents & Final Review</h5>
                            <p class="mb-0 text-sm text-tertiary">Record submitted documents and confirm admission approval readiness.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Birth Certificate</label
                                ><select class="form-select" name="birth_certificate">
                                    <option value="">Select status</option>
                                    <option value="submitted" @selected (old('birth_certificate') === 'submitted')>
                                        Submitted
                                    </option>
                                    <option value="pending" @selected (old('birth_certificate') === 'pending')>
                                        Pending
                                    </option>
                                    <option value="not_ " @selected (old('birth_certificate') === 'not_ ')>Not</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">B-Form / CNIC Copy</label
                                ><select class="form-select" name="bform_cnic_copy">
                                    <option value="">Select status</option>
                                    <option value="submitted" @selected (old('bform_cnic_copy') === 'submitted')>
                                        Submitted
                                    </option>
                                    <option value="pending" @selected (old('bform_cnic_copy') === 'pending')>
                                        Pending
                                    </option>
                                    <option value="not_required" @selected (old('bform_cnic_copy') === 'not_required')>
                                        Not Required
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Guardian CNIC Copy</label
                                ><select class="form-select" name="guardian_cnic_copy">
                                    <option value="">Select status</option>
                                    <option value="submitted" @selected (old('guardian_cnic_copy') === 'submitted')>
                                        Submitted
                                    </option>
                                    <option value="pending" @selected (old('guardian_cnic_copy') === 'pending')>
                                        Pending
                                    </option>
                                    <option value="not_required" @selected (old('guardian_cnic_copy') === 'not_required')>
                                        Not Required
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">School Leaving Certificate</label>
                                <select class="form-select" name="school_leaving_certificate">
                                    <option value="">Select status</option>
                                    <option value="submitted" @selected (old('school_leaving_certificate') === 'submitted')>
                                        Submitted
                                    </option>
                                    <option value="pending" @selected (old('school_leaving_certificate') === 'pending')>
                                        Pending
                                    </option>
                                    <option value="not_required" @selected (old('school_leaving_certificate') === 'not_required')>
                                        Not Required
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Student Photo</label
                                ><input type="file" class="form-control" name="student_photo" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Additional Attachment</label
                                ><input
                                    type="file"
                                    class="form-control"
                                    id="attached_documents"
                                    name="attached_documents[]"
                                    multiple
                                />
                                <div id="attachmentPreview" class="mt-2"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Status <span class="text-danger">*</span></label
                                ><select class="form-select" name="admission_status">
                                    <option value="">Select status</option>
                                    <option value="pending" @selected (old('admission_status') === 'pending')>
                                        Pending
                                    </option>
                                    <option value="active" @selected (old('admission_status') === 'active')>
                                        Active
                                    </option>
                                    <option value="inactive" @selected (old('admission_status') === 'inactive')>
                                        Inactive
                                    </option>
                                    <option value="rejected" @selected (old('admission_status') === 'rejected')>
                                        Rejected
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <textarea
                                    class="form-control"
                                    rows="4"
                                    name="admission_remarks"
                                    placeholder="Internal comments, follow-up items, special instructions"
                                    >{{ old('admission_remarks') }}</textarea
                                >
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="admissionDeclaration"
                                        name="is_confirmed"
                                        value="1"
                                    />
                                    <label class="form-check-label" for="admissionDeclaration">
                                        I confirm that the above admission information has been reviewed and the
                                        documents have been collected.
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-secondary" type="button" data-step-prev>Previous</button
                                ><button class="btn btn-primary" type="submit">Save Admission</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 admission-form-note">
                <p class="mb-0 text-sm text-tertiary">* Fields are required on submit.</p>
            </div>
        </div>
    </div>

@endsection
