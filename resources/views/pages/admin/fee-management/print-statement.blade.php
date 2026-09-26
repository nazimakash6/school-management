<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fee Transaction Statement - {{ $selectedStudent ? $selectedStudent->first_name . ' ' . $selectedStudent->last_name : ($globalSchoolInfo->school_name ?? 'School ERP') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
      border-bottom: 2px solid #0d9488;
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
      color: #0f766e;
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
      background: #0f766e;
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

    /* STUDENT PROFILE BOX */
    .student-box {
      width: 100%;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      background-color: #f8fafc;
      padding: 8px 12px;
      margin-bottom: 14px;
    }

    .student-box table {
      width: 100%;
      border-collapse: collapse;
      font-size: 8.5pt;
    }

    .student-box td {
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

    .summary-card.highlight-danger {
      background: #fef2f2;
      border-color: #fecaca;
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
      background-color: #0f766e;
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
    .status-partial { background: #fef9c3; color: #854d0e; }
    .status-unpaid { background: #fee2e2; color: #991b1b; }
    .status-cancelled { background: #f1f5f9; color: #475569; }

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
      background: #0d9488;
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
      <strong>Fee Account Statement Print View</strong> &bull; {{ date('d M Y, h:i A') }}
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
          <strong>Period:</strong> {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Start' }} - {{ $endDate ? date('d/m/Y', strtotime($endDate)) : date('d/m/Y') }}
        </td>
      </tr>
    </table>

    <!-- STATEMENT TITLE BANNER -->
    <div class="statement-title-badge">
      Fee Transaction Statement & Student Ledger
      @if(isset($selectedSession) && $selectedSession)
        &bull; Session: {{ $selectedSession->session_name }}
      @endif
    </div>

    <!-- STUDENT DETAILS IF SINGLE STUDENT -->
    @if($selectedStudent)
      <div class="student-box">
        <table>
          <tr>
            <td style="width: 50%;"><strong>Student Name:</strong> {{ $selectedStudent->first_name }} {{ $selectedStudent->last_name }}</td>
            <td style="width: 50%;"><strong>Admission No:</strong> {{ $selectedStudent->admission_no }}</td>
          </tr>
          <tr>
            <td><strong>Father's Name:</strong> {{ $selectedStudent->father_name ?? 'N/A' }}</td>
            <td><strong>Class & Section:</strong> {{ $selectedStudent->class_name }} ({{ $selectedStudent->section ?? 'A' }})</td>
          </tr>
          <tr>
            <td><strong>Contact No:</strong> {{ $selectedStudent->phone_number ?? 'N/A' }}</td>
            <td><strong>Address:</strong> {{ $selectedStudent->current_address ?? 'N/A' }}</td>
          </tr>
        </table>
      </div>
    @endif

    <!-- FINANCIAL SUMMARY GRID -->
    <div class="summary-grid">
      <div class="summary-card">
        <span class="summary-label">Gross Amount</span>
        <span class="summary-val">Rs. {{ number_format($totalGrossAmount, 2) }}</span>
      </div>
      <div class="summary-card">
        <span class="summary-label">Concessions</span>
        <span class="summary-val" style="color: #0284c7;">Rs. {{ number_format($totalDiscount, 2) }}</span>
      </div>
      <div class="summary-card">
        <span class="summary-label">Net Receivable</span>
        <span class="summary-val" style="color: #0d9488;">Rs. {{ number_format($totalNetAmount, 2) }}</span>
      </div>
      <div class="summary-card highlight-success">
        <span class="summary-label" style="color: #166534;">Total Collected</span>
        <span class="summary-val" style="color: #15803d;">Rs. {{ number_format($totalPaidAmount, 2) }}</span>
      </div>
      <div class="summary-card highlight-danger">
        <span class="summary-label" style="color: #991b1b;">Balance Due</span>
        <span class="summary-val" style="color: #b91c1c;">Rs. {{ number_format($totalDueBalance, 2) }}</span>
      </div>
    </div>

    <!-- TRANSACTION LEDGER TABLE -->
    <table class="statement-table">
      <thead>
        <tr>
          <th style="width: 65px;">Date</th>
          <th style="width: 80px;">Invoice #</th>
          <th>Student Details</th>
          <th>Session</th>
          <th>Fee Type</th>
          <th>Month</th>
          <th class="text-end" style="width: 70px;">Gross</th>
          <th class="text-end" style="width: 60px;">Disc.</th>
          <th class="text-end" style="width: 70px;">Net</th>
          <th class="text-end" style="width: 70px;">Paid</th>
          <th class="text-end" style="width: 70px;">Balance</th>
          <th class="text-center" style="width: 60px;">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($invoices as $inv)
          <tr>
            <td>{{ $inv->created_at->format('d/m/Y') }}</td>
            <td><strong>{{ $inv->invoice_no }}</strong></td>
            <td>
              {{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'N/A' }}
              <br><small style="color: #64748b;">{{ $inv->admission ? ($inv->admission->admission_no . ' • ' . $inv->admission->class_name) : '' }}</small>
            </td>
            <td>{{ $inv->academicSession->session_name ?? 'N/A' }}</td>
            <td>{{ str_replace('_', ' ', $inv->fee_type) }}</td>
            <td>{{ $inv->fee_month }}</td>
            <td class="text-end">Rs. {{ number_format($inv->amount, 2) }}</td>
            <td class="text-end" style="color: #0284c7;">
              {{ $inv->discount > 0 ? ('Rs. ' . number_format($inv->discount, 2)) : '-' }}
            </td>
            <td class="text-end">Rs. {{ number_format($inv->net_amount, 2) }}</td>
            <td class="text-end" style="font-weight: 700; color: #15803d;">
              Rs. {{ number_format($inv->paid_amount, 2) }}
            </td>
            <td class="text-end" style="font-weight: 700; color: {{ $inv->due_balance > 0 ? '#b91c1c' : '#64748b' }};">
              Rs. {{ number_format($inv->due_balance, 2) }}
            </td>
            <td class="text-center">
              <span class="badge-status status-{{ $inv->status }}">
                {{ $inv->status }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="12" class="text-center" style="padding: 20px; color: #64748b;">
              No fee records found for the selected filter parameters.
            </td>
          </tr>
        @endforelse
      </tbody>
      @if($invoices->count() > 0)
        <tfoot>
          <tr style="font-weight: 800; background-color: #f1f5f9; border-top: 2px solid #0f766e;">
            <td colspan="6" class="text-end">STATEMENT TOTALS:</td>
            <td class="text-end">Rs. {{ number_format($totalGrossAmount, 2) }}</td>
            <td class="text-end" style="color: #0284c7;">Rs. {{ number_format($totalDiscount, 2) }}</td>
            <td class="text-end">Rs. {{ number_format($totalNetAmount, 2) }}</td>
            <td class="text-end" style="color: #15803d;">Rs. {{ number_format($totalPaidAmount, 2) }}</td>
            <td class="text-end" style="color: #b91c1c;">Rs. {{ number_format($totalDueBalance, 2) }}</td>
            <td></td>
          </tr>
        </tfoot>
      @endif
    </table>

    <!-- SIGNATURES SECTION -->
    <table class="signatures-table">
      <tr>
        <td>
          <div class="sig-line"></div>
          <div class="sig-title">Accounts Officer</div>
        </td>
        <td>
          @if($globalSchoolInfo->stamp_url)
            <img src="{{ $globalSchoolInfo->stamp_url }}" alt="Stamp" class="stamp-seal"><br>
          @endif
          <div class="sig-title" style="margin-top: 5px;">Official Seal / Stamp</div>
        </td>
        <td>
          <div class="sig-line"></div>
          <div class="sig-title">Principal Signature</div>
        </td>
      </tr>
    </table>

  </div>

  <script>
    window.addEventListener('DOMContentLoaded', () => {
      // Auto trigger print dialog when opened in standalone window
      if (window.opener || window.location.search.includes('autoprint=1')) {
        setTimeout(() => {
          window.print();
        }, 500);
      }
    });
  </script>
</body>
</html>
