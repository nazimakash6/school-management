@extends('layouts.app')

@section('title', 'Payroll Transaction Statement')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/payroll-detail.css') }}?v={{ time() }}">
  <style>
    @media print {
      @page {
        size: A4 portrait;
        margin: 6mm 10mm;
      }

      html, body,
      .app-wrapper,
      .main-content,
      .page-content {
        background: #ffffff !important;
        color: #000000 !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 8pt !important;
        line-height: 1.2 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        position: static !important;
        overflow: visible !important;
        display: block !important;
        box-shadow: none !important;
        border: none !important;
      }

      .sidebar,
      .sidebar-overlay,
      .sidebar-brand,
      .notification-panel,
      .notification-overlay,
      header, footer, nav,
      .app-header, .app-sidebar, .topbar, .navbar,
      .content-header,
      .no-print,
      .btn, .breadcrumb,
      .alert,
      .screen-statement-wrapper {
        display: none !important;
        height: 0 !important;
        width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
      }

      .print-a4-statement,
      .print-a4-statement * {
        visibility: visible !important;
      }

      .print-a4-statement {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 5mm 6mm !important;
        background: #fdfaf3 !important;
        border: 3px double #3d1a06 !important;
        color: #3d1a06 !important;
        position: static !important;
        font-family: 'Times New Roman', 'Georgia', serif !important;
      }

      .statement-print-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 8px !important;
      }

      .statement-print-table th,
      .statement-print-table td {
        border: 1px solid #c7ad8d !important;
        padding: 4px 6px !important;
        font-size: 8pt !important;
        vertical-align: middle !important;
        color: #3d1a06 !important;
      }

      .statement-print-table th {
        background-color: #3d1a06 !important;
        color: #fdfaf3 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
      }

      .statement-print-table tr:nth-child(even) {
        background-color: #f7f0e4 !important;
      }
    }
  </style>
@endpush

@section('content')
  <div class="admission-page-wrapper py-4 px-3 px-md-4 no-print screen-statement-wrapper">
    <div class="container-fluid max-width-1600">

      {{-- BREADCRUMB & HEADER ACTIONS --}}
      <div class="content-header mb-4 flex-column align-items-start gap-3">
        <div class="w-100">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1.5 fs-7">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}" class="text-decoration-none text-muted">Payroll</a></li>
              <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Transaction Statement</li>
            </ol>
          </nav>
          <div class="d-flex align-items-center gap-2.5">
            <div class="p-2.5 rounded-3 bg-primary text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
              <i data-lucide="file-spreadsheet" style="width:1.5rem;height:1.5rem;"></i>
            </div>
            <div>
              <h3 class="mb-0 fw-bold text-dark tracking-tight">Payroll Transaction Statement & Audit Ledger</h3>
              <p class="text-muted mb-0 fs-7">Generate detailed staff salary disbursement reports, audit logs, and transaction ledgers</p>
            </div>
          </div>
        </div>
        <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center pt-2 border-top w-100">
          <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
            <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Payroll
          </a>
          <a href="{{ route('payroll.statement.export', request()->all()) }}" class="btn btn-outline-success btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
            <i data-lucide="download" style="width:1rem;height:1rem;"></i> Export CSV
          </a>
          <a href="{{ route('payroll.statement.print', request()->all()) }}" target="_blank" class="btn btn-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
            <i data-lucide="printer" style="width:1rem;height:1rem;"></i> Print Statement
          </a>
        </div>
      </div>

      {{-- STATEMENT FILTER CARD --}}
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
          <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i data-lucide="filter" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Filter Transaction Ledger
          </h6>
        </div>
        <div class="card-body p-4">
          <form method="GET" action="{{ route('payroll.statement') }}" class="row g-3">
            <div class="col-md-3">
              <label class="form-label text-xs font-semibold text-uppercase text-muted">Start Date</label>
              <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
              <label class="form-label text-xs font-semibold text-uppercase text-muted">End Date</label>
              <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
              <label class="form-label text-xs font-semibold text-uppercase text-muted">Staff Member</label>
              <select name="staff_id" class="form-select form-select-sm">
                <option value="0">All Active Staff</option>
                @foreach($staffMembers as $st)
                  <option value="{{ $st->id }}" @selected($staffId === $st->id)>
                    {{ $st->full_name }} ({{ $st->staff_id }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label text-xs font-semibold text-uppercase text-muted">Department</label>
              <select name="department" class="form-select form-select-sm">
                <option value="all" @selected($department === 'all')>All Departments</option>
                @foreach($departments as $dept)
                  <option value="{{ $dept }}" @selected($department === $dept)>{{ ucwords(str_replace(['_', '-'], ' ', $dept)) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label text-xs font-semibold text-uppercase text-muted">Payroll Month</label>
              <select name="month" class="form-select form-select-sm">
                <option value="all" @selected($month === 'all')>All Months</option>
                @foreach($months as $m)
                  <option value="{{ $m }}" @selected($month === $m)>{{ $m }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label text-xs font-semibold text-uppercase text-muted">Payment Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="all" @selected($status === 'all')>All Status</option>
                <option value="paid" @selected($status === 'paid')>Paid</option>
                <option value="pending" @selected($status === 'pending')>Pending</option>
                <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label text-xs font-semibold text-uppercase text-muted">Payment Method</label>
              <select name="method" class="form-select form-select-sm">
                <option value="all" @selected($method === 'all')>All Methods</option>
                <option value="cash" @selected($method === 'cash')>Cash</option>
                <option value="bank_transfer" @selected($method === 'bank_transfer')>Bank Transfer</option>
                <option value="cheque" @selected($method === 'cheque')>Cheque</option>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
              <button type="submit" class="btn btn-primary btn-sm px-3 flex-grow-1">
                <i data-lucide="search" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Generate Report
              </button>
              <a href="{{ route('payroll.statement') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
            </div>
          </form>
        </div>
      </div>

      {{-- STATS METRIC SUMMARY CARDS --}}
      <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-3">
          <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
            <div class="text-muted text-xs text-uppercase font-semibold mb-1">Total Net Disbursed</div>
            <h3 class="fw-bold text-primary mb-0">Rs. {{ number_format($totalNetSalary, 2) }}</h3>
            <span class="fs-7 text-muted">{{ count($payrolls) }} total transaction(s)</span>
          </div>
        </div>
        <div class="col-md-4 col-lg-3">
          <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
            <div class="text-muted text-xs text-uppercase font-semibold mb-1">Paid Settlement</div>
            <h3 class="fw-bold text-success mb-0">Rs. {{ number_format($paidAmount, 2) }}</h3>
            <span class="fs-7 text-emerald-600">{{ $payrolls->where('status', 'paid')->count() }} paid payrolls</span>
          </div>
        </div>
        <div class="col-md-4 col-lg-3">
          <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
            <div class="text-muted text-xs text-uppercase font-semibold mb-1">Pending Liability</div>
            <h3 class="fw-bold text-warning mb-0">Rs. {{ number_format($pendingAmount, 2) }}</h3>
            <span class="fs-7 text-amber-600">{{ $payrolls->where('status', 'pending')->count() }} pending approval</span>
          </div>
        </div>
        <div class="col-md-4 col-lg-3">
          <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
            <div class="text-muted text-xs text-uppercase font-semibold mb-1">Total Adjustments</div>
            <div class="d-flex justify-content-between align-items-center mt-1">
              <span class="text-success fs-7 fw-semibold">+ Allow: {{ number_format($totalAllowance, 0) }}</span>
              <span class="text-danger fs-7 fw-semibold">- Deduc: {{ number_format($totalDeduction, 0) }}</span>
            </div>
            <span class="fs-7 text-muted">Base Salary Total: Rs. {{ number_format($totalBasic, 2) }}</span>
          </div>
        </div>
      </div>

      {{-- TRANSACTION LEDGER TABLE --}}
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold text-dark">Itemized Payroll Transaction Ledger</h6>
          <span class="badge bg-light text-dark border">Found {{ count($payrolls) }} Entry(ies)</span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 40px;">#</th>
                <th>Staff Details</th>
                <th>Month</th>
                <th>Payment Method</th>
                <th class="text-end">Basic Salary</th>
                <th class="text-end text-success">Allowance</th>
                <th class="text-end text-danger">Deduction</th>
                <th class="text-end">Net Salary</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($payrolls as $index => $p)
                <tr>
                  <td class="text-muted fs-7">{{ $index + 1 }}</td>
                  <td>
                    @if($p->staff)
                      <div class="fw-bold text-dark">{{ $p->staff->full_name }}</div>
                      <div class="text-xs text-muted">ID: {{ $p->staff->staff_id }} • {{ $p->staff->formatted_department }}</div>
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                  <td><span class="badge bg-light text-dark font-monospace border">{{ $p->payroll_month }}</span></td>
                  <td class="text-capitalize fs-7">{{ str_replace('_', ' ', $p->payment_method) }}</td>
                  <td class="text-end font-monospace fs-7">Rs. {{ number_format($p->basic_salary, 2) }}</td>
                  <td class="text-end font-monospace fs-7 text-success">+ Rs. {{ number_format($p->allowance, 2) }}</td>
                  <td class="text-end font-monospace fs-7 text-danger">- Rs. {{ number_format($p->deduction, 2) }}</td>
                  <td class="text-end font-monospace fw-bold text-primary">Rs. {{ number_format($p->net_salary, 2) }}</td>
                  <td>
                    @if($p->status === 'paid')
                      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Paid</span>
                    @elseif($p->status === 'pending')
                      <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Pending</span>
                    @else
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Cancelled</span>
                    @endif
                  </td>
                  <td class="fs-7">{{ $p->payment_date ? $p->payment_date->format('d M, Y') : '—' }}</td>
                  <td class="text-center">
                    <a href="{{ route('payroll.show', $p->id) }}" class="btn btn-ghost btn-sm text-primary" title="View Payslip">
                      <i data-lucide="eye" style="width:1rem;height:1rem;"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="11" class="text-center text-muted py-4">No payroll transactions match the specified filter criteria.</td>
                </tr>
              @endforelse
            </tbody>
            @if(count($payrolls) > 0)
              <tfoot class="table-light fw-bold">
                <tr>
                  <td colspan="4">GRAND TOTAL SUMMARY ({{ count($payrolls) }} RECORDS)</td>
                  <td class="text-end font-monospace">Rs. {{ number_format($totalBasic, 2) }}</td>
                  <td class="text-end font-monospace text-success">+ Rs. {{ number_format($totalAllowance, 2) }}</td>
                  <td class="text-end font-monospace text-danger">- Rs. {{ number_format($totalDeduction, 2) }}</td>
                  <td class="text-end font-monospace text-primary fs-6">Rs. {{ number_format($totalNetSalary, 2) }}</td>
                  <td colspan="3"></td>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>
      </div>

    </div>
  </div>

@endsection
