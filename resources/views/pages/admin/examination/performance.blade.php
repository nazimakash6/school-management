@extends('layouts.app')

@section('title', 'Student Result Performance')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
        min-height: 31px;
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
                    <li class="breadcrumb-item"><a href="{{ route('examination.index') }}">Examination</a></li>
                    <li class="breadcrumb-item active">Student Performance</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Student Result Performance Analysis</h1>
            <p class="text-muted small mb-0">Track and compare student performance across different exam types (Daily Test, 3rd Day, Weekly, Monthly, Mid, Term, Annual, etc.)</p>
        </div>
        <a href="{{ route('examination.index') }}" class="btn btn-outline-secondary btn-sm">
            <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Examinations
        </a>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.index') }}"><i data-lucide="list" style="width:1rem;height:1rem;" class="me-1"></i> Examinations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('exam-types.index') }}"><i data-lucide="tag" style="width:1rem;height:1rem;" class="me-1"></i> Exam Types</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.results') }}"><i data-lucide="bar-chart-2" style="width:1rem;height:1rem;" class="me-1"></i> Class Results</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="{{ route('examination.performance') }}"><i data-lucide="trending-up" style="width:1rem;height:1rem;" class="me-1"></i> Student Performance</a>
        </li>
    </ul>

    <!-- Student Selection Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="user-check" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                Select Student for Performance Evaluation
            </h6>
        </div>
        <div class="card-body p-3">
            <form action="{{ route('examination.performance') }}" method="GET" class="row g-3 align-items-center" id="performanceFilterForm">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Academic Session</label>
                    <select name="academic_session_id" id="academic_session_id" class="form-select form-select-sm">
                        <option value="all">— All Sessions —</option>
                        @foreach($academicSessions as $sess)
                            <option value="{{ $sess->id }}" @selected($sessionId == $sess->id)>{{ $sess->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Filter Class</label>
                    <select name="class_name" id="class_name" class="form-select form-select-sm">
                        <option value="all">— All Classes —</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->name }}" @selected($className == $cls->name)>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Select Student <span class="text-danger">*</span></label>
                    <select name="admission_id" id="admission_id" class="form-select form-select-sm select2-student">
                        <option value="">— Choose Student —</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" @selected($admissionId == $st->id)>
                                {{ $st->student_name }} (Adm: {{ $st->admission_no }} | Class: {{ $st->class_name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-grid align-self-end">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">
                        <i data-lucide="search" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Analyze
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedStudent)
        <!-- Selected Student Profile & Overall Standing -->
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-primary-subtle border-primary-subtle">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-box bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 55px; height: 55px;">
                            {{ strtoupper(substr($selectedStudent->student_name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-1">{{ $selectedStudent->student_name }}</h4>
                            <div class="text-muted small">
                                Father Name: <span class="fw-semibold text-dark">{{ $selectedStudent->father_name }}</span> | 
                                Class: <span class="fw-bold text-dark">{{ $selectedStudent->class_name }}</span> | 
                                Admission No: <span class="font-monospace fw-bold text-dark">{{ $selectedStudent->admission_no }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('examination.complete-marksheet', ['admission' => $selectedStudent->id, 'academic_session_id' => $sessionId, 'class_name' => $className]) }}" target="_blank" class="btn btn-primary btn-sm fw-bold">
                            <i data-lucide="printer" style="width:1rem;height:1rem;" class="me-1"></i> Print Complete Marksheet
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Across Exam Types Grid -->
        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i data-lucide="grid" style="width:1.2rem;height:1.2rem;" class="text-primary"></i>
            Performance Breakdown by Exam Type
        </h5>

        <div class="row g-4 mb-4">
            @foreach($examTypes as $type)
                @php
                    $stats = $examTypeStats[$type->name] ?? null;
                    $pct = $stats ? $stats['percentage'] : 0;
                    $count = $stats ? $stats['count'] : 0;
                    
                    $badgeClass = 'bg-secondary';
                    if ($pct >= 80) $badgeClass = 'bg-success';
                    elseif ($pct >= 60) $badgeClass = 'bg-info';
                    elseif ($pct >= 40) $badgeClass = 'bg-warning';
                    elseif ($count > 0) $badgeClass = 'bg-danger';
                @endphp

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-light text-dark border font-monospace">{{ $type->code ?: 'TYPE' }}</span>
                                <span class="badge {{ $badgeClass }} text-white rounded-pill">{{ $count }} Exam(s)</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $type->name }}</h6>
                            <div class="h3 fw-bold text-dark mb-2">
                                {{ $count > 0 ? $pct . '%' : 'N/A' }}
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $badgeClass }}" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 small text-muted">
                                <span>Obtained: {{ $stats['obtained'] ?? 0 }}</span>
                                <span>Total: {{ $stats['total'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Detailed Exam Marks Log -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="list-checks" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                    All Recorded Marks for {{ $selectedStudent->student_name }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Exam Title</th>
                                <th>Exam Type</th>
                                <th>Subject</th>
                                <th>Marks Obtained</th>
                                <th>Total Marks</th>
                                <th>Percentage</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $hasAny = false; @endphp
                            @foreach($examTypeStats as $typeName => $stat)
                                @foreach($stat['marks'] as $m)
                                    @php
                                        $hasAny = true;
                                        $pct = $m->total_marks > 0 ? round(($m->marks_obtained / $m->total_marks) * 100, 1) : 0;
                                        $isPass = !$m->is_absent && $pct >= 40;
                                    @endphp
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">{{ optional($m->examination)->title }}</td>
                                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $typeName }}</span></td>
                                        <td><span class="fw-semibold text-dark">{{ $m->subject_name }}</span></td>
                                        <td>
                                            @if($m->is_absent)
                                                <span class="badge bg-danger text-white">ABSENT</span>
                                            @else
                                                <span class="fw-bold text-dark">{{ $m->marks_obtained }}</span>
                                            @endif
                                        </td>
                                        <td><span class="text-muted">{{ number_format($m->total_marks) }}</span></td>
                                        <td><span class="fw-bold {{ $isPass ? 'text-success' : 'text-danger' }}">{{ $pct }}%</span></td>
                                        <td>
                                            <span class="badge {{ $isPass ? 'bg-success' : 'bg-danger' }} text-white rounded-pill">
                                                {{ $m->is_absent ? 'ABSENT' : ($isPass ? 'PASS' : 'FAIL') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            @if(!$hasAny)
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No marks recorded yet for this student across any exam types.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-3 p-5 text-center text-muted">
            <i data-lucide="user-search" style="width:3rem;height:3rem;" class="mx-auto text-secondary mb-2"></i>
            <h5>Select a Student to View Performance</h5>
            <p class="small mb-0">Use the dropdown above to choose a student and view their performance analysis across Daily, Weekly, Monthly, Term, and Annual exams.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initSelect2() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $('#admission_id').select2({
                theme: 'bootstrap-5',
                placeholder: '— Choose or Search Student —',
                allowClear: true,
                width: '100%'
            });

            $('#academic_session_id').off('change').on('change', function() {
                var sessionId = $(this).val();
                var className = $('#class_name').val();
                fetchFilterOptions(sessionId, className, true);
            });

            $('#class_name').off('change').on('change', function() {
                var sessionId = $('#academic_session_id').val();
                var className = $(this).val();
                fetchFilterOptions(sessionId, className, false);
            });
        }
    }

    function fetchFilterOptions(sessionId, className, updateClasses) {
        $.ajax({
            url: "{{ route('examination.filter-options') }}",
            type: 'GET',
            data: {
                academic_session_id: sessionId,
                class_name: className
            },
            success: function(response) {
                if (updateClasses && response.classes) {
                    var currentClass = $('#class_name').val();
                    var $classSelect = $('#class_name');
                    $classSelect.empty();
                    $classSelect.append('<option value="all">— All Classes —</option>');
                    $.each(response.classes, function(index, c) {
                        var selected = (c === currentClass) ? 'selected' : '';
                        $classSelect.append('<option value="' + c + '" ' + selected + '>' + c + '</option>');
                    });
                }

                if (response.students) {
                    var currentStudent = $('#admission_id').val();
                    var $studentSelect = $('#admission_id');
                    $studentSelect.empty();
                    $studentSelect.append('<option value="">— Choose Student —</option>');
                    $.each(response.students, function(index, st) {
                        var selected = (st.id == currentStudent) ? 'selected' : '';
                        $studentSelect.append('<option value="' + st.id + '" ' + selected + '>' + st.text + '</option>');
                    });
                    $studentSelect.trigger('change.select2');
                }
            }
        });
    }

    initSelect2();
});
</script>
@endpush
