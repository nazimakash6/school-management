@extends('layouts.app')

@section('title', 'Edit Skill Evaluation - Skills Institute')

@push('styles')
<style>
.star-filled {
    color: #f59e0b;
    fill: #f59e0b;
}
.star-empty {
    color: #cbd5e1;
}
.live-star-card {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1px solid #fcd34d;
    border-radius: 12px;
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
                    <li class="breadcrumb-item active" aria-current="page">Edit Skill Evaluation</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Edit Skill Evaluation Record</h1>
            <p class="text-muted small mb-0">Update assessment details for {{ $skill->student->full_name ?? 'Student' }}</p>
        </div>
        <div>
            <a href="{{ route('skills-institute.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Catalog
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i data-lucide="alert-triangle" class="me-1" style="width:1rem;height:1rem;"></i> Validation Errors</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('skills-institute.update', $skill->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Main Form Left -->
            <div class="col-lg-8">
                <!-- 1. Student Info Locked Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            1. Student Details (Read Only)
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                            <div class="avatar bg-primary text-white rounded-circle p-2 fw-bold" style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;">
                                {{ strtoupper(substr($skill->student->first_name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $skill->student->full_name ?? 'N/A' }}</h6>
                                <small class="text-muted">Roll #{{ $skill->student->roll_no ?? 'N/A' }} &bull; Class: {{ $skill->student->class_name ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Skill Evaluation Details Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="award" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            2. Skill & Category Specification
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Evaluation Date <span class="text-danger">*</span></label>
                                <input type="date" name="evaluation_date" class="form-control @error('evaluation_date') is-invalid @enderror" value="{{ old('evaluation_date', $skill->evaluation_date ? $skill->evaluation_date->format('Y-m-d') : '') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Skill Category <span class="text-danger">*</span></label>
                                <select name="skill_category" class="form-select @error('skill_category') is-invalid @enderror" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('skill_category', $skill->skill_category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Skill Name / Topic <span class="text-danger">*</span></label>
                                <input type="text" name="skill_name" class="form-control @error('skill_name') is-invalid @enderror" value="{{ old('skill_name', $skill->skill_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Assessment Method <span class="text-danger">*</span></label>
                                <select name="assessment_type" class="form-select @error('assessment_type') is-invalid @enderror" required>
                                    <option value="Practical Evaluation" {{ old('assessment_type', $skill->assessment_type) == 'Practical Evaluation' ? 'selected' : '' }}>Practical Evaluation</option>
                                    <option value="Project Submission" {{ old('assessment_type', $skill->assessment_type) == 'Project Submission' ? 'selected' : '' }}>Project Submission</option>
                                    <option value="Workshop Performance" {{ old('assessment_type', $skill->assessment_type) == 'Workshop Performance' ? 'selected' : '' }}>Workshop Performance</option>
                                    <option value="Oral Assessment" {{ old('assessment_type', $skill->assessment_type) == 'Oral Assessment' ? 'selected' : '' }}>Oral Assessment</option>
                                    <option value="Skill Exam" {{ old('assessment_type', $skill->assessment_type) == 'Skill Exam' ? 'selected' : '' }}>Skill Exam</option>
                                    <option value="Live Demonstration" {{ old('assessment_type', $skill->assessment_type) == 'Live Demonstration' ? 'selected' : '' }}>Live Demonstration</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Performance Period <span class="text-danger">*</span></label>
                                <select name="performance_period" class="form-select @error('performance_period') is-invalid @enderror" required>
                                    <option value="daily" {{ old('performance_period', $skill->performance_period) == 'daily' ? 'selected' : '' }}>Daily Assessment</option>
                                    <option value="weekly" {{ old('performance_period', $skill->performance_period) == 'weekly' ? 'selected' : '' }}>Weekly Evaluation</option>
                                    <option value="monthly" {{ old('performance_period', $skill->performance_period) == 'monthly' ? 'selected' : '' }}>Monthly Course Evaluation</option>
                                    <option value="quarterly" {{ old('performance_period', $skill->performance_period) == 'quarterly' ? 'selected' : '' }}>Quarterly Certification</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Certificate Code</label>
                                <input type="text" name="certificate_code" class="form-control" value="{{ old('certificate_code', $skill->certificate_code) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Remarks & Proof Upload Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="file-text" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            3. Instructor Remarks & Proof Attachment
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Instructor Evaluation Notes</label>
                            <textarea name="instructor_notes" class="form-control" rows="3">{{ old('instructor_notes', $skill->instructor_notes) }}</textarea>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark">Replace Image / Certificate Proof (Optional)</label>
                            @if($skill->image_proof_url)
                                <div class="mb-2">
                                    <img src="{{ $skill->image_proof_url }}" alt="Proof" class="img-thumbnail" style="max-height: 120px;">
                                </div>
                            @endif
                            <input type="file" name="image_proof" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scoring & Live Rating Right Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="percent" class="text-warning" style="width:1.2rem;height:1.2rem;"></i>
                            Score & Star Calculation
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Total Max Score <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="total_score" id="totalScore" class="form-control form-control-lg @error('total_score') is-invalid @enderror" value="{{ old('total_score', $skill->total_score) }}" min="0.1" max="1000" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Obtained Score <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="obtained_score" id="obtainedScore" class="form-control form-control-lg @error('obtained_score') is-invalid @enderror" value="{{ old('obtained_score', $skill->obtained_score) }}" min="0" max="1000" required>
                        </div>

                        <!-- Live Calculated Star Card -->
                        <div class="live-star-card p-3 text-center mb-4">
                            <span class="text-muted small text-uppercase fw-bold">Calculated Rating</span>
                            <div class="d-flex align-items-baseline justify-content-center gap-1 my-2">
                                <h1 id="starRatingVal" class="fw-bold text-dark mb-0 display-5">{{ number_format($skill->star_rating, 1) }}</h1>
                                <span class="text-muted fw-bold">/ 5.0</span>
                            </div>

                            <div class="mb-2" id="starsContainer">
                                @for($i = 1; $i <= 5; $i++)
                                    <i data-lucide="star" style="width:1.5rem;height:1.5rem;" class="{{ $i <= round($skill->star_rating) ? 'star-filled' : 'star-empty' }}"></i>
                                @endfor
                            </div>

                            <span id="badgeLevelText" class="badge {{ $skill->badge_class }} px-3 py-2 rounded-pill fw-bold">
                                {{ $skill->badge_level }}
                            </span>
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="save" style="width:1.25rem;height:1.25rem;"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalScore    = document.getElementById('totalScore');
    const obtainedScore = document.getElementById('obtainedScore');
    const starRatingVal = document.getElementById('starRatingVal');
    const badgeLevelText = document.getElementById('badgeLevelText');
    const starsContainer = document.getElementById('starsContainer');

    function updateStars() {
        const total = parseFloat(totalScore.value) || 100;
        const obtained = parseFloat(obtainedScore.value) || 0;

        let rating = 0;
        if (total > 0) {
            rating = (obtained / total) * 5.0;
        }
        rating = Math.max(0, Math.min(5.0, rating));
        rating = Math.round(rating * 10) / 10;

        starRatingVal.textContent = rating.toFixed(1);

        let badge = 'Beginner (<2.0★)';
        let badgeClass = 'bg-danger text-white';
        if (rating >= 4.8) {
            badge = 'Master Skilled (5★)';
            badgeClass = 'bg-warning text-dark border border-warning';
        } else if (rating >= 4.0) {
            badge = 'Expert (4.0 - 4.7★)';
            badgeClass = 'bg-success text-white';
        } else if (rating >= 3.0) {
            badge = 'Proficient (3.0 - 3.9★)';
            badgeClass = 'bg-info text-dark';
        } else if (rating >= 2.0) {
            badge = 'Developing (2.0 - 2.9★)';
            badgeClass = 'bg-secondary text-white';
        }
        badgeLevelText.textContent = badge;
        badgeLevelText.className = `badge px-3 py-2 rounded-pill fw-bold ${badgeClass}`;

        const rounded = Math.round(rating);
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            const starClass = i <= rounded ? 'star-filled' : 'star-empty';
            starsHtml += `<i data-lucide="star" style="width:1.5rem;height:1.5rem;" class="${starClass}"></i> `;
        }
        starsContainer.innerHTML = starsHtml;
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    totalScore.addEventListener('input', updateStars);
    obtainedScore.addEventListener('input', updateStars);
});
</script>
@endpush
