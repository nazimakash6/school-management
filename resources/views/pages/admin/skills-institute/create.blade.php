@extends('layouts.app')

@section('title', 'Assess Student Skill - Skills Institute')

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
                    <li class="breadcrumb-item active" aria-current="page">Assess Student Skill</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Record Student Skill Evaluation</h1>
            <p class="text-muted small mb-0">Assess vocational & technical skills, enter scores, and generate 5-star ratings & badges</p>
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

    <form action="{{ route('skills-institute.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Main Form Left -->
            <div class="col-lg-8">
                <!-- 1. Student Selection Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            1. Select Class & Student
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Class <span class="text-danger">*</span></label>
                                <select id="selectClass" class="form-select @error('student_id') is-invalid @enderror" required>
                                    <option value="">-- Choose Class --</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="selectStudent" class="form-select @error('student_id') is-invalid @enderror" required disabled>
                                    <option value="">-- Select Class First --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Student Quick Info Box -->
                        <div id="studentInfoBox" class="mt-3 p-3 bg-light rounded-3 d-none">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar bg-primary text-white rounded-circle p-2 fw-bold" id="studentAvatar">
                                    ST
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="studentNameDisplay">Student Name</h6>
                                    <small class="text-muted" id="studentDetailDisplay">Roll # -- &bull; Class --</small>
                                </div>
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
                                <input type="date" name="evaluation_date" class="form-control @error('evaluation_date') is-invalid @enderror" value="{{ old('evaluation_date', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Skill Category <span class="text-danger">*</span></label>
                                <select name="skill_category" id="skillCategory" class="form-select @error('skill_category') is-invalid @enderror" required>
                                    <option value="">-- Choose Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('skill_category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Skill Name / Topic <span class="text-danger">*</span></label>
                                <input type="text" name="skill_name" class="form-control @error('skill_name') is-invalid @enderror" value="{{ old('skill_name') }}" placeholder="e.g. Web Development (HTML/CSS), Tajweed Precision" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Assessment Method <span class="text-danger">*</span></label>
                                <select name="assessment_type" class="form-select @error('assessment_type') is-invalid @enderror" required>
                                    <option value="Practical Evaluation" {{ old('assessment_type') == 'Practical Evaluation' ? 'selected' : '' }}>Practical Evaluation</option>
                                    <option value="Project Submission" {{ old('assessment_type') == 'Project Submission' ? 'selected' : '' }}>Project Submission</option>
                                    <option value="Workshop Performance" {{ old('assessment_type') == 'Workshop Performance' ? 'selected' : '' }}>Workshop Performance</option>
                                    <option value="Oral Assessment" {{ old('assessment_type') == 'Oral Assessment' ? 'selected' : '' }}>Oral Assessment</option>
                                    <option value="Skill Exam" {{ old('assessment_type') == 'Skill Exam' ? 'selected' : '' }}>Skill Exam</option>
                                    <option value="Live Demonstration" {{ old('assessment_type') == 'Live Demonstration' ? 'selected' : '' }}>Live Demonstration</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Performance Period <span class="text-danger">*</span></label>
                                <select name="performance_period" class="form-select @error('performance_period') is-invalid @enderror" required>
                                    <option value="daily" {{ old('performance_period') == 'daily' ? 'selected' : '' }}>Daily Assessment</option>
                                    <option value="weekly" {{ old('performance_period') == 'weekly' ? 'selected' : '' }}>Weekly Evaluation</option>
                                    <option value="monthly" {{ old('performance_period', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly Course Evaluation</option>
                                    <option value="quarterly" {{ old('performance_period') == 'quarterly' ? 'selected' : '' }}>Quarterly Certification</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Certificate Code (Optional)</label>
                                <input type="text" name="certificate_code" class="form-control" value="{{ old('certificate_code') }}" placeholder="e.g. SKL-IT-2026-9041 (Auto-generated if empty)">
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
                            <textarea name="instructor_notes" class="form-control" rows="3" placeholder="Enter detailed feedback regarding student technique, practical execution, areas of improvement, etc.">{{ old('instructor_notes') }}</textarea>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark">Attach Image / Certificate Proof (Optional)</label>
                            <input type="file" name="image_proof" class="form-control" accept="image/*">
                            <small class="text-muted">Upload project photo, certificate scan, or practical work sample (JPEG, PNG, WEBP max 5MB)</small>
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
                            <input type="number" step="0.1" name="total_score" id="totalScore" class="form-control form-control-lg @error('total_score') is-invalid @enderror" value="{{ old('total_score', 100) }}" min="0.1" max="1000" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Obtained Score <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="obtained_score" id="obtainedScore" class="form-control form-control-lg @error('obtained_score') is-invalid @enderror" value="{{ old('obtained_score', 90) }}" min="0" max="1000" required>
                        </div>

                        <!-- Live Calculated Star Card -->
                        <div class="live-star-card p-3 text-center mb-4">
                            <span class="text-muted small text-uppercase fw-bold">Calculated Rating</span>
                            <div class="d-flex align-items-baseline justify-content-center gap-1 my-2">
                                <h1 id="starRatingVal" class="fw-bold text-dark mb-0 display-5">4.5</h1>
                                <span class="text-muted fw-bold">/ 5.0</span>
                            </div>

                            <div class="mb-2" id="starsContainer">
                                @for($i = 1; $i <= 5; $i++)
                                    <i data-lucide="star" style="width:1.5rem;height:1.5rem;" class="star-filled"></i>
                                @endfor
                            </div>

                            <span id="badgeLevelText" class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                                Expert (4.0 - 4.7★)
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;"></i> Save Skill Evaluation
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
    const selectClass   = document.getElementById('selectClass');
    const selectStudent = document.getElementById('selectStudent');
    const totalScore    = document.getElementById('totalScore');
    const obtainedScore = document.getElementById('obtainedScore');
    const starRatingVal = document.getElementById('starRatingVal');
    const badgeLevelText = document.getElementById('badgeLevelText');
    const starsContainer = document.getElementById('starsContainer');
    const studentInfoBox = document.getElementById('studentInfoBox');
    const studentNameDisplay = document.getElementById('studentNameDisplay');
    const studentDetailDisplay = document.getElementById('studentDetailDisplay');
    const studentAvatar = document.getElementById('studentAvatar');

    // Fetch students when class selected
    selectClass.addEventListener('change', function () {
        const className = this.value;
        selectStudent.innerHTML = '<option value="">-- Loading Students... --</option>';
        selectStudent.disabled = true;
        studentInfoBox.classList.add('d-none');

        if (!className) {
            selectStudent.innerHTML = '<option value="">-- Select Class First --</option>';
            return;
        }

        fetch(`/skills-institute/get-students/${encodeURIComponent(className)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.students.length > 0) {
                    selectStudent.innerHTML = '<option value="">-- Select Student --</option>';
                    data.students.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = `Roll #${s.roll_no} - ${s.first_name} ${s.last_name}`;
                        opt.dataset.firstName = s.first_name;
                        opt.dataset.lastName = s.last_name;
                        opt.dataset.rollNo = s.roll_no;
                        opt.dataset.admissionNo = s.admission_no;
                        selectStudent.appendChild(opt);
                    });
                    selectStudent.disabled = false;
                } else {
                    selectStudent.innerHTML = '<option value="">No students found in this class</option>';
                }
            })
            .catch(() => {
                selectStudent.innerHTML = '<option value="">Error loading students</option>';
            });
    });

    // Display selected student info
    selectStudent.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const firstName = selected.dataset.firstName || '';
            const lastName = selected.dataset.lastName || '';
            const rollNo = selected.dataset.rollNo || 'N/A';
            const admNo = selected.dataset.admissionNo || 'N/A';

            studentNameDisplay.textContent = `${firstName} ${lastName}`;
            studentDetailDisplay.textContent = `Roll #${rollNo} • Adm #${admNo}`;
            studentAvatar.textContent = firstName.substring(0, 1).toUpperCase();
            studentInfoBox.classList.remove('d-none');
        } else {
            studentInfoBox.classList.add('d-none');
        }
    });

    // Recalculate stars and badge level live
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

        // Update badge text
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

        // Render Lucide icons
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
    updateStars();
});
</script>
@endpush
