<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll Statement - {{ $selectedStaff ? $selectedStaff->full_name : ($globalSchoolInfo->school_name ?? 'School ERP') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    @page {
      size: A4 portrait;
      margin: 10mm 12mm;
    }

    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      font-size: 8.5pt;
      color: #0f172a;
      background-color: #ffffff;
      margin: 0;
      padding: 0;
      line-height: 1.3;
    }

    .statement-container {
      width: 100%;
      margin: 0 auto;
    }

    /* HEADER */
    .header-table {
      width: 100%;
      border-collapse: collapse;
      border-bottom: 2px solid #2563eb;
      padding-bottom: 10px;
      margin-bottom: 12px;
    }

    .header-table td {
      vertical-align: middle;
    }

    .brand-logo {
      max-height: 70px;
      max-width: 90px;
      object-fit: contain;
    }

    .school-title {
      font-size: 16pt;
      font-weight: 800;
      color: #1e40af;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0 0 2px 0;
    }

    .school-code {
      font-size: 9pt;
      font-weight: 600;
      color: #64748b;
      margin: 0 0 4px 0;
    }

    .school-meta {
      font-size: 8pt;
      color: #475569;
    }

    .statement-title-badge {
      background: #1e40af;
      color: #ffffff;
      text-align: center;
      padding: 6px 12px;
      font-weight: 700;
      font-size: 11pt;
      letter-spacing: 1px;
      text-transform: uppercase;
      border-radius: 4px;
      margin-bottom: 14px;
    }

    /* STAFF / SCOPE PROFILE BOX */
    .scope-box {
      width: 100%;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      background-color: #f8fafc;
      padding: 8px 12px;
      margin-bottom: 14px;
    }

    .scope-box table {
      width: 100%;
      border-collapse: collapse;
      font-size: 8.5pt;
    }

    .scope-box td {
      padding: 3px 6px;
      vertical-align: top;
    }

    /* SUMMARY CARDS GRID */
    .summary-grid {
      display: table;
      width: 100%;
      margin-bottom: 14px;
      border-collapse: separate;
      border-spacing: 8px;
    }

    .summary-card {
      display: table-cell;
      width: 20%;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      padding: 8px;
      text-align: center;
    }

    .summary-card.highlight-success {
      background: #f0fdf4;
      border-color: #bbf7d0;
    }

    .summary-card.highlight-warning {
      background: #fffbeb;
      border-color: #fef3c7;
    }

    .summary-label {
      font-size: 7pt;
      font-weight: 700;
      text-transform: uppercase;
      color: #64748b;
      margin-bottom: 4px;
      display: block;
    }

    .summary-val {
      font-size: 10pt;
      font-weight: 800;
      color: #0f172a;
    }

    /* TABLE */
    .statement-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .statement-table th,
    .statement-table td {
      border: 1px solid #cbd5e1;
      padding: 5px 7px;
      font-size: 8pt;
      vertical-align: middle;
    }

    .statement-table th {
      background-color: #1e40af;
      color: #ffffff;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 7.5pt;
      text-align: left;
    }

    .statement-table tr:nth-child(even) {
      background-color: #f8fafc;
    }

    .text-end {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .badge-status {
      display: inline-block;
      padding: 2px 6px;
      font-size: 7pt;
      font-weight: 700;
      border-radius: 3px;
      text-transform: uppercase;
    }

    .status-paid { background: #dcfce7; color: #166534; }
    .status-pending { background: #fef9c3; color: #854d0e; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }

    /* FOOTER SIGNATURES */
    .signatures-table {
      width: 100%;
      margin-top: 35px;
      border-collapse: collapse;
    }

    .signatures-table td {
      width: 33%;
      text-align: center;
      vertical-align: bottom;
    }

    .sig-line {
      border-top: 1.5px dashed #64748b;
      width: 80%;
      margin: 0 auto 4px auto;
    }

    .sig-title {
      font-size: 8.5pt;
      font-weight: 700;
      color: #334155;
    }

    .stamp-seal {
      width: 70px;
      height: 70px;
      opacity: 0.85;
    }

    /* PRINT ACTION CONTROLS */
    .no-print-toolbar {
      background: #1e293b;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .btn-print {
      background: #2563eb;
      color: white;
      border: none;
      padding: 6px 16px;
      font-weight: 600;
      border-radius: 4px;
      cursor: pointer;
    }

    @media print {
      .no-print-toolbar {
        display: none !important;
      }
    }
  </style>
</head>
<body>

  <div class="no-print-toolbar">
    <div>
      <strong>Payroll Statement Executive Print View</strong> &bull; {{ date('d M Y, h:i A') }}
    </div>
    <div>
      <button class="btn-print" onclick="window.print()">Print Statement</button>
      <button class="btn-print" style="background:#64748b;" onclick="window.close()">Close Window</button>
    </div>
  </div>

  <div class="statement-container">

    <!-- SCHOOL HEADER -->
    <table class="header-table">
      <tr>
        <td style="width: 80px;">
          @if($globalSchoolInfo->logo_url)
            <img src="{{ $globalSchoolInfo->logo_url }}" alt="Logo" class="brand-logo">
          @endif
        </td>
        <td style="padding-left: 10px;">
          <h1 class="school-title">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}</h1>
          @if($globalSchoolInfo->school_code)
            <div class="school-code">{{ $globalSchoolInfo->school_code }}</div>
          @endif
          <div class="school-meta">
            {{ $globalSchoolInfo->full_address ?? 'Dhanote, District Lodhran' }}<br>
            Phone: {{ $globalSchoolInfo->phone ?? '03266850002' }} | Email: {{ $globalSchoolInfo->email ?? 'Superiorschoolnps@gmail.com' }}
          </div>
        </td>
        <td class="text-end" style="width: 180px; font-size: 8pt; color: #475569;">
          <strong>Date Generated:</strong> {{ date('d-M-Y') }}<br>
          <strong>Period Scope:</strong> {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Start' }} - {{ $endDate ? date('d/m/Y', strtotime($endDate)) : date('d/m/Y') }}
        </td>
      </tr>
    </table>

    <!-- STATEMENT TITLE BANNER -->
    <div class="statement-title-badge">
      Official Payroll Disbursement Statement & Audit Ledger
    </div>

    <!-- SCOPE DETAILS BOX -->
    <div class="scope-box">
      <table>
        <tr>
          <td style="width: 50%;">
            <strong>Target Staff:</strong> 
            {{ $selectedStaff ? $selectedStaff->full_name . ' (' . $selectedStaff->staff_id . ')' : 'All Active Staff Members' }}
          </td>
          <td style="width: 50%;">
            <strong>Department Scope:</strong> 
            {{ $department !== 'all' ? ucwords(str_replace(['_', '-'], ' ', $department)) : 'All Departments' }}
          </td>
        </tr>
        <tr>
          <td>
            <strong>Payroll Month:</strong> {{ $month !== 'all' ? $month : 'All Months' }}
          </td>
          <td>
            <strong>Filter Status:</strong> <span style="text-transform: uppercase; font-weight: bold;">{{ $status }}</span>
          </td>
        </tr>
        @if($selectedStaff)
          <tr>
            <td><strong>Designation:</strong> {{ $selectedStaff->designation ?? 'N/A' }}</td>
            <td><strong>Account Details:</strong> {{ $selectedStaff->bank_name ? ($selectedStaff->bank_name . ' - ' . $selectedStaff->account_number) : 'Cash Settlement' }}</td>
          </tr>
        @endif
      </table>
    </div>

    <!-- FINANCIAL SUMMARY GRID -->
    <div class="summary-grid">
      <div class="summary-card">
        <span class="summary-label">Base Salary</span>
        <span class="summary-val">Rs. {{ number_format($totalBasic, 2) }}</span>
      </div>
      <div class="summary-card">
        <span class="summary-label">Allowances (+)</span>
        <span class="summary-val" style="color: #166534;">+Rs. {{ number_format($totalAllowance, 2) }}</span>
      </div>
      <div class="summary-card">
        <span class="summary-label">Deductions (-)</span>
        <span class="summary-val" style="color: #991b1b;">-Rs. {{ number_format($totalDeduction, 2) }}</span>
      </div>
      <div class="summary-card highlight-success">
        <span class="summary-label" style="color: #166534;">Settled Paid</span>
        <span class="summary-val" style="color: #15803d;">Rs. {{ number_format($paidAmount, 2) }}</span>
      </div>
      <div class="summary-card highlight-warning">
        <span class="summary-label" style="color: #854d0e;">Pending Liability</span>
        <span class="summary-val" style="color: #b45309;">Rs. {{ number_format($pendingAmount, 2) }}</span>
      </div>
    </div>

    <!-- TRANSACTION LEDGER TABLE -->
    <table class="statement-table">
      <thead>
        <tr>
          <th style="width: 25px;">#</th>
          <th>Staff Details</th>
          <th>Department</th>
          <th>Month</th>
          <th>Method</th>
          <th class="text-end" style="width: 75px;">Basic</th>
          <th class="text-end" style="width: 70px;">Allow.</th>
          <th class="text-end" style="width: 70px;">Deduc.</th>
          <th class="text-end" style="width: 80px;">Net Pay</th>
          <th class="text-center" style="width: 65px;">Status</th>
          <th style="width: 75px;">Pay Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($payrolls as $index => $p)
          <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>
              <strong>{{ $p->staff ? $p->staff->full_name : 'N/A' }}</strong>
              <br><small style="color: #64748b;">{{ $p->staff ? $p->staff->staff_id : '' }}</small>
            </td>
            <td>{{ $p->staff ? $p->staff->formatted_department : 'N/A' }}</td>
            <td>{{ $p->payroll_month }}</td>
            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $p->payment_method) }}</td>
            <td class="text-end">Rs. {{ number_format($p->basic_salary, 2) }}</td>
            <td class="text-end" style="color: #166534;">+Rs. {{ number_format($p->allowance, 2) }}</td>
            <td class="text-end" style="color: #991b1b;">-Rs. {{ number_format($p->deduction, 2) }}</td>
            <td class="text-end" style="font-weight: 800; color: #1e40af;">Rs. {{ number_format($p->net_salary, 2) }}</td>
            <td class="text-center">
              <span class="badge-status status-{{ $p->status }}">
                {{ $p->status }}
              </span>
            </td>
            <td>{{ $p->payment_date ? $p->payment_date->format('d/m/Y') : 'Pending' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="11" class="text-center" style="padding: 20px; color: #64748b;">
              No payroll records match the specified filter criteria.
            </td>
          </tr>
        @endforelse
      </tbody>
      @if(count($payrolls) > 0)
        <tfoot>
          <tr style="font-weight: 800; background-color: #f1f5f9; border-top: 2px solid #1e40af;">
            <td colspan="5" class="text-end">STATEMENT TOTALS ({{ count($payrolls) }} RECORDS):</td>
            <td class="text-end">Rs. {{ number_format($totalBasic, 2) }}</td>
            <td class="text-end" style="color: #166534;">+Rs. {{ number_format($totalAllowance, 2) }}</td>
            <td class="text-end" style="color: #991b1b;">-Rs. {{ number_format($totalDeduction, 2) }}</td>
            <td class="text-end" style="color: #1e40af; font-size: 8.5pt;">Rs. {{ number_format($totalNetSalary, 2) }}</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      @endif
    </table>

    <!-- SIGNATURES SECTION -->
    <table class="signatures-table">
      <tr>
        <td>
          <div class="sig-line"></div>
          <div class="sig-title">Prepared By (Accounts Officer)</div>
        </td>
        <td>
          @if($globalSchoolInfo->stamp_url)
            <img src="{{ $globalSchoolInfo->stamp_url }}" alt="Stamp" class="stamp-seal"><br>
          @endif
          <div class="sig-title" style="margin-top: 5px;">Official Seal / Stamp</div>
        </td>
        <td>
          <div class="sig-line"></div>
          <div class="sig-title">Approved By (Principal/Director)</div>
        </td>
      </tr>
    </table>

  </div>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      if (window.opener || window.location.search.includes('autoprint=1')) {
        setTimeout(() => {
          window.print();
        }, 500);
      }
    });
  </script>
</body>
</html>
