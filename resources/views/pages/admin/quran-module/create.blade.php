@extends('layouts.app')

@section('title', 'Record Quran Progress')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quran-module.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Content Header --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Record Student Quran Progress
            </h1>
            <p class="text-muted mb-0">Add evaluation entry for Hifz, Nazra, Qaida, Tajweed, Hadith, or Dua</p>
        </div>
        <div>
            <a href="{{ route('quran-module.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;"></i>
                <strong class="fw-bold">Please check the form for errors:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('quran-module.store') }}" method="POST">
        @csrf
        
        <input type="hidden" name="category" id="categoryInput" value="{{ old('category', 'Hifz') }}">

        <div class="row g-4">
            {{-- Main Form Card --}}
            <div class="col-lg-8">
                {{-- Section 1: Student Selection & Date --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user-check" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            1. Select Student &amp; Evaluation Date
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Class <span class="text-danger">*</span></label>
                                <select id="classSelect" class="form-select" required>
                                    <option value="">-- Select Class --</option>
                                    @foreach($classes as $cls)
                                        <option value="{{ $cls->name }}">{{ $cls->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Student <span class="text-danger">*</span></label>
                                <select id="studentSelect" name="student_id" class="form-select" required disabled>
                                    <option value="">-- Select Class First --</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Entry Date <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date" class="form-control" value="{{ old('entry_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        {{-- Latest Progress Notice Badge --}}
                        <div id="latestProgressNotice" class="mt-3 p-3 rounded-3 bg-light border text-muted small" style="display: none;">
                            <i data-lucide="info" class="text-primary me-1" style="width:1rem;height:1rem;"></i>
                            <span id="latestProgressText"></span>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Category Selector --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="layers" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            2. Select Study Category
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap gap-2" id="categoryPillsContainer">
                            <button type="button" class="category-pill-btn active" data-cat="Hifz">
                                <i data-lucide="award" style="width:1.1rem;height:1.1rem;" class="text-purple"></i>
                                <span>Hifz (Memorization)</span>
                            </button>

                            <button type="button" class="category-pill-btn" data-cat="Nazra">
                                <i data-lucide="book-open" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                                <span>Nazra (Recitation)</span>
                            </button>

                            <button type="button" class="category-pill-btn" data-cat="Qaida">
                                <i data-lucide="file-text" style="width:1.1rem;height:1.1rem;" class="text-info"></i>
                                <span>Qaida (Basic Arabic)</span>
                            </button>

                            <button type="button" class="category-pill-btn" data-cat="Tajweed">
                                <i data-lucide="volume-2" style="width:1.1rem;height:1.1rem;" class="text-warning"></i>
                                <span>Tajweed Rules</span>
                            </button>

                            <button type="button" class="category-pill-btn" data-cat="Hadith">
                                <i data-lucide="bookmark" style="width:1.1rem;height:1.1rem;" class="text-success"></i>
                                <span>Hadith</span>
                            </button>

                            <button type="button" class="category-pill-btn" data-cat="Dua">
                                <i data-lucide="heart" style="width:1.1rem;height:1.1rem;" class="text-teal"></i>
                                <span>Masnoon Duas</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Dynamic Category Details --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="edit-3" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            3. Lesson &amp; Progress Details
                        </h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- Panel: Hifz --}}
                        <div id="panelHifz" class="category-panel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-dark small">Daily Sabaq (New Lesson) <span class="text-danger">*</span></label>
                                    <input type="text" name="sabaq" class="form-control" placeholder="e.g. Para 5 (Surah An-Nisa v1-24)" value="{{ old('sabaq') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark small">Sabqi (Recent Revision)</label>
                                    <input type="text" name="sabqi" class="form-control" placeholder="e.g. Para 4 (Ali-Imran v150-200)" value="{{ old('sabqi') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark small">Manzil (Old Revision)</label>
                                    <input type="text" name="manzil" class="form-control" placeholder="e.g. Para 1 to 3" value="{{ old('manzil') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark small">Total Parahs Memorized So Far</label>
                                    <input type="number" name="total_parahs_memorized" class="form-control" min="0" max="30" placeholder="e.g. 5" value="{{ old('total_parahs_memorized', 0) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Panel: Nazra --}}
                        <div id="panelNazra" class="category-panel" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark small">Para Number (Juz)</label>
                                    <select name="para_no" class="form-select">
                                        <option value="">-- Select Para --</option>
                                        @for($i=1;$i<=30;$i++)
                                            <option value="{{ $i }}" {{ old('para_no') == $i ? 'selected' : '' }}>Para {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark small">Surah Name</label>
                                    <input type="text" name="surah_name" class="form-control" placeholder="e.g. Surah Al-Baqarah" value="{{ old('surah_name') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold text-dark small">Ayah From</label>
                                    <input type="number" name="ayah_from" class="form-control" min="1" placeholder="1" value="{{ old('ayah_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold text-dark small">Ayah To</label>
                                    <input type="number" name="ayah_to" class="form-control" min="1" placeholder="50" value="{{ old('ayah_to') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Panel: General (Qaida, Tajweed, Hadith, Dua) --}}
                        <div id="panelGeneral" class="category-panel" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label id="generalLessonLabel" class="form-label fw-semibold text-dark small">Lesson / Topic Name <span class="text-danger">*</span></label>
                                    <input type="text" name="lesson_name" id="lessonNameInput" class="form-control" placeholder="e.g. Lesson 8: Leen Letters" value="{{ old('lesson_name') }}">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Sidebar Column: Evaluation & Settings --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="check-square" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            4. Evaluation &amp; Grade
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small">Assigned Teacher / Qari</label>
                                <input type="text" name="teacher_name" class="form-control" placeholder="e.g. Qari Abdul Rahman" value="{{ old('teacher_name', 'Qari Abdul Rahman') }}">
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Score / Grade (%)</label>
                                <input type="number" step="0.1" name="score" class="form-control" min="0" max="100" value="{{ old('score', 95.0) }}" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Mistakes Count</label>
                                <input type="number" name="mistakes_count" class="form-control" min="0" value="{{ old('mistakes_count', 0) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small">Evaluation Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="In Progress" {{ old('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Excellent" {{ old('status') === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="Needs Improvement" {{ old('status') === 'Needs Improvement' ? 'selected' : '' }}>Needs Improvement</option>
                                    <option value="Passed" {{ old('status') === 'Passed' ? 'selected' : '' }}>Passed</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small">Teacher Remarks / Notes</label>
                                <textarea name="remarks" class="form-control" rows="3" placeholder="Enter teacher feedback, pronunciation notes, or recommendations...">{{ old('remarks') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top p-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i data-lucide="save" style="width:1rem;height:1rem;"></i>
                            <span>Save Progress Record</span>
                        </button>
                        <a href="{{ route('quran-module.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect          = document.getElementById('classSelect');
    const studentSelect        = document.getElementById('studentSelect');
    const categoryInput        = document.getElementById('categoryInput');
    const pillsContainer       = document.getElementById('categoryPillsContainer');
    const panelHifz            = document.getElementById('panelHifz');
    const panelNazra           = document.getElementById('panelNazra');
    const panelGeneral         = document.getElementById('panelGeneral');
    const generalLessonLabel   = document.getElementById('generalLessonLabel');
    const lessonNameInput      = document.getElementById('lessonNameInput');
    const latestProgressNotice = document.getElementById('latestProgressNotice');
    const latestProgressText   = document.getElementById('latestProgressText');

    // Handle Category Pill Switching
    pillsContainer.addEventListener('click', function(e) {
        const btn = e.target.closest('.category-pill-btn');
        if (!btn) return;

        // Toggle active state
        document.querySelectorAll('.category-pill-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const cat = btn.getAttribute('data-cat');
        categoryInput.value = cat;
        switchCategoryPanels(cat);
    });

    function switchCategoryPanels(cat) {
        panelHifz.style.display    = 'none';
        panelNazra.style.display   = 'none';
        panelGeneral.style.display = 'none';

        if (cat === 'Hifz') {
            panelHifz.style.display = 'block';
        } else if (cat === 'Nazra') {
            panelNazra.style.display = 'block';
        } else {
            panelGeneral.style.display = 'block';
            if (cat === 'Qaida') {
                generalLessonLabel.innerHTML = 'Qaida Lesson / Page Number <span class="text-danger">*</span>';
                lessonNameInput.placeholder = 'e.g. Lesson 8: Leen Letters (Waw & Ya Leen)';
            } else if (cat === 'Tajweed') {
                generalLessonLabel.innerHTML = 'Tajweed Topic / Rule Name <span class="text-danger">*</span>';
                lessonNameInput.placeholder = 'e.g. Rules of Noon Sakinah & Tanween (Idgham & Ikhfa)';
            } else if (cat === 'Hadith') {
                generalLessonLabel.innerHTML = 'Hadith Title / Number <span class="text-danger">*</span>';
                lessonNameInput.placeholder = 'e.g. Hadith #12: Leave that which makes you doubtful';
            } else if (cat === 'Dua') {
                generalLessonLabel.innerHTML = 'Dua Title / Recitation Topic <span class="text-danger">*</span>';
                lessonNameInput.placeholder = 'e.g. Masnoon Duas: Before & After Meals & Sleeping';
            }
        }
    }

    // Class selection change -> Fetch Students
    classSelect.addEventListener('change', function() {
        const className = this.value;
        studentSelect.innerHTML = '<option value="">Loading students...</option>';
        studentSelect.disabled = true;
        latestProgressNotice.style.display = 'none';

        if (!className) {
            studentSelect.innerHTML = '<option value="">-- Select Class First --</option>';
            return;
        }

        fetch(`/quran-module/get-students/${encodeURIComponent(className)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.students.length > 0) {
                    let html = '<option value="">-- Select Student --</option>';
                    data.students.forEach(s => {
                        html += `<option value="${s.id}">Roll #${s.roll_no} - ${s.first_name} ${s.last_name || ''}</option>`;
                    });
                    studentSelect.innerHTML = html;
                    studentSelect.disabled = false;
                } else {
                    studentSelect.innerHTML = '<option value="">No students found in this class</option>';
                }
            })
            .catch(() => {
                studentSelect.innerHTML = '<option value="">Error loading students</option>';
            });
    });

    // Student selection change -> Fetch Latest Progress
    studentSelect.addEventListener('change', function() {
        const studentId = this.value;
        if (!studentId) {
            latestProgressNotice.style.display = 'none';
            return;
        }

        fetch(`/quran-module/latest-progress/${studentId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.latest) {
                    const l = data.latest;
                    latestProgressText.textContent = `Last Record: ${l.category} (${l.status}) on ${l.entry_date ? new Date(l.entry_date).toLocaleDateString() : ''} - Score: ${l.score}% ${l.remarks ? ' · "' + l.remarks + '"' : ''}`;
                    latestProgressNotice.style.display = 'block';
                } else {
                    latestProgressNotice.style.display = 'none';
                }
            })
            .catch(() => {
                latestProgressNotice.style.display = 'none';
            });
    });

    // Initialize category view
    switchCategoryPanels(categoryInput.value);
});
</script>
@endpush
@endsection
