@extends('layouts.app')

@section('title', 'Examination Details — ' . $examination->title)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('examination.index') }}">Examination</a></li>
                    <li class="breadcrumb-item active">{{ $examination->title }}</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">{{ $examination->title }}</h1>
            <p class="text-muted small mb-0">Exam Type: <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ optional($examination->examType)->name }}</span> | Class: {{ $examination->class_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('examination.marks', $examination) }}" class="btn btn-success btn-sm fw-bold">
                <i data-lucide="edit-3" style="width:1rem;height:1rem;" class="me-1"></i> Enter / Edit Student Marks
            </a>
            <a href="{{ route('examination.edit', $examination) }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="pencil" style="width:1rem;height:1rem;" class="me-1"></i> Edit Exam
            </a>
            <a href="{{ route('examination.index') }}" class="btn btn-outline-dark btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Overview Cards -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 border-start border-4 border-primary">
                <div class="text-muted small fw-semibold">EXAM TYPE</div>
                <div class="h4 fw-bold text-dark mb-0 mt-1">{{ optional($examination->examType)->name ?: 'Standard Test' }}</div>
                <div class="small text-primary mt-1">{{ optional($examination->examType)->code ?: 'N/A' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 border-start border-4 border-success">
                <div class="text-muted small fw-semibold">TARGET CLASS</div>
                <div class="fw-bold text-dark mb-0 mt-1 d-flex flex-wrap gap-1">
                    @if($examination->isAllClasses())
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fs-6">All Classes</span>
                    @else
                        @foreach($examination->target_classes_array as $tCls)
                            <span class="badge bg-secondary-subtle text-dark border fs-6">{{ $tCls }}</span>
                        @endforeach
                    @endif
                </div>
                <div class="small text-muted mt-1">Section: {{ $examination->section_name ?: 'All' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 border-start border-4 border-info">
                <div class="text-muted small fw-semibold">TOTAL MARKS</div>
                <div class="h4 fw-bold text-dark mb-0 mt-1">{{ number_format($examination->total_marks) }}</div>
                <div class="small text-muted mt-1">Pass Marks: {{ number_format($examination->pass_marks) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 border-start border-4 border-warning">
                <div class="text-muted small fw-semibold">STATUS</div>
                <div class="h4 fw-bold text-capitalize text-dark mb-0 mt-1">{{ $examination->status }}</div>
                <div class="small text-muted mt-1">
                    {{ $examination->start_date ? $examination->start_date->format('M d, Y') : 'Date N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects Configured & Student Marks Summary -->
    <div class="row g-4">
        <!-- Subjects Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="book-open" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                        Configured Subjects
                    </h6>
                    <span class="badge bg-secondary rounded-pill">{{ $examination->schedules->count() }} Subject(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Subject Name</th>
                                    <th>Max Marks</th>
                                    <th>Pass Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examination->schedules as $sch)
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">{{ $sch->subject_name }}</td>
                                    <td><span class="badge bg-primary-subtle text-primary font-monospace">{{ number_format($sch->max_marks) }}</span></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace">{{ number_format($sch->pass_marks) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No subjects explicitly set. Default subjects will be used.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Students Column (Class-wise Accordion) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="users" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                        Enrolled Students & Marks Status By Class
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill">{{ $students->count() }} Total Student(s)</span>
                        <a href="{{ route('examination.marks', $examination) }}" class="btn btn-sm btn-outline-success">
                            <i data-lucide="edit-3" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Marks Entry Matrix
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    @php
                        $studentsByClass = $students->groupBy('class_name');
                        if ($examination->isAllClasses()) {
                            $targetClassesList = $studentsByClass->keys()->toArray();
                        } else {
                            $targetClassesList = $examination->target_classes_array;
                        }
                        if (empty($targetClassesList)) {
                            $targetClassesList = [$examination->class_name];
                        }
                    @endphp

                    <div class="accordion d-flex flex-column gap-3" id="examinationStudentsAccordion">
                        @forelse($targetClassesList as $clsIdx => $clsName)
                            @php
                                $classStudents = $studentsByClass->get($clsName, collect());
                                $accordionId = 'class_acc_' . Str::slug($clsName ?: 'class_' . $clsIdx);
                            @endphp
                            <div class="accordion-item border rounded-3 overflow-hidden shadow-sm">
                                <h2 class="accordion-header" id="heading_{{ $accordionId }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} bg-light fw-bold text-dark py-3" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse_{{ $accordionId }}" 
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                            aria-controls="collapse_{{ $accordionId }}">
                                        <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <i data-lucide="graduation-cap" style="width:1.25rem;height:1.25rem;" class="text-primary"></i>
                                                <span class="fs-6 fw-bold text-dark">{{ $clsName }}</span>
                                            </div>
                                            <div>
                                                <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 fs-7">
                                                    {{ $classStudents->count() }} Student(s)
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse_{{ $accordionId }}" 
                                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                     aria-labelledby="heading_{{ $accordionId }}" 
                                     data-bs-parent="#examinationStudentsAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3">Student Name</th>
                                                        <th>Adm No / Roll</th>
                                                        <th>Obtained / Total</th>
                                                        <th>Percentage</th>
                                                        <th>Status</th>
                                                        <th class="pe-3 text-end">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($classStudents as $st)
                                                        @php
                                                            $stMarks = $marks->get($st->id, collect());
                                                            $obtainedSum = $stMarks->where('is_absent', false)->sum('marks_obtained');
                                                            $totalSum = $stMarks->sum('total_marks');
                                                            $hasMarks = $stMarks->isNotEmpty();
                                                            $percentage = $totalSum > 0 ? round(($obtainedSum / $totalSum) * 100, 1) : 0;
                                                            $isPass = $hasMarks && $percentage >= 40;
                                                        @endphp
                                                        <tr>
                                                            <td class="ps-3">
                                                                <div class="fw-bold text-dark">{{ $st->student_name }}</div>
                                                                <div class="text-muted small">Father: {{ $st->father_name }}</div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-dark font-monospace border">{{ $st->admission_no }}</span>
                                                                <div class="small text-muted">Roll: {{ $st->roll_no ?: 'N/A' }}</div>
                                                            </td>
                                                            <td>
                                                                @if($hasMarks)
                                                                    <span class="fw-bold text-dark">{{ $obtainedSum }}</span> / <span class="text-muted">{{ $totalSum }}</span>
                                                                @else
                                                                    <span class="text-muted small">No marks</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($hasMarks)
                                                                    <span class="fw-bold fs-6 {{ $isPass ? 'text-success' : 'text-danger' }}">{{ $percentage }}%</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($hasMarks)
                                                                    <span class="badge {{ $isPass ? 'bg-success' : 'bg-danger' }} text-white rounded-pill">
                                                                        {{ $isPass ? 'PASS' : 'FAIL' }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                                                                @endif
                                                            </td>
                                                            <td class="pe-3 text-end">
                                                                <a href="{{ route('examination.result-card', [$st->id, 'examination_id' => $examination->id]) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Print Marksheet Report Card">
                                                                    <i data-lucide="printer" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Result Card
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center py-4 text-muted">
                                                                No enrolled students found for {{ $clsName }}.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                No classes or enrolled students found for this examination.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
