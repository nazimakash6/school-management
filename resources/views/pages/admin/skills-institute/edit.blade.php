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
    border: 1px dashed #fcd34d;
    border-radius: 12px;
}
.skill-card-item {
    border: 1.5px solid #e2e8f0;
    transition: all 0.2s ease;
    border-left: 4px solid #0284c7 !important;
}
.skill-card-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
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
            <p class="text-muted small mb-0">Update single or multiple vocational skills for {{ $skill->student->full_name ?? 'Student' }}</p>
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

    <form action="{{ route('skills-institute.update', $skill->id) }}" method="POST" id="skillEvalEditForm">
        @csrf
        @method('PUT')

        <!-- 1. Student & Evaluation Context -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="user" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                    1. Select Academic Session, Class, Student & Evaluation Date
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Academic Session <span class="text-danger">*</span></label>
                        <select name="academic_session_id" id="selectSession" class="form-select @error('academic_session_id') is-invalid @enderror" required>
                            <option value="">-- Choose Session --</option>
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->id }}" {{ (old('academic_session_id', $currentSessionId) == $session->id) ? 'selected' : '' }}>
                                    {{ $session->name }} {{ $session->status == 'Active' ? '(Active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Class <span class="text-danger">*</span></label>
                        <select id="selectClass" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">-- Choose Class --</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->name }}" {{ (old('class_name', $skill->studentClass?->name ?? $skill->student?->class_name) == $cls->name) ? 'selected' : '' }}>
                                    {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Student <span class="text-danger">*</span></label>
                        <select name="student_id" id="selectStudent" class="form-select @error('student_id') is-invalid @enderror" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}" {{ (old('student_id', $skill->student_id) == $st->id) ? 'selected' : '' }}
                                    data-first-name="{{ $st->first_name }}"
                                    data-last-name="{{ $st->last_name }}"
                                    data-roll-no="{{ $st->roll_no }}"
                                    data-admission-no="{{ $st->admission_no }}">
                                    Roll #{{ $st->roll_no }} - {{ $st->first_name }} {{ $st->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Evaluation Date <span class="text-danger">*</span></label>
                        <input type="date" name="evaluation_date" class="form-control @error('evaluation_date') is-invalid @enderror" value="{{ old('evaluation_date', $skill->evaluation_date ? $skill->evaluation_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>
                </div>

                <!-- Student Quick Info Box -->
                <div id="studentInfoBox" class="mt-3 p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-primary text-white rounded-circle p-2 fw-bold d-flex align-items-center justify-content-center" id="studentAvatar" style="width:42px;height:42px;">
                            {{ strtoupper(substr($skill->student?->first_name ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" id="studentNameDisplay">{{ $skill->student?->full_name ?? 'Student Name' }}</h6>
                            <small class="text-muted" id="studentDetailDisplay">Roll #{{ $skill->student?->roll_no ?? 'N/A' }} &bull; Adm #{{ $skill->student?->admission_no ?? 'N/A' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $skillsList = old('skills', $skill->skills_list);
            if (!is_array($skillsList) || count($skillsList) === 0) {
                $skillsList = [
                    [
                        'skill_name'         => $skill->skill_name,
                        'skill_category'     => $skill->skill_category,
                        'assessment_type'    => $skill->assessment_type,
                        'performance_period' => $skill->performance_period,
                        'total_score'        => $skill->total_score,
                        'obtained_score'     => $skill->obtained_score,
                        'instructor_notes'   => $skill->instructor_notes,
                    ]
                ];
            }
        @endphp

        <!-- 2. Dynamic Multiple Skills Container -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="award" class="text-primary" style="width:1.3rem;height:1.3rem;"></i>
                2. Student Skill Evaluations
                <span class="badge bg-primary text-white fs-6 ms-1" id="skillsCountBadge">{{ count($skillsList) }} {{ count($skillsList) > 1 ? 'Skills' : 'Skill' }}</span>
            </h5>
            <button type="button" class="btn btn-outline-primary btn-sm fw-bold d-flex align-items-center gap-1 shadow-sm" id="btnAddSkill">
                <i data-lucide="plus-circle" style="width:1.1rem;height:1.1rem;"></i> Add Another Skill
            </button>
        </div>

        <div id="skillsContainer">
            @foreach($skillsList as $idx => $item)
                @php
                    $itemCategory = $item['skill_category'] ?? '';
                    $itemName = $item['skill_name'] ?? '';
                    $itemAssess = $item['assessment_type'] ?? 'Practical Evaluation';
                    $itemPeriod = $item['performance_period'] ?? 'monthly';
                    $itemTotal = floatval($item['total_score'] ?? 100);
                    $itemObtained = floatval($item['obtained_score'] ?? 0);
                    $itemNotes = $item['instructor_notes'] ?? '';

                    $itemRating = $itemTotal > 0 ? min(5.0, max(0.0, ($itemObtained / $itemTotal) * 5.0)) : 0.0;
                    $itemRating = round($itemRating, 1);
                    $itemPct = $itemTotal > 0 ? round(($itemObtained / $itemTotal) * 100) : 0;
                @endphp

                <div class="card border-0 shadow-sm rounded-3 mb-4 skill-card-item" data-index="{{ $idx }}">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-white rounded-pill px-3 py-1">Skill #{{ $idx + 1 }}</span>
                            @if($itemName)
                                <span class="skill-name-heading text-primary fw-bold">— {{ $itemName }}</span>
                            @else
                                <span class="skill-name-heading text-muted small">Enter skill details below...</span>
                            @endif
                        </h6>
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-skill {{ count($skillsList) <= 1 ? 'd-none' : '' }}" title="Remove this skill">
                            <i data-lucide="trash-2" style="width:1rem;height:1rem;"></i> Remove
                        </button>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Left Form Inputs -->
                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Skill Category <span class="text-danger">*</span></label>
                                        <select name="skills[{{ $idx }}][skill_category]" class="form-select select-category" required>
                                            <option value="">-- Choose Category --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat }}" {{ $itemCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Skill Name / Topic <span class="text-danger">*</span></label>
                                        <input type="text" name="skills[{{ $idx }}][skill_name]" class="form-control input-skill-name" value="{{ $itemName }}" placeholder="e.g. Web Development (HTML/CSS), Tajweed Precision" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Assessment Method <span class="text-danger">*</span></label>
                                        <select name="skills[{{ $idx }}][assessment_type]" class="form-select" required>
                                            <option value="Practical Evaluation" {{ $itemAssess == 'Practical Evaluation' ? 'selected' : '' }}>Practical Evaluation</option>
                                            <option value="Project Submission" {{ $itemAssess == 'Project Submission' ? 'selected' : '' }}>Project Submission</option>
                                            <option value="Workshop Performance" {{ $itemAssess == 'Workshop Performance' ? 'selected' : '' }}>Workshop Performance</option>
                                            <option value="Oral Assessment" {{ $itemAssess == 'Oral Assessment' ? 'selected' : '' }}>Oral Assessment</option>
                                            <option value="Skill Exam" {{ $itemAssess == 'Skill Exam' ? 'selected' : '' }}>Skill Exam</option>
                                            <option value="Live Demonstration" {{ $itemAssess == 'Live Demonstration' ? 'selected' : '' }}>Live Demonstration</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Performance Period <span class="text-danger">*</span></label>
                                        <select name="skills[{{ $idx }}][performance_period]" class="form-select" required>
                                            <option value="daily" {{ $itemPeriod == 'daily' ? 'selected' : '' }}>Daily Assessment</option>
                                            <option value="weekly" {{ $itemPeriod == 'weekly' ? 'selected' : '' }}>Weekly Evaluation</option>
                                            <option value="monthly" {{ $itemPeriod == 'monthly' ? 'selected' : '' }}>Monthly Course Evaluation</option>
                                            <option value="quarterly" {{ $itemPeriod == 'quarterly' ? 'selected' : '' }}>Quarterly Certification</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Total Max Score <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" name="skills[{{ $idx }}][total_score]" class="form-control input-total-score" value="{{ $itemTotal }}" min="0.1" max="1000" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">Obtained Score <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" name="skills[{{ $idx }}][obtained_score]" class="form-control input-obtained-score" value="{{ $itemObtained }}" min="0" max="1000" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">Instructor Notes / Remarks</label>
                                        <textarea name="skills[{{ $idx }}][instructor_notes]" class="form-control" rows="2" placeholder="Enter feedback regarding practical execution, accuracy, techniques, areas of improvement...">{{ $itemNotes }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Live Rating & Badge Preview -->
                            <div class="col-lg-4">
                                <div class="live-star-card p-3 text-center h-100 d-flex flex-column justify-content-center">
                                    <span class="text-muted small text-uppercase fw-bold">Live Skill Rating</span>
                                    <div class="d-flex align-items-baseline justify-content-center gap-1 my-2">
                                        <h2 class="fw-bold text-dark mb-0 display-6 star-rating-val">{{ number_format($itemRating, 1) }}</h2>
                                        <span class="text-muted fw-bold">/ 5.0</span>
                                    </div>

                                    <div class="mb-2 stars-container">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star" style="width:1.3rem;height:1.3rem;" class="{{ $i <= round($itemRating) ? 'star-filled' : 'star-empty' }}"></i>
                                        @endfor
                                    </div>

                                    <div class="mb-2">
                                        @php
                                            $badgeText = 'Beginner (<2.0★)';
                                            $badgeClass = 'bg-danger text-white';
                                            if ($itemRating >= 4.8) {
                                                $badgeText = 'Master Skilled (5★)';
                                                $badgeClass = 'bg-warning text-dark border border-warning';
                                            } elseif ($itemRating >= 4.0) {
                                                $badgeText = 'Expert (4.0 - 4.7★)';
                                                $badgeClass = 'bg-success text-white';
                                            } elseif ($itemRating >= 3.0) {
                                                $badgeText = 'Proficient (3.0 - 3.9★)';
                                                $badgeClass = 'bg-info text-dark';
                                            } elseif ($itemRating >= 2.0) {
                                                $badgeText = 'Developing (2.0 - 2.9★)';
                                                $badgeClass = 'bg-secondary text-white';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill fw-bold badge-level-text">
                                            {{ $badgeText }}
                                        </span>
                                    </div>

                                    <small class="text-muted fw-semibold pct-display">Percentage: {{ $itemPct }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Form Action Bar -->
        <div class="card border-0 shadow-sm rounded-3 mb-5 bg-white">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-outline-primary fw-bold d-flex align-items-center gap-2" id="btnAddSkillBottom">
                    <i data-lucide="plus-circle" style="width:1.1rem;height:1.1rem;"></i> Add Another Skill
                </button>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('skills-institute.index') }}" class="btn btn-light fw-semibold">Cancel</a>
                    <button type="submit" class="btn btn-warning btn-lg px-4 fw-bold shadow-sm d-flex align-items-center gap-2">
                        <i data-lucide="save" style="width:1.25rem;height:1.25rem;"></i>
                        <span id="btnSubmitText">
                            {{ count($skillsList) > 1 ? 'Save Changes for All ' . count($skillsList) . ' Skills' : 'Save Changes' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    function initSkillEditForm() {
        const selectSession = document.getElementById('selectSession');
        const selectClass   = document.getElementById('selectClass');
        const selectStudent = document.getElementById('selectStudent');
        const studentInfoBox = document.getElementById('studentInfoBox');
        const studentNameDisplay = document.getElementById('studentNameDisplay');
        const studentDetailDisplay = document.getElementById('studentDetailDisplay');
        const studentAvatar = document.getElementById('studentAvatar');

        const skillsContainer = document.getElementById('skillsContainer');
        const skillsCountBadge = document.getElementById('skillsCountBadge');
        const btnSubmitText = document.getElementById('btnSubmitText');

        if (!skillsContainer) return;

        let skillIndexCounter = {{ count($skillsList) }};
        const categoriesOptions = @json($categories);

        // Fetch classes when Academic Session changed
        if (selectSession) {
            selectSession.addEventListener('change', function () {
                const sessionId = this.value;
                selectClass.innerHTML = '<option value="">-- Loading Classes... --</option>';
                selectStudent.innerHTML = '<option value="">-- Select Class First --</option>';
                selectStudent.disabled = true;
                if (studentInfoBox) studentInfoBox.classList.add('d-none');

                if (!sessionId) {
                    selectClass.innerHTML = '<option value="">-- Select Session First --</option>';
                    return;
                }

                fetch(`/skills-institute/get-classes-by-session/${sessionId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.classes.length > 0) {
                            selectClass.innerHTML = '<option value="">-- Choose Class --</option>';
                            data.classes.forEach(c => {
                                const opt = document.createElement('option');
                                opt.value = c.name;
                                opt.textContent = c.name;
                                selectClass.appendChild(opt);
                            });
                        } else {
                            selectClass.innerHTML = '<option value="">No classes found in this session</option>';
                        }
                    })
                    .catch(() => {
                        selectClass.innerHTML = '<option value="">Error loading classes</option>';
                    });
            });
        }

        // Fetch students when class selected
        if (selectClass) {
            selectClass.addEventListener('change', function () {
                const className = this.value;
                const sessionId = selectSession ? selectSession.value : '';

                selectStudent.innerHTML = '<option value="">-- Loading Students... --</option>';
                selectStudent.disabled = true;
                if (studentInfoBox) studentInfoBox.classList.add('d-none');

                if (!className) {
                    selectStudent.innerHTML = '<option value="">-- Select Class First --</option>';
                    return;
                }

                let url = `/skills-institute/get-students/${encodeURIComponent(className)}`;
                if (sessionId) {
                    url += `?session_id=${sessionId}`;
                }

                fetch(url)
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
        }

        // Display selected student info
        if (selectStudent) {
            selectStudent.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                if (selected && selected.value) {
                    const firstName = selected.dataset.firstName || '';
                    const lastName = selected.dataset.lastName || '';
                    const rollNo = selected.dataset.rollNo || 'N/A';
                    const admNo = selected.dataset.admissionNo || 'N/A';

                    if (studentNameDisplay) studentNameDisplay.textContent = `${firstName} ${lastName}`.trim() || selected.text;
                    if (studentDetailDisplay) studentDetailDisplay.textContent = `Roll #${rollNo} • Adm #${admNo}`;
                    if (studentAvatar) studentAvatar.textContent = (firstName || selected.text).substring(0, 1).toUpperCase();
                    if (studentInfoBox) studentInfoBox.classList.remove('d-none');
                }
            });
        }

        // Helper: Calculate Star Rating & Badge for a skill card
        function updateCardRating(card) {
            const totalInput = card.querySelector('.input-total-score');
            const obtainedInput = card.querySelector('.input-obtained-score');
            const starRatingVal = card.querySelector('.star-rating-val');
            const badgeLevelText = card.querySelector('.badge-level-text');
            const starsContainer = card.querySelector('.stars-container');
            const pctDisplay = card.querySelector('.pct-display');

            if (!totalInput || !obtainedInput) return;

            const total = parseFloat(totalInput.value) || 100;
            const obtained = parseFloat(obtainedInput.value) || 0;

            let rating = 0;
            let percentage = 0;
            if (total > 0) {
                rating = (obtained / total) * 5.0;
                percentage = Math.round((obtained / total) * 100);
            }
            rating = Math.max(0, Math.min(5.0, rating));
            rating = Math.round(rating * 10) / 10;

            if (starRatingVal) starRatingVal.textContent = rating.toFixed(1);
            if (pctDisplay) pctDisplay.textContent = `Percentage: ${percentage}%`;

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
            if (badgeLevelText) {
                badgeLevelText.textContent = badge;
                badgeLevelText.className = `badge px-3 py-2 rounded-pill fw-bold ${badgeClass}`;
            }

            if (starsContainer) {
                const rounded = Math.round(rating);
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    const starClass = i <= rounded ? 'star-filled' : 'star-empty';
                    starsHtml += `<i data-lucide="star" style="width:1.3rem;height:1.3rem;" class="${starClass}"></i> `;
                }
                starsContainer.innerHTML = starsHtml;
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            }
        }

        // Re-index skill cards
        function reindexSkills() {
            const cards = skillsContainer.querySelectorAll('.skill-card-item');
            cards.forEach((card, idx) => {
                const num = idx + 1;
                card.setAttribute('data-index', idx);

                const numBadge = card.querySelector('.card-header .badge');
                if (numBadge) numBadge.textContent = `Skill #${num}`;

                card.querySelectorAll('[name]').forEach(elem => {
                    const name = elem.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/skills\[\d+\]/, `skills[${idx}]`);
                        elem.setAttribute('name', newName);
                    }
                });

                const btnRemove = card.querySelector('.btn-remove-skill');
                if (btnRemove) {
                    if (cards.length > 1) {
                        btnRemove.classList.remove('d-none');
                    } else {
                        btnRemove.classList.add('d-none');
                    }
                }
            });

            const count = cards.length;
            if (skillsCountBadge) skillsCountBadge.textContent = `${count} ${count > 1 ? 'Skills' : 'Skill'}`;
            if (btnSubmitText) btnSubmitText.textContent = count > 1 ? `Save Changes for All ${count} Skills` : 'Save Changes';
        }

        // Attach input listeners to card elements (live score calculation & title update)
        function bindCardInputs(card) {
            const totalInput = card.querySelector('.input-total-score');
            const obtainedInput = card.querySelector('.input-obtained-score');
            const skillNameInput = card.querySelector('.input-skill-name');
            const headingSpan = card.querySelector('.skill-name-heading');

            if (totalInput) totalInput.addEventListener('input', () => updateCardRating(card));
            if (obtainedInput) obtainedInput.addEventListener('input', () => updateCardRating(card));

            if (skillNameInput && headingSpan) {
                skillNameInput.addEventListener('input', function() {
                    if (this.value.trim()) {
                        headingSpan.textContent = `— ${this.value.trim()}`;
                        headingSpan.classList.remove('text-muted', 'small');
                        headingSpan.classList.add('text-primary', 'fw-bold');
                    } else {
                        headingSpan.textContent = 'Enter skill details below...';
                        headingSpan.classList.remove('text-primary', 'fw-bold');
                        headingSpan.classList.add('text-muted', 'small');
                    }
                });
            }

            updateCardRating(card);
        }

        // Function to append a new skill card
        function addNewSkillCard() {
            const idx = skillIndexCounter++;
            const cardNum = skillsContainer.querySelectorAll('.skill-card-item').length + 1;

            let categoryOptionsHtml = '<option value="">-- Choose Category --</option>';
            categoriesOptions.forEach(cat => {
                categoryOptionsHtml += `<option value="${cat}">${cat}</option>`;
            });

            let starIconsHtml = '';
            for (let i = 1; i <= 5; i++) {
                starIconsHtml += `<i data-lucide="star" style="width:1.3rem;height:1.3rem;" class="star-filled"></i> `;
            }

            const newCard = document.createElement('div');
            newCard.className = 'card border-0 shadow-sm rounded-3 mb-4 skill-card-item';
            newCard.setAttribute('data-index', idx);

            newCard.innerHTML = `
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span class="badge bg-dark text-white rounded-pill px-3 py-1">Skill #${cardNum}</span>
                        <span class="skill-name-heading text-muted small">Enter skill details below...</span>
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-skill" title="Remove this skill">
                        <i data-lucide="trash-2" style="width:1rem;height:1rem;"></i> Remove
                    </button>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Skill Category <span class="text-danger">*</span></label>
                                    <select name="skills[${idx}][skill_category]" class="form-select select-category" required>
                                        ${categoryOptionsHtml}
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Skill Name / Topic <span class="text-danger">*</span></label>
                                    <input type="text" name="skills[${idx}][skill_name]" class="form-control input-skill-name" placeholder="e.g. Graphic Design (Photoshop), Electrical Wiring" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Assessment Method <span class="text-danger">*</span></label>
                                    <select name="skills[${idx}][assessment_type]" class="form-select" required>
                                        <option value="Practical Evaluation" selected>Practical Evaluation</option>
                                        <option value="Project Submission">Project Submission</option>
                                        <option value="Workshop Performance">Workshop Performance</option>
                                        <option value="Oral Assessment">Oral Assessment</option>
                                        <option value="Skill Exam">Skill Exam</option>
                                        <option value="Live Demonstration">Live Demonstration</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Performance Period <span class="text-danger">*</span></label>
                                    <select name="skills[${idx}][performance_period]" class="form-select" required>
                                        <option value="daily">Daily Assessment</option>
                                        <option value="weekly">Weekly Evaluation</option>
                                        <option value="monthly" selected>Monthly Course Evaluation</option>
                                        <option value="quarterly">Quarterly Certification</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Total Max Score <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" name="skills[${idx}][total_score]" class="form-control input-total-score" value="100" min="0.1" max="1000" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Obtained Score <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" name="skills[${idx}][obtained_score]" class="form-control input-obtained-score" value="85" min="0" max="1000" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">Instructor Notes / Remarks</label>
                                    <textarea name="skills[${idx}][instructor_notes]" class="form-control" rows="2" placeholder="Enter feedback regarding practical execution, accuracy, techniques..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="live-star-card p-3 text-center h-100 d-flex flex-column justify-content-center">
                                <span class="text-muted small text-uppercase fw-bold">Live Skill Rating</span>
                                <div class="d-flex align-items-baseline justify-content-center gap-1 my-2">
                                    <h2 class="fw-bold text-dark mb-0 display-6 star-rating-val">4.3</h2>
                                    <span class="text-muted fw-bold">/ 5.0</span>
                                </div>

                                <div class="mb-2 stars-container">
                                    ${starIconsHtml}
                                </div>

                                <div class="mb-2">
                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-bold badge-level-text">
                                        Expert (4.0 - 4.7★)
                                    </span>
                                </div>

                                <small class="text-muted fw-semibold pct-display">Percentage: 85%</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            skillsContainer.appendChild(newCard);
            bindCardInputs(newCard);
            reindexSkills();

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }

            newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Global Event Delegation for Add Skill & Remove Skill buttons
        document.addEventListener('click', function(e) {
            const btnAdd = e.target.closest('#btnAddSkill, #btnAddSkillBottom');
            if (btnAdd) {
                e.preventDefault();
                addNewSkillCard();
                return;
            }

            const btnRemove = e.target.closest('.btn-remove-skill');
            if (btnRemove) {
                e.preventDefault();
                const card = btnRemove.closest('.skill-card-item');
                if (card) {
                    const allCards = skillsContainer.querySelectorAll('.skill-card-item');
                    if (allCards.length > 1) {
                        card.remove();
                        reindexSkills();
                    }
                }
            }
        });

        // Bind events to initial existing card(s)
        skillsContainer.querySelectorAll('.skill-card-item').forEach(card => {
            bindCardInputs(card);
        });
        reindexSkills();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSkillEditForm);
    } else {
        initSkillEditForm();
    }
})();
</script>
@endpush
