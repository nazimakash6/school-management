<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll Statement - {{ $selectedStaff ? $selectedStaff->full_name : ($globalSchoolInfo->school_name ?? 'School ERP') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* ===== GLOBAL RESET & PRINT SETUP (ADMISSION FORM BLANK-FORM SCHEME) ===== */
    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    html, body {
      width: 100%;
      min-height: 100%;
      background: #e2e8f0;
      font-family: 'Times New Roman', 'Georgia', serif;
      color: #3d1a06;
      line-height: 1.25;
    }

    @page {
      size: A4 portrait;
      margin: 4mm 5mm 4mm 5mm;
    }

    @media print {
      html, body {
        background: #ffffff !important;
        width: 210mm !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .no-print-toolbar {
        display: none !important;
      }
      .statement-container {
        width: 100% !important;
        margin: 0 !important;
        padding: 4mm 5mm !important;
        box-shadow: none !important;
        border: 2px solid #3d1a06 !important;
        outline: none !important;
        background: #fdfaf3 !important;
      }
    }

    /* ===== TOP ACTION BAR (SCREEN ONLY) ===== */
    .no-print-toolbar {
      width: 210mm;
      margin: 12px auto 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #3d1a06;
      color: #fdfaf3;
      padding: 10px 18px;
      border-radius: 8px;
      border: 1px solid #c7ad8d;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      font-family: Arial, sans-serif;
      font-size: 13px;
    }

    .btn-act {
      padding: 7px 15px;
      border-radius: 6px;
      font-weight: bold;
      font-size: 12px;
      font-family: Arial, sans-serif;
      cursor: pointer;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .btn-print { background: #22c55e; color: #ffffff; }
    .btn-print:hover { background: #16a34a; }
    .btn-close-act { background: #c7ad8d; color: #3d1a06; }
    .btn-close-act:hover { background: #b89b78; }

    /* ===== MAIN A4 CONTAINER ===== */
    .statement-container {
      width: 210mm;
      margin: 12px auto 20px;
      background: #fdfaf3;
      border: 3px double #3d1a06;
      outline: 2px solid #c7ad8d;
      outline-offset: -5px;
      padding: 5mm 6mm;
      box-shadow: 0 10px 30px rgba(61,26,6,0.25);
      position: relative;
    }

    /* HEADER TABLE */
    .header-table {
      width: 100%;
      border-collapse: collapse;
      border-bottom: 2px solid #c7ad8d;
      padding-bottom: 6px;
      margin-bottom: 8px;
    }

    .header-table td {
      vertical-align: middle;
    }

    .brand-logo {
      max-height: 70px;
      max-width: 85px;
      object-fit: contain;
    }

    .school-title {
      font-size: 16pt;
      font-weight: 800;
      color: #3d1a06;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0 0 2px 0;
      font-family: 'Times New Roman', 'Georgia', serif;
    }

    .school-code {
      font-size: 9pt;
      font-weight: bold;
      color: #7c5c43;
      margin: 0 0 3px 0;
    }

    .school-meta {
      font-size: 8pt;
      color: #5c3821;
    }

    /* STATEMENT TITLE BANNER */
    .statement-title-badge {
      background: #3d1a06;
      color: #fdfaf3;
      text-align: center;
      padding: 6px 12px;
      font-weight: bold;
      font-size: 11pt;
      letter-spacing: 1px;
      text-transform: uppercase;
      border-radius: 4px;
      border: 1px solid #c7ad8d;
      margin-bottom: 10px;
      font-family: 'Times New Roman', 'Georgia', serif;
    }

    /* STAFF / SCOPE PROFILE BOX */
    .scope-box {
      width: 100%;
      border: 1.5px solid #c7ad8d;
      border-radius: 6px;
      background-color: #f4ece1;
      padding: 8px 12px;
      margin-bottom: 10px;
      color: #3d1a06;
    }

    .scope-box table {
      width: 100%;
      border-collapse: collapse;
      font-size: 8.5pt;
    }

    .scope-box td {
      padding: 3px 6px;
      vertical-align: top;
      color: #3d1a06;
    }

    /* STAFF PROFILE SPOTLIGHT */
    .staff-spotlight {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 8px;
      padding-bottom: 8px;
      border-bottom: 1px dashed #c7ad8d;
    }

    .staff-spotlight-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .staff-avatar-img {
      width: 60px;
      height: 65px;
      border-radius: 4px;
      object-fit: cover;
      border: 1.5px solid #3d1a06;
      background: #ffffff;
      flex-shrink: 0;
    }

    .staff-spotlight-info h2 {
      font-size: 12pt;
      font-weight: bold;
      color: #3d1a06;
      margin: 0 0 2px 0;
    }

    .staff-spotlight-sub {
      font-size: 8.5pt;
      color: #7c5c43;
      font-weight: 600;
    }

    /* FINANCIAL SUMMARY GRID */
    .summary-grid {
      display: table;
      width: 100%;
      margin-bottom: 10px;
      border-collapse: separate;
      border-spacing: 6px;
    }

    .summary-card {
      display: table-cell;
      width: 20%;
      background: #f4ece1;
      border: 1px solid #c7ad8d;
      border-radius: 5px;
      padding: 6px 8px;
      text-align: center;
    }

    .summary-card.highlight-success {
      background: #e6f4ea;
      border-color: #a7f3d0;
    }

    .summary-card.highlight-warning {
      background: #fef3c7;
      border-color: #fde68a;
    }

    .summary-label {
      font-size: 7pt;
      font-weight: bold;
      text-transform: uppercase;
      color: #7c5c43;
      margin-bottom: 3px;
      display: block;
    }

    .summary-val {
      font-size: 9.5pt;
      font-weight: 800;
      color: #3d1a06;
    }

    /* TABLE */
    .statement-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
    }

    .statement-table th,
    .statement-table td {
      border: 1px solid #c7ad8d;
      padding: 5px 6px;
      font-size: 8pt;
      vertical-align: middle;
      color: #3d1a06;
    }

    .statement-table th {
      background-color: #3d1a06;
      color: #fdfaf3;
      font-weight: bold;
      text-transform: uppercase;
      font-size: 7.5pt;
      text-align: left;
      font-family: 'Times New Roman', 'Georgia', serif;
    }

    .statement-table tr:nth-child(even) {
      background-color: #f7f0e4;
    }

    .staff-row-avatar {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid #3d1a06;
      flex-shrink: 0;
    }

    .text-end { text-align: right; }
    .text-center { text-align: center; }

    .badge-status {
      display: inline-block;
      padding: 2px 6px;
      font-size: 7pt;
      font-weight: bold;
      border-radius: 3px;
      text-transform: uppercase;
    }

    .status-paid { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-pending { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    .status-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

    /* FOOTER SIGNATURES */
    .signatures-table {
      width: 100%;
      margin-top: 25px;
      border-collapse: collapse;
    }

    .signatures-table td {
      width: 33%;
      text-align: center;
      vertical-align: bottom;
    }

    .sig-line {
      border-top: 1.5px dashed #3d1a06;
      width: 80%;
      margin: 0 auto 4px auto;
    }

    .sig-title {
      font-size: 8.5pt;
      font-weight: bold;
      color: #3d1a06;
    }

    .stamp-seal {
      width: 65px;
      height: 65px;
      opacity: 0.85;
    }
  </style>
</head>
<body>

  <div class="no-print-toolbar">
    <div>
      <strong>Payroll Statement Executive Print View</strong> &bull; {{ date('d M Y, h:i A') }}
    </div>
    <div>
      <button class="btn-act btn-print" onclick="window.print()">Print Statement</button>
      <button class="btn-act btn-close-act" onclick="window.close()">Close Window</button>
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
        <td class="text-end" style="width: 180px; font-size: 8pt; color: #5c3821;">
          <strong>Date Generated:</strong> {{ date('d-M-Y') }}<br>
          <strong>Period Scope:</strong> {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Start' }} - {{ $endDate ? date('d/m/Y', strtotime($endDate)) : date('d/m/Y') }}
        </td>
      </tr>
    </table>

    <!-- STATEMENT TITLE BANNER -->
    <div class="statement-title-badge">
      Official Payroll Disbursement Statement & Audit Ledger
    </div>

    <!-- SCOPE DETAILS BOX & STAFF PROFILE -->
    <div class="scope-box">
      @if($selectedStaff)
        <div class="staff-spotlight">
          <div class="staff-spotlight-left">
            @if($selectedStaff->profile_picture)
              <img src="{{ asset('storage/' . $selectedStaff->profile_picture) }}" alt="{{ $selectedStaff->full_name }}" class="staff-avatar-img" />
            @else
              <img src="https://ui-avatars.com/api/?name={{ urlencode($selectedStaff->full_name) }}&background=3d1a06&color=fdfaf3&size=150" alt="{{ $selectedStaff->full_name }}" class="staff-avatar-img" />
            @endif
            <div class="staff-spotlight-info">
              <h2>{{ $selectedStaff->full_name }}</h2>
              <div class="staff-spotlight-sub">
                ID: {{ $selectedStaff->staff_id }} &bull; {{ $selectedStaff->formatted_designation }} &bull; {{ $selectedStaff->formatted_department }}
              </div>
              <div style="font-size: 8pt; color: #5c3821; margin-top: 2px;">
                Phone: {{ $selectedStaff->phone ?? 'N/A' }} | CNIC: {{ $selectedStaff->cnic ?? 'N/A' }}
              </div>
            </div>
          </div>
          <div style="text-align: right; font-size: 8pt; color: #5c3821;">
            <strong>Joining Date:</strong> {{ $selectedStaff->joining_date ? (is_object($selectedStaff->joining_date) ? $selectedStaff->joining_date->format('d/m/Y') : date('d/m/Y', strtotime($selectedStaff->joining_date))) : 'N/A' }}<br>
            <strong>Bank Account:</strong> {{ $selectedStaff->bank_name ? ($selectedStaff->bank_name . ' - ' . $selectedStaff->account_number) : 'Cash Settlement' }}
          </div>
        </div>
      @endif

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
        <span class="summary-val" style="color: #059669;">+Rs. {{ number_format($totalAllowance, 2) }}</span>
      </div>
      <div class="summary-card">
        <span class="summary-label">Deductions (-)</span>
        <span class="summary-val" style="color: #b91c1c;">-Rs. {{ number_format($totalDeduction, 2) }}</span>
      </div>
      <div class="summary-card highlight-success">
        <span class="summary-label" style="color: #047857;">Settled Paid</span>
        <span class="summary-val" style="color: #047857;">Rs. {{ number_format($paidAmount, 2) }}</span>
      </div>
      <div class="summary-card highlight-warning">
        <span class="summary-label" style="color: #b45309;">Pending Liability</span>
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
              <div style="display: flex; align-items: center; gap: 8px;">
                @if($p->staff && $p->staff->profile_picture)
                  <img src="{{ asset('storage/' . $p->staff->profile_picture) }}" alt="{{ $p->staff->full_name }}" class="staff-row-avatar" />
                @else
                  <img src="https://ui-avatars.com/api/?name={{ urlencode($p->staff ? $p->staff->full_name : 'Staff') }}&background=3d1a06&color=fdfaf3&size=100" alt="{{ $p->staff ? $p->staff->full_name : 'Staff' }}" class="staff-row-avatar" />
                @endif
                <div>
                  <strong>{{ $p->staff ? $p->staff->full_name : 'N/A' }}</strong>
                  <br><small style="color: #7c5c43;">{{ $p->staff ? $p->staff->staff_id : '' }}</small>
                </div>
              </div>
            </td>
            <td>{{ $p->staff ? $p->staff->formatted_department : 'N/A' }}</td>
            <td>{{ $p->payroll_month }}</td>
            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $p->payment_method) }}</td>
            <td class="text-end">Rs. {{ number_format($p->basic_salary, 2) }}</td>
            <td class="text-end" style="color: #059669;">+Rs. {{ number_format($p->allowance, 2) }}</td>
            <td class="text-end" style="color: #b91c1c;">-Rs. {{ number_format($p->deduction, 2) }}</td>
            <td class="text-end" style="font-weight: 800; color: #3d1a06;">Rs. {{ number_format($p->net_salary, 2) }}</td>
            <td class="text-center">
              <span class="badge-status status-{{ $p->status }}">
                {{ $p->status }}
              </span>
            </td>
            <td>{{ $p->payment_date ? $p->payment_date->format('d/m/Y') : 'Pending' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="11" class="text-center" style="padding: 20px; color: #7c5c43;">
              No payroll records match the specified filter criteria.
            </td>
          </tr>
        @endforelse
      </tbody>
      @if(count($payrolls) > 0)
        <tfoot>
          <tr style="font-weight: 800; background-color: #f4ece1; border-top: 2px solid #3d1a06;">
            <td colspan="5" class="text-end">STATEMENT TOTALS ({{ count($payrolls) }} RECORDS):</td>
            <td class="text-end">Rs. {{ number_format($totalBasic, 2) }}</td>
            <td class="text-end" style="color: #059669;">+Rs. {{ number_format($totalAllowance, 2) }}</td>
            <td class="text-end" style="color: #b91c1c;">-Rs. {{ number_format($totalDeduction, 2) }}</td>
            <td class="text-end" style="color: #3d1a06; font-size: 8.5pt;">Rs. {{ number_format($totalNetSalary, 2) }}</td>
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
