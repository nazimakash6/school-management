@extends('layouts.app')

@section('title', 'Academic Planning Trash Archive')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectAll = document.getElementById('select-all-trash');
  const checkboxes = document.querySelectorAll('.record-checkbox');
  const restoreBtn = document.getElementById('bulkRestoreBtn');
  const deleteBtn = document.getElementById('bulkDeleteBtn');

  function updateBulkBtnState() {
    const checkedCount = document.querySelectorAll('.record-checkbox:checked').length;
    if (restoreBtn) restoreBtn.disabled = checkedCount === 0;
    if (deleteBtn) deleteBtn.disabled = checkedCount === 0;
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
  
  {{-- FLASH MESSAGES --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <div class="d-flex align-items-center gap-2">
        <i data-lucide="check-circle-2" style="width:1.25rem;height:1.25rem;"></i>
        <div>{{ session('success') }}</div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <div class="d-flex align-items-center gap-2">
        <i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;"></i>
        <div>{{ session('error') }}</div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- EXECUTIVE HEADER --}}
  <div class="content-header mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('academic-planning.index') }}" class="text-decoration-none text-muted">Academic Planning</a></li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Trash Archive</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 bg-secondary text-white shadow-sm d-inline-flex align-items-center justify-content-center">
          <i data-lucide="archive" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">Academic Planning Trash Archive</h3>
          <p class="text-muted mb-0 fs-7">Restore soft-deleted academic plans or remove them permanently</p>
        </div>
      </div>
    </div>
    <div class="content-header-actions d-flex gap-2 align-items-center">
      <a href="{{ route('academic-planning.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Active Plans
      </a>
    </div>
  </div>

  {{-- TRASHED TABLE CARD WITH BULK ACTION FORM --}}
  <form id="bulkActionForm" action="{{ route('academic-planning.bulk-action') }}" method="POST">
    @csrf
    
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <h6 class="mb-0 fw-bold text-dark me-2">Deleted Academic Plans</h6>
          <button type="submit" name="action" value="restore" id="bulkRestoreBtn" class="btn btn-outline-success btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" disabled>
            <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;"></i> Restore Selected
          </button>
          <button type="submit" name="action" value="force_delete" id="bulkDeleteBtn" class="btn btn-outline-danger btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" onclick="return confirm('PERMANENT ACTION: Are you sure you want to permanently delete selected plans?');" disabled>
            <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i> Delete Permanently
          </button>
        </div>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ count($plans) }} Record(s) in Trash</span>
      </div>
      
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 40px;" class="text-center">
                <input type="checkbox" id="select-all-trash" class="form-check-input cursor-pointer">
              </th>
              <th style="width: 40px;">#</th>
              <th>Plan Title</th>
              <th>Type</th>
              <th>Class / Subject</th>
              <th>Deleted At</th>
              <th class="text-center" style="width: 160px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($plans as $index => $plan)
              <tr>
                <td class="text-center">
                  <input type="checkbox" name="ids[]" value="{{ $plan->id }}" class="form-check-input record-checkbox cursor-pointer">
                </td>
                <td class="text-muted fs-7">{{ $plans->firstItem() + $index }}</td>
                <td>
                  <span class="fw-bold text-dark d-block">{{ $plan->title }}</span>
                  @if($plan->academic_session)
                    <span class="text-xs text-muted">Session: {{ $plan->academic_session }}</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill fs-8">
                    {{ $plan->plan_type_label }}
                  </span>
                </td>
                <td>
                  <div class="fs-7 text-dark fw-medium">{{ $plan->class_name ?? 'All Classes' }}</div>
                  <div class="text-xs text-muted">{{ $plan->subject_name ?? 'General' }}</div>
                </td>
                <td class="fs-7 text-muted">
                  {{ $plan->deleted_at ? $plan->deleted_at->format('d M Y h:i A') : 'N/A' }}
                </td>
                <td class="text-center">
                  <div class="d-flex align-items-center justify-content-center gap-1">
                    <form action="{{ route('academic-planning.restore', $plan->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-outline-success btn-sm px-2 py-1 fs-8 d-inline-flex align-items-center gap-1" title="Restore Plan">
                        <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;"></i> Restore
                      </button>
                    </form>
                    <form action="{{ route('academic-planning.force-delete', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('PERMANENT ACTION: Are you sure you want to permanently delete this academic plan?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1 fs-8 d-inline-flex align-items-center gap-1" title="Permanently Delete">
                        <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i> Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <i data-lucide="archive" style="width:2.5rem;height:2.5rem;" class="mb-2 text-muted opacity-50"></i>
                  <p class="mb-0 fw-semibold">Trash is empty</p>
                  <span class="fs-7">No deleted academic plans found.</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($plans->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top">
          {{ $plans->links() }}
        </div>
      @endif
    </div>
  </form>

</div>
@endsection
