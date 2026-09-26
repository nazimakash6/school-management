@extends('layouts.app')

@section('title', 'Salary Advance Management')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/payroll.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admission.css') }}">
@endpush

@section('content')
  <div class="content-header">
    <div>
      <h1 class="page-title">Salary Advances</h1>
      <p class="page-subtitle">Manage staff advance requests, repayment schedules, and payroll recovery</p>
    </div>
    <div class="content-header-actions d-flex gap-2">
      <a href="{{ route('staff-advances.trash') }}" class="btn btn-secondary btn-sm">
        <i data-lucide="archive" style="width:1rem;height:1rem;" class="me-1"></i> Trash
        @if(($trashCount ?? 0) > 0)
          <span class="badge bg-danger text-white rounded-pill ms-1 fs-7 px-2">{{ $trashCount }}</span>
        @endif
      </a>
      <a href="{{ route('staff-advances.create') }}" class="btn btn-primary btn-sm">
        <i data-lucide="plus" style="width:1rem;height:1rem;" class="me-1"></i> Issue Salary Advance
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

  <section class="admission-listing">
    <!-- Stat Cards -->
    <div class="admission-stat-grid" data-admission-stats>
      <article class="card stat-card stat-card-total">
        <div class="card-body">
          <p class="stat-label">Total Advanced Amount</p>
          <h3 class="stat-value text-primary">Rs. {{ number_format($totalAdvanced, 2) }}</h3>
          <p class="stat-footnote">Issued to staff</p>
        </div>
      </article>
      <article class="card stat-card stat-card-active">
        <div class="card-body">
          <p class="stat-label">Total Repaid Amount</p>
          <h3 class="stat-value text-success">Rs. {{ number_format($totalRepaid, 2) }}</h3>
          <p class="stat-footnote">Recovered so far</p>
        </div>
      </article>
      <article class="card stat-card stat-card-pending">
        <div class="card-body">
          <p class="stat-label">Outstanding Balance</p>
          <h3 class="stat-value text-danger">Rs. {{ number_format($outstandingBalance, 2) }}</h3>
          <p class="stat-footnote">Remaining to collect</p>
        </div>
      </article>
      <article class="card stat-card">
        <div class="card-body">
          <p class="stat-label">Active Advances</p>
          <h3 class="stat-value">{{ $activeCount }}</h3>
          <p class="stat-footnote">Ongoing installments</p>
        </div>
      </article>
    </div>

    <!-- Filters Bar -->
    <div class="card mb-4 border-0 shadow-sm">
      <div class="card-body py-3">
        <form method="GET" action="{{ route('staff-advances.index') }}" class="row g-2 align-items-center">
          <div class="col-md-6">
            <input type="text" name="search" class="form-control form-control-sm"
              placeholder="Search staff name or ID..." value="{{ request('search') }}">
          </div>
          <div class="col-md-4">
            <select name="status" class="form-select form-select-sm">
              <option value="">All Statuses</option>
              <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved (Active)</option>
              <option value="fully_repaid" {{ request('status') == 'fully_repaid' ? 'selected' : '' }}>Fully Repaid</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
          </div>
          <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i data-lucide="search" style="width:0.875rem;height:0.875rem;"></i> Filter
            </button>
            <a href="{{ route('staff-advances.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Advances Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <form id="staffAdvanceBulkActionForm" action="{{ route('staff-advances.bulk-action') }}" method="POST"
          class="d-flex flex-wrap gap-2 align-items-center mb-0">
          @csrf
          <select name="bulk_action" class="form-select form-select-sm" style="width:auto;">
            <option value="">Bulk Actions</option>
            <option value="trash">Move to Trash</option>
          </select>
          <button type="submit" class="btn btn-secondary btn-sm">Apply to Selected</button>
        </form>
        <span class="text-muted fs-7">Showing {{ $advances->total() }} record(s)</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:40px;" class="ps-3">
                <input type="checkbox" id="selectAllAdvances" class="form-check-input">
              </th>
              <th>Staff Member</th>
              <th>Advance Date</th>
              <th>Advance Amount</th>
              <th>Remaining</th>
              <th>Status</th>
              <th class="text-end pe-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($advances as $advance)
              <tr>
                <td class="ps-3">
                  <input type="checkbox" name="selected_ids[]" value="{{ $advance->id }}"
                    class="form-check-input advance-row-checkbox" form="staffAdvanceBulkActionForm">
                </td>
                <td>
                  <div class="fw-semibold text-dark">{{ $advance->staff ? $advance->staff->full_name : 'N/A' }}</div>
                  <small class="text-muted">
                    {{ $advance->staff ? ($advance->staff->staff_id ?? 'STF-' . $advance->staff->id) : '' }}
                    &bull; {{ $advance->staff ? $advance->staff->formatted_designation : '' }}
                  </small>
                </td>
                <td>{{ $advance->advance_date ? $advance->advance_date->format('M d, Y') : 'N/A' }}</td>
                <td class="fw-bold text-dark">Rs. {{ number_format($advance->advance_amount, 2) }}</td>
                <td class="fw-bold text-danger">Rs. {{ number_format($advance->remaining_balance, 2) }}</td>
                <td>
                  <span class="badge {{ $advance->status_badge_class }} border text-capitalize">
                    {{ str_replace('_', ' ', is_object($advance->status) ? $advance->status->value : $advance->status) }}
                  </span>
                </td>
                <td class="text-end text-nowrap pe-3">
                  {{-- View --}}
                  <a href="{{ route('staff-advances.show', $advance->id) }}"
                    class="btn btn-sm btn-outline-info me-1" title="View Details">
                    <i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i>
                  </a>
                  {{-- Edit --}}
                  <a href="{{ route('staff-advances.edit', $advance->id) }}"
                    class="btn btn-sm btn-outline-warning me-1" title="Edit Advance">
                    <i data-lucide="edit-2" style="width:0.875rem;height:0.875rem;"></i>
                  </a>
                  {{-- Single Trash — standalone form, NOT nested inside any other form --}}
                  <form action="{{ route('staff-advances.destroy', $advance->id) }}" method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Move this salary advance record to trash?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Move to Trash">
                      <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i data-lucide="info" class="mb-2" style="width:2rem;height:2rem;"></i>
                  <p class="mb-0">No staff salary advance records found.</p>
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
    </div><!-- /card -->
  </section>

  {{-- =====================================================================
       Repayment Modals — placed OUTSIDE the table so browsers don't break
       the form hierarchy (HTML forbids nested <form> elements).
       ===================================================================== --}}
  @foreach($advances as $advance)
    @if($advance->remaining_balance > 0)
      <div class="modal fade text-start" id="repayModal{{ $advance->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form action="{{ route('staff-advances.repayment', $advance->id) }}" method="POST">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold">
                  Record Repayment &mdash; {{ $advance->staff ? $advance->staff->full_name : '' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3 p-2 bg-light rounded border">
                  <div class="d-flex justify-content-between text-muted fs-7">
                    <span>Total Advance:</span>
                    <span class="fw-semibold text-dark">Rs. {{ number_format($advance->advance_amount, 2) }}</span>
                  </div>
                  <div class="d-flex justify-content-between text-muted fs-7">
                    <span>Already Repaid:</span>
                    <span class="fw-semibold text-success">Rs. {{ number_format($advance->repaid_amount, 2) }}</span>
                  </div>
                  <div class="d-flex justify-content-between fs-7 mt-1 border-top pt-1">
                    <span class="fw-bold">Outstanding Balance:</span>
                    <span class="fw-bold text-danger">Rs. {{ number_format($advance->remaining_balance, 2) }}</span>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-medium">Repayment Amount (Rs.)</label>
                  <input type="number" step="0.01" min="1" max="{{ $advance->remaining_balance }}"
                    name="repayment_amount" class="form-control"
                    value="{{ $advance->remaining_balance }}" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success btn-sm">Save Repayment</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif
  @endforeach

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var selectAll   = document.getElementById('selectAllAdvances');
        var checkboxes  = document.querySelectorAll('.advance-row-checkbox');
        var bulkForm    = document.getElementById('staffAdvanceBulkActionForm');

        // Select-All toggle
        if (selectAll) {
          selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
          });
        }

        // Keep Select-All in sync
        checkboxes.forEach(function (cb) {
          cb.addEventListener('change', function () {
            if (selectAll) {
              selectAll.checked = Array.from(checkboxes).every(function (c) { return c.checked; });
            }
          });
        });

        // Bulk form guard
        if (bulkForm) {
          bulkForm.addEventListener('submit', function (e) {
            var actionVal    = (bulkForm.querySelector('select[name="bulk_action"]') || {}).value;
            var checkedCount = document.querySelectorAll('.advance-row-checkbox:checked').length;

            if (!actionVal) {
              alert('Please choose a bulk action from the dropdown.');
              e.preventDefault(); return false;
            }
            if (checkedCount === 0) {
              alert('Please select at least one record.');
              e.preventDefault(); return false;
            }
            if (!confirm('Move ' + checkedCount + ' record(s) to trash?')) {
              e.preventDefault(); return false;
            }
          });
        }
      });
    </script>
  @endpush
@endsection