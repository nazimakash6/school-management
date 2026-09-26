@extends('layouts.app')

@section('title', 'Student Promotion')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student-promotion.css') }}">
<style>
    .student-row {
        cursor: pointer;
    }
    .student-row:hover {
        background-color: rgba(13, 110, 253, 0.04) !important;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Student Promotion</h1>
        <p class="page-subtitle">Promote students to the next class based on exams or teacher recommendation.</p>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (session('warning'))
<div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Filter Bar -->
<div class="filter-bar mb-3 p-3 bg-white border rounded shadow-sm">
    <form method="GET" action="{{ route('student-promotion.index') }}" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label text-sm text-tertiary fw-bold">Academic Session</label>
            <select name="academic_session_id" class="form-select">
                <option value="">All Academic Sessions</option>
                @foreach ($academicSessions as $sessionOption)
                    <option value="{{ $sessionOption->id }}" @selected($academicSessionFilter == $sessionOption->id)>
                        {{ $sessionOption->session_name }} {{ $sessionOption->is_current ? '(Current)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label text-sm text-tertiary fw-bold">Current Class</label>
            <select name="class_filter" class="form-select">
                <option value="">All Classes</option>
                @foreach ($dbClasses as $classOption)
                    <option value="{{ $classOption }}" @selected($currentClassFilter == $classOption)>{{ $classOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label text-sm text-tertiary fw-bold">Search Student</label>
            <input type="text" name="search" class="form-control" placeholder="Search by name or admission no..." value="{{ $search }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('student-promotion.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Promotion Form & Table -->
<form id="promotionForm" method="POST" action="{{ route('student-promotion.store') }}">
    @csrf

    <div class="card mb-4 border shadow-sm">
        <div class="card-body bg-light">
            <h5 class="card-title text-sm fw-bold mb-3"><i class="bi bi-mortarboard me-1"></i> Promotion Settings</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-sm fw-bold">Target Academic Session</label>
                    <select name="target_academic_session_id" class="form-select">
                        <option value="">Keep Current Session</option>
                        @foreach ($academicSessions as $sessionOption)
                            <option value="{{ $sessionOption->id }}">{{ $sessionOption->session_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-bold">Promote To Class <span class="text-danger">*</span></label>
                    <select name="promote_to_class" id="promoteToClassSelect" class="form-select" required>
                        <option value="">Select Target Class</option>
                        @foreach ($dbClasses as $classOption)
                            <option value="{{ $classOption }}">{{ $classOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-bold">Promotion Type <span class="text-danger">*</span></label>
                    <select name="promotion_type" class="form-select" required>
                        <option value="exam">Annual Exam Pass</option>
                        <option value="teacher">Teacher Promotion (Intelligent)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm fw-bold">Promotion Date <span class="text-danger">*</span></label>
                    <input type="date" name="promotion_date" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-12">
                    <label class="form-label text-sm fw-bold">Remarks / Notes</label>
                    <input type="text" name="notes" class="form-control" placeholder="Optional notes for this promotion">
                </div>
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <span class="text-muted text-sm fw-bold" id="selectedCountText">0 student(s) selected</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleSelectAllBtn">Select All</button>
                        <button type="submit" class="btn btn-primary px-4" id="promoteBtn">
                            <i class="bi bi-arrow-up-circle me-1"></i> Promote Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container card border shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                    <th>Admission No</th>
                    <th>Student Name</th>
                    <th>Academic Session</th>
                    <th>Current Class</th>
                    <th>Promotion History</th>
                    <th class="text-end">Quick Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                <tr class="student-row">
                    <td onclick="event.stopPropagation();">
                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-checkbox">
                    </td>
                    <td class="fw-bold">{{ $student->admission_no }}</td>
                    <td>{{ $student->student_name }}</td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ optional($student->academicSession)->session_name ?: ($student->academic_session ?: 'N/A') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-primary fs-6">{{ $student->current_class }}</span>
                    </td>
                    <td>
                        @if($student->promotions->count() > 0)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $student->promotions->count() }} time(s) promoted</span>
                            <div class="text-xs text-muted mt-1">Latest: to <strong>{{ $student->promotions->first()->promoted_to_class }}</strong> on {{ optional($student->promotions->first()->promotion_date)->format('M d, Y') }}</div>
                        @else
                            <span class="text-sm text-tertiary">No prior promotions</span>
                        @endif
                    </td>
                    <td class="text-end" onclick="event.stopPropagation();">
                        <button type="button" class="btn btn-sm btn-outline-primary single-promote-btn" data-student-id="{{ $student->id }}" data-student-name="{{ $student->student_name }}" data-student-class="{{ $student->current_class }}">
                            <i class="bi bi-arrow-up-circle"></i> Promote
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-tertiary">No students found for the selected session and class.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const toggleSelectAllBtn = document.getElementById('toggleSelectAllBtn');
        const checkboxes = document.querySelectorAll('.student-checkbox');
        const form = document.getElementById('promotionForm');
        const selectedCountText = document.getElementById('selectedCountText');
        const promoteToClassSelect = document.getElementById('promoteToClassSelect');

        function getNextClassName(currentClassName) {
            if (!currentClassName) return '';
            const match = currentClassName.match(/\d+/);
            if (match) {
                const nextNum = parseInt(match[0], 10) + 1;
                return currentClassName.replace(/\d+/, nextNum);
            }
            return '';
        }

        // Auto-select target class if current class filter is active and target class is empty
        const currentFilterClass = "{{ $currentClassFilter ?? '' }}";
        if (currentFilterClass && promoteToClassSelect && !promoteToClassSelect.value) {
            const suggestedClass = getNextClassName(currentFilterClass);
            if (suggestedClass) {
                for (let option of promoteToClassSelect.options) {
                    if (option.value === suggestedClass) {
                        promoteToClassSelect.value = suggestedClass;
                        break;
                    }
                }
            }
        }

        function updateSelectionState() {
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            if (selectedCountText) {
                selectedCountText.textContent = `${checkedCount} student(s) selected`;
            }
            if (selectAll) {
                selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
            }
            if (toggleSelectAllBtn) {
                toggleSelectAllBtn.textContent = (checkboxes.length > 0 && checkedCount === checkboxes.length) ? 'Deselect All' : 'Select All';
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateSelectionState();
            });
        }

        if (toggleSelectAllBtn) {
            toggleSelectAllBtn.addEventListener('click', function () {
                const targetState = !(checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked));
                checkboxes.forEach(cb => cb.checked = targetState);
                updateSelectionState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectionState);
        });

        // Row clicking toggles checkbox
        document.querySelectorAll('.student-row').forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
                    const cb = row.querySelector('.student-checkbox');
                    if (cb) {
                        cb.checked = !cb.checked;
                        updateSelectionState();
                    }
                }
            });
        });

        let isSinglePromoteTrigger = false;

        // Single Promote Button Action
        document.querySelectorAll('.single-promote-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const studentId = btn.getAttribute('data-student-id');
                const studentName = btn.getAttribute('data-student-name');
                const studentClass = btn.getAttribute('data-student-class');

                let targetClass = promoteToClassSelect ? promoteToClassSelect.value : '';

                // If target class is not selected, auto-suggest next class
                if (!targetClass && studentClass) {
                    const suggested = getNextClassName(studentClass);
                    if (suggested && promoteToClassSelect) {
                        for (let option of promoteToClassSelect.options) {
                            if (option.value === suggested) {
                                promoteToClassSelect.value = suggested;
                                targetClass = suggested;
                                break;
                            }
                        }
                    }
                }

                if (!targetClass) {
                    alert('Please select a target class in Promotion Settings first.');
                    if (promoteToClassSelect) promoteToClassSelect.focus();
                    return;
                }

                if (confirm(`Are you sure you want to promote "${studentName}" to class "${targetClass}"?`)) {
                    isSinglePromoteTrigger = true;
                    // Check ONLY this student
                    checkboxes.forEach(cb => cb.checked = (String(cb.value) === String(studentId)));
                    updateSelectionState();

                    // Submit form safely
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }
            });
        });

        // Form Submit Handler
        if (form) {
            form.addEventListener('submit', function (event) {
                const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);

                if (checkedBoxes.length === 0) {
                    event.preventDefault();
                    alert('Please select at least one student to promote.');
                    return;
                }

                let targetClass = promoteToClassSelect ? promoteToClassSelect.value : '';

                // Auto-fill target class if empty based on first checked student's row
                if (!targetClass && checkedBoxes.length > 0) {
                    const firstCheckedRow = checkedBoxes[0].closest('tr');
                    const singleBtn = firstCheckedRow ? firstCheckedRow.querySelector('.single-promote-btn') : null;
                    const studentClass = singleBtn ? singleBtn.getAttribute('data-student-class') : null;
                    if (studentClass) {
                        const suggested = getNextClassName(studentClass);
                        if (suggested && promoteToClassSelect) {
                            for (let option of promoteToClassSelect.options) {
                                if (option.value === suggested) {
                                    promoteToClassSelect.value = suggested;
                                    targetClass = suggested;
                                    break;
                                }
                            }
                        }
                    }
                }

                if (!targetClass) {
                    event.preventDefault();
                    alert('Please select a target class to promote the students to.');
                    if (promoteToClassSelect) promoteToClassSelect.focus();
                    return;
                }

                if (!isSinglePromoteTrigger) {
                    const confirmPromotion = confirm(`Are you sure you want to promote ${checkedBoxes.length} student(s) to class "${targetClass}"?`);
                    if (!confirmPromotion) {
                        event.preventDefault();
                    }
                }
                isSinglePromoteTrigger = false;
            });
        }

        updateSelectionState();
    });
</script>
@endpush

@endsection
