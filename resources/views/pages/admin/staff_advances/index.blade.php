@extends('layouts.app')

@section('title', 'Salary Advance Management')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/payroll.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admission.css') }}">
  <style>
    /* Prevent parent containers from expanding beyond page viewport */
    .admission-listing,
    .tab-content,
    .tab-pane,
    .card {
      max-width: 100% !important;
      min-width: 0 !important;
    }

    .card {
      overflow: hidden !important;
    }

    /* Scrollable Table Container - Enforces internal X and Y scrollbars */
    .table-responsive,
    .custom-table-scroll,
    .table-container {
      display: block !important;
      width: 100% !important;
      max-width: 100% !important;
      min-width: 0 !important;
      max-height: 520px !important;
      overflow-x: auto !important;
      overflow-y: auto !important;
      -webkit-overflow-scrolling: touch;
      border-radius: 0 0 0.5rem 0.5rem;
      border-top: 1px solid #cbd5e1;
      scrollbar-width: auto;
      scrollbar-color: #475569 #e2e8f0;
    }

    .table-responsive::-webkit-scrollbar,
    .custom-table-scroll::-webkit-scrollbar,
    .table-container::-webkit-scrollbar {
      width: 10px !important;
      height: 12px !important;
    }

    .table-responsive::-webkit-scrollbar-track,
    .custom-table-scroll::-webkit-scrollbar-track,
    .table-container::-webkit-scrollbar-track {
      background: #e2e8f0 !important;
      border-radius: 6px !important;
    }

    .table-responsive::-webkit-scrollbar-thumb,
    .custom-table-scroll::-webkit-scrollbar-thumb,
    .table-container::-webkit-scrollbar-thumb {
      background: #475569 !important;
      border-radius: 6px !important;
      border: 2px solid #e2e8f0 !important;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover,
    .custom-table-scroll::-webkit-scrollbar-thumb:hover,
    .table-container::-webkit-scrollbar-thumb:hover {
      background: #1e293b !important;
    }

    .table-responsive table,
    .custom-table-scroll table,
    .table-container table {
      width: 100% !important;
      min-width: 1250px !important;
      margin-bottom: 0 !important;
      table-layout: auto !important;
    }

    .table-responsive table thead th,
    .custom-table-scroll table thead th,
    .table-container table thead th {
      position: sticky !important;
      top: 0 !important;
      background-color: #f1f5f9 !important;
      z-index: 10 !important;
      box-shadow: 0 1px 0 #cbd5e1;
      white-space: nowrap !important;
    }

    .table-responsive th,
    .table-responsive td,
    .table-responsive th *,
    .table-responsive td *,
    .custom-table-scroll th,
    .custom-table-scroll td,
    .custom-table-scroll th *,
    .custom-table-scroll td *,
    .table-container th,
    .table-container td,
    .table-container th *,
    .table-container td * {
      white-space: nowrap !important;
    }

    .table-responsive [data-lucide],
    .table-responsive svg.lucide,
    .custom-table-scroll [data-lucide],
    .custom-table-scroll svg.lucide,
    .table-container [data-lucide],
    .table-container svg.lucide {
      display: inline-block !important;
      vertical-align: middle !important;
      flex-shrink: 0 !important;
    }
  </style>
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
    <div class="admission-stat-grid mb-4" data-admission-stats>
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

    <!-- Nav Tabs for Advances vs Repayments -->
    <ul class="nav nav-tabs nav-tabs-bordered mb-4 border-bottom fw-bold" id="staffAdvanceTabs" role="tablist" style="font-size: 0.95rem;">
      <li class="nav-item" role="presentation">
        <button class="nav-link text-dark d-inline-flex align-items-center gap-2 {{ $activeTab === 'repayments' ? '' : 'active' }}" 
                id="advances-list-tab" data-bs-toggle="tab" data-bs-target="#advances-list-pane" type="button" role="tab" aria-controls="advances-list-pane" aria-selected="{{ $activeTab === 'repayments' ? 'false' : 'true' }}">
          <i data-lucide="hand-coins" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
          Salary Advances History
          <span class="badge bg-primary-subtle text-primary border rounded-pill fs-7 ms-1 px-2.5 py-0.5">{{ $advances->total() }}</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-dark d-inline-flex align-items-center gap-2 {{ $activeTab === 'repayments' ? 'active' : '' }}" 
                id="repayments-list-tab" data-bs-toggle="tab" data-bs-target="#repayments-list-pane" type="button" role="tab" aria-controls="repayments-list-pane" aria-selected="{{ $activeTab === 'repayments' ? 'true' : 'false' }}">
          <i data-lucide="receipt" style="width:1.1rem;height:1.1rem;" class="text-success"></i>
          Record Repayment Details
          <span class="badge bg-success-subtle text-success border rounded-pill fs-7 ms-1 px-2.5 py-0.5">{{ $repayments->total() }}</span>
        </button>
      </li>
    </ul>

    <div class="tab-content" id="staffAdvanceTabsContent">
      {{-- TAB 1: SALARY ADVANCES HISTORY --}}
      <div class="tab-pane fade {{ $activeTab === 'repayments' ? '' : 'show active' }}" id="advances-list-pane" role="tabpanel" aria-labelledby="advances-list-tab">
        <!-- Filters Bar for Advances -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-body py-3">
            <form method="GET" action="{{ route('staff-advances.index') }}" class="row g-2 align-items-center">
              <input type="hidden" name="tab" value="advances">
              <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm"
                  placeholder="Search staff name or ID..." value="{{ request('tab') !== 'repayments' ? request('search') : '' }}">
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
            <table class="table table-hover align-middle mb-0 text-nowrap">
              <thead class="table-light">
                <tr>
                  <th style="width:45px; min-width:45px;" class="ps-3">
                    <input type="checkbox" id="selectAllAdvances" class="form-check-input">
                  </th>
                  <th style="min-width:250px;">Staff Member</th>
                  <th style="min-width:150px;">Advance Date</th>
                  <th style="min-width:160px;">Advance Amount</th>
                  <th style="min-width:160px;">Remaining</th>
                  <th style="min-width:150px;">Status</th>
                  <th style="min-width:200px;" class="text-end pe-3">Actions</th>
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
                      {{-- Repay Button --}}
                      @if($advance->remaining_balance > 0)
                        <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal"
                          data-bs-target="#repayModal{{ $advance->id }}" title="Record Repayment Entry">
                          <i data-lucide="banknote" style="width:0.875rem;height:0.875rem;"></i>
                        </button>
                      @endif
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
                      {{-- Single Trash --}}
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
              {{ $advances->appends(['tab' => 'advances'])->links() }}
            </div>
          @endif
        </div><!-- /card -->
      </div>

      {{-- TAB 2: RECORD REPAYMENT DETAILS --}}
      <div class="tab-pane fade {{ $activeTab === 'repayments' ? 'show active' : '' }}" id="repayments-list-pane" role="tabpanel" aria-labelledby="repayments-list-tab">
        <!-- Filters Bar for Repayments -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-body py-3">
            <form method="GET" action="{{ route('staff-advances.index') }}" class="row g-2 align-items-center">
              <input type="hidden" name="tab" value="repayments">
              <div class="col-md-9">
                <input type="text" name="search" class="form-control form-control-sm"
                  placeholder="Search staff name or ID in repayments..." value="{{ request('tab') === 'repayments' ? request('search') : '' }}">
              </div>
              <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm w-100">
                  <i data-lucide="search" style="width:0.875rem;height:0.875rem;"></i> Filter Repayments
                </button>
                <a href="{{ route('staff-advances.index', ['tab' => 'repayments']) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
              </div>
            </form>
          </div>
        </div>

        <!-- Repayments Table Card -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
              <i data-lucide="receipt" class="text-success" style="width:1.1rem;height:1.1rem;"></i>
              Repayment Transactions History
            </h6>
            <span class="text-muted fs-7">Showing {{ $repayments->total() }} repayment entry(s)</span>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
              <thead class="table-light">
                <tr>
                  <th style="min-width:140px;" class="ps-3">Voucher #</th>
                  <th style="min-width:250px;">Staff Member</th>
                  <th style="min-width:150px;">Repayment Date</th>
                  <th style="min-width:220px;">Type / Source</th>
                  <th style="min-width:160px;" class="text-end">Repaid Amount</th>
                  <th style="min-width:260px;" class="pe-3">Remarks / Notes</th>
                  <th style="min-width:140px;" class="text-end pe-3">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($repayments as $repay)
                  <tr>
                    <td class="ps-3 fw-bold text-dark">#RPY-{{ sprintf('%05d', $repay->id) }}</td>
                    <td>
                      <div class="fw-semibold text-dark">{{ $repay->staff ? $repay->staff->full_name : 'N/A' }}</div>
                      <small class="text-muted">
                        {{ $repay->staff ? ($repay->staff->staff_id ?? 'STF-' . $repay->staff->id) : '' }}
                        &bull; {{ $repay->staff ? $repay->staff->formatted_designation : '' }}
                      </small>
                    </td>
                    <td>{{ $repay->repayment_date ? $repay->repayment_date->format('M d, Y') : 'N/A' }}</td>
                    <td>
                      @if($repay->repayment_type === 'payroll_deduction')
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                          🏦 Payroll Auto Deduction @if($repay->payroll) ({{ $repay->payroll->payroll_month }}) @endif
                        </span>
                      @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle text-capitalize">
                          💵 {{ str_replace('_', ' ', $repay->repayment_type) }}
                        </span>
                      @endif
                    </td>
                    <td class="text-end fw-bold text-success fs-6">Rs. {{ number_format($repay->amount, 2) }}</td>
                    <td class="text-muted small pe-3">{{ $repay->notes ?? 'N/A' }}</td>
                    <td class="text-end pe-3">
                      @if($repay->staff)
                        <a href="{{ route('staff-advances.create', ['staff_id' => $repay->staff_id]) }}" class="btn btn-xs btn-outline-primary" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">
                          <i data-lucide="eye" style="width:0.8rem;height:0.8rem;" class="me-1"></i> Advances
                        </a>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                      <i data-lucide="receipt" class="mb-2 text-muted" style="width:2rem;height:2rem;"></i>
                      <p class="mb-0">No repayment entries recorded yet.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if($repayments->hasPages())
            <div class="card-footer bg-white py-2 border-0">
              {{ $repayments->appends(['tab' => 'repayments'])->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

  {{-- Repayment Modals --}}
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
                <div class="mb-3 p-2.5 bg-light rounded border fs-7">
                  <div class="d-flex justify-content-between text-muted">
                    <span>Total Advance:</span>
                    <span class="fw-bold text-dark">Rs. {{ number_format($advance->advance_amount, 2) }}</span>
                  </div>
                  <div class="d-flex justify-content-between text-muted">
                    <span>Already Repaid:</span>
                    <span class="fw-bold text-success">Rs. {{ number_format($advance->repaid_amount, 2) }}</span>
                  </div>
                  <div class="d-flex justify-content-between fs-7 mt-1 border-top pt-1">
                    <span class="fw-bold">Outstanding Balance:</span>
                    <span class="fw-bold text-danger">Rs. {{ number_format($advance->remaining_balance, 2) }}</span>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold">Repayment Amount (Rs.) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="1" max="{{ $advance->remaining_balance }}"
                    name="repayment_amount" class="form-control"
                    value="{{ $advance->remaining_balance }}" required>
                </div>

                <div class="row g-2 mb-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Repayment Date <span class="text-danger">*</span></label>
                    <input type="date" name="repayment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select" required>
                      <option value="cash">Cash</option>
                      <option value="bank_transfer">Bank Transfer</option>
                      <option value="cheque">Cheque</option>
                    </select>
                  </div>
                </div>

                <div class="mb-2">
                  <label class="form-label fw-semibold">Notes / Remarks</label>
                  <input type="text" name="notes" class="form-control" placeholder="e.g. Cash repayment received from staff">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success btn-sm">Save Repayment Entry</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif
  @endforeach

  @push('scripts')
    <script>
      function renderLucideIcons() {
        if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
          lucide.createIcons(lucide.icons ? { icons: lucide.icons } : undefined);
        } else if (window.lucide && typeof window.lucide.createIcons === 'function') {
          window.lucide.createIcons(window.lucide.icons ? { icons: window.lucide.icons } : undefined);
        }
      }

      document.addEventListener('DOMContentLoaded', function () {
        renderLucideIcons();
        setTimeout(renderLucideIcons, 100);
        setTimeout(renderLucideIcons, 300);
        setTimeout(renderLucideIcons, 800);

        var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(function (tabEl) {
          tabEl.addEventListener('shown.bs.tab', function () {
            renderLucideIcons();
          });
        });

        var selectAll   = document.getElementById('selectAllAdvances');
        var checkboxes  = document.querySelectorAll('.advance-row-checkbox');
        var bulkForm    = document.getElementById('staffAdvanceBulkActionForm');

        if (selectAll) {
          selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
          });
        }

        checkboxes.forEach(function (cb) {
          cb.addEventListener('change', function () {
            if (selectAll) {
              selectAll.checked = Array.from(checkboxes).every(function (c) { return c.checked; });
            }
          });
        });

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