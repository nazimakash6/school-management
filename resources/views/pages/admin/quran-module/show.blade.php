@extends('layouts.app')

@section('title', 'Quran Record Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quran-module.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="book-open" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Student Quran Evaluation Details
            </h1>
            <p class="text-muted mb-0">Record #{{ $record->id }} · {{ $record->student->full_name ?? 'Student' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('quran-module.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Back to List</span>
            </a>
            <a href="{{ route('quran-module.edit', $record->id) }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                <i data-lucide="pencil" style="width: 1rem; height: 1rem;"></i>
                <span>Edit Record</span>
            </a>
            <form action="{{ route('quran-module.destroy', $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-1">
                    <i data-lucide="trash-2" style="width: 1rem; height: 1rem;"></i>
                    <span>Delete</span>
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="check-circle" class="text-success" style="width:1.25rem;height:1.25rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Profile & Stat Bar --}}
    <div class="row g-4 mb-4">
        {{-- Student Info Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 4.5rem; height: 4.5rem; font-size: 1.8rem;">
                        {{ strtoupper(substr($record->student->first_name ?? 'S', 0, 1)) }}
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $record->student->full_name ?? 'N/A' }}</h4>
                    <p class="text-muted small mb-3">
                        Roll #: <span class="fw-bold text-dark">{{ $record->student->roll_no ?? 'N/A' }}</span> · 
                        Class: <span class="fw-bold text-dark">{{ $record->student->class_name ?? 'N/A' }}</span>
                    </p>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge badge-category {{ $record->category_badge_class }} px-3 py-2">
                            {{ $record->category }}
                        </span>
                        <span class="badge {{ $record->status_badge_class }} px-3 py-2">
                            {{ $record->status }}
                        </span>
                    </div>

                    <hr class="my-3 opacity-25">

                    <div class="row text-start g-2 small">
                        <div class="col-6 text-muted">Entry Date:</div>
                        <div class="col-6 fw-semibold text-dark text-end">{{ $record->entry_date ? $record->entry_date->format('M d, Y') : 'N/A' }}</div>
                        
                        <div class="col-6 text-muted">Teacher / Qari:</div>
                        <div class="col-6 fw-semibold text-dark text-end">{{ $record->teacher_name ?: 'Assigned Teacher' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metrics Summary --}}
        <div class="col-lg-8">
            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                        <div class="text-muted small fw-semibold text-uppercase">Score / Accuracy</div>
                        <h2 class="fw-bold text-success mb-0 mt-1">{{ number_format($record->score, 1) }}%</h2>
                        <div class="small text-muted mt-1">
                            @if($record->mistakes_count > 0)
                                <span class="text-danger">⚠️ {{ $record->mistakes_count }} Mistakes recorded</span>
                            @else
                                <span class="text-success">✅ Zero Mistakes</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                        <div class="text-muted small fw-semibold text-uppercase">Parahs Memorized</div>
                        <h2 class="fw-bold text-purple mb-0 mt-1">{{ $record->total_parahs_memorized }} <span class="fs-6 text-muted">/ 30</span></h2>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-hifz" style="width: {{ min(100, round(($record->total_parahs_memorized / 30) * 100)) }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                        <div class="text-muted small fw-semibold text-uppercase">Total History Entries</div>
                        <h2 class="fw-bold text-primary mb-0 mt-1">{{ $studentStats['total_entries'] }}</h2>
                        <div class="small text-muted mt-1">Avg Score: <strong class="text-dark">{{ $studentStats['avg_score'] }}%</strong></div>
                    </div>
                </div>
            </div>

            {{-- Details Card --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="file-text" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Evaluation Record Breakdown
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($record->category === 'Hifz')
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="text-muted small fw-semibold text-uppercase d-block">Daily Sabaq (New Lesson)</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">
                                    {{ $record->sabaq ?: "Para {$record->para_no} ({$record->surah_name} {$record->formatted_ayah_range})" }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-semibold text-uppercase d-block">Sabqi (Recent Revision)</label>
                                <div class="p-3 bg-light rounded-3 fw-semibold text-dark">
                                    {{ $record->sabqi ?: 'N/A' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-semibold text-uppercase d-block">Manzil (Old Revision)</label>
                                <div class="p-3 bg-light rounded-3 fw-semibold text-dark">
                                    {{ $record->manzil ?: 'N/A' }}
                                </div>
                            </div>
                        </div>
                    @elseif($record->category === 'Nazra')
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="text-muted small fw-semibold text-uppercase d-block">Para (Juz) #</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">Para {{ $record->para_no ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-semibold text-uppercase d-block">Surah Name</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">{{ $record->surah_name ?: 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small fw-semibold text-uppercase d-block">Ayah Range</label>
                                <div class="p-3 bg-light rounded-3 fw-bold text-dark">{{ $record->formatted_ayah_range ?: 'Full Surah' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="text-muted small fw-semibold text-uppercase d-block">Lesson / Topic Name</label>
                            <div class="p-3 bg-light rounded-3 fw-bold text-dark fs-5">
                                {{ $record->lesson_name ?: "{$record->category} Evaluation" }}
                            </div>
                        </div>
                    @endif

                    @if($record->remarks)
                        <div class="mt-3">
                            <label class="text-muted small fw-semibold text-uppercase d-block">Teacher Remarks &amp; Feedback</label>
                            <div class="p-3 bg-primary bg-opacity-10 text-dark rounded-3 border border-primary-subtle italic">
                                <i data-lucide="quote" class="text-primary me-2" style="width:1rem;height:1rem;"></i>
                                "{{ $record->remarks }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Historical Records for Student --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="history" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                Student Quran Evaluation History ({{ $studentHistory->count() }} Entries)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Category</th>
                        <th>Progress / Lesson</th>
                        <th>Score</th>
                        <th>Mistakes</th>
                        <th>Status</th>
                        <th>Teacher</th>
                        <th class="pe-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($studentHistory as $h)
                        <tr class="{{ $h->id === $record->id ? 'table-primary bg-opacity-10' : '' }}">
                            <td class="ps-3 fw-medium">
                                {{ $h->entry_date ? $h->entry_date->format('M d, Y') : 'N/A' }}
                                @if($h->id === $record->id)
                                    <span class="badge bg-primary ms-1">Viewing</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-category {{ $h->category_badge_class }}">
                                    {{ $h->category }}
                                </span>
                            </td>
                            <td>
                                @if($h->category === 'Hifz')
                                    <span class="fw-semibold text-dark">{{ $h->sabaq ?: "Para {$h->para_no}" }}</span>
                                @elseif($h->category === 'Nazra')
                                    <span class="fw-semibold text-dark">Para {{ $h->para_no }} · {{ $h->surah_name }}</span>
                                @else
                                    <span class="fw-semibold text-dark">{{ $h->lesson_name }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($h->score, 1) }}%</span>
                            </td>
                            <td>
                                @if($h->mistakes_count > 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">{{ $h->mistakes_count }} Mistakes</span>
                                @else
                                    <span class="badge bg-light text-muted border">0</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $h->status_badge_class }}">{{ $h->status }}</span>
                            </td>
                            <td class="small text-muted">{{ $h->teacher_name ?: 'Qari' }}</td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('quran-module.show', $h->id) }}" class="btn btn-light btn-sm border" title="View">
                                    <i data-lucide="eye" style="width:0.85rem;height:0.85rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No historical entries found for this student.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
