@extends('layouts.app')

@section('title', 'Student Discipline Profile — ' . ($student->full_name ?? 'Student'))

@push('styles')
<style>
.star-filled {
    color: #f59e0b;
    fill: #f59e0b;
}
.star-empty {
    color: #cbd5e1;
}
.profile-card {
    border: none;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Web Header Card & Action Buttons -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('discipline.index') }}">Discipline & Character</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Student Web Profile</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Student Character & Discipline Web Profile</h1>
            <p class="text-muted small mb-0">{{ $student->full_name }} (Class: {{ $student->class_name }}, Roll #: {{ $student->roll_no ?? 'N/A' }})</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('discipline.daily-print', $discipline->id) }}" target="_blank" class="btn btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5 text-white" style="background-color: #3d1a06; border-color: #3d1a06;">
                <i data-lucide="file-spreadsheet" style="width: 1rem; height: 1rem;"></i> Daily Print Report
            </a>
            <a href="{{ route('discipline.print', $discipline->id) }}" target="_blank" class="btn btn-primary btn-sm fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5">
                <i data-lucide="printer" style="width: 1rem; height: 1rem;"></i> Print Official Report
            </a>
            <a href="{{ route('discipline.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i> Back to List
            </a>
            <a href="{{ route('discipline.edit', $discipline->id) }}" class="btn btn-warning text-dark fw-semibold btn-sm d-inline-flex align-items-center gap-1">
                <i data-lucide="pencil" style="width: 1rem; height: 1rem;"></i> Edit Record
            </a>
        </div>
    </div>

    <!-- Periodic Performance & Star Rating Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="profile-card p-3 border-start border-warning border-4 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Current Day Rating</span>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <h2 class="fw-bold text-warning mb-0">{{ number_format($dailyStars, 1) }}</h2>
                    <span class="text-muted small">/ 5.0 Stars</span>
                </div>
                <div class="mt-2 d-flex align-items-center justify-content-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= round($dailyStars) ? '#f59e0b' : '#cbd5e1' }}" stroke="{{ $i <= round($dailyStars) ? '#f59e0b' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-0.5">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="profile-card p-3 border-start border-primary border-4 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Weekly Star Rating</span>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <h2 class="fw-bold text-primary mb-0">{{ number_format($weeklyStars, 1) }}</h2>
                    <span class="text-muted small">/ 5.0 Stars</span>
                </div>
                <div class="mt-2 d-flex align-items-center justify-content-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= round($weeklyStars) ? '#3b82f6' : '#cbd5e1' }}" stroke="{{ $i <= round($weeklyStars) ? '#3b82f6' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-0.5">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="profile-card p-3 border-start border-info border-4 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Monthly Star Rating</span>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <h2 class="fw-bold text-info mb-0">{{ number_format($monthlyStars, 1) }}</h2>
                    <span class="text-muted small">/ 5.0 Stars</span>
                </div>
                <div class="mt-2 d-flex align-items-center justify-content-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= round($monthlyStars) ? '#06b6d4' : '#cbd5e1' }}" stroke="{{ $i <= round($monthlyStars) ? '#06b6d4' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-0.5">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="profile-card p-3 border-start border-success border-4 text-center">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Yearly Star Rating</span>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <h2 class="fw-bold text-success mb-0">{{ number_format($yearlyStars, 1) }}</h2>
                    <span class="text-muted small">/ 5.0 Stars</span>
                </div>
                <div class="mt-2 d-flex align-items-center justify-content-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= round($yearlyStars) ? '#10b981' : '#cbd5e1' }}" stroke="{{ $i <= round($yearlyStars) ? '#10b981' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-0.5">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Evaluation Details -->
        <div class="col-lg-8">
            <!-- Dedicated Daily Average Performance Report Card Box -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%); border-left: 5px solid #f59e0b !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-warning bg-opacity-20 rounded-3 shadow-sm d-inline-flex align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-award">
                                    <circle cx="12" cy="8" r="6" fill="#fef3c7"></circle>
                                    <path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 mb-1">Daily Average Report</span>
                                <h4 class="fw-bold text-dark mb-0">Student Daily Average Star Rating</h4>
                                <p class="text-muted small mb-0">Overall daily conduct rating across {{ number_format($totalEvaluationsCount) }} recorded evaluations</p>
                            </div>
                        </div>

                        <div class="text-end border-start ps-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Daily Average Rating</span>
                            <div class="d-flex align-items-baseline gap-1">
                                <h1 class="fw-bold text-warning mb-0 display-6">{{ number_format($overallDailyAvgStar, 1) }}</h1>
                                <span class="text-muted fw-bold">/ 5.0 Stars</span>
                            </div>
                            <div class="mt-1 d-flex align-items-center justify-content-end">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="{{ $i <= round($overallDailyAvgStar) ? '#f59e0b' : '#cbd5e1' }}" stroke="{{ $i <= round($overallDailyAvgStar) ? '#f59e0b' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-0.5">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 pt-3 border-top border-warning border-opacity-25">
                        <div class="col-sm-4 text-center border-end">
                            <span class="text-muted small d-block mb-1">Average Daily Score</span>
                            <h5 class="fw-bold text-dark mb-0">{{ number_format($overallAvgScore, 1) }} / 100</h5>
                        </div>
                        <div class="col-sm-4 text-center border-end">
                            <span class="text-muted small d-block mb-1">Total Evaluated Records</span>
                            <h5 class="fw-bold text-dark mb-0">{{ number_format($totalEvaluationsCount) }} Entries</h5>
                        </div>
                        <div class="col-sm-4 text-center">
                            <span class="text-muted small d-block mb-1">Overall Conduct Standing</span>
                            @if($overallDailyAvgStar >= 4.5)
                                <span class="badge bg-success px-3 py-1 fs-6">🏆 Excellent Conduct</span>
                            @elseif($overallDailyAvgStar >= 3.5)
                                <span class="badge bg-primary px-3 py-1 fs-6">⭐ Good Standing</span>
                            @elseif($overallDailyAvgStar >= 2.5)
                                <span class="badge bg-warning text-dark px-3 py-1 fs-6">🌤 Satisfactory</span>
                            @else
                                <span class="badge bg-danger px-3 py-1 fs-6">⚠️ Needs Improvement</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Record Details Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Record Details: {{ $discipline->entry_date ? $discipline->entry_date->format('F d, Y') : '' }}</h5>
                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 fs-6">
                        ⭐ {{ number_format($discipline->star_rating, 1) }} Stars
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Category Ratings</span>
                            @if (!empty($discipline->category_ratings) && is_array($discipline->category_ratings))
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach ($discipline->category_ratings as $cr)
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50">
                                            {{ $cr['category'] }}: <strong>{{ number_format($cr['star_rating'], 1) }}★</strong>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="fw-semibold text-dark fs-6">{{ $discipline->category }}</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small d-block">Evaluation Score</span>
                            <span class="fw-bold text-dark fs-5">{{ (float)$discipline->obtained_score == (int)$discipline->obtained_score ? (int)$discipline->obtained_score : number_format($discipline->obtained_score, 1) }} / {{ (float)$discipline->total_score == (int)$discipline->total_score ? (int)$discipline->total_score : number_format($discipline->total_score, 1) }} Points</span>
                        </div>
                    </div>

                    @if($discipline->title)
                        <h5 class="fw-bold text-dark mb-2">{{ $discipline->title }}</h5>
                    @endif

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <h6 class="fw-semibold text-secondary mb-2">Teacher / Counselor Remarks</h6>
                        <p class="text-dark mb-0 lh-base fs-6" style="white-space: pre-wrap;">{{ $discipline->remarks ?: 'No additional remarks provided.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Student Full Discipline Performance History -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Full Performance History for {{ $student->full_name }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('discipline.daily-print', $discipline->id) }}" target="_blank" class="btn btn-sm text-white fw-semibold" style="background-color: #3d1a06; border-color: #3d1a06;">
                            <i data-lucide="file-spreadsheet" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Daily Print Report
                        </a>
                        <a href="{{ route('discipline.print', $discipline->id) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold">
                            <i data-lucide="printer" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Print Full Certificate
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Date</th>
                                <th>Category Ratings</th>
                                <th>Score</th>
                                <th>Star Rating</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentDisciplines as $hist)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $hist->entry_date ? $hist->entry_date->format('M d, Y') : '-' }}</td>
                                    <td>
                                        @if (!empty($hist->category_ratings) && is_array($hist->category_ratings))
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($hist->category_ratings as $cr)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $cr['category'] }}: <strong>{{ number_format($cr['star_rating'], 1) }}★</strong>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="badge bg-light text-dark border">{{ $hist->category }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ (float)$hist->obtained_score == (int)$hist->obtained_score ? (int)$hist->obtained_score : number_format($hist->obtained_score, 1) }} / {{ (float)$hist->total_score == (int)$hist->total_score ? (int)$hist->total_score : number_format($hist->total_score, 1) }}</td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning">
                                            ⭐ {{ number_format($hist->star_rating, 1) }}
                                        </span>
                                    </td>
                                    <td class="small text-muted text-truncate" style="max-width: 200px;">{{ $hist->remarks ?: ($hist->title ?: '-') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Student Profile Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Student Profile Summary</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Student Name</span>
                            <span class="fw-semibold text-dark">{{ $student->full_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Roll Number</span>
                            <span class="fw-semibold text-dark">{{ $student->roll_no ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Class & Section</span>
                            <span class="fw-semibold text-dark">{{ $student->class_name }} {{ $student->section_name ? "({$student->section_name})" : '' }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Admission No</span>
                            <span class="fw-semibold text-dark">{{ $student->admission_no }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Daily Average Star</span>
                            <span class="fw-bold text-warning">⭐ {{ number_format($overallDailyAvgStar, 1) }} / 5.0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Overall Avg Score</span>
                            <span class="fw-bold text-success">{{ number_format($overallAvgScore, 1) }} / 100</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-3 text-center d-flex flex-column gap-2">
                    <a href="{{ route('discipline.daily-print', $discipline->id) }}" target="_blank" class="btn btn-sm w-100 fw-semibold text-white shadow-sm" style="background-color: #3d1a06; border-color: #3d1a06;">
                        <i data-lucide="file-spreadsheet" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Print Daily Development Report
                    </a>
                    <a href="{{ route('discipline.print', $discipline->id) }}" target="_blank" class="btn btn-primary btn-sm w-100 fw-semibold">
                        <i data-lucide="printer" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Print Admission-Style Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
