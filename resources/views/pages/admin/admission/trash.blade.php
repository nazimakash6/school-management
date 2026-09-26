@extends('layouts.app')

@section('title', 'Admission Trash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admission.css') }}">
@endpush

@section('content')

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admission.index') }}" class="text-decoration-none text-muted">Students</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Admission Trash</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                    <i data-lucide="trash-2" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Admission Trash & Archives</h3>
                    <p class="text-muted mb-0 fs-7">Restore deleted records or permanently delete admissions from system trash</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('admission.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Admissions
            </a>
        </div>
    </div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<section class="admission-listing">
    <div class="admission-stat-grid" data-admission-stats>
        <article class="card stat-card stat-card-total">
            <div class="card-body">
                <p class="stat-label">Trash Admissions</p>
                <h3 class="stat-value">{{ $trashCount }}</h3>
                <p class="stat-footnote">Currently in trash</p>
            </div>
        </article>
        <article class="card stat-card stat-card-active">
            <div class="card-body">
                <p class="stat-label">Active Admissions</p>
                <h3 class="stat-value">{{ $activeCount }}</h3>
                <p class="stat-footnote">Available in main list</p>
            </div>
        </article>
        <article class="card stat-card stat-card-pending">
            <div class="card-body">
                <p class="stat-label">Trashed Last 30 Days</p>
                <h3 class="stat-value">{{ $trashedLast30Days }}</h3>
                <p class="stat-footnote">Recent removals</p>
            </div>
        </article>
        <article class="card stat-card stat-card-month">
            <div class="card-body">
                <p class="stat-label">Filtered Results</p>
                <h3 class="stat-value">{{ $admissions->total() }}</h3>
                <p class="stat-footnote">Based on current filters</p>
            </div>
        </article>
    </div>

    <div class="filter-bar d-flex flex-wrap gap-2 align-items-end mt-3">
        <form method="GET" action="{{ route('admission.trash') }}" class="d-flex flex-wrap gap-2 align-items-end w-100">
            <div>
                <label for="admissionSearch" class="text-sm text-tertiary mb-1 d-block">Search</label>
                <input id="admissionSearch" name="search" type="text" class="form-control" placeholder="Search by student, guardian, class, or admission no" value="{{ $search }}">
            </div>

            <div>
                <label for="admissionStatus" class="text-sm text-tertiary mb-1 d-block">Status</label>
                <select id="admissionStatus" name="status" class="form-select">
                    <option value="all" @selected($status === 'all')>All Status</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
            </div>

            <div>
                <label for="admissionPerPage" class="text-sm text-tertiary mb-1 d-block">Show records</label>
                <select id="admissionPerPage" name="per_page" class="form-select">
                    <option value="25" @selected($perPage === 25)>25</option>
                    <option value="50" @selected($perPage === 50)>50</option>
                    <option value="100" @selected($perPage === 100)>100</option>
                </select>
            </div>

            <div class="d-flex gap-2 ms-auto">
                <button class="btn btn-secondary btn-sm" type="submit"><i data-lucide="filter" style="width:1rem;height:1rem;"></i> Apply</button>
                <a href="{{ route('admission.trash') }}" class="btn btn-ghost btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <form id="admissionTrashBulkActionForm" action="{{ route('admission.bulk-action') }}" method="POST" class="d-flex flex-wrap gap-2 align-items-end mt-2">
        @csrf
        <input type="hidden" name="bulk_action" id="trashBulkActionValue" value="restore">

        <button type="submit" class="btn btn-secondary btn-sm" onclick="return setTrashBulkAction('restore');">Restore Selected</button>
        <button type="submit" class="btn btn-danger btn-sm" onclick="return setTrashBulkAction('force_delete');">Permanent Delete Selected</button>
    </form>

    <div class="table-container mt-3">
        <table class="table">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="selectAllTrashAdmissions">
                    </th>
                    <th>Admission No</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Guardian</th>
                    <th>Admission Date</th>
                    <th>Status</th>
                    <th>Contact</th>
                    <th>City</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admissions as $admission)
                    <tr>
                        <td>
                            <input type="checkbox" name="selected_ids[]" value="{{ $admission->id }}" class="trash-admission-row-checkbox" form="admissionTrashBulkActionForm">
                        </td>
                        <td>{{ $admission->admission_no }}</td>
                        <td>{{ $admission->student_name }}</td>
                        <td>{{ $admission->class_name }}</td>
                        <td>{{ $admission->guardian_name }}</td>
                        <td>{{ optional($admission->admission_date)->format('d M Y') }}</td>
                        <td>
                            <span class="status-badge {{ $admission->status }}">{{ ucfirst($admission->status) }}</span>
                        </td>
                        <td>{{ $admission->contact_no ?: 'N/A' }}</td>
                        <td>{{ $admission->city ?: 'N/A' }}</td>
                        <td class="actions d-flex gap-2">
                            <form action="{{ route('admission.restore', $admission->id) }}" method="POST" onsubmit="return confirm('Restore this admission?');">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-icon-sm" title="Restore"><i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;"></i></button>
                            </form>
                            <form action="{{ route('admission.force-delete', $admission->id) }}" method="POST" onsubmit="return confirm('Permanently delete this admission?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-icon-sm text-danger" title="Permanent Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="admission-empty-state">No trashed admissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admission-pagination">
        <p class="pagination-info">
            Showing {{ $admissions->firstItem() ?? 0 }} to {{ $admissions->lastItem() ?? 0 }} of {{ $admissions->total() }} entries
        </p>

        @if ($admissions->hasPages())
            <ul class="pagination mb-0">
                <li class="page-item {{ $admissions->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $admissions->previousPageUrl() ?: '#' }}" @if($admissions->onFirstPage()) aria-disabled="true" @endif><i data-lucide="chevron-left" style="width:1rem;height:1rem;"></i></a>
                </li>

                @foreach ($admissions->getUrlRange(1, $admissions->lastPage()) as $page => $url)
                    <li class="page-item {{ $page === $admissions->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                <li class="page-item {{ $admissions->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $admissions->nextPageUrl() ?: '#' }}" @if(!$admissions->hasMorePages()) aria-disabled="true" @endif><i data-lucide="chevron-right" style="width:1rem;height:1rem;"></i></a>
                </li>
            </ul>
        @endif
    </div>
</section>

@push('scripts')
<script>
    function setTrashBulkAction(action) {
        var field = document.getElementById('trashBulkActionValue');
        if (field) field.value = action;

        if (action === 'force_delete') {
            return confirm('Permanently delete selected admissions?');
        }

        return confirm('Restore selected admissions?');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var selectAll = document.getElementById('selectAllTrashAdmissions');
        var rowCheckboxes = document.querySelectorAll('.trash-admission-row-checkbox');

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
