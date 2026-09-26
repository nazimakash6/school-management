@extends('layouts.app')

@section('title', 'Bulk Student Marks Entry — ' . $examination->title)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('examination.index') }}">Examination</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('examination.show', $examination) }}">{{ $examination->title }}</a></li>
                    <li class="breadcrumb-item active">Marks Entry</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Student Marks Entry Matrix</h1>
            <p class="text-muted small mb-0">
                Exam: <span class="fw-bold text-dark">{{ $examination->title }}</span> | 
                Type: <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ optional($examination->examType)->name }}</span> | 
                Class: <span class="fw-bold text-dark">{{ $examination->class_name }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('examination.show', $examination) }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Exam Details
            </a>
            <button type="submit" form="marksForm" class="btn btn-success btn-sm fw-bold">
                <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Save Student Marks
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="marksForm" action="{{ route('examination.save-marks', $examination) }}" method="POST">
        @csrf

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="edit-3" style="width:1.1rem;height:1.1rem;" class="text-success"></i>
                    Student Subject Marks Input Matrix
                </h6>
                <div class="small text-muted">
                    Total Students: <span class="fw-bold text-dark">{{ count($students) }}</span> | 
                    Subjects: <span class="fw-bold text-dark">{{ count($subjects) }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="min-width: 180px;" class="text-start">Student Details</th>
                                @foreach($subjects as $sub)
                                    @php
                                        $sch = $examination->schedules->where('subject_name', $sub)->first();
                                        $subMax = $sch ? $sch->max_marks : $examination->total_marks;
                                        $subPass = $sch ? $sch->pass_marks : $examination->pass_marks;
                                    @endphp
                                    <th style="min-width: 140px;">
                                        <div class="fw-bold text-dark">{{ $sub }}</div>
                                        <div class="small text-muted font-monospace" style="font-size: 0.75rem;">Max: {{ number_format($subMax) }} | Pass: {{ number_format($subPass) }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentClassGroup = null; @endphp
                            @forelse($students as $idx => $st)
                            @if($currentClassGroup !== $st->class_name)
                                @php $currentClassGroup = $st->class_name; @endphp
                                <tr class="table-primary fw-bold">
                                    <td colspan="{{ count($subjects) + 2 }}" class="ps-3 py-2 bg-primary-subtle text-primary border-primary">
                                        <i data-lucide="graduation-cap" style="width:1.1rem;height:1.1rem;" class="me-1"></i>
                                        Class: {{ $currentClassGroup }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-center fw-semibold text-muted">{{ $idx + 1 }}</td>
                                <td class="text-start">
                                    <div class="fw-bold text-dark">{{ $st->student_name }}</div>
                                    <div class="small text-muted">
                                        Adm: <span class="font-monospace fw-semibold">{{ $st->admission_no }}</span> 
                                        @if($st->roll_no) | Roll: <span class="font-monospace fw-semibold">{{ $st->roll_no }}</span> @endif
                                    </div>
                                </td>
                                @foreach($subjects as $sub)
                                    @php
                                        $sch = $examination->schedules->where('subject_name', $sub)->first();
                                        $subMax = $sch ? $sch->max_marks : $examination->total_marks;
                                        $subPass = $sch ? $sch->pass_marks : $examination->pass_marks;
                                        $key = $st->id . '_' . $sub;
                                        $mRecord = isset($existingMarks[$key]) ? $existingMarks[$key]->first() : null;
                                        $obtainedVal = $mRecord ? $mRecord->marks_obtained : '';
                                        $isAbsentVal = $mRecord ? $mRecord->is_absent : false;
                                    @endphp
                                    <td class="p-2 text-center">
                                        <input type="hidden" name="marks[{{ $st->id }}][{{ $sub }}][total]" value="{{ $subMax }}">
                                        <input type="hidden" name="marks[{{ $st->id }}][{{ $sub }}][pass]" value="{{ $subPass }}">
                                        
                                        <div class="input-group input-group-sm mb-1">
                                            <input type="number" 
                                                   name="marks[{{ $st->id }}][{{ $sub }}][obtained]" 
                                                   class="form-control form-control-sm text-center fw-bold mark-input" 
                                                   value="{{ $obtainedVal }}" 
                                                   max="{{ $subMax }}" 
                                                   min="0" 
                                                   step="0.5" 
                                                   placeholder="0-{{ (int)$subMax }}"
                                                   @if($isAbsentVal) disabled @endif>
                                        </div>
                                        <div class="form-check form-check-inline small text-start">
                                            <input class="form-check-input absent-chk" 
                                                   type="checkbox" 
                                                   name="marks[{{ $st->id }}][{{ $sub }}][absent]" 
                                                   value="1" 
                                                   id="abs_{{ $st->id }}_{{ Str::slug($sub) }}"
                                                   @checked($isAbsentVal)>
                                            <label class="form-check-label text-danger small fw-semibold" for="abs_{{ $st->id }}_{{ Str::slug($sub) }}">Absent</label>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ count($subjects) + 2 }}" class="text-center py-5 text-muted">
                                    No active students found for class {{ $examination->class_name }}.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center">
                <a href="{{ route('examination.show', $examination) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                <button type="submit" class="btn btn-success btn-lg fw-bold px-4">
                    <i data-lucide="check-circle" style="width:1.2rem;height:1.2rem;" class="me-1"></i> Save All Marks
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.absent-chk').forEach(function(chk) {
        chk.addEventListener('change', function() {
            const input = this.closest('td').querySelector('.mark-input');
            if (this.checked) {
                input.value = '';
                input.disabled = true;
            } else {
                input.disabled = false;
            }
        });
    });
});
</script>
@endpush
@endsection
