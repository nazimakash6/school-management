@extends('layouts.app')

@section('title', 'Classes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/classes.css') }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectAll = document.getElementById('select-all');
  const checkboxes = document.querySelectorAll('.record-checkbox');
  const bulkBtn = document.getElementById('bulkTrashBtn');

  function updateBulkBtnState() {
    const checkedCount = document.querySelectorAll('.record-checkbox:checked').length;
    if (bulkBtn) {
      bulkBtn.disabled = checkedCount === 0;
    }
  }

  if (selectAll) {
    selectAll.addEventListener('change', function () {
      checkboxes.forEach(cb => cb.checked = selectAll.checked);
      updateBulkBtnState();
    });
  }

  checkboxes.forEach(cb => {
    cb.addEventListener('change', function () {
      if (selectAll) {
        selectAll.checked = (checkboxes.length > 0 && document.querySelectorAll('.record-checkbox:checked').length === checkboxes.length);
      }
      updateBulkBtnState();
    });
  });

  updateBulkBtnState();
});
</script>
@endpush

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

  {{-- EXECUTIVE HEADER --}}
  <div class="content-header mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item text-muted">Academic Setup</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Classes</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
          <i data-lucide="layout-grid" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">Classes & Sections Management</h3>
          <p class="text-muted mb-0 fs-7">Configure academic grade levels, student capacities, and class rosters</p>
        </div>
      </div>
    </div>
    <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center">
      <a href="{{ route('classes.trash') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
        <i data-lucide="archive" style="width:1rem;height:1rem;"></i> Trash
        @if(isset($trashCount) && $trashCount > 0)
          <span class="badge bg-danger text-white rounded-pill ms-1 fs-8 px-2">{{ $trashCount }}</span>
        @endif
      </a>
      <a href="{{ route('classes.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
        <i data-lucide="plus" style="width:1rem;height:1rem;"></i> Add Class
      </a>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <div class="d-flex align-items-center gap-2">
        <i data-lucide="check-circle-2" style="width:1.25rem;height:1.25rem;"></i>
        <div>{{ session('status') }}</div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <div class="d-flex align-items-center gap-2">
        <i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;"></i>
        <div>{{ session('error') }}</div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- FILTER BAR --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('classes.index') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
          <label for="search" class="form-label text-xs font-semibold text-uppercase text-muted mb-1">Search Keyword</label>
          <input type="text" id="search" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Search class name, level, status...">
        </div>

        <div class="col-md-3">
          <label for="level" class="form-label text-xs font-semibold text-uppercase text-muted mb-1">Grade Level</label>
          <select class="form-select form-select-sm" name="level" id="level">
            <option value="">All Levels</option>
            @foreach ($levels as $levelOption)
              <option value="{{ $levelOption }}" @selected($level === $levelOption)>{{ $levelOption }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label for="perPage" class="form-label text-xs font-semibold text-uppercase text-muted mb-1">Records Per Page</label>
          <select class="form-select form-select-sm" name="per_page" id="perPage">
            @foreach ([25, 50, 100] as $size)
              <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3 d-flex gap-2 ms-auto">
          <button class="btn btn-primary btn-sm px-3 flex-grow-1" type="submit">
            <i data-lucide="filter" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Apply Filter
          </button>
          <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
        </div>
      </form>
    </div>
  </div>

  {{-- CLASSES DATA TABLE WITH BULK TRASH FORM --}}
  <form id="bulkTrashForm" action="{{ route('classes.bulk-trash') }}" method="POST">
    @csrf
    @method('DELETE')
    
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
          <h6 class="mb-0 fw-bold text-dark">Active Class Directory</h6>
          <button type="submit" id="bulkTrashBtn" class="btn btn-outline-danger btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" onclick="return confirm('Are you sure you want to move selected classes to trash?');" disabled>
            <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i> Move Selected to Trash
          </button>
        </div>
        <span class="badge bg-light text-dark border">
          @if ($studentClasses->total() > 0)
            Showing {{ $studentClasses->firstItem() }} - {{ $studentClasses->lastItem() }} of {{ $studentClasses->total() }} entries
          @else
            0 entries
          @endif
        </span>
      </div>

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 40px;" class="text-center">
                <input type="checkbox" id="select-all" class="form-check-input cursor-pointer">
              </th>
              <th>Class Name</th>
              <th>Level</th>
              <th>Group</th>
              <th>Sections</th>
              <th>Students</th>
              <th class="text-center" style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($studentClasses as $studentClass)
              <tr>
                <td class="text-center">
                  <input type="checkbox" name="ids[]" value="{{ $studentClass->id }}" class="form-check-input record-checkbox cursor-pointer">
                </td>
                <td class="fw-bold text-dark">{{ $studentClass->name }}</td>
                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-8">{{ $studentClass->level }}</span></td>
                <td>{{ $studentClass->group ?: 'N/A' }}</td>
                <td><span class="badge bg-light text-dark border px-2 py-1 fs-8">{{ $studentClass->section_count }} Section(s)</span></td>
                <td class="fw-medium text-dark">{{ $studentClass->students_count }} Student(s)</td>
                <td class="text-center">
                  <div class="d-flex align-items-center justify-content-center gap-1">
                    <a href="{{ route('classes.show', $studentClass) }}" class="btn btn-ghost btn-icon-sm text-primary" title="View Class Details">
                      <i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i>
                    </a>
                    <a href="{{ route('classes.edit', $studentClass) }}" class="btn btn-ghost btn-icon-sm text-secondary" title="Edit Class">
                      <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <i data-lucide="layout-grid" style="width:2.5rem;height:2.5rem;" class="mb-2 text-muted opacity-50"></i>
                  <p class="mb-0 fw-semibold">No classes found</p>
                  <span class="fs-7">Create your first class or adjust filter criteria.</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($studentClasses->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top">
          {{ $studentClasses->links() }}
        </div>
      @endif
    </div>
  </form>

</div>
@endsection
