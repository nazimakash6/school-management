@extends('layouts.app')

@section('title', 'Student Skill Scorecard - ' . ($student->full_name ?? 'Student'))

@push('styles')
<style>
.skill-hero-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 16px;
    color: #ffffff;
}
.star-filled {
    color: #f59e0b;
    fill: #f59e0b;
}
.star-empty {
    color: #475569;
}
.cert-frame {
    border: 2px dashed #cbd5e1;
    border-radius: 14px;
    background: #f8fafc;
}
.badge-level-pill {
    font-weight: 700;
    font-size: 0.85rem;
    padding: 0.45em 0.9em;
    border-radius: 50rem;
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
                    <li class="breadcrumb-item"><a href="{{ route('skills-institute.index') }}">Skills Institute</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Student Scorecard</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Student Skill Performance Report</h1>
            <p class="text-muted small mb-0">Practical score breakdown, category competencies, and certification details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('skills-institute.edit', $skill->id) }}" class="btn btn-outline-warning btn-sm">
                <i data-lucide="edit-3" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Edit Record
            </a>
            <a href="{{ route('skills-institute.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Back to Catalog
            </a>
        </div>
    </div>

    <!-- Student Hero Profile Card -->
    <div class="skill-hero-card p-4 mb-4 shadow">
        <div class="row align-items-center g-4">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar bg-warning text-dark rounded-circle fw-bold display-6 d-flex align-items-center justify-content-center shadow" style="width:64px;height:64px;">
                        {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 text-white">{{ $student->full_name ?? 'Student' }}</h2>
                        <div class="text-white-50 small d-flex flex-wrap gap-3">
                            <span><i data-lucide="hash" style="width:0.85rem;height:0.85rem;"></i> Roll #: <strong>{{ $student->roll_no ?? 'N/A' }}</strong></span>
                            <span><i data-lucide="book-open" style="width:0.85rem;height:0.85rem;"></i> Class: <strong>{{ $student->class_name ?? 'N/A' }}</strong></span>
                            <span><i data-lucide="badge-check" style="width:0.85rem;height:0.85rem;"></i> Adm #: <strong>{{ $student->admission_no ?? 'N/A' }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 text-md-end">
                <div class="d-inline-block text-center p-3 bg-white bg-opacity-10 rounded-3 backdrop-blur">
                    <span class="text-white-50 small text-uppercase fw-semibold">Overall Skill Rating</span>
                    <div class="d-flex align-items-baseline justify-content-center gap-1 my-1">
                        <h2 class="fw-bold text-warning mb-0">{{ number_format($avgStars, 1) }}</h2>
                        <span class="text-white-50">/ 5.0</span>
                    </div>
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" style="width:1rem;height:1rem;" class="{{ $i <= round($avgStars) ? 'star-filled' : 'star-empty' }}"></i>
                        @endfor
                    </div>
                    <small class="text-white-50 mt-1 d-block">{{ $totalEvaluations }} Evaluation Record(s)</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Detailed Evaluation -->
        <div class="col-lg-7">
            <!-- Current Skill Evaluation Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="award" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Evaluation Record Details
                    </h6>
                    <span class="badge {{ $skill->badge_class }} badge-level-pill">
                        {{ $skill->badge_level }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Skill Name / Topic</span>
                            <h5 class="fw-bold text-dark mb-0">{{ $skill->skill_name }}</h5>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Category</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-1 mt-1">
                                {{ $skill->skill_category }}
                            </span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted small d-block">Assessment Method</span>
                            <strong class="text-dark">{{ $skill->assessment_type }}</strong>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted small d-block">Performance Period</span>
                            <strong class="text-dark text-capitalize">{{ $skill->performance_period }}</strong>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted small d-block">Evaluation Date</span>
                            <strong class="text-dark">{{ $skill->evaluation_date ? $skill->evaluation_date->format('M d, Y') : 'N/A' }}</strong>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Score Progress Bar Card -->
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark">Obtained Score</span>
                            <span class="fw-bold text-primary fs-5">
                                {{ number_format($skill->obtained_score) }} / {{ number_format($skill->total_score) }}
                            </span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            @php
                                $percent = $skill->total_score > 0 ? min(100, ($skill->obtained_score / $skill->total_score) * 100) : 0;
                            @endphp
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-2">
                            <span>Score Percentage: <strong>{{ number_format($percent, 1) }}%</strong></span>
                            <span>Star Rating: <strong class="text-warning"><i data-lucide="star" style="width:0.85rem;height:0.85rem;" class="star-filled"></i> {{ number_format($skill->star_rating, 1) }} / 5.0</strong></span>
                        </div>
                    </div>

                    <!-- Instructor Feedback Notes -->
                    @if($skill->instructor_notes)
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark mb-2"><i data-lucide="message-square" style="width:1rem;height:1rem;" class="text-primary me-1"></i> Instructor Remarks</h6>
                            <div class="p-3 bg-white border rounded-3 text-secondary italic">
                                "{{ $skill->instructor_notes }}"
                            </div>
                        </div>
                    @endif

                    <!-- Image Proof Attachment -->
                    @if($skill->image_proof_url)
                        <div class="mt-4">
                            <h6 class="fw-bold text-dark mb-2"><i data-lucide="image" style="width:1rem;height:1rem;" class="text-primary me-1"></i> Practical Work / Certificate Attachment</h6>
                            <div class="text-center p-3 border rounded-3 bg-light">
                                <img src="{{ $skill->image_proof_url }}" alt="Proof" class="img-fluid rounded shadow-sm" style="max-height: 350px;">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Verification & Skill History -->
        <div class="col-lg-5">
            <!-- Verification & Certificate Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="shield-check" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
                        Certificate & Verification
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="cert-frame p-3 text-center mb-3">
                        <span class="text-muted small text-uppercase fw-bold">Official Certificate Code</span>
                        <div class="h4 fw-bold font-monospace text-primary my-2">
                            {{ $skill->certificate_code ?: 'SKL-VERIFIED-2026' }}
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                            <i data-lucide="check-circle" style="width:0.85rem;height:0.85rem;" class="me-1"></i> Verified & Recorded
                        </span>
                    </div>

                    <div class="text-muted small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span>Evaluated By:</span>
                            <strong class="text-dark">{{ $skill->creator->name ?? 'School Instructor' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span>Recorded On:</span>
                            <strong class="text-dark">{{ $skill->created_at ? $skill->created_at->format('M d, Y @ h:i A') : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span>Status:</span>
                            <strong class="text-success">Active & Certified</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student's Skill History Summary Table -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="history" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Student's Complete Skill Log
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-3">Skill / Topic</th>
                                    <th>Rating</th>
                                    <th class="pe-3 text-end">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStudentSkills as $item)
                                    <tr class="{{ $item->id == $skill->id ? 'table-warning fw-bold' : '' }}">
                                        <td class="ps-3">
                                            <a href="{{ route('skills-institute.show', $item->id) }}" class="text-decoration-none text-dark">
                                                {{ $item->skill_name }}
                                            </a>
                                            <div class="text-muted small">{{ $item->skill_category }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                <i data-lucide="star" style="width:0.75rem;height:0.75rem;" class="star-filled"></i>
                                                {{ number_format($item->star_rating, 1) }}
                                            </span>
                                        </td>
                                        <td class="pe-3 text-end text-muted small">
                                            {{ $item->evaluation_date ? $item->evaluation_date->format('M d') : 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted small">No other skills recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
