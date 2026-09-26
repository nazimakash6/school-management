@extends('layouts.app')

@section('title', 'ID Card Generator')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/id-cards.css') }}">
@endpush

@section('content')
<div class="container-fluid id-cards-page">
    
    <!-- Header Section -->
    <div class="content-header d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <h1 class="page-title text-dark fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="id-card" style="width:1.8rem;height:1.8rem;color:#3d1a06;"></i>
                ID Card Generator
            </h1>
            <p class="page-subtitle text-muted mb-0">Generate, customize, and print high-quality identity cards for Students & Staff</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-print-custom" onclick="window.print()">
                <i data-lucide="printer" style="width:1.1rem;height:1.1rem;"></i>
                Print Current ID Card
            </button>
        </div>
    </div>

    <!-- Main Navigation Tabs (Student vs Staff) -->
    <ul class="nav nav-tabs id-tabs mb-4 no-print" id="idCardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student-panel" type="button" role="tab" aria-controls="student-panel" aria-selected="true">
                <i data-lucide="graduation-cap" style="width:1.1rem;height:1.1rem;"></i>
                Student ID Cards
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff-panel" type="button" role="tab" aria-controls="staff-panel" aria-selected="false">
                <i data-lucide="briefcase" style="width:1.1rem;height:1.1rem;"></i>
                Staff ID Cards
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="idCardTabsContent">

        <!-- ========================================================================= -->
        <!-- TAB 1: STUDENT ID CARDS -->
        <!-- ========================================================================= -->
        <div class="tab-pane fade show active" id="student-panel" role="tabpanel" aria-labelledby="student-tab">
            <div class="row g-4">
                
                <!-- Controls Column (Session & Student Selection) -->
                <div class="col-lg-4 col-md-5 no-print">
                    <div class="card control-card p-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color:#3d1a06;">
                            <i data-lucide="sliders" style="width:1.2rem;height:1.2rem;color:#c7ad8d;"></i>
                            Select Parameters
                        </h5>
                        
                        <!-- Step 1: Academic Session -->
                        <div class="mb-4">
                            <label class="form-label" for="student_session_select">1. Academic Session</label>
                            <select id="student_session_select" class="form-select shadow-sm">
                                @foreach($sessions as $sess)
                                    <option value="{{ $sess->id }}" {{ $sess->id == $selectedSessionId ? 'selected' : '' }}>
                                        {{ $sess->session_name }} {{ $sess->status == 'Active' ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Select Class -->
                        <div class="mb-4">
                            <label class="form-label" for="student_class_select">2. Select Class</label>
                            <select id="student_class_select" class="form-select shadow-sm">
                                <option value="all">-- All Classes --</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 3: Choose Student -->
                        <div class="mb-4">
                            <label class="form-label" for="student_select">3. Select Student</label>
                            <select id="student_select" class="form-select shadow-sm">
                                <option value="">-- Choose Student --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $st->full_name }} (Adm: {{ $st->admission_no }} - {{ $st->class_name }} {{ $st->section_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search Helper Input -->
                        <div class="mb-3">
                            <label class="form-label text-muted" style="font-size:0.75rem;">Filter Student List</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i></span>
                                <input type="text" id="student_search_input" class="form-control border-start-0" placeholder="Type name or roll no...">
                            </div>
                        </div>

                        <hr class="my-3" style="border-color:#e2d5c3;">

                        <!-- Print & Quick Actions -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-print-custom w-100 justify-content-center" onclick="window.print()">
                                <i data-lucide="printer" style="width:1.1rem;height:1.1rem;"></i>
                                Print Student ID Card
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_print_all_students">
                                <i data-lucide="layers" style="width:0.9rem;height:0.9rem;" class="me-1"></i>
                                Print Select Class Students
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Preview Canvas Column -->
                <div class="col-lg-8 col-md-7">
                    <div class="id-card-stage">
                        <span class="id-card-stage-title">Live Preview Container</span>

                        <!-- Printable ID Card Wrapper -->
                        <div class="id-card-wrapper" id="student_card_wrapper">
                            
                            <!-- STUDENT ID CARD FRONT -->
                            <div class="id-card" id="student_card_front">
                                <!-- Top School Header (#3d1a06) -->
                                <div class="id-card-header">
                                    @if(!empty($schoolInfo->logo_url))
                                        <img src="{{ $schoolInfo->logo_url }}" alt="Logo" class="school-logo">
                                    @else
                                        <i data-lucide="graduation-cap" class="school-icon"></i>
                                    @endif
                                    <h4 class="school-name" id="card_school_name">{{ $schoolInfo->school_name ?? 'Al Huda Educational Complex' }}</h4>
                                    <p class="school-tagline" id="card_school_tagline">{{ $schoolInfo->tagline ?? 'Excellence in Education' }}</p>
                                </div>

                                <!-- Category Badge -->
                                <div class="id-card-badge">STUDENT IDENTITY CARD</div>

                                <!-- Card Body (#fdfaf3) -->
                                <div class="id-card-body">
                                    <!-- Photo -->
                                    <div class="id-card-photo-wrapper">
                                        <div class="id-card-photo-placeholder" id="student_photo_holder">ST</div>
                                        <img src="" id="student_photo_img" class="d-none" alt="Student Photo">
                                    </div>

                                    <!-- Student Name -->
                                    <div class="id-card-name" id="card_student_name">Muhammad Ali</div>
                                    <div class="id-card-sub-badge" id="card_student_class">Class 5 - Section A</div>

                                    <!-- Table with #3d1a06 Header as specified -->
                                    <table class="id-card-table">
                                        <thead>
                                            <tr>
                                                <th colspan="2" style="text-align:center;">STUDENT PARTICULARS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="lbl">Adm No:</td>
                                                <td class="val" id="card_student_adm">ADM-2026-001</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Roll No:</td>
                                                <td class="val" id="card_student_roll">05</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Father:</td>
                                                <td class="val" id="card_student_father">Tariq Mahmood</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Phone:</td>
                                                <td class="val" id="card_student_phone">03001234567</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Session:</td>
                                                <td class="val" id="card_student_session">2025 - 2026</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Footer -->
                                <div class="id-card-footer">
                                    <div class="valid-box">
                                        <span class="valid-title">VALID UNTIL</span>
                                        <span class="valid-date">DEC 2026</span>
                                    </div>
                                    <div class="sig-box">
                                        <div class="sig-line"></div>
                                        <div class="sig-title">Principal</div>
                                    </div>
                                </div>
                            </div>

                            <!-- STUDENT ID CARD BACK -->
                            <div class="id-card id-card-back" id="student_card_back">
                                <div class="id-card-header">
                                    <h4 class="school-name">EMERGENCY & RETURN</h4>
                                </div>
                                <div class="id-card-badge">TERMS & INSTRUCTIONS</div>

                                <div class="id-card-body">
                                    <div class="back-notice">
                                        This Identity Card is non-transferable and remains the official property of the institution. If found, please return immediately.
                                    </div>

                                    <table class="id-card-table mb-2">
                                        <thead>
                                            <tr>
                                                <th colspan="2" style="text-align:center;">ADDITIONAL DETAILS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="lbl">D.O.B:</td>
                                                <td class="val" id="card_student_dob">12 May, 2015</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Blood Grp:</td>
                                                <td class="val" id="card_student_blood">B+</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="contact-info">
                                        <div class="contact-info-row">
                                            <i data-lucide="map-pin" style="width:10px;height:10px;"></i>
                                            <span id="card_school_address">{{ $schoolInfo->full_address ?? 'Main Campus, Lahore' }}</span>
                                        </div>
                                        <div class="contact-info-row">
                                            <i data-lucide="phone" style="width:10px;height:10px;"></i>
                                            <span id="card_school_phone">{{ $schoolInfo->phone ?? '+92 42 35881234' }}</span>
                                        </div>
                                        <div class="contact-info-row">
                                            <i data-lucide="globe" style="width:10px;height:10px;"></i>
                                            <span id="card_school_web">{{ $schoolInfo->website ?? 'https://educore.edu.pk' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="id-card-footer justify-content-center">
                                    <div class="official-seal">
                                        <i data-lucide="shield-check" style="width:14px;height:14px;color:#3d1a06;"></i>
                                        <span>OFFICIAL STUDENT CARD</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: STAFF ID CARDS -->
        <!-- ========================================================================= -->
        <div class="tab-pane fade" id="staff-panel" role="tabpanel" aria-labelledby="staff-tab">
            <div class="row g-4">
                
                <!-- Controls Column (Staff Selection) -->
                <div class="col-lg-4 col-md-5 no-print">
                    <div class="card control-card p-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color:#3d1a06;">
                            <i data-lucide="user-check" style="width:1.2rem;height:1.2rem;color:#c7ad8d;"></i>
                            Select Staff Member
                        </h5>
                        
                        <!-- Step 1: Select Staff -->
                        <div class="mb-4">
                            <label class="form-label" for="staff_select">1. Select Staff</label>
                            <select id="staff_select" class="form-select shadow-sm">
                                <option value="">-- Choose Staff Member --</option>
                                @foreach($staffs as $stf)
                                    <option value="{{ $stf->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $stf->full_name }} (ID: {{ $stf->staff_id }} - {{ $stf->formatted_designation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Helper -->
                        <div class="mb-3">
                            <label class="form-label text-muted" style="font-size:0.75rem;">Search Staff</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i></span>
                                <input type="text" id="staff_search_input" class="form-control border-start-0" placeholder="Search staff name or ID...">
                            </div>
                        </div>

                        <hr class="my-3" style="border-color:#e2d5c3;">

                        <!-- Print Button -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-print-custom w-100 justify-content-center" onclick="window.print()">
                                <i data-lucide="printer" style="width:1.1rem;height:1.1rem;"></i>
                                Print Staff ID Card
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Preview Canvas Column -->
                <div class="col-lg-8 col-md-7">
                    <div class="id-card-stage">
                        <span class="id-card-stage-title">Live Preview Container</span>

                        <!-- Printable Staff ID Card Wrapper -->
                        <div class="id-card-wrapper" id="staff_card_wrapper">
                            
                            <!-- STAFF ID CARD FRONT -->
                            <div class="id-card" id="staff_card_front">
                                <div class="id-card-header">
                                    @if(!empty($schoolInfo->logo_url))
                                        <img src="{{ $schoolInfo->logo_url }}" alt="Logo" class="school-logo">
                                    @else
                                        <i data-lucide="graduation-cap" class="school-icon"></i>
                                    @endif
                                    <h4 class="school-name">{{ $schoolInfo->school_name ?? 'Al Huda Educational Complex' }}</h4>
                                    <p class="school-tagline">{{ $schoolInfo->tagline ?? 'Excellence in Education' }}</p>
                                </div>

                                <div class="id-card-badge">STAFF IDENTITY CARD</div>

                                <div class="id-card-body">
                                    <div class="id-card-photo-wrapper">
                                        <div class="id-card-photo-placeholder" id="staff_photo_holder">SF</div>
                                        <img src="" id="staff_photo_img" class="d-none" alt="Staff Photo">
                                    </div>

                                    <div class="id-card-name" id="card_staff_name">Tariq Mehmood</div>
                                    <div class="id-card-sub-badge" id="card_staff_desig">Driver - Transport</div>

                                    <table class="id-card-table">
                                        <thead>
                                            <tr>
                                                <th colspan="2" style="text-align:center;">STAFF DETAILS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="lbl">Staff ID:</td>
                                                <td class="val" id="card_staff_id">DRV-101</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">CNIC:</td>
                                                <td class="val" id="card_staff_cnic">35202-1234567-1</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Phone:</td>
                                                <td class="val" id="card_staff_phone">0300-4567891</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Joined:</td>
                                                <td class="val" id="card_staff_joined">15 Jan, 2023</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="id-card-footer">
                                    <div class="valid-box">
                                        <span class="valid-title">VALID UNTIL</span>
                                        <span class="valid-date">DEC 2026</span>
                                    </div>
                                    <div class="sig-box">
                                        <div class="sig-line"></div>
                                        <div class="sig-title">Principal</div>
                                    </div>
                                </div>
                            </div>

                            <!-- STAFF ID CARD BACK -->
                            <div class="id-card id-card-back" id="staff_card_back">
                                <div class="id-card-header">
                                    <h4 class="school-name">OFFICIAL STAFF CARD</h4>
                                </div>
                                <div class="id-card-badge">TERMS & EMERGENCY</div>

                                <div class="id-card-body">
                                    <div class="back-notice">
                                        Authorized Staff Member of Al Huda School. This card must be presented on request by security personnel.
                                    </div>

                                    <table class="id-card-table mb-2">
                                        <thead>
                                            <tr>
                                                <th colspan="2" style="text-align:center;">EMERGENCY INFO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="lbl">Blood Grp:</td>
                                                <td class="val" id="card_staff_blood">O+</td>
                                            </tr>
                                            <tr>
                                                <td class="lbl">Emergency:</td>
                                                <td class="val" id="card_staff_emergency">0300-1112233</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="contact-info">
                                        <div class="contact-info-row">
                                            <i data-lucide="map-pin" style="width:10px;height:10px;"></i>
                                            <span>{{ $schoolInfo->full_address ?? 'Main Campus, Lahore' }}</span>
                                        </div>
                                        <div class="contact-info-row">
                                            <i data-lucide="phone" style="width:10px;height:10px;"></i>
                                            <span>{{ $schoolInfo->phone ?? '+92 42 35881234' }}</span>
                                        </div>
                                        <div class="contact-info-row">
                                            <i data-lucide="globe" style="width:10px;height:10px;"></i>
                                            <span>{{ $schoolInfo->website ?? 'https://educore.edu.pk' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="id-card-footer justify-content-center">
                                    <div class="official-seal">
                                        <i data-lucide="shield-check" style="width:14px;height:14px;color:#3d1a06;"></i>
                                        <span>OFFICIAL STAFF CARD</span>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ----------------------------------------------------
    // Elements - Student Tab
    // ----------------------------------------------------
    const studentSessionSelect = document.getElementById('student_session_select');
    const studentClassSelect = document.getElementById('student_class_select');
    const studentSelect = document.getElementById('student_select');
    const studentSearchInput = document.getElementById('student_search_input');

    // ----------------------------------------------------
    // Elements - Staff Tab
    // ----------------------------------------------------
    const staffSelect = document.getElementById('staff_select');
    const staffSearchInput = document.getElementById('staff_search_input');

    // Store original options for search filtering
    let allStudentOptions = Array.from(studentSelect.options);
    let allStaffOptions = Array.from(staffSelect.options);

    // ----------------------------------------------------
    // Function: Update Student ID Card Display
    // ----------------------------------------------------
    function loadStudentData(studentId) {
        if (!studentId) return;

        fetch(`{{ url('id-cards/student-data') }}/${studentId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.student) {
                    const s = data.student;
                    
                    document.getElementById('card_student_name').textContent = s.name;
                    document.getElementById('card_student_class').textContent = `${s.class_name} - Sec ${s.section_name}`;
                    document.getElementById('card_student_adm').textContent = s.admission_no;
                    document.getElementById('card_student_roll').textContent = s.roll_no;
                    document.getElementById('card_student_father').textContent = s.father_name;
                    document.getElementById('card_student_phone').textContent = s.phone;
                    document.getElementById('card_student_session').textContent = s.session;
                    if (document.getElementById('card_student_barcode')) {
                        document.getElementById('card_student_barcode').textContent = s.admission_no;
                    }
                    document.getElementById('card_student_dob').textContent = s.dob;
                    document.getElementById('card_student_blood').textContent = s.blood_group;

                    // Photo Handling
                    const photoImg = document.getElementById('student_photo_img');
                    const photoHolder = document.getElementById('student_photo_holder');

                    if (s.photo) {
                        photoImg.src = s.photo;
                        photoImg.classList.remove('d-none');
                        photoHolder.classList.add('d-none');
                    } else {
                        const initials = s.name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
                        photoHolder.textContent = initials || 'ST';
                        photoImg.classList.add('d-none');
                        photoHolder.classList.remove('d-none');
                    }

                    if (window.lucide) {
                        lucide.createIcons();
                    }
                }
            })
            .catch(err => console.error('Error fetching student data:', err));
    }

    // ----------------------------------------------------
    // Function: Update Staff ID Card Display
    // ----------------------------------------------------
    function loadStaffData(staffId) {
        if (!staffId) return;

        fetch(`{{ url('id-cards/staff-data') }}/${staffId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.staff) {
                    const st = data.staff;
                    
                    document.getElementById('card_staff_name').textContent = st.name;
                    document.getElementById('card_staff_desig').textContent = `${st.designation} (${st.department})`;
                    document.getElementById('card_staff_id').textContent = st.staff_id;
                    document.getElementById('card_staff_cnic').textContent = st.cnic;
                    document.getElementById('card_staff_phone').textContent = st.phone;
                    document.getElementById('card_staff_joined').textContent = st.joining_date;
                    if (document.getElementById('card_staff_barcode')) {
                        document.getElementById('card_staff_barcode').textContent = st.staff_id;
                    }
                    document.getElementById('card_staff_blood').textContent = st.blood_group;
                    document.getElementById('card_staff_emergency').textContent = st.emergency_contact;

                    // Photo Handling
                    const photoImg = document.getElementById('staff_photo_img');
                    const photoHolder = document.getElementById('staff_photo_holder');

                    if (st.photo) {
                        photoImg.src = st.photo;
                        photoImg.classList.remove('d-none');
                        photoHolder.classList.add('d-none');
                    } else {
                        const initials = st.name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
                        photoHolder.textContent = initials || 'SF';
                        photoImg.classList.add('d-none');
                        photoHolder.classList.remove('d-none');
                    }

                    if (window.lucide) {
                        lucide.createIcons();
                    }
                }
            })
            .catch(err => console.error('Error fetching staff data:', err));
    }

    // ----------------------------------------------------
    // Event: Session & Class Dropdown Change
    // ----------------------------------------------------
    function fetchStudents() {
        const sessionId = studentSessionSelect ? studentSessionSelect.value : '';
        const className = studentClassSelect ? studentClassSelect.value : 'all';
        if (!sessionId) return;

        let fetchUrl = `{{ url('id-cards/students-by-session') }}/${sessionId}?class_name=${encodeURIComponent(className)}`;

        fetch(fetchUrl)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    studentSelect.innerHTML = '<option value="">-- Choose Student --</option>';
                    data.students.forEach(st => {
                        const opt = document.createElement('option');
                        opt.value = st.id;
                        opt.textContent = `${st.name} (Adm: ${st.admission_no} - ${st.class_name} ${st.section_name})`;
                        studentSelect.appendChild(opt);
                    });

                    allStudentOptions = Array.from(studentSelect.options);

                    if (data.students.length > 0) {
                        studentSelect.selectedIndex = 1;
                        loadStudentData(data.students[0].id);
                    } else {
                        document.getElementById('card_student_name').textContent = 'No Student Found';
                        document.getElementById('card_student_class').textContent = '-';
                        document.getElementById('card_student_adm').textContent = '-';
                        document.getElementById('card_student_roll').textContent = '-';
                        document.getElementById('card_student_father').textContent = '-';
                        document.getElementById('card_student_phone').textContent = '-';
                    }
                }
            })
            .catch(err => console.error('Error loading session students:', err));
    }

    if (studentSessionSelect) {
        studentSessionSelect.addEventListener('change', fetchStudents);
    }
    if (studentClassSelect) {
        studentClassSelect.addEventListener('change', fetchStudents);
    }

    // ----------------------------------------------------
    // Event: Student Select Change
    // ----------------------------------------------------
    studentSelect.addEventListener('change', function() {
        if (this.value) {
            loadStudentData(this.value);
        }
    });

    // ----------------------------------------------------
    // Event: Staff Select Change
    // ----------------------------------------------------
    staffSelect.addEventListener('change', function() {
        if (this.value) {
            loadStaffData(this.value);
        }
    });

    // ----------------------------------------------------
    // Search Filters
    // ----------------------------------------------------
    if (studentSearchInput) {
        studentSearchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            studentSelect.innerHTML = '';
            allStudentOptions.forEach(opt => {
                if (opt.value === '' || opt.textContent.toLowerCase().includes(term)) {
                    studentSelect.appendChild(opt.cloneNode(true));
                }
            });
        });
    }

    if (staffSearchInput) {
        staffSearchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            staffSelect.innerHTML = '';
            allStaffOptions.forEach(opt => {
                if (opt.value === '' || opt.textContent.toLowerCase().includes(term)) {
                    staffSelect.appendChild(opt.cloneNode(true));
                }
            });
        });
    }

    // ----------------------------------------------------
    // Bulk Print All Session Students
    // ----------------------------------------------------
    const btnPrintAll = document.getElementById('btn_print_all_students');
    if (btnPrintAll) {
        btnPrintAll.addEventListener('click', function() {
            const sessionId = studentSessionSelect.value;
            const className = studentClassSelect ? studentClassSelect.value : 'all';
            if (!sessionId) return;

            btnPrintAll.disabled = true;
            btnPrintAll.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Preparing Bulk Cards...';

            fetch(`{{ url('id-cards/students-by-session') }}/${sessionId}?class_name=${encodeURIComponent(className)}`)
                .then(res => res.json())
                .then(async data => {
                    if (data.success && data.students.length > 0) {
                        const wrapper = document.getElementById('student_card_wrapper');
                        const originalHTML = wrapper.innerHTML;

                        wrapper.innerHTML = '';

                        for (let st of data.students) {
                            try {
                                const stRes = await fetch(`{{ url('id-cards/student-data') }}/${st.id}`);
                                const stData = await stRes.json();
                                if (stData.success && stData.student) {
                                    const s = stData.student;
                                    const initials = s.name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
                                    
                                    const cardHTML = `
                                        <!-- FRONT CARD -->
                                        <div class="id-card mb-4" style="page-break-inside: avoid; break-inside: avoid;">
                                            <div class="id-card-header">
                                                ${stData.school.logo_url ? `<img src="${stData.school.logo_url}" class="school-logo">` : `<i data-lucide="graduation-cap" class="school-icon"></i>`}
                                                <h4 class="school-name">${stData.school.school_name}</h4>
                                                <p class="school-tagline">${stData.school.tagline}</p>
                                            </div>
                                            <div class="id-card-badge">STUDENT IDENTITY CARD</div>
                                            <div class="id-card-body">
                                                <div class="id-card-photo-wrapper">
                                                    ${s.photo ? `<img src="${s.photo}">` : `<div class="id-card-photo-placeholder">${initials}</div>`}
                                                </div>
                                                <div class="id-card-name">${s.name}</div>
                                                <div class="id-card-sub-badge">${s.class_name} - Sec ${s.section_name}</div>
                                                <table class="id-card-table">
                                                    <thead><tr><th colspan="2" style="text-align:center;">STUDENT PARTICULARS</th></tr></thead>
                                                    <tbody>
                                                        <tr><td class="lbl">Adm No:</td><td class="val">${s.admission_no}</td></tr>
                                                        <tr><td class="lbl">Roll No:</td><td class="val">${s.roll_no}</td></tr>
                                                        <tr><td class="lbl">Father:</td><td class="val">${s.father_name}</td></tr>
                                                        <tr><td class="lbl">Phone:</td><td class="val">${s.phone}</td></tr>
                                                        <tr><td class="lbl">Session:</td><td class="val">${s.session}</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="id-card-footer">
                                                <div class="valid-box">
                                                    <span class="valid-title">VALID UNTIL</span>
                                                    <span class="valid-date">DEC 2026</span>
                                                </div>
                                                <div class="sig-box">
                                                    <div class="sig-line"></div>
                                                    <div class="sig-title">Principal</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- BACK CARD -->
                                        <div class="id-card id-card-back mb-4" style="page-break-inside: avoid; break-inside: avoid;">
                                            <div class="id-card-header">
                                                <h4 class="school-name">EMERGENCY & RETURN</h4>
                                            </div>
                                            <div class="id-card-badge">TERMS & INSTRUCTIONS</div>
                                            <div class="id-card-body">
                                                <div class="back-notice">
                                                    This Identity Card is non-transferable and remains the official property of the institution. If found, please return immediately.
                                                </div>
                                                <table class="id-card-table mb-2">
                                                    <thead>
                                                        <tr><th colspan="2" style="text-align:center;">ADDITIONAL DETAILS</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr><td class="lbl">D.O.B:</td><td class="val">${s.dob}</td></tr>
                                                        <tr><td class="lbl">Blood Grp:</td><td class="val">${s.blood_group}</td></tr>
                                                    </tbody>
                                                </table>
                                                <div class="contact-info">
                                                    <div class="contact-info-row">
                                                        <i data-lucide="map-pin" style="width:10px;height:10px;"></i>
                                                        <span>${stData.school.address}</span>
                                                    </div>
                                                    <div class="contact-info-row">
                                                        <i data-lucide="phone" style="width:10px;height:10px;"></i>
                                                        <span>${stData.school.phone}</span>
                                                    </div>
                                                    <div class="contact-info-row">
                                                        <i data-lucide="globe" style="width:10px;height:10px;"></i>
                                                        <span>${stData.school.website}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="id-card-footer justify-content-center">
                                                <div class="official-seal">
                                                    <i data-lucide="shield-check" style="width:14px;height:14px;color:#3d1a06;"></i>
                                                    <span>OFFICIAL STUDENT CARD</span>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    wrapper.insertAdjacentHTML('beforeend', cardHTML);
                                }
                            } catch(e) {
                                console.error('Bulk fetch error', e);
                            }
                        }

                        if (window.lucide) lucide.createIcons();

                        // Stop loading state on button immediately when cards are rendered into preview
                        btnPrintAll.disabled = false;
                        btnPrintAll.innerHTML = '<i data-lucide="layers" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Print Select Class Students';
                        if (window.lucide) lucide.createIcons();

                        setTimeout(() => {
                            window.print();
                            wrapper.innerHTML = originalHTML;
                            if (studentSelect.value) loadStudentData(studentSelect.value);
                            if (window.lucide) lucide.createIcons();
                        }, 500);

                    } else {
                        alert('No active students found for the selected class.');
                        btnPrintAll.disabled = false;
                        btnPrintAll.innerHTML = '<i data-lucide="layers" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Print Select Class Students';
                        if (window.lucide) lucide.createIcons();
                    }
                })
                .catch(err => {
                    console.error('Bulk print error:', err);
                    btnPrintAll.disabled = false;
                    btnPrintAll.innerHTML = '<i data-lucide="layers" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Print Select Class Students';
                    if (window.lucide) lucide.createIcons();
                });
        });
    }

    // ----------------------------------------------------
    // Initial Loads on Page Load
    // ----------------------------------------------------
    if (studentSelect && studentSelect.value) {
        loadStudentData(studentSelect.value);
    }
    if (staffSelect && staffSelect.value) {
        loadStaffData(staffSelect.value);
    }

    // Refresh lucide icons
    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
@endpush
