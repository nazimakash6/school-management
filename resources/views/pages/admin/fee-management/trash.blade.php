@extends('layouts.app')

@section('title', 'Fee Management Trash')

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
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Fee Invoices Trash</h1>
      <p class="page-subtitle">Manage soft-deleted student fee invoices and vouchers</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('fee-management.index') }}" class="btn btn-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Active Invoices
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <form id="bulkActionForm" action="{{ route('fee-management.bulk-action') }}" method="POST">
    @csrf
    
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <h6 class="mb-0 fw-bold text-dark me-2">Deleted Fee Invoices</h6>
          <button type="submit" name="action" value="restore" id="bulkRestoreBtn" class="btn btn-outline-success btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-medium" disabled>
            <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;"></i> Restore Selected
          </button>
          <button type="submit" name="action" value="force_delete" id="bulkDeleteBtn" class="btn btn-outline-danger btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-medium" onclick="return confirm('PERMANENT ACTION: Are you sure you want to permanently delete selected fee invoices?');" disabled>
            <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i> Delete Permanently
          </button>
        </div>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ method_exists($invoices, 'total') ? $invoices->total() : count($invoices) }} Record(s) in Trash</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 40px;" class="text-center">
                <input type="checkbox" id="select-all-trash" class="form-check-input cursor-pointer">
              </th>
              <th>Invoice No</th>
              <th>Student</th>
              <th>Fee Type & Month</th>
              <th>Net Amount</th>
              <th>Deleted At</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $inv)
              <tr>
                <td class="text-center">
                  <input type="checkbox" name="ids[]" value="{{ $inv->id }}" class="form-check-input record-checkbox cursor-pointer">
                </td>
                <td>
                  <span class="fw-bold text-secondary">{{ $inv->invoice_no }}</span>
                </td>
                <td>
                  <div class="fw-semibold text-dark">
                    {{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'N/A' }}
                  </div>
                  <small class="text-muted">{{ $inv->admission ? $inv->admission->admission_no : '' }}</small>
                </td>
                <td>
                  <span class="badge bg-light text-dark border text-capitalize me-1">{{ str_replace('_', ' ', $inv->fee_type) }}</span>
                  <small class="text-muted d-block">{{ $inv->fee_month }}</small>
                </td>
                <td class="fw-semibold">
                  Rs. {{ number_format($inv->net_amount, 2) }}
                </td>
                <td class="text-muted">
                  {{ $inv->deleted_at ? $inv->deleted_at->format('M d, Y H:i') : '' }}
                </td>
                <td class="text-end text-nowrap">
                  <button type="submit" form="restore-form-{{ $inv->id }}" class="btn btn-sm btn-outline-success me-1" title="Restore Invoice">
                    <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Restore
                  </button>
                  <button type="submit" form="delete-form-{{ $inv->id }}" class="btn btn-sm btn-outline-danger" title="Permanently Delete">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Delete Permanently
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  <i data-lucide="info" class="mb-2" style="width:2rem;height:2rem;"></i>
                  <p class="mb-0">Trash is currently empty.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($invoices, 'hasPages') && $invoices->hasPages())
        <div class="card-footer bg-white py-2 border-0">
          {{ $invoices->links() }}
        </div>
      @endif
    </div>
  </form>

  {{-- Individual Forms outside main bulk form to avoid nested forms --}}
  @foreach($invoices as $inv)
    <form id="restore-form-{{ $inv->id }}" action="{{ route('fee-management.restore', $inv->id) }}" method="POST" style="display:none;">
      @csrf
    </form>
    <form id="delete-form-{{ $inv->id }}" action="{{ route('fee-management.force-delete', $inv->id) }}" method="POST" style="display:none;" onsubmit="return confirm('Permanently delete this fee invoice?')">
      @csrf
      @method('DELETE')
    </form>
  @endforeach

@endsection
