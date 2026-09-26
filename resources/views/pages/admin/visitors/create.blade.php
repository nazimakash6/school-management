@extends('layouts.app')

@section('title', 'Register New Visitor - Gate Entry')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('visitors.index') }}">Visitors</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Register Visitor</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Register New Gate Visitor</h1>
            <p class="text-muted small mb-0">Record visitor details, CNIC verification, purpose & target person to meet</p>
        </div>
        <div>
            <a href="{{ route('visitors.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Register
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i data-lucide="alert-triangle" class="me-1" style="width:1rem;height:1rem;"></i> Validation Errors</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('visitors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Left Form Column -->
            <div class="col-lg-8">
                <!-- 1. Visitor Personal Information -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user-check" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            1. Visitor Identity & Contact
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Visitor Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="visitor_name" class="form-control @error('visitor_name') is-invalid @enderror" value="{{ old('visitor_name') }}" placeholder="e.g. Muhammad Tariq" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Mobile / Contact Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. +92 300 1234567" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">CNIC / ID Card Number</label>
                                <input type="text" name="cnic_id" class="form-control" value="{{ old('cnic_id') }}" placeholder="e.g. 35202-1234567-1">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">No. of Persons <span class="text-danger">*</span></label>
                                <input type="number" name="num_persons" class="form-control" value="{{ old('num_persons', 1) }}" min="1" max="20" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Entry Gate <span class="text-danger">*</span></label>
                                <select name="gate_no" class="form-select" required>
                                    @foreach($gates as $g)
                                        <option value="{{ $g }}" {{ old('gate_no') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Vehicle Plate / Type (Optional)</label>
                                <input type="text" name="vehicle_no" class="form-control" value="{{ old('vehicle_no') }}" placeholder="e.g. LEB-4921 / Honda 125 Bike">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Purpose of Visit <span class="text-danger">*</span></label>
                                <select name="purpose" class="form-select" required>
                                    @foreach($purposes as $p)
                                        <option value="{{ $p }}" {{ old('purpose') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Target Person To Meet -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="target" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            2. Person / Department To Meet
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark d-block mb-2">Who is the visitor meeting? <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group" aria-label="Meet Type Selection">
                                <input type="radio" class="btn-check" name="meet_type" id="meet_student" value="Student" {{ old('meet_type', 'Student') == 'Student' ? 'checked' : '' }} autocomplete="off">
                                <label class="btn btn-outline-primary py-2 fw-semibold" for="meet_student">
                                    <i data-lucide="graduation-cap" style="width:1.1rem;height:1.1rem;" class="me-1"></i> Meeting Student (Parent/Guardian)
                                </label>

                                <input type="radio" class="btn-check" name="meet_type" id="meet_staff" value="Staff" {{ old('meet_type') == 'Staff' ? 'checked' : '' }} autocomplete="off">
                                <label class="btn btn-outline-info py-2 fw-semibold text-dark" for="meet_staff">
                                    <i data-lucide="user" style="width:1.1rem;height:1.1rem;" class="me-1"></i> Meeting Staff / Teacher
                                </label>

                                <input type="radio" class="btn-check" name="meet_type" id="meet_general" value="General" {{ old('meet_type') == 'General' ? 'checked' : '' }} autocomplete="off">
                                <label class="btn btn-outline-secondary py-2 fw-semibold text-dark" for="meet_general">
                                    <i data-lucide="building" style="width:1.1rem;height:1.1rem;" class="me-1"></i> General Inquiry / Office
                                </label>
                            </div>
                        </div>

                        <!-- Student Selector Box -->
                        <div id="student_section" class="p-3 bg-light rounded-3 mb-3 border">
                            <h6 class="fw-bold text-dark mb-3"><i data-lucide="graduation-cap" class="text-primary me-1"></i> Select Student</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Class Filter</label>
                                    <select id="class_select" class="form-select form-select-sm">
                                        <option value="">-- Choose Class --</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Select Student</label>
                                    <select name="student_id" id="student_select" class="form-select form-select-sm">
                                        <option value="">-- Select Class First --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Selector Box -->
                        <div id="staff_section" class="p-3 bg-light rounded-3 mb-3 border d-none">
                            <h6 class="fw-bold text-dark mb-3"><i data-lucide="user" class="text-info me-1"></i> Select Staff / Teacher</h6>
                            <div class="col-12">
                                <select name="staff_id" class="form-select">
                                    <option value="">-- Choose Staff Member --</option>
                                    @foreach($staffList as $st)
                                        <option value="{{ $st->id }}" {{ old('staff_id') == $st->id ? 'selected' : '' }}>
                                            {{ $st->first_name }} {{ $st->last_name }} ({{ $st->designation ?: 'Staff' }} - {{ $st->department ?: 'General' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- General / Person to Meet Text Input -->
                        <div id="general_section" class="p-3 bg-light rounded-3 border d-none">
                            <label class="form-label fw-semibold text-dark">Office / Person Description</label>
                            <input type="text" name="person_to_meet" class="form-control" value="{{ old('person_to_meet') }}" placeholder="e.g. Accounts Office / Principal / Admission Desk">
                        </div>
                    </div>
                </div>

                <!-- 3. Photo Proof & Remarks -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="camera" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            3. ID Proof & Gate Notes
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Upload Visitor Photo / CNIC ID Image (Optional)</label>
                            <input type="file" name="id_proof_image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload photo of visitor or CNIC card (max 5MB)</small>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark">Security Remarks / Gatekeeper Notes</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter any security notes, items deposited at gate, or remarks...">{{ old('remarks') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="shield-check" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
                            Security Gate Status
                        </h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3 mb-3">
                            <span class="text-dark fw-bold d-block"><i data-lucide="clock" style="width:1.2rem;height:1.2rem;" class="me-1 text-warning"></i> Auto Check-In</span>
                            <small class="text-muted">Submitting will issue a Checked-In pass with current timestamp.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;"></i> Issue Visitor Pass
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const radioStudent = document.getElementById('meet_student');
    const radioStaff = document.getElementById('meet_staff');
    const radioGeneral = document.getElementById('meet_general');

    const studentSec = document.getElementById('student_section');
    const staffSec = document.getElementById('staff_section');
    const generalSec = document.getElementById('general_section');

    function toggleSections() {
        if (radioStudent.checked) {
            studentSec.classList.remove('d-none');
            staffSec.classList.add('d-none');
            generalSec.classList.add('d-none');
        } else if (radioStaff.checked) {
            staffSec.classList.remove('d-none');
            studentSec.classList.add('d-none');
            generalSec.classList.add('d-none');
        } else {
            generalSec.classList.remove('d-none');
            studentSec.classList.add('d-none');
            staffSec.classList.add('d-none');
        }
    }

    radioStudent.addEventListener('change', toggleSections);
    radioStaff.addEventListener('change', toggleSections);
    radioGeneral.addEventListener('change', toggleSections);

    // AJAX Student Loader
    const classSelect = document.getElementById('class_select');
    const studentSelect = document.getElementById('student_select');

    if (classSelect) {
        classSelect.addEventListener('change', function () {
            const className = this.value;
            studentSelect.innerHTML = '<option value="">Loading students...</option>';

            if (!className) {
                studentSelect.innerHTML = '<option value="">-- Select Class First --</option>';
                return;
            }

            fetch(`/visitors/get-students/${encodeURIComponent(className)}`)
                .then(res => res.json())
                .then(data => {
                    studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
                    if (data.students && data.students.length > 0) {
                        data.students.forEach(st => {
                            const opt = document.createElement('option');
                            opt.value = st.id;
                            opt.textContent = `${st.first_name} ${st.last_name} (Roll #${st.roll_no})`;
                            studentSelect.appendChild(opt);
                        });
                    } else {
                        studentSelect.innerHTML = '<option value="">No students found in this class</option>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    studentSelect.innerHTML = '<option value="">Error loading students</option>';
                });
        });
    }

    toggleSections();
});
</script>
@endpush
