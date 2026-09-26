@extends('layouts.app')

@section('title', 'Schedule Meeting - Meetings & Conferences')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Schedule Meeting</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Schedule New Meeting / Conference</h1>
            <p class="text-muted small mb-0">Setup staff meetings, parent-teacher conferences, or council assemblies</p>
        </div>
        <div>
            <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Meetings
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

    <form action="{{ route('meetings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Left Form Area -->
            <div class="col-lg-8">
                <!-- 1. General Meeting Details Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="calendar" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            1. Basic Meeting Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Meeting Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Monthly Staff Review & Exam Planning" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Meeting Type <span class="text-danger">*</span></label>
                                <select name="meeting_type" class="form-select @error('meeting_type') is-invalid @enderror" required>
                                    <option value="">-- Select Type --</option>
                                    @foreach($types as $t)
                                        <option value="{{ $t }}" {{ old('meeting_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Meeting Mode <span class="text-danger">*</span></label>
                                <select name="mode" class="form-select @error('mode') is-invalid @enderror" required>
                                    @foreach($modes as $m)
                                        <option value="{{ $m }}" {{ old('mode') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Meeting Date <span class="text-danger">*</span></label>
                                <input type="date" name="meeting_date" class="form-control @error('meeting_date') is-invalid @enderror" value="{{ old('meeting_date', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', '10:00') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">End Time</label>
                                <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', '11:30') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Venue / Room Location</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="e.g. Main Auditorium, Conference Room B">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Virtual Meeting Link (Zoom / Meet)</label>
                                <input type="url" name="meeting_link" class="form-control" value="{{ old('meeting_link') }}" placeholder="https://meet.google.com/xyz-abc or Zoom URL">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Target Audience / Department <span class="text-danger">*</span></label>
                                <input type="text" name="target_audience" class="form-control @error('target_audience') is-invalid @enderror" value="{{ old('target_audience', 'All Teachers & Staff') }}" placeholder="e.g. All Staff, Class 5 Parents, HODs" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach($statuses as $st)
                                        <option value="{{ $st }}" {{ old('status', 'Scheduled') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Agenda & Documents Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="file-text" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            2. Meeting Agenda & Document Attachment
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Meeting Agenda & Discussion Topics</label>
                            <textarea name="agenda" class="form-control" rows="4" placeholder="Enter key discussion items, bullet points, presentation outline...">{{ old('agenda') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Action Items / Expected Outcomes</label>
                            <textarea name="action_items" class="form-control" rows="3" placeholder="Enter tasks to be assigned or required preparation...">{{ old('action_items') }}</textarea>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark">Attach Agenda PDF / Document (Optional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.png,.jpg,.webp">
                            <small class="text-muted">Upload presentation slides, memo, or agenda PDF (max 10MB)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Invites & Actions -->
            <div class="col-lg-4">
                <!-- Invite Staff Members Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user-check" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
                            Invite Staff Attendees
                        </h6>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" id="selectAllStaff">Select All</button>
                    </div>
                    <div class="card-body p-3" style="max-height: 380px; overflow-y: auto;">
                        @forelse($staffList as $st)
                            <div class="form-check p-2 border-bottom">
                                <input class="form-check-input staff-checkbox" type="checkbox" name="attendees[]" value="{{ $st->id }}" id="staff_{{ $st->id }}" checked>
                                <label class="form-check-label d-block cursor-pointer" for="staff_{{ $st->id }}">
                                    <strong class="text-dark d-block mb-0">{{ $st->first_name }} {{ $st->last_name }}</strong>
                                    <small class="text-muted">{{ $st->designation ?: 'Staff Member' }} &bull; {{ $st->department ?: 'General' }}</small>
                                </label>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No staff members found to invite.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="calendar-check" style="width:1.25rem;height:1.25rem;"></i> Schedule Meeting Now
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
    const selectAllBtn = document.getElementById('selectAllStaff');
    const checkboxes = document.querySelectorAll('.staff-checkbox');
    let allChecked = true;

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function () {
            allChecked = !allChecked;
            checkboxes.forEach(cb => cb.checked = allChecked);
            selectAllBtn.textContent = allChecked ? 'Deselect All' : 'Select All';
        });
    }
});
</script>
@endpush
