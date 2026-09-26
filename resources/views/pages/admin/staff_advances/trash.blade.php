@extends('layouts.app')

@section('title', 'Trashed Salary Advances')

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Trashed Salary Advances</h1>
      <p class="page-subtitle">View and restore deleted salary advance records</p>
    </div>
    <div>
      <a href="{{ route('staff-advances.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Advances
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <form id="staffAdvanceTrashBulkForm" action="{{ route('staff-advances.bulk-action') }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center mb-0">
        @csrf
        <select name="bulk_action" class="form-select form-select-sm" style="width: auto;" required>
          <option value="">Bulk Actions</option>
          <option value="restore">Restore Selected</option>
          <option value="force_delete">Permanently Delete Selected</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">
          Apply to Selected
        </button>
      </form>
      <span class="text-muted fs-7">Showing {{ $advances->total() }} trashed record(s)</span>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 40px;" class="ps-3">
              <input type="checkbox" id="selectAllTrashAdvances" class="form-check-input">
            </th>
            <th>Staff Member</th>
            <th>Advance Amount</th>
            <th>Repaid Amount</th>
            <th>Date Trashed</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($advances as $advance)
            <tr>
              <td class="ps-3">
                <input type="checkbox" name="selected_ids[]" value="{{ $advance->id }}" class="form-check-input advance-trash-checkbox" form="staffAdvanceTrashBulkForm">
              </td>
              <td>
                <div class="fw-semibold text-dark">{{ $advance->staff ? $advance->staff->full_name : 'N/A' }}</div>
                <small class="text-muted">{{ $advance->staff ? ($advance->staff->staff_id ?? 'STF-'.$advance->staff->id) : '' }}</small>
              </td>
              <td class="fw-bold">Rs. {{ number_format($advance->advance_amount, 2) }}</td>
              <td class="text-success">Rs. {{ number_format($advance->repaid_amount, 2) }}</td>
              <td class="text-muted">{{ $advance->deleted_at ? $advance->deleted_at->format('M d, Y H:i') : '' }}</td>
              <td class="text-end">
                <form action="{{ route('staff-advances.restore', $advance->id) }}" method="POST" class="d-inline-block">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-success me-1">
                    <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Restore
                  </button>
                </form>
                <form action="{{ route('staff-advances.force-delete', $advance->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Permanently delete this advance record? This action cannot be undone.')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Delete Permanently
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">
                <i data-lucide="trash-2" class="mb-2" style="width:2rem;height:2rem;"></i>
                <p class="mb-0">No trashed advance records found.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($advances->hasPages())
      <div class="card-footer bg-white py-2 border-0">
        {{ $advances->links() }}
      </div>
    @endif
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var selectAll = document.getElementById('selectAllTrashAdvances');
        var rowCheckboxes = document.querySelectorAll('.advance-trash-checkbox');
        var bulkForm = document.getElementById('staffAdvanceTrashBulkForm');

        if (selectAll) {
          selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(function (cb) {
              cb.checked = selectAll.checked;
            });
          });
        }

        rowCheckboxes.forEach(function (cb) {
          cb.addEventListener('change', function () {
            if (!cb.checked) {
              if (selectAll) selectAll.checked = false;
              return;
            }
            if (selectAll) {
              selectAll.checked = Array.from(rowCheckboxes).every(function (c) { return c.checked; });
            }
          });
        });

        if (bulkForm) {
          bulkForm.addEventListener('submit', function (e) {
            var actionSelect = bulkForm.querySelector('select[name="bulk_action"]');
            if (!actionSelect || !actionSelect.value) {
              alert('Please select a bulk action from the dropdown.');
              e.preventDefault();
              return false;
            }

            var checkedCount = document.querySelectorAll('.advance-trash-checkbox:checked').length;
            if (checkedCount === 0) {
              alert('Please select at least one trashed salary advance record to perform this action.');
              e.preventDefault();
              return false;
            }

            var actionName = actionSelect.value === 'restore' ? 'restore' : 'permanently delete';
            if (!confirm('Are you sure you want to ' + actionName + ' ' + checkedCount + ' selected record(s)?')) {
              e.preventDefault();
              return false;
            }
          });
        }
      });
    </script>
  @endpush
@endsection
