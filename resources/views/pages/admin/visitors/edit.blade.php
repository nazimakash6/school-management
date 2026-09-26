@extends('layouts.app')

@section('title', 'Edit Visitor Entry - Pass #' . $visitor->pass_code)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('visitors.index') }}">Visitors</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Visitor</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Edit Visitor Log & Pass Status</h1>
            <p class="text-muted small mb-0">Update visitor information, exit timestamp, status, or proof attachments</p>
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

    <form action="{{ route('visitors.update', $visitor->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Form Column -->
            <div class="col-lg-8">
                <!-- 1. Visitor Details -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            1. Visitor Identity & Contact
                        </h6>
                        <span class="badge bg-dark text-white font-monospace">{{ $visitor->pass_code }}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Visitor Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="visitor_name" class="form-control @error('visitor_name') is-invalid @enderror" value="{{ old('visitor_name', $visitor->visitor_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Mobile / Contact Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $visitor->phone) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">CNIC / ID Card Number</label>
                                <input type="text" name="cnic_id" class="form-control" value="{{ old('cnic_id', $visitor->cnic_id) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">No. of Persons <span class="text-danger">*</span></label>
                                <input type="number" name="num_persons" class="form-control" value="{{ old('num_persons', $visitor->num_persons) }}" min="1" max="20" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Entry Gate <span class="text-danger">*</span></label>
                                <select name="gate_no" class="form-select" required>
                                    @foreach($gates as $g)
                                        <option value="{{ $g }}" {{ old('gate_no', $visitor->gate_no) == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Vehicle Plate / Type</label>
                                <input type="text" name="vehicle_no" class="form-control" value="{{ old('vehicle_no', $visitor->vehicle_no) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Purpose of Visit <span class="text-danger">*</span></label>
                                <select name="purpose" class="form-select" required>
                                    @foreach($purposes as $p)
                                        <option value="{{ $p }}" {{ old('purpose', $visitor->purpose) == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Meet Type Target <span class="text-danger">*</span></label>
                                <select name="meet_type" class="form-select" required>
                                    @foreach($meetTypes as $mt)
                                        <option value="{{ $mt }}" {{ old('meet_type', $visitor->meet_type) == $mt ? 'selected' : '' }}>{{ $mt }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Person / Office Description</label>
                                <input type="text" name="person_to_meet" class="form-control" value="{{ old('person_to_meet', $visitor->person_to_meet) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Status & Photo Proof -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="shield-check" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            2. Pass Status & Photo Proof
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Campus Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Checked-In" {{ old('status', $visitor->status) == 'Checked-In' ? 'selected' : '' }}>Checked-In (Inside Campus)</option>
                                    <option value="Checked-Out" {{ old('status', $visitor->status) == 'Checked-Out' ? 'selected' : '' }}>Checked-Out (Left Campus)</option>
                                    <option value="Blocked" {{ old('status', $visitor->status) == 'Blocked' ? 'selected' : '' }}>Blocked / Entry Denied</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Replace ID Proof / Photo</label>
                                @if($visitor->id_proof_url)
                                    <div class="mb-2">
                                        <a href="{{ $visitor->id_proof_url }}" target="_blank" class="btn btn-sm btn-light border">
                                            <i data-lucide="image" style="width:0.9rem;height:0.9rem;" class="me-1"></i> View Existing Photo
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="id_proof_image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark">Security Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $visitor->remarks) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0">Save Entry Changes</h6>
                    </div>
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="save" style="width:1.25rem;height:1.25rem;"></i> Update Visitor Pass
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
