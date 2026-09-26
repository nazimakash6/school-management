@extends('layouts.app')

@section('title', 'Teachers')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admission.css') }}">
@endpush

@section('content')
    {{-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS --}}
    <div class="content-header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">HR & Faculty</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Teachers Directory</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="user-check" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Teachers & Academic Faculty</h3>
                    <p class="text-muted mb-0 fs-7">Track teachers, subject assignments, shifts, and faculty directory</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('teacher.trash') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="archive" style="width:1rem;height:1rem;"></i>
                Trash
                @if ($trashCount > 0)
                    <span class="badge bg-danger text-white rounded-pill ms-1 fs-7 px-2">{{ $trashCount }}</span>
                @endif
            </a>
            <a href="{{ route('staff.create') }}" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="plus" style="width:1rem;height:1rem;"></i> Add Teacher
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="admission-listing">
        <div class="admission-stat-grid" data-admission-stats>
            <article class="card stat-card stat-card-total">
                <div class="card-body">
                    <p class="stat-label">Total Teachers</p>
                    <h3 class="stat-value">{{ $totalCount }}</h3>
                    <p class="stat-footnote">All registered teachers</p>
                </div>
            </article>
            <article class="card stat-card stat-card-active">
                <div class="card-body">
                    <p class="stat-label">Active Teachers</p>
                    <h3 class="stat-value">{{ $activeCount }}</h3>
                    <p class="stat-footnote">Currently on duty</p>
                </div>
            </article>
            <article class="card stat-card stat-card-pending">
                <div class="card-body">
                    <p class="stat-label">Inactive Teachers</p>
                    <h3 class="stat-value">{{ $inactiveCount }}</h3>
                    <p class="stat-footnote">Not currently active</p>
                </div>
            </article>
            <article class="card stat-card stat-card-month">
                <div class="card-body">
                    <p class="stat-label">New This Month</p>
                    <h3 class="stat-value">{{ $newThisMonth }}</h3>
                    <p class="stat-footnote">Recent teacher additions</p>
                </div>
            </article>
        </div>

        <div class="filter-bar d-flex flex-wrap gap-2 align-items-end mt-3">
            <form method="GET" action="{{ route('teacher.index') }}" class="d-flex flex-wrap gap-2 align-items-end w-100">
                <div>
                    <label for="teacherSearch" class="text-sm text-tertiary mb-1 d-block">Search</label>
                    <input id="teacherSearch" name="search" type="text" class="form-control"
                        value="{{ $search }}"
                        placeholder="Search by name, email or ID">
                </div>

                <div>
                    <label for="teacherStatus" class="text-sm text-tertiary mb-1 d-block">Status</label>
                    <select id="teacherStatus" name="status" class="form-select">
                        <option value="all" @selected($status === 'all')>All Status</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div>
                    <label for="teacherShift" class="text-sm text-tertiary mb-1 d-block">Shift</label>
                    <select id="teacherShift" name="shift" class="form-select">
                        <option value="all" @selected($shift === 'all')>All Shifts</option>
                        <option value="morning" @selected($shift === 'morning')>Morning</option>
                        <option value="evening" @selected($shift === 'evening')>Evening</option>
                    </select>
                </div>

                <div>
                    <label for="teacherPerPage" class="text-sm text-tertiary mb-1 d-block">Show records</label>
                    <select id="teacherPerPage" name="per_page" class="form-select">
                        @foreach ([25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2 ms-auto">
                    <button class="btn btn-secondary btn-sm" type="submit">
                        <i data-lucide="filter" style="width:1rem;height:1rem;"></i> Apply
                    </button>
                    <a href="{{ route('teacher.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <div class="admission-filter-summary mt-2">
            Showing {{ $teachers->total() }} teacher record(s)
        </div>

        <div class="table-container mt-3">
            <form id="teacherBulkActionForm" action="{{ route('teacher.bulk-action') }}" method="POST"
                class="d-flex flex-wrap gap-2 align-items-end mb-3">
                @csrf
                <input type="hidden" name="bulk_action" value="trash">
                <button type="submit" class="btn btn-danger btn-sm"
                    onclick="return confirm('Move selected teachers to trash?');">
                    Bulk Trash
                </button>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllTeachers"></th>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Shift</th>
                        <th>Joining Date</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" value="{{ $teacher->id }}"
                                    class="teacher-row-checkbox" form="teacherBulkActionForm"></td>
                            <td>{{ $teacher->staff_id }}</td>
                            <td>{{ trim($teacher->first_name . ' ' . ($teacher->last_name ?? '')) }}</td>
                            <td>{{ $teacher->shift ? ucfirst($teacher->shift) : '—' }}</td>
                            <td>{{ $teacher->joining_date }}</td>
                            <td><span class="badge badge-success">{{ ucfirst($teacher->status) }}</span></td>
                            <td>{{ $teacher->mobile_no }}</td>
                            <td class="actions d-flex gap-2">
                                <a href="{{ route('staff.show', $teacher) }}" class="btn btn-ghost btn-icon-sm"
                                    title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a>
                                <a href="{{ route('staff.edit', $teacher) }}" class="btn btn-ghost btn-icon-sm"
                                    title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a>
                                <form method="POST" action="{{ route('teacher.destroy', $teacher->id) }}"
                                    onsubmit="return confirm('Move this teacher to trash?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-ghost btn-icon-sm text-danger" type="submit" title="Trash">
                                        <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-tertiary py-4">No teacher records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <nav class="mt-3 d-flex justify-content-between align-items-center">
            <span class="text-sm text-tertiary">
                @if ($teachers->total() > 0)
                    Showing {{ $teachers->firstItem() }} to {{ $teachers->lastItem() }} of {{ $teachers->total() }} entries
                @else
                    Showing 0 entries
                @endif
            </span>

            @if ($teachers->hasPages())
                <ul class="pagination mb-0">
                    <li class="page-item {{ $teachers->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $teachers->previousPageUrl() ?: '#' }}">
                            <i data-lucide="chevron-left" style="width:1rem;height:1rem;"></i>
                        </a>
                    </li>
                    @foreach ($teachers->getUrlRange(1, $teachers->lastPage()) as $page => $url)
                        <li class="page-item {{ $page === $teachers->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                    <li class="page-item {{ $teachers->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $teachers->nextPageUrl() ?: '#' }}">
                            <i data-lucide="chevron-right" style="width:1rem;height:1rem;"></i>
                        </a>
                    </li>
                </ul>
            @endif
        </nav>
    </section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var selectAll      = document.getElementById('selectAllTeachers');
        var rowCheckboxes  = document.querySelectorAll('.teacher-row-checkbox');

        if (!selectAll) return;

        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
        });

        rowCheckboxes.forEach(function (cb) {
            cb.addEventListener('change', function () {
                if (!cb.checked) { selectAll.checked = false; return; }
                selectAll.checked = Array.from(rowCheckboxes).every(function (c) { return c.checked; });
            });
        });
    });
</script>
@endpush

@endsection
