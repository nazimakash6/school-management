@extends('layouts.app')

@section('title', '')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/staff.css') }}">
@endpush

@section('content')



  {{-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS --}}
  <div class="content-header">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item text-muted">HR & Finance</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Staff Directory</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
          <i data-lucide="briefcase" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">Staff Management & Directory</h3>
          <p class="text-muted mb-0 fs-7">Manage non-teaching staff, recruitment, departments & records</p>
        </div>
      </div>
    </div>
    <div class="content-header-actions d-flex gap-2">
      <a href="{{ route('staff.export', request()->query()) }}" class="btn btn-outline-success btn-sm" title="Export CSV"><i data-lucide="download" style="width:1rem;height:1rem;"></i> Export CSV</a>
      <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#importStaffModal" title="Import CSV"><i data-lucide="upload" style="width:1rem;height:1rem;"></i> Import CSV</button>
      <a href="{{ route('staff.trash') }}" class="btn btn-outline-secondary btn-sm"><i data-lucide="archive"
          style="width:1rem;height:1rem;"></i> Trash</a>
      <a href="{{ route('staff.create') }}" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;"><i data-lucide="plus"
          style="width:1rem;height:1rem;"></i> Add Staff Member</a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <!-- Top Metric Cards -->
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Staff</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalStaff }}</h3>
          </div>
          <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="users" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Active Staff</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ $activeStaff }}</h3>
          </div>
          <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="user-check" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Inactive Staff</span>
            <h3 class="fw-bold text-warning mb-0 mt-1">{{ $inactiveStaff }}</h3>
          </div>
          <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="user-x" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Monthly Salary Budget</span>
            <h3 class="fw-bold text-info mb-0 mt-1">Rs. {{ number_format($totalPayroll, 0) }}</h3>
          </div>
          <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="banknote" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="filter-bar d-flex flex-wrap gap-2 align-items-end">
    <form method="GET" action="{{ route('staff.index') }}" class="d-flex flex-wrap gap-2 align-items-end w-100">
      <div>
        <label for="staffSearch" class="text-sm text-tertiary mb-1 d-block">Search</label>
        <input type="text" id="staffSearch" name="search" class="form-control" value="{{ $search }}"
          placeholder="Search by ID, name, department, designation">
      </div>

      <div>
        <label for="staffDepartment" class="text-sm text-tertiary mb-1 d-block">Department</label>
        <select class="form-select" name="department" id="staffDepartment">
          <option value="all" @selected($department === 'all')>All Departments</option>
          @foreach ($departments as $departmentOption)
            <option value="{{ $departmentOption }}" @selected($department === $departmentOption)>
              {{ strtolower($departmentOption) === 'it' ? 'IT' : ucwords(str_replace(['_', '-'], ' ', $departmentOption)) }}
            </option>
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
        <button class="btn btn-secondary btn-sm" type="submit"><i data-lucide="filter"
            style="width:1rem;height:1rem;"></i> Apply</button>
        <a href="{{ route('staff.index') }}" class="btn btn-ghost btn-sm">Reset</a>
      </div>
    </form>
  </div>
  <div class="table-container">
    <form id="staffBulkActionForm" action="{{ route('staff.bulk-action') }}" method="POST"
      class="d-flex flex-wrap gap-2 align-items-end mb-3">
      @csrf
      <input type="hidden" name="bulk_action" value="trash">
      <button type="submit" class="btn btn-danger btn-sm"
        onclick="return confirm('Move selected staff records to trash?');">Bulk Trash</button>
    </form>

    <table class="table">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAllStaff"></th>
          <th>Staff ID</th>
          <th>Name</th>
          <th>Department</th>
          <th>Designation</th>
          <th>Join Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($staffMembers as $staff)
          <tr>
            <td><input type="checkbox" name="selected_ids[]" value="{{ $staff->id }}" class="staff-row-checkbox"
                form="staffBulkActionForm"></td>
            <td>{{ $staff->staff_id }}</td>
            <td>{{ trim($staff->first_name . ' ' . ($staff->last_name ?? '')) }}</td>
            <td>{{ $staff->formatted_department }}</td>
            <td>{{ $staff->formatted_designation }}</td>
            <td>{{ $staff->joining_date }}</td>
            <td><span class="badge badge-success">{{ ucfirst($staff->status) }}</span></td>
            <td class="actions d-flex gap-2">
              <a href="{{ route('staff.show', $staff) }}" class="btn btn-ghost btn-icon-sm" title="View"><i
                  data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a>
              <a href="{{ route('staff.edit', $staff) }}" class="btn btn-ghost btn-icon-sm" title="Edit"><i
                  data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a>
              <form method="POST" action="{{ route('staff.destroy', $staff) }}"
                onsubmit="return confirm('Move this staff record to trash?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-ghost btn-icon-sm text-danger" type="submit" title="Delete"><i data-lucide="trash-2"
                    style="width:0.875rem;height:0.875rem;"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-tertiary py-4">No staff records found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <nav class="mt-3 d-flex justify-content-between align-items-center">
    <span class="text-sm text-tertiary">
      @if ($staffMembers->total() > 0)
        Showing {{ $staffMembers->firstItem() }} to {{ $staffMembers->lastItem() }} of {{ $staffMembers->total() }} entries
      @else
        Showing 0 entries
      @endif
    </span>
    @if ($staffMembers->hasPages())
      <ul class="pagination mb-0">
        <li class="page-item {{ $staffMembers->onFirstPage() ? 'disabled' : '' }}"><a class="page-link"
            href="{{ $staffMembers->previousPageUrl() ?: '#' }}"><i data-lucide="chevron-left"
              style="width:1rem;height:1rem;"></i></a></li>
        @foreach ($staffMembers->getUrlRange(1, $staffMembers->lastPage()) as $page => $url)
          <li class="page-item {{ $page === $staffMembers->currentPage() ? 'active' : '' }}"><a class="page-link"
              href="{{ $url }}">{{ $page }}</a></li>
        @endforeach
        <li class="page-item {{ $staffMembers->hasMorePages() ? '' : 'disabled' }}"><a class="page-link"
            href="{{ $staffMembers->nextPageUrl() ?: '#' }}"><i data-lucide="chevron-right"
              style="width:1rem;height:1rem;"></i></a></li>
      </ul>
    @endif
  </nav>

  <!-- Import Staff Modal -->
  <div class="modal fade" id="importStaffModal" tabindex="-1" aria-labelledby="importStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('staff.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="importStaffModalLabel">Import Staff Members</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-sm text-tertiary mb-3">Upload a CSV file containing staff records to import them into the system.</p>
            
            <div class="mb-3 p-3 bg-light rounded border text-center">
              <span class="d-block text-sm fw-medium mb-1">Need the correct CSV format?</span>
              <a href="{{ route('staff.sample-csv') }}" class="btn btn-sm btn-outline-primary">
                <i data-lucide="file-spreadsheet" style="width:1rem;height:1rem;" class="me-1"></i> Download Sample CSV Template
              </a>
            </div>

            <div class="mb-3">
              <label for="importFile" class="form-label fw-medium">Select CSV File <span class="text-danger">*</span></label>
              <input type="file" name="import_file" id="importFile" class="form-control" accept=".csv, text/csv, application/csv, text/comma-separated-values" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm">
              <i data-lucide="upload" style="width:1rem;height:1rem;" class="me-1"></i> Import Records
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var selectAll = document.getElementById('selectAllStaff');
        var rowCheckboxes = document.querySelectorAll('.staff-row-checkbox');

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