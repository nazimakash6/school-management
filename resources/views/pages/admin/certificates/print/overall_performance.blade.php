<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Report & Progress Card — {{ ($data['student_name'] ?? '') ?: 'Noor Ul Huda Superior School' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  /* ===== RESET & BASE STYLES ===== */
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { width: 100%; height: 100%; }

  body {
    font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background: #e2e8f0;
    color: #3d1a06;
    line-height: 1.25;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  /* ===== PRINT PAGE SETUP (A4 PORTRAIT - EXACT SINGLE PAGE FIT) ===== */
  @page {
    size: A4 portrait;
    margin: 0mm !important;
  }

  @media print {
    html, body {
      width: 210mm !important;
      height: 297mm !important;
      max-height: 297mm !important;
      margin: 0 !important;
      padding: 0 !important;
      overflow: hidden !important;
      background: #fff !important;
    }

    .no-print, .no-print-bar {
      display: none !important;
    }

    .cert-page {
      box-shadow: none !important;
      margin: 0 !important;
      width: 210mm !important;
      height: 297mm !important;
      max-height: 297mm !important;
      padding: 4mm !important;
      box-sizing: border-box !important;
      overflow: hidden !important;
      page-break-before: avoid !important;
      page-break-after: avoid !important;
      page-break-inside: avoid !important;
    }

    .outer-frame {
      width: 100% !important;
      height: 100% !important;
      max-height: 100% !important;
      box-sizing: border-box !important;
      overflow: hidden !important;
      padding: 5px !important;
      gap: 4px !important;
    }
  }

  /* ===== TOP ACTION BAR (NO-PRINT) ===== */
  .no-print-bar {
    position: fixed;
    top: 15px;
    right: 20px;
    z-index: 9999;
    display: flex;
    gap: 10px;
    background: rgba(255, 255, 255, 0.95);
    padding: 10px 18px;
    border-radius: 50px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid #e2e8f0;
  }
  .btn-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 30px;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
  }
  .btn-print { background: #3d1a06; color: #fff; }
  .btn-print:hover { background: #5c2809; }
  .btn-close-preview { background: #fee2e2; color: #991b1b; }
  .btn-close-preview:hover { background: #fca5a5; color: #7f1d1d; }
  .btn-back { background: #edf2f7; color: #4a5568; }
  .btn-back:hover { background: #e2e8f0; }

  /* ===== MAIN PORTRAIT CONTAINER ===== */
  .cert-page {
    width: 210mm;
    max-width: 210mm;
    height: 297mm;
    max-height: 297mm;
    margin: 15px auto;
    background: #fdfaf3; /* Warm ivory matching reference image */
    position: relative;
    box-shadow: 0 12px 45px rgba(61, 26, 6, 0.25);
    padding: 5mm;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-sizing: border-box;
    overflow: hidden;
  }

  /* Outer Framed Border with Corner Accents */
  .outer-frame {
    width: 100%;
    height: 100%;
    max-height: 100%;
    border: 4px solid #3d1a06;
    border-radius: 12px;
    padding: 6px;
    position: relative;
    background: #fdfaf3;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 4px;
    box-sizing: border-box;
    overflow: hidden;
  }
  .inner-frame {
    position: absolute;
    inset: 3px;
    border: 1px solid #c7ad8d;
    border-radius: 9px;
    pointer-events: none;
  }

  /* ===== HEADER SECTION ===== */
  .header-container {
    text-align: center;
    position: relative;
    padding-top: 2px;
    padding-bottom: 4px;
    border-bottom: 2px solid #e8dfd1;
  }
  .header-ayah {
    font-family: 'Amiri', serif;
    font-size: 20px;
    font-weight: 700;
    color: #3d1a06;
    line-height: 1.1;
  }
  .header-translation {
    font-size: 10px;
    font-weight: 600;
    color: #5c4333;
    margin-top: 1px;
    font-style: italic;
  }
  .header-main-grid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 10px;
    padding: 0 8px;
  }
  .header-logo-box {
    width: 75px;
    height: 75px;
    border-radius: 50%;
    border: 2px solid #3d1a06;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(61, 26, 6, 0.15);
    overflow: hidden;
    padding: 3px;
  }
  .header-logo-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
  .header-school-seal {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    border: 2px dashed #3d1a06;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2px;
  }
  .header-school-seal-text {
    font-size: 7px;
    font-weight: 800;
    color: #3d1a06;
    text-transform: uppercase;
    line-height: 1;
  }
  .header-title-box {
    text-align: center;
    flex: 1;
    margin-top: 14px;
    padding-top: 4px;
  }
  .school-title {
    font-family: 'Outfit', serif;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: 0.5px;
    color: #3d1a06;
    text-transform: uppercase;
    line-height: 1;
  }
  .school-subtitle {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 2px;
    color: #3d1a06;
    margin: 2px 0 4px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .school-subtitle::before, .school-subtitle::after {
    content: "";
    display: inline-block;
    width: 25px;
    height: 1.5px;
    background: #3d1a06;
  }
  .header-badge {
    background: #3d1a06;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    padding: 3px 16px;
    border-radius: 20px;
    display: inline-block;
    letter-spacing: 1px;
    text-transform: uppercase;
  }
  .header-period-bullets {
    font-size: 9px;
    font-weight: 800;
    color: #3d1a06;
    letter-spacing: 2px;
    margin-top: 3px;
  }
  .header-kids-illustration {
    width: 90px;
    height: 75px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .header-kids-badge {
    background: #f4eae0;
    border: 1.5px solid #3d1a06;
    border-radius: 10px;
    padding: 4px 8px;
    text-align: center;
  }
  .header-kids-badge .icon { font-size: 24px; }
  .header-kids-badge span { font-size: 8px; font-weight: 800; color: #3d1a06; display: block; }

  /* ===== STUDENT PROFILE & REPORT TYPE CARD ===== */
  .profile-card {
    background: #f9f4ea;
    border: 1.5px solid #d4c3b0;
    border-radius: 8px;
    padding: 8px;
    display: grid;
    grid-template-columns: 1.3fr 0.8fr 0.9fr;
    gap: 10px;
    align-items: stretch;
  }
  .profile-left {
    display: flex;
    gap: 10px;
    align-items: center;
  }
  .student-photo-box {
    width: 65px;
    height: 75px;
    border: 1.5px solid #b8a28a;
    border-radius: 6px;
    background: #e8ded2;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
  }
  .student-photo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .student-photo-placeholder {
    font-size: 32px;
    color: #8c7866;
  }
  .student-info-list {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .info-row {
    display: flex;
    align-items: center;
    font-size: 11px;
    color: #2d1a0e;
  }
  .info-label {
    font-weight: 700;
    width: 105px;
    display: flex;
    align-items: center;
    gap: 4px;
    color: #3d1a06;
  }
  .info-value {
    flex: 1;
    border-bottom: 1px dotted #8c735c;
    padding-bottom: 1px;
    font-weight: 700;
    color: #1a0d06;
  }

  /* Report Type Box */
  .type-box {
    background: #fff;
    border: 1px solid #d4c3b0;
    border-radius: 6px;
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
    gap: 3px;
  }
  .box-title {
    font-size: 9.5px;
    font-weight: 800;
    color: #3d1a06;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e8dfd1;
    padding-bottom: 2px;
    margin-bottom: 2px;
    text-align: center;
  }
  .type-options {
    display: flex;
    flex-direction: column;
    gap: 2px;
    font-size: 10px;
    font-weight: 600;
  }
  .type-option-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .checkbox-sq {
    width: 11px;
    height: 11px;
    border: 1.5px solid #3d1a06;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 900;
    background: #fff;
  }
  .checkbox-sq.checked {
    background: #3d1a06;
    color: #fff;
  }

  /* Focus Area Box */
  .focus-box {
    background: #fff;
    border: 1px solid #d4c3b0;
    border-radius: 6px;
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .focus-lines {
    font-size: 10.5px;
    font-weight: 600;
    color: #3d1a06;
    line-height: 1.5;
    border-bottom: 1px dotted #8c735c;
    min-height: 48px;
    padding-top: 4px;
  }

  /* ===== 2-COLUMN TABLES SECTION ===== */
  .tables-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }
  .table-card {
    background: #fff;
    border: 1.5px solid #3d1a06;
    border-radius: 6px;
    overflow: hidden;
  }
  .table-card-header {
    background: #3d1a06;
    color: #fff;
    font-size: 10.5px;
    font-weight: 800;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }
  .table-card-header .icon { font-size: 12px; }
  
  .report-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9.5px;
  }
  .report-table th, .report-table td {
    border: 1px solid #d4c3b0;
    padding: 3px 4px;
    text-align: center;
    vertical-align: middle;
  }
  .report-table th {
    background: #f4eae0;
    color: #3d1a06;
    font-weight: 800;
    font-size: 8.5px;
    text-transform: uppercase;
  }
  .report-table td.subject-name {
    text-align: left;
    font-weight: 700;
    color: #2d1a0e;
    padding-left: 6px;
    width: 32%;
  }
  .report-table td.cell-check {
    width: 10%;
    font-size: 10px;
    font-weight: 900;
  }
  .report-table td.cell-remarks {
    text-align: left;
    font-size: 8.5px;
    color: #4a3728;
    width: 28%;
    padding-left: 4px;
  }
  .mark-active {
    color: #3d1a06;
    font-weight: 900;
  }
  .mark-star { color: #d97706; }
  .mark-thumb { color: #2563eb; }
  .mark-avg { color: #d97706; }
  .mark-needs { color: #dc2626; }

  /* ===== 3-BOX MIDDLE ROW ===== */
  .middle-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 0.9fr;
    gap: 10px;
  }
  .box-card {
    background: #fff;
    border: 1.5px solid #3d1a06;
    border-radius: 6px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .box-card-header {
    background: #3d1a06;
    color: #fff;
    font-size: 9.5px;
    font-weight: 800;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    gap: 5px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }
  .box-card-content {
    padding: 6px 8px;
    font-size: 9.5px;
    font-weight: 600;
    color: #2d1a0e;
    flex: 1;
    line-height: 1.5;
    background-image: repeating-linear-gradient(transparent, transparent 17px, #e8dfd1 17px, #e8dfd1 18px);
    background-size: 100% 18px;
    min-height: 52px;
  }

  .attendance-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 6px 10px;
  }
  .attendance-row {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    font-weight: 700;
    color: #2d1a0e;
  }
  .attendance-row .val {
    border-bottom: 1px dotted #8c735c;
    min-width: 50px;
    text-align: center;
    color: #3d1a06;
  }

  /* ===== PROGRESS SUMMARY SECTION ===== */
  .summary-card {
    background: #fff;
    border: 1.5px solid #3d1a06;
    border-radius: 6px;
    overflow: hidden;
  }
  .summary-grid {
    display: grid;
    grid-template-columns: 1fr 120px;
    align-items: center;
  }
  .stars-list {
    display: flex;
    justify-content: space-around;
    padding: 6px 8px;
    border-right: 1.5px solid #3d1a06;
  }
  .star-category-item {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
  }
  .star-cat-icon { font-size: 14px; }
  .star-cat-title {
    font-size: 8px;
    font-weight: 800;
    color: #3d1a06;
    text-transform: uppercase;
  }
  .stars-render {
    color: #d97706;
    font-size: 11px;
    letter-spacing: 1px;
  }

  .overall-status-box {
    text-align: center;
    padding: 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }
  .overall-status-title {
    font-size: 8.5px;
    font-weight: 900;
    color: #3d1a06;
    text-transform: uppercase;
    margin-bottom: 2px;
  }
  .overall-circle {
    width: 38px;
    height: 38px;
    border: 2px solid #3d1a06;
    border-radius: 50%;
    background: #fdfaf3;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin-bottom: 2px;
  }
  .overall-text {
    font-size: 9px;
    font-weight: 900;
    color: #166534;
    text-transform: uppercase;
  }

  /* ===== 3-BOX REMARKS GRID ===== */
  .remarks-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 10px;
  }

  /* ===== SIGNATURES & STAMP SECTION ===== */
  .sig-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding: 10px 10px 4px 10px;
    margin-top: 2px;
  }
  .sig-box {
    text-align: center;
    width: 120px;
  }
  .sig-line {
    border-bottom: 1.5px dashed #3d1a06;
    margin-bottom: 3px;
    min-height: 24px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    font-family: 'Georgia', serif;
    font-style: italic;
    font-size: 11px;
    color: #1e293b;
  }
  .sig-title {
    font-size: 9.5px;
    font-weight: 800;
    color: #3d1a06;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
  }
  .stamp-box {
    width: 55px;
    height: 55px;
    border: 2px dashed #8c735c;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2px;
    color: #8c735c;
  }
  .stamp-text {
    font-size: 7.5px;
    font-weight: 800;
    text-transform: uppercase;
    line-height: 1;
  }

  /* ===== FOOTER RIBBON ===== */
  .footer-ribbon {
    background: #3d1a06;
    color: #fff;
    text-align: center;
    padding: 4px 10px;
    font-size: 9.5px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 2px;
  }
  .footer-ribbon .book-icon { font-size: 12px; }
</style>
</head>
<body>

<!-- FLOATING ACTION BUTTONS (HIDDEN ON PRINT) -->
<div class="no-print-bar no-print">
  <button onclick="window.print()" class="btn-action btn-print">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
    Print Report Card
  </button>
  <button onclick="window.close()" class="btn-action btn-close-preview">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
    Close Window
  </button>
  <a href="{{ route('certificates.index') }}" class="btn-action btn-back">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back to Certificates
  </a>
</div>

<!-- PORTRAIT A4 CONTAINER -->
<div class="cert-page">
  <div class="outer-frame">
    <div class="inner-frame"></div>

    @if(($data['entry_mode'] ?? 'manual') === 'dynamic')
      <!-- ─── DYNAMIC AUTO-FETCH REPORT CARD PRINT LAYOUT (EXACT SPECIFICATION) ───── -->
      <!-- 1. HEADER SECTION -->
      <div class="header-container">
        <div class="header-ayah">رَّبِّ زِدْنِي عِلْمًا</div>
        <div class="header-translation">"My Lord! Increase me in knowledge." (Quran 20:114)</div>

        <div class="header-main-grid">
          <div class="header-logo-box">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Noor Ul Huda School Logo">
          </div>

          <div class="header-title-box">
            <div class="school-title">NOOR UL HUDA</div>
            <div class="school-subtitle">SUPERIOR SCHOOL</div>
            <div class="header-badge" style="background:#3d1a06;border:1px solid #c7ad8d;color:#ffffff;padding:4px 16px;">DYNAMIC COMPREHENSIVE PROGRESS REPORT CARD</div>
            <div class="header-period-bullets">• SESSION {{ ($data['session'] ?? '') ?: date('Y') }} • {{ ($data['date_period'] ?? '') ?: date('F Y') }} •</div>
          </div>

          <div class="header-kids-illustration">
            <div class="header-kids-badge">
              <div class="icon">🎓📊</div>
              <span>MODULES DB DATA</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. STUDENT PARTICULARS & AUTHORITY DETAILS -->
      <div class="profile-card" style="grid-template-columns: 1.4fr 1fr 1fr; margin-top:4px; padding:6px 8px;">
        <div class="profile-left">
          <div class="student-photo-box" style="width:58px;height:68px;">
            @if(!empty($data['student_photo_url']))
              <img src="{{ $data['student_photo_url'] }}" alt="Student Photo">
            @else
              <div class="student-photo-placeholder" style="font-size:26px;">👤</div>
            @endif
          </div>
          <div class="student-info-list" style="gap:2px;">
            <div class="info-row">
              <span class="info-label">👤 Student Name:</span>
              <span class="info-value" style="font-size:11px;font-weight:800;color:#3d1a06;">{{ ($data['student_name'] ?? '') ?: '________________________' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">👨‍👦 Father Name:</span>
              <span class="info-value">{{ ($data['father_name'] ?? '') ?: '________________________' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">🏫 Class & Sec:</span>
              <span class="info-value">{{ trim(($data['class_name'] ?? '') . ' ' . ($data['section'] ?? '')) ?: '________________________' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">🆔 Roll / Adm No:</span>
              <span class="info-value">{{ ($data['roll_no'] ?? '') ?: 'N/A' }} / {{ ($data['admission_no'] ?? '') ?: 'N/A' }}</span>
            </div>
          </div>
        </div>

        <div class="type-box" style="padding:4px 6px;">
          <div class="box-title">AUTHORITY & CERTIFICATE DETAILS</div>
          <div class="type-options" style="gap:2px;font-size:9.5px;">
            <div class="type-option-item">
              <span>Issue Date:</span>
              <strong style="color:#3d1a06;">{{ $data['issue_date'] ?? date('d M, Y') }}</strong>
            </div>
            <div class="type-option-item">
              <span>Report Type:</span>
              <strong style="color:#3d1a06;">{{ $data['report_type'] ?? 'Monthly' }} Card</strong>
            </div>
            <div class="type-option-item">
              <span>Date / Month:</span>
              <strong>{{ ($data['date_period'] ?? '') ?: date('F Y') }}</strong>
            </div>
          </div>
        </div>

        <div class="focus-box" style="padding:4px 6px;">
          <div class="box-title">FOCUS AREA / SUBJECT</div>
          <div class="focus-lines" style="min-height:36px;font-size:9.5px;">
            {{ ($data['focus_area'] ?? '') ?: 'General Academics & Character Development' }}
          </div>
        </div>
      </div>

      <!-- 3. SECTION 4: PERFORMANCE TRACKER (EXAMINATION MODULE DB) -->
      @php
        $ptList = $data['performance_tracker'] ?? [];
      @endphp
      <div class="table-card" style="margin-top:4px;">
        <div class="table-card-header" style="background:#3d1a06;color:#ffffff;padding:3px 8px;font-size:9px;">
          <span class="icon">📊</span>
          <span>SECTION 4: PERFORMANCE TRACKER (SUBJECT-WISE ASSESSMENT - EXAMINATION MODULE DB)</span>
        </div>
        <table class="report-table">
          <thead>
            <tr>
              <th style="width:26%;">SUBJECT NAME</th>
              <th style="width:20%;">EXAM TITLE</th>
              <th style="width:18%;">MARKS OBTAINED / TOTAL (%)</th>
              <th style="width:14%;">RATING / GRADE</th>
              <th style="width:22%;">REMARKS / NOTES</th>
            </tr>
          </thead>
          <tbody>
            @if(!empty($ptList) && is_array($ptList) && count($ptList) > 0)
              @foreach($ptList as $pt)
                @php
                  $rating = strtolower($pt['rating'] ?? 'good');
                  $ratingLabel = $rating === 'excellent' ? '★ Excellent' :
                                ($rating === 'good' ? '👍 Good' :
                                ($rating === 'average' ? 'Average' : 'Needs Imp.'));
                  $ratingColor = $rating === 'excellent' ? '#3d1a06' :
                                 ($rating === 'good' ? '#5c2809' :
                                 ($rating === 'average' ? '#8c735c' : '#a12d1b'));
                  $isAbsent = isset($pt['is_absent']) && ($pt['is_absent'] == 1 || $pt['is_absent'] === '1');
                  $marksText = $isAbsent ? '<span style="color:#a12d1b;font-weight:800;">Absent</span>' :
                               (isset($pt['marks_obtained']) && $pt['marks_obtained'] !== ''
                                 ? $pt['marks_obtained'] . ' / ' . ($pt['total_marks'] ?? 100) . ' (' . ($pt['percentage'] ?? 0) . '%)'
                                 : 'N/A');
                @endphp
                <tr>
                  <td class="subject-name" style="width:26%;font-weight:800;">📖 {{ $pt['subject'] ?? 'Subject' }}</td>
                  <td style="text-align:center;font-size:8.5px;color:#3d1a06;">{{ $pt['exam_title'] ?? 'Assessment' }}</td>
                  <td style="text-align:center;font-weight:700;">{!! $marksText !!}</td>
                  <td style="text-align:center;font-weight:800;color:{{ $ratingColor }};">{{ $ratingLabel }}</td>
                  <td class="cell-remarks" style="width:22%;">{{ $pt['remarks'] ?? '' }}</td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="5" style="text-align:center;color:#64748b;font-style:italic;padding:6px;">
                  No examination mark records found in database table (http://localhost:8000/examination) for this student.
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      <!-- 4. SECTION 5: SKILLS & ATTRIBUTES (SKILLS INSTITUTE DB) -->
      @php
        $skList = $data['skills_attributes'] ?? [];
      @endphp
      <div class="table-card" style="margin-top:4px;">
        <div class="table-card-header" style="background:#3d1a06;color:#ffffff;padding:3px 8px;font-size:9px;">
          <span class="icon">👤</span>
          <span>SECTION 5: SKILLS & ATTRIBUTES (PERSONAL & SOCIAL DEVELOPMENT - SKILLS INSTITUTE DB)</span>
        </div>
        <table class="report-table">
          <thead>
            <tr>
              <th style="width:28%;">SKILL AREA</th>
              <th style="width:20%;">CATEGORY</th>
              <th style="width:18%;">STAR RATING / BADGE</th>
              <th style="width:14%;">RATING LEVEL</th>
              <th style="width:20%;">INSTRUCTOR NOTES</th>
            </tr>
          </thead>
          <tbody>
            @if(!empty($skList) && is_array($skList) && count($skList) > 0)
              @foreach($skList as $sk)
                @php
                  $skRating = strtolower($sk['rating'] ?? 'good');
                  $skColor = $skRating === 'excellent' ? '#3d1a06' : ($skRating === 'good' ? '#5c2809' : '#8c735c');
                @endphp
                <tr>
                  <td class="subject-name" style="width:28%;font-weight:800;">👤 {{ $sk['skill'] ?? 'Skill' }}</td>
                  <td style="font-size:8.5px;font-weight:600;">{{ $sk['category'] ?? 'General' }}</td>
                  <td style="text-align:center;font-weight:800;color:#3d1a06;">
                    {{ $sk['star_rating'] ?? 5 }} ★ {{ !empty($sk['badge_level']) ? '('.$sk['badge_level'].')' : '' }}
                  </td>
                  <td style="text-align:center;font-weight:800;color:{{ $skColor }};">{{ ucfirst($skRating) }}</td>
                  <td class="cell-remarks" style="width:20%;">{{ $sk['remarks'] ?? '' }}</td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="5" style="text-align:center;color:#64748b;font-style:italic;padding:6px;">
                  No skill assessment records found in database table (http://localhost:8000/skills-institute) for this student.
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      <!-- 5. SECTION 6 & 7: DISCIPLINE & QURAN EVALUATIONS (2-COLUMN GRID) -->
      <div class="tables-grid" style="margin-top:4px;">
        <!-- SECTION 6: DISCIPLINE -->
        @php
          $discList = $data['discipline'] ?? [];
        @endphp
        <div class="table-card">
          <div class="table-card-header" style="background:#3d1a06;color:#ffffff;padding:3px 8px;font-size:9px;">
            <span class="icon">🛡️</span>
            <span>SECTION 6: DISCIPLINE PERFORMANCE</span>
          </div>
          <table class="report-table">
            <thead>
              <tr>
                <th style="width:38%;">EVALUATION TITLE</th>
                <th style="width:22%;">CATEGORY</th>
                <th style="width:15%;">RATING</th>
                <th style="width:25%;">REMARKS / SCORE</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($discList) && is_array($discList) && count($discList) > 0)
                @foreach($discList as $dItem)
                  <tr>
                    <td class="subject-name" style="width:38%;">{{ $dItem['title'] ?? 'Discipline' }}</td>
                    <td style="font-size:8.5px;font-weight:600;">{{ $dItem['category'] ?? 'General' }}</td>
                    <td style="color:#3d1a06;font-weight:800;font-size:9px;text-align:center;">{{ $dItem['star_rating'] ?? 5 }} ★</td>
                    <td class="cell-remarks" style="width:25%;">
                      {{ !empty($dItem['remarks']) ? $dItem['remarks'] : (!empty($dItem['obtained']) ? $dItem['obtained'].'/'.$dItem['total_score'] : 'Satisfactory') }}
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="4" style="text-align:center;color:#64748b;font-style:italic;padding:6px;">
                    No discipline evaluation records found in database table (http://localhost:8000/discipline) for this student.
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>

        <!-- SECTION 7: QURAN -->
        @php
          $qurList = $data['quran'] ?? [];
        @endphp
        <div class="table-card">
          <div class="table-card-header" style="background:#3d1a06;color:#ffffff;padding:3px 8px;font-size:9px;">
            <span class="icon">📖</span>
            <span>SECTION 7: QURAN EVALUATION</span>
          </div>
          <table class="report-table">
            <thead>
              <tr>
                <th style="width:32%;">CATEGORY / STATUS</th>
                <th style="width:28%;">PARA / SURAH</th>
                <th style="width:15%;">SCORE</th>
                <th style="width:25%;">NOTES</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($qurList) && is_array($qurList) && count($qurList) > 0)
                @foreach($qurList as $qItem)
                  <tr>
                    <td class="subject-name" style="width:32%;">
                      {{ $qItem['category'] ?? 'Quran' }}
                      @if(!empty($qItem['status']))
                        <br><span style="font-size:7.5px;color:#3d1a06;font-weight:700;">({{ $qItem['status'] }})</span>
                      @endif
                    </td>
                    <td style="font-size:8.5px;font-weight:600;">
                      {{ !empty($qItem['para_no']) ? 'Para '.$qItem['para_no'] : '' }}
                      {{ !empty($qItem['surah_name']) ? ' '.$qItem['surah_name'] : '' }}
                    </td>
                    <td style="font-weight:800;font-size:9px;color:#3d1a06;text-align:center;">{{ $qItem['score'] ?? 'A' }}</td>
                    <td class="cell-remarks" style="width:25%;">{{ $qItem['notes'] ?? '' }}</td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="4" style="text-align:center;color:#64748b;font-style:italic;padding:6px;">
                    No Quran module evaluation records found in database table (http://localhost:8000/quran-module) for this student.
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>

      <!-- 6. SECTION 8: REMARKS & FUTURE ACTION PLAN (3-BOX GRID) -->
      <div class="remarks-grid" style="margin-top:4px;">
        <div class="box-card">
          <div class="box-card-header" style="background:#3d1a06;color:#ffffff;padding:3px 8px;font-size:9px;">
            <span>✏️</span>
            <span>TEACHER'S REMARKS</span>
          </div>
          <div class="box-card-content" style="min-height:44px;font-size:9px;">
            {{ ($data['teacher_remarks'] ?? '') ?: 'Demonstrates exemplary academic performance and excellent moral conduct in school.' }}
          </div>
        </div>

        <div class="box-card">
          <div class="box-card-header" style="background:#3d1a06;color:#ffffff;padding:3px 8px;font-size:9px;">
            <span>👤</span>
            <span>PARENT'S REMARKS</span>
          </div>
          <div class="box-card-content" style="min-height:44px;font-size:9px;">
            {{ ($data['parent_remarks'] ?? '') ?: 'Very satisfied with student progress and school environment.' }}
          </div>
        </div>

        <div class="box-card">
          <div class="box-card-header" style="background:#3d1a06;color:#ffffff;padding:3px 8px;font-size:9px;">
            <span>🎯</span>
            <span>ACTION PLAN / NEXT STEPS</span>
          </div>
          <div class="box-card-content" style="min-height:44px;font-size:9px;">
            {{ ($data['action_plan'] ?? '') ?: 'Continue reading practice, participate in upcoming Tajweed competition & sports gala.' }}
          </div>
        </div>
      </div>

      <!-- 7. SECTION 9: AUTHORITY & SIGNATURES -->
      <div class="sig-section" style="margin-top:4px;padding:6px 10px 2px 10px;">
        <div class="sig-box" style="width:130px;">
          <div class="sig-line">{{ !empty($data['class_teacher']) && $data['class_teacher'] !== 'Class Teacher' ? $data['class_teacher'] : '' }}</div>
          <div class="sig-title">✏️ Class Teacher</div>
        </div>

        <div class="sig-box" style="width:130px;">
          <div class="sig-line">{{ !empty($data['parent_guardian']) && $data['parent_guardian'] !== 'Parent / Guardian' ? $data['parent_guardian'] : '' }}</div>
          <div class="sig-title">👤 Parent / Guardian</div>
        </div>

        <div class="sig-box" style="width:130px;">
          <div class="sig-line">{{ !empty($data['principal']) && $data['principal'] !== 'Principal' ? $data['principal'] : '' }}</div>
          <div class="sig-title">✏️ Principal</div>
        </div>

        <div class="stamp-box" style="width:50px;height:50px;">
          <span class="stamp-text">School<br>Stamp</span>
        </div>
      </div>

      <!-- 8. FOOTER RIBBON -->
      <div class="footer-ribbon" style="margin-top:2px;padding:3px 10px;font-size:9px;">
        <span>KNOWLEDGE</span>
        <span>•</span>
        <span>FAITH</span>
        <span>•</span>
        <span class="book-icon">📖</span>
        <span>•</span>
        <span>CHARACTER</span>
        <span>•</span>
        <span>EXCELLENCE</span>
      </div>
    @else
      <!-- ─── MANUAL FORM PRINT LAYOUT (DEFAULT) ────────────────────────── -->
      <!-- 1. HEADER SECTION -->
      <div class="header-container">
        <div class="header-ayah">رَّبِّ زِدْنِي عِلْمًا</div>
        <div class="header-translation">"My Lord! Increase me in knowledge." (Quran 20:114)</div>

        <div class="header-main-grid">
          <!-- School Logo -->
          <div class="header-logo-box">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Noor Ul Huda School Logo">
          </div>

          <!-- School Title & Subtitle -->
          <div class="header-title-box">
            <div class="school-title">NOOR UL HUDA</div>
            <div class="school-subtitle">SUPERIOR SCHOOL</div>
            <div class="header-badge">STUDENT REPORT & PROGRESS CARD</div>
            <div class="header-period-bullets">• DAILY • WEEKLY • MONTHLY •</div>
          </div>

          <!-- Kids Illustration Graphic Badge -->
          <div class="header-kids-illustration">
            <div class="header-kids-badge">
              <div class="icon">👧🏻👦🏻</div>
              <span>EXCELLENCE IN EDUCATION</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. STUDENT DETAILS & REPORT TYPE CARD -->
      <div class="profile-card">
        <!-- Left: Photo & Student Details -->
        <div class="profile-left">
          <div class="student-photo-box">
            @if(!empty($data['student_photo_url']))
              <img src="{{ $data['student_photo_url'] }}" alt="Student Photo">
            @else
              <div class="student-photo-placeholder">👤</div>
            @endif
          </div>
          <div class="student-info-list">
            <div class="info-row">
              <span class="info-label">👤 Student Name :</span>
              <span class="info-value">{{ ($data['student_name'] ?? '') ?: '________________________' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">🏫 Class & Section :</span>
              <span class="info-value">{{ trim(($data['class_name'] ?? '') . ' ' . ($data['section'] ?? '')) ?: '________________________' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">🆔 Roll No. :</span>
              <span class="info-value">{{ ($data['roll_no'] ?? '') ?: '__________' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">📅 Date / Period :</span>
              <span class="info-value">{{ ($data['date_period'] ?? '') ?: date('F Y') }}</span>
            </div>
          </div>
        </div>

        <!-- Middle: Report Type -->
        @php
          $rType = $data['report_type'] ?? 'Monthly';
        @endphp
        <div class="type-box">
          <div class="box-title">REPORT TYPE (✓)</div>
          <div class="type-options">
            <div class="type-option-item">
              <span>Daily</span>
              <span class="checkbox-sq {{ $rType === 'Daily' ? 'checked' : '' }}">{{ $rType === 'Daily' ? '✓' : '' }}</span>
            </div>
            <div class="type-option-item">
              <span>Weekly</span>
              <span class="checkbox-sq {{ $rType === 'Weekly' ? 'checked' : '' }}">{{ $rType === 'Weekly' ? '✓' : '' }}</span>
            </div>
            <div class="type-option-item">
              <span>Monthly</span>
              <span class="checkbox-sq {{ $rType === 'Monthly' ? 'checked' : '' }}">{{ $rType === 'Monthly' ? '✓' : '' }}</span>
            </div>
            <div class="type-option-item">
              <span>Other :</span>
              <span style="font-size:9px;font-weight:700;">{{ !in_array($rType, ['Daily','Weekly','Monthly']) ? $rType : '_______' }}</span>
            </div>
          </div>
        </div>

        <!-- Right: Focus Area / Subject -->
        <div class="focus-box">
          <div class="box-title">FOCUS AREA / SUBJECT</div>
          <div class="focus-lines">
            {{ ($data['focus_area'] ?? '') ?: 'General Academics & Moral Conduct' }}
          </div>
        </div>
      </div>

      <!-- 3. MAIN TABLES GRID (PERFORMANCE & SKILLS SIDE BY SIDE) -->
      <div class="tables-grid">
        <!-- Performance Tracker Table -->
        <div class="table-card">
          <div class="table-card-header">
            <span class="icon">📊</span>
            <span>PERFORMANCE TRACKER</span>
          </div>
          <table class="report-table">
            <thead>
              <tr>
                <th style="width:34%;">SUBJECT / AREA</th>
                <th title="Excellent">EXCELLENT<br>★</th>
                <th title="Good">GOOD<br>👍</th>
                <th title="Average">AVERAGE<br>-</th>
                <th title="Needs Improvement">NEEDS IMP.<br>!</th>
                <th style="width:26%;">REMARKS</th>
              </tr>
            </thead>
            <tbody>
              @php
                $defaultSubjects = [
                  ['icon' => '📖', 'name' => 'Quran'],
                  ['icon' => '🕌', 'name' => 'Islamic Studies'],
                  ['icon' => '🔤', 'name' => 'English'],
                  ['icon' => '✍️', 'name' => 'Urdu'],
                  ['icon' => '➕', 'name' => 'Mathematics'],
                  ['icon' => '🔬', 'name' => 'Science'],
                  ['icon' => '🌍', 'name' => 'Social Studies'],
                  ['icon' => '💻', 'name' => 'Computer'],
                  ['icon' => '💡', 'name' => 'General Knowledge'],
                  ['icon' => '💬', 'name' => 'Other'],
                ];
              @endphp
              @foreach($defaultSubjects as $idx => $subjItem)
                @php
                  $rowRating = ($idx === 0 || $idx === 4) ? 'excellent' : 'good';
                @endphp
                <tr>
                  <td class="subject-name">
                    <span style="margin-right:2px;">{{ $subjItem['icon'] }}</span> {{ $subjItem['name'] }}
                  </td>
                  <td class="cell-check mark-star">{{ $rowRating === 'excellent' ? '★' : '' }}</td>
                  <td class="cell-check mark-thumb">{{ $rowRating === 'good' ? '👍' : '' }}</td>
                  <td class="cell-check mark-avg">{{ $rowRating === 'average' ? '✓' : '' }}</td>
                  <td class="cell-check mark-needs">{{ $rowRating === 'needs_improvement' ? '!' : '' }}</td>
                  <td class="cell-remarks"></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Skills & Attributes Table -->
        <div class="table-card">
          <div class="table-card-header">
            <span class="icon">👤</span>
            <span>SKILLS & ATTRIBUTES</span>
          </div>
          <table class="report-table">
            <thead>
              <tr>
                <th style="width:34%;">SKILL AREA</th>
                <th title="Excellent">EXCELLENT<br>★</th>
                <th title="Good">GOOD<br>👍</th>
                <th title="Average">AVERAGE<br>-</th>
                <th title="Needs Improvement">NEEDS IMP.<br>!</th>
                <th style="width:26%;">REMARKS</th>
              </tr>
            </thead>
            <tbody>
              @php
                $defaultSkills = [
                  ['icon' => '👥', 'name' => 'Attention & Participation'],
                  ['icon' => '😊', 'name' => 'Behaviour'],
                  ['icon' => '📋', 'name' => 'Assignments'],
                  ['icon' => '🕒', 'name' => 'Punctuality'],
                  ['icon' => '✏️', 'name' => 'Neatness & Presentation'],
                  ['icon' => '👤', 'name' => 'Confidence'],
                  ['icon' => '🤝', 'name' => 'Co-operation'],
                  ['icon' => '❤️', 'name' => 'Respect for Others'],
                ];
              @endphp
              @foreach($defaultSkills as $sIdx => $skItem)
                @php
                  $skRating = ($sIdx % 2 === 0) ? 'excellent' : 'good';
                @endphp
                <tr>
                  <td class="subject-name">
                    <span style="margin-right:2px;">{{ $skItem['icon'] }}</span> {{ $skItem['name'] }}
                  </td>
                  <td class="cell-check mark-star">{{ $skRating === 'excellent' ? '★' : '' }}</td>
                  <td class="cell-check mark-thumb">{{ $skRating === 'good' ? '👍' : '' }}</td>
                  <td class="cell-check mark-avg">{{ $skRating === 'average' ? '✓' : '' }}</td>
                  <td class="cell-check mark-needs">{{ $skRating === 'needs_improvement' ? '!' : '' }}</td>
                  <td class="cell-remarks"></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- 4. MIDDLE 3-BOX ROW -->
      <div class="middle-grid">
        <div class="box-card">
          <div class="box-card-header">
            <span>🏅</span>
            <span>LEARNING HIGHLIGHTS</span>
          </div>
          <div class="box-card-content">
            {{ ($data['learning_highlights'] ?? '') ?: 'Shows great interest in Quranic recitation & Mathematics. Consistently completes home assignments on time.' }}
          </div>
        </div>

        <div class="box-card">
          <div class="box-card-header">
            <span>📈</span>
            <span>AREAS TO IMPROVE</span>
          </div>
          <div class="box-card-content">
            {{ ($data['areas_to_improve'] ?? '') ?: 'Needs slight improvement in English vocabulary & handwriting neatness.' }}
          </div>
        </div>

        <div class="box-card">
          <div class="box-card-header">
            <span>📅</span>
            <span>ATTENDANCE</span>
          </div>
          <div class="attendance-list">
            <div class="attendance-row">
              <span>Total Days :</span>
              <span class="val">{{ ($data['attendance_total'] ?? '') ?: '25' }}</span>
            </div>
            <div class="attendance-row">
              <span>Present Days :</span>
              <span class="val">{{ ($data['attendance_present'] ?? '') ?: '24' }}</span>
            </div>
            <div class="attendance-row">
              <span>Absent Days :</span>
              <span class="val">{{ ($data['attendance_absent'] ?? '') ?: '1' }}</span>
            </div>
            <div class="attendance-row" style="margin-top:2px;">
              <span>Attendance % :</span>
              <span class="val" style="color:#166534;font-weight:900;">{{ ($data['attendance_pct'] ?? '') ?: '96%' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 5. PROGRESS SUMMARY (OVERALL) -->
      <div class="summary-card">
        <div class="table-card-header">
          <span class="icon">🚀</span>
          <span>PROGRESS SUMMARY (OVERALL)</span>
        </div>
        <div class="summary-grid">
          <div class="stars-list">
            <div class="star-category-item">
              <span class="star-cat-icon">🎓</span>
              <span class="star-cat-title">ACADEMIC</span>
              <div class="stars-render">
                @php $aCount = (int)($data['academic_stars'] ?? 5); @endphp
                @for($i=1; $i<=5; $i++) {{ $i <= $aCount ? '★' : '☆' }} @endfor
              </div>
            </div>

            <div class="star-category-item">
              <span class="star-cat-icon">🕌</span>
              <span class="star-cat-title">ISLAMIC DEV.</span>
              <div class="stars-render">
                @php $iCount = (int)($data['islamic_stars'] ?? 5); @endphp
                @for($i=1; $i<=5; $i++) {{ $i <= $iCount ? '★' : '☆' }} @endfor
              </div>
            </div>

            <div class="star-category-item">
              <span class="star-cat-icon">👤</span>
              <span class="star-cat-title">PERSONAL DEV.</span>
              <div class="stars-render">
                @php $pCount = (int)($data['personal_stars'] ?? 5); @endphp
                @for($i=1; $i<=5; $i++) {{ $i <= $pCount ? '★' : '☆' }} @endfor
              </div>
            </div>

            <div class="star-category-item">
              <span class="star-cat-icon">⭐</span>
              <span class="star-cat-title">BEHAVIOUR</span>
              <div class="stars-render">
                @php $bCount = (int)($data['behaviour_stars'] ?? 5); @endphp
                @for($i=1; $i<=5; $i++) {{ $i <= $bCount ? '★' : '☆' }} @endfor
              </div>
            </div>

            <div class="star-category-item">
              <span class="star-cat-icon">🏆</span>
              <span class="star-cat-title">CO-CURRICULAR</span>
              <div class="stars-render">
                @php $cCount = (int)($data['cocurricular_stars'] ?? 5); @endphp
                @for($i=1; $i<=5; $i++) {{ $i <= $cCount ? '★' : '☆' }} @endfor
              </div>
            </div>
          </div>

          <div class="overall-status-box">
            <div class="overall-status-title">OVERALL PROGRESS</div>
            <div class="overall-circle">
              👍
            </div>
            <div class="overall-text">{{ ($data['overall_progress'] ?? '') ?: 'GOOD' }}</div>
          </div>
        </div>
      </div>

      <!-- 6. 3-BOX REMARKS GRID -->
      <div class="remarks-grid">
        <div class="box-card">
          <div class="box-card-header">
            <span>✏️</span>
            <span>TEACHER'S REMARKS</span>
          </div>
          <div class="box-card-content">
            {{ ($data['teacher_remarks'] ?? '') ?: 'Demonstrates exemplary academic performance and excellent moral conduct in school.' }}
          </div>
        </div>

        <div class="box-card">
          <div class="box-card-header">
            <span>👤</span>
            <span>PARENT'S REMARKS</span>
          </div>
          <div class="box-card-content">
            {{ ($data['parent_remarks'] ?? '') ?: 'Very satisfied with student progress and school environment.' }}
          </div>
        </div>

        <div class="box-card">
          <div class="box-card-header">
            <span>🎯</span>
            <span>ACTION PLAN / NEXT STEPS</span>
          </div>
          <div class="box-card-content">
            {{ ($data['action_plan'] ?? '') ?: 'Continue reading practice, participate in upcoming Tajweed competition & sports gala.' }}
          </div>
        </div>
      </div>

      <!-- 7. SIGNATURES & STAMP SECTION -->
      <div class="sig-section">
        <div class="sig-box">
          <div class="sig-line">{{ !empty($data['subject_teacher']) && $data['subject_teacher'] !== 'Subject Teacher' ? $data['subject_teacher'] : '' }}</div>
          <div class="sig-title">✏️ Subject Teacher</div>
        </div>

        <div class="sig-box">
          <div class="sig-line">{{ !empty($data['class_teacher']) && $data['class_teacher'] !== 'Class Teacher' ? $data['class_teacher'] : '' }}</div>
          <div class="sig-title">✏️ Class Teacher</div>
        </div>

        <div class="sig-box">
          <div class="sig-line">{{ !empty($data['parent_guardian']) && $data['parent_guardian'] !== 'Parent / Guardian' ? $data['parent_guardian'] : '' }}</div>
          <div class="sig-title">👤 Parent / Guardian</div>
        </div>

        <div class="sig-box">
          <div class="sig-line">{{ !empty($data['principal']) && $data['principal'] !== 'Principal' ? $data['principal'] : '' }}</div>
          <div class="sig-title">✏️ Principal</div>
        </div>

        <div class="stamp-box">
          <span class="stamp-text">School<br>Stamp</span>
        </div>
      </div>

      <!-- 8. FOOTER RIBBON -->
      <div class="footer-ribbon">
        <span>KNOWLEDGE</span>
        <span>•</span>
        <span>FAITH</span>
        <span>•</span>
        <span class="book-icon">📖</span>
      </div>
    @endif

  </div>
</div>

</body>
</html>
