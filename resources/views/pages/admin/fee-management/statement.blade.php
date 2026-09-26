@extends('layouts.app')

@section('title', 'Fee Transaction Statement')

@push('styles')
  <style>
    @media print {
      @page {
        size: A4 portrait;
        margin: 8mm 10mm;
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

      .print-a4-statement {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
      }

      .statement-print-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 10px !important;
      }

      .statement-print-table th,
      .statement-print-table td {
        border: 1px solid #cbd5e1 !important;
        padding: 4px 6px !important;
        font-size: 7.5pt !important;
        vertical-align: middle !important;
      }

      .statement-print-table th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
      }
    }

    @media screen {
      .print-a4-statement {
        display: none;
      }
    }
  </style>
@endpush

@section('content')
  <div class="screen-statement-wrapper container-fluid py-4 no-print">

    {{-- EXECUTIVE HEADER --}}
    {{-- EXECUTIVE HEADER WITH ACTIONS BELOW HEADING TAGLINE --}}
    <div class="content-header mb-4 flex-column align-items-start gap-3">
      <div class="w-100">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-1.5 fs-7">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('fee-management.index') }}" class="text-decoration-none text-muted">Fee Management</a></li>
            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Transaction Statement</li>
          </ol>
        </nav>
        <div class="d-flex align-items-center gap-2.5">
          <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
            <i data-lucide="file-spreadsheet" style="width:1.5rem;height:1.5rem;"></i>
          </div>
          <div>
            <h3 class="mb-0 fw-bold text-dark tracking-tight">Fee Account Ledger & Transaction Statement</h3>
            <p class="text-muted mb-0 fs-7">Generate detailed fee collection reports, student ledgers, and transaction histories</p>
          </div>
        </div>
      </div>
      <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center pt-2 border-top w-100">
        <a href="{{ route('fee-management.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
          <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Invoices
        </a>
        <a href="{{ route('fee-management.statement.export', request()->all()) }}" class="btn btn-outline-success btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
          <i data-lucide="download" style="width:1rem;height:1rem;"></i> Export CSV
        </a>
        <a href="{{ route('fee-management.statement.print', request()->all()) }}" target="_blank" class="btn btn-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border: none;">
          <i data-lucide="printer" style="width:1rem;height:1rem;"></i> Print Statement
        </a>
      </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
          <i data-lucide="sliders" style="width:1.1rem;height:1.1rem;" class="text-teal-600"></i>
          Statement Filter Options
        </h6>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('fee-management.statement') }}" class="row g-3">
          <!-- Student Picker -->
          <div class="col-md-4">
            <label class="form-label fs-7 fw-semibold text-secondary mb-1">Select Student</label>
            <select name="admission_id" class="form-select form-select-sm select2-student">
              <option value="0">All Students (Global Statement)</option>
              @foreach($studentsList as $st)
                <option value="{{ $st->id }}" {{ $admissionId == $st->id ? 'selected' : '' }}>
                  {{ $st->admission_no }} - {{ $st->first_name }} {{ $st->last_name }} ({{ $st->class_name }})
                </option>
              @endforeach
            </select>
          </div>

          <!-- Class Picker -->
          <div class="col-md-2">
            <label class="form-label fs-7 fw-semibold text-secondary mb-1">Class</label>
            <select name="class_name" class="form-select form-select-sm">
              <option value="all">All Classes</option>
              @foreach($classesList as $c)
                <option value="{{ $c }}" {{ $className == $c ? 'selected' : '' }}>{{ $c }}</option>
              @endforeach
            </select>
          </div>

          <!-- Fee Type -->
          <div class="col-md-2">
            <label class="form-label fs-7 fw-semibold text-secondary mb-1">Fee Type</label>
            <select name="fee_type" class="form-select form-select-sm">
              <option value="all">All Fee Types</option>
              <option value="school_fee" {{ $feeType == 'school_fee' ? 'selected' : '' }}>School Fee</option>
              <option value="tuition" {{ $feeType == 'tuition' ? 'selected' : '' }}>Tuition Fee</option>
              <option value="admission" {{ $feeType == 'admission' ? 'selected' : '' }}>Admission Fee</option>
              <option value="examination" {{ $feeType == 'examination' ? 'selected' : '' }}>Examination Fee</option>
              <option value="transport" {{ $feeType == 'transport' ? 'selected' : '' }}>Transport Fee</option>
              <option value="hostel" {{ $feeType == 'hostel' ? 'selected' : '' }}>Hostel Fee</option>
              <option value="miscellaneous" {{ $feeType == 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
            </select>
          </div>

          <!-- Payment Status -->
          <div class="col-md-2">
            <label class="form-label fs-7 fw-semibold text-secondary mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="all">All Status</option>
              <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
              <option value="partial" {{ $status == 'partial' ? 'selected' : '' }}>Partial</option>
              <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
              <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>

          <!-- Payment Method -->
          <div class="col-md-2">
            <label class="form-label fs-7 fw-semibold text-secondary mb-1">Payment Method</label>
            <select name="payment_method" class="form-select form-select-sm">
              <option value="all">All Methods</option>
              <option value="cash" {{ $method == 'cash' ? 'selected' : '' }}>Cash</option>
              <option value="bank_transfer" {{ $method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
              <option value="online" {{ $method == 'online' ? 'selected' : '' }}>Online</option>
              <option value="cheque" {{ $method == 'cheque' ? 'selected' : '' }}>Cheque</option>
            </select>
          </div>

          <!-- Date Range: Start Date -->
          <div class="col-md-3">
            <label class="form-label fs-7 fw-semibold text-secondary mb-1">Start Date</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
          </div>

          <!-- Date Range: End Date -->
          <div class="col-md-3">
            <label class="form-label fs-7 fw-semibold text-secondary mb-1">End Date</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
          </div>

          <!-- Buttons -->
          <div class="col-md-6 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-sm btn-primary px-4" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border: none;">
              <i data-lucide="filter" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Apply Filters
            </button>
            <a href="{{ route('fee-management.statement') }}" class="btn btn-sm btn-outline-secondary">Reset Filters</a>
          </div>
        </form>
      </div>
    </div>

    {{-- SELECTED STUDENT LEDGER SUMMARY CARD --}}
    @if($selectedStudent)
      <div class="card border-0 shadow-sm mb-4 bg-teal-50 border-start border-4 border-teal-600">
        <div class="card-body p-3">
          <div class="row align-items-center">
            <div class="col-md-2 text-center text-md-start">
              @if($selectedStudent->student_photo)
                <img src="{{ asset('storage/' . $selectedStudent->student_photo) }}" alt="Student" class="rounded-circle shadow-sm" style="width: 72px; height: 72px; object-fit: cover;">
              @else
                <div class="rounded-circle bg-teal-600 text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 72px; height: 72px; font-size: 1.5rem;">
                  {{ substr($selectedStudent->first_name, 0, 1) }}{{ substr($selectedStudent->last_name, 0, 1) }}
                </div>
              @endif
            </div>
            <div class="col-md-6">
              <h5 class="fw-bold text-dark mb-1">{{ $selectedStudent->first_name }} {{ $selectedStudent->last_name }}</h5>
              <div class="text-muted fs-7">
                <span class="me-3"><strong class="text-dark">Admission No:</strong> {{ $selectedStudent->admission_no }}</span>
                <span class="me-3"><strong class="text-dark">Class & Section:</strong> {{ $selectedStudent->class_name }} ({{ $selectedStudent->section ?? 'A' }})</span>
              </div>
              <div class="text-muted fs-7 mt-1">
                <span class="me-3"><strong class="text-dark">Father Name:</strong> {{ $selectedStudent->father_name ?? 'N/A' }}</span>
                <span><strong class="text-dark">Contact:</strong> {{ $selectedStudent->phone_number ?? 'N/A' }}</span>
              </div>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
              <div class="p-2 bg-white rounded border shadow-xs d-inline-block text-center min-w-160">
                <span class="text-muted fs-7 text-uppercase d-block fw-semibold">Student Account Dues</span>
                <span class="fs-4 fw-bold {{ $totalDueBalance > 0 ? 'text-danger' : 'text-success' }}">
                  Rs. {{ number_format($totalDueBalance, 2) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif

    {{-- SUMMARY STATISTIC CARDS --}}
    <div class="row g-3 mb-4">
      <!-- Gross Invoiced -->
      <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
          <div class="card-body p-3">
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Gross Invoiced</span>
            <h4 class="fw-bold text-dark mb-1 mt-1">Rs. {{ number_format($totalGrossAmount, 2) }}</h4>
            <span class="badge bg-secondary text-white fw-normal fs-8">
              <i data-lucide="file-text" style="width:0.75rem;height:0.75rem;" class="me-1"></i> {{ $invoices->count() }} Total Invoices
            </span>
          </div>
        </div>
      </div>

      <!-- Paid Invoices -->
      <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 bg-success-subtle border-start border-3 border-success h-100">
          <div class="card-body p-3">
            <span class="text-success text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Paid Invoices</span>
            <h4 class="fw-bold text-success mb-1 mt-1">Rs. {{ number_format($totalPaidAmount, 2) }}</h4>
            <span class="badge bg-success text-white fw-semibold fs-8">
              <i data-lucide="check-circle" style="width:0.75rem;height:0.75rem;" class="me-1"></i> {{ $paidInvoicesCount }} Paid Invoices
            </span>
          </div>
        </div>
      </div>

      <!-- Pending Invoices Breakdown -->
      <div class="col-xl-3 col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 bg-warning-subtle border-start border-3 border-warning h-100">
          <div class="card-body p-3">
            <span class="text-warning-emphasis text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pending Invoices</span>
            <h4 class="fw-bold text-dark mb-1 mt-1">{{ $pendingInvoicesCount }} Invoices</h4>
            <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
              <span class="badge bg-danger text-white fw-semibold fs-8">
                {{ $unpaidInvoicesCount }} Unpaid
              </span>
              <span class="badge bg-warning text-dark fw-semibold fs-8">
                {{ $partialInvoicesCount }} Partial
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Outstanding Dues -->
      <div class="col-xl-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 bg-danger-subtle border-start border-3 border-danger h-100">
          <div class="card-body p-3">
            <span class="text-danger text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Outstanding Dues</span>
            <h4 class="fw-bold text-danger mb-1 mt-1">Rs. {{ number_format($totalDueBalance, 2) }}</h4>
            <small class="text-danger fs-8 fw-semibold">Pending Due Balance</small>
          </div>
        </div>
      </div>

      <!-- Net Receivable -->
      <div class="col-xl-2 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
          <div class="card-body p-3">
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Net Receivable</span>
            <h5 class="fw-bold text-primary mb-1 mt-1">Rs. {{ number_format($totalNetAmount, 2) }}</h5>
            <small class="text-muted fs-8">Concession: Rs. {{ number_format($totalDiscount, 2) }}</small>
          </div>
        </div>
      </div>
    </div>

    {{-- TRANSACTION STATEMENT LEDGER TABLE --}}
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark">Fee Transaction Ledger</h6>
        <span class="badge bg-light text-dark border fs-7 fw-normal">
          Showing {{ $invoices->count() }} statement entries
        </span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Invoice No</th>
              <th>Student Details</th>
              <th>Fee Description</th>
              <th class="text-end">Amount</th>
              <th class="text-end">Discount</th>
              <th class="text-end">Net Payable</th>
              <th class="text-end text-success">Paid</th>
              <th class="text-end text-danger">Due Balance</th>
              <th>Method</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $inv)
              <tr>
                <td>
                  <small class="fw-semibold text-dark">{{ $inv->created_at->format('d M Y') }}</small>
                  @if($inv->payment_date)
                    <small class="text-muted d-block fs-8">Paid: {{ $inv->payment_date->format('d M Y') }}</small>
                  @endif
                </td>
                <td>
                  <span class="fw-bold text-primary">{{ $inv->invoice_no }}</span>
                </td>
                <td>
                  <div class="fw-semibold text-dark">
                    {{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'N/A' }}
                  </div>
                  <small class="text-muted">
                    {{ $inv->admission ? ($inv->admission->admission_no . ' • ' . $inv->admission->class_name) : '' }}
                  </small>
                </td>
                <td>
                  <span class="badge bg-light text-dark border text-capitalize me-1">{{ str_replace('_', ' ', $inv->fee_type) }}</span>
                  <small class="text-muted d-block">{{ $inv->fee_month }}</small>
                </td>
                <td class="text-end">Rs. {{ number_format($inv->amount, 2) }}</td>
                <td class="text-end text-info">
                  {{ $inv->discount > 0 ? ('Rs. ' . number_format($inv->discount, 2)) : '-' }}
                </td>
                <td class="text-end fw-semibold">Rs. {{ number_format($inv->net_amount, 2) }}</td>
                <td class="text-end text-success fw-bold">
                  Rs. {{ number_format($inv->paid_amount, 2) }}
                </td>
                <td class="text-end fw-bold {{ $inv->due_balance > 0 ? 'text-danger' : 'text-muted' }}">
                  Rs. {{ number_format($inv->due_balance, 2) }}
                </td>
                <td>
                  <small class="text-capitalize text-muted">{{ str_replace('_', ' ', $inv->payment_method) }}</small>
                </td>
                <td>
                  <span class="badge {{ $inv->status_badge_class }} text-capitalize px-2 py-1">
                    {{ $inv->status }}
                  </span>
                </td>
                <td class="text-center">
                  <a href="{{ route('fee-management.show', $inv->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="View Invoice">
                    <i data-lucide="eye" style="width:0.8rem;height:0.8rem;"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="12" class="text-center py-5 text-muted">
                  <i data-lucide="file-x-2" class="mb-2 text-secondary" style="width:2.5rem;height:2.5rem;"></i>
                  <p class="mb-0 fw-semibold">No fee transactions found for the selected criteria.</p>
                  <small>Try selecting a different date range or student filter.</small>
                </td>
              </tr>
            @endforelse
          </tbody>
          @if($invoices->count() > 0)
            <tfoot class="table-light fw-bold border-top border-2 border-dark">
              <tr>
                <td colspan="4" class="text-end">TOTALS:</td>
                <td class="text-end">Rs. {{ number_format($totalGrossAmount, 2) }}</td>
                <td class="text-end text-info">Rs. {{ number_format($totalDiscount, 2) }}</td>
                <td class="text-end">Rs. {{ number_format($totalNetAmount, 2) }}</td>
                <td class="text-end text-success">Rs. {{ number_format($totalPaidAmount, 2) }}</td>
                <td class="text-end text-danger">Rs. {{ number_format($totalDueBalance, 2) }}</td>
                <td colspan="3"></td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

  {{-- PRINTABLE A4 SECTION FOR DIRECT PRINTING --}}
  <div class="print-a4-statement">
    <div style="text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-bottom: 12px;">
      <h2 style="margin: 0; font-size: 16pt; font-weight: bold; color: #0f172a; text-transform: uppercase;">
        {{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}
      </h2>
      <p style="margin: 2px 0 0 0; font-size: 9pt; color: #475569;">
        {{ $globalSchoolInfo->full_address ?? 'Dhanote, District Lodhran' }} | Phone: {{ $globalSchoolInfo->phone ?? '03266850002' }} | Email: {{ $globalSchoolInfo->email ?? 'Superiorschoolnps@gmail.com' }}
      </p>
      <h4 style="margin: 8px 0 0 0; font-size: 11pt; font-weight: bold; background: #f1f5f9; display: inline-block; padding: 3px 12px; border-radius: 4px; border: 1px solid #cbd5e1;">
        FEE TRANSACTION STATEMENT & ACCOUNT LEDGER
      </h4>
    </div>

    @if($selectedStudent)
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8pt; border: 1px solid #cbd5e1;">
        <tr style="background-color: #f8fafc;">
          <td style="padding: 4px 8px; border: 1px solid #cbd5e1;"><strong>Student Name:</strong> {{ $selectedStudent->first_name }} {{ $selectedStudent->last_name }}</td>
          <td style="padding: 4px 8px; border: 1px solid #cbd5e1;"><strong>Admission No:</strong> {{ $selectedStudent->admission_no }}</td>
        </tr>
        <tr>
          <td style="padding: 4px 8px; border: 1px solid #cbd5e1;"><strong>Father Name:</strong> {{ $selectedStudent->father_name ?? 'N/A' }}</td>
          <td style="padding: 4px 8px; border: 1px solid #cbd5e1;"><strong>Class & Section:</strong> {{ $selectedStudent->class_name }} ({{ $selectedStudent->section ?? 'A' }})</td>
        </tr>
      </table>
    @endif

    <div style="font-size: 8pt; margin-bottom: 8px;">
      <strong>Statement Date Range:</strong> {{ $startDate ? date('d M Y', strtotime($startDate)) : 'All Time' }} to {{ $endDate ? date('d M Y', strtotime($endDate)) : date('d M Y') }}
    </div>

    <table class="statement-print-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Invoice #</th>
          <th>Student</th>
          <th>Fee Type</th>
          <th>Month</th>
          <th style="text-align: right;">Amount</th>
          <th style="text-align: right;">Discount</th>
          <th style="text-align: right;">Net</th>
          <th style="text-align: right;">Paid</th>
          <th style="text-align: right;">Balance</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoices as $inv)
          <tr>
            <td>{{ $inv->created_at->format('d/m/Y') }}</td>
            <td><strong>{{ $inv->invoice_no }}</strong></td>
            <td>{{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'N/A' }}</td>
            <td>{{ str_replace('_', ' ', $inv->fee_type) }}</td>
            <td>{{ $inv->fee_month }}</td>
            <td style="text-align: right;">{{ number_format($inv->amount, 2) }}</td>
            <td style="text-align: right;">{{ number_format($inv->discount, 2) }}</td>
            <td style="text-align: right;">{{ number_format($inv->net_amount, 2) }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($inv->paid_amount, 2) }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($inv->due_balance, 2) }}</td>
            <td style="text-transform: uppercase;">{{ $inv->status }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="font-weight: bold; background-color: #f1f5f9;">
          <td colspan="5" style="text-align: right;">TOTALS:</td>
          <td style="text-align: right;">{{ number_format($totalGrossAmount, 2) }}</td>
          <td style="text-align: right;">{{ number_format($totalDiscount, 2) }}</td>
          <td style="text-align: right;">{{ number_format($totalNetAmount, 2) }}</td>
          <td style="text-align: right;">{{ number_format($totalPaidAmount, 2) }}</td>
          <td style="text-align: right;">{{ number_format($totalDueBalance, 2) }}</td>
          <td></td>
        </tr>
      </tfoot>
    </table>

    <div style="margin-top: 30px; display: flex; justify-content: space-between; font-size: 8pt;">
      <div style="text-align: center; width: 180px; border-top: 1px solid #000; padding-top: 4px;">
        Accounts Officer
      </div>
      <div style="text-align: center; width: 180px; border-top: 1px solid #000; padding-top: 4px;">
        Principal Signature
      </div>
    </div>
  </div>
@endsection
