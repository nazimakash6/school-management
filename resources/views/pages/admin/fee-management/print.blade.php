<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fee Record Summary - {{ $invoice->invoice_no }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&family=Cinzel:wght@700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Caveat:wght@700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* ===== MODERN HYPER-PREMIUM COLOR PALETTE & TYPOGRAPHY ===== */
        :root {
            --navy-dark: #0f172a;
            --navy-deep: #020617;
            --navy-primary: #1e293b;
            --blue-accent: #2563eb;
            --gold-primary: #d4af37;
            --gold-light: #f59e0b;
            --gold-gradient: linear-gradient(135deg, #fbbf24 0%, #d4af37 50%, #b45309 100%);
            --navy-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --bg-page: #0f172a;
            --bg-card: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --green-paid: #10b981;
            --amber-partial: #f59e0b;
            --rose-unpaid: #ef4444;
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--navy-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark);
            line-height: 1.35;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ===== TOP CONTROL BAR (Screen Only) ===== */
        .print-control-bar {
            background: var(--navy-gradient);
            color: #ffffff;
            padding: 14px 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 3px solid var(--gold-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-action-print {
            background: var(--gold-gradient);
            color: #0f172a;
            border: none;
            padding: 9px 24px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 3px 10px rgba(212, 175, 55, 0.35);
        }

        .btn-action-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.5);
            color: #000000;
        }

        .btn-action-close {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            border: 1.5px solid rgba(212, 175, 55, 0.6);
            padding: 9px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-action-close:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* ===== PRINT SHEET WRAPPER ===== */
        .print-sheet-wrapper {
            display: flex;
            justify-content: center;
            padding: 24px 0;
        }

        .a4-print-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            padding: 6mm 8mm;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        /* Main Outer Container with Dual Border Frame */
        .invoice-card-container {
            width: 100%;
            flex: 1;
            background: #ffffff;
            border: 3px solid var(--navy-dark);
            border-radius: 14px;
            outline: 2px solid var(--gold-primary);
            outline-offset: -7px;
            padding: 7mm 9mm 0mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 25px rgba(15, 23, 42, 0.02);
        }

        /* ===== 1. HEADER SECTION ===== */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            padding-bottom: 2px;
        }

        .header-logo-box {
            width: 44mm;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-svg {
            width: 40mm;
            height: 40mm;
            filter: drop-shadow(0 3px 6px rgba(15, 23, 42, 0.15));
        }

        .header-title-box {
            flex: 1;
            text-align: center;
            padding: 0 6px;
        }

        .main-school-title {
            font-family: 'Cinzel', serif;
            font-size: 24pt;
            font-weight: 900;
            color: var(--navy-dark);
            letter-spacing: 0.8px;
            line-height: 1.05;
            text-transform: uppercase;
        }

        .sub-school-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13pt;
            font-weight: 800;
            color: var(--navy-dark);
            letter-spacing: 2px;
            margin-top: 2px;
            text-transform: uppercase;
        }

        .arabic-school-title {
            font-family: 'Amiri', serif;
            font-size: 19pt;
            font-weight: 700;
            color: var(--navy-dark);
            line-height: 1.1;
            margin-top: 1px;
            direction: rtl;
        }

        .header-divider-line {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 4px;
        }

        .line-dash {
            width: 100px;
            height: 2px;
            background: var(--gold-gradient);
            border-radius: 2px;
        }

        .line-diamond {
            width: 9px;
            height: 9px;
            background: var(--gold-primary);
            transform: rotate(45deg);
        }

        .session-badge-box {
            width: 38mm;
            border: 2px solid var(--navy-dark);
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.12);
        }

        .session-lbl {
            background-color: var(--navy-dark);
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: 800;
            padding: 4px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .session-val {
            background: var(--gold-gradient);
            color: var(--navy-dark);
            font-size: 11.5pt;
            font-weight: 900;
            padding: 4px;
            letter-spacing: 0.5px;
        }

        /* Contact Info Bar */
        .contact-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 12px;
            margin-top: 3mm;
            font-size: 7.5pt;
            font-weight: 700;
            color: var(--text-dark);
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .contact-icon {
            width: 16px;
            height: 16px;
            background-color: var(--navy-dark);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7.5pt;
        }

        /* Banner Ribbon Section */
        .section-banner-ribbon {
            background: var(--navy-gradient);
            color: #ffffff;
            text-align: center;
            font-size: 11.5pt;
            font-weight: 900;
            letter-spacing: 2px;
            padding: 6px 24px;
            margin-top: 3mm;
            border-top: 1.5px solid var(--gold-primary);
            border-bottom: 1.5px solid var(--gold-primary);
            clip-path: polygon(18px 0%, calc(100% - 18px) 0%, 100% 50%, calc(100% - 18px) 100%, 18px 100%, 0% 50%);
            text-transform: uppercase;
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.15);
        }

        /* ===== 2. STUDENT INFORMATION CARD ===== */
        .student-info-card {
            border: 2px solid var(--navy-dark);
            border-radius: 10px;
            margin-top: 3mm;
            display: flex;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
        }

        .student-info-tab {
            background: var(--navy-gradient);
            color: #ffffff;
            width: 25mm;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 4px;
            text-align: center;
            border-right: 2px solid var(--gold-primary);
        }

        .student-tab-icon {
            width: 24px;
            height: 24px;
            background: var(--gold-gradient);
            color: var(--navy-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 9.5pt;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .student-tab-title {
            font-size: 7pt;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .student-info-grid {
            flex: 1;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            font-size: 8.5pt;
            line-height: 1.6;
        }

        .info-col-left {
            width: 46%;
        }

        .info-col-right {
            width: 52%;
            border-left: 1px dashed #cbd5e1;
            padding-left: 12px;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 3px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-lbl {
            font-weight: 700;
            color: var(--text-muted);
            min-width: 31mm;
            flex-shrink: 0;
        }

        .info-colon {
            margin-right: 6px;
            font-weight: bold;
            color: var(--navy-dark);
            flex-shrink: 0;
        }

        .info-val {
            font-weight: 800;
            color: var(--navy-dark);
            flex: 1;
            white-space: normal;
            word-break: break-word;
            line-height: 1.35;
        }

        /* ===== 3. TWO SUMMARY CARDS ===== */
        .summary-cards-row {
            display: flex;
            justify-content: space-between;
            margin-top: 3mm;
            gap: 12px;
        }

        .summary-card {
            width: 49%;
            border: 2px solid var(--navy-dark);
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }

        .summary-card-header {
            background: var(--navy-gradient);
            color: #ffffff;
            font-size: 9pt;
            font-weight: 800;
            text-align: center;
            padding: 5px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--gold-primary);
        }

        .summary-card-body {
            padding: 9px 12px;
            font-size: 8.5pt;
            flex: 1;
        }

        .summary-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .summary-item-row:last-child {
            margin-bottom: 0;
        }

        .item-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .item-icon {
            width: 18px;
            height: 18px;
            background-color: #f1f5f9;
            color: var(--navy-dark);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8.5pt;
            border: 1px solid #cbd5e1;
        }

        .item-val {
            font-weight: 800;
            color: var(--navy-dark);
        }

        .summary-highlight-banner {
            background-color: #f8fafc;
            border-top: 1.5px solid #e2e8f0;
            padding: 6px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9.5pt;
            font-weight: 800;
            color: var(--navy-dark);
        }

        .summary-remaining-banner {
            background: var(--navy-gradient);
            color: #ffffff;
            padding: 7px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10pt;
            font-weight: 900;
            border-top: 1.5px solid var(--gold-primary);
        }

        .status-badge-banner {
            background-color: #fffbeb;
            border-top: 2px solid var(--navy-dark);
            padding: 7px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-pill {
            background: var(--gold-gradient);
            color: var(--navy-dark);
            font-size: 10pt;
            font-weight: 900;
            padding: 3.5px 16px;
            border-radius: 14px;
            letter-spacing: 0.6px;
            box-shadow: 0 2px 5px rgba(212, 175, 55, 0.4);
        }

        /* ===== 4. MONTH-WISE FEE RECORD TABLE ===== */
        .table-section {
            margin-top: 3.5mm;
        }

        .table-header-banner {
            background: var(--navy-gradient);
            color: #ffffff;
            font-size: 9pt;
            font-weight: 800;
            text-align: center;
            padding: 5.5px;
            letter-spacing: 1.2px;
            border-radius: 8px 8px 0 0;
            text-transform: uppercase;
            border-bottom: 2px solid var(--gold-primary);
        }

        .month-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            text-align: center;
            background-color: #ffffff;
            border: 2px solid var(--navy-dark);
        }

        .month-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: 800;
            padding: 6px 4px;
            border: 1px solid #334155;
            font-size: 7.5pt;
            text-transform: uppercase;
        }

        .month-table td {
            padding: 4.5px 4px;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }

        .month-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 7.5pt;
            font-weight: 800;
            padding: 2px 9px;
            border-radius: 12px;
        }

        .badge-status.paid {
            background-color: #d1fae5;
            color: #047857;
        }

        .badge-status.partial {
            background-color: #fef3c7;
            color: #b45309;
        }

        .badge-status.unpaid {
            background-color: #ffe4e6;
            color: #be123c;
        }

        .month-table tfoot td {
            background: var(--navy-gradient);
            color: #ffffff;
            font-weight: 900;
            font-size: 8.5pt;
            padding: 6px;
            border: 1px solid var(--navy-dark);
        }

        /* ===== 5. BOTTOM CARDS ===== */
        .bottom-cards-row {
            display: flex;
            justify-content: space-between;
            margin-top: 3.5mm;
            gap: 12px;
        }

        .bottom-card {
            width: 49%;
            border: 2px solid var(--navy-dark);
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff;
            font-size: 8pt;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .card-inner-padding {
            padding: 9px 12px;
        }

        .notes-list {
            list-style: none;
            padding: 0;
        }

        .notes-list li {
            position: relative;
            padding-left: 15px;
            margin-bottom: 3.5px;
            font-size: 7.5pt;
            color: var(--text-muted);
            font-weight: 600;
            line-height: 1.3;
        }

        .notes-list li::before {
            content: "⚙";
            position: absolute;
            left: 0;
            color: var(--gold-primary);
            font-size: 8.5pt;
        }

        /* ===== 6. SIGNATURES ROW ===== */
        .signatures-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 5mm;
            padding: 0 10px;
        }

        .sig-col {
            width: 26%;
            text-align: center;
        }

        .sig-font {
            font-family: 'Caveat', cursive;
            font-size: 22pt;
            font-weight: 700;
            color: var(--navy-dark);
            line-height: 0.95;
            margin-bottom: 1px;
        }

        .sig-line {
            border-top: 1.5px solid var(--navy-dark);
            margin-bottom: 4px;
        }

        .sig-lbl {
            font-size: 7.5pt;
            font-weight: 800;
            color: var(--navy-dark);
            text-transform: uppercase;
        }

        .stamp-circle-box {
            width: 33mm;
            height: 33mm;
            border: 2.5px double var(--navy-dark);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4px;
            background-color: #ffffff;
            color: var(--navy-dark);
            font-size: 6.5pt;
            font-weight: 800;
            outline: 1.5px solid var(--gold-primary);
            outline-offset: -5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        /* ===== 7. FOOTER BANNER ===== */
        .footer-banner {
            background: var(--navy-gradient);
            color: #ffffff;
            padding: 7px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 5mm;
            margin-left: -9mm;
            margin-right: -9mm;
            border-top: 2.5px solid var(--gold-primary);
        }

        .footer-slogan {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 7.5pt;
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        .footer-thankyou {
            text-align: right;
        }

        .thankyou-script {
            font-family: 'Caveat', cursive;
            font-size: 19pt;
            font-weight: 700;
            color: var(--gold-light);
            line-height: 0.9;
        }

        .thankyou-sub {
            font-size: 6.5pt;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        /* ===== MEDIA PRINT SETUP ===== */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
            }

            .no-print {
                display: none !important;
            }

            .print-sheet-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }

            .a4-print-sheet {
                box-shadow: none !important;
                border: none !important;
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                padding: 5mm 7mm !important;
                margin: 0 !important;
            }

            .invoice-card-container {
                border-width: 3px !important;
            }
        }
    </style>
</head>
<body>

    @php
        $schoolInfo  = \App\Models\SchoolInfo::first();
        $student     = $invoice->admission;

        $studentName = $student ? trim($student->first_name . ' ' . ($student->last_name ?: '')) : 'N/A';
        $fatherName  = $student ? ($student->father_name ?: ($student->guardian_name ?: 'N/A')) : 'N/A';
        $className   = $student ? ($student->class_name ?: 'N/A') : 'N/A';
        $sectionName = $student && $student->section_name ? $student->section_name : '';
        $classSectionDisplay = $className . ($sectionName ? ' / ' . $sectionName : '');
        $rollNo      = $student ? ($student->roll_no ?: 'N/A') : 'N/A';
        $admissionNo = $student ? $student->admission_no : 'N/A';

        $sessionName = $invoice->academicSession ? $invoice->academicSession->session_name : ($student && $student->academicSession ? $student->academicSession->session_name : '2026-2027');
        $admDate     = $student && $student->admission_date ? optional($student->admission_date)->format('d-M-Y') : now()->format('d-M-Y');
        $contactNo   = $student ? ($student->phone ?: ($student->emergency_contact ?: ($schoolInfo->phone ?? 'N/A'))) : ($schoolInfo->phone ?? 'N/A');
        $fullAddress = $student ? ($student->address ?: ($schoolInfo->full_address ?? 'N/A')) : ($schoolInfo->full_address ?? 'N/A');

        $schoolName    = $schoolInfo->school_name ?? 'NOOR UL HUDA';
        $schoolPhone   = $schoolInfo->phone ?? '0300-1234567';
        $schoolEmail   = $schoolInfo->email ?? 'info@school.edu.pk';
        $schoolAddress = $schoolInfo->address ?? 'Main Campus';

        // 1. Fee Breakdown Mapping from Current Invoice items
        $feeItems = $invoice->fee_items;
        $tuitionAmt          = 0;
        $admissionFeeAmt     = 0;
        $examAmt             = 0;
        $activityAmt         = 0;
        $compAmt             = 0;
        $otherAmt            = 0;
        $siblingFeeAmt       = 0;
        $prevArrearsItemAmt  = 0;

        foreach ($feeItems as $item) {
            $type  = strtolower($item['fee_type'] ?? '');
            $title = strtolower($item['title'] ?? '');
            $amt   = (float)($item['amount'] ?? 0);

            if (str_contains($type, 'sibling') || str_contains($title, 'sibling')) {
                $siblingFeeAmt += $amt;
            } elseif (str_contains($type, 'previous') || str_contains($title, 'previous') || str_contains($title, 'arrears')) {
                $prevArrearsItemAmt += $amt;
            } elseif (str_contains($type, 'tuition') || str_contains($title, 'tuition') || str_contains($type, 'school_fee') || str_contains($title, 'school fee')) {
                $tuitionAmt += $amt;
            } elseif (str_contains($type, 'admission') || str_contains($title, 'admission')) {
                $admissionFeeAmt += $amt;
            } elseif (str_contains($type, 'exam') || str_contains($title, 'exam') || str_contains($title, 'examination')) {
                $examAmt += $amt;
            } elseif (str_contains($type, 'activity') || str_contains($title, 'activity')) {
                $activityAmt += $amt;
            } elseif (str_contains($type, 'computer') || str_contains($title, 'computer')) {
                $compAmt += $amt;
            } else {
                $otherAmt += $amt;
            }
        }

        if ($tuitionAmt == 0 && (float)$invoice->amount > 0 && ($admissionFeeAmt + $examAmt + $activityAmt + $compAmt + $otherAmt + $siblingFeeAmt + $prevArrearsItemAmt) == 0) {
            $tuitionAmt = (float)$invoice->amount;
        }

        $invAmount   = (float)$invoice->amount;
        $invDiscount = (float)$invoice->discount;
        $invPaid     = (float)$invoice->paid_amount;

        // 2. Unpaid previous dues from other invoices (not included in line items)
        $externalPrevDues = isset($previousUnpaid) ? (float)$previousUnpaid->where('admission_id', $invoice->admission_id)->sum('due_balance') : 0;
        $externalSiblingDues = isset($siblingFeeSummaries) ? (float)collect($siblingFeeSummaries)->sum('total_due') : 0;

        $displayPrevDues = $prevArrearsItemAmt + $externalPrevDues;
        $displaySiblingDues = $siblingFeeAmt + $externalSiblingDues;

        // Net Payable for this invoice voucher
        $totalPayable   = max(0, $invAmount - $invDiscount + $externalPrevDues + $externalSiblingDues);
        $totalRemaining = max(0, $totalPayable - $invPaid);

        // Overall Status Calculation
        if ($totalRemaining <= 0 && $totalPayable > 0) {
            $statusLabel = 'PAID';
        } elseif ($invPaid > 0) {
            $statusLabel = 'PARTIAL';
        } else {
            $statusLabel = 'UNPAID';
        }

        // 3. Dynamic Session Invoices Query for Student (ONLY Actual Transactions)
        $studentSessionInvoices = \App\Models\FeeManagement::where('admission_id', $invoice->admission_id)
            ->when($invoice->academic_session_id, function($q) use ($invoice) {
                $q->where('academic_session_id', $invoice->academic_session_id);
            })
            ->orderBy('due_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalSessionFeeInvoiced = $studentSessionInvoices->sum('amount');
        $totalSessionPaid        = $studentSessionInvoices->sum('paid_amount');
        $paymentCount            = \App\Models\FeePayment::whereIn('fee_management_id', $studentSessionInvoices->pluck('id'))->count();
        if ($paymentCount == 0 && $invPaid > 0) $paymentCount = 1;

        $lastPayment = \App\Models\FeePayment::whereIn('fee_management_id', $studentSessionInvoices->pluck('id'))
            ->latest('payment_date')
            ->latest('id')
            ->first();
        $lastPaidDateStr = $lastPayment ? $lastPayment->payment_date->format('d-M-Y') : ($invoice->payment_date ? $invoice->payment_date->format('d-M-Y') : 'N/A');
        $lastPaidAmt     = $lastPayment ? $lastPayment->amount : $invPaid;

        $outstandingMonthsCount = $studentSessionInvoices->whereIn('status', ['unpaid', 'partial'])->count();

        // 4. Actual Fee Transactions Only (No Dummy Months)
        $transactionRecords = [];
        $totTableFee     = 0;
        $totTablePaid    = 0;

        foreach ($studentSessionInvoices as $sInv) {
            $mFee     = (float)$sInv->net_amount;
            $mPaid    = (float)$sInv->paid_amount;
            $mDueDate = $sInv->due_date ? $sInv->due_date->format('d-M-Y') : 'N/A';
            $mPayDate = $sInv->payment_date ? $sInv->payment_date->format('d-M-Y') : ($mPaid > 0 ? now()->format('d-M-Y') : '-');
            $mStatus  = ucfirst($sInv->status ?? 'unpaid');

            $totTableFee  += $mFee;
            $totTablePaid += $mPaid;

            $transactionRecords[] = [
                'invoice_no' => $sInv->invoice_no,
                'fee_type'   => $sInv->fee_type_formatted,
                'month'      => $sInv->fee_month ?: 'N/A',
                'due_date'   => $mDueDate,
                'total'      => $mFee,
                'paid_amt'   => $mPaid > 0 ? 'Rs. ' . number_format($mPaid) : '-',
                'pay_date'   => $mPayDate,
                'status'     => $mStatus,
            ];
        }
    @endphp

    {{-- TOP CONTROL BAR (Screen Only) --}}
    <div class="print-control-bar no-print">
        <div style="display: flex; align-items: center; gap: 14px;">
            <i data-lucide="printer" style="width: 24px; height: 24px; color: var(--gold-primary);"></i>
            <div>
                <strong style="color: #ffffff; font-size: 16px;">Student Fee Invoice & Voucher</strong>
                <div style="color: var(--gold-light); font-size: 13px;">Invoice #: {{ $invoice->invoice_no }} &bull; {{ $studentName }} ({{ $invoice->fee_month }})</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <button type="button" onclick="window.print()" class="btn-action-print">
                <i data-lucide="printer" style="width: 17px; height: 17px;"></i> Print Invoice
            </button>
            <button type="button" onclick="if(window.opener){ window.close(); } else { window.location.href='{{ route('fee-management.show', $invoice->id) }}'; }" class="btn-action-close">
                <i data-lucide="x" style="width: 17px; height: 17px;"></i> Close
            </button>
        </div>
    </div>

    {{-- PRINT SHEET WRAPPER --}}
    <div class="print-sheet-wrapper">

        <div class="a4-print-sheet">

            {{-- MAIN INVOICE CARD CONTAINER --}}
            <div class="invoice-card-container">

                {{-- 1. TOP HEADER --}}
                <div class="header-section">
                    {{-- Left Crest Shield Logo --}}
                    <div class="header-logo-box">
                        @if($schoolInfo && $schoolInfo->logo_url)
                            <img src="{{ $schoolInfo->logo_url }}" alt="Logo" style="width:40mm; height:40mm; object-fit:contain;">
                        @else
                            {{-- High Precision Crest Shield SVG --}}
                            <svg class="logo-svg" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="shieldGoldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#fbbf24"/>
                                        <stop offset="50%" stop-color="#d4af37"/>
                                        <stop offset="100%" stop-color="#997a15"/>
                                    </linearGradient>
                                </defs>
                                <g fill="url(#shieldGoldGrad)">
                                    <path d="M 20 60 C 15 40 30 20 40 25 C 32 35 28 50 32 65 Z"/>
                                    <path d="M 100 60 C 105 40 90 20 80 25 C 88 35 92 50 88 65 Z"/>
                                </g>
                                <path d="M 30 20 L 90 20 C 90 20 95 65 60 95 C 25 65 30 20 30 20 Z" fill="#0f172a" stroke="url(#shieldGoldGrad)" stroke-width="2.5"/>
                                <path d="M 35 24 L 85 24 C 85 24 89 62 60 88 C 31 62 35 24 35 24 Z" fill="none" stroke="#ffffff" stroke-width="1.2"/>
                                <path d="M 60 30 L 60 38 L 65 33 L 72 38 L 60 28 Z" fill="url(#shieldGoldGrad)"/>
                                <g transform="translate(42, 42) scale(0.7)">
                                    <path d="M 0 15 Q 25 5 50 15 L 50 35 Q 25 25 0 35 Z" fill="#ffffff"/>
                                    <path d="M 50 15 Q 75 5 100 15 L 100 35 Q 75 25 50 35 Z" fill="#ffffff"/>
                                    <path d="M 50 12 L 50 36" stroke="#0f172a" stroke-width="3"/>
                                </g>
                                <path d="M 15 90 L 40 85 L 60 92 L 80 85 L 105 90 L 98 102 L 60 96 L 22 102 Z" fill="#0f172a" stroke="url(#shieldGoldGrad)" stroke-width="1.5"/>
                                <text x="60" y="96" font-size="5" font-weight="bold" fill="#ffffff" text-anchor="middle" font-family="'Plus Jakarta Sans', sans-serif" letter-spacing="0.5">KNOWLEDGE • FAITH • SUCCESS</text>
                            </svg>
                        @endif
                    </div>

                    {{-- Center School Titles --}}
                    <div class="header-title-box">
                        <div class="main-school-title">{{ $schoolName }}</div>
                        <div class="sub-school-title">PUBLIC SCHOOL DHANOT</div>
                        <div class="arabic-school-title">نورُ الهُدٰى پبلک سکول دھنوٹ</div>
                        <div class="header-divider-line">
                            <span class="line-dash"></span>
                            <span class="line-diamond"></span>
                            <span class="line-dash"></span>
                        </div>
                    </div>

                    {{-- Right Academic Session Badge --}}
                    <div class="session-badge-box">
                        <div class="session-lbl">Academic Session</div>
                        <div class="session-val">{{ $sessionName }}</div>
                    </div>
                </div>

                {{-- Contact Info Bar --}}
                <div class="contact-bar">
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <span>{{ $schoolAddress }}</span>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">📞</span>
                        <span>{{ $schoolPhone }}</span>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">✉</span>
                        <span>{{ $schoolEmail }}</span>
                    </div>
                </div>

                {{-- Chevron Ribbon Section Banner --}}
                <div class="section-banner-ribbon">
                    ✦ STUDENT FEE INVOICE & CHALLAN VOUCHER ✦
                </div>

                {{-- 2. STUDENT INFORMATION CARD --}}
                <div class="student-info-card">
                    <div class="student-info-tab">
                        <div class="student-tab-icon">👤</div>
                        <div class="student-tab-title">Student Information</div>
                    </div>
                    <div class="student-info-grid">
                        <div class="info-col-left">
                            <div class="info-row">
                                <span class="info-lbl">Student Name</span>
                                <span class="info-colon">:</span>
                                <span class="info-val">{{ $studentName }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-lbl">Father's Name</span>
                                <span class="info-colon">:</span>
                                <span class="info-val">{{ $fatherName }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-lbl">Admission No.</span>
                                <span class="info-colon">:</span>
                                <span class="info-val">{{ $admissionNo }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-lbl">Class / Section</span>
                                <span class="info-colon">:</span>
                                <span class="info-val">{{ $classSectionDisplay }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-lbl">Roll No.</span>
                                <span class="info-colon">:</span>
                                <span class="info-val">{{ $rollNo }}</span>
                            </div>
                        </div>
                        <div class="info-col-right">
                            <div class="info-row">
                                <span class="info-lbl">Invoice / Voucher #</span>
                                <span class="info-colon">:</span>
                                <span class="info-val" style="color: var(--blue-accent);">{{ $invoice->invoice_no }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-lbl">Billing Month</span>
                                <span class="info-colon">:</span>
                                <span class="info-val">{{ $invoice->fee_month }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-lbl">Due Date</span>
                                <span class="info-colon">:</span>
                                <span class="info-val" style="color: var(--rose-unpaid);">{{ $invoice->due_date ? $invoice->due_date->format('d-M-Y') : 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-lbl">Contact Phone</span>
                                <span class="info-colon">:</span>
                                <span class="info-val">{{ $contactNo }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-lbl">Address</span>
                                <span class="info-colon">:</span>
                                <span class="info-val">{{ $fullAddress }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. TWO SUMMARY CARDS (FEE SUMMARY & PAYMENT SUMMARY) --}}
                <div class="summary-cards-row">
                    {{-- Left Card: Fee Summary --}}
                    <div class="summary-card">
                        <div class="summary-card-header">FEE SUMMARY</div>
                        <div class="summary-card-body">
                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">💰</span>
                                    <span>Current Invoice Amount</span>
                                </div>
                                <div class="item-val">: Rs. {{ number_format($invAmount) }}</div>
                            </div>
                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">🏷</span>
                                    <span>Discount / Concession</span>
                                </div>
                                <div class="item-val" style="color: var(--rose-unpaid);">: Rs. {{ number_format($invDiscount) }}</div>
                            </div>
                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">📋</span>
                                    <span>Previous Pending Dues</span>
                                </div>
                                <div class="item-val" style="color: {{ $displayPrevDues > 0 ? 'var(--rose-unpaid)' : 'inherit' }};">: Rs. {{ number_format($displayPrevDues) }}</div>
                            </div>

                            @if($displaySiblingDues > 0)
                                <div class="summary-item-row">
                                    <div class="item-left">
                                        <span class="item-icon">👨‍👩‍👧</span>
                                        <span>Sibling Fee / Dues</span>
                                    </div>
                                    <div class="item-val" style="color: #854d0e;">: Rs. {{ number_format($displaySiblingDues) }}</div>
                                </div>
                            @endif

                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">💳</span>
                                    <span>Total Paid Amount</span>
                                </div>
                                <div class="item-val" style="color: var(--green-paid);">: Rs. {{ number_format($invPaid) }}</div>
                            </div>
                        </div>
                        <div class="summary-highlight-banner">
                            <span>TOTAL PAYABLE</span>
                            <span>: Rs. {{ number_format($totalPayable) }}</span>
                        </div>
                        <div class="summary-remaining-banner">
                            <span>TOTAL REMAINING BALANCE</span>
                            <span>: Rs. {{ number_format($totalRemaining) }}</span>
                        </div>
                    </div>

                    {{-- Right Card: Payment Summary --}}
                    <div class="summary-card">
                        <div class="summary-card-header">PAYMENT & VOUCHER STATUS</div>
                        <div class="summary-card-body">
                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">🔢</span>
                                    <span>Total Session Payments</span>
                                </div>
                                <div class="item-val">: {{ $paymentCount }} Record(s)</div>
                            </div>
                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">📅</span>
                                    <span>Last Settlement Date</span>
                                </div>
                                <div class="item-val">: {{ $lastPaidDateStr }}</div>
                            </div>
                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">💵</span>
                                    <span>Last Settlement Amount</span>
                                </div>
                                <div class="item-val">: Rs. {{ number_format($lastPaidAmt) }}</div>
                            </div>
                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">🏦</span>
                                    <span>Payment Method</span>
                                </div>
                                <div class="item-val">: {{ ucfirst($invoice->payment_method ?? 'Cash') }}</div>
                            </div>
                            <div class="summary-item-row">
                                <div class="item-left">
                                    <span class="item-icon">⏳</span>
                                    <span>Pending Unpaid Months</span>
                                </div>
                                <div class="item-val">: {{ $outstandingMonthsCount }} Month(s)</div>
                            </div>
                        </div>
                        <div class="status-badge-banner">
                            <span style="font-size: 8pt; font-weight: 800; color: var(--navy-dark); text-transform: uppercase;">INVOICE STATUS</span>
                            <span class="status-pill">{{ $statusLabel }}</span>
                        </div>
                    </div>
                </div>

                {{-- 4. CURRENT INVOICE PARTICULAR FEE HEADS BREAKDOWN --}}
                <div class="table-section">
                    <div class="table-header-banner">
                        INVOICE PARTICULARS & FEE HEADS BREAKDOWN ({{ $invoice->invoice_no }})
                    </div>
                    <table class="month-table">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding-left: 10px; width: 45%;">Description / Fee Head</th>
                                <th style="width: 25%;">Category</th>
                                <th style="text-end; padding-right: 12px; width: 30%;">Amount (PKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feeItems as $fItem)
                                @php
                                    $fType  = $fItem['fee_type'] ?? 'school_fee';
                                    $fTitle = $fItem['title'] ?? ucwords(str_replace('_', ' ', $fType));
                                    $fAmt   = (float)($fItem['amount'] ?? 0);
                                @endphp
                                <tr>
                                    <td style="font-weight: 800; text-align: left; padding-left: 10px; color: var(--navy-dark);">
                                        ❖ {{ $fTitle }}
                                    </td>
                                    <td><span style="font-size: 7.5pt; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">{{ str_replace('_', ' ', $fType) }}</span></td>
                                    <td style="font-weight: 800; text-align: right; padding-right: 12px; color: var(--navy-dark);">Rs. {{ number_format($fAmt) }}</td>
                                </tr>
                            @endforeach
                            @if($invDiscount > 0)
                                <tr>
                                    <td style="font-weight: 800; text-align: left; padding-left: 10px; color: var(--rose-unpaid);">
                                        🏷 Less: Scholarship / Concession Discount
                                    </td>
                                    <td><span style="font-size: 7.5pt; font-weight: 700; color: var(--rose-unpaid);">DISCOUNT</span></td>
                                    <td style="font-weight: 800; text-align: right; padding-right: 12px; color: var(--rose-unpaid);">- Rs. {{ number_format($invDiscount) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align: left; padding-left: 10px;">NET PAYABLE FOR THIS INVOICE</td>
                                <td>-</td>
                                <td style="text-align: right; padding-right: 12px;">Rs. {{ number_format($invAmount - $invDiscount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- 5. ACTUAL RECORDED FEE TRANSACTIONS TABLE (ONLY ACTUAL MONTH TRANSACTIONS) --}}
                @if(count($transactionRecords) > 0)
                    <div class="table-section" style="margin-top: 3mm;">
                        <div class="table-header-banner">
                            RECORDED MONTH-WISE FEE TRANSACTIONS ({{ count($transactionRecords) }} {{ Str::plural('RECORD', count($transactionRecords)) }})
                        </div>
                        <table class="month-table">
                            <thead>
                                <tr>
                                    <th style="width: 18%;">Voucher #</th>
                                    <th style="width: 18%;">Billing Month</th>
                                    <th style="width: 18%;">Fee Type</th>
                                    <th style="width: 14%;">Due Date</th>
                                    <th style="width: 12%;">Fee (Rs.)</th>
                                    <th style="width: 12%;">Paid (Rs.)</th>
                                    <th style="width: 8%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactionRecords as $rec)
                                    <tr style="{{ $rec['invoice_no'] == $invoice->invoice_no ? 'background-color: #f0f9ff; font-weight: bold;' : '' }}">
                                        <td style="font-weight: 800; color: var(--blue-accent);">{{ $rec['invoice_no'] }}</td>
                                        <td style="font-weight: 800;">{{ $rec['month'] }}</td>
                                        <td style="font-size: 7.5pt; color: var(--text-muted);">{{ Str::limit($rec['fee_type'], 25) }}</td>
                                        <td>{{ $rec['due_date'] }}</td>
                                        <td style="font-weight: 800;">Rs. {{ number_format($rec['total']) }}</td>
                                        <td style="font-weight: 800; color: var(--green-paid);">{{ $rec['paid_amt'] }}</td>
                                        <td>
                                            @if(strtolower($rec['status']) === 'paid')
                                                <span class="badge-status paid">✔ Paid</span>
                                            @elseif(strtolower($rec['status']) === 'partial')
                                                <span class="badge-status partial">✔ Partial</span>
                                            @else
                                                <span class="badge-status unpaid">✖ Unpaid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right; padding-right: 10px;">TOTAL TRANSACTIONS SUM:</td>
                                    <td>Rs. {{ number_format($totTableFee) }}</td>
                                    <td>Rs. {{ number_format($totTablePaid) }}</td>
                                    <td>-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif

                {{-- 6. BOTTOM CARDS (STRUCTURE & NOTES) --}}
                <div class="bottom-cards-row">
                    {{-- Left: Fee Structure --}}
                    <div class="bottom-card">
                        <div>
                            <div class="summary-card-header">FEE STRUCTURE (PER MONTH)</div>
                            <div class="card-inner-padding">
                                <div class="summary-item-row">
                                    <span style="font-weight: 700;">Tuition Fee</span>
                                    <span style="font-weight: 800;">: Rs. {{ number_format($tuitionAmt > 0 ? $tuitionAmt : 1000) }}</span>
                                </div>
                                <div class="summary-item-row" style="margin-top: 5px;">
                                    <span style="font-weight: 700;">Other Charges (Diary, Copies, Activity, etc.)</span>
                                    <span style="font-weight: 800;">: Rs. {{ number_format($otherAmt > 0 ? $otherAmt : 200) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="summary-remaining-banner" style="font-size: 8.5pt;">
                            <span>Total Monthly Fee</span>
                            <span>: Rs. {{ number_format(($tuitionAmt > 0 ? $tuitionAmt : 1000) + ($otherAmt > 0 ? $otherAmt : 200)) }}</span>
                        </div>
                    </div>

                    {{-- Right: Important Notes --}}
                    <div class="bottom-card">
                        <div>
                            <div class="summary-card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 5px 12px;">
                                <span>IMPORTANT NOTES</span>
                                <span style="font-size: 10pt;">🔔</span>
                            </div>
                            <div class="card-inner-padding">
                                <ul class="notes-list">
                                    <li>Fee must be paid before the due date.</li>
                                    <li>Late payment may be subject to fine.</li>
                                    <li>Please keep the paid slip safely as proof of payment.</li>
                                    <li>In case of any query, contact the school office.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 7. SIGNATURES ROW --}}
                <div class="signatures-row">
                    <div class="sig-col">
                        <div class="sig-font">Bshulai</div>
                        <div class="sig-line"></div>
                        <div class="sig-lbl">Parent / Guardian Signature</div>
                    </div>

                    <div class="sig-col">
                        <div class="sig-font">Aaula</div>
                        <div class="sig-line"></div>
                        <div class="sig-lbl">Fee Incharge Signature</div>
                    </div>

                    <div class="stamp-circle-box">
                        <div>NOOR UL HUDA PUBLIC SCHOOL</div>
                        <div style="font-size: 11pt; color: var(--gold-primary); margin: 2px 0;">★</div>
                        <div>DHANOT</div>
                    </div>

                    <div class="sig-col">
                        <div class="sig-font">Afbraan</div>
                        <div class="sig-line"></div>
                        <div class="sig-lbl">Principal Signature</div>
                    </div>
                </div>

                {{-- 8. FOOTER BANNER --}}
                <div class="footer-banner">
                    <div class="footer-slogan">
                        <span style="font-size: 13pt; color: var(--gold-primary);">📖</span>
                        <span>EDUCATION IS THE MOST POWERFUL WEAPON WHICH YOU CAN USE TO CHANGE THE WORLD.</span>
                    </div>
                    <div class="footer-thankyou">
                        <div class="thankyou-script">Thank You!</div>
                        <div class="thankyou-sub">FOR YOUR TIMELY PAYMENT</div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>

