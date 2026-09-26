@extends('layouts.app')

@section('title', 'Edit Discipline Record')

@push('styles')
<style>
.star-rating-box {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1px solid #fcd34d;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
}
.star-filled { color: #f59e0b; fill: #f59e0b; }
.star-empty  { color: #cbd5e1; }
.star-select-item {
    cursor: pointer;
    transition: transform 0.15s ease;
    font-size: 1.8rem;
    user-select: none;
}
.star-select-item:hover {
    transform: scale(1.2);
}
.star-select-item.star-filled {
    color: #f59e0b;
    fill: #f59e0b;
}
.star-select-item.star-empty {
    color: #cbd5e1;
    fill: none;
}
.stars-locked {
    pointer-events: none !important;
    opacity: 0.7;
    cursor: not-allowed !important;
}
.attendance-badge { display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-weight:600;font-size:.85rem; }
.att-present { background:#dcfce7;color:#15803d;border:1px solid #86efac; }
.att-one     { background:#fef9c3;color:#854d0e;border:1px solid #fde047; }
.att-leave   { background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc; }
.att-absent  { background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5; }
.att-none    { background:#f3f4f6;color:#6b7280;border:1px solid #d1d5db; }

.attached-cat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #f59e0b;
    border-radius: 10px;
    padding: 0.9rem 1.1rem;
    transition: all 0.2s ease;
}
.attached-cat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
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
                    <li class="breadcrumb-item"><a href="{{ route('discipline.index') }}">Discipline & Character</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Performance</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Edit Discipline Performance Record</h1>
            <p class="text-muted small mb-0">Student: {{ $discipline->student->full_name ?? 'N/A' }} (Roll #: {{ $discipline->student->roll_no ?? 'N/A' }})</p>
        </div>
        <div>
            <a href="{{ route('discipline.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i data-lucide="arrow-left" class="me-1" style="width:1rem;height:1rem;"></i> Back to List
            </a>
            <button type="submit" form="editDisciplineForm" class="btn btn-warning text-dark fw-semibold btn-sm">
                <i data-lucide="save" class="me-1" style="width:1rem;height:1rem;"></i> Update Record
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="award" class="text-warning" style="width:1.25rem;height:1.25rem;"></i>
                Edit Performance Entry for {{ $discipline->student->full_name ?? 'Student' }}
            </h5>
        </div>
        <div class="card-body p-4">
            <form id="editDisciplineForm" action="{{ route('discipline.update', $discipline->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Student Name</label>
                        <input type="text" class="form-control" value="{{ $discipline->student->full_name ?? 'Student' }} ({{ $discipline->student->class_name }})" readonly disabled>
                        <input type="hidden" name="student_id" id="editStudentId" value="{{ $discipline->student_id }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Entry Date <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" id="editEntryDate" class="form-control @error('entry_date') is-invalid @enderror" value="{{ old('entry_date', $discipline->entry_date ? $discipline->entry_date->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Performance Period <span class="text-danger">*</span></label>
                        <select name="performance_period" id="editPerformancePeriod" class="form-select @error('performance_period') is-invalid @enderror" required>
                            <option value="daily" selected>Daily</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Performance Title / Chapter</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $discipline->title) }}" placeholder="e.g. Daily Character Rating">
                    </div>

                    {{-- Section Divider --}}
                    <div class="col-12 my-2">
                        <hr class="text-muted">
                    </div>

                    {{-- Category Selection & Star Rating Control --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Select Performance Category <span class="text-danger">*</span></label>
                        <select id="singleCategorySelect" class="form-select">
                            <option value="General Behavior">General Behavior</option>
                            <option value="Punctuality">Punctuality &amp; Attendance</option>
                            <option value="Uniform & Cleanliness">Uniform &amp; Cleanliness</option>
                            <option value="Respect & Conduct">Respect &amp; Conduct</option>
                            <option value="Classroom Conduct">Classroom Conduct</option>
                            <option value="Homework Discipline">Homework Discipline</option>
                            <option value="Incident Warning">Incident Warning</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Category Remarks / Observation</label>
                        <input type="text" id="singleRemarksTextarea" class="form-control" placeholder="Write specific observation for this category..." value="{{ old('remarks', $discipline->remarks) }}">
                    </div>

                    {{-- Attendance Status Badge (shown when Punctuality selected) --}}
                    <div class="col-12" id="attendanceStatusRow" style="display:none;">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border">
                            <i data-lucide="calendar-check" class="text-primary" style="width:1.25rem;height:1.25rem;"></i>
                            <div>
                                <div class="text-muted small fw-semibold mb-1">Attendance Status on Selected Date</div>
                                <span id="attendanceStatusBadge" class="attendance-badge att-none">
                                    <i data-lucide="loader" style="width:0.9rem;height:0.9rem;"></i> Checking...
                                </span>
                            </div>
                            <div class="ms-auto text-end">
                                <div class="text-muted small">Session 1: <strong id="s1Label">—</strong></div>
                                <div class="text-muted small">Session 2: <strong id="s2Label">—</strong></div>
                            </div>
                        </div>
                    </div>

                    {{-- Star Rating Selector Box with Attach Rating Button --}}
                    <div class="col-12 mt-2">
                        <div class="star-rating-box">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <label class="form-label fw-semibold text-dark mb-1">
                                        Assign Rating for: <strong id="activeCategoryName" class="text-primary fs-6">General Behavior</strong>
                                    </label>
                                    <div class="text-muted small">
                                        <span id="starRatingInstruction">Click stars below to set rating (1 to 5 Stars)</span>
                                        <span id="starLockBadge" class="badge bg-warning text-dark ms-2" style="display:none;">
                                            <i data-lucide="lock" style="width:0.85rem;height:0.85rem;"></i> Auto-calculated from Attendance Records
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-4">
                                    <div id="interactiveStarsContainer" class="d-flex align-items-center gap-1">
                                        <i data-star="1" class="star-select-item" data-lucide="star" style="width:2rem;height:2rem;"></i>
                                        <i data-star="2" class="star-select-item" data-lucide="star" style="width:2rem;height:2rem;"></i>
                                        <i data-star="3" class="star-select-item" data-lucide="star" style="width:2rem;height:2rem;"></i>
                                        <i data-star="4" class="star-select-item" data-lucide="star" style="width:2rem;height:2rem;"></i>
                                        <i data-star="5" class="star-select-item" data-lucide="star" style="width:2rem;height:2rem;"></i>
                                    </div>
                                    <div class="border-start ps-4 text-center">
                                        <span class="text-muted small fw-semibold text-uppercase d-block">Rating</span>
                                        <div class="d-flex align-items-center justify-content-center gap-1 mt-1">
                                            <h2 id="starRatingValue" class="fw-bold text-dark mb-0">{{ number_format($discipline->star_rating, 1) }}</h2>
                                            <span class="text-muted fw-semibold">/ 5.0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top border-warning border-opacity-25">
                                <span class="text-muted small">
                                    Click <strong>"Attach Category Rating"</strong> to update or add this rating to the student's category list below.
                                </span>
                                <button type="button" id="btnAttachRating" class="btn btn-warning text-dark fw-bold btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1">
                                    <i data-lucide="plus-circle" style="width:1.1rem;height:1.1rem;"></i> Attach Category Rating
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Attached Performance Categories Box --}}
                    <div class="col-12 mt-3">
                        <div class="card border border-2 border-warning border-opacity-50 rounded-3 shadow-sm bg-light">
                            <div class="card-header bg-white py-2.5 d-flex align-items-center justify-content-between border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i data-lucide="layers" class="text-warning" style="width:1.15rem;height:1.15rem;"></i>
                                    Selected Performance Categories Box
                                </h6>
                                <span id="attachedCountBadge" class="badge bg-warning text-dark fw-bold px-2.5 py-1.5">0 Categories Attached</span>
                            </div>
                            <div class="card-body p-3">
                                <div id="attachedItemsContainer" class="d-flex flex-column gap-2">
                                    <div id="emptyAttachedState" class="text-center py-4 text-muted">
                                        <i data-lucide="award" class="mb-2 text-warning opacity-50" style="width:2.5rem;height:2.5rem;"></i>
                                        <h6 class="fw-bold text-dark mb-1">No Categories Attached Yet</h6>
                                        <p class="small text-muted mb-0">Choose a performance category above, pick its star rating, and click <strong>"Attach Category Rating"</strong> to add it here.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('discipline.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-warning text-dark fw-semibold px-4">Update Performance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const studentIdInput    = document.getElementById('editStudentId');
    const dateInput         = document.getElementById('editEntryDate');
    const categorySelect    = document.getElementById('singleCategorySelect');
    const periodSelect      = document.getElementById('editPerformancePeriod');
    const remarksInput      = document.getElementById('singleRemarksTextarea');
    const starsContainer    = document.getElementById('interactiveStarsContainer');
    const starLockBadge     = document.getElementById('starLockBadge');
    const starInstruction   = document.getElementById('starRatingInstruction');
    const activeCatNameEl   = document.getElementById('activeCategoryName');
    const btnAttach         = document.getElementById('btnAttachRating');
    const attachedContainer = document.getElementById('attachedItemsContainer');
    const emptyState        = document.getElementById('emptyAttachedState');
    const countBadge        = document.getElementById('attachedCountBadge');

    const studentId = studentIdInput ? studentIdInput.value : {{ $discipline->student_id }};
    let currentRating = parseFloat("{{ old('star_rating', $discipline->star_rating) }}") || 4.0;
    let currentObtained = parseFloat("{{ old('obtained_score', $discipline->obtained_score) }}") || 80;
    let currentTotal = parseFloat("{{ old('total_score', $discipline->total_score) }}") || 100;
    let isAttendanceLocked = false;

    // Load initial items from database
    let dbCategoryRatings = @json($discipline->category_ratings ?? []);
    let attachedItems = [];

    if (Array.isArray(dbCategoryRatings) && dbCategoryRatings.length > 0) {
        attachedItems = dbCategoryRatings.map(item => ({
            category: item.category,
            star_rating: parseFloat(item.star_rating || 5.0),
            obtained_score: parseFloat(item.obtained_score || 100),
            total_score: parseFloat(item.total_score || 100),
            remarks: item.remarks || ''
        }));
    } else {
        // Fallback for older single records
        attachedItems = [{
            category: @json($discipline->category ?? 'General Behavior'),
            star_rating: parseFloat(@json($discipline->star_rating ?? 5.0)),
            obtained_score: parseFloat(@json($discipline->obtained_score ?? 100)),
            total_score: parseFloat(@json($discipline->total_score ?? 100)),
            remarks: @json($discipline->remarks ?? '')
        }];
    }

    function renderStars(ratingVal) {
        const rounded = Math.round(ratingVal);
        document.getElementById('starRatingValue').textContent = parseFloat(ratingVal).toFixed(1);

        const starIcons = starsContainer.querySelectorAll('.star-select-item');
        starIcons.forEach((icon, idx) => {
            const starNum = idx + 1;
            if (starNum <= rounded) {
                icon.classList.add('star-filled');
                icon.classList.remove('star-empty');
            } else {
                icon.classList.remove('star-filled');
                icon.classList.add('star-empty');
            }
        });
        if (window.lucide) lucide.createIcons();
    }

    function setRating(ratingVal, obtainedScore = null, totalScore = 100) {
        currentRating = Math.max(0, Math.min(5.0, parseFloat(ratingVal) || 0));
        currentTotal = totalScore;
        currentObtained = obtainedScore !== null ? obtainedScore : Math.round((currentRating / 5.0) * totalScore);
        renderStars(currentRating);
    }

    // Interactive Click Selection
    starsContainer.addEventListener('click', function(e) {
        if (isAttendanceLocked) return;
        const starItem = e.target.closest('.star-select-item');
        if (!starItem) return;
        const starVal = parseFloat(starItem.getAttribute('data-star'));
        setRating(starVal);
    });

    // Hover effects
    starsContainer.addEventListener('mouseover', function(e) {
        if (isAttendanceLocked) return;
        const starItem = e.target.closest('.star-select-item');
        if (!starItem) return;
        const hoverVal = parseInt(starItem.getAttribute('data-star'));
        renderStars(hoverVal);
    });

    starsContainer.addEventListener('mouseleave', function() {
        if (isAttendanceLocked) return;
        renderStars(currentRating);
    });

    // Update Active Category Name Display
    categorySelect.addEventListener('change', function() {
        const cat = this.value;
        activeCatNameEl.textContent = cat;

        // Check if already attached
        const existing = attachedItems.find(i => i.category === cat);
        if (existing) {
            setRating(existing.star_rating, existing.obtained_score, existing.total_score);
            remarksInput.value = existing.remarks || '';
        }

        maybeLoadAttendance();
    });

    // Attach Category Rating Button Click
    btnAttach.addEventListener('click', function() {
        attachCurrentCategory();
    });

    function attachCurrentCategory() {
        const cat = categorySelect.value;
        if (!cat) return;

        const item = {
            category: cat,
            star_rating: currentRating,
            obtained_score: currentObtained,
            total_score: currentTotal,
            remarks: remarksInput.value.trim()
        };

        // Check if already in list -> update
        const index = attachedItems.findIndex(i => i.category === cat);
        if (index >= 0) {
            attachedItems[index] = item;
        } else {
            attachedItems.push(item);
        }

        renderAttachedItems();
    }

    function renderAttachedItems() {
        if (attachedItems.length === 0) {
            emptyState.style.display = 'block';
            countBadge.textContent = '0 Categories Attached';
            return;
        }

        emptyState.style.display = 'none';
        countBadge.textContent = `${attachedItems.length} Categories Attached`;

        let html = '';
        attachedItems.forEach((item, idx) => {
            const starsHtml = generateStarsHtml(item.star_rating);
            html += `
                <div class="attached-cat-card d-flex align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3">
                            <i data-lucide="award" style="width:1.35rem;height:1.35rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">${item.category}</h6>
                            <span class="text-muted small">${item.remarks ? item.remarks : 'No additional remarks'}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2 bg-light px-3 py-1.5 rounded-pill border">
                            <div class="d-flex align-items-center gap-1">${starsHtml}</div>
                            <strong class="text-dark fs-6 ms-1">${parseFloat(item.star_rating).toFixed(1)} / 5.0 Stars</strong>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm border-0 rounded-circle btn-remove-item" data-idx="${idx}" title="Remove category">
                            <i data-lucide="trash-2" style="width:1rem;height:1rem;"></i>
                        </button>
                    </div>

                    <!-- Hidden inputs for form submit -->
                    <input type="hidden" name="items[${idx}][category]" value="${escapeHtml(item.category)}">
                    <input type="hidden" name="items[${idx}][star_rating]" value="${item.star_rating}">
                    <input type="hidden" name="items[${idx}][obtained_score]" value="${item.obtained_score}">
                    <input type="hidden" name="items[${idx}][total_score]" value="${item.total_score}">
                    <input type="hidden" name="items[${idx}][remarks]" value="${escapeHtml(item.remarks || '')}">
                </div>
            `;
        });

        attachedContainer.innerHTML = html;
        if (window.lucide) lucide.createIcons();

        // Bind remove buttons
        attachedContainer.querySelectorAll('.btn-remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const i = parseInt(this.getAttribute('data-idx'));
                attachedItems.splice(i, 1);
                renderAttachedItems();
            });
        });
    }

    function generateStarsHtml(ratingVal) {
        const rounded = Math.round(ratingVal);
        let h = '';
        for (let i = 1; i <= 5; i++) {
            h += `<i data-lucide="star" style="width:1rem;height:1rem;" class="${i <= rounded ? 'star-filled' : 'star-empty'}"></i>`;
        }
        return h;
    }

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function maybeLoadAttendance() {
        const isAttendance = categorySelect.value === 'Punctuality';
        document.getElementById('attendanceStatusRow').style.display = isAttendance ? '' : 'none';

        if (isAttendance) {
            isAttendanceLocked = true;
            starsContainer.classList.add('stars-locked');
            starLockBadge.style.display = 'inline-flex';
            starInstruction.style.display = 'none';

            const date   = dateInput.value;
            const period = periodSelect ? periodSelect.value : 'daily';
            if (!studentId || !date) return;

            setBadge('checking');

            fetch(`/discipline/get-attendance-score/${studentId}?date=${encodeURIComponent(date)}&period=${encodeURIComponent(period)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    setRating(data.star_rating, data.obtained_score, data.total_score);
                    remarksInput.value = data.remarks;

                    if (period === 'daily') {
                        const s = data.obtained_score;
                        if (s >= 2)      setBadge('present');
                        else if (s >= 1) setBadge('one');
                        else if (s > 0)  setBadge('leave');
                        else             setBadge('absent');

                        document.getElementById('s1Label').textContent = data.session1 ? capitalize(data.session1) : '—';
                        document.getElementById('s2Label').textContent = data.session2 ? capitalize(data.session2) : '—';
                    } else {
                        const b = data.breakdown || {};
                        setBadgePeriod(
                            data.period_label,
                            data.days_counted,
                            b.presentBoth  || 0,
                            b.presentOne   || 0,
                            b.leaveDays    || 0,
                            b.absentDays   || 0,
                            data.obtained_score,
                            data.total_score,
                            data.star_rating
                        );
                        document.getElementById('s1Label').textContent = `${data.days_counted} day(s)`;
                        document.getElementById('s2Label').textContent = `Score: ${data.obtained_score}/${data.total_score}`;
                    }
                });
        } else {
            isAttendanceLocked = false;
            starsContainer.classList.remove('stars-locked');
            starLockBadge.style.display = 'none';
            starInstruction.style.display = 'inline';
            renderStars(currentRating);
        }
    }

    function setBadge(type) {
        const badge = document.getElementById('attendanceStatusBadge');
        const map = {
            checking: ['att-none',    '⏳ Checking...'],
            present:  ['att-present', '✅ Present (Both Sessions) — Rating: 5.0 ★'],
            one:      ['att-one',     '🌤 Present (One Session) — Rating: 2.5 ★'],
            leave:    ['att-leave',   '📋 On Leave — Rating: 0.75 ★'],
            absent:   ['att-absent',  '❌ Absent — Rating: 0.0 ★'],
        };
        badge.className = 'attendance-badge ' + map[type][0];
        badge.textContent = map[type][1];
    }

    function setBadgePeriod(label, days, both, one, leave, absent, obtained, total, rating) {
        const badge = document.getElementById('attendanceStatusBadge');
        const pct   = total > 0 ? Math.round((obtained / total) * 100) : 0;
        const cls   = pct >= 80 ? 'att-present' : pct >= 50 ? 'att-one' : pct > 0 ? 'att-leave' : 'att-absent';
        badge.className  = 'attendance-badge ' + cls;
        badge.textContent = `📊 ${label}: ${days} day(s) | ✅${both}d 🌤${one}d 📋${leave}d ❌${absent}d | Rating: ${rating}★`;
    }

    function capitalize(str) { return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''; }

    dateInput.addEventListener('change', maybeLoadAttendance);
    if (periodSelect) periodSelect.addEventListener('change', maybeLoadAttendance);

    // Auto-attach current active category if user submits without clicking attach first
    document.getElementById('editDisciplineForm').addEventListener('submit', function(e) {
        if (attachedItems.length === 0) {
            attachCurrentCategory();
        }
    });

    // Initial render of attached items
    renderAttachedItems();
    if (attachedItems.length > 0) {
        const first = attachedItems[0];
        categorySelect.value = first.category;
        activeCatNameEl.textContent = first.category;
        setRating(first.star_rating, first.obtained_score, first.total_score);
    }
});
</script>
@endpush
