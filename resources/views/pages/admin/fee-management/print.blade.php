<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Fee Voucher - {{ $invoice->invoice_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fee-invoice-detail.css') }}?v={{ time() }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background-color: #334155;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
        }

        .print-control-bar {
            background-color: #3d1a06;
            color: #fdfaf3;
            padding: 12px 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .print-container {
            display: flex;
            justify-content: center;
            padding: 24px 12px;
        }

        .print-a4-invoice {
            display: block !important;
            width: 210mm !important;
            min-height: 297mm !important;
            margin: 0 auto !important;
            background: #ffffff !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4) !important;
            border-radius: 4px !important;
            position: relative !important;
            overflow: hidden !important;
            text-align: left !important;
        }

        @media print {
            body {
                background: #ffffff !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }
            .print-a4-invoice {
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    {{-- TOP CONTROL BAR (Screen Only) --}}
    <div class="print-control-bar no-print">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="printer" style="width:1.3rem;height:1.3rem;color:#c7ad8d;"></i>
                <div>
                    <h6 class="mb-0 fw-bold" style="color:#fdfaf3;">Fee Voucher Print View</h6>
                    <small style="color:#c7ad8d;">Invoice #: {{ $invoice->invoice_no }} &bull; {{ $invoice->admission ? ($invoice->admission->first_name . ' ' . $invoice->admission->last_name) : '' }}</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" onclick="window.print()" class="btn btn-sm px-3.5 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm me-1" style="background-color: #c7ad8d; border-color: #c7ad8d; color: #3d1a06;">
                    <i data-lucide="printer" style="width:1rem;height:1rem;"></i> Print
                </button>
                <button type="button" onclick="if(window.opener){ window.close(); } else { window.location.href='{{ route('fee-management.show', $invoice->id) }}'; }" class="btn btn-outline-light btn-sm px-3 py-1.5 d-inline-flex align-items-center gap-1.5">
                    <i data-lucide="x" style="width:1rem;height:1rem;"></i> Close
                </button>
            </div>
        </div>
    </div>

    {{-- PRINT CONTAINER --}}
    <div class="print-container">

        <div class="print-a4-invoice">

            @php
                $schoolInfo = \App\Models\SchoolInfo::first();
                $status     = $invoice->status;
            @endphp

            {{-- Status-based watermark --}}
            <div class="fv-watermark {{ $status }}">{{ strtoupper($status) }}</div>

            {{-- ── Gradient Header ── --}}
            <div class="fv-header">
                <div class="fv-logo-wrap">
                    @if($schoolInfo && $schoolInfo->logo_url)
                        <img src="{{ $schoolInfo->logo_url }}" alt="Logo" class="fv-logo">
                    @else
                        <div class="fv-logo-ph">{{ strtoupper(substr($schoolInfo->school_name ?? 'S', 0, 1)) }}</div>
                    @endif
                    <div>
                        <div class="fv-school-name">{{ $schoolInfo->school_name ?? 'School Name' }}</div>
                        <div class="fv-school-sub">
                            {{ $schoolInfo->full_address ?? '' }}
                            @if($schoolInfo && $schoolInfo->phone) &bull; {{ $schoolInfo->phone }} @endif
                            @if($schoolInfo && $schoolInfo->email) &bull; {{ $schoolInfo->email }} @endif
                        </div>
                    </div>
                </div>
                <div class="fv-badge-wrap">
                    <div class="fv-doc-title">Fee<br>Voucher</div>
                    <div class="fv-invoice-no">{{ $invoice->invoice_no }}</div>
                    <div class="fv-print-date">Print Date: {{ now()->format('d M Y') }}</div>
                </div>
            </div>

            {{-- ── Status Stripe ── --}}
            <div class="fv-status-stripe {{ $status }}">
                <span>
                    @if($status === 'paid') ✓ Fee Fully Paid &amp; Cleared
                    @elseif($status === 'partial') ◑ Partial Payment Received — Balance Pending
                    @elseif($status === 'unpaid') ✗ Fee Unpaid — Immediate Payment Required
                    @else ✗ Invoice Cancelled
                    @endif
                </span>
                <span>
                    Due Date: {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}
                </span>
            </div>

            {{-- ── Student Hero Strip ── --}}
            <div class="fv-student-hero">
                <div>
                    <div class="fv-student-name">
                        {{ $invoice->admission ? ($invoice->admission->first_name . ' ' . $invoice->admission->last_name) : 'Student' }}
                    </div>
                    <div class="fv-student-meta">
                        Adm No: {{ $invoice->admission ? $invoice->admission->admission_no : '—' }}
                        &bull; Class: {{ $invoice->admission ? $invoice->admission->class_name : '—' }}
                        {{ $invoice->admission && $invoice->admission->section_name ? '(' . $invoice->admission->section_name . ')' : '' }}
                        &bull; Month: {{ $invoice->fee_month }}
                    </div>
                </div>
                <div>
                    <div class="fv-balance-label">Outstanding Balance</div>
                    <div class="fv-balance-amount" style="color:{{ $invoice->due_balance > 0 ? '#dc2626' : '#3d1a06' }};">
                        Rs. {{ number_format($invoice->due_balance, 2) }}
                    </div>
                </div>
            </div>

            {{-- ── Body ── --}}
            <div class="fv-body">

                {{-- Two-column info cards --}}
                <div class="fv-two-col">
                    {{-- Student info --}}
                    <div class="fv-col">
                        <div class="fv-card">
                            <div class="fv-card-head">🎓 Student Information</div>
                            <div class="fv-row"><span class="fv-lbl">Full Name</span>      <span class="fv-val">{{ $invoice->admission ? ($invoice->admission->first_name . ' ' . $invoice->admission->last_name) : 'N/A' }}</span></div>
                            <div class="fv-row"><span class="fv-lbl">Admission No.</span>  <span class="fv-val">{{ $invoice->admission ? $invoice->admission->admission_no : 'N/A' }}</span></div>
                            <div class="fv-row"><span class="fv-lbl">Father / Guardian</span><span class="fv-val">{{ $invoice->admission ? ($invoice->admission->father_name ?: $invoice->admission->guardian_name) : 'N/A' }}</span></div>
                            <div class="fv-row"><span class="fv-lbl">Class &amp; Section</span><span class="fv-val">{{ $invoice->admission ? ($invoice->admission->class_name . ' ' . ($invoice->admission->section_name ? '('.$invoice->admission->section_name.')' : '')) : 'N/A' }}</span></div>
                            <div class="fv-row"><span class="fv-lbl">Contact Phone</span>  <span class="fv-val">{{ $invoice->admission ? ($invoice->admission->mobile_number ?: 'N/A') : 'N/A' }}</span></div>
                        </div>
                    </div>
                    {{-- Invoice info --}}
                    <div class="fv-col">
                        <div class="fv-card">
                            <div class="fv-card-head">🧾 Voucher &amp; Billing Details</div>
                            <div class="fv-row"><span class="fv-lbl">Invoice / Voucher No.</span> <span class="fv-val" style="color:#0369a1;font-family:monospace;">{{ $invoice->invoice_no }}</span></div>
                            <div class="fv-row"><span class="fv-lbl">Fee Classification</span>    <span class="fv-val" style="text-transform:capitalize;">{{ str_replace('_', ' ', $invoice->fee_type) }}</span></div>
                            <div class="fv-row"><span class="fv-lbl">Billing Month/Session</span> <span class="fv-val">{{ $invoice->fee_month }}</span></div>
                            <div class="fv-row"><span class="fv-lbl">Due Date</span>              <span class="fv-val" style="color:#dc2626;">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</span></div>
                            <div class="fv-row">
                                <span class="fv-lbl">Payment Date</span>
                                <span class="fv-val">{{ $invoice->payment_date ? $invoice->payment_date->format('d M Y') . ' (' . str_replace('_',' ',$invoice->payment_method) . ')' : 'Pending Settlement' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fee summary cards --}}
                <div class="fv-fin-row">
                    <div class="fv-fin-cell fv-fc-base">
                        <div class="fv-fin-label">Base Fee</div>
                        <div class="fv-fin-amt">Rs. {{ number_format($invoice->amount, 2) }}</div>
                        <div class="fv-fin-sub">Standard charge</div>
                    </div>
                    <div class="fv-fin-cell fv-fc-disc">
                        <div class="fv-fin-label">Discount</div>
                        <div class="fv-fin-amt">− Rs. {{ number_format($invoice->discount, 2) }}</div>
                        <div class="fv-fin-sub">Scholarship/concession</div>
                    </div>
                    <div class="fv-fin-cell fv-fc-net">
                        <div class="fv-fin-label">Net Payable</div>
                        <div class="fv-fin-amt">Rs. {{ number_format($invoice->net_amount, 2) }}</div>
                        <div class="fv-fin-sub">After deductions</div>
                    </div>
                </div>

                {{-- Ledger breakdown --}}
                <table class="fv-ledger">
                    <thead>
                        <tr><th colspan="3">Fee Payment Ledger</th></tr>
                        <tr>
                            <th style="width:52%;">Description</th>
                            <th class="text-r" style="width:24%;">Amount (Rs.)</th>
                            <th class="text-r" style="width:24%;">Running Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Base Fee</strong> — {{ ucwords(str_replace('_',' ',$invoice->fee_type)) }}<br>
                                <span style="color:#94a3b8;font-size:7pt;">{{ $invoice->fee_month }}</span>
                            </td>
                            <td class="text-r" style="color:#1d4ed8;font-weight:700;">{{ number_format($invoice->amount, 2) }}</td>
                            <td class="text-r" style="font-weight:700;">{{ number_format($invoice->amount, 2) }}</td>
                        </tr>
                        @if($invoice->discount > 0)
                        <tr>
                            <td>
                                <strong>Discount / Concession</strong><br>
                                <span style="color:#94a3b8;font-size:7pt;">Scholarship or special reduction</span>
                            </td>
                            <td class="text-r" style="color:#3d1a06;font-weight:700;">− {{ number_format($invoice->discount, 2) }}</td>
                            <td class="text-r">{{ number_format($invoice->net_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->paid_amount > 0)
                        <tr>
                            <td>
                                <strong>Amount Received</strong><br>
                                <span style="color:#94a3b8;font-size:7pt;">{{ $invoice->payment_date ? $invoice->payment_date->format('d M Y') . ' via ' . str_replace('_',' ',$invoice->payment_method) : 'Partial payments received' }}</span>
                            </td>
                            <td class="text-r" style="color:#3d1a06;font-weight:700;">− {{ number_format($invoice->paid_amount, 2) }}</td>
                            <td class="text-r">{{ number_format($invoice->due_balance, 2) }}</td>
                        </tr>
                        @endif
                        @if(isset($previousUnpaid) && $previousUnpaid->isNotEmpty())
                            @foreach($previousUnpaid as $prevInv)
                            <tr style="background-color: #fef2f2;">
                                <td>
                                    <strong style="color: #dc2626;">Previous Unpaid Arrears</strong> — {{ $prevInv->invoice_no }} ({{ ucwords(str_replace('_',' ',$prevInv->fee_type)) }})<br>
                                    <span style="color: #b91c1c; font-size: 7pt;">Billing Month: {{ $prevInv->fee_month }} &bull; Due Date: {{ $prevInv->due_date ? $prevInv->due_date->format('d M Y') : 'N/A' }}</span>
                                </td>
                                <td class="text-r" style="color: #dc2626; font-weight: 700;">+ {{ number_format($prevInv->due_balance, 2) }}</td>
                                <td class="text-r" style="color: #dc2626; font-weight: 700;">Pending Arrears</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-r" style="font-size:8pt;color:{{ $invoice->due_balance > 0 ? '#dc2626' : '#3d1a06' }};font-weight:800;">
                                OUTSTANDING BALANCE DUE:
                            </td>
                            <td class="text-r" style="font-size:9.5pt;color:{{ $invoice->due_balance > 0 ? '#dc2626' : '#3d1a06' }};font-weight:900;">
                                Rs. {{ number_format($invoice->due_balance, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Outstanding balance box --}}
                <div class="fv-balance-box {{ $invoice->due_balance > 0 ? 'overdue' : 'clear' }}">
                    <div>
                        <div class="fv-bb-label">{{ $invoice->due_balance > 0 ? 'Outstanding Balance Due' : 'Account Fully Settled' }}</div>
                        <div class="fv-bb-sub">
                            Net: Rs. {{ number_format($invoice->net_amount,2) }} &nbsp;&bull;&nbsp;
                            Paid: Rs. {{ number_format($invoice->paid_amount,2) }}
                        </div>
                    </div>
                    <div class="fv-bb-amount">Rs. {{ number_format($invoice->due_balance, 2) }}</div>
                </div>

                {{-- Sibling(s) Fee Status & Account Summary --}}
                @if(!empty($siblingFeeSummaries) && count($siblingFeeSummaries) > 0)
                    <div style="margin-top: 12px; margin-bottom: 12px; border: 1px solid #c7ad8d; border-radius: 4px; overflow: hidden; background: #fff;">
                        <div style="background-color: #3d1a06; color: #fdfaf3; padding: 6px 12px; font-weight: 700; font-size: 8.5pt; display: flex; justify-content: space-between; align-items: center;">
                            <span>🎓 SIBLING(S) FEE STATUS &amp; ACCOUNT SUMMARY</span>
                            <span>{{ count($siblingFeeSummaries) }} Sibling(s)</span>
                        </div>
                        <div style="padding: 8px 12px;">
                            @foreach($siblingFeeSummaries as $sibData)
                                @php
                                    $sib = $sibData['student'];
                                    $sibInvoices = $sibData['invoices'];
                                @endphp
                                <div style="margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px dashed #e2e8f0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; font-size: 8pt;">
                                        <div>
                                            <strong style="color: #3d1a06;">{{ $sib->first_name }} {{ $sib->last_name }}</strong>
                                            <span style="color: #64748b;">(Adm: {{ $sib->admission_no }} &bull; Class: {{ $sib->class_name }})</span>
                                        </div>
                                        <div>
                                            <span style="font-size: 7.5pt; color: #475569;">Total Fee: <strong>Rs. {{ number_format($sibData['total_invoiced'], 2) }}</strong></span> &bull;
                                            <span style="font-size: 7.5pt; color: #166534;">Paid: <strong>Rs. {{ number_format($sibData['total_paid'], 2) }}</strong></span> &bull;
                                            <span style="font-size: 7.5pt; color: {{ $sibData['total_due'] > 0 ? '#dc2626' : '#64748b' }};">Due: <strong>Rs. {{ number_format($sibData['total_due'], 2) }}</strong></span>
                                        </div>
                                    </div>
                                    @if($sibInvoices->isNotEmpty())
                                        <table style="width: 100%; font-size: 7.5pt; border-collapse: collapse;">
                                            <thead>
                                                <tr style="background: #f8fafc; color: #475569; text-align: left; border-bottom: 1px solid #cbd5e1;">
                                                    <th style="padding: 2px 4px;">Voucher #</th>
                                                    <th style="padding: 2px 4px;">Fee Details</th>
                                                    <th style="padding: 2px 4px; text-align: right;">Net Fee</th>
                                                    <th style="padding: 2px 4px; text-align: right;">Paid</th>
                                                    <th style="padding: 2px 4px; text-align: right;">Due Balance</th>
                                                    <th style="padding: 2px 4px; text-align: center;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sibInvoices as $sInv)
                                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                                        <td style="padding: 2px 4px; font-weight: 600; color: #0369a1; font-family: monospace;">{{ $sInv->invoice_no }}</td>
                                                        <td style="padding: 2px 4px; text-transform: capitalize;">{{ str_replace('_',' ',$sInv->fee_type) }} ({{ $sInv->fee_month }})</td>
                                                        <td style="padding: 2px 4px; text-align: right;">Rs. {{ number_format($sInv->net_amount, 2) }}</td>
                                                        <td style="padding: 2px 4px; text-align: right; color: #166534; font-weight: 600;">Rs. {{ number_format($sInv->paid_amount, 2) }}</td>
                                                        <td style="padding: 2px 4px; text-align: right; font-weight: 700; color: {{ $sInv->due_balance > 0 ? '#dc2626' : '#64748b' }};">
                                                            Rs. {{ number_format($sInv->due_balance, 2) }}
                                                        </td>
                                                        <td style="padding: 2px 4px; text-align: center; text-transform: uppercase; font-size: 6.5pt; font-weight: 700;">
                                                            <span style="padding: 1px 4px; border-radius: 2px; color: #fff; background-color: {{ $sInv->status === 'paid' ? '#166534' : ($sInv->status === 'partial' ? '#d97706' : '#dc2626') }};">
                                                                {{ $sInv->status }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notes --}}
                @if($invoice->notes)
                    <div class="fv-notes">
                        <span class="fv-notes-title">📝 Special Notes / Payment Instructions</span>{{ $invoice->notes }}
                    </div>
                @endif

                <hr class="fv-divider">

                {{-- Signatures --}}
                <div class="fv-sig-row">
                    <div class="fv-sig-cell">
                        <span class="fv-sig-space"></span>
                        <div class="fv-sig-line"></div>
                        <div class="fv-sig-name">Cashier / Accounts Officer</div>
                        <div class="fv-sig-role">Fee Collector</div>
                    </div>
                    <div class="fv-sig-cell">
                        @if($schoolInfo && $schoolInfo->stamp_url)
                            <img src="{{ $schoolInfo->stamp_url }}" alt="Stamp" class="fv-stamp-img"><br>
                        @else
                            <span class="fv-sig-space"></span>
                        @endif
                        <div class="fv-sig-line"></div>
                        <div class="fv-sig-name">Official Seal</div>
                        <div class="fv-sig-role">School Stamp</div>
                    </div>
                    <div class="fv-sig-cell">
                        <span class="fv-sig-space"></span>
                        <div class="fv-sig-line"></div>
                        <div class="fv-sig-name">{{ $schoolInfo->principal_name ?? 'Principal / Director' }}</div>
                        <div class="fv-sig-role">Authorized Signatory</div>
                    </div>
                </div>

            </div>{{-- /fv-body --}}

            {{-- Footer --}}
            <div class="fv-footer">
                <span>{{ $schoolInfo->school_name ?? 'School ERP' }} &bull; {{ $schoolInfo->full_address ?? '' }}</span>
                <span>Generated: {{ now()->format('d-M-Y h:i A') }} &bull; Invoice: {{ $invoice->invoice_no }}</span>
            </div>

        </div>{{-- /print-a4-invoice --}}

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
