<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Remarks &amp; Signature — {{ $data['student_name'] ?: 'Noor Ul Huda Superior School' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
  /* ===== RESET & BASE ===== */
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { width: 100%; height: 100%; }

  body {
    font-family: 'Times New Roman', 'Georgia', serif;
    background: #e2e8f0;
    color: #3d1a06;
    line-height: 1.3;
  }

  /* ===== PRINT A4 PORTRAIT PAGE SETUP ===== */
  @page {
    size: A4 portrait;
    margin: 0;
  }

  @media print {
    body { background: #fff; }
    .no-print { display: none !important; }
    .cert-page {
      box-shadow: none !important;
      margin: 0 !important;
      width: 100% !important;
      height: 100% !important;
      page-break-after: avoid;
    }
  }

  /* ===== PORTRAIT CONTAINER ===== */
  .cert-page {
    width: 210mm;
    height: 297mm;
    margin: 15px auto;
    background: #fdfaf3;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(61,26,6,0.3);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* ===== OUTER BORDER WITH ROYAL DARK BROWN & GOLD CORNERS ===== */
  .border-outer {
    position: absolute;
    inset: 12px;
    border: 12px solid #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -6px;
    pointer-events: none;
    z-index: 10;
  }

  .border-inner {
    position: absolute;
    inset: 28px;
    border: 1.5px solid #c7ad8d;
    pointer-events: none;
    z-index: 10;
  }

  /* Corner Filigree SVG Overlays */
  .corner-svg {
    position: absolute;
    width: 60px;
    height: 60px;
    z-index: 11;
    fill: #c7ad8d;
  }
  .corner-tl { top: 28px; left: 28px; }
  .corner-tr { top: 28px; right: 28px; transform: scaleX(-1); }
  .corner-bl { bottom: 28px; left: 28px; transform: scaleY(-1); }
  .corner-br { bottom: 28px; right: 28px; transform: scale(-1); }

  /* ===== CERTIFICATE CONTENT INNER ===== */
  .cert-inner {
    position: relative;
    padding: 38px 44px 12px;
    z-index: 2;
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
  }

  /* Header Top Row */
  .header-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2px;
    position: relative;
    z-index: 3;
  }

  .official-logo-img {
    width: 95px;
    height: 95px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(61,26,6,0.15));
  }

  .student-profile-photo-img {
    width: 85px;
    height: 102px;
    object-fit: cover;
    border: 2px solid #3d1a06;
    outline: 1.5px solid #c7ad8d;
    outline-offset: -2px;
    border-radius: 6px;
    box-shadow: 0 3px 6px rgba(61,26,6,0.15);
    margin-top: 18px;
    margin-right: 22px;
  }

  .header-right-emblem {
    width: 85px;
    height: 102px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 12px;
    margin-right: 18px;
    filter: drop-shadow(0 3px 6px rgba(61, 26, 6, 0.18));
  }

  .header-center-text {
    flex: 1;
    text-align: center;
    padding: 0 10px;
  }

  .arabic-verse {
    font-size: 22px;
    font-weight: bold;
    color: #3d1a06;
    font-family: 'Amiri', 'Traditional Arabic', 'Georgia', serif;
    line-height: 1.1;
  }

  .verse-translation {
    font-size: 10px;
    color: #3d1a06;
    font-style: italic;
    margin-top: 1px;
    margin-bottom: 4px;
    opacity: 0.9;
  }

  .school-name {
    font-size: 28px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
    line-height: 1;
  }

  .school-subtitle-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin: 3px 0 4px;
  }

  .sub-line {
    height: 1.5px;
    width: 40px;
    background: #c7ad8d;
  }

  .school-subtitle {
    font-size: 12.5px;
    color: #3d1a06;
    letter-spacing: 3px;
    font-weight: 800;
    font-family: 'Times New Roman', serif;
    text-transform: uppercase;
  }

  .tagline-badge {
    display: inline-block;
    background: #3d1a06;
    color: #ffffff;
    border: 1px solid #c7ad8d;
    border-radius: 20px;
    padding: 2.5px 18px;
    font-size: 9.5px;
    font-weight: bold;
    letter-spacing: 2px;
    font-family: Arial, sans-serif;
  }

  /* Filigree Ornament Divider */
  .filigree-divider {
    text-align: center;
    margin: 3px 0;
    color: #c7ad8d;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .filigree-line {
    flex: 1;
    max-width: 130px;
    height: 1px;
    background: linear-gradient(to right, transparent, #c7ad8d, transparent);
  }

  /* ===== CERTIFICATE TITLE BANNER ===== */
  .cert-title-wrap {
    background: #3d1a06;
    color: #ffffff;
    text-align: center;
    padding: 6px 20px;
    border-radius: 4px;
    border: 1.5px solid #c7ad8d;
    margin: 4px 30px;
    box-shadow: 0 3px 10px rgba(61,26,6,0.15);
  }

  .cert-title {
    font-size: 21px;
    font-weight: 900;
    color: #ffffff;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
  }

  /* ===== ELEGANT DATA TABLE STYLING FOR STUDENT PROFILE ===== */
  .student-table-container {
    margin: 6px 5px;
    border: 1.5px solid #c7ad8d;
    border-radius: 6px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(61,26,6,0.06);
  }

  .cert-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    color: #3d1a06;
  }

  .cert-table th {
    background: #3d1a06;
    color: #ffffff;
    padding: 7px 12px;
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
    border-bottom: 1.5px solid #c7ad8d;
    text-align: left;
  }

  .cert-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #c7ad8d;
    vertical-align: middle;
  }

  .cert-table tr:last-child td {
    border-bottom: none;
  }

  .cert-table tr:nth-child(even) {
    background: #fdfaf3;
  }

  .table-label {
    font-weight: bold;
    color: #3d1a06;
    width: 22%;
  }

  .table-value {
    color: #000000;
    font-weight: 700;
    font-size: 13.5px;
  }

  /* ===== REMARKS & SIGNATURES MAIN CONTAINER ===== */
  .remarks-sig-section {
    border: 2px solid #3d1a06;
    border-radius: 12px;
    background: #fdfaf3;
    padding: 12px;
    position: relative;
    box-shadow: 0 4px 14px rgba(61,26,6,0.1);
    margin: 6px 5px;
  }

  /* Banner Header inside Section Card */
  .section-card-header {
    background: #3d1a06;
    border: 1.5px solid #c7ad8d;
    border-radius: 24px;
    padding: 6px 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin: -24px auto 14px;
    width: fit-content;
    box-shadow: 0 3px 8px rgba(61,26,6,0.25);
  }

  .section-card-icon {
    width: 32px;
    height: 32px;
    background: #c7ad8d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #3d1a06;
  }

  .section-card-title {
    font-size: 17px;
    font-weight: 900;
    color: #ffffff;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
  }

  /* Dual Remarks Boxes Grid */
  .dual-remarks-grid {
    display: flex;
    gap: 12px;
    margin-bottom: 14px;
  }

  .remarks-box-card {
    flex: 1;
    border: 1.5px solid #c7ad8d;
    border-radius: 10px;
    background: #ffffff;
    padding: 10px;
    position: relative;
    box-shadow: 0 2px 6px rgba(61,26,6,0.04);
    display: flex;
    flex-direction: column;
  }

  .remarks-box-badge {
    background: #3d1a06;
    color: #ffffff;
    border-radius: 16px;
    padding: 4px 16px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
    align-self: center;
    margin-bottom: 10px;
    border: 1px solid #c7ad8d;
  }

  .remarks-content-area {
    flex: 1;
    min-height: 130px;
    position: relative;
    padding: 4px 6px;
  }

  /* Dotted Lines Background for Handwriting or Text Baseline */
  .dotted-lines-bg {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 125px;
  }

  .dotted-line {
    width: 100%;
    border-bottom: 1.5px dashed #c7ad8d;
    height: 25px;
  }

  .remarks-text-overlay {
    position: absolute;
    inset: 0;
    font-size: 13px;
    color: #1a0a03;
    font-family: 'Georgia', serif;
    font-style: italic;
    line-height: 25px;
    font-weight: 600;
    white-space: pre-wrap;
    word-break: break-word;
  }

  /* Signatures Box inside Section */
  .signatures-card-container {
    border: 1.5px solid #c7ad8d;
    border-radius: 10px;
    background: #ffffff;
    padding: 12px 14px;
    margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(61,26,6,0.04);
  }

  .signatures-grid-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 10px;
  }

  .sig-column-item {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .sig-avatar-icon {
    width: 44px;
    height: 44px;
    background: #3d1a06;
    border: 2px solid #c7ad8d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c7ad8d;
    flex-shrink: 0;
    box-shadow: 0 2px 5px rgba(61,26,6,0.15);
  }

  .sig-details-wrap {
    flex: 1;
  }

  .sig-header-title {
    font-size: 11.5px;
    font-weight: 900;
    color: #3d1a06;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: Arial, sans-serif;
    margin-bottom: 6px;
  }

  .sig-line-bar {
    width: 100%;
    border-top: 1.5px solid #3d1a06;
    margin-bottom: 4px;
  }

  .sig-authority-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #000000;
    margin-bottom: 2px;
  }

  .sig-date-row {
    font-size: 10.5px;
    font-weight: 800;
    color: #3d1a06;
    font-family: Arial, sans-serif;
  }

  .sig-date-fill {
    display: inline-block;
    border-bottom: 1px solid #3d1a06;
    min-width: 80px;
    text-align: center;
  }

  /* Quote Pill Banner */
  .quote-pill-banner {
    border: 1.5px solid #c7ad8d;
    background: #ffffff;
    border-radius: 20px;
    padding: 6px 16px;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    color: #3d1a06;
    font-style: italic;
    font-family: 'Georgia', serif;
    box-shadow: 0 2px 6px rgba(61,26,6,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .quote-star {
    color: #c7ad8d;
    font-size: 13px;
  }

  /* ===== DARK CHOCOLATE FOOTER BAR ===== */
  .footer-contact-bar {
    background: #3d1a06;
    color: #ffffff;
    margin: 0 -44px -12px;
    padding: 8px 44px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 10.5px;
    font-weight: bold;
    font-family: Arial, sans-serif;
    border-top: 2px solid #c7ad8d;
  }

  .contact-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffffff;
  }

  .contact-item svg {
    fill: #c7ad8d;
  }

  /* Print Action Buttons Bar (Screen Only) */
  .print-actions-bar {
    width: 210mm;
    margin: 15px auto 0;
    display: flex;
    justify-content: center;
    gap: 15px;
  }

  .btn-action {
    padding: 10px 24px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 14px;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  }

  .btn-primary-print { background: #3d1a06; color: #ffffff; border: 1px solid #c7ad8d; }
  .btn-primary-print:hover { background: #542408; }
  .btn-close-print { background: #cbd5e1; color: #1e293b; }
  .btn-close-print:hover { background: #94a3b8; }
</style>
</head>
<body>

<!-- Screen-only Print Bar -->
<div class="print-actions-bar no-print">
    <button class="btn-action btn-primary-print" onclick="window.print()">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"></path></svg>
        Print / Download Remarks &amp; Signature Card
    </button>
    <a href="javascript:window.close()" class="btn-action btn-close-print">✕ Close Tab</a>
</div>

<div class="cert-page">
  <!-- Outer & Inner Gold/Chocolate Decorative Frame -->
  <div class="border-outer"></div>
  <div class="border-inner"></div>

  <!-- Corner Filigree Ornaments -->
  <svg class="corner-svg corner-tl" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-tr" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-bl" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-br" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>

  <div class="cert-inner">

    <!-- Header Section with Official School Logo -->
    <div class="header-top-row">
      <img src="{{ asset('assets/images/logo.png') }}" alt="Noor Ul Huda School Logo" class="official-logo-img">

      <div class="header-center-text">
        <div class="arabic-verse">رَّبِّ زِدْنِي عِلْمًا</div>
        <div class="verse-translation">"My Lord! Increase me in knowledge." (Quran 20:114)</div>

        <div class="school-name">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA' }}</div>
        <div class="school-subtitle-wrap">
          <div class="sub-line"></div>
          <div class="school-subtitle">{{ $globalSchoolInfo->school_code ?? 'SUPERIOR SCHOOL' }}</div>
          <div class="sub-line"></div>
        </div>
        <div class="tagline-badge">{{ $globalSchoolInfo->tagline ?? 'LEARN SUPERIOR • BE SUPERIOR' }}</div>
      </div>

      @if(!empty($data['student_photo_url']))
        <img src="{{ $data['student_photo_url'] }}" alt="Student Photo" class="student-profile-photo-img">
      @else
        <div class="header-right-emblem">
          <svg width="80" height="95" viewBox="0 0 100 115" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="goldGradStudent" x1="0" y1="0" x2="100" y2="115" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#f5e6d3"/>
                <stop offset="40%" stop-color="#c7ad8d"/>
                <stop offset="100%" stop-color="#8c6c45"/>
              </linearGradient>
              <linearGradient id="innerBgGradStudent" x1="0" y1="0" x2="0" y2="100" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#ffffff"/>
                <stop offset="100%" stop-color="#fdfaf3"/>
              </linearGradient>
            </defs>
            <!-- Outer Shield Outer Frame -->
            <path d="M50 4 L92 20 V60 C92 86 50 111 50 111 C50 111 8 86 8 60 V20 L50 4 Z" fill="url(#goldGradStudent)" stroke="#3d1a06" stroke-width="2.5"/>
            <!-- Inner Shield Area -->
            <path d="M50 11 L84 24 V58 C84 79 50 101 50 101 C50 101 16 79 16 58 V24 L50 11 Z" fill="url(#innerBgGradStudent)" stroke="#c7ad8d" stroke-width="1.5"/>
            <path d="M50 14 L81 26 V56 C81 76 50 97 50 97 C50 97 19 76 19 56 V26 L50 14 Z" fill="none" stroke="#c7ad8d" stroke-width="1" stroke-dasharray="3 2"/>

            <!-- Central Ornamental Circle -->
            <circle cx="50" cy="48" r="23" fill="#fdfaf3" stroke="#c7ad8d" stroke-width="1.5"/>
            <circle cx="50" cy="48" r="20" fill="none" stroke="#3d1a06" stroke-width="1"/>

            <!-- Book & Feather Emblem in Center -->
            <path d="M37 52 C41 49 46 50 50 52 C54 50 59 49 63 52 V40 C59 38 54 39 50 41 C46 39 41 38 37 40 Z" fill="#3d1a06"/>
            <path d="M50 41 V52" stroke="#fdfaf3" stroke-width="1.2"/>
            <path d="M55 31 Q62 26 66 34 Q59 38 53 43 L51 41 Z" fill="#c7ad8d" stroke="#3d1a06" stroke-width="0.8"/>
            <path d="M42 21 L45 25 L50 20 L55 25 L58 21 L56 27 H44 Z" fill="#c7ad8d" stroke="#3d1a06" stroke-width="0.8"/>

            <!-- Lower Ribbon Banner -->
            <path d="M22 78 L34 73 L50 76 L66 73 L78 78 L74 88 L50 83 L26 88 Z" fill="#3d1a06" stroke="#c7ad8d" stroke-width="1.2"/>
            <path d="M34 73 L50 76 L66 73" stroke="#c7ad8d" stroke-width="1" fill="none"/>
            <circle cx="34" cy="81" r="1.5" fill="#c7ad8d"/>
            <circle cx="50" cy="79.5" r="1.8" fill="#c7ad8d"/>
            <circle cx="66" cy="81" r="1.5" fill="#c7ad8d"/>
          </svg>
        </div>
      @endif
    </div>

    <!-- Title Banner -->
    <div>
      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="45" height="13" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>

      <div class="cert-title-wrap">
        <div class="cert-title">STUDENT REMARKS &amp; SIGNATURE CARD</div>
      </div>

      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="32" height="9" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>
    </div>

    <!-- Structured Data Table Container for Student Particulars (Same as Admission Form) -->
    <div style="position: relative;">
      <div class="student-table-container">
        <table class="cert-table">
          <thead>
            <tr>
              <th colspan="4">STUDENT PROFILE &amp; ACADEMIC PARTICULARS</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="table-label">Student Name</td>
              <td class="table-value" style="width: 38%;">{{ $data['student_name'] }}</td>
              <td class="table-label">Father's Name</td>
              <td class="table-value">{{ $data['father_name'] }}</td>
            </tr>
            <tr>
              <td class="table-label">Admission No.</td>
              <td class="table-value">{{ $data['admission_no'] }}</td>
              <td class="table-label">Roll No.</td>
              <td class="table-value">{{ $data['roll_no'] ?: 'N/A' }}</td>
            </tr>
            <tr>
              <td class="table-label">Class &amp; Section</td>
              <td class="table-value">{{ $data['class_name'] }} {{ $data['section'] ? "({$data['section']})" : '' }}</td>
              <td class="table-label">Academic Session</td>
              <td class="table-value">{{ $data['session'] }}</td>
            </tr>
            <tr>
              <td class="table-label">Date of Birth</td>
              <td class="table-value">{{ $data['date_of_birth'] ?: 'N/A' }}</td>
              <td class="table-label">Issue Date</td>
              <td class="table-value">{{ $data['issue_date'] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- REMARKS & SIGNATURES MAIN CARD SECTION -->
    <div class="remarks-sig-section">
      
      <!-- Section Card Header Banner -->
      <div class="section-card-header">
        <div class="section-card-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
            <path d="M9 12h6"></path>
            <path d="M9 16h6"></path>
          </svg>
        </div>
        <div class="section-card-title">REMARKS &amp; SIGNATURES</div>
        <div style="color: #c7ad8d; font-size: 14px;">✦</div>
      </div>

      <!-- Dual Remarks Boxes (Teacher's Remarks & Principal's Remarks) -->
      <div class="dual-remarks-grid">
        
        <!-- TEACHER'S REMARKS BOX -->
        <div class="remarks-box-card">
          <div class="remarks-box-badge">TEACHER'S REMARKS</div>
          <div class="remarks-content-area">
            <div class="dotted-lines-bg">
              <div class="dotted-line"></div>
              <div class="dotted-line"></div>
              <div class="dotted-line"></div>
              <div class="dotted-line"></div>
              <div class="dotted-line"></div>
            </div>
            @if(!empty($data['teacher_remarks']))
              <div class="remarks-text-overlay">{{ $data['teacher_remarks'] }}</div>
            @endif
          </div>
        </div>

        <!-- PRINCIPAL'S REMARKS BOX -->
        <div class="remarks-box-card">
          <div class="remarks-box-badge">PRINCIPAL'S REMARKS</div>
          <div class="remarks-content-area">
            <div class="dotted-lines-bg">
              <div class="dotted-line"></div>
              <div class="dotted-line"></div>
              <div class="dotted-line"></div>
              <div class="dotted-line"></div>
              <div class="dotted-line"></div>
            </div>
            @if(!empty($data['principal_remarks']))
              <div class="remarks-text-overlay">{{ $data['principal_remarks'] }}</div>
            @endif
          </div>
        </div>

      </div>

      <!-- Filigree Divider above Signatures -->
      <div class="filigree-divider" style="margin: 6px 0 10px;">
        <div class="filigree-line"></div>
        <svg width="40" height="12" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>

      <!-- SIGNATURES CARD CONTAINER -->
      <div class="signatures-card-container">
        <div class="signatures-grid-row">
          
          <!-- CLASS TEACHER -->
          <div class="sig-column-item">
            <div class="sig-avatar-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <div class="sig-details-wrap">
              <div class="sig-header-title">CLASS TEACHER</div>
              <div class="sig-line-bar"></div>
              @if(!empty($data['class_teacher']))
                <div class="sig-authority-name">{{ $data['class_teacher'] }}</div>
              @endif
            </div>
          </div>

          <!-- PRINCIPAL -->
          <div class="sig-column-item">
            <div class="sig-avatar-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
              </svg>
            </div>
            <div class="sig-details-wrap">
              <div class="sig-header-title">PRINCIPAL</div>
              <div class="sig-line-bar"></div>
              @if(!empty($data['principal']))
                <div class="sig-authority-name">{{ $data['principal'] }}</div>
              @endif
            </div>
          </div>

          <!-- PARENT / GUARDIAN -->
          <div class="sig-column-item">
            <div class="sig-avatar-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M9 21v-2a4 4 0 0 1 3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                <circle cx="9" cy="7" r="4"></circle>
              </svg>
            </div>
            <div class="sig-details-wrap">
              <div class="sig-header-title">PARENT / GUARDIAN</div>
              <div class="sig-line-bar"></div>
            </div>
          </div>

        </div>
      </div>

      <!-- Quote Pill Banner -->
      <div class="quote-pill-banner">
        <span class="quote-star">★</span>
        <span>Keep striving, keep praying, and keep believing in yourself.</span>
        <span class="quote-star">★</span>
      </div>

    </div><!-- end remarks-sig-section -->

    <!-- Bottom Dark Chocolate Contact Bar (Same as Admission Form) -->
    <div class="footer-contact-bar">
      <div class="contact-item">
        <svg width="14" height="14" viewBox="0 0 24 24"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.27c1.21.49 2.53.76 3.88.76a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.35.27 2.67.76 3.88a1 1 0 01-.27 1.11l-2.37 2.4z"/></svg>
        {{ $globalSchoolInfo->phone ?? '03266850002' }}
      </div>
      <div class="contact-item">
        <svg width="14" height="14" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
        {{ $globalSchoolInfo->email ?? 'Superiorschoolnps@gmail.com' }}
      </div>
      <div class="contact-item">
        <svg width="14" height="14" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
        {{ $globalSchoolInfo->full_address ?? 'Dhanote, District Lodhran' }}
      </div>
    </div>

  </div><!-- end cert-inner -->
</div><!-- end cert-page -->

</body>
</html>
