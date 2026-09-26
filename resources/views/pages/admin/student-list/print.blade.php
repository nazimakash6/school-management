<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $photo = $student->student_photo ?: ($student->admission ? $student->admission->student_photo : null);
        $fatherName = $student->father_name ? ucwords(strtolower($student->father_name)) : ($student->admission && $student->admission->father_name ? ucwords(strtolower($student->admission->father_name)) : 'N/A');
        $motherName = $student->mother_name ? ucwords(strtolower($student->mother_name)) : ($student->admission && $student->admission->mother_name ? ucwords(strtolower($student->admission->mother_name)) : 'N/A');
        $guardianName = $student->guardian_name ? ucwords(strtolower($student->guardian_name)) : ($student->parent_name ? ucwords(strtolower($student->parent_name)) : ($student->admission && $student->admission->guardian_name ? ucwords(strtolower($student->admission->guardian_name)) : 'N/A'));
        $guardianContact = $student->guardian_primary_mobile_no ?: ($student->parent_contact ?: ($student->admission ? $student->admission->guardian_primary_mobile_no : 'N/A'));
        $guardianRelation = $student->guardian_relation ?: ($student->admission ? $student->admission->guardian_relation : 'Guardian');
        $cnicBform = $student->cnic_bform ?: ($student->admission ? $student->admission->cnic_bform : 'N/A');
        $currentAddr = $student->current_address ?: ($student->address ?: ($student->admission ? $student->admission->current_address : 'N/A'));
        $permAddr = $student->permanent_address ?: ($student->admission ? $student->admission->permanent_address : 'N/A');
        $admDate = $student->admission_date ?: ($student->admission ? $student->admission->admission_date : null);
        $dob = $student->date_of_birth ?: ($student->admission ? $student->admission->date_of_birth : null);
        $className = $student->studentClass ? $student->studentClass->name : ($student->class_name ?: 'N/A');
        $sectionName = $student->section_name ?: ($student->section ?: 'A');
        $sessionName = $student->admission && $student->admission->academicSession ? $student->admission->academicSession->session_name : '2026-2027';

        $totFee = ($student->monthly_fee ?? 0) + ($student->quarterly_fee ?? 0) + ($student->annual_fee ?? 0) + ($student->registration_fee ?? 0);
        if ($totFee == 0 && $student->admission) {
            $totFee = ($student->admission->monthly_fee ?? 0) + ($student->admission->quarterly_fee ?? 0) + ($student->admission->annual_fee ?? 0) + ($student->admission->registration_fee ?? 0);
        }
        $disc = (float) ($student->scholarship_discount ?: ($student->admission ? $student->admission->scholarship_discount : 0));
        $netFee = max($totFee - $disc, 0);
        $status = strtolower($student->status ?: 'active');
    @endphp
    <title>Student Profile — {{ $student->full_name }} (Adm: {{ $student->admission_no }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* ==========================================
           1. ADMISSION BLANK-FORM COLOR SCHEME SYSTEM
              Background: #fdfaf3 (Parchment/Cream)
              Text/Dark Accent: #3d1a06 (Mahogany Brown)
              Border/Gold Accent: #c7ad8d (Warm Bronze/Gold)
        =========================================== */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-height: 100%;
            background: #e2e8f0;
            font-family: 'Times New Roman', 'Georgia', serif;
            color: #3d1a06;
            line-height: 1.25;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ==========================================
           2. TOP ACTION BAR (SCREEN ONLY)
        =========================================== */
        .print-action-bar {
            position: sticky;
            top: 0;
            z-index: 9999;
            background: #3d1a06;
            color: #fdfaf3;
            padding: 10px 24px;
            box-shadow: 0 4px 20px rgba(61, 26, 6, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #c7ad8d;
            font-family: Arial, sans-serif;
        }

        .action-bar-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .action-bar-title .icon-badge {
            background: #c7ad8d;
            color: #3d1a06;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .action-bar-title h4 {
            font-size: 15px;
            font-weight: 800;
            margin: 0;
            color: #fdfaf3;
            letter-spacing: -0.2px;
        }

        .action-bar-title p {
            font-size: 11px;
            margin: 0;
            color: #c7ad8d;
        }

        .action-bar-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-act {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            font-family: Arial, sans-serif;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-act-print {
            background: #22c55e;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
        }

        .btn-act-print:hover {
            background: #16a34a;
        }

        .btn-act-download {
            background: #059669;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
        }

        .btn-act-download:hover {
            background: #047857;
        }

        .btn-act-close {
            background: #c7ad8d;
            color: #3d1a06;
            box-shadow: 0 2px 8px rgba(199, 173, 141, 0.3);
        }

        .btn-act-close:hover {
            background: #b89b78;
        }

        /* ==========================================
           3. A4 FORM CONTAINER (BLANK-FORM STYLE)
        =========================================== */
        .paper-wrapper {
            padding: 20px 12px;
            display: flex;
            justify-content: center;
        }

        .form-container {
            width: 210mm;
            min-height: 297mm;
            background: #fdfaf3;
            border: 3px double #3d1a06;
            outline: 2px solid #c7ad8d;
            outline-offset: -5px;
            padding: 6mm 7mm;
            box-shadow: 0 10px 30px rgba(61, 26, 6, 0.25);
            position: relative;
        }

        /* Header Grid & Branding */
        .header-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #c7ad8d;
            padding-bottom: 5px;
            margin-bottom: 8px;
            gap: 10px;
        }

        .header-logo-wrapper {
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .header-logo {
            max-width: 65px;
            max-height: 65px;
            object-fit: contain;
        }

        .header-center {
            flex: 1;
            text-align: center;
        }

        .bismillah {
            font-family: 'Amiri', serif;
            font-size: 14px;
            font-weight: bold;
            color: #3d1a06;
            line-height: 1;
        }

        .school-name {
            font-size: 18px;
            font-weight: 900;
            color: #3d1a06;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            line-height: 1.15;
            margin-top: 1px;
        }

        .school-tagline {
            font-size: 8.5px;
            font-weight: bold;
            color: #3d1a06;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: Arial, sans-serif;
            margin-top: 1px;
        }

        .school-contact-line {
            font-size: 7.5px;
            font-family: Arial, sans-serif;
            color: #3d1a06;
            margin-top: 1px;
        }

        .header-ref-box {
            background: #f4ece1;
            border: 1px solid #c7ad8d;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #3d1a06;
            text-align: right;
            flex-shrink: 0;
        }

        .header-ref-box .ref-title {
            font-weight: 900;
            font-size: 9px;
            text-transform: uppercase;
            color: #3d1a06;
        }

        .header-ref-box .ref-code {
            font-weight: bold;
            color: #3d1a06;
        }

        /* Student Spotlight Hero Banner (Record Style in Blank-Form Colors) */
        .spotlight-card {
            background: #f4ece1;
            border: 1.5px solid #c7ad8d;
            border-radius: 6px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .spotlight-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .spotlight-avatar {
            width: 65px;
            height: 75px;
            border-radius: 4px;
            object-fit: cover;
            border: 1.5px solid #3d1a06;
            background: #ffffff;
        }

        .spotlight-details h2 {
            font-size: 13pt;
            font-weight: 900;
            color: #3d1a06;
            margin: 0 0 2px 0;
        }

        .spotlight-role-badge {
            font-size: 8pt;
            font-weight: bold;
            font-family: Arial, sans-serif;
            color: #3d1a06;
            background: #ffffff;
            border: 1px solid #c7ad8d;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 3px;
        }

        .spotlight-status {
            font-size: 7.5pt;
            font-family: Arial, sans-serif;
            font-weight: bold;
            color: #3d1a06;
        }

        .status-pill-active {
            background: #e6f4ea;
            color: #137333;
            border: 1px solid #a8dab5;
            padding: 1px 6px;
            border-radius: 10px;
        }

        .status-pill-inactive {
            background: #fce8e6;
            color: #c5221f;
            border: 1px solid #f5c2c7;
            padding: 1px 6px;
            border-radius: 10px;
        }

        .spotlight-metrics {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            min-width: 220px;
            font-family: Arial, sans-serif;
        }

        .metric-mini-tile {
            background: #ffffff;
            border: 1px solid #c7ad8d;
            border-radius: 4px;
            padding: 4px 7px;
            text-align: center;
        }

        .metric-mini-tile .m-lbl {
            font-size: 6pt;
            font-weight: bold;
            color: #3d1a06;
            text-transform: uppercase;
            display: block;
        }

        .metric-mini-tile .m-val {
            font-size: 8.5pt;
            font-weight: 900;
            color: #3d1a06;
            display: block;
        }

        /* Profile Section Cards */
        .section-card {
            border: 1px solid #c7ad8d;
            border-radius: 5px;
            margin-bottom: 7px;
            overflow: hidden;
            background: #ffffff;
            page-break-inside: avoid;
        }

        .section-bar {
            background: #3d1a06;
            color: #ffffff;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 4px 10px;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-bar i {
            color: #c7ad8d;
            font-size: 9pt;
        }

        .section-body {
            padding: 6px 8px;
        }

        /* Detail Tiles Grid (Record Style) */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
        }

        .detail-tile {
            background: #fdfaf3;
            border: 1px solid #c7ad8d;
            border-radius: 4px;
            padding: 4px 7px;
        }

        .detail-tile.span-2 {
            grid-column: span 2;
        }

        .detail-tile.span-4 {
            grid-column: span 4;
        }

        .tile-label {
            font-size: 6.5pt;
            font-weight: bold;
            font-family: Arial, sans-serif;
            color: #3d1a06;
            text-transform: uppercase;
            margin-bottom: 1px;
            opacity: 0.85;
        }

        .tile-value {
            font-size: 8pt;
            font-weight: bold;
            color: #3d1a06;
            word-break: break-word;
        }

        .font-mono {
            font-family: SFMono-Regular, Consolas, "Courier New", monospace;
        }

        /* Signatures Area */
        .print-signatures-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 14px;
            padding-top: 4px;
            page-break-inside: avoid;
            font-family: Arial, sans-serif;
        }

        .sig-block {
            text-align: center;
            width: 22%;
        }

        .sig-line {
            border-top: 1.5px solid #3d1a06;
            width: 100%;
            margin-bottom: 3px;
        }

        .sig-title {
            font-size: 7.5pt;
            font-weight: bold;
            color: #3d1a06;
            text-transform: uppercase;
        }

        .sig-subtitle {
            font-size: 6.5pt;
            color: #3d1a06;
            opacity: 0.8;
        }

        .sig-stamp-circle {
            width: 46px;
            height: 46px;
            border: 1.5px dashed #c7ad8d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 5.5pt;
            color: #3d1a06;
            font-weight: bold;
            text-align: center;
            line-height: 1.1;
            background: #f4ece1;
        }

        /* Footer Security Line */
        .print-footer-security {
            margin-top: 8px;
            border-top: 1px dotted #c7ad8d;
            padding-top: 4px;
            font-size: 6.5pt;
            font-family: Arial, sans-serif;
            color: #3d1a06;
            page-break-inside: avoid;
        }

        .security-line {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .timestamp-line {
            text-align: center;
            font-style: italic;
            opacity: 0.8;
        }

        /* ==========================================
           4. PRINT MEDIA OVERRIDES
        =========================================== */
        @media print {
            @page {
                size: A4 portrait;
                margin: 4mm 5mm 4mm 5mm;
            }

            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
                overflow: hidden !important;
            }

            .no-print, .print-action-bar {
                display: none !important;
            }

            .paper-wrapper {
                padding: 0 !important;
                display: block !important;
            }

            .form-container {
                width: 100% !important;
                max-height: 286mm !important;
                margin: 0 !important;
                padding: 3mm 4mm !important;
                box-shadow: none !important;
                border: 2px solid #3d1a06 !important;
                outline: none !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
                background: #fdfaf3 !important;
            }
        }
    </style>
</head>
<body>

    <!-- TOP ACTION BAR (SCREEN ONLY) -->
    <div class="print-action-bar no-print">
        <div class="action-bar-title">
            <div class="icon-badge">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div>
                <h4>Student Profile Record Print Preview</h4>
                <p>{{ $student->full_name }} &bull; Adm No: {{ $student->admission_no }} &bull; Class: {{ $className }} ({{ $sectionName }})</p>
            </div>
        </div>

        <div class="action-bar-buttons">
            <button type="button" onclick="window.print()" class="btn-act btn-act-print">
                <i class="bi bi-printer-fill"></i> Print Profile
            </button>
            <button type="button" onclick="downloadPDF()" class="btn-act btn-act-download">
                <i class="bi bi-download"></i> Download PDF
            </button>
            <button type="button" onclick="closePage()" class="btn-act btn-act-close">
                <i class="bi bi-x-circle-fill"></i> Close
            </button>
        </div>
    </div>

    <!-- MAIN A4 PRINTABLE DOCUMENT CONTAINER -->
    <div class="paper-wrapper">
        <div class="form-container">
            
            {{-- Header Branding Banner --}}
            <div class="header-grid">
                <div class="header-logo-wrapper">
                    <img src="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" alt="School Logo" class="header-logo" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
                </div>

                <div class="header-center">
                    <div class="bismillah">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
                    <h1 class="school-name">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}</h1>
                    <div class="school-tagline">{{ $globalSchoolInfo->tagline ?? 'DISCIPLINE • EDUCATION • EXCELLENCE' }}</div>
                    <div class="school-contact-line">
                        @if(!empty($globalSchoolInfo->phone)) Phone: {{ $globalSchoolInfo->phone }} &bull; @endif
                        @if(!empty($globalSchoolInfo->email)) Email: {{ $globalSchoolInfo->email }} &bull; @endif
                        {{ $globalSchoolInfo->full_address ?? ($globalSchoolInfo->address ?? 'Main Campus, Educational Complex') }}
                    </div>
                </div>

                <div class="header-ref-box">
                    <div class="ref-title">STUDENT DOSSIER</div>
                    <div class="ref-code">ADM: {{ $student->admission_no }}</div>
                    <div>Printed: {{ date('d/m/Y h:i A') }}</div>
                </div>
            </div>

            {{-- Student Spotlight Hero Card --}}
            <div class="spotlight-card">
                <div class="spotlight-left">
                    @if ($photo)
                        <img src="{{ asset('storage/' . $photo) }}" alt="{{ $student->full_name }}" class="spotlight-avatar" />
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->full_name) }}&background=3d1a06&color=fdfaf3&size=150" alt="{{ $student->full_name }}" class="spotlight-avatar" />
                    @endif
                    <div class="spotlight-details">
                        <h2>{{ $student->full_name }}</h2>
                        <span class="spotlight-role-badge">
                            <i class="bi bi-mortarboard-fill me-1"></i> Class {{ $className }} &bull; Section {{ strtoupper($sectionName) }}
                        </span>
                        <div class="spotlight-status">
                            <span>Status: </span>
                            <span class="{{ $status === 'active' ? 'status-pill-active' : 'status-pill-inactive' }}">
                                ● {{ strtoupper($status) }}
                            </span>
                            <span class="ms-2">&bull; Session: <strong>{{ $sessionName }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="spotlight-metrics">
                    <div class="metric-mini-tile">
                        <span class="m-lbl">ADMISSION NO</span>
                        <span class="m-val font-mono">{{ $student->admission_no }}</span>
                    </div>
                    <div class="metric-mini-tile">
                        <span class="m-lbl">ROLL NO</span>
                        <span class="m-val font-mono">{{ $student->roll_no ?: '-' }}</span>
                    </div>
                    <div class="metric-mini-tile">
                        <span class="m-lbl">CLASS / SECTION</span>
                        <span class="m-val">{{ $className }} - {{ strtoupper($sectionName) }}</span>
                    </div>
                    <div class="metric-mini-tile">
                        <span class="m-lbl">NET FEE / MONTH</span>
                        <span class="m-val font-mono">Rs. {{ number_format((float) $netFee, 0) }}</span>
                    </div>
                </div>
            </div>

            {{-- SECTION 1: PERSONAL & IDENTITY DETAILS --}}
            <div class="section-card">
                <div class="section-bar">
                    <span><i class="bi bi-person-fill"></i> 1. Personal & Identity Information</span>
                </div>
                <div class="section-body">
                    <div class="details-grid">
                        <div class="detail-tile">
                            <div class="tile-label">Full Name</div>
                            <div class="tile-value">{{ $student->full_name }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Admission Number</div>
                            <div class="tile-value font-mono">{{ $student->admission_no }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Roll Number</div>
                            <div class="tile-value font-mono">{{ $student->roll_no ?: 'N/A' }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Gender</div>
                            <div class="tile-value">{{ ucfirst($student->gender ?: 'N/A') }}</div>
                        </div>

                        <div class="detail-tile">
                            <div class="tile-label">Date of Birth</div>
                            <div class="tile-value">{{ $dob ? date('d M, Y', strtotime($dob)) : 'N/A' }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">CNIC / B-Form</div>
                            <div class="tile-value font-mono">{{ $cnicBform }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Blood Group</div>
                            <div class="tile-value">{{ $student->blood_group ?: 'N/A' }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Religion / Nationality</div>
                            <div class="tile-value">{{ $student->religion ?: 'Islam' }} / {{ $student->nationality ?: 'Pakistani' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: ACADEMIC & ADMISSION INFORMATION --}}
            <div class="section-card">
                <div class="section-bar">
                    <span><i class="bi bi-book-fill"></i> 2. Academic & Enrollment Details</span>
                </div>
                <div class="section-body">
                    <div class="details-grid">
                        <div class="detail-tile">
                            <div class="tile-label">Class Name</div>
                            <div class="tile-value">{{ $className }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Section</div>
                            <div class="tile-value">Section {{ strtoupper($sectionName) }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Academic Session</div>
                            <div class="tile-value">{{ $sessionName }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Admission Date</div>
                            <div class="tile-value">{{ $admDate ? date('d M, Y', strtotime($admDate)) : 'N/A' }}</div>
                        </div>

                        <div class="detail-tile span-2">
                            <div class="tile-label">Previous School Attended</div>
                            <div class="tile-value">{{ $student->previous_school ?: ($student->admission ? $student->admission->previous_school : 'N/A') }}</div>
                        </div>
                        <div class="detail-tile span-2">
                            <div class="tile-label">Academic Group</div>
                            <div class="tile-value">{{ $student->group_name ?: ($student->group ?: ($student->admission ? $student->admission->group_name : 'General')) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: PARENT & GUARDIAN DETAILS --}}
            <div class="section-card">
                <div class="section-bar">
                    <span><i class="bi bi-people-fill"></i> 3. Parent & Guardian Information</span>
                </div>
                <div class="section-body">
                    <div class="details-grid">
                        <div class="detail-tile">
                            <div class="tile-label">Father's Name</div>
                            <div class="tile-value">{{ $fatherName }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Father's CNIC</div>
                            <div class="tile-value font-mono">{{ $student->father_cnic ?: ($student->admission ? $student->admission->father_cnic : 'N/A') }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Mother's Name</div>
                            <div class="tile-value">{{ $motherName }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Guardian Name</div>
                            <div class="tile-value">{{ $guardianName }}</div>
                        </div>

                        <div class="detail-tile span-2">
                            <div class="tile-label">Guardian Relationship</div>
                            <div class="tile-value">{{ $guardianRelation }}</div>
                        </div>
                        <div class="detail-tile span-2">
                            <div class="tile-label">Primary Guardian Mobile</div>
                            <div class="tile-value font-mono">{{ $guardianContact }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: CONTACT & RESIDENCE ADDRESS --}}
            <div class="section-card">
                <div class="section-bar">
                    <span><i class="bi bi-geo-alt-fill"></i> 4. Contact & Address Details</span>
                </div>
                <div class="section-body">
                    <div class="details-grid">
                        <div class="detail-tile">
                            <div class="tile-label">Student Mobile No</div>
                            <div class="tile-value font-mono">{{ $student->student_mobile_no ?: ($student->admission ? $student->admission->student_mobile_no : 'N/A') }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Student / Guardian Email</div>
                            <div class="tile-value">{{ $student->student_email ?: ($student->email ?: ($student->admission ? $student->admission->student_email : 'N/A')) }}</div>
                        </div>
                        <div class="detail-tile span-2">
                            <div class="tile-label">Emergency Phone</div>
                            <div class="tile-value font-mono">{{ $student->emergency_contact_mobile_no ?: ($student->admission ? $student->admission->emergency_contact_mobile_no : $guardianContact) }}</div>
                        </div>

                        <div class="detail-tile span-2">
                            <div class="tile-label">Current Residence Address</div>
                            <div class="tile-value">{{ $currentAddr }}</div>
                        </div>
                        <div class="detail-tile span-2">
                            <div class="tile-label">Permanent Address</div>
                            <div class="tile-value">{{ $permAddr }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 5: FEE STRUCTURE & ACADEMIC REMARKS --}}
            <div class="section-card">
                <div class="section-bar">
                    <span><i class="bi bi-cash-stack"></i> 5. Fee Structure & Remarks</span>
                </div>
                <div class="section-body">
                    <div class="details-grid">
                        <div class="detail-tile">
                            <div class="tile-label">Fee Plan</div>
                            <div class="tile-value">{{ $student->fee_plan ?: ($student->admission ? $student->admission->fee_plan : 'Monthly') }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Monthly Base Fee</div>
                            <div class="tile-value font-mono">Rs. {{ number_format((float) ($student->monthly_fee ?: ($student->admission ? $student->admission->monthly_fee : 0)), 0) }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Scholarship Discount</div>
                            <div class="tile-value font-mono">Rs. {{ number_format((float) $disc, 0) }}</div>
                        </div>
                        <div class="detail-tile">
                            <div class="tile-label">Net Payable Fee</div>
                            <div class="tile-value font-mono" style="color: #3d1a06;">Rs. {{ number_format((float) $netFee, 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Official Signatures Area --}}
            <div class="print-signatures-wrapper">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-title">Parent / Guardian Signature</div>
                    <div class="sig-subtitle">Date: _________________</div>
                </div>
                <div class="sig-block">
                    <div class="sig-stamp-circle">
                        <span>OFFICIAL<br>SEAL</span>
                    </div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-title">Class Teacher Signature</div>
                    <div class="sig-subtitle">Verification Officer</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-title">Principal / Director</div>
                    <div class="sig-subtitle">Authorized Signatory</div>
                </div>
            </div>

            {{-- Security Footer --}}
            <div class="print-footer-security">
                <div class="security-line">
                    <span>CONFIDENTIAL &bull; EDUCORE MANAGEMENT ERP SYSTEM</span>
                    <span>VERIFIED STUDENT RECORD &bull; ADM: {{ $student->admission_no }}</span>
                </div>
                <div class="timestamp-line">
                    This document is an electronically generated official record printed on {{ date('F d, Y \a\t h:i A') }}. Any unauthorized alteration renders it invalid.
                </div>
            </div>

        </div>
    </div>

    <script>
        function downloadPDF() {
            window.print();
        }

        function closePage() {
            if (window.opener || window.history.length > 1) {
                window.close();
                setTimeout(function() {
                    window.location.href = "{{ route('student-list.show', $student->id) }}";
                }, 300);
            } else {
                window.location.href = "{{ route('student-list.show', $student->id) }}";
            }
        }
    </script>
</body>
</html>
