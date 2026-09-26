<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Character Certificate — {{ $data['student_name'] ?: 'Noor Ul Huda Superior School' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
  /* ===== RESET & BASE ===== */
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { width: 100%; height: 100%; }

  body {
    font-family: 'Outfit', 'Times New Roman', 'Georgia', serif;
    background: #e2e8f0;
    color: #3d1a06;
    line-height: 1.3;
  }

  /* ===== PRINT LANDSCAPE PAGE SETUP ===== */
  @page {
    size: A4 landscape;
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

  /* ===== LANDSCAPE CERTIFICATE CONTAINER ===== */
  .cert-page {
    width: 297mm;
    height: 210mm;
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
    inset: 10px;
    border: 10px solid #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -5px;
    pointer-events: none;
    z-index: 10;
  }

  .border-inner {
    position: absolute;
    inset: 24px;
    border: 1.5px solid #c7ad8d;
    pointer-events: none;
    z-index: 10;
  }

  /* Corner Filigree SVG Overlays */
  .corner-svg {
    position: absolute;
    width: 55px;
    height: 55px;
    z-index: 11;
    fill: #c7ad8d;
  }
  .corner-tl { top: 24px; left: 24px; }
  .corner-tr { top: 24px; right: 24px; transform: scaleX(-1); }
  .corner-bl { bottom: 24px; left: 24px; transform: scaleY(-1); }
  .corner-br { bottom: 24px; right: 24px; transform: scale(-1); }

  /* ===== CERTIFICATE CONTENT INNER ===== */
  .cert-inner {
    position: relative;
    padding: 34px 40px 8px;
    z-index: 2;
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
  }

  /* Top Left Ribbon Seal Badge with Spacing from Top Border */
  .top-left-ribbon-badge {
    position: absolute;
    top: 38px;
    left: 45px;
    z-index: 15;
    width: 80px;
    filter: drop-shadow(0 4px 8px rgba(61,26,6,0.25));
  }

  /* Top Right Mosque Silhouette */
  .top-right-mosque {
    position: absolute;
    top: 38px;
    right: 45px;
    z-index: 5;
    width: 140px;
    opacity: 0.85;
    pointer-events: none;
  }

  /* ===== HEADER TEXT SECTION ===== */
  .cert-header {
    text-align: center;
    position: relative;
    z-index: 3;
    margin: 0 auto;
    width: 100%;
    max-width: 650px;
  }

  .arabic-verse {
    font-size: 21px;
    font-weight: bold;
    color: #3d1a06;
    font-family: 'Amiri', 'Traditional Arabic', serif;
    line-height: 1.2;
    margin-bottom: 2px;
  }

  .verse-translation {
    font-size: 9.5px;
    color: #3d1a06;
    font-style: italic;
    margin-bottom: 6px;
    opacity: 0.85;
  }

  .school-name {
    font-size: 28px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
    line-height: 1.1;
    margin-top: 2px;
  }

  .school-subtitle-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin: 2px 0;
  }

  .sub-line {
    height: 1.5px;
    width: 40px;
    background: #c7ad8d;
  }

  .school-subtitle {
    font-size: 11.5px;
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
    padding: 1.5px 16px;
    font-size: 9px;
    font-weight: bold;
    letter-spacing: 1.8px;
    font-family: Arial, sans-serif;
  }

  /* ===== CERTIFICATE TITLE & META BAR ===== */
  .cert-title-wrap {
    background: linear-gradient(135deg, #3d1a06 0%, #251004 100%);
    color: #ffffff;
    text-align: center;
    padding: 5px 20px;
    border-radius: 4px;
    border: 1.5px solid #c7ad8d;
    margin: 2px 40px;
    box-shadow: 0 3px 8px rgba(61,26,6,0.2);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .cert-title {
    font-size: 20px;
    font-weight: 900;
    color: #ffffff;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
    flex: 1;
    text-align: center;
  }

  .cert-meta-item {
    font-size: 10.5px;
    font-weight: 700;
    color: #c7ad8d;
    background: rgba(255,255,255,0.08);
    padding: 2px 10px;
    border-radius: 4px;
    border: 1px solid rgba(199,173,141,0.3);
  }

  /* Filigree Ornament Divider */
  .filigree-divider {
    text-align: center;
    margin: 2px 0;
    color: #c7ad8d;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  .filigree-line {
    flex: 1;
    max-width: 160px;
    height: 1px;
    background: linear-gradient(to right, transparent, #c7ad8d, transparent);
  }

  /* ===== MAIN LANDSCAPE GRID LAYOUT ===== */
  .landscape-main-grid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 2px 5px;
    position: relative;
    z-index: 4;
    gap: 8px;
  }

  /* Left & Right Badges Column */
  .badges-col {
    display: flex;
    flex-direction: column;
    gap: 6px;
    width: 100px;
    z-index: 5;
    flex-shrink: 0;
  }

  .badge-item-card {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 9px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 0.5px;
    font-family: Arial, sans-serif;
    background: #ffffff;
    border: 1.5px solid #c7ad8d;
    border-radius: 12px;
    padding: 3px 8px;
    box-shadow: 0 2px 5px rgba(61,26,6,0.08);
  }

  .badge-icon-svg {
    width: 14px;
    height: 14px;
    fill: #3d1a06;
    flex-shrink: 0;
  }

  /* 3D Student Characters Containers */
  .student-char-boy {
    width: 105px;
    height: 205px;
    object-fit: contain;
    filter: drop-shadow(0 4px 8px rgba(61,26,6,0.25));
    flex-shrink: 0;
  }

  .student-char-girl {
    width: 105px;
    height: 205px;
    object-fit: contain;
    filter: drop-shadow(0 4px 8px rgba(61,26,6,0.25));
    flex-shrink: 0;
  }

  /* CENTER FORM BODY CONTENT */
  .center-form-body {
    flex: 1;
    padding: 0 6px;
    text-align: center;
  }

  /* Executive Student Particulars Card Table */
  .student-data-table-wrap {
    background: #ffffff;
    border: 2px solid #3d1a06;
    outline: 1.5px solid #c7ad8d;
    outline-offset: -4px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(61,26,6,0.08);
    margin-bottom: 6px;
  }

  .data-grid-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
  }

  .data-grid-table td {
    padding: 5px 10px;
    border-bottom: 1px solid #ebdccb;
    border-right: 1.5px solid #c7ad8d;
    vertical-align: middle;
  }

  .data-grid-table tr:last-child td {
    border-bottom: none;
  }

  .data-grid-table td:last-child {
    border-right: none;
  }

  .data-grid-table tr:nth-child(even) {
    background: #fdfaf3;
  }

  .field-label-cell {
    font-weight: 800;
    color: #3d1a06;
    font-size: 11px;
    width: 32%;
    white-space: nowrap;
    background: rgba(61,26,6,0.03);
    text-align: left;
  }

  .field-label-inner {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .label-icon-box {
    width: 20px;
    height: 20px;
    background: #f4ece1;
    border: 1px solid #dfcfbc;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .label-icon {
    width: 12px;
    height: 12px;
    fill: #3d1a06;
  }

  .field-value-cell {
    color: #0c0a09;
    font-weight: 800;
    font-size: 12.5px;
    text-align: left;
  }

  .value-highlight-pill {
    display: inline-block;
    background: rgba(61,26,6,0.04);
    border-left: 3px solid #3d1a06;
    padding: 1px 8px;
    border-radius: 0 4px 4px 0;
    font-weight: 900;
    color: #3d1a06;
  }

  .paragraph-eval-text {
    font-size: 11.5px;
    line-height: 1.45;
    color: #3d1a06;
    margin: 4px 0 6px;
    text-align: center;
    font-family: 'Georgia', serif;
  }

  .paragraph-eval-text strong {
    color: #3d1a06;
    font-weight: 900;
  }

  /* Assessment Rating Row */
  .rating-boxes-row {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 6px;
  }

  .rating-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fdfaf3;
    color: #3d1a06;
    border: 1.5px solid #c7ad8d;
    padding: 2.5px 12px;
    border-radius: 12px;
    font-size: 10.5px;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(61,26,6,0.1);
  }

  .rating-pill.selected {
    background: linear-gradient(135deg, #3d1a06 0%, #240f03 100%);
    color: #ffffff;
    border-color: #c7ad8d;
  }

  /* Remarks Micro Cards Grid */
  .remarks-grid {
    display: flex;
    gap: 8px;
    margin-top: 4px;
  }

  .remarks-card {
    flex: 1;
    border: 1.5px solid #c7ad8d;
    background: #ffffff;
    border-radius: 6px;
    padding: 5px 8px;
    text-align: left;
    box-shadow: 0 2px 5px rgba(61,26,6,0.05);
  }

  .remarks-card-title {
    font-size: 9px;
    font-weight: 900;
    color: #3d1a06;
    display: flex;
    align-items: center;
    gap: 4px;
    border-bottom: 1px solid #ebdccb;
    padding-bottom: 2px;
    margin-bottom: 3px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  .remarks-card-body {
    font-size: 10px;
    color: #1e1b18;
    font-style: italic;
    line-height: 1.25;
    font-weight: 600;
  }

  /* ===== SIGNATURE SECTION ===== */
  .signatures-wrap {
    margin: 2px 10px 4px;
    position: relative;
    z-index: 5;
  }

  .signatures-grid {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    text-align: center;
  }

  .sig-box {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .sig-line {
    width: 85%;
    border-top: 1.5px solid #3d1a06;
    margin-bottom: 3px;
  }

  .sig-title {
    font-size: 10.5px;
    font-weight: 800;
    color: #3d1a06;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .sig-val {
    font-size: 10px;
    color: #000000;
    margin-top: 1px;
    font-weight: 700;
  }

  /* Center Medal Seal in Signatures Row */
  .center-stamp-wrap {
    flex: 0 0 75px;
    position: relative;
    top: 2px;
    text-align: center;
  }

  .center-medal-seal {
    width: 65px;
    filter: drop-shadow(0 3px 6px rgba(61,26,6,0.25));
  }

  /* ===== DARK FOOTER BAR ===== */
  .footer-contact-bar {
    background: #3d1a06;
    color: #ffffff;
    margin: 0 -36px -8px;
    padding: 6px 36px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 10px;
    font-weight: bold;
    font-family: Arial, sans-serif;
    border-top: 2px solid #c7ad8d;
  }

  .contact-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #ffffff;
  }

  .contact-item svg {
    fill: #c7ad8d;
  }

  /* Print Action Buttons Bar (Screen Only) */
  .print-actions-bar {
    width: 297mm;
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
        Print / Download Character Certificate
    </button>
    <a href="javascript:window.close()" class="btn-action btn-close-print">✕ Close Tab</a>
</div>

<div class="cert-page">
  <!-- Outer & Inner Decorative Frame -->
  <div class="border-outer"></div>
  <div class="border-inner"></div>

  <!-- Corner Filigree Ornaments -->
  <svg class="corner-svg corner-tl" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-tr" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-bl" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-br" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>

  <!-- Top Left Ribbon Seal Badge with Spacing from Top Border -->
  <div class="top-left-ribbon-badge">
    <svg viewBox="0 0 140 180" xmlns="http://www.w3.org/2000/svg">
      <path d="M45 100 L25 170 L50 155 L75 170 L60 100 Z" fill="#3d1a06" stroke="#c7ad8d" stroke-width="1.5"/>
      <path d="M80 100 L65 170 L90 155 L115 170 L95 100 Z" fill="#291104" stroke="#c7ad8d" stroke-width="1.5"/>
      <circle cx="70" cy="65" r="58" fill="#c7ad8d" stroke="#ffffff" stroke-width="2"/>
      <circle cx="70" cy="65" r="54" fill="#3d1a06" stroke="#c7ad8d" stroke-width="2"/>
      <circle cx="70" cy="65" r="46" fill="#291104" stroke="#c7ad8d" stroke-width="1"/>
      <image href="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" x="25" y="20" width="90" height="90" />
    </svg>
  </div>

  <!-- Top Right Mosque Silhouette -->
  <div class="top-right-mosque">
    <svg viewBox="0 0 200 150" xmlns="http://www.w3.org/2000/svg">
      <line x1="140" y1="0" x2="140" y2="35" stroke="#c7ad8d" stroke-width="1.5"/>
      <polygon points="140,35 148,45 144,60 136,60 132,45" fill="#fdfaf3" stroke="#c7ad8d" stroke-width="1.5"/>
      <line x1="175" y1="0" x2="175" y2="20" stroke="#c7ad8d" stroke-width="1.5"/>
      <polygon points="175,20 183,30 179,45 171,45 167,30" fill="#fdfaf3" stroke="#c7ad8d" stroke-width="1.5"/>
      <g fill="#c7ad8d" opacity="0.35">
        <path d="M40 150 V90 Q60 65 80 90 V150 Z" />
        <path d="M70 150 V80 Q100 50 130 80 V150 Z" />
        <path d="M120 150 V95 Q140 75 160 95 V150 Z" />
        <rect x="25" y="60" width="8" height="90" />
        <path d="M25 60 L29 45 L33 60 Z" />
        <rect x="170" y="70" width="8" height="80" />
        <path d="M170 70 L174 55 L178 70 Z" />
      </g>
    </svg>
  </div>

  <div class="cert-inner">

    <!-- Top Header -->
    <div class="cert-header">
      <div class="arabic-verse">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>
      <div class="verse-translation">"My Lord! Increase me in knowledge." (Quran 20:114)</div>

      <div class="school-name">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA' }}</div>
      <div class="school-subtitle-wrap">
        <div class="sub-line"></div>
        <div class="school-subtitle">{{ $globalSchoolInfo->school_code ?? 'SUPERIOR SCHOOL' }}</div>
        <div class="sub-line"></div>
      </div>
      <div class="tagline-badge">{{ $globalSchoolInfo->tagline ?? 'LEARN SUPERIOR • BE SUPERIOR' }}</div>
    </div>

    <!-- Title Banner & Serial Bar -->
    <div>
      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="40" height="12" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>

      <div class="cert-title-wrap">
        <div class="cert-meta-item">Serial: <strong>CC-{{ date('Y') }}/{{ $data['admission_no'] ? str_replace('ADM-', '', $data['admission_no']) : rand(100,999) }}</strong></div>
        <div class="cert-title">CHARACTER CERTIFICATE</div>
        <div class="cert-meta-item">Date: <strong>{{ $data['issue_date'] }}</strong></div>
      </div>

      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="30" height="8" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>
    </div>

    <!-- Main Grid Section (Left Badges | 3D Boy | Center Form | 3D Girl | Right Badges) -->
    <div class="landscape-main-grid">

      <!-- Left Column Badges -->
      <div class="badges-col">
        <div class="badge-item-card">
          <svg class="badge-icon-svg" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
          HONESTY
        </div>
        <div class="badge-item-card">
          <svg class="badge-icon-svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          RESPECT
        </div>
        <div class="badge-item-card">
          <svg class="badge-icon-svg" viewBox="0 0 24 24"><polygon points="12,2 15,9 22,9 17,14 19,21 12,17 5,21 7,14 2,9 9,9"/></svg>
          DISCIPLINE
        </div>
        <div class="badge-item-card">
          <svg class="badge-icon-svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          FAITH
        </div>
      </div>

      <!-- 3D Boy Student Character -->
      <img src="{{ asset('assets/images/student_boy.png') }}" alt="Boy Student" class="student-char-boy">

      <!-- Center Certificate Form Content -->
      <div class="center-form-body">
        
        <!-- Structured Student Data Card Table -->
        <div class="student-data-table-wrap">
          <table class="data-grid-table">
            <tbody>
              <tr>
                <td class="field-label-cell">
                  <div class="field-label-inner">
                    <div class="label-icon-box">
                      <svg class="label-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                    </div>
                    <span>Student Name</span>
                  </div>
                </td>
                <td class="field-value-cell">
                  <span class="value-highlight-pill">{{ $data['student_name'] }}</span>
                </td>
                <td class="field-label-cell">
                  <div class="field-label-inner">
                    <div class="label-icon-box">
                      <svg class="label-icon" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    </div>
                    <span>Father's Name</span>
                  </div>
                </td>
                <td class="field-value-cell">{{ $data['father_name'] }}</td>
              </tr>
              <tr>
                <td class="field-label-cell">
                  <div class="field-label-inner">
                    <div class="label-icon-box">
                      <svg class="label-icon" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    </div>
                    <span>Admission No.</span>
                  </div>
                </td>
                <td class="field-value-cell">{{ $data['admission_no'] }}</td>
                <td class="field-label-cell">
                  <div class="field-label-inner">
                    <div class="label-icon-box">
                      <svg class="label-icon" viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                    </div>
                    <span>Class &amp; Section</span>
                  </div>
                </td>
                <td class="field-value-cell">{{ $data['class_name'] }} {{ $data['section'] ? "({$data['section']})" : '' }}</td>
              </tr>
              <tr>
                <td class="field-label-cell">
                  <div class="field-label-inner">
                    <div class="label-icon-box">
                      <svg class="label-icon" viewBox="0 0 24 24"><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/></svg>
                    </div>
                    <span>Roll Number</span>
                  </div>
                </td>
                <td class="field-value-cell">{{ $data['roll_no'] ?: 'N/A' }}</td>
                <td class="field-label-cell">
                  <div class="field-label-inner">
                    <div class="label-icon-box">
                      <svg class="label-icon" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                    </div>
                    <span>Academic Session</span>
                  </div>
                </td>
                <td class="field-value-cell">{{ $data['session'] }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paragraph text -->
        <div class="paragraph-eval-text">
          This is to certify that the student above has been a bonafide student of <strong>{{ $globalSchoolInfo->school_name ?? 'Noor Ul Huda Superior School' }}</strong> and has consistently displayed<br>
          exemplary moral values, respect towards teachers and peers, high discipline, honesty,<br>
          and commendable adherence to Islamic character and school rules.
        </div>

        <!-- Character Rating Badges Row -->
        <div class="rating-boxes-row">
          <div class="rating-pill selected">★ Exemplary / Excellent Conduct</div>
          <div class="rating-pill">✓ Outstanding Moral Character</div>
          <div class="rating-pill">✓ All Dues &amp; Accounts Cleared</div>
        </div>

        <!-- Remarks Section -->
        <div class="remarks-grid">
          <div class="remarks-card">
            <div class="remarks-card-title">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="#3d1a06"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
              TEACHER CONDUCT REMARKS
            </div>
            <div class="remarks-card-body">
              {{ $data['remarks'] ?: 'Polite, respectful, punctual, and highly disciplined student with excellent academic attitude.' }}
            </div>
          </div>

          <div class="remarks-card">
            <div class="remarks-card-title">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="#3d1a06"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
              PRINCIPAL RECOMMENDATION
            </div>
            <div class="remarks-card-body">
              Highly recommended for admission in higher institutions and competitive academic programs.
            </div>
          </div>
        </div>

      </div><!-- end center-form-body -->

      <!-- 3D Girl Student Character -->
      <img src="{{ asset('assets/images/student_girl.png') }}" alt="Girl Student" class="student-char-girl">

      <!-- Right Column Badges -->
      <div class="badges-col">
        <div class="badge-item-card">
          <svg class="badge-icon-svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          KINDNESS
        </div>
        <div class="badge-item-card">
          <svg class="badge-icon-svg" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
          MANNERS
        </div>
        <div class="badge-item-card">
          <svg class="badge-icon-svg" viewBox="0 0 24 24"><path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3z"/></svg>
          INTEGRITY
        </div>
        <div class="badge-item-card">
          <svg class="badge-icon-svg" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          LEADERSHIP
        </div>
      </div>

    </div><!-- end landscape-main-grid -->

    <!-- Signatures Section -->
    <div class="signatures-wrap">
      <div class="signatures-grid">
        <!-- Class Teacher -->
        <div class="sig-box">
          <div class="sig-line"></div>
          <div class="sig-title">Class Teacher</div>
          @if(!empty($data['class_teacher']))
            <div class="sig-val">{{ $data['class_teacher'] }}</div>
          @endif
        </div>

        <!-- Center Stamp Seal with Ribbon Tails -->
        <div class="center-stamp-wrap">
          <svg class="center-medal-seal" viewBox="0 0 140 160" xmlns="http://www.w3.org/2000/svg">
            <path d="M45 90 L30 150 L50 138 L70 150 L60 90 Z" fill="#3d1a06" stroke="#c7ad8d" stroke-width="1.5"/>
            <path d="M80 90 L70 150 L90 138 L110 150 L95 90 Z" fill="#2d1304" stroke="#c7ad8d" stroke-width="1.5"/>
            <circle cx="70" cy="55" r="50" fill="#c7ad8d" stroke="#ffffff" stroke-width="2"/>
            <circle cx="70" cy="55" r="46" fill="#3d1a06" stroke="#c7ad8d" stroke-width="2"/>
            <image href="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" x="30" y="15" width="80" height="80" />
          </svg>
        </div>

        <!-- Principal -->
        <div class="sig-box">
          <div class="sig-line"></div>
          <div class="sig-title">Principal</div>
          @if(!empty($data['principal']))
            <div class="sig-val">{{ $data['principal'] }}</div>
          @endif
        </div>
      </div>
    </div>

    <!-- Bottom Dark Contact Bar -->
    <div class="footer-contact-bar">
      <div class="contact-item">
        <svg width="13" height="13" viewBox="0 0 24 24"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.27c1.21.49 2.53.76 3.88.76a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.35.27 2.67.76 3.88a1 1 0 01-.27 1.11l-2.37 2.4z"/></svg>
        {{ $globalSchoolInfo->phone ?? '03266850002' }}
      </div>
      <div class="contact-item">
        <svg width="13" height="13" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
        {{ $globalSchoolInfo->email ?? 'Superiorschoolnps@gmail.com' }}
      </div>
      <div class="contact-item">
        <svg width="13" height="13" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
        {{ $globalSchoolInfo->full_address ?? 'Dhanote, District Lodhran' }}
      </div>
    </div>

  </div><!-- end cert-inner -->
</div><!-- end cert-page -->

</body>
</html>
