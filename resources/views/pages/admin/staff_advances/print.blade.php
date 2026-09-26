<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @php
    $staff = $advance->staff;
    $staffName = $staff ? $staff->full_name : 'Staff Member';
    $invNo = 'ADV-' . str_pad($advance->id, 5, '0', STR_PAD_LEFT);
    $statusVal = is_object($advance->status) ? $advance->status->value : $advance->status;
    $statusText = strtoupper(str_replace('_', ' ', $statusVal));
  @endphp
  <title>Salary Advance Invoice — {{ $staffName }} ({{ $invNo }})</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* ===== RESET & COLOR SYSTEM =====
       Admission Form Color Scheme:
       - Dark Accent: #3d1a06 (Deep Mahogany Brown)
       - Light Background: #fdfaf3 (Warm Parchment/Cream)
       - Border/Gold Accent: #c7ad8d (Warm Bronze/Gold)
       - Surface Card BG: #ffffff
    ================================== */
    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      width: 100%;
      min-height: 100%;
      background: #e2e8f0;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #3d1a06;
      line-height: 1.4;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    @page {
      size: A4 portrait;
      margin: 6mm 8mm;
    }

    @media print {
      html, body {
        background: #ffffff !important;
        width: 210mm !important;
        height: 297mm !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .no-print {
        display: none !important;
      }
      .page-container {
        width: 100% !important;
        max-height: 285mm !important;
        margin: 0 !important;
        padding: 5mm 6mm !important;
        box-shadow: none !important;
        border: 1.5px solid #c7ad8d !important;
        border-radius: 0 !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        overflow: hidden !important;
        background: #fdfaf3 !important;
      }
    }

    /* ===== TOP SCREEN ACTION BAR ===== */
    .action-bar {
      width: 210mm;
      margin: 14px auto 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #3d1a06;
      padding: 12px 20px;
      border-radius: 12px;
      border: 1px solid #c7ad8d;
      box-shadow: 0 10px 25px rgba(61, 26, 6, 0.25);
    }

    .action-bar .status-info {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #fdfaf3;
      font-size: 13px;
      font-weight: 700;
    }

    .action-bar .status-badge {
      background: #c7ad8d;
      color: #3d1a06;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .btn-group-actions {
      display: flex;
      gap: 10px;
    }

    .btn-act {
      padding: 8px 18px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 12px;
      cursor: pointer;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-print {
      background: #22c55e;
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }
    .btn-print:hover {
      background: #16a34a;
      transform: translateY(-1px);
    }

    .btn-back {
      background: #c7ad8d;
      color: #3d1a06;
    }
    .btn-back:hover {
      background: #b89b78;
    }

    .btn-close-act {
      background: #ef4444;
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .btn-close-act:hover {
      background: #dc2626;
      transform: translateY(-1px);
    }

    /* ===== MAIN A4 PAGE CONTAINER ===== */
    .page-container {
      width: 210mm;
      margin: 14px auto 24px;
      background: #fdfaf3;
      border: 1px solid #c7ad8d;
      border-radius: 16px;
      padding: 7mm 8mm;
      box-shadow: 0 15px 35px rgba(61, 26, 6, 0.12);
      position: relative;
    }

    /* ===== MODERN HERO HEADER ===== */
    .header-card {
      background: #ffffff;
      border: 1px solid #c7ad8d;
      border-radius: 12px;
      padding: 12px 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 14px;
      margin-bottom: 10px;
      box-shadow: 0 2px 8px rgba(61, 26, 6, 0.04);
    }

    .header-brand {
      display: flex;
      align-items: center;
      gap: 14px;
      flex: 1;
    }

    .brand-logo-box {
      width: 64px;
      height: 64px;
      border-radius: 12px;
      background: #fdfaf3;
      border: 1px solid #c7ad8d;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4px;
      flex-shrink: 0;
    }

    .brand-logo {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    .brand-details .bismillah {
      font-size: 11px;
      font-weight: 700;
      color: #3d1a06;
      margin-bottom: 1px;
    }

    .brand-details .school-title {
      font-size: 17px;
      font-weight: 800;
      color: #3d1a06;
      letter-spacing: -0.3px;
      line-height: 1.15;
    }

    .brand-details .school-sub {
      font-size: 9px;
      font-weight: 600;
      color: #6d4b32;
      margin-top: 2px;
    }

    .header-voucher-meta {
      display: flex;
      align-items: center;
      gap: 12px;
      text-align: right;
    }

    .voucher-pill-container {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
    }

    .voucher-pill {
      background: #3d1a06;
      color: #ffffff;
      padding: 4px 12px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      margin-bottom: 4px;
    }

    .voucher-num {
      font-size: 13px;
      font-weight: 800;
      color: #3d1a06;
    }

    .voucher-date {
      font-size: 9.5px;
      font-weight: 600;
      color: #6d4b32;
    }

    .staff-avatar-box {
      width: 58px;
      height: 64px;
      border-radius: 10px;
      border: 1.5px solid #c7ad8d;
      background: #fdfaf3;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-size: 8px;
      font-weight: 700;
      color: #3d1a06;
      text-align: center;
      flex-shrink: 0;
    }

    .staff-avatar-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* ===== FINANCIAL KPI HERO CARDS ===== */
    .kpi-grid {
      display: flex;
      gap: 10px;
      margin-bottom: 10px;
    }

    .kpi-card {
      flex: 1;
      background: #ffffff;
      border: 1px solid #c7ad8d;
      border-radius: 10px;
      padding: 9px 12px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .kpi-card .kpi-label {
      font-size: 8.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #6d4b32;
      margin-bottom: 2px;
    }

    .kpi-card .kpi-value {
      font-size: 15px;
      font-weight: 800;
      color: #3d1a06;
      line-height: 1.1;
    }

    .kpi-card .kpi-sub {
      font-size: 8px;
      font-weight: 500;
      color: #8c6a4f;
      margin-top: 2px;
    }

    .kpi-card.kpi-highlight {
      background: #3d1a06;
      border-color: #3d1a06;
    }

    .kpi-card.kpi-highlight .kpi-label {
      color: #c7ad8d;
    }

    .kpi-card.kpi-highlight .kpi-value {
      color: #ffffff;
    }

    .kpi-card.kpi-highlight .kpi-sub {
      color: #e5d6c5;
    }

    /* ===== MODERN SECTION HEADERS ===== */
    .section-header-modern {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 8px;
      margin-bottom: 4px;
    }

    .section-header-modern .badge-num {
      background: #3d1a06;
      color: #ffffff;
      width: 18px;
      height: 18px;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 9px;
      font-weight: 800;
      flex-shrink: 0;
    }

    .section-header-modern h3 {
      font-size: 10px;
      font-weight: 800;
      color: #3d1a06;
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .section-header-modern .header-line {
      flex: 1;
      height: 1px;
      background: #c7ad8d;
      opacity: 0.6;
    }

    /* ===== MODERN DATA GRID TABLE ===== */
    table.grid-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      font-size: 9px;
      margin-bottom: 6px;
      background: #ffffff;
      border: 1px solid #c7ad8d;
      border-radius: 8px;
      overflow: hidden;
    }

    table.grid-table th {
      background: #3d1a06;
      color: #ffffff;
      font-size: 8.5px;
      font-weight: 700;
      padding: 5px 8px;
      text-align: left;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    table.grid-table td {
      padding: 4px 8px;
      border-bottom: 1px solid #f0e8dd;
      border-right: 1px solid #f0e8dd;
      vertical-align: middle;
    }

    table.grid-table tr:last-child td {
      border-bottom: none;
    }

    table.grid-table td:last-child {
      border-right: none;
    }

    .lbl-cell {
      font-weight: 700;
      color: #6d4b32;
      background: #fdfaf3;
      width: 16%;
      font-size: 8.5px;
    }

    .val-cell {
      color: #1a0b03;
      font-weight: 600;
      width: 34%;
    }

    /* ===== RECOVERY PROGRESS TRACK ===== */
    .progress-bar-card {
      background: #ffffff;
      border: 1px solid #c7ad8d;
      border-radius: 8px;
      padding: 5px 10px;
      margin-bottom: 6px;
    }

    .progress-info {
      display: flex;
      justify-content: space-between;
      font-size: 8.5px;
      font-weight: 700;
      color: #3d1a06;
      margin-bottom: 3px;
    }

    .progress-track {
      height: 6px;
      background: #f0e8dd;
      border-radius: 10px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #6d4b32 0%, #3d1a06 100%);
      border-radius: 10px;
    }

    /* ===== REMARKS & NOTES CARD ===== */
    .notes-card {
      background: #ffffff;
      border: 1px solid #c7ad8d;
      border-left: 3.5px solid #3d1a06;
      border-radius: 6px;
      padding: 6px 10px;
      margin-bottom: 6px;
      font-size: 8.5px;
    }

    .notes-card strong {
      color: #3d1a06;
      display: block;
      margin-bottom: 2px;
      font-weight: 800;
    }

    /* ===== SIGNATURES ROW ===== */
    .signatures-grid {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: 10px;
      gap: 12px;
    }

    .sig-box {
      flex: 1;
      background: #ffffff;
      border: 1px dashed #c7ad8d;
      border-radius: 8px;
      padding: 8px 10px;
      text-align: center;
    }

    .sig-line {
      border-top: 1.5px solid #3d1a06;
      margin: 20px auto 4px;
      width: 80%;
    }

    .sig-name {
      font-size: 9px;
      font-weight: 800;
      color: #3d1a06;
    }

    .sig-role {
      font-size: 7.5px;
      font-weight: 600;
      color: #6d4b32;
    }

    .stamp-img {
      max-width: 40px;
      max-height: 40px;
      object-fit: contain;
      margin-bottom: 2px;
      opacity: 0.9;
    }

    /* ===== FOOTER BANNER ===== */
    .page-footer {
      text-align: center;
      font-size: 7.5px;
      font-weight: 600;
      color: #6d4b32;
      margin-top: 8px;
      padding-top: 4px;
      border-top: 1px solid #c7ad8d;
    }
  </style>
</head>
<body>

<!-- Screen Top Action Bar -->
<div class="action-bar no-print">
  <div class="status-info">
    <span>💳 Salary Advance Voucher &bull; {{ $invNo }}</span>
    <span class="status-badge">{{ $statusText }}</span>
  </div>
  <div class="btn-group-actions">
    <button onclick="window.print()" class="btn-act btn-print">
      🖨️ Print Invoice
    </button>
    <button onclick="window.close(); setTimeout(function(){ if(!window.closed){ window.history.back(); } }, 200);" class="btn-act btn-close-act">
      ✖ Close
    </button>
    <a href="{{ route('staff-advances.show', $advance->id) }}" class="btn-act btn-back">
      📋 View Record
    </a>
    <a href="{{ route('staff-advances.index') }}" class="btn-act btn-back">
      ⬅ Advances List
    </a>
  </div>
</div>

<div class="page-container">

  <!-- ===== HERO HEADER ===== -->
  <div class="header-card">
    <div class="header-brand">
      <div class="brand-logo-box">
        <img src="{{ $schoolInfo && $schoolInfo->logo_url ? $schoolInfo->logo_url : asset('assets/images/logo.png') }}" alt="School Logo" class="brand-logo" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
      </div>
      <div class="brand-details">
        <div class="bismillah">رَّبِّ زِدْنِي عِلْمًا</div>
        <div class="school-title">{{ $schoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}</div>
        <div class="school-sub">
          {{ $schoolInfo->tagline ?? 'DISCIPLINE | EDUCATION | EXCELLENCE' }}
          @if($schoolInfo && $schoolInfo->phone) &bull; Phone: {{ $schoolInfo->phone }} @endif
        </div>
      </div>
    </div>

    <div class="header-voucher-meta">
      <div class="voucher-pill-container">
        <div class="voucher-pill">ADVANCE VOUCHER</div>
        <div class="voucher-num">{{ $invNo }}</div>
        <div class="voucher-date">Date: {{ $advance->advance_date ? $advance->advance_date->format('d/m/Y') : now()->format('d/m/Y') }}</div>
      </div>

      <div class="staff-avatar-box">
        @if($staff && $staff->profile_picture)
          <img src="{{ asset('storage/' . $staff->profile_picture) }}" alt="Staff Photo">
        @else
          STAFF<br>PHOTO
        @endif
      </div>
    </div>
  </div>

  <!-- ===== FINANCIAL KPI HERO CARDS ===== -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Current Voucher Advance</div>
      <div class="kpi-value">Rs. {{ number_format($advance->advance_amount, 2) }}</div>
      <div class="kpi-sub">Voucher Disbursed Amount</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Current Voucher Outstanding</div>
      <div class="kpi-value" style="color: #c2410c;">Rs. {{ number_format($advance->remaining_balance, 2) }}</div>
      <div class="kpi-sub">Pending Voucher Recovery</div>
    </div>
    @if(isset($previousUnpaidAdvances) && $previousUnpaidAdvances->count() > 0)
    <div class="kpi-card">
      <div class="kpi-label">Previous Unpaid Advances</div>
      <div class="kpi-value" style="color: #dc2626;">Rs. {{ number_format($previousUnpaidTotalBalance, 2) }}</div>
      <div class="kpi-sub">{{ $previousUnpaidAdvances->count() }} Unpaid Advance Record(s)</div>
    </div>
    <div class="kpi-card kpi-highlight">
      <div class="kpi-label">Total Combined Outstanding</div>
      <div class="kpi-value">Rs. {{ number_format($advance->remaining_balance + $previousUnpaidTotalBalance, 2) }}</div>
      <div class="kpi-sub">Current + Previous Pending Balance</div>
    </div>
    @else
    <div class="kpi-card kpi-highlight">
      <div class="kpi-label">Outstanding Balance</div>
      <div class="kpi-value">Rs. {{ number_format($advance->remaining_balance, 2) }}</div>
      <div class="kpi-sub">Pending Recovery</div>
    </div>
    @endif
  </div>

  <!-- ===== 1. STAFF PERSONAL INFORMATION ===== -->
  <div class="section-header-modern">
    <div class="badge-num">1</div>
    <h3>Staff Personal Information</h3>
    <div class="header-line"></div>
  </div>
  <table class="grid-table">
    <tr>
      <td class="lbl-cell">Full Name</td>
      <td class="val-cell">{{ $staff ? $staff->full_name : 'N/A' }}</td>
      <td class="lbl-cell">Staff Code / ID</td>
      <td class="val-cell">{{ $staff ? ($staff->staff_id ? 'STF-' . $staff->staff_id : 'STF-' . $staff->id) : 'N/A' }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">CNIC Number</td>
      <td class="val-cell">{{ $staff->cnic ?? 'N/A' }}</td>
      <td class="lbl-cell">Gender / Marital</td>
      <td class="val-cell">{{ ucfirst($staff->gender ?? 'N/A') }} / {{ ucfirst($staff->marital_status ?? 'N/A') }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">Date of Birth</td>
      <td class="val-cell">{{ $staff->dob ? \Carbon\Carbon::parse($staff->dob)->format('d M, Y') : 'N/A' }}</td>
      <td class="lbl-cell">Religion / National</td>
      <td class="val-cell">{{ ucfirst($staff->religion ?? 'Islam') }} / {{ ucfirst($staff->nationality ?? 'Pakistani') }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">Primary Mobile</td>
      <td class="val-cell">{{ $staff->mobile_no ?? 'N/A' }}</td>
      <td class="lbl-cell">Alternate Mobile</td>
      <td class="val-cell">{{ $staff->alternate_mobile_no ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">Email Address</td>
      <td class="val-cell">{{ $staff->email ?? 'N/A' }}</td>
      <td class="lbl-cell">Qualification / Exp.</td>
      <td class="val-cell">{{ $staff->qualification ?? 'N/A' }} @if($staff->experience) ({{ $staff->experience }}) @endif</td>
    </tr>
    <tr>
      <td class="lbl-cell">Current Address</td>
      <td class="val-cell" colspan="3">{{ $staff->current_address ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">Permanent Address</td>
      <td class="val-cell" colspan="3">{{ $staff->permanent_address ?? ($staff->current_address ?? 'N/A') }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">Emergency Contact</td>
      <td class="val-cell" colspan="3">
        {{ $staff->emergency_contact_name ?? 'N/A' }} 
        @if($staff->emergency_contact_relation) ({{ $staff->emergency_contact_relation }}) @endif
        @if($staff->emergency_contact_number) &bull; Mob: {{ $staff->emergency_contact_number }} @endif
      </td>
    </tr>
  </table>

  <!-- ===== 2. EMPLOYMENT & SALARY DETAILS ===== -->
  <div class="section-header-modern">
    <div class="badge-num">2</div>
    <h3>Employment &amp; Banking Profile</h3>
    <div class="header-line"></div>
  </div>
  <table class="grid-table">
    <tr>
      <td class="lbl-cell">Department</td>
      <td class="val-cell">{{ $staff ? $staff->formatted_department : 'N/A' }}</td>
      <td class="lbl-cell">Designation</td>
      <td class="val-cell">{{ $staff ? $staff->formatted_designation : 'N/A' }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">Employment Type</td>
      <td class="val-cell">{{ ucwords(str_replace('_', ' ', $staff->employment_type ?? 'Full Time')) }}</td>
      <td class="lbl-cell">Work Shift</td>
      <td class="val-cell">{{ ucfirst($staff->shift ?? 'Morning') }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">Joining Date</td>
      <td class="val-cell">{{ $staff->joining_date ? \Carbon\Carbon::parse($staff->joining_date)->format('d M, Y') : 'N/A' }}</td>
      <td class="lbl-cell">Basic Monthly Salary</td>
      <td class="val-cell" style="font-weight: 800; color: #3d1a06;">
        Rs. {{ number_format($staff->salary ?? 0, 2) }} <span style="font-size: 8px; font-weight: normal; color: #6d4b32;">({{ ucwords(str_replace('_', ' ', $staff->salary_type ?? 'Monthly')) }})</span>
      </td>
    </tr>
    <tr>
      <td class="lbl-cell">Bank Name</td>
      <td class="val-cell">{{ $staff->bank_name ?? 'N/A' }}</td>
      <td class="lbl-cell">Account Title</td>
      <td class="val-cell">{{ $staff->bank_account_title ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td class="lbl-cell">Account Number</td>
      <td class="val-cell">{{ $staff->bank_account_number ?? 'N/A' }}</td>
      <td class="lbl-cell">IBAN Number</td>
      <td class="val-cell">{{ $staff->iban ?? 'N/A' }}</td>
    </tr>
  </table>

  <!-- ===== 3. ADVANCE TRANSACTION & LEDGER ===== -->
  <div class="section-header-modern">
    <div class="badge-num">3</div>
    <h3>Current Voucher Disbursement &amp; Financial Ledger</h3>
    <div class="header-line"></div>
  </div>
  <table class="grid-table">
    <thead>
      <tr>
        <th style="width: 44%;">TRANSACTION DETAILS</th>
        <th style="width: 18%; text-align: right;">DEBIT (RS.)</th>
        <th style="width: 18%; text-align: right;">CREDIT (RS.)</th>
        <th style="width: 20%; text-align: right;">BALANCE (RS.)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <strong>Advance Disbursed</strong> &bull; {{ $advance->reason ?? 'Personal Advance' }}<br>
          <span style="font-size: 8px; color: #6d4b32;">Issued via {{ ucwords(str_replace('_', ' ', $advance->payment_method)) }} on {{ $advance->advance_date ? $advance->advance_date->format('d M, Y') : 'N/A' }}</span>
        </td>
        <td style="text-align: right; font-weight: 700; color: #3d1a06;">{{ number_format($advance->advance_amount, 2) }}</td>
        <td style="text-align: right; color: #999;">—</td>
        <td style="text-align: right; font-weight: 800; color: #3d1a06;">{{ number_format($advance->advance_amount, 2) }}</td>
      </tr>
      @php
        $repaymentList = isset($staffRepayments) ? $staffRepayments : $advance->repayments;
        $runningBal = (float)$advance->advance_amount;
      @endphp
      @foreach($repaymentList as $repay)
        @php
          $runningBal = max(0, $runningBal - (float)$repay->amount);
        @endphp
        <tr>
          <td>
            <strong>Repayment Entry</strong> &bull; {{ $repay->repayment_type === 'payroll_deduction' ? 'Payroll Auto Deduction' : ucwords(str_replace('_', ' ', $repay->repayment_type)) }}<br>
            <span style="font-size: 8px; color: #6d4b32;">
              Date: {{ $repay->repayment_date ? $repay->repayment_date->format('d M, Y') : 'N/A' }}
              @if($repay->payroll) &bull; Month: {{ $repay->payroll->payroll_month }} @endif
              @if($repay->notes) &bull; {{ $repay->notes }} @endif
            </span>
          </td>
          <td style="text-align: right; color: #999;">—</td>
          <td style="text-align: right; font-weight: 700; color: #16a34a;">{{ number_format($repay->amount, 2) }}</td>
          <td style="text-align: right; font-weight: 800; color: #3d1a06;">{{ number_format($runningBal, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td class="lbl-cell" style="background: #3d1a06; color: #ffffff; font-weight: 800;">TOTAL SANCTIONED ADVANCE</td>
        <td class="val-cell" style="text-align: right; font-weight: 800;">Rs. {{ number_format($advance->advance_amount, 2) }}</td>
        <td class="lbl-cell" style="background: #3d1a06; color: #ffffff; font-weight: 800;">RECOVERED</td>
        <td class="val-cell" style="text-align: right; font-weight: 800; color: #16a34a;">Rs. {{ number_format($advance->repaid_amount, 2) }}</td>
      </tr>
      <tr>
        <td colspan="3" class="lbl-cell" style="background: #3d1a06; color: #ffffff; text-align: right; font-weight: 800;">OUTSTANDING RECOVERY BALANCE:</td>
        <td class="val-cell" style="text-align: right; font-weight: 900; background: #fdfaf3; color: #3d1a06; font-size: 11px;">
          Rs. {{ number_format($advance->remaining_balance, 2) }}
        </td>
      </tr>
    </tfoot>
  </table>

  <!-- Recovery Progress -->
  <div class="progress-bar-card">
    <div class="progress-info">
      <span>CURRENT VOUCHER RECOVERY PROGRESS</span>
      <span>{{ $advance->repayment_progress }}% RECOVERED</span>
    </div>
    <div class="progress-track">
      <div class="progress-fill" style="width: {{ $advance->repayment_progress }}%;"></div>
    </div>
  </div>

  <!-- ===== 4. PREVIOUS UNPAID ADVANCES OF STAFF ===== -->
  @if(isset($previousUnpaidAdvances) && $previousUnpaidAdvances->count() > 0)
  <div class="section-header-modern">
    <div class="badge-num">4</div>
    <h3>Previous Unpaid Advances &amp; Outstanding Loan History</h3>
    <div class="header-line"></div>
  </div>
  <table class="grid-table">
    <thead>
      <tr>
        <th style="width: 15%;">VOUCHER NO</th>
        <th style="width: 15%;">ADVANCE DATE</th>
        <th style="width: 25%;">REASON / PURPOSE</th>
        <th style="width: 15%; text-align: right;">SANCTIONED (RS.)</th>
        <th style="width: 15%; text-align: right;">REPAID (RS.)</th>
        <th style="width: 15%; text-align: right;">UNPAID BALANCE (RS.)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($previousUnpaidAdvances as $prevAdv)
        @php
          $prevBalance = max(0, (float)$prevAdv->advance_amount - (float)$prevAdv->repaid_amount);
          $prevInvNo = 'ADV-' . str_pad($prevAdv->id, 5, '0', STR_PAD_LEFT);
        @endphp
        <tr>
          <td style="font-weight: 700; color: #3d1a06;">{{ $prevInvNo }}</td>
          <td>{{ $prevAdv->advance_date ? $prevAdv->advance_date->format('d M, Y') : 'N/A' }}</td>
          <td>{{ $prevAdv->reason ?? 'Personal Loan / Advance' }}</td>
          <td style="text-align: right;">{{ number_format($prevAdv->advance_amount, 2) }}</td>
          <td style="text-align: right; color: #16a34a;">{{ number_format($prevAdv->repaid_amount, 2) }}</td>
          <td style="text-align: right; font-weight: 800; color: #dc2626;">Rs. {{ number_format($prevBalance, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" class="lbl-cell" style="background: #3d1a06; color: #ffffff; text-align: right; font-weight: 800;">TOTAL PREVIOUS UNPAID ADVANCES BALANCE:</td>
        <td class="val-cell" style="text-align: right; font-weight: 900; background: #fdfaf3; color: #dc2626; font-size: 10px;">
          Rs. {{ number_format($previousUnpaidTotalBalance, 2) }}
        </td>
      </tr>
      <tr>
        <td colspan="5" class="lbl-cell" style="background: #3d1a06; color: #ffffff; text-align: right; font-weight: 800;">CUMULATIVE TOTAL PENDING RECOVERY (ALL ADVANCES):</td>
        <td class="val-cell" style="text-align: right; font-weight: 900; background: #3d1a06; color: #ffffff; font-size: 11px;">
          Rs. {{ number_format($advance->remaining_balance + $previousUnpaidTotalBalance, 2) }}
        </td>
      </tr>
    </tfoot>
  </table>
  @endif

  <!-- ===== 5. NOTES & REMARKS ===== -->
  @if($advance->notes || ($staff && $staff->note))
  <div class="notes-card">
    <strong>Remarks &amp; Disbursement Notes:</strong>
    @if($advance->notes) <div>&bull; {{ $advance->notes }}</div> @endif
    @if($staff && $staff->note) <div>&bull; Staff Note: {{ $staff->note }}</div> @endif
  </div>
  @endif

  <!-- ===== SIGNATURES ROW ===== -->
  <div class="signatures-grid">
    <div class="sig-box">
      <div class="sig-line"></div>
      <div class="sig-name">{{ $staffName }}</div>
      <div class="sig-role">Employee Signature</div>
    </div>
    <div class="sig-box">
      @if($schoolInfo && $schoolInfo->stamp_url)
        <img src="{{ $schoolInfo->stamp_url }}" alt="Stamp" class="stamp-img"><br>
      @endif
      <div class="sig-line" style="margin-top: {{ $schoolInfo && $schoolInfo->stamp_url ? '4px' : '20px' }};"></div>
      <div class="sig-name">Accounts Department</div>
      <div class="sig-role">Verified &amp; Disbursed By</div>
    </div>
    <div class="sig-box">
      <div class="sig-line"></div>
      <div class="sig-name">{{ $schoolInfo->principal_name ?? 'Principal / Director' }}</div>
      <div class="sig-role">Authorized Signatory &amp; Stamp</div>
    </div>
  </div>

  <!-- ===== FOOTER ===== -->
  <div class="page-footer">
    {{ $schoolInfo->school_name ?? 'School Management System' }} &bull; {{ $schoolInfo->full_address ?? '' }} &bull; Generated on {{ now()->format('d-M-Y h:i A') }}
  </div>

</div>

</body>
</html>

