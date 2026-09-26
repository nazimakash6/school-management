@extends('layouts.app')

@section('title', 'Fee Transaction Statement - School Management System')

@push('styles')
  <style>
    .filter-card {
      border: 1px solid rgba(13, 148, 136, 0.2) !important;
      background: #ffffff;
      box-shadow: 0 4px 20px -2px rgba(13, 148, 136, 0.06) !important;
    }
    .filter-card .form-select, .filter-card .form-control {
      border-color: #cbd5e1;
      border-radius: 0.5rem;
      font-size: 0.85rem;
      padding: 0.45rem 0.75rem;
      transition: all 0.2s ease;
    }
    .filter-card .form-select:focus, .filter-card .form-control:focus {
      border-color: #0d9488;
      box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.15);
    }
    .filter-label {
      font-size: 0.78rem;
      font-weight: 600;
      color: #334155;
      margin-bottom: 0.35rem;
    }
    .statement-stat-card {
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid #e2e8f0 !important;
      background: #ffffff;
    }
    .statement-stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08) !important;
    }
    .stat-icon-badge {
      width: 38px;
      height: 38px;
      border-radius: 0.6rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .table-ledger th {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 700;
      color: #334155;
      background-color: #f1f5f9 !important;
      padding: 0.75rem 0.75rem;
    }
    @media print {
      @page {
        size: A4 portrait;
        margin: 8mm 10mm;
      }
      html, body, .app-wrapper, .main-content, .page-content {
        background: #ffffff !important;
        color: #000000 !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        font-size: 8pt !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
      }
      .no-print, header, footer, nav, .app-header, .app-sidebar, .topbar, .breadcrumb, .alert {
        display: none !important;
      }
      .print-a4-statement {
        display: block !important;
        width: 100% !important;
      }
      .statement-print-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 10px !important;
      }
      .statement-print-table th, .statement-print-table td {
        border: 1px solid #cbd5e1 !important;
        padding: 4px 6px !important;
        font-size: 7.5pt !important;
      }
      .statement-print-table th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 700 !important;
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
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-1 fs-7">
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
            <h3 class="mb-0 fw-bold text-dark tracking-tight">Fee Account Ledger & Statement</h3>
            <p class="text-muted mb-0 fs-7">Generate detailed collection reports, filter by academic session, class and student ledgers</p>
          </div>
        </div>
      </div>
      <div class="d-flex gap-2 flex-wrap align-items-center">
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

    {{-- OPTIMIZED FILTER FORM CARD WITH DEPENDENT DROPDOWNS --}}
    <div class="card border-0 rounded-3 mb-4 filter-card">
      <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
          <i data-lucide="sliders" style="width:1.1rem;height:1.1rem;" class="text-teal-600"></i>
          Filter Statement Records
        </h6>
        <span class="badge bg-teal-subtle text-teal-700 fw-semibold px-2.5 py-1 fs-8">
          <i data-lucide="filter" style="width:0.75rem;height:0.75rem;" class="me-1"></i> Dependent Filter (Session &rarr; Class &rarr; Student)
        </span>
      </div>
      <div class="card-body p-4">
        <form method="GET" action="{{ route('fee-management.statement') }}" id="statementFilterForm">
          <!-- Filter Grid: Session -> Class -> Student Order -->
          <div class="row g-3">
            <!-- 1. Academic Session Filter -->
            <div class="col-lg-3 col-md-6 col-12">
              <label class="filter-label d-flex align-items-center gap-1">
                <i data-lucide="calendar-range" style="width:0.875rem;height:0.875rem;" class="text-teal-600"></i>
                1. Academic Session
              </label>
              <select name="academic_session_id" id="filter_academic_session" class="form-select" onchange="onSessionChange()">
                <option value="0">All Sessions</option>
                @foreach($academicSessions as $session)
                  <option value="{{ $session->id }}" {{ $academicSessionId == $session->id ? 'selected' : '' }}>
                    {{ $session->session_name }} {{ $session->status ? '('.ucfirst($session->status->value ?? $session->status).')' : '' }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- 2. Class Picker -->
            <div class="col-lg-3 col-md-6 col-12">
              <label class="filter-label d-flex align-items-center gap-1">
                <i data-lucide="graduation-cap" style="width:0.875rem;height:0.875rem;" class="text-teal-600"></i>
                2. Class
              </label>
              <select name="class_name" id="filter_class" class="form-select" onchange="onClassChange()">
                <option value="all">All Classes</option>
                @foreach($classesList as $c)
                  <option value="{{ $c }}" {{ $className == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
              </select>
            </div>

            <!-- 3. Student Picker -->
            <div class="col-lg-3 col-md-6 col-12">
              <label class="filter-label d-flex align-items-center gap-1">
                <i data-lucide="user" style="width:0.875rem;height:0.875rem;" class="text-teal-600"></i>
                3. Student
              </label>
              <select name="admission_id" id="filter_student" class="form-select select2-student">
                <option value="0">All Students (Global Ledger)</option>
                @foreach($studentsList as $st)
                  <option value="{{ $st->id }}" {{ $admissionId == $st->id ? 'selected' : '' }}>
                    {{ $st->admission_no }} - {{ $st->first_name }} {{ $st->last_name }} ({{ $st->class_name }})
                  </option>
                @endforeach
              </select>
            </div>

            <!-- 4. Fee Type -->
            <div class="col-lg-3 col-md-6 col-12">
              <label class="filter-label d-flex align-items-center gap-1">
                <i data-lucide="tag" style="width:0.875rem;height:0.875rem;" class="text-teal-600"></i>
                Fee Type
              </label>
              <select name="fee_type" class="form-select">
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

            <!-- 5. Payment Status -->
            <div class="col-lg-3 col-md-6 col-12">
              <label class="filter-label d-flex align-items-center gap-1">
                <i data-lucide="check-circle-2" style="width:0.875rem;height:0.875rem;" class="text-teal-600"></i>
                Payment Status
              </label>
              <select name="status" class="form-select">
                <option value="all">All Status</option>
                <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="partial" {{ $status == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>

            <!-- 6. Payment Method -->
            <div class="col-lg-3 col-md-6 col-12">
              <label class="filter-label d-flex align-items-center gap-1">
                <i data-lucide="credit-card" style="width:0.875rem;height:0.875rem;" class="text-teal-600"></i>
                Payment Method
              </label>
              <select name="payment_method" class="form-select">
                <option value="all">All Methods</option>
                <option value="cash" {{ $method == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="bank_transfer" {{ $method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="online" {{ $method == 'online' ? 'selected' : '' }}>Online</option>
                <option value="cheque" {{ $method == 'cheque' ? 'selected' : '' }}>Cheque</option>
              </select>
            </div>

            <!-- 7. Start Date -->
            <div class="col-lg-3 col-md-6 col-12">
              <label class="filter-label d-flex align-items-center gap-1">
                <i data-lucide="calendar" style="width:0.875rem;height:0.875rem;" class="text-teal-600"></i>
                Start Date
              </label>
              <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>

            <!-- 8. End Date -->
            <div class="col-lg-3 col-md-6 col-12">
              <label class="filter-label d-flex align-items-center gap-1">
                <i data-lucide="calendar" style="width:0.875rem;height:0.875rem;" class="text-teal-600"></i>
                End Date
              </label>
              <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
          </div>

          <!-- Action Buttons Bar -->
          <div class="d-flex justify-content-end align-items-center gap-2 pt-3 mt-3 border-top">
            <a href="{{ route('fee-management.statement') }}" class="btn btn-outline-secondary px-4 d-inline-flex align-items-center gap-1.5" title="Reset all filters">
              <i data-lucide="refresh-cw" style="width:0.9rem;height:0.9rem;"></i> Reset Filters
            </a>
            <button type="submit" class="btn text-white fw-bold px-4 d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border: none;">
              <i data-lucide="filter" style="width:0.9rem;height:0.9rem;"></i> Apply Filters
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- SELECTED STUDENT LEDGER SUMMARY CARD --}}
    @if($selectedStudent)
      <div class="card border-0 shadow-sm mb-4 rounded-3 bg-white border-start border-4 border-teal-600">
        <div class="card-body p-3.5">
          <div class="row align-items-center">
            <div class="col-md-2 text-center text-md-start">
              @if($selectedStudent->student_photo)
                <img src="{{ asset('storage/' . $selectedStudent->student_photo) }}" alt="Student" class="rounded-circle shadow-sm border border-2 border-white" style="width: 72px; height: 72px; object-fit: cover;">
              @else
                <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 72px; height: 72px; font-size: 1.5rem; background-color: #0d9488;">
                  {{ substr($selectedStudent->first_name, 0, 1) }}{{ substr($selectedStudent->last_name, 0, 1) }}
                </div>
              @endif
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-2 mb-1">
                <h5 class="fw-bold text-dark mb-0">{{ $selectedStudent->first_name }} {{ $selectedStudent->last_name }}</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-8 fw-semibold">
                  Adm #: {{ $selectedStudent->admission_no }}
                </span>
              </div>
              <div class="text-muted fs-7 d-flex flex-wrap gap-3">
                <span><strong class="text-dark">Class & Sec:</strong> {{ $selectedStudent->class_name }} ({{ $selectedStudent->section ?? 'A' }})</span>
                <span><strong class="text-dark">Father:</strong> {{ $selectedStudent->father_name ?? 'N/A' }}</span>
                <span><strong class="text-dark">Contact:</strong> {{ $selectedStudent->phone_number ?? 'N/A' }}</span>
              </div>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
              <div class="p-3 bg-light rounded-3 border d-inline-block text-center min-w-160">
                <span class="text-muted fs-8 text-uppercase d-block fw-bold tracking-wider mb-1">Student Current Dues</span>
                <span class="h4 fw-bold mb-0 {{ $totalDueBalance > 0 ? 'text-danger' : 'text-success' }}">
                  Rs. {{ number_format($totalDueBalance, 2) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif

    {{-- RESPONSIVE AUTO-WRAPPING SUMMARY STATISTIC CARDS --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
      <!-- 1. Gross Invoiced -->
      <div class="col">
        <div class="card border-0 shadow-sm rounded-3 h-100 statement-stat-card">
          <div class="card-body p-3.5 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Gross Invoiced</span>
              <div class="stat-icon-badge bg-light text-secondary">
                <i data-lucide="file-text" style="width:1.1rem;height:1.1rem;"></i>
              </div>
            </div>
            <div>
              <h4 class="fw-bold text-dark mb-1 fs-5">Rs. {{ number_format($totalGrossAmount, 2) }}</h4>
              <span class="badge bg-secondary-subtle text-secondary fw-medium fs-8">
                {{ $invoices->count() }} Invoices Billed
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Total Paid -->
      <div class="col">
        <div class="card border-0 shadow-sm rounded-3 bg-success-subtle border-start border-3 border-success h-100 statement-stat-card">
          <div class="card-body p-3.5 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="text-success text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Total Collected</span>
              <div class="stat-icon-badge bg-success text-white">
                <i data-lucide="check-circle-2" style="width:1.1rem;height:1.1rem;"></i>
              </div>
            </div>
            <div>
              <h4 class="fw-bold text-success mb-1 fs-5">Rs. {{ number_format($totalPaidAmount, 2) }}</h4>
              <span class="badge bg-success text-white fw-semibold fs-8">
                {{ $paidInvoicesCount }} Paid Invoices
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Pending Invoices Breakdown -->
      <div class="col">
        <div class="card border-0 shadow-sm rounded-3 bg-warning-subtle border-start border-3 border-warning h-100 statement-stat-card">
          <div class="card-body p-3.5 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="text-warning-emphasis text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Pending Count</span>
              <div class="stat-icon-badge bg-warning text-dark">
                <i data-lucide="clock" style="width:1.1rem;height:1.1rem;"></i>
              </div>
            </div>
            <div>
              <h4 class="fw-bold text-dark mb-1 fs-5">{{ $pendingInvoicesCount }} Invoices</h4>
              <div class="d-flex align-items-center gap-1.5 mt-1 flex-wrap">
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
      </div>

      <!-- 4. Outstanding Dues -->
      <div class="col">
        <div class="card border-0 shadow-sm rounded-3 bg-danger-subtle border-start border-3 border-danger h-100 statement-stat-card">
          <div class="card-body p-3.5 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="text-danger text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Outstanding Dues</span>
              <div class="stat-icon-badge bg-danger text-white">
                <i data-lucide="alert-circle" style="width:1.1rem;height:1.1rem;"></i>
              </div>
            </div>
            <div>
              <h4 class="fw-bold text-danger mb-1 fs-5">Rs. {{ number_format($totalDueBalance, 2) }}</h4>
              <small class="text-danger fs-8 fw-semibold d-block">Pending Dues Balance</small>
            </div>
          </div>
        </div>
      </div>

      <!-- 5. Net Receivable -->
      <div class="col">
        <div class="card border-0 shadow-sm rounded-3 h-100 statement-stat-card">
          <div class="card-body p-3.5 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Net Receivable</span>
              <div class="stat-icon-badge bg-primary-subtle text-primary">
                <i data-lucide="receipt" style="width:1.1rem;height:1.1rem;"></i>
              </div>
            </div>
            <div>
              <h5 class="fw-bold text-primary mb-1 fs-6">Rs. {{ number_format($totalNetAmount, 2) }}</h5>
              <small class="text-muted fs-8 d-block">Discount: Rs. {{ number_format($totalDiscount, 2) }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- TRANSACTION STATEMENT LEDGER TABLE --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
      <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
          <i data-lucide="list-tree" style="width:1.1rem;height:1.1rem;" class="text-teal-600"></i>
          Fee Transaction Ledger Entries
        </h6>
        <span class="badge bg-light text-dark border fs-7 fw-normal">
          Showing {{ $invoices->count() }} statement entries
        </span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-ledger fs-7">
          <thead>
            <tr>
              <th class="ps-3">Date</th>
              <th>Voucher #</th>
              <th>Student Details</th>
              <th>Academic Session</th>
              <th>Fee Description</th>
              <th class="text-end">Gross Amount</th>
              <th class="text-end">Discount</th>
              <th class="text-end">Net Payable</th>
              <th class="text-end text-success">Paid Amount</th>
              <th class="text-end text-danger">Due Balance</th>
              <th>Method</th>
              <th>Status</th>
              <th class="text-center pe-3">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $inv)
              <tr>
                <td class="ps-3">
                  <small class="fw-semibold text-dark">{{ $inv->created_at->format('d M Y') }}</small>
                  @if($inv->payment_date)
                    <small class="text-muted d-block fs-8">Paid: {{ $inv->payment_date->format('d M Y') }}</small>
                  @endif
                </td>
                <td>
                  <span class="fw-bold text-primary font-monospace">{{ $inv->invoice_no }}</span>
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
                  @if($inv->academicSession)
                    <span class="badge bg-info-subtle text-info border border-info-subtle fs-8">
                      {{ $inv->academicSession->session_name }}
                    </span>
                  @else
                    <span class="text-muted fs-8">N/A</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-light text-dark border text-capitalize me-1">{{ str_replace('_', ' ', $inv->fee_type) }}</span>
                  <small class="text-muted d-block">{{ $inv->fee_month }}</small>
                </td>
                <td class="text-end fw-medium">Rs. {{ number_format($inv->amount, 2) }}</td>
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
                  <span class="badge {{ $inv->status_badge_class }} text-capitalize px-2 py-1 fs-8">
                    {{ $inv->status }}
                  </span>
                </td>
                <td class="text-center pe-3">
                  <a href="{{ route('fee-management.show', $inv->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="View Invoice">
                    <i data-lucide="eye" style="width:0.85rem;height:0.85rem;"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="13" class="text-center py-5 text-muted">
                  <i data-lucide="file-x-2" class="mb-2 text-secondary opacity-50" style="width:2.5rem;height:2.5rem;"></i>
                  <p class="mb-0 fw-semibold">No fee transactions found for the selected criteria.</p>
                  <small>Try selecting a different academic session or date range filter.</small>
                </td>
              </tr>
            @endforelse
          </tbody>
          @if($invoices->count() > 0)
            <tfoot class="table-light fw-bold border-top border-2 border-dark fs-7">
              <tr>
                <td colspan="5" class="text-end pe-3">TOTALS:</td>
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
          <th>Session</th>
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
            <td>{{ $inv->academicSession->session_name ?? 'N/A' }}</td>
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
          <td colspan="6" style="text-align: right;">TOTALS:</td>
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

@push('scripts')
<script>
var allStudentsData = @json($allStudentsRaw);

function onSessionChange() {
  var sessionId = document.getElementById('filter_academic_session').value;
  var classSelect = document.getElementById('filter_class');
  var studentSelect = document.getElementById('filter_student');

  var currentSelectedClass = classSelect.value;
  var currentSelectedStudent = studentSelect.value;

  // Filter available classes for selected session
  var availableClasses = [];
  allStudentsData.forEach(function(st) {
    if (!st.class_name) return;
    if (sessionId === '0' || String(st.academic_session_id) === String(sessionId)) {
      if (!availableClasses.includes(st.class_name)) {
        availableClasses.push(st.class_name);
      }
    }
  });
  availableClasses.sort();

  // Rebuild Class Dropdown options
  var classHtml = '<option value="all">All Classes</option>';
  availableClasses.forEach(function(c) {
    var isSel = (c === currentSelectedClass) ? 'selected' : '';
    classHtml += `<option value="${c}" ${isSel}>${c}</option>`;
  });
  classSelect.innerHTML = classHtml;

  // Rebuild Student Dropdown options
  rebuildStudentOptions(sessionId, classSelect.value, currentSelectedStudent);
}

function onClassChange() {
  var sessionId = document.getElementById('filter_academic_session').value;
  var className = document.getElementById('filter_class').value;
  var studentSelect = document.getElementById('filter_student');
  
  rebuildStudentOptions(sessionId, className, studentSelect.value);
}

function rebuildStudentOptions(sessionId, className, selectedStudentId) {
  var studentSelect = document.getElementById('filter_student');
  var studentHtml = '<option value="0">All Students (Global Ledger)</option>';

  allStudentsData.forEach(function(st) {
    var matchSession = (sessionId === '0' || String(st.academic_session_id) === String(sessionId));
    var matchClass = (className === 'all' || String(st.class_name) === String(className));

    if (matchSession && matchClass) {
      var isSel = (String(st.id) === String(selectedStudentId)) ? 'selected' : '';
      studentHtml += `<option value="${st.id}" ${isSel}>${st.admission_no} - ${st.first_name} ${st.last_name} (${st.class_name || 'N/A'})</option>`;
    }
  });

  studentSelect.innerHTML = studentHtml;
}
</script>
@endpush
