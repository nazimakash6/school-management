@extends('layouts.app')

@section('title', 'Payroll Management')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/payroll.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admission.css') }}">
@endpush

@section('content')
  <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS BELOW HEADING -->
  <div class="content-header mb-4 flex-column align-items-start gap-3">
    <div class="w-100">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item text-muted">HR & Finance</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Staff Payroll</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 bg-primary text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
          <i data-lucide="banknote" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">Staff Payroll & Salary Management</h3>
          <p class="text-muted mb-0 fs-7">Manage monthly salaries, allowances, deductions, payslips & audit logs</p>
        </div>
      </div>
    </div>
    <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center pt-2 border-top w-100">
      <a href="{{ route('payroll.statement') }}" class="btn btn-outline-success btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
        <i data-lucide="file-spreadsheet" style="width:1rem;height:1rem;"></i> Transaction Statement
      </a>
      <a href="{{ route('payroll.trash') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
        <i data-lucide="archive" style="width:1rem;height:1rem;"></i> Trash
        @if ($trashCount > 0)
          <span class="badge bg-danger text-white rounded-pill ms-1 fs-7 px-2">{{ $trashCount }}</span>
        @endif
      </a>
      <a href="{{ route('payroll.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold ms-auto" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
        <i data-lucide="plus" style="width:1rem;height:1rem;"></i> Generate Payroll
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <section class="admission-listing">
    <!-- Stats Row -->
    <div class="admission-stat-grid" data-admission-stats>
      <article class="card stat-card stat-card-active">
        <div class="card-body">
          <p class="stat-label">Total Disbursed (Paid)</p>
          <h3 class="stat-value text-success">Rs. {{ number_format($totalDisbursed, 2) }}</h3>
          <p class="stat-footnote">{{ $paidCount }} payrolls paid</p>
        </div>
      </article>
      <article class="card stat-card stat-card-pending">
        <div class="card-body">
          <p class="stat-label">Pending Payrolls</p>
          <h3 class="stat-value text-warning">Rs. {{ number_format($totalPending, 2) }}</h3>
          <p class="stat-footnote">{{ $pendingCount }} payrolls pending</p>
        </div>
      </article>
      <article class="card stat-card stat-card-total">
        <div class="card-body">
          <p class="stat-label">Paid Payroll Count</p>
          <h3 class="stat-value">{{ $paidCount }}</h3>
          <p class="stat-footnote">Disbursed entries</p>
        </div>
      </article>
      <article class="card stat-card stat-card-month">
        <div class="card-body">
          <p class="stat-label">Pending Payroll Count</p>
          <h3 class="stat-value">{{ $pendingCount }}</h3>
          <p class="stat-footnote">Awaiting payment</p>
        </div>
      </article>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar d-flex flex-wrap gap-2 align-items-end mt-3">
      <form method="GET" action="{{ route('payroll.index') }}" class="d-flex flex-wrap gap-2 align-items-end w-100">
        <div>
          <label for="payrollSearch" class="text-sm text-tertiary mb-1 d-block">Search Staff</label>
          <input type="text" id="payrollSearch" name="search" class="form-control" value="{{ $search }}"
            placeholder="Search by ID, name, email">
        </div>

        <div>
          <label for="payrollMonth" class="text-sm text-tertiary mb-1 d-block">Month</label>
          <select class="form-select" name="month" id="payrollMonth">
            <option value="all" @selected($month === 'all')>All Months</option>
            @foreach ($months as $m)
              <option value="{{ $m }}" @selected($month === $m)>{{ $m }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label for="payrollDept" class="text-sm text-tertiary mb-1 d-block">Department</label>
          <select class="form-select" name="department" id="payrollDept">
            <option value="all" @selected($department === 'all')>All Departments</option>
            @foreach ($departments as $dept)
              <option value="{{ $dept }}" @selected($department === $dept)>{{ ucwords(str_replace(['_', '-'], ' ', $dept)) }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label for="payrollStatus" class="text-sm text-tertiary mb-1 d-block">Status</label>
          <select class="form-select" name="status" id="payrollStatus">
            <option value="all" @selected($status === 'all')>All Status</option>
            <option value="paid" @selected($status === 'paid')>Paid</option>
            <option value="pending" @selected($status === 'pending')>Pending</option>
            <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
          </select>
        </div>

        <div>
          <label for="payrollPerPage" class="text-sm text-tertiary mb-1 d-block">Show</label>
          <select class="form-select" name="per_page" id="payrollPerPage">
            @foreach ([25, 50, 100] as $size)
              <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
            @endforeach
          </select>
        </div>

        <div class="d-flex gap-2 ms-auto">
          <button class="btn btn-secondary btn-sm" type="submit">
            <i data-lucide="filter" style="width:1rem;height:1rem;"></i> Apply Filter
          </button>
          <a href="{{ route('payroll.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </div>
      </form>
    </div>

    <div class="admission-filter-summary mt-2">
      Showing {{ $payrolls->total() }} payroll record(s)
    </div>

    <!-- Table Container -->
    <div class="table-container mt-3">
      <form id="payrollBulkActionForm" action="{{ route('payroll.bulk-action') }}" method="POST"
        class="d-flex flex-wrap gap-2 align-items-center mb-3">
        @csrf
        <select name="bulk_action" class="form-select form-select-sm" style="width: auto;" required>
          <option value="">Bulk Actions</option>
          <option value="mark_paid">Mark as Paid</option>
          <option value="trash">Move to Trash</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Apply to Selected</button>
      </form>

      <table class="table align-middle">
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAllPayrolls"></th>
            <th>Staff Details</th>
            <th>Month</th>
            <th>Basic Salary</th>
            <th>Allowances</th>
            <th>Deductions</th>
            <th>Net Salary</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($payrolls as $payroll)
            <tr>
              <td>
                <input type="checkbox" name="selected_ids[]" value="{{ $payroll->id }}"
                  class="payroll-row-checkbox" form="payrollBulkActionForm">
              </td>
              <td>
                @if ($payroll->staff)
                  <div class="fw-bold">{{ $payroll->staff->full_name }}</div>
                  <div class="text-xs text-tertiary">
                    ID: {{ $payroll->staff->staff_id }} • {{ $payroll->staff->formatted_department }}
                  </div>
                @else
                  <span class="text-muted">Unknown Staff</span>
                @endif
              </td>
              <td><span class="badge bg-light text-dark font-monospace">{{ $payroll->payroll_month }}</span></td>
              <td>Rs. {{ number_format($payroll->basic_salary, 2) }}</td>
              <td class="text-success">+ Rs. {{ number_format($payroll->allowance, 2) }}</td>
              <td class="text-danger">- Rs. {{ number_format($payroll->deduction, 2) }}</td>
              <td><span class="fw-bold {{ $payroll->net_salary < 0 ? 'text-danger' : 'text-primary' }}">{{ $payroll->formatted_net_salary }}</span></td>
              <td>
                @if ($payroll->status === 'paid')
                  <span class="badge badge-success">Paid</span>
                @elseif ($payroll->status === 'pending')
                  <span class="badge badge-warning">Pending</span>
                @else
                  <span class="badge badge-danger">Cancelled</span>
                @endif
              </td>
              <td>{{ $payroll->payment_date ? $payroll->payment_date->format('d M, Y') : '—' }}</td>
              <td class="actions text-end">
                <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-ghost btn-icon-sm" title="View Payslip">
                  <i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-ghost btn-icon-sm" title="Edit">
                  <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                <form method="POST" action="{{ route('payroll.destroy', $payroll) }}" class="d-inline"
                  onsubmit="return confirm('Move this payroll entry to trash?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-ghost btn-icon-sm text-danger" type="submit" title="Trash">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center text-tertiary py-4">No payroll records found matching your filters.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <nav class="mt-3 d-flex justify-content-between align-items-center">
      <span class="text-sm text-tertiary">
        @if ($payrolls->total() > 0)
          Showing {{ $payrolls->firstItem() }} to {{ $payrolls->lastItem() }} of {{ $payrolls->total() }} entries
        @else
          Showing 0 entries
        @endif
      </span>

      @if ($payrolls->hasPages())
        <ul class="pagination mb-0">
          <li class="page-item {{ $payrolls->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $payrolls->previousPageUrl() ?: '#' }}">
              <i data-lucide="chevron-left" style="width:1rem;height:1rem;"></i>
            </a>
          </li>
          @foreach ($payrolls->getUrlRange(1, $payrolls->lastPage()) as $page => $url)
            <li class="page-item {{ $page === $payrolls->currentPage() ? 'active' : '' }}">
              <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
          @endforeach
          <li class="page-item {{ $payrolls->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $payrolls->nextPageUrl() ?: '#' }}">
              <i data-lucide="chevron-right" style="width:1rem;height:1rem;"></i>
            </a>
          </li>
        </ul>
      @endif
    </nav>
  </section>

  <!-- Generate Monthly Payroll Modal -->
  <div class="modal fade" id="generateMonthlyPayrollModal" tabindex="-1" aria-labelledby="generateMonthlyPayrollModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('payroll.generate-monthly') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="generateMonthlyPayrollModalLabel">Batch Process Monthly Payroll</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-sm text-tertiary mb-3">
              This will automatically create pending payroll entries for all active staff members for the selected month using their default basic salary.
            </p>
            <div class="mb-3">
              <label for="targetMonth" class="form-label fw-medium">Target Payroll Month <span class="text-danger">*</span></label>
              <input type="month" name="target_month" id="targetMonth" class="form-control" value="{{ date('Y-m') }}" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm">
              <i data-lucide="play" style="width:1rem;height:1rem;" class="me-1"></i> Process Batch Payroll
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var selectAll = document.getElementById('selectAllPayrolls');
        var rowCheckboxes = document.querySelectorAll('.payroll-row-checkbox');

        if (!selectAll) return;

        selectAll.addEventListener('change', function () {
          rowCheckboxes.forEach(function (cb) {
            cb.checked = selectAll.checked;
          });
        });

        rowCheckboxes.forEach(function (cb) {
          cb.addEventListener('change', function () {
            if (!cb.checked) {
              selectAll.checked = false;
              return;
            }
            selectAll.checked = Array.from(rowCheckboxes).every(function (c) { return c.checked; });
          });
        });
      });
    </script>
  @endpush
@endsection
