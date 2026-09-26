@extends('layouts.app')

@section('title', 'Edit Examination — ' . $examination->title)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('examination.index') }}">Examination</a></li>
                    <li class="breadcrumb-item active">Edit Examination</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Edit Examination: {{ $examination->title }}</h1>
            <p class="text-muted small mb-0">Update examination details, Exam Type, and subject max/pass marks.</p>
        </div>
        <a href="{{ route('examination.show', $examination) }}" class="btn btn-outline-secondary btn-sm">
            <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Exam Details
        </a>
    </div>

    @include('partials.error-alerts')

    <form action="{{ route('examination.update', $examination) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: Basic Details -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="file-text" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                            Examination Basic Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Exam Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $examination->title) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Exam Type <span class="text-danger">*</span></label>
                                <select name="exam_type_id" class="form-select form-select-lg" required>
                                    <option value="">— Select Exam Type —</option>
                                    @foreach($examTypes as $type)
                                        <option value="{{ $type->id }}" @selected(old('exam_type_id', $examination->exam_type_id) == $type->id)>
                                            {{ $type->name }} {{ $type->code ? '('.$type->code.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Academic Session</label>
                                <select name="academic_session_id" class="form-select form-select-lg">
                                    <option value="">— Select Academic Session —</option>
                                    @foreach($academicSessions as $sess)
                                        <option value="{{ $sess->id }}" @selected(old('academic_session_id', $examination->academic_session_id) == $sess->id)>
                                            Session {{ $sess->session_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Target Class Tag System -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark d-flex justify-content-between align-items-center mb-1">
                                    <span>Target Class / Classes <span class="text-danger">*</span></span>
                                    <span class="small">
                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none me-2 fw-semibold" id="selectAllClassesBtn">
                                            <i data-lucide="check-check" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Select All
                                        </button>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-danger fw-semibold" id="clearClassesBtn">
                                            <i data-lucide="x" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Clear
                                        </button>
                                    </span>
                                </label>
                                <div class="mb-2">
                                    <select id="class_select_dropdown" class="form-select">
                                        <option value="">— Choose Class to Add —</option>
                                        <option value="All Classes" class="fw-bold text-primary">✨ All Classes (Whole School)</option>
                                        @foreach($classes as $cls)
                                            <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="target_classes_tag_box" class="form-control d-flex flex-wrap align-items-center gap-1.5 p-2 bg-light border min-vh-0" style="min-height: 52px; border-radius: 8px;">
                                    <!-- Dynamic Tag Badges -->
                                </div>
                                <input type="hidden" name="class_name" id="class_name_hidden" value="{{ old('class_name', $examination->class_name) }}" required>
                                <div class="form-text small mt-1">Select one or more classes for this exam, or choose "All Classes" for a school-wide exam.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Section</label>
                                <input type="text" name="section_name" class="form-control" value="{{ old('section_name', $examination->section_name) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($examination->start_date)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($examination->end_date)->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subject Setup Section -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="book-open" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                            Subjects Included & Max Marks
                        </h6>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addSubjectBtn">
                            <i data-lucide="plus" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Add Subject Row
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="subjectsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject Name</th>
                                        <th style="width: 25%;">Max Marks</th>
                                        <th style="width: 25%;">Passing Marks</th>
                                        <th style="width: 10%; text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $schedules = $examination->schedules;
                                        if($schedules->isEmpty()) {
                                            $schedules = collect([
                                                (object)['subject_name' => 'English', 'max_marks' => 100, 'pass_marks' => 40],
                                                (object)['subject_name' => 'Mathematics', 'max_marks' => 100, 'pass_marks' => 40],
                                                (object)['subject_name' => 'Science', 'max_marks' => 100, 'pass_marks' => 40],
                                            ]);
                                        }
                                    @endphp
                                    @foreach($schedules as $idx => $sch)
                                    <tr>
                                        <td>
                                            <input type="text" name="subjects[{{ $idx }}][name]" class="form-control" value="{{ $sch->subject_name }}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="subjects[{{ $idx }}][max_marks]" class="form-control" value="{{ $sch->max_marks }}" min="1" step="0.5" required>
                                        </td>
                                        <td>
                                            <input type="number" name="subjects[{{ $idx }}][pass_marks]" class="form-control" value="{{ $sch->pass_marks }}" min="0" step="0.5" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Submit -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="settings" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                            Exam Settings & Status
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Default Total Marks <span class="text-danger">*</span></label>
                            <input type="number" name="total_marks" class="form-control" value="{{ old('total_marks', $examination->total_marks) }}" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Default Passing Marks <span class="text-danger">*</span></label>
                            <input type="number" name="pass_marks" class="form-control" value="{{ old('pass_marks', $examination->pass_marks) }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="scheduled" @selected(old('status', $examination->status) == 'scheduled')>Scheduled</option>
                                <option value="ongoing" @selected(old('status', $examination->status) == 'ongoing')>Ongoing</option>
                                <option value="completed" @selected(old('status', $examination->status) == 'completed')>Completed</option>
                                <option value="published" @selected(old('status', $examination->status) == 'published')>Published</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Description / Instructions</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $examination->description) }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                <i data-lucide="check-circle" style="width:1.2rem;height:1.2rem;" class="me-1"></i> Update Examination
                            </button>
                            <a href="{{ route('examination.show', $examination) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let subjectIdx = {{ count($schedules) }};
    const addBtn = document.getElementById('addSubjectBtn');
    const tableBody = document.querySelector('#subjectsTable tbody');

    addBtn.addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="subjects[${subjectIdx}][name]" class="form-control" placeholder="e.g. Social Studies" required></td>
            <td><input type="number" name="subjects[${subjectIdx}][max_marks]" class="form-control" value="100" min="1" step="0.5" required></td>
            <td><input type="number" name="subjects[${subjectIdx}][pass_marks]" class="form-control" value="40" min="0" step="0.5" required></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm remove-row-btn"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td>
        `;
        tableBody.appendChild(tr);
        subjectIdx++;
        if (window.lucide) { lucide.createIcons(); }
    });

    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row-btn')) {
            const tr = e.target.closest('tr');
            if (tableBody.querySelectorAll('tr').length > 1) {
                tr.remove();
            } else {
                alert('At least one subject is required.');
            }
        }
    });

    // Target Classes Tag System Logic
    const classSelectDropdown = document.getElementById('class_select_dropdown');
    const tagBox = document.getElementById('target_classes_tag_box');
    const hiddenInput = document.getElementById('class_name_hidden');
    const selectAllBtn = document.getElementById('selectAllClassesBtn');
    const clearBtn = document.getElementById('clearClassesBtn');

    let selectedClasses = [];

    const initialVal = hiddenInput.value ? hiddenInput.value.trim() : '';
    if (initialVal) {
        if (initialVal === 'All Classes' || initialVal === 'All') {
            selectedClasses = ['All Classes'];
        } else {
            selectedClasses = initialVal.split(',').map(s => s.trim()).filter(Boolean);
        }
    }

    function renderTags() {
        tagBox.innerHTML = '';
        if (selectedClasses.length === 0) {
            tagBox.innerHTML = '<span class="text-muted small fst-italic py-1 px-2"><i data-lucide="info" style="width:0.875rem;height:0.875rem;" class="me-1"></i>No class selected yet. Choose from dropdown above.</span>';
            hiddenInput.value = '';
        } else {
            selectedClasses.forEach(cls => {
                const badge = document.createElement('span');
                if (cls === 'All Classes' || cls === 'All') {
                    badge.className = 'badge bg-primary text-white d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm fs-6 me-1 mb-1';
                    badge.innerHTML = `<i data-lucide="layers" style="width:0.875rem;height:0.875rem;"></i> <span>All Classes (Whole School)</span> <button type="button" class="btn-close btn-close-white ms-1" style="width:0.4em;height:0.4em;" aria-label="Remove"></button>`;
                } else {
                    badge.className = 'badge bg-dark text-white d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm fs-6 me-1 mb-1';
                    badge.innerHTML = `<span>${cls}</span> <button type="button" class="btn-close btn-close-white ms-1" style="width:0.4em;height:0.4em;" aria-label="Remove"></button>`;
                }
                
                badge.querySelector('.btn-close').addEventListener('click', function(e) {
                    e.stopPropagation();
                    removeTag(cls);
                });
                tagBox.appendChild(badge);
            });
            hiddenInput.value = selectedClasses.join(', ');
        }
        if (window.lucide) { lucide.createIcons(); }
    }

    function addTag(className) {
        if (!className) return;
        if (className === 'All Classes' || className === 'All') {
            selectedClasses = ['All Classes'];
        } else {
            selectedClasses = selectedClasses.filter(c => c !== 'All Classes' && c !== 'All');
            if (!selectedClasses.includes(className)) {
                selectedClasses.push(className);
            }
        }
        renderTags();
    }

    function removeTag(className) {
        selectedClasses = selectedClasses.filter(c => c !== className);
        renderTags();
    }

    classSelectDropdown.addEventListener('change', function() {
        const val = this.value;
        if (val) {
            addTag(val);
            this.value = '';
        }
    });

    selectAllBtn.addEventListener('click', function() {
        selectedClasses = ['All Classes'];
        renderTags();
    });

    clearBtn.addEventListener('click', function() {
        selectedClasses = [];
        renderTags();
    });

    renderTags();
});
</script>
@endpush
@endsection
