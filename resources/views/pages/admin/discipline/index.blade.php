@extends ('layouts.app')

@section ('title', 'Student Discipline & Character Performance')

@push ('styles')
    <style>
        .stat-card-perf {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }
        .stat-card-perf:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }
        .star-rating-badge {
            background: #fffbebf5;
            border: 1px solid #fde68a;
            color: #b45309;
            font-weight: 700;
            padding: 0.3rem 0.65rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .star-filled {
            color: #f59e0b;
            fill: #f59e0b;
        }
        .star-empty {
            color: #cbd5e1;
        }
    </style>
@endpush

@section ('content')
    <div class="container-fluid px-4 py-3">
        <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
        <div class="content-header mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1.5 fs-7">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"
                                ><i data-lucide="home" style="width: 0.875rem; height: 0.875rem" class="me-1"></i
                                >Dashboard</a
                            >
                        </li>
                        <li class="breadcrumb-item text-muted">Student Affairs</li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                            Discipline & Character
                        </li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2.5">
                    <div
                        class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center"
                        style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)"
                    >
                        <i data-lucide="award" style="width: 1.5rem; height: 1.5rem"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-dark tracking-tight">
                            Student Discipline & Character Performance
                        </h3>
                        <p class="text-muted mb-0 fs-7">Track daily student behavior, character scores, and weekly/monthly/yearly star ratings</p>
                    </div>
                </div>
            </div>
            <div class="content-header-actions">
                <a
                    href="{{ route('discipline.create') }}"
                    class="btn btn-warning text-dark fw-semibold btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5"
                    style="border: none"
                >
                    <i data-lucide="star" style="width: 1rem; height: 1rem" class="star-filled"></i> Record Student
                    Performance
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i data-lucide="check-circle-2" class="me-2" style="width: 1.2rem; height: 1.2rem"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Periodic Performance Star Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl col-md-4 col-sm-6">
                <div class="stat-card-perf p-3 border-start border-warning border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Daily Star Rating</span>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <h2 class="fw-bold text-warning mb-0">{{ number_format($dailyAvgStar, 1) }}</h2>
                                <span class="text-muted small">/ 5.0 Stars</span>
                            </div>
                            <div class="mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        data-lucide="star"
                                        style="width: 0.875rem; height: 0.875rem"
                                        class="{{ $i <= round($dailyAvgStar) ? 'star-filled' : 'star-empty' }}"
                                    ></i>
                                @endfor
                            </div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2.5 rounded-3 text-warning">
                            <i data-lucide="zap" style="width: 1.5rem; height: 1.5rem"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-4 col-sm-6">
                <div class="stat-card-perf p-3 border-start border-primary border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Weekly Star Rating</span>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <h2 class="fw-bold text-primary mb-0">{{ number_format($weeklyAvgStar, 1) }}</h2>
                                <span class="text-muted small">/ 5.0 Stars</span>
                            </div>
                            <div class="mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        data-lucide="star"
                                        style="width: 0.875rem; height: 0.875rem"
                                        class="{{ $i <= round($weeklyAvgStar) ? 'star-filled' : 'star-empty' }}"
                                    ></i>
                                @endfor
                            </div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2.5 rounded-3 text-primary">
                            <i data-lucide="calendar" style="width: 1.5rem; height: 1.5rem"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-4 col-sm-6">
                <div class="stat-card-perf p-3 border-start border-info border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Monthly Star Rating</span>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <h2 class="fw-bold text-info mb-0">{{ number_format($monthlyAvgStar, 1) }}</h2>
                                <span class="text-muted small">/ 5.0 Stars</span>
                            </div>
                            <div class="mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        data-lucide="star"
                                        style="width: 0.875rem; height: 0.875rem"
                                        class="{{ $i <= round($monthlyAvgStar) ? 'star-filled' : 'star-empty' }}"
                                    ></i>
                                @endfor
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2.5 rounded-3 text-info">
                            <i data-lucide="award" style="width: 1.5rem; height: 1.5rem"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-4 col-sm-6">
                <div class="stat-card-perf p-3 border-start border-success border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Yearly Star Rating</span>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <h2 class="fw-bold text-success mb-0">{{ number_format($yearlyAvgStar, 1) }}</h2>
                                <span class="text-muted small">/ 5.0 Stars</span>
                            </div>
                            <div class="mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        data-lucide="star"
                                        style="width: 0.875rem; height: 0.875rem"
                                        class="{{ $i <= round($yearlyAvgStar) ? 'star-filled' : 'star-empty' }}"
                                    ></i>
                                @endfor
                            </div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2.5 rounded-3 text-success">
                            <i data-lucide="trophy" style="width: 1.5rem; height: 1.5rem"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-4 col-sm-6">
                <div class="stat-card-perf p-3 border-start border-secondary border-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total Evaluated Entries</span>
                            <h2 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalRecordsCount) }}</h2>
                            <span class="text-muted small">Discipline Records</span>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-2.5 rounded-3 text-secondary">
                            <i data-lucide="user-check" style="width: 1.5rem; height: 1.5rem"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar Card -->
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('discipline.index') }}" class="row g-2 align-items-center">
                    {{-- 1. Academic Session Dropdown --}}
                    <div class="col-md-2">
                        <select
                            name="academic_session_id"
                            id="filterSessionSelect"
                            class="form-select form-select-sm"
                            onchange="this.form.submit()"
                        >
                            <option value="">All Academic Sessions</option>
                            @foreach ($academicSessions as $sess)
                                <option
                                    value="{{ $sess->id }}"
                                    {{ request('academic_session_id') == $sess->id ? 'selected' : '' }}
                                >
                                    {{ $sess->session_name ?? $sess->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Class Dropdown --}}
                    <div class="col-md-2">
                        <select
                            name="class_name"
                            id="filterClassSelect"
                            class="form-select form-select-sm"
                            onchange="this.form.submit()"
                        >
                            <option value="">All Classes</option>
                            @foreach ($classes as $c)
                                <option
                                    value="{{ $c->name }}"
                                    {{ request('class_name') == $c->name ? 'selected' : '' }}
                                >
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. Student Dropdown --}}
                    <div class="col-md-2">
                        <select
                            name="student_id"
                            id="filterStudentSelect"
                            class="form-select form-select-sm"
                            onchange="this.form.submit()"
                            {{ request('class_name') ? '' : 'disabled' }}
                        >
                            <option value="">All Students</option>
                            @foreach ($filterStudents as $fs)
                                <option value="{{ $fs->id }}" {{ request('student_id') == $fs->id ? 'selected' : '' }}>
                                    {{ $fs->full_name }} (Roll: {{ $fs->roll_no ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. Category Dropdown --}}
                    <div class="col-md-2">
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <option
                                value="General Behavior"
                                {{ request('category') === 'General Behavior' ? 'selected' : '' }}
                            >
                                General Behavior
                            </option>
                            <option value="Punctuality" {{ request('category') === 'Punctuality' ? 'selected' : '' }}>
                                Punctuality & Attendance
                            </option>
                            <option
                                value="Uniform & Cleanliness"
                                {{ request('category') === 'Uniform & Cleanliness' ? 'selected' : '' }}
                            >
                                Uniform & Cleanliness
                            </option>
                            <option
                                value="Respect & Conduct"
                                {{ request('category') === 'Respect & Conduct' ? 'selected' : '' }}
                            >
                                Respect & Conduct
                            </option>
                            <option
                                value="Classroom Conduct"
                                {{ request('category') === 'Classroom Conduct' ? 'selected' : '' }}
                            >
                                Classroom Conduct
                            </option>
                            <option
                                value="Homework Discipline"
                                {{ request('category') === 'Homework Discipline' ? 'selected' : '' }}
                            >
                                Homework Discipline
                            </option>
                            <option
                                value="Incident Warning"
                                {{ request('category') === 'Incident Warning' ? 'selected' : '' }}
                            >
                                Incident Warning
                            </option>
                        </select>
                    </div>

                    {{-- 5. Date Filter --}}
                    <div class="col-md-2">
                        <input
                            type="date"
                            name="date"
                            class="form-control form-control-sm"
                            value="{{ request('date') }}"
                            onchange="this.form.submit()"
                        />
                    </div>

                    {{-- 6. Search & Clear Button --}}
                    <div class="col-md-2 d-flex gap-1">
                        <div class="input-group input-group-sm">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search..."
                                value="{{ request('search') }}"
                            />
                            <button class="btn btn-outline-secondary" type="submit">
                                <i data-lucide="search" style="width: 0.85rem; height: 0.85rem"></i>
                            </button>
                        </div>
                        @if (request()->hasAny(['academic_session_id', 'class_name', 'student_id', 'category', 'date', 'search']))
                            <a
                                href="{{ route('discipline.index') }}"
                                class="btn btn-sm btn-light border text-danger d-inline-flex align-items-center"
                                title="Clear Filters"
                            >
                                <i data-lucide="rotate-ccw" style="width: 0.85rem; height: 0.85rem"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-3 py-3 text-secondary text-uppercase small" style="width: 110px">Date</th>
                            <th class="py-3 text-secondary text-uppercase small">Student Details</th>
                            <th class="py-3 text-secondary text-uppercase small">Class</th>
                            <th class="py-3 text-secondary text-uppercase small">Performance Score</th>
                            <th class="py-3 text-secondary text-uppercase small">Star Rating</th>
                            <th class="pe-3 py-3 text-end text-secondary text-uppercase small">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($disciplines as $d)
                            <tr>
                                <td class="ps-3">
                                    <span
                                        class="fw-semibold text-dark"
                                        >{{ $d->entry_date ? $d->entry_date->format('M d, Y') : '-' }}</span
                                    >
                                </td>
                                <td>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">
                                            {{ $d->student->full_name ?? 'Student' }}
                                        </h6>
                                        <span class="text-muted small"
                                            >Roll #: {{ $d->student->roll_no ?? 'N/A' }} | Adm: {{ $d->student->admission_no ?? 'N/A' }}</span
                                        >
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border px-2 py-1 fs-6">
                                        {{ $d->studentClass->name ?? ($d->student->class_name ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <span
                                            class="fw-bold text-dark fs-6"
                                            >{{ (float)$d->obtained_score == (int)$d->obtained_score ? (int)$d->obtained_score : number_format($d->obtained_score, 1) }}</span
                                        >
                                        <span class="text-muted small"
                                            >/ {{ (float)$d->total_score == (int)$d->total_score ? (int)$d->total_score : number_format($d->total_score, 1) }}</span
                                        >
                                    </div>
                                </td>
                                <td>
                                    <div class="star-rating-badge">
                                        <i
                                            data-lucide="star"
                                            style="width: 0.875rem; height: 0.875rem"
                                            class="star-filled"
                                        ></i>
                                        <span>{{ number_format($d->star_rating, 1) }} Stars</span>
                                    </div>
                                </td>
                                <td class="pe-3 text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a
                                            href="{{ route('discipline.show', $d->id) }}"
                                            class="btn btn-light text-warning"
                                            title="View Student Character Web Profile"
                                        >
                                            <i data-lucide="eye" style="width: 0.875rem; height: 0.875rem"></i>
                                        </a>
                                        <a
                                            href="{{ route('discipline.daily-print', $d->id) }}"
                                            target="_blank"
                                            class="btn btn-light text-dark"
                                            style="color: #3d1a06 !important;"
                                            title="Print Daily Development Report (100 Marks)"
                                        >
                                            <i data-lucide="file-spreadsheet" style="width: 0.875rem; height: 0.875rem"></i>
                                        </a>
                                        <a
                                            href="{{ route('discipline.print', $d->id) }}"
                                            target="_blank"
                                            class="btn btn-light text-primary"
                                            title="Print Official Admission-Style Report"
                                        >
                                            <i data-lucide="printer" style="width: 0.875rem; height: 0.875rem"></i>
                                        </a>
                                        <a
                                            href="{{ route('discipline.edit', $d->id) }}"
                                            class="btn btn-light text-secondary"
                                            title="Edit"
                                        >
                                            <i data-lucide="pencil" style="width: 0.875rem; height: 0.875rem"></i>
                                        </a>
                                        <form
                                            action="{{ route('discipline.destroy', $d->id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="
                                                return confirm(
                                                    'Are you sure you want to delete this performance record?',
                                                );
                                            "
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <button type="submit" class="btn btn-light text-danger" title="Delete">
                                                <i data-lucide="trash-2" style="width: 0.875rem; height: 0.875rem"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i
                                            data-lucide="award"
                                            class="mb-2"
                                            style="width: 3rem; height: 3rem; opacity: 0.4"
                                        ></i>
                                        <h6>No Discipline Performance Records Found</h6>
                                        <p class="small mb-3">Record daily student behavior & character ratings to view star trends.</p>
                                        <a
                                            href="{{ route('discipline.create') }}"
                                            class="btn btn-warning text-dark btn-sm"
                                        >
                                            <i
                                                data-lucide="star"
                                                style="width: 0.9rem; height: 0.9rem"
                                                class="star-filled"
                                            ></i>
                                            Record Performance Now
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($disciplines->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            Showing {{ $disciplines->firstItem() }} to {{ $disciplines->lastItem() }} of {{ $disciplines->total() }} entries
                        </span>
                        <div>{{ $disciplines->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterClass = document.getElementById('filterClassSelect');
            const filterStudent = document.getElementById('filterStudentSelect');
            const filterSession = document.getElementById('filterSessionSelect');

            if (filterClass && filterStudent) {
                filterClass.addEventListener('change', function () {
                    const cls = this.value;
                    const sessId = filterSession ? filterSession.value : '';

                    if (!cls) {
                        filterStudent.innerHTML = '<option value="">All Students</option>';
                        filterStudent.disabled = true;
                        this.form.submit();
                        return;
                    }

                    filterStudent.disabled = true;
                    filterStudent.innerHTML = '<option value="">Loading...</option>';

                    fetch(
                        `/discipline/get-students/${encodeURIComponent(cls)}?session_id=${encodeURIComponent(sessId)}`,
                    )
                        .then((r) => r.json())
                        .then((data) => {
                            let html = '<option value="">All Students</option>';
                            if (data.success && data.students.length) {
                                data.students.forEach((s) => {
                                    html += `<option value="${s.id}">${s.first_name} ${s.last_name || ''} (Roll: ${s.roll_no || 'N/A'})</option>`;
                                });
                            }
                            filterStudent.innerHTML = html;
                            filterStudent.disabled = false;
                            filterClass.form.submit();
                        });
                });
            }
        });
    </script>
@endpush
