@extends('layouts.app')

@section('title', 'Payroll Trash')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/payroll.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admission.css') }}">
@endpush

@section('content')
  <div class="content-header">
    <div>
      <h1 class="page-title">Payroll Trash</h1>
      <p class="page-subtitle">View and restore soft-deleted payroll records</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Payrolls
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
    <div class="filter-bar d-flex flex-wrap gap-2 align-items-end mt-3">
      <form method="GET" action="{{ route('payroll.trash') }}" class="d-flex flex-wrap gap-2 align-items-end w-100">
        <div>
          <label for="payrollSearch" class="text-sm text-tertiary mb-1 d-block">Search</label>
          <input type="text" id="payrollSearch" name="search" class="form-control" value="{{ $search }}"
            placeholder="Search by staff name or ID">
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
          <a href="{{ route('payroll.trash') }}" class="btn btn-ghost btn-sm">Reset</a>
        </div>
      </form>
    </div>

    <div class="table-container mt-3">
      <form id="payrollTrashBulkForm" action="{{ route('payroll.bulk-action') }}" method="POST"
        class="d-flex flex-wrap gap-2 align-items-center mb-3">
        @csrf
        <select name="bulk_action" class="form-select form-select-sm" style="width: auto;" required>
          <option value="">Bulk Actions</option>
          <option value="restore">Restore Selected</option>
          <option value="force_delete">Permanently Delete Selected</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Execute selected bulk action?');">Apply</button>
      </form>

      <table class="table align-middle">
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAllTrashPayrolls"></th>
            <th>Staff Details</th>
            <th>Month</th>
            <th>Net Salary</th>
            <th>Status</th>
            <th>Deleted At</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($payrolls as $payroll)
            <tr>
              <td>
                <input type="checkbox" name="selected_ids[]" value="{{ $payroll->id }}"
                  class="payroll-trash-checkbox" form="payrollTrashBulkForm">
              </td>
              <td>
                @if ($payroll->staff)
                  <div class="fw-bold">{{ $payroll->staff->full_name }}</div>
                  <div class="text-xs text-tertiary">ID: {{ $payroll->staff->staff_id }}</div>
                @else
                  <span class="text-muted">Unknown Staff</span>
                @endif
              </td>
              <td><span class="badge bg-light text-dark font-monospace">{{ $payroll->payroll_month }}</span></td>
              <td><span class="fw-bold {{ $payroll->net_salary < 0 ? 'text-danger' : '' }}">{{ $payroll->formatted_net_salary }}</span></td>
              <td><span class="badge badge-secondary">{{ ucfirst($payroll->status) }}</span></td>
              <td>{{ $payroll->deleted_at ? $payroll->deleted_at->format('d M, Y H:i') : '—' }}</td>
              <td class="actions text-end">
                <form method="POST" action="{{ route('payroll.restore', $payroll->id) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-ghost btn-icon-sm text-success" type="submit" title="Restore">
                    <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;"></i>
                  </button>
                </form>

                <form method="POST" action="{{ route('payroll.force-delete', $payroll->id) }}" class="d-inline"
                  onsubmit="return confirm('Permanently delete this payroll entry? This cannot be undone.');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-ghost btn-icon-sm text-danger" type="submit" title="Delete Permanently">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-tertiary py-4">No trashed payroll records found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

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

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var selectAll = document.getElementById('selectAllTrashPayrolls');
        var rowCheckboxes = document.querySelectorAll('.payroll-trash-checkbox');

        if (!selectAll) return;

        selectAll.addEventListener('change', function () {
          rowCheckboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
        });

        rowCheckboxes.forEach(function (cb) {
          cb.addEventListener('change', function () {
            if (!cb.checked) { selectAll.checked = false; return; }
            selectAll.checked = Array.from(rowCheckboxes).every(function (c) { return c.checked; });
          });
        });
      });
    </script>
  @endpush
@endsection
