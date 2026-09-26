@extends('layouts.app')

@section('title', 'Edit Staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admission.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/staff-edit.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('js/admission.js') }}?v={{ time() }}"></script>
@endpush

@section('content')
    <div class="content-header mb-4">
        <div>
            <h1 class="page-title">Edit Staff</h1>
            <p class="page-subtitle">Update information for {{ trim($staff->first_name . ' ' . ($staff->last_name ?? '')) }}
                ({{ $staff->staff_id }})</p>
        </div>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary"><i data-lucide="arrow-left"
                    style="width:1rem;height:1rem;"></i> Back</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong>Please correct the errors below:</strong>
            <ul class="mb-0 ps-3 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card admission-card">
        <div class="card-body">
            <!-- Stepper Tabs -->
            <div class="admission-stepper mb-4" role="tablist" aria-label="Staff Edit Steps">
                <button class="admission-step active" type="button" data-step-target="0" aria-current="step">
                    <span class="admission-step-number">1</span>
                    <span class="admission-step-text">Personal Details</span>
                </button>
                <button class="admission-step" type="button" data-step-target="1">
                    <span class="admission-step-number">2</span>
                    <span class="admission-step-text">Contact & Address</span>
                </button>
                <button class="admission-step" type="button" data-step-target="2">
                    <span class="admission-step-number">3</span>
                    <span class="admission-step-text">Employment Details</span>
                </button>
                <button class="admission-step" type="button" data-step-target="3">
                    <span class="admission-step-number">4</span>
                    <span class="admission-step-text">Payroll & Vehicle</span>
                </button>
                <button class="admission-step" type="button" data-step-target="4">
                    <span class="admission-step-number">5</span>
                    <span class="admission-step-text">Documents & Review</span>
                </button>
            </div>

            <!-- Multi-Step Form -->
            <form id="staffEditForm" class="row g-4" data-step-form method="POST"
                action="{{ route('staff.update', $staff) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Step 1: Personal Details -->
                <div class="col-12 admission-step-panel is-active" data-step-panel="0">
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Personal Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Basic personal details and identification of the staff
                                member.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Staff ID</label>
                                <input type="text" class="form-control bg-light" value="{{ $staff->staff_id }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name', $staff->first_name) }}" placeholder="e.g. Mohammad"
                                    required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name', $staff->last_name) }}" placeholder="e.g. Ali">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Select gender</option>
                                    <option value="male" @selected(old('gender', $staff->gender) === 'male')>Male</option>
                                    <option value="female" @selected(old('gender', $staff->gender) === 'female')>Female
                                    </option>
                                    <option value="other" @selected(old('gender', $staff->gender) === 'other')>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                    value="{{ old('dob', $staff->dob) }}" required>
                                @error('dob')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CNIC <span class="text-danger">*</span></label>
                                <input type="text" name="cnic" class="form-control @error('cnic') is-invalid @enderror"
                                    value="{{ old('cnic', $staff->cnic) }}" maxlength="13" placeholder="e.g. 3520112345671"
                                    required>
                                @error('cnic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Marital Status <span class="text-danger">*</span></label>
                                <select name="marital_status"
                                    class="form-select @error('marital_status') is-invalid @enderror" required>
                                    <option value="">Select marital status</option>
                                    <option value="single" @selected(old('marital_status', $staff->marital_status) === 'single')>Single</option>
                                    <option value="married" @selected(old('marital_status', $staff->marital_status) === 'married')>Married</option>
                                    <option value="divorced" @selected(old('marital_status', $staff->marital_status) === 'divorced')>Divorced</option>
                                    <option value="widowed" @selected(old('marital_status', $staff->marital_status) === 'widowed')>Widowed</option>
                                </select>
                                @error('marital_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Blood Group</label>
                                <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                    <option value="">Select blood group</option>
                                    <option value="A+" @selected(old('blood_group', $staff->blood_group) === 'A+')>A+</option>
                                    <option value="A-" @selected(old('blood_group', $staff->blood_group) === 'A-')>A-</option>
                                    <option value="B+" @selected(old('blood_group', $staff->blood_group) === 'B+')>B+</option>
                                    <option value="B-" @selected(old('blood_group', $staff->blood_group) === 'B-')>B-</option>
                                    <option value="AB+" @selected(old('blood_group', $staff->blood_group) === 'AB+')>AB+
                                    </option>
                                    <option value="AB-" @selected(old('blood_group', $staff->blood_group) === 'AB-')>AB-
                                    </option>
                                    <option value="O+" @selected(old('blood_group', $staff->blood_group) === 'O+')>O+</option>
                                    <option value="O-" @selected(old('blood_group', $staff->blood_group) === 'O-')>O-</option>
                                </select>
                                @error('blood_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Religion</label>
                                <input type="text" name="religion"
                                    class="form-control @error('religion') is-invalid @enderror"
                                    value="{{ old('religion', $staff->religion) }}" placeholder="e.g. Islam">
                                @error('religion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nationality</label>
                                <input type="text" name="nationality"
                                    class="form-control @error('nationality') is-invalid @enderror"
                                    value="{{ old('nationality', $staff->nationality) }}" placeholder="e.g. Pakistani">
                                @error('nationality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <a href="{{ route('staff.show', $staff) }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="button" class="btn btn-primary btn-sm" data-step-next>Next: Contact Details
                                &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Contact & Address -->
                <div class="col-12 admission-step-panel" data-step-panel="1" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Contact & Address Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Phone numbers, email address, emergency contacts, and
                                residential locations.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Mobile No <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_no"
                                    class="form-control @error('mobile_no') is-invalid @enderror"
                                    value="{{ old('mobile_no', $staff->mobile_no) }}" placeholder="e.g. 03001234567"
                                    required>
                                @error('mobile_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Alternate Mobile No</label>
                                <input type="text" name="alternate_mobile_no"
                                    class="form-control @error('alternate_mobile_no') is-invalid @enderror"
                                    value="{{ old('alternate_mobile_no', $staff->alternate_mobile_no) }}"
                                    placeholder="e.g. 03217654321">
                                @error('alternate_mobile_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $staff->email) }}" placeholder="e.g. staff@example.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
                                <input type="text" name="emergency_contact_name"
                                    class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                    value="{{ old('emergency_contact_name', $staff->emergency_contact_name) }}"
                                    placeholder="e.g. Robert Ali" required>
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="emergency_contact_number"
                                    class="form-control @error('emergency_contact_number') is-invalid @enderror"
                                    value="{{ old('emergency_contact_number', $staff->emergency_contact_number) }}"
                                    placeholder="e.g. 03009876543" required>
                                @error('emergency_contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Relation <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="emergency_contact_relation"
                                    class="form-control @error('emergency_contact_relation') is-invalid @enderror"
                                    value="{{ old('emergency_contact_relation', $staff->emergency_contact_relation) }}"
                                    placeholder="e.g. Brother / Father" required>
                                @error('emergency_contact_relation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Current Address <span class="text-danger">*</span></label>
                                <textarea name="current_address"
                                    class="form-control @error('current_address') is-invalid @enderror" rows="3"
                                    placeholder="Enter full present address"
                                    required>{{ old('current_address', $staff->current_address) }}</textarea>
                                @error('current_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Permanent Address <span class="text-danger">*</span></label>
                                <textarea name="permanent_address"
                                    class="form-control @error('permanent_address') is-invalid @enderror" rows="3"
                                    placeholder="Enter full permanent home address"
                                    required>{{ old('permanent_address', $staff->permanent_address) }}</textarea>
                                @error('permanent_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-step-prev>&larr; Previous</button>
                            <button type="button" class="btn btn-primary btn-sm" data-step-next>Next: Employment Details
                                &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Employment Details -->
                <div class="col-12 admission-step-panel" data-step-panel="2" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Employment Details</h5>
                            <p class="mb-0 text-sm text-tertiary">Department, designation, qualification, joining dates, and
                                employment status.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department" class="form-select @error('department') is-invalid @enderror"
                                    required>
                                    <option value="">Select department</option>
                                    <option value="administration" @selected(old('department', $staff->department) === 'administration')>Administration</option>
                                    <option value="teaching" @selected(old('department', $staff->department) === 'teaching')>
                                        Teaching</option>
                                    <option value="accounts" @selected(old('department', $staff->department) === 'accounts')>
                                        Accounts</option>
                                    <option value="transport" @selected(old('department', $staff->department) === 'transport')>Transport</option>
                                    <option value="security" @selected(old('department', $staff->department) === 'security')>
                                        Security</option>
                                    <option value="maintenance" @selected(old('department', $staff->department) === 'maintenance')>Maintenance</option>
                                    <option value="library" @selected(old('department', $staff->department) === 'library')>
                                        Library</option>
                                    <option value="laboratory" @selected(old('department', $staff->department) === 'laboratory')>Laboratory</option>
                                    <option value="sports" @selected(old('department', $staff->department) === 'sports')>
                                        Sports</option>
                                    <option value="it" @selected(old('department', $staff->department) === 'it')>IT</option>
                                    <option value="cleaning" @selected(old('department', $staff->department) === 'cleaning')>
                                        Cleaning</option>
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Designation <span class="text-danger">*</span></label>
                                <select name="designation" class="form-select @error('designation') is-invalid @enderror"
                                    required>
                                    <option value="">Select designation</option>
                                    <option value="principal" @selected(old('designation', $staff->designation) === 'principal')>Principal</option>
                                    <option value="vice_principal" @selected(old('designation', $staff->designation) === 'vice_principal')>Vice Principal</option>
                                    <option value="coordinator" @selected(old('designation', $staff->designation) === 'coordinator')>Coordinator</option>
                                    <option value="head_teacher" @selected(old('designation', $staff->designation) === 'head_teacher')>Head Teacher</option>
                                    <option value="teacher" @selected(old('designation', $staff->designation) === 'teacher')>
                                        Teacher</option>
                                    <option value="accountant" @selected(old('designation', $staff->designation) === 'accountant')>Accountant</option>
                                    <option value="clerk" @selected(old('designation', $staff->designation) === 'clerk')>Clerk
                                    </option>
                                    <option value="receptionist" @selected(old('designation', $staff->designation) === 'receptionist')>Receptionist</option>
                                    <option value="librarian" @selected(old('designation', $staff->designation) === 'librarian')>Librarian</option>
                                    <option value="lab_assistant" @selected(old('designation', $staff->designation) === 'lab_assistant')>Lab Assistant</option>
                                    <option value="computer_operator" @selected(old('designation', $staff->designation) === 'computer_operator')>Computer Operator</option>
                                    <option value="office_boy" @selected(old('designation', $staff->designation) === 'office_boy')>Office Boy</option>
                                    <option value="peon" @selected(old('designation', $staff->designation) === 'peon')>Peon
                                    </option>
                                    <option value="driver" @selected(old('designation', $staff->designation) === 'driver')>
                                        Driver</option>
                                    <option value="sweeper" @selected(old('designation', $staff->designation) === 'sweeper')>
                                        Sweeper</option>
                                    <option value="security_guard" @selected(old('designation', $staff->designation) === 'security_guard')>Security Guard</option>
                                    <option value="cleaner" @selected(old('designation', $staff->designation) === 'cleaner')>
                                        Cleaner</option>
                                    <option value="gardener" @selected(old('designation', $staff->designation) === 'gardener')>Gardener</option>
                                    <option value="sports_coach" @selected(old('designation', $staff->designation) === 'sports_coach')>Sports Coach</option>
                                    <option value="electrician" @selected(old('designation', $staff->designation) === 'electrician')>Electrician</option>
                                    <option value="it_support" @selected(old('designation', $staff->designation) === 'it_support')>IT Support</option>
                                    <option value="maintenance_staff" @selected(old('designation', $staff->designation) === 'maintenance_staff')>Maintenance Staff</option>
                                </select>
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Qualification <span class="text-danger">*</span></label>
                                <input type="text" name="qualification"
                                    class="form-control @error('qualification') is-invalid @enderror"
                                    value="{{ old('qualification', $staff->qualification) }}"
                                    placeholder="e.g. Master in Education / BS CS" required>
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Experience</label>
                                <input type="text" name="experience"
                                    class="form-control @error('experience') is-invalid @enderror"
                                    value="{{ old('experience', $staff->experience) }}" placeholder="e.g. 5 Years">
                                @error('experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select name="employment_type"
                                    class="form-select @error('employment_type') is-invalid @enderror" required>
                                    <option value="">Select employment type</option>
                                    <option value="full_time" @selected(old('employment_type', $staff->employment_type) === 'full_time')>Full Time</option>
                                    <option value="part_time" @selected(old('employment_type', $staff->employment_type) === 'part_time')>Part Time</option>
                                    <option value="contract" @selected(old('employment_type', $staff->employment_type) === 'contract')>Contract</option>
                                    <option value="internship" @selected(old('employment_type', $staff->employment_type) === 'internship')>Internship</option>
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                                <input type="date" name="joining_date"
                                    class="form-control @error('joining_date') is-invalid @enderror"
                                    value="{{ old('joining_date', $staff->joining_date) }}" required>
                                @error('joining_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Leaving Date</label>
                                <input type="date" name="leaving_date"
                                    class="form-control @error('leaving_date') is-invalid @enderror"
                                    value="{{ old('leaving_date', $staff->leaving_date) }}">
                                @error('leaving_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <input type="text" name="shift" class="form-control @error('shift') is-invalid @enderror"
                                    value="{{ old('shift', $staff->shift) }}" placeholder="e.g. Morning / Evening">
                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" @selected(old('status', $staff->status) === 'active')>Active
                                    </option>
                                    <option value="inactive" @selected(old('status', $staff->status) === 'inactive')>Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-step-prev>&larr; Previous</button>
                            <button type="button" class="btn btn-primary btn-sm" data-step-next>Next: Payroll & Vehicle
                                &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Payroll & Vehicle -->
                <div class="col-12 admission-step-panel" data-step-panel="3" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Payroll & Vehicle Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Salary details, bank account information, driving license,
                                and vehicle registration.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Salary Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="salary"
                                    class="form-control @error('salary') is-invalid @enderror"
                                    value="{{ old('salary', $staff->salary) }}" placeholder="e.g. 50000" required>
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Salary Frequency</label>
                                <select name="salary_type" class="form-select @error('salary_type') is-invalid @enderror">
                                    <option value="">Select salary type</option>
                                    <option value="monthly" @selected(old('salary_type', $staff->salary_type) === 'monthly')>
                                        Monthly</option>
                                    <option value="weekly" @selected(old('salary_type', $staff->salary_type) === 'weekly')>
                                        Weekly</option>
                                    <option value="daily" @selected(old('salary_type', $staff->salary_type) === 'daily')>Daily
                                    </option>
                                    <option value="hourly" @selected(old('salary_type', $staff->salary_type) === 'hourly')>
                                        Hourly</option>
                                </select>
                                @error('salary_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name"
                                    class="form-control @error('bank_name') is-invalid @enderror"
                                    value="{{ old('bank_name', $staff->bank_name) }}" placeholder="e.g. Habib Bank Limited">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Bank Account Title</label>
                                <input type="text" name="bank_account_title"
                                    class="form-control @error('bank_account_title') is-invalid @enderror"
                                    value="{{ old('bank_account_title', $staff->bank_account_title) }}"
                                    placeholder="e.g. Mohammad Ali">
                                @error('bank_account_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Bank Account Number</label>
                                <input type="text" name="bank_account_number"
                                    class="form-control @error('bank_account_number') is-invalid @enderror"
                                    value="{{ old('bank_account_number', $staff->bank_account_number) }}"
                                    placeholder="e.g. 12345678901234">
                                @error('bank_account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">IBAN</label>
                                <input type="text" name="iban" class="form-control @error('iban') is-invalid @enderror"
                                    value="{{ old('iban', $staff->iban) }}" placeholder="e.g. PK36HABB0012345678901234">
                                @error('iban')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Driving License Number</label>
                                <input type="text" name="driving_license_number"
                                    class="form-control @error('driving_license_number') is-invalid @enderror"
                                    value="{{ old('driving_license_number', $staff->driving_license_number) }}"
                                    placeholder="e.g. LHR-123456">
                                @error('driving_license_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Driving License Expiry</label>
                                <input type="date" name="driving_license_expiry"
                                    class="form-control @error('driving_license_expiry') is-invalid @enderror"
                                    value="{{ old('driving_license_expiry', $staff->driving_license_expiry) }}">
                                @error('driving_license_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Vehicle Registration Number</label>
                                <input type="text" name="vehicle_registration_number"
                                    class="form-control @error('vehicle_registration_number') is-invalid @enderror"
                                    value="{{ old('vehicle_registration_number', $staff->vehicle_registration_number) }}"
                                    placeholder="e.g. LEA-12-3456">
                                @error('vehicle_registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Vehicle Type</label>
                                <input type="text" name="vehicle_type"
                                    class="form-control @error('vehicle_type') is-invalid @enderror"
                                    value="{{ old('vehicle_type', $staff->vehicle_type) }}"
                                    placeholder="e.g. Motorbike / Car / Van">
                                @error('vehicle_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-step-prev>&larr; Previous</button>
                            <button type="button" class="btn btn-primary btn-sm" data-step-next>Next: Documents & Review
                                &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Documents & Review -->
                <div class="col-12 admission-step-panel" data-step-panel="4" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Documents & Additional Notes</h5>
                            <p class="mb-0 text-sm text-tertiary">Upload or update CV, profile photo, and add any final
                                notes before saving.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Attach CV (PDF, DOC, DOCX)</label>
                                <input type="file" name="cv" class="form-control @error('cv') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx">
                                @if ($staff->cv)
                                    <small class="text-muted d-block mt-1">Current file: <a
                                            href="{{ asset('storage/' . $staff->cv) }}"
                                            target="_blank">{{ basename($staff->cv) }}</a></small>
                                @endif
                                @error('cv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Profile Photo (JPEG, PNG, WEBP)</label>
                                <input type="file" name="profile_picture"
                                    class="form-control @error('profile_picture') is-invalid @enderror" accept="image/*">
                                @if ($staff->profile_picture)
                                    <small class="text-muted d-block mt-1">Current image: <a
                                            href="{{ asset('storage/' . $staff->profile_picture) }}"
                                            target="_blank">{{ basename($staff->profile_picture) }}</a></small>
                                @endif
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notes / Remarks</label>
                                <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="4"
                                    placeholder="Add any extra notes or remarks here...">{{ old('note', $staff->note) }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-step-prev>&larr; Previous</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Update Staff Member
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection