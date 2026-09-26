@extends('layouts.app')

@section('title', 'Staff Trash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/staff.css') }}">
@endpush

@section('content')

<div class="content-header">
    <div>
        <h1 class="page-title">Staff Trash</h1>
        <p class="page-subtitle">Restore staff records or permanently delete them from trash</p>
    </div>
    <div class="content-header-actions d-flex gap-2">
        <a href="{{ route('staff.index') }}" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Staff</a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="filter-bar d-flex flex-wrap gap-2 align-items-end">
    <form method="GET" action="{{ route('staff.trash') }}" class="d-flex flex-wrap gap-2 align-items-end w-100">
        <div>
            <label for="staffSearch" class="text-sm text-tertiary mb-1 d-block">Search</label>
            <input type="text" id="staffSearch" name="search" class="form-control" value="{{ $search }}" placeholder="Search by ID, name, department, designation">
        </div>

        <div>
            <label for="staffDepartment" class="text-sm text-tertiary mb-1 d-block">Department</label>
            <select class="form-select" name="department" id="staffDepartment">
                <option value="all" @selected($department === 'all')>All Departments</option>
                @foreach ($departments as $departmentOption)
                <option value="{{ $departmentOption }}" @selected($department === $departmentOption)>{{ strtolower($departmentOption) === 'it' ? 'IT' : ucwords(str_replace(['_', '-'], ' ', $departmentOption)) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="staffStatus" class="text-sm text-tertiary mb-1 d-block">Status</label>
            <select class="form-select" name="status" id="staffStatus">
                <option value="all" @selected($status === 'all')>All Status</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </select>
        </div>

        <div>
            <label for="staffPerPage" class="text-sm text-tertiary mb-1 d-block">Show records</label>
            <select class="form-select" name="per_page" id="staffPerPage">
                @foreach ([25, 50, 100] as $size)
                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 ms-auto">
            <button class="btn btn-secondary btn-sm" type="submit"><i data-lucide="filter" style="width:1rem;height:1rem;"></i> Apply</button>
            <a href="{{ route('staff.trash') }}" class="btn btn-ghost btn-sm">Reset</a>
        </div>
    </form>
</div>

<form id="staffTrashBulkActionForm" action="{{ route('staff.bulk-action') }}" method="POST" class="d-flex flex-wrap gap-2 align-items-end mt-3">
    @csrf
    <input type="hidden" name="bulk_action" id="staffTrashBulkActionValue" value="restore">

    <button type="submit" class="btn btn-secondary btn-sm" onclick="return setStaffTrashBulkAction('restore');">Restore Selected</button>
    <button type="submit" class="btn btn-danger btn-sm" onclick="return setStaffTrashBulkAction('force_delete');">Permanent Delete Selected</button>
</form>

<div class="table-container mt-3">
    <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAllTrashStaff"></th>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Joining Date</th>
                <th>Status</th>
                <th>Deleted At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($staffMembers as $staff)
            <tr>
                <td><input type="checkbox" name="selected_ids[]" value="{{ $staff->id }}" class="trash-staff-row-checkbox" form="staffTrashBulkActionForm"></td>
                <td>{{ $staff->staff_id }}</td>
                <td>{{ trim($staff->first_name . ' ' . ($staff->last_name ?? '')) }}</td>
                <td>{{ $staff->formatted_department }}</td>
                <td>{{ $staff->formatted_designation }}</td>
                <td>{{ $staff->joining_date }}</td>
                <td><span class="badge badge-success">{{ ucfirst($staff->status) }}</span></td>
                <td>{{ optional($staff->deleted_at)->format('d M Y') }}</td>
                <td class="actions d-flex gap-2">
                    <form method="POST" action="{{ route('staff.restore', $staff->id) }}" onsubmit="return confirm('Restore this staff record?');">
                        @csrf
                        <button class="btn btn-ghost btn-icon-sm" type="submit" title="Restore"><i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;"></i></button>
                    </form>
                    <form method="POST" action="{{ route('staff.force-delete', $staff->id) }}" onsubmit="return confirm('Permanently delete this staff record?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-ghost btn-icon-sm text-danger" type="submit" title="Permanent Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-tertiary py-4">No trashed staff records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<nav class="mt-3 d-flex justify-content-between align-items-center">
    <span class="text-sm text-tertiary">
        @if ($staffMembers->total() > 0)
        Showing {{ $staffMembers->firstItem() }} to {{ $staffMembers->lastItem() }} of {{ $staffMembers->total() }} trashed staff entries
        @else
        Showing 0 trashed staff entries
        @endif
    </span>

    @if ($staffMembers->hasPages())
    <ul class="pagination mb-0">
        <li class="page-item {{ $staffMembers->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $staffMembers->previousPageUrl() ?: '#' }}" @if($staffMembers->onFirstPage()) aria-disabled="true" @endif><i data-lucide="chevron-left" style="width:1rem;height:1rem;"></i></a>
        </li>

        @foreach ($staffMembers->getUrlRange(1, $staffMembers->lastPage()) as $page => $url)
        <li class="page-item {{ $page === $staffMembers->currentPage() ? 'active' : '' }}">
            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
        @endforeach

        <li class="page-item {{ $staffMembers->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $staffMembers->nextPageUrl() ?: '#' }}" @if(!$staffMembers->hasMorePages()) aria-disabled="true" @endif><i data-lucide="chevron-right" style="width:1rem;height:1rem;"></i></a>
        </li>
    </ul>
    @endif
</nav>

@push('scripts')
<script>
    function setStaffTrashBulkAction(action) {
        var field = document.getElementById('staffTrashBulkActionValue');
        if (field) field.value = action;

        if (action === 'force_delete') {
            return confirm('Permanently delete selected staff records?');
        }

        return confirm('Restore selected staff records?');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var selectAll = document.getElementById('selectAllTrashStaff');
        var rowCheckboxes = document.querySelectorAll('.trash-staff-row-checkbox');

        if (!selectAll) return;

        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });
        });

        rowCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                if (!checkbox.checked) {
                    selectAll.checked = false;
                    return;
                }

                var allChecked = Array.from(rowCheckboxes).every(function (item) {
                    return item.checked;
                });

                selectAll.checked = allChecked;
            });
        });
    });
</script>
@endpush

@endsection
