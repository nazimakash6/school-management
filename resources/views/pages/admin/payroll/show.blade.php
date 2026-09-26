@extends('layouts.app')

@section('title', 'Payroll Payslip - ' . ($payroll->staff ? $payroll->staff->full_name : 'Staff'))

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/payroll-detail.css') }}?v={{ time() }}">
@endpush

@section('content')
  <div class="admission-page-wrapper py-4 px-3 px-md-4 no-print">
    <div class="container-fluid max-width-1600">

      {{-- BREADCRUMB & HEADER ACTIONS --}}
      <div class="profile-header-card fade-up mb-4 p-3 bg-white rounded-3 border shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}" class="text-decoration-none text-muted">Payroll</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Payslip Detail</li>
              </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Staff Salary Payslip</h4>
          </div>
          <div class="d-flex gap-2 flex-wrap align-items-center">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5">
              <i data-lucide="printer" style="width:1rem;height:1rem;"></i> Print Payslip
            </button>
            <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1.5">
              <i data-lucide="pencil" style="width:1rem;height:1rem;"></i> Edit
            </a>
            <a href="{{ route('payroll.index') }}" class="btn btn-ghost btn-sm px-3 d-inline-flex align-items-center gap-1.5">
              <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to List
            </a>
          </div>
        </div>
      </div>

      {{-- HERO PAYSLIP CARD --}}
      <div class="payroll-hero-card d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="payroll-badge-month"><i data-lucide="calendar" style="width:0.875rem;height:0.875rem;" class="me-1"></i> PAYSLIP • {{ strtoupper($payroll->payroll_month) }}</span>
            @if ($payroll->status === 'paid')
              <span class="badge bg-emerald-500 text-white px-2.5 py-1 rounded-pill fs-7"><i data-lucide="check-circle-2" style="width:0.8rem;height:0.8rem;" class="me-1"></i> PAID</span>
            @elseif ($payroll->status === 'pending')
              <span class="badge bg-amber-500 text-white px-2.5 py-1 rounded-pill fs-7"><i data-lucide="clock" style="width:0.8rem;height:0.8rem;" class="me-1"></i> PENDING</span>
            @else
              <span class="badge bg-rose-500 text-white px-2.5 py-1 rounded-pill fs-7"><i data-lucide="x-circle" style="width:0.8rem;height:0.8rem;" class="me-1"></i> CANCELLED</span>
            @endif
          </div>
          <h2 class="fw-bold mb-1 text-white">{{ $payroll->staff ? $payroll->staff->full_name : 'Staff Member' }}</h2>
          <p class="text-slate-300 mb-0 fs-7">
            ID: <span class="fw-semibold text-white me-3">{{ $payroll->staff ? $payroll->staff->staff_id : '—' }}</span>
            Dept: <span class="fw-semibold text-white me-3">{{ $payroll->staff ? $payroll->staff->formatted_department : '—' }}</span>
            Designation: <span class="fw-semibold text-white">{{ $payroll->staff ? $payroll->staff->formatted_designation : '—' }}</span>
          </p>
        </div>
        <div class="text-end">
          <div class="text-slate-400 text-xs text-uppercase fw-semibold mb-1">Net Payable Amount</div>
          <div class="payroll-hero-amount {{ $payroll->net_salary < 0 ? 'text-danger' : '' }}">{{ $payroll->formatted_net_salary }}</div>
        </div>
      </div>

      {{-- SCREEN INFORMATION CARDS GRID --}}
      <div class="row g-4 mb-4">
        {{-- Employee Details --}}
        <div class="col-lg-6">
          <div class="payroll-info-card">
            <div class="payroll-info-card-title d-flex align-items-center justify-content-between">
              <span><i data-lucide="user-check" style="width:1rem;height:1rem;" class="me-1.5 text-primary"></i> Employee Profile</span>
              <span class="badge bg-light text-muted border fw-normal fs-7">Personal & HR</span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Full Name</span>
              <span class="payroll-detail-value">{{ $payroll->staff ? $payroll->staff->full_name : '—' }}</span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Staff Code</span>
              <span class="payroll-detail-value"><code class="bg-light px-2 py-0.5 rounded text-dark fs-7">{{ $payroll->staff ? $payroll->staff->staff_id : '—' }}</code></span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Department</span>
              <span class="payroll-detail-value">{{ $payroll->staff ? $payroll->staff->formatted_department : '—' }}</span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Designation</span>
              <span class="payroll-detail-value">{{ $payroll->staff ? $payroll->staff->formatted_designation : '—' }}</span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Contact Mobile</span>
              <span class="payroll-detail-value">{{ $payroll->staff ? $payroll->staff->mobile_no : '—' }}</span>
            </div>
          </div>
        </div>

        {{-- Payment & Bank Details --}}
        <div class="col-lg-6">
          <div class="payroll-info-card">
            <div class="payroll-info-card-title d-flex align-items-center justify-content-between">
              <span><i data-lucide="credit-card" style="width:1rem;height:1rem;" class="me-1.5 text-primary"></i> Payment & Banking</span>
              <span class="badge bg-light text-muted border fw-normal fs-7">Disbursement</span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Payment Status</span>
              <span class="payroll-detail-value">
                @if ($payroll->status === 'paid')
                  <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">PAID</span>
                @elseif ($payroll->status === 'pending')
                  <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">PENDING</span>
                @else
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">CANCELLED</span>
                @endif
              </span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Payment Method</span>
              <span class="payroll-detail-value text-capitalize">{{ str_replace('_', ' ', $payroll->payment_method) }}</span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Disbursement Date</span>
              <span class="payroll-detail-value">{{ $payroll->payment_date ? $payroll->payment_date->format('d F, Y') : 'Pending Release' }}</span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Bank Name</span>
              <span class="payroll-detail-value">{{ $payroll->staff && $payroll->staff->bank_name ? $payroll->staff->bank_name : 'Cash Payment' }}</span>
            </div>
            <div class="payroll-detail-row">
              <span class="payroll-detail-label">Account No / IBAN</span>
              <span class="payroll-detail-value">{{ $payroll->staff && $payroll->staff->bank_account_number ? $payroll->staff->bank_account_number : ($payroll->staff->iban ?? 'N/A') }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- SALARY BREAKDOWN TABLE --}}
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i data-lucide="calculator" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Salary Computation Breakdown
          </h6>
          <span class="text-muted fs-7">Currency: PKR</span>
        </div>
        <div class="table-responsive">
          <table class="table table-payroll-breakdown align-middle mb-0">
            <thead>
              <tr>
                <th>Item Description</th>
                <th>Classification</th>
                <th class="text-end" style="width: 30%;">Amount (PKR)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="fw-medium text-dark"><i data-lucide="wallet" style="width:1rem;height:1rem;" class="me-2 text-primary"></i> Basic Salary Rate</td>
                <td><span class="badge bg-light text-dark border">Base Pay</span></td>
                <td class="text-end fw-semibold text-dark">Rs. {{ number_format($payroll->basic_salary, 2) }}</td>
              </tr>
              <tr>
                <td class="fw-medium text-success"><i data-lucide="plus-circle" style="width:1rem;height:1rem;" class="me-2 text-success"></i> Allowances & Benefits</td>
                <td><span class="badge bg-success-subtle text-success border border-success-subtle">Addition</span></td>
                <td class="text-end fw-bold text-success">+ Rs. {{ number_format($payroll->allowance, 2) }}</td>
              </tr>
              <tr>
                <td class="fw-medium text-danger"><i data-lucide="minus-circle" style="width:1rem;height:1rem;" class="me-2 text-danger"></i> Deductions (Taxes / Fines / Absences)</td>
                <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle">Deduction</span></td>
                <td class="text-end fw-bold text-danger">- Rs. {{ number_format($payroll->deduction, 2) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="2" class="fs-6 fw-bold">TOTAL NET PAYABLE SALARY</th>
                <th class="text-end fs-5 fw-extrabold {{ $payroll->net_salary < 0 ? 'text-danger' : 'text-primary' }}">{{ $payroll->formatted_net_salary }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      @if ($payroll->notes)
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-light mb-4">
          <div class="d-flex align-items-center gap-2 mb-1">
            <i data-lucide="file-text" style="width:1rem;height:1rem;" class="text-secondary"></i>
            <span class="fw-bold text-xs text-uppercase text-secondary">Remarks / Internal Notes</span>
          </div>
          <p class="mb-0 text-dark fs-7">{{ $payroll->notes }}</p>
        </div>
      @endif

    </div>
  </div>

  {{-- ══════════════════════════════════════════════════
       A4 MODERN PAYSLIP — print only (hidden on screen)
  ══════════════════════════════════════════════════ --}}
  <div class="print-a4-payslip">

    @php
      $schoolInfo = \App\Models\SchoolInfo::first();
      $status     = $payroll->status;
    @endphp

    {{-- Status-based watermark --}}
    <div class="ps-watermark {{ $status }}">{{ strtoupper($status) }}</div>

    {{-- ── Gradient Header ── --}}
    <div class="ps-header">
      <div class="ps-logo-wrap">
        @if($schoolInfo && $schoolInfo->logo_url)
          <img src="{{ $schoolInfo->logo_url }}" alt="Logo" class="ps-logo">
        @else
          <div class="ps-logo-ph">{{ strtoupper(substr($schoolInfo->school_name ?? 'S', 0, 1)) }}</div>
        @endif
        <div>
          <div class="ps-school-name">{{ $schoolInfo->school_name ?? 'School Name' }}</div>
          <div class="ps-school-sub">
            {{ $schoolInfo->full_address ?? '' }}
            @if($schoolInfo && $schoolInfo->phone) &bull; {{ $schoolInfo->phone }} @endif
            @if($schoolInfo && $schoolInfo->email) &bull; {{ $schoolInfo->email }} @endif
          </div>
        </div>
      </div>
      <div class="ps-badge-wrap">
        <div class="ps-badge-title">Salary<br>Payslip</div>
        <div class="ps-badge-month">{{ strtoupper($payroll->payroll_month) }}</div>
        <div class="ps-badge-date">Print Date: {{ now()->format('d M Y') }}</div>
      </div>
    </div>

    {{-- ── Status Stripe ── --}}
    <div class="ps-status-stripe {{ $status }}">
      <span>
        @if($status === 'paid') ✓ Payment Confirmed &amp; Disbursed
        @elseif($status === 'pending') ⏳ Payment Pending Release
        @else ✗ Payment Cancelled
        @endif
      </span>
      <span>
        {{ $payroll->payment_date ? 'Payment Date: ' . $payroll->payment_date->format('d M Y') : 'Disbursement Date: Pending' }}
      </span>
    </div>

    {{-- ── Staff Hero Strip ── --}}
    <div class="ps-staff-hero">
      <div>
        <div class="ps-staff-name">{{ $payroll->staff ? $payroll->staff->full_name : 'N/A' }}</div>
        <div class="ps-staff-meta">
          {{ $payroll->staff ? ($payroll->staff->staff_id ?? '') : '' }}
          &bull; {{ $payroll->staff ? $payroll->staff->formatted_department : '' }}
          &bull; {{ $payroll->staff ? $payroll->staff->formatted_designation : '' }}
        </div>
      </div>
      <div>
        <div class="ps-net-amount-label">Net Payable Amount</div>
        <div class="ps-net-amount {{ $payroll->net_salary < 0 ? 'text-danger' : '' }}">{{ $payroll->formatted_net_salary }}</div>
      </div>
    </div>

    {{-- ── Body ── --}}
    <div class="ps-body">

      {{-- Two-column info cards --}}
      <div class="ps-two-col">
        {{-- Employee Details --}}
        <div class="ps-col">
          <div class="ps-card">
            <div class="ps-card-head">👤 Employee Information</div>
            <div class="ps-card-body">
              <div class="ps-row"><span class="ps-lbl">Full Name</span>        <span class="ps-val">{{ $payroll->staff ? $payroll->staff->full_name : 'N/A' }}</span></div>
              <div class="ps-row"><span class="ps-lbl">Staff ID</span>         <span class="ps-val">{{ $payroll->staff ? ($payroll->staff->staff_id ?? 'N/A') : 'N/A' }}</span></div>
              <div class="ps-row"><span class="ps-lbl">Department</span>       <span class="ps-val">{{ $payroll->staff ? $payroll->staff->formatted_department : 'N/A' }}</span></div>
              <div class="ps-row"><span class="ps-lbl">Designation</span>      <span class="ps-val">{{ $payroll->staff ? $payroll->staff->formatted_designation : 'N/A' }}</span></div>
              <div class="ps-row"><span class="ps-lbl">Mobile No.</span>       <span class="ps-val">{{ $payroll->staff ? $payroll->staff->mobile_no : 'N/A' }}</span></div>
            </div>
          </div>
        </div>
        {{-- Payment & Bank Details --}}
        <div class="ps-col">
          <div class="ps-card">
            <div class="ps-card-head">💳 Payment &amp; Banking</div>
            <div class="ps-card-body">
              <div class="ps-row"><span class="ps-lbl">Payment Method</span>   <span class="ps-val" style="text-transform:capitalize;">{{ str_replace('_', ' ', $payroll->payment_method) }}</span></div>
              <div class="ps-row"><span class="ps-lbl">Payment Status</span>   <span class="ps-val" style="text-transform:uppercase;font-weight:800;">{{ $status }}</span></div>
              <div class="ps-row"><span class="ps-lbl">Payment Date</span>     <span class="ps-val">{{ $payroll->payment_date ? $payroll->payment_date->format('d M Y') : 'Pending' }}</span></div>
              <div class="ps-row"><span class="ps-lbl">Bank Name</span>        <span class="ps-val">{{ $payroll->staff && $payroll->staff->bank_name ? $payroll->staff->bank_name : 'Cash Payment' }}</span></div>
              <div class="ps-row"><span class="ps-lbl">Account / IBAN</span>   <span class="ps-val">{{ $payroll->staff && $payroll->staff->bank_account_number ? $payroll->staff->bank_account_number : ($payroll->staff->iban ?? 'N/A') }}</span></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Finance summary cards --}}
      <div class="ps-fin-row">
        <div class="ps-fin-cell ps-fc-basic">
          <div class="ps-fin-label">Basic Salary</div>
          <div class="ps-fin-amt">Rs. {{ number_format($payroll->basic_salary, 2) }}</div>
          <div class="ps-fin-sub">Base pay rate</div>
        </div>
        <div class="ps-fin-cell ps-fc-allow">
          <div class="ps-fin-label">Allowances (+)</div>
          <div class="ps-fin-amt">+ Rs. {{ number_format($payroll->allowance, 2) }}</div>
          <div class="ps-fin-sub">Benefits &amp; additions</div>
        </div>
        <div class="ps-fin-cell ps-fc-deduct">
          <div class="ps-fin-label">Deductions (−)</div>
          <div class="ps-fin-amt">− Rs. {{ number_format($payroll->deduction, 2) }}</div>
          <div class="ps-fin-sub">Taxes / fines / absences</div>
        </div>
      </div>

      {{-- Net Pay Box --}}
      <div class="ps-net-box">
        <div>
          <div class="ps-net-box-label">Total Net Payable Salary</div>
          <div class="ps-net-box-sub">Basic {{ number_format($payroll->basic_salary,2) }} + Allow. {{ number_format($payroll->allowance,2) }} − Deduct. {{ number_format($payroll->deduction,2) }}</div>
        </div>
        <div class="ps-net-box-amount {{ $payroll->net_salary < 0 ? 'text-danger' : '' }}">{{ $payroll->formatted_net_salary }}</div>
      </div>

      {{-- Notes --}}
      @if($payroll->notes)
        <div class="ps-notes">
          <span class="ps-notes-title">📝 Remarks / Notes</span>{{ $payroll->notes }}
        </div>
      @endif

      <hr class="ps-divider">

      {{-- Signatures --}}
      <div class="ps-sig-row">
        <div class="ps-sig-cell">
          <span class="ps-sig-space"></span>
          <div class="ps-sig-line"></div>
          <div class="ps-sig-name">Accounts Officer</div>
          <div class="ps-sig-role">Prepared By</div>
        </div>
        <div class="ps-sig-cell">
          @if($schoolInfo && $schoolInfo->stamp_url)
            <img src="{{ $schoolInfo->stamp_url }}" alt="Stamp" class="ps-sig-stamp"><br>
          @else
            <span class="ps-sig-space"></span>
          @endif
          <div class="ps-sig-line"></div>
          <div class="ps-sig-name">Official Seal</div>
          <div class="ps-sig-role">School Stamp</div>
        </div>
        <div class="ps-sig-cell">
          <span class="ps-sig-space"></span>
          <div class="ps-sig-line"></div>
          <div class="ps-sig-name">{{ $schoolInfo->principal_name ?? 'Principal / Director' }}</div>
          <div class="ps-sig-role">Authorized Signatory</div>
        </div>
      </div>

    </div>{{-- /ps-body --}}

    {{-- Footer --}}
    <div class="ps-footer">
      <span>{{ $schoolInfo->school_name ?? 'School ERP' }} &bull; {{ $schoolInfo->full_address ?? '' }}</span>
      <span>Generated: {{ now()->format('d-M-Y h:i A') }} &bull; Payroll Month: {{ $payroll->payroll_month }}</span>
    </div>

  </div>{{-- /print-a4-payslip --}}
@endsection

