@extends('layouts.app')

@section('title', 'Add New House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/houses-create.css') }}">
@endpush

@section('content')
<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Add New House</h1>
        <p class="page-subtitle text-muted">Create a new school house for student group activities</p>
    </div>
    <div>
        <a href="{{ route('houses.index') }}" class="btn btn-secondary btn-sm">
            <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('houses.store') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label class="form-label fw-medium">House Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Red Dragons, Jinnah House" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-medium">House Code</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g. RED, JIN">
                @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Theme Color</label>
                <div class="input-group">
                    <input type="color" id="colorPicker" class="form-control form-control-color" value="{{ old('color', '#6366f1') }}" title="Choose house color" onchange="document.getElementById('colorInput').value = this.value">
                    <input type="text" id="colorInput" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', '#6366f1') }}" placeholder="#6366f1 or Red">
                </div>
                @error('color')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">House Master (Teacher / Staff)</label>
                <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror">
                    <option value="">Select Staff Member (Optional)</option>
                    @foreach ($staffMembers as $staff)
                    <option value="{{ $staff->id }}" @selected(old('staff_id') == $staff->id)>
                        {{ $staff->first_name }} {{ $staff->last_name }} ({{ ucfirst($staff->designation ?? 'Staff') }})
                    </option>
                    @endforeach
                </select>
                @error('staff_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">
                    Assign Students <span class="text-muted font-normal">(Select multiple students to allocate to this house)</span>
                </label>
                <div class="border rounded-3 p-3 bg-light-subtle">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <div class="input-group input-group-sm" style="max-width: 320px;">
                            <span class="input-group-text bg-white"><i data-lucide="search" style="width:0.875rem;height:0.875rem;"></i></span>
                            <input type="text" id="studentSearchInput" class="form-control" placeholder="Search student, class, section, or admission no...">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="selectAllStudentsBtn">Select All</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllStudentsBtn">Deselect All</button>
                        </div>
                    </div>

                    <div class="border rounded-3 p-2 bg-white" style="max-height: 260px; overflow-y: auto;" id="studentListContainer">
                        @forelse ($availableStudents as $student)
                        @php
                            $isOldSelected = is_array(old('student_ids')) && in_array($student->id, old('student_ids'));
                        @endphp
                        <div class="form-check p-2 border-bottom student-item" data-search-text="{{ strtolower($student->first_name . ' ' . $student->last_name . ' ' . $student->admission_no . ' ' . $student->class_name . ' ' . $student->section_name) }}">
                            <input class="form-check-input student-checkbox ms-1 me-2" type="checkbox" name="student_ids[]" value="{{ $student->id }}" id="student_check_{{ $student->id }}" @checked($isOldSelected)>
                            <label class="form-check-label w-100 d-flex flex-wrap align-items-center justify-content-between gap-2 cursor-pointer" for="student_check_{{ $student->id }}">
                                <div>
                                    <strong class="text-dark">{{ $student->first_name }} {{ $student->last_name }}</strong>
                                    <span class="text-tertiary text-xs ms-1">({{ $student->admission_no }})</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Class: {{ $student->class_name ?: 'N/A' }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Section: {{ $student->section_name ?: 'N/A' }}</span>
                                    @if ($student->house_name)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">House: {{ $student->house_name }}</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3 mb-0">No students registered in the system yet.</p>
                        @endforelse
                    </div>
                    <small class="text-muted d-block mt-2">Selected students will be automatically assigned to this house.</small>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-medium">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Brief details about house history, motto, or guidelines...">{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('houses.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create House</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/houses-create.js') }}"></script>
@endpush
