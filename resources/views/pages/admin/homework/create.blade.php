@extends('layouts.app')

@section('title', 'Assign Homework')

@push('styles')
<style>
.homework-card {
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}
.homework-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
}
.homework-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 0.85rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.subject-badge {
    background: #4f46e5;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.empty-state-box {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
    padding: 3rem 1.5rem;
    text-align: center;
    color: #64748b;
}
.animate-fade-in {
    animation: fadeIn 0.35s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('homework.index') }}">Homework</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Homework</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Assign Homework</h1>
            <p class="text-muted small mb-0">Assign subject-wise homework for a class in one single form</p>
        </div>
        <div>
            <a href="{{ route('homework.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i data-lucide="arrow-left" class="me-1" style="width:1rem;height:1rem;"></i> Back to List
            </a>
            <button type="button" form="homeworkForm" onclick="submitForm()" class="btn btn-primary btn-sm">
                <i data-lucide="check-circle" class="me-1" style="width:1rem;height:1rem;"></i> Save All Homework
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center mb-1">
                <i data-lucide="alert-triangle" class="me-2" style="width:1.25rem;height:1.25rem;"></i>
                <strong>Please correct the errors below:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="homeworkForm" action="{{ route('homework.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Step 1: Class & General Date Selection -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                    <i data-lucide="book-open" style="width:1.25rem;height:1.25rem;"></i>
                    1. Select Class & Assigning Date
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Select Class <span class="text-danger">*</span></label>
                        <select name="student_class_id" id="classSelect" class="form-select @error('student_class_id') is-invalid @enderror" required>
                            <option value="">-- Choose Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (old('student_class_id', $selectedClassId) == $class->id) ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Assigned Date <span class="text-danger">*</span></label>
                        <input type="date" name="assigned_date" id="assignedDate" class="form-control @error('assigned_date') is-invalid @enderror" value="{{ old('assigned_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Default Due Date</label>
                        <input type="date" id="defaultDueDate" class="form-control" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        <div class="form-text">Used automatically for newly added subject tasks</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Subject Dropdown & Dynamic Work Areas -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                        <i data-lucide="layers" style="width:1.25rem;height:1.25rem;"></i>
                        2. Subject Homework Assignment Work Area
                    </h5>
                    <p class="text-muted small mb-0">Select a subject to add its homework task area</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select id="subjectSelect" class="form-select form-select-sm" style="min-width: 220px;" disabled>
                        <option value="">-- Select Class First --</option>
                    </select>
                    <button type="button" id="addSubjectBtn" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" disabled>
                        <i data-lucide="plus" style="width:1rem;height:1rem;"></i> Add Subject Task
                    </button>
                    <button type="button" id="addAllSubjectsBtn" class="btn btn-soft-info btn-sm text-info bg-info bg-opacity-10 border-info border-opacity-25 d-flex align-items-center gap-1" disabled>
                        <i data-lucide="zap" style="width:1rem;height:1rem;"></i> Add All Class Subjects
                    </button>
                </div>
            </div>

            <div class="card-body bg-light bg-opacity-50 min-vh-25">
                <!-- Subject Tasks Container -->
                <div id="subjectTasksContainer" class="d-flex flex-column gap-3">
                    <!-- Default Empty State -->
                    <div id="emptyState" class="empty-state-box">
                        <i data-lucide="book-marked" class="text-tertiary mb-2" style="width:3rem;height:3rem;"></i>
                        <h6 class="fw-semibold text-secondary">No Subject Selected Yet</h6>
                        <p class="small text-muted mb-0">Select a Class above, then choose a Subject from the dropdown to assign homework tasks.</p>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
                <span id="taskCounterText" class="text-muted small fw-medium">0 Subject Tasks Added</span>
                <div class="d-flex gap-2">
                    <a href="{{ route('homework.index') }}" class="btn btn-light btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i data-lucide="save" class="me-1" style="width:1rem;height:1rem;"></i> Save All Homework
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let classSubjectsMap = [];
let addedSubjectIds = new Set();
let taskIndexCounter = 0;

document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('classSelect');
    const subjectSelect = document.getElementById('subjectSelect');
    const addSubjectBtn = document.getElementById('addSubjectBtn');
    const addAllSubjectsBtn = document.getElementById('addAllSubjectsBtn');

    if (classSelect.value) {
        fetchClassSubjects(classSelect.value);
    }

    classSelect.addEventListener('change', function () {
        const classId = this.value;
        // Clear existing tasks on class change
        document.getElementById('subjectTasksContainer').innerHTML = `
            <div id="emptyState" class="empty-state-box">
                <i data-lucide="book-marked" class="text-tertiary mb-2" style="width:3rem;height:3rem;"></i>
                <h6 class="fw-semibold text-secondary">No Subject Selected Yet</h6>
                <p class="small text-muted mb-0">Select a Subject from the dropdown to assign homework tasks.</p>
            </div>
        `;
        addedSubjectIds.clear();
        updateTaskCounter();

        if (classId) {
            fetchClassSubjects(classId);
        } else {
            subjectSelect.innerHTML = '<option value="">-- Select Class First --</option>';
            subjectSelect.disabled = true;
            addSubjectBtn.disabled = true;
            addAllSubjectsBtn.disabled = true;
        }
    });

    subjectSelect.addEventListener('change', function () {
        if (this.value) {
            addSubjectTaskCard(this.value);
            this.value = '';
        }
    });

    addSubjectBtn.addEventListener('click', function () {
        if (subjectSelect.value) {
            addSubjectTaskCard(subjectSelect.value);
            subjectSelect.value = '';
        }
    });

    addAllSubjectsBtn.addEventListener('click', function () {
        if (classSubjectsMap.length === 0) return;
        classSubjectsMap.forEach(subj => {
            if (!addedSubjectIds.has(subj.id)) {
                addSubjectTaskCard(subj.id);
            }
        });
    });
});

function fetchClassSubjects(classId) {
    const subjectSelect = document.getElementById('subjectSelect');
    const addSubjectBtn = document.getElementById('addSubjectBtn');
    const addAllSubjectsBtn = document.getElementById('addAllSubjectsBtn');

    subjectSelect.disabled = true;
    subjectSelect.innerHTML = '<option value="">Loading subjects...</option>';

    fetch(`/homework/get-subjects/${classId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.subjects.length > 0) {
                classSubjectsMap = data.subjects;
                renderSubjectDropdown();
                subjectSelect.disabled = false;
                addSubjectBtn.disabled = false;
                addAllSubjectsBtn.disabled = false;
            } else {
                classSubjectsMap = [];
                subjectSelect.innerHTML = '<option value="">No subjects found for this class</option>';
                subjectSelect.disabled = true;
                addSubjectBtn.disabled = true;
                addAllSubjectsBtn.disabled = true;
            }
        })
        .catch(err => {
            console.error(err);
            subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
        });
}

function renderSubjectDropdown() {
    const subjectSelect = document.getElementById('subjectSelect');
    let html = '<option value="">-- Select Subject --</option>';
    classSubjectsMap.forEach(subj => {
        const isAdded = addedSubjectIds.has(subj.id);
        html += `<option value="${subj.id}" ${isAdded ? 'disabled' : ''}>
            ${subj.subject_name} (${subj.subject_code}) ${isAdded ? '[Added]' : ''}
        </option>`;
    });
    subjectSelect.innerHTML = html;
}

function addSubjectTaskCard(subjectId) {
    const subj = classSubjectsMap.find(s => s.id == subjectId);
    if (!subj) return;

    if (addedSubjectIds.has(subj.id)) {
        alert(`${subj.subject_name} task area is already added!`);
        return;
    }

    const emptyState = document.getElementById('emptyState');
    if (emptyState) emptyState.remove();

    addedSubjectIds.add(subj.id);
    renderSubjectDropdown();

    const idx = taskIndexCounter++;
    const defaultDueDate = document.getElementById('defaultDueDate').value;

    const card = document.createElement('div');
    card.className = 'homework-card animate-fade-in mb-3';
    card.id = `subject-card-${subj.id}`;

    card.innerHTML = `
        <div class="homework-card-header">
            <div class="d-flex align-items-center gap-2">
                <span class="subject-badge">
                    <i data-lucide="book-open" style="width:0.875rem;height:0.875rem;"></i>
                    ${subj.subject_name}
                </span>
                <span class="badge bg-light text-dark border ms-1">${subj.subject_code}</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase" style="font-size: 0.75rem;">${subj.subject_type || 'core'}</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-icon" title="Remove Subject" onclick="removeSubjectTaskCard(${subj.id})">
                <i data-lucide="x" style="width:1.25rem;height:1.25rem;"></i>
            </button>
        </div>
        <div class="card-body p-3">
            <input type="hidden" name="homeworks[${idx}][subject_id]" value="${subj.id}">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-secondary fw-semibold small">Homework Title / Chapter Topic</label>
                    <input type="text" name="homeworks[${idx}][title]" class="form-control form-control-sm" placeholder="e.g. Chapter 3 - Exercises & Vocabulary">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary fw-semibold small">Due Date</label>
                    <input type="date" name="homeworks[${idx}][due_date]" class="form-control form-control-sm" value="${defaultDueDate}">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary fw-semibold small">Status</label>
                    <select name="homeworks[${idx}][status]" class="form-select form-select-sm">
                        <option value="active" selected>Active</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label text-secondary fw-semibold small">Homework Description / Task Details <span class="text-danger">*</span></label>
                    <textarea name="homeworks[${idx}][description]" class="form-control form-control-sm" rows="3" placeholder="Write down the detailed homework instructions assigned for ${subj.subject_name}..." required></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label text-secondary fw-semibold small">Optional Worksheet / Attachment</label>
                    <input type="file" name="homeworks[${idx}][attachment]" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <div class="form-text small">Accepted formats: PDF, DOC, DOCX, Images (Max: 5MB)</div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('subjectTasksContainer').appendChild(card);
    if (window.lucide) {
        lucide.createIcons();
    }
    updateTaskCounter();
}

function removeSubjectTaskCard(subjectId) {
    const card = document.getElementById(`subject-card-${subjectId}`);
    if (card) {
        card.remove();
        addedSubjectIds.delete(subjectId);
        renderSubjectDropdown();
        updateTaskCounter();

        const container = document.getElementById('subjectTasksContainer');
        if (container.children.length === 0) {
            container.innerHTML = `
                <div id="emptyState" class="empty-state-box">
                    <i data-lucide="book-marked" class="text-tertiary mb-2" style="width:3rem;height:3rem;"></i>
                    <h6 class="fw-semibold text-secondary">No Subject Selected Yet</h6>
                    <p class="small text-muted mb-0">Select a Subject from the dropdown to assign homework tasks.</p>
                </div>
            `;
            if (window.lucide) lucide.createIcons();
        }
    }
}

function updateTaskCounter() {
    const text = document.getElementById('taskCounterText');
    const count = addedSubjectIds.size;
    text.textContent = `${count} Subject Task(s) Added`;
}

function submitForm() {
    if (addedSubjectIds.size === 0) {
        alert('Please select a class and add at least one subject homework task before saving.');
        return;
    }
    document.getElementById('homeworkForm').submit();
}
</script>
@endpush
