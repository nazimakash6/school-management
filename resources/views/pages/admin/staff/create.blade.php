@extends('layouts.app')

@section('title', 'Create Staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admission.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/staff-create.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('js/admission.js') }}?v={{ time() }}"></script>
@endpush

@section('content')
    <div class="content-header mb-4">
        <div>
            <h1 class="page-title">Create Staff</h1>
            <p class="page-subtitle">Add a new staff member with personal, employment, and financial details</p>
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

    <div class="card admission-card">
        <div class="card-body">
            <!-- Stepper Tabs -->
            <div class="admission-stepper mb-4" role="tablist" aria-label="Staff Creation Steps">
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
                    <span class="admission-step-text">Payroll</span>
                </button>
                <button class="admission-step" type="button" data-step-target="4">
                    <span class="admission-step-number">5</span>
                    <span class="admission-step-text">Documents & Review</span>
                </button>
            </div>

            <!-- Multi-Step Form -->
            <form id="staffCreateForm" class="row g-4" data-step-form method="POST" action="{{ route('staff.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Step 1: Personal Details -->
                <div class="col-12 admission-step-panel is-active" data-step-panel="0">
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Personal Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Basic personal details and identification of the staff member.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name') }}" placeholder="e.g. Mohammad" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name') }}" placeholder="e.g. Ali">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Select gender</option>
                                    <option value="male" @selected(old('gender') === 'male')>Male</option>
                                    <option value="female" @selected(old('gender') === 'female')>Female</option>
                                    <option value="other" @selected(old('gender') === 'other')>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                    value="{{ old('dob') }}" required>
                                @error('dob')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CNIC <span class="text-danger">*</span></label>
                                <input type="text" name="cnic" class="form-control @error('cnic') is-invalid @enderror"
                                    value="{{ old('cnic') }}" maxlength="13" placeholder="e.g. 3520112345671" required>
                                @error('cnic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Marital Status <span class="text-danger">*</span></label>
                                <select name="marital_status" class="form-select @error('marital_status') is-invalid @enderror" required>
                                    <option value="">Select marital status</option>
                                    <option value="single" @selected(old('marital_status') === 'single')>Single</option>
                                    <option value="married" @selected(old('marital_status') === 'married')>Married</option>
                                    <option value="divorced" @selected(old('marital_status') === 'divorced')>Divorced</option>
                                    <option value="widowed" @selected(old('marital_status') === 'widowed')>Widowed</option>
                                </select>
                                @error('marital_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Blood Group</label>
                                <select name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                    <option value="">Select blood group</option>
                                    <option value="A+" @selected(old('blood_group') === 'A+')>A+</option>
                                    <option value="A-" @selected(old('blood_group') === 'A-')>A-</option>
                                    <option value="B+" @selected(old('blood_group') === 'B+')>B+</option>
                                    <option value="B-" @selected(old('blood_group') === 'B-')>B-</option>
                                    <option value="AB+" @selected(old('blood_group') === 'AB+')>AB+</option>
                                    <option value="AB-" @selected(old('blood_group') === 'AB-')>AB-</option>
                                    <option value="O+" @selected(old('blood_group') === 'O+')>O+</option>
                                    <option value="O-" @selected(old('blood_group') === 'O-')>O-</option>
                                </select>
                                @error('blood_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Religion</label>
                                <input type="text" name="religion" class="form-control @error('religion') is-invalid @enderror"
                                    value="{{ old('religion') }}" placeholder="e.g. Islam">
                                @error('religion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nationality</label>
                                <input type="text" name="nationality" class="form-control @error('nationality') is-invalid @enderror"
                                    value="{{ old('nationality') }}" placeholder="e.g. Pakistani">
                                @error('nationality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <a href="{{ route('staff.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="button" class="btn btn-primary btn-sm" data-step-next>Next: Contact Details &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Contact & Address -->
                <div class="col-12 admission-step-panel" data-step-panel="1" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Contact & Address Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Phone numbers, email address, emergency contacts, and residential locations.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Mobile No <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_no" class="form-control @error('mobile_no') is-invalid @enderror"
                                    value="{{ old('mobile_no') }}" placeholder="e.g. 03001234567" required>
                                @error('mobile_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Alternate Mobile No</label>
                                <input type="text" name="alternate_mobile_no" class="form-control @error('alternate_mobile_no') is-invalid @enderror"
                                    value="{{ old('alternate_mobile_no') }}" placeholder="e.g. 03217654321">
                                @error('alternate_mobile_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="e.g. staff@example.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
                                <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                    value="{{ old('emergency_contact_name') }}" placeholder="e.g. Robert Ali" required>
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="emergency_contact_number" class="form-control @error('emergency_contact_number') is-invalid @enderror"
                                    value="{{ old('emergency_contact_number') }}" placeholder="e.g. 03009876543" required>
                                @error('emergency_contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Relation <span class="text-danger">*</span></label>
                                <input type="text" name="emergency_contact_relation" class="form-control @error('emergency_contact_relation') is-invalid @enderror"
                                    value="{{ old('emergency_contact_relation') }}" placeholder="e.g. Brother / Father" required>
                                @error('emergency_contact_relation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Current Address <span class="text-danger">*</span></label>
                                <textarea name="current_address" class="form-control @error('current_address') is-invalid @enderror" rows="3"
                                    placeholder="Enter full present address" required>{{ old('current_address') }}</textarea>
                                @error('current_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Permanent Address <span class="text-danger">*</span></label>
                                <textarea name="permanent_address" class="form-control @error('permanent_address') is-invalid @enderror" rows="3"
                                    placeholder="Enter full permanent home address" required>{{ old('permanent_address') }}</textarea>
                                @error('permanent_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-step-prev>&larr; Previous</button>
                            <button type="button" class="btn btn-primary btn-sm" data-step-next>Next: Employment Details &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Employment Details -->
                <div class="col-12 admission-step-panel" data-step-panel="2" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Employment Details</h5>
                            <p class="mb-0 text-sm text-tertiary">Department, designation, qualification, joining dates, and employment status.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                <select name="department" class="form-select @error('department') is-invalid @enderror" required>
                                    <option value="">Select department</option>
                                    <option value="administration" @selected(old('department') === 'administration')>Administration</option>
                                    <option value="teaching" @selected(old('department') === 'teaching')>Teaching</option>
                                    <option value="accounts" @selected(old('department') === 'accounts')>Accounts</option>
                                    <option value="transport" @selected(old('department') === 'transport')>Transport</option>
                                    <option value="security" @selected(old('department') === 'security')>Security</option>
                                    <option value="maintenance" @selected(old('department') === 'maintenance')>Maintenance</option>
                                    <option value="library" @selected(old('department') === 'library')>Library</option>
                                    <option value="laboratory" @selected(old('department') === 'laboratory')>Laboratory</option>
                                    <option value="sports" @selected(old('department') === 'sports')>Sports</option>
                                    <option value="it" @selected(old('department') === 'it')>IT</option>
                                    <option value="cleaning" @selected(old('department') === 'cleaning')>Cleaning</option>
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Designation <span class="text-danger">*</span></label>
                                <select name="designation" class="form-select @error('designation') is-invalid @enderror" required>
                                    <option value="">Select designation</option>
                                    <option value="principal" @selected(old('designation') === 'principal')>Principal</option>
                                    <option value="vice_principal" @selected(old('designation') === 'vice_principal')>Vice Principal</option>
                                    <option value="coordinator" @selected(old('designation') === 'coordinator')>Coordinator</option>
                                    <option value="head_teacher" @selected(old('designation') === 'head_teacher')>Head Teacher</option>
                                    <option value="teacher" @selected(old('designation') === 'teacher')>Teacher</option>
                                    <option value="accountant" @selected(old('designation') === 'accountant')>Accountant</option>
                                    <option value="clerk" @selected(old('designation') === 'clerk')>Clerk</option>
                                    <option value="receptionist" @selected(old('designation') === 'receptionist')>Receptionist</option>
                                    <option value="librarian" @selected(old('designation') === 'librarian')>Librarian</option>
                                    <option value="lab_assistant" @selected(old('designation') === 'lab_assistant')>Lab Assistant</option>
                                    <option value="computer_operator" @selected(old('designation') === 'computer_operator')>Computer Operator</option>
                                    <option value="office_boy" @selected(old('designation') === 'office_boy')>Office Boy</option>
                                    <option value="peon" @selected(old('designation') === 'peon')>Peon</option>
                                    <option value="driver" @selected(old('designation') === 'driver')>Driver</option>
                                    <option value="sweeper" @selected(old('designation') === 'sweeper')>Sweeper</option>
                                    <option value="security_guard" @selected(old('designation') === 'security_guard')>Security Guard</option>
                                    <option value="cleaner" @selected(old('designation') === 'cleaner')>Cleaner</option>
                                    <option value="gardener" @selected(old('designation') === 'gardener')>Gardener</option>
                                    <option value="sports_coach" @selected(old('designation') === 'sports_coach')>Sports Coach</option>
                                    <option value="electrician" @selected(old('designation') === 'electrician')>Electrician</option>
                                    <option value="it_support" @selected(old('designation') === 'it_support')>IT Support</option>
                                    <option value="maintenance_staff" @selected(old('designation') === 'maintenance_staff')>Maintenance Staff</option>
                                </select>
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Qualification <span class="text-danger">*</span></label>
                                <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror"
                                    value="{{ old('qualification') }}" placeholder="e.g. Master in Education / BS CS" required>
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Experience</label>
                                <input type="text" name="experience" class="form-control @error('experience') is-invalid @enderror"
                                    value="{{ old('experience') }}" placeholder="e.g. 5 Years">
                                @error('experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                                    <option value="">Select employment type</option>
                                    <option value="full_time" @selected(old('employment_type') === 'full_time')>Full Time</option>
                                    <option value="part_time" @selected(old('employment_type') === 'part_time')>Part Time</option>
                                    <option value="contract" @selected(old('employment_type') === 'contract')>Contract</option>
                                    <option value="internship" @selected(old('employment_type') === 'internship')>Internship</option>
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                                <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror"
                                    value="{{ old('joining_date') }}" required>
                                @error('joining_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Leaving Date</label>
                                <input type="date" name="leaving_date" class="form-control @error('leaving_date') is-invalid @enderror"
                                    value="{{ old('leaving_date') }}">
                                @error('leaving_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Shift</label>
                                <input type="text" name="shift" class="form-control @error('shift') is-invalid @enderror"
                                    value="{{ old('shift') }}" placeholder="e.g. Morning / Evening">
                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-step-prev>&larr; Previous</button>
                            <button type="button" class="btn btn-primary btn-sm" data-step-next>Next: Payroll &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Payroll -->
                <div class="col-12 admission-step-panel" data-step-panel="3" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Payroll Information</h5>
                            <p class="mb-0 text-sm text-tertiary">Salary details and bank account information.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Salary Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="salary" class="form-control @error('salary') is-invalid @enderror"
                                    value="{{ old('salary') }}" placeholder="e.g. 50000" required>
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Salary Frequency</label>
                                <select name="salary_type" class="form-select @error('salary_type') is-invalid @enderror">
                                    <option value="">Select salary type</option>
                                    <option value="monthly" @selected(old('salary_type') === 'monthly')>Monthly</option>
                                    <option value="weekly" @selected(old('salary_type') === 'weekly')>Weekly</option>
                                    <option value="daily" @selected(old('salary_type') === 'daily')>Daily</option>
                                    <option value="hourly" @selected(old('salary_type') === 'hourly')>Hourly</option>
                                </select>
                                @error('salary_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                                    value="{{ old('bank_name') }}" placeholder="e.g. Habib Bank Limited">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Bank Account Title</label>
                                <input type="text" name="bank_account_title" class="form-control @error('bank_account_title') is-invalid @enderror"
                                    value="{{ old('bank_account_title') }}" placeholder="e.g. Mohammad Ali">
                                @error('bank_account_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Bank Account Number</label>
                                <input type="text" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror"
                                    value="{{ old('bank_account_number') }}" placeholder="e.g. 12345678901234">
                                @error('bank_account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">IBAN</label>
                                <input type="text" name="iban" class="form-control @error('iban') is-invalid @enderror"
                                    value="{{ old('iban') }}" placeholder="e.g. PK36HABB0012345678901234">
                                @error('iban')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-step-prev>&larr; Previous</button>
                            <button type="button" class="btn btn-primary btn-sm" data-step-next>Next: Documents & Review &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Documents & Review -->
                <div class="col-12 admission-step-panel" data-step-panel="4" hidden>
                    <div class="card border-0 bg-light-subtle">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <h5 class="mb-1 fw-bold">Documents & Additional Notes</h5>
                            <p class="mb-0 text-sm text-tertiary">Upload CV, profile photo, and add any final notes before saving.</p>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Attach CV (PDF, DOC, DOCX)</label>
                                <input type="file" name="cv" class="form-control @error('cv') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                @error('cv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Profile Photo (JPEG, PNG, WEBP)</label>
                                <input type="file" name="profile_picture" class="form-control @error('profile_picture') is-invalid @enderror" accept="image/*">
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notes / Remarks</label>
                                <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="4"
                                    placeholder="Add any extra notes or remarks here...">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-step-prev>&larr; Previous</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Save Staff Member
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
