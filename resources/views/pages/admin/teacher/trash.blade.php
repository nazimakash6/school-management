@extends('layouts.app')

@section('title', 'Teachers Trash')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admission.css') }}">
@endpush

@section('content')
    <div class="content-header">
        <div>
            <h1 class="page-title">Teachers Trash</h1>
            <p class="page-subtitle">Restore teacher records or permanently delete them</p>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('teacher.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Teachers
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="admission-listing">

        {{-- Stats --}}
        <div class="admission-stat-grid" data-admission-stats>
            <article class="card stat-card stat-card-total">
                <div class="card-body">
                    <p class="stat-label">Total in Trash</p>
                    <h3 class="stat-value">{{ $trashCount }}</h3>
                    <p class="stat-footnote">Trashed teacher records</p>
                </div>
            </article>
            <article class="card stat-card stat-card-active">
                <div class="card-body">
                    <p class="stat-label">Active Teachers</p>
                    <h3 class="stat-value">{{ $activeTeachers }}</h3>
                    <p class="stat-footnote">Currently active</p>
                </div>
            </article>
            <article class="card stat-card stat-card-month">
                <div class="card-body">
                    <p class="stat-label">Trashed Last 30 Days</p>
                    <h3 class="stat-value">{{ $trashedLast30Days }}</h3>
                    <p class="stat-footnote">Recently trashed</p>
                </div>
            </article>
        </div>

        {{-- Filters --}}
        <div class="filter-bar d-flex flex-wrap gap-2 align-items-end mt-3">
            <form method="GET" action="{{ route('teacher.trash') }}" class="d-flex flex-wrap gap-2 align-items-end w-100">
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
                    <a href="{{ route('teacher.trash') }}" class="btn btn-ghost btn-sm">Reset</a>
                </div>
            </form>
        </div>

        {{-- Bulk action form --}}
        <form id="teacherTrashBulkForm" action="{{ route('teacher.bulk-action') }}" method="POST"
            class="d-flex flex-wrap gap-2 align-items-end mt-3">
            @csrf
            <input type="hidden" name="bulk_action" id="teacherTrashBulkActionValue" value="restore">

            <button type="submit" class="btn btn-secondary btn-sm"
                onclick="return setTeacherTrashBulkAction('restore');">
                Restore Selected
            </button>
            <button type="submit" class="btn btn-danger btn-sm"
                onclick="return setTeacherTrashBulkAction('force_delete');">
                Permanent Delete Selected
            </button>
        </form>

        {{-- Table --}}
        <div class="table-container mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllTrashTeachers"></th>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Shift</th>
                        <th>Joining Date</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" value="{{ $teacher->id }}"
                                    class="trash-teacher-row-checkbox" form="teacherTrashBulkForm"></td>
                            <td>{{ $teacher->staff_id }}</td>
                            <td>{{ trim($teacher->first_name . ' ' . ($teacher->last_name ?? '')) }}</td>
                            <td>{{ $teacher->shift ? ucfirst($teacher->shift) : '—' }}</td>
                            <td>{{ $teacher->joining_date }}</td>
                            <td><span class="badge badge-success">{{ ucfirst($teacher->status) }}</span></td>
                            <td>{{ optional($teacher->deleted_at)->format('d M Y') }}</td>
                            <td class="actions d-flex gap-2">
                                <form method="POST" action="{{ route('teacher.restore', $teacher->id) }}"
                                    onsubmit="return confirm('Restore this teacher?');">
                                    @csrf
                                    <button class="btn btn-ghost btn-icon-sm" type="submit" title="Restore">
                                        <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('teacher.force-delete', $teacher->id) }}"
                                    onsubmit="return confirm('Permanently delete this teacher?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-ghost btn-icon-sm text-danger" type="submit"
                                        title="Permanent Delete">
                                        <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-tertiary py-4">No trashed teacher records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <nav class="mt-3 d-flex justify-content-between align-items-center">
            <span class="text-sm text-tertiary">
                @if ($teachers->total() > 0)
                    Showing {{ $teachers->firstItem() }} to {{ $teachers->lastItem() }} of {{ $teachers->total() }} trashed entries
                @else
                    Showing 0 trashed entries
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
    function setTeacherTrashBulkAction(action) {
        var field = document.getElementById('teacherTrashBulkActionValue');
        if (field) field.value = action;

        if (action === 'force_delete') {
            return confirm('Permanently delete selected teacher records?');
        }

        return confirm('Restore selected teacher records?');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var selectAll     = document.getElementById('selectAllTrashTeachers');
        var rowCheckboxes = document.querySelectorAll('.trash-teacher-row-checkbox');

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
