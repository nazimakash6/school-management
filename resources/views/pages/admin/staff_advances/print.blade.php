<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advance Invoice #{{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }} — {{ $advance->staff ? $advance->staff->full_name : 'Staff' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* ── Page setup ─────────────────────────────────────── */
    @page {
      size: A4 portrait;
      margin: 8mm 10mm;
    }

    *, *::before, *::after {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      font-size: 8pt;
      color: #0f172a;
      line-height: 1.35;
      background: #f1f5f9;
    }

    /* ── Toolbar (screen only) ─────────────────────────── */
    .toolbar {
      background: #1e293b;
      color: #fff;
      padding: 8px 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 8.5pt;
    }
    .btn-t {
      border: none;
      padding: 6px 14px;
      font-weight: 600;
      font-size: 8.5pt;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 6px;
    }
    .btn-print { background: #2563eb; color: #fff; }
    .btn-back  { background: #64748b; color: #fff; }

    /* ── A4 invoice card ───────────────────────────────── */
    .page {
      width: 210mm;
      min-height: 297mm;
      max-height: 297mm;
      overflow: hidden;
      margin: 12px auto;
      background: #fff;
      display: flex;
      flex-direction: column;
    }

    /* ── Header ────────────────────────────────────────── */
    .hdr {
      background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 65%, #3b82f6 100%);
      padding: 10px 14px 8px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }
    .hdr-left { display: flex; align-items: center; gap: 10px; flex: 1; }

    .logo-img {
      max-height: 48px;
      max-width: 60px;
      object-fit: contain;
      background: #fff;
      border-radius: 4px;
      padding: 3px;
    }
    .logo-ph {
      width: 48px; height: 48px;
      background: rgba(255,255,255,.15);
      border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16pt; font-weight: 800; color: #fff;
    }

    .school-name {
      font-size: 11pt; font-weight: 800; color: #fff;
      text-transform: uppercase; letter-spacing: .3px; margin: 0 0 1px;
    }
    .school-tag  { font-size: 6.5pt; color: rgba(255,255,255,.72); margin: 0 0 3px; }
    .school-meta { font-size: 6.5pt; color: rgba(255,255,255,.82); line-height: 1.5; }

    .hdr-right { text-align: right; }
    .inv-title  { font-size: 11pt; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: .8px; line-height: 1.2; }
    .inv-no     { font-size: 8pt; color: rgba(255,255,255,.80); margin-top: 2px; }
    .inv-date   { font-size: 7pt; color: rgba(255,255,255,.65); margin-top: 1px; }

    /* ── Status ribbon ─────────────────────────────────── */
    .ribbon {
      padding: 4px 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 7pt;
      font-weight: 700;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: .4px;
    }
    .r-approved         { background: #2563eb; }
    .r-fully_repaid     { background: #16a34a; }
    .r-pending          { background: #0891b2; }
    .r-rejected         { background: #dc2626; }
    .r-cancelled        { background: #6b7280; }
    .r-default          { background: #334155; }

    /* ── Body ──────────────────────────────────────────── */
    .body { padding: 10px 14px; flex: 1; }

    /* ── Two-col info cards ────────────────────────────── */
    .two-col { display: flex; gap: 10px; margin-bottom: 9px; }
    .info-card { flex: 1; border: 1px solid #e2e8f0; border-radius: 5px; overflow: hidden; }
    .ic-head {
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
      padding: 4px 10px;
      font-size: 6.5pt;
      font-weight: 700;
      text-transform: uppercase;
      color: #475569;
      letter-spacing: .4px;
    }
    .ic-body { padding: 5px 10px; }
    .ir {
      display: flex;
      justify-content: space-between;
      padding: 2.5px 0;
      border-bottom: 1px dashed #f1f5f9;
      font-size: 7.5pt;
      gap: 6px;
    }
    .ir:last-child { border-bottom: none; }
    .il { color: #64748b; white-space: nowrap; }
    .iv { font-weight: 600; text-align: right; }

    /* ── Financial summary row ─────────────────────────── */
    .fin-row { display: flex; gap: 8px; margin-bottom: 9px; }
    .fin-cell {
      flex: 1;
      border-radius: 5px;
      padding: 7px 10px;
      text-align: center;
    }
    .fc-issued  { background: #eff6ff; border: 1.5px solid #bfdbfe; }
    .fc-repaid  { background: #f0fdf4; border: 1.5px solid #bbf7d0; }
    .fc-balance { background: #fff7ed; border: 1.5px solid #fed7aa; }

    .fl { font-size: 6pt; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 3px; }
    .fc-issued  .fl { color: #1d4ed8; }
    .fc-repaid  .fl { color: #15803d; }
    .fc-balance .fl { color: #c2410c; }

    .fa { font-size: 10.5pt; font-weight: 800; line-height: 1; }
    .fc-issued  .fa { color: #1d4ed8; }
    .fc-repaid  .fa { color: #15803d; }
    .fc-balance .fa { color: #c2410c; }

    .fs { font-size: 6pt; color: #64748b; margin-top: 2px; }

    /* ── Progress bar ──────────────────────────────────── */
    .prog-wrap { margin-bottom: 9px; }
    .prog-hdr  { display: flex; justify-content: space-between; font-size: 7.5pt; font-weight: 600; margin-bottom: 3px; color: #334155; }
    .prog-track { height: 7px; background: #e2e8f0; border-radius: 20px; overflow: hidden; }
    .prog-fill  { height: 100%; background: linear-gradient(90deg,#16a34a,#4ade80); border-radius: 20px; }

    /* ── Ledger table ──────────────────────────────────── */
    .ledger { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 7.5pt; }
    .ledger thead tr:first-child th {
      background: #1e40af; color: #fff;
      padding: 5px 8px; font-size: 7.5pt; font-weight: 700;
      text-transform: uppercase; letter-spacing: .3px; text-align: left;
    }
    .ledger thead tr:nth-child(2) th {
      background: #dbeafe; color: #1e40af;
      padding: 4px 8px; font-size: 7pt; font-weight: 700;
      text-transform: uppercase;
    }
    .ledger tbody td {
      padding: 5px 8px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: top;
    }
    .ledger tfoot td {
      padding: 5px 8px;
      font-weight: 800;
      background: #f1f5f9;
      border-top: 2px solid #1e40af;
    }
    .text-r { text-align: right; }
    .text-c { text-align: center; }

    /* ── Notes ─────────────────────────────────────────── */
    .notes {
      background: #fafafa;
      border: 1px solid #e2e8f0;
      border-left: 3px solid #2563eb;
      border-radius: 0 4px 4px 0;
      padding: 5px 10px;
      font-size: 7pt;
      color: #475569;
      margin-bottom: 8px;
      white-space: pre-line;
    }
    .notes strong { color: #0f172a; display: block; margin-bottom: 2px; font-size: 7pt; }

    /* ── Divider ────────────────────────────────────────── */
    .div { border: none; border-top: 1px solid #e2e8f0; margin: 7px 0; }

    /* ── Signatures ────────────────────────────────────── */
    .sig-row { display: flex; gap: 8px; margin-top: 10px; }
    .sig-cell { flex: 1; text-align: center; }
    .sig-line { border-top: 1.2px dashed #94a3b8; width: 85%; margin: 0 auto 3px auto; }
    .sig-name { font-size: 7.5pt; font-weight: 700; color: #334155; }
    .sig-role { font-size: 6.5pt; color: #64748b; margin-top: 1px; }
    .stamp-img { max-width: 52px; max-height: 52px; object-fit: contain; opacity: .85; margin-bottom: 2px; }

    /* ── Footer band ────────────────────────────────────── */
    .footer {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      padding: 5px 14px;
      display: flex;
      justify-content: space-between;
      font-size: 6.5pt;
      color: #94a3b8;
      margin-top: auto;
    }

    /* ── Print overrides ────────────────────────────────── */
    @media print {
      html, body { background: #fff; }
      .toolbar   { display: none !important; }
      .page {
        margin: 0;
        width: 100%;
        min-height: unset;
        max-height: unset;
        overflow: visible;
      }
    }
  </style>
</head>
<body>

  {{-- Toolbar (hidden on print) --}}
  <div class="toolbar">
    <span><strong>Salary Advance Invoice</strong> &bull; {{ $advance->staff ? $advance->staff->full_name : '' }} &bull; #{{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</span>
    <div>
      <button class="btn-t btn-print" onclick="window.print()">🖨 Print / Save PDF</button>
      <button class="btn-t btn-back"  onclick="history.back()">← Back</button>
    </div>
  </div>

  <div class="page">

    {{-- ── Header ── --}}
    <div class="hdr">
      <div class="hdr-left">
        @if($schoolInfo && $schoolInfo->logo_url)
          <img src="{{ $schoolInfo->logo_url }}" alt="Logo" class="logo-img">
        @else
          <div class="logo-ph">{{ strtoupper(substr($schoolInfo->school_name ?? 'S', 0, 1)) }}</div>
        @endif
        <div>
          <div class="school-name">{{ $schoolInfo->school_name ?? 'School Name' }}</div>
          @if($schoolInfo && $schoolInfo->tagline)
            <div class="school-tag">{{ $schoolInfo->tagline }}</div>
          @endif
          <div class="school-meta">
            {{ $schoolInfo->full_address ?? '' }}
            @if($schoolInfo && $schoolInfo->phone) &bull; {{ $schoolInfo->phone }} @endif
            @if($schoolInfo && $schoolInfo->email) &bull; {{ $schoolInfo->email }} @endif
          </div>
        </div>
      </div>
      <div class="hdr-right">
        <div class="inv-title">Advance<br>Invoice</div>
        <div class="inv-no"># {{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</div>
        <div class="inv-date">{{ now()->format('d M Y') }}</div>
      </div>
    </div>

    {{-- ── Status ribbon ── --}}
    @php
      $statusVal   = is_object($advance->status) ? $advance->status->value : $advance->status;
      $validStatus = ['approved','fully_repaid','pending','rejected','cancelled'];
      $rClass      = in_array($statusVal, $validStatus) ? 'r-' . $statusVal : 'r-default';
    @endphp
    <div class="ribbon {{ $rClass }}">
      <span>Status: {{ strtoupper(str_replace('_', ' ', $statusVal)) }}</span>
      <span>Advance Date: {{ $advance->advance_date ? $advance->advance_date->format('d M Y') : 'N/A' }}</span>
    </div>

    {{-- ── Body ── --}}
    <div class="body">

      {{-- Two-column info cards --}}
      <div class="two-col">
        {{-- Staff info --}}
        <div class="info-card">
          <div class="ic-head">👤 Staff Information</div>
          <div class="ic-body">
            <div class="ir"><span class="il">Full Name</span>    <span class="iv">{{ $advance->staff ? $advance->staff->full_name : 'N/A' }}</span></div>
            <div class="ir"><span class="il">Staff ID</span>     <span class="iv">{{ $advance->staff ? ($advance->staff->staff_id ?? 'STF-'.$advance->staff->id) : 'N/A' }}</span></div>
            <div class="ir"><span class="il">Department</span>   <span class="iv">{{ $advance->staff ? $advance->staff->formatted_department : 'N/A' }}</span></div>
            <div class="ir"><span class="il">Designation</span>  <span class="iv">{{ $advance->staff ? $advance->staff->formatted_designation : 'N/A' }}</span></div>
            <div class="ir"><span class="il">Base Salary</span>  <span class="iv">Rs. {{ number_format($advance->staff->salary ?? 0, 2) }}</span></div>
            <div class="ir"><span class="il">Mobile No</span>    <span class="iv">{{ $advance->staff->mobile_no ?? 'N/A' }}</span></div>
          </div>
        </div>
        {{-- Advance info --}}
        <div class="info-card">
          <div class="ic-head">📋 Advance Details</div>
          <div class="ic-body">
            <div class="ir"><span class="il">Invoice No.</span>       <span class="iv"># {{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</span></div>
            <div class="ir"><span class="il">Advance Date</span>      <span class="iv">{{ $advance->advance_date ? $advance->advance_date->format('d M Y') : 'N/A' }}</span></div>
            <div class="ir"><span class="il">Payment Method</span>    <span class="iv" style="text-transform:capitalize;">{{ str_replace('_', ' ', $advance->payment_method) }}</span></div>
            <div class="ir"><span class="il">Reason / Purpose</span>  <span class="iv">{{ $advance->reason ?? 'Not specified' }}</span></div>
            <div class="ir"><span class="il">Status</span>            <span class="iv" style="text-transform:capitalize;">{{ str_replace('_', ' ', $statusVal) }}</span></div>
            <div class="ir"><span class="il">Print Date</span>        <span class="iv">{{ now()->format('d M Y, h:i A') }}</span></div>
          </div>
        </div>
      </div>

      {{-- Financial summary --}}
      <div class="fin-row">
        <div class="fin-cell fc-issued">
          <div class="fl">Advance Issued</div>
          <div class="fa">Rs. {{ number_format($advance->advance_amount, 2) }}</div>
          <div class="fs">Total disbursed</div>
        </div>
        <div class="fin-cell fc-repaid">
          <div class="fl">Amount Repaid</div>
          <div class="fa">Rs. {{ number_format($advance->repaid_amount, 2) }}</div>
          <div class="fs">Recovered so far</div>
        </div>
        <div class="fin-cell fc-balance">
          <div class="fl">Outstanding Balance</div>
          <div class="fa">Rs. {{ number_format($advance->remaining_balance, 2) }}</div>
          <div class="fs">Pending recovery</div>
        </div>
      </div>

      {{-- Progress bar --}}
      <div class="prog-wrap">
        <div class="prog-hdr">
          <span>Repayment Progress</span>
          <span style="color:#15803d;">{{ $advance->repayment_progress }}% Recovered</span>
        </div>
        <div class="prog-track">
          <div class="prog-fill" style="width:{{ $advance->repayment_progress }}%;"></div>
        </div>
      </div>

      {{-- Ledger table --}}
      <table class="ledger">
        <thead>
          <tr><th colspan="4">Advance Financial Ledger</th></tr>
          <tr>
            <th style="width:46%;">Description</th>
            <th class="text-r" style="width:18%;">Debit (Rs.)</th>
            <th class="text-r" style="width:18%;">Credit (Rs.)</th>
            <th class="text-r" style="width:18%;">Balance (Rs.)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>Advance Disbursed</strong> — {{ $advance->staff ? $advance->staff->full_name : 'Staff' }}<br>
              <span style="color:#94a3b8;font-size:7pt;">
                {{ $advance->advance_date ? $advance->advance_date->format('d M Y') : '' }}
                &bull; via {{ ucwords(str_replace('_', ' ', $advance->payment_method)) }}
              </span>
            </td>
            <td class="text-r" style="color:#1d4ed8;font-weight:700;">{{ number_format($advance->advance_amount, 2) }}</td>
            <td class="text-r" style="color:#94a3b8;">—</td>
            <td class="text-r" style="font-weight:800;">{{ number_format($advance->advance_amount, 2) }}</td>
          </tr>
          @if((float)$advance->repaid_amount > 0)
          <tr>
            <td>
              <strong>Repayment Recovered</strong><br>
              <span style="color:#94a3b8;font-size:7pt;">Accumulated repayments against advance</span>
            </td>
            <td class="text-r" style="color:#94a3b8;">—</td>
            <td class="text-r" style="color:#15803d;font-weight:700;">{{ number_format($advance->repaid_amount, 2) }}</td>
            <td class="text-r">{{ number_format((float)$advance->advance_amount - (float)$advance->repaid_amount, 2) }}</td>
          </tr>
          @endif
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-r" style="font-size:8pt;color:#1e40af;">OUTSTANDING BALANCE:</td>
            <td class="text-r" style="font-size:9pt;color:{{ $advance->remaining_balance > 0 ? '#c2410c' : '#15803d' }};">
              Rs. {{ number_format($advance->remaining_balance, 2) }}
            </td>
          </tr>
        </tfoot>
      </table>

      {{-- Notes (if any) --}}
      @if($advance->notes)
        <div class="notes"><strong>📝 Notes / Remarks:</strong>{{ $advance->notes }}</div>
      @endif

      <hr class="div">

      {{-- Signatures --}}
      <div class="sig-row">
        <div class="sig-cell">
          <div class="sig-line" style="margin-top:28px;"></div>
          <div class="sig-name">{{ $advance->staff ? $advance->staff->full_name : 'Employee' }}</div>
          <div class="sig-role">Employee Signature</div>
        </div>
        <div class="sig-cell">
          @if($schoolInfo && $schoolInfo->stamp_url)
            <img src="{{ $schoolInfo->stamp_url }}" alt="Stamp" class="stamp-img"><br>
          @else
            <div style="height:34px;"></div>
          @endif
          <div class="sig-line"></div>
          <div class="sig-name">Official Seal</div>
          <div class="sig-role">School Stamp</div>
        </div>
        <div class="sig-cell">
          <div class="sig-line" style="margin-top:28px;"></div>
          <div class="sig-name">{{ $schoolInfo->principal_name ?? 'Principal / Director' }}</div>
          <div class="sig-role">Authorized Signatory</div>
        </div>
      </div>

    </div>{{-- /body --}}

    {{-- Footer band --}}
    <div class="footer">
      <span>{{ $schoolInfo->school_name ?? 'School ERP' }} &bull; {{ $schoolInfo->full_address ?? '' }}</span>
      <span>Generated: {{ now()->format('d-M-Y h:i A') }} &bull; Invoice # {{ str_pad($advance->id, 5, '0', STR_PAD_LEFT) }}</span>
    </div>

  </div>{{-- /page --}}

  <script>
    window.addEventListener('DOMContentLoaded', function () {
      if (window.opener || window.location.search.includes('autoprint=1')) {
        setTimeout(function () { window.print(); }, 500);
      }
    });
  </script>
</body>
</html>
