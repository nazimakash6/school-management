<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Merit Certificate — {{ $data['student_name'] ?: 'Noor Ul Huda Superior School' }}</title>
<style>
  /* ===== RESET & BASE ===== */
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { width: 100%; height: 100%; }

  body {
    font-family: 'Times New Roman', 'Georgia', serif;
    background: #e2e8f0;
    color: #3d1a06;
  }

  /* ===== PRINT PAGE SETUP ===== */
  @page {
    size: A4 portrait;
    margin: 0;
  }

  @media print {
    body { background: #fff; }
    .no-print { display: none !important; }
    .cert-page { box-shadow: none !important; margin: 0 !important; width: 100% !important; height: 100% !important; page-break-after: avoid; }
  }

  /* ===== CERTIFICATE CONTAINER ===== */
  .cert-page {
    width: 210mm;
    height: 297mm;
    margin: 15px auto;
    background: #fdfaf3;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* ===== OUTER BORDER WITH MAHOGANY & GOLD CORNERS ===== */
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
    width: 65px;
    height: 65px;
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
    padding: 34px 40px 10px;
    z-index: 2;
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
  }

  /* Top Left Ribbon Seal Badge with Margins */
  .top-left-ribbon-badge {
    position: absolute;
    top: 38px;
    left: 44px;
    z-index: 15;
    width: 115px;
    filter: drop-shadow(0 4px 10px rgba(61,26,6,0.3));
  }

  /* Top Right Mosque Silhouette with Lanterns */
  .top-right-mosque {
    position: absolute;
    top: 38px;
    right: 44px;
    z-index: 5;
    width: 145px;
    opacity: 0.85;
    pointer-events: none;
  }

  /* ===== PERFECTLY CENTERED HEADER TEXT SECTION ===== */
  .cert-header {
    text-align: center;
    position: relative;
    z-index: 3;
    margin: 2px auto 0;
    width: 100%;
    max-width: 480px;
  }

  .arabic-verse {
    font-size: 25px;
    font-weight: bold;
    color: #3d1a06;
    font-family: 'Amiri', 'Traditional Arabic', 'Georgia', serif;
    line-height: 1.1;
    text-align: center;
  }

  .verse-translation {
    font-size: 10px;
    color: #3d1a06;
    font-style: italic;
    margin-top: 1px;
    margin-bottom: 4px;
    opacity: 0.9;
    text-align: center;
  }

  .school-name {
    font-size: 32px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
    line-height: 1;
    text-align: center;
  }

  .school-subtitle-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin: 3px 0 5px;
  }

  .sub-line {
    height: 1.5px;
    width: 50px;
    background: #c7ad8d;
  }

  .school-subtitle {
    font-size: 13px;
    color: #c7ad8d;
    letter-spacing: 4px;
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
    padding: 3px 20px;
    font-size: 9.5px;
    font-weight: bold;
    letter-spacing: 2px;
    font-family: Arial, sans-serif;
  }

  /* Filigree Ornament Divider */
  .filigree-divider {
    text-align: center;
    margin: 4px 0;
    color: #c7ad8d;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  .filigree-line {
    flex: 1;
    max-width: 150px;
    height: 1px;
    background: linear-gradient(to right, transparent, #c7ad8d, transparent);
  }

  /* ===== CERTIFICATE TITLE BANNER ===== */
  .cert-title-wrap {
    background: linear-gradient(135deg, #3d1a06 0%, #251004 100%);
    color: #ffffff;
    text-align: center;
    padding: 8px 24px;
    border-radius: 6px;
    border: 1.5px solid #c7ad8d;
    margin: 2px 30px;
    box-shadow: 0 4px 12px rgba(61,26,6,0.25);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .cert-title {
    font-size: 24px;
    font-weight: 900;
    color: #ffffff;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
    flex: 1;
    text-align: center;
  }

  .cert-meta-badge {
    font-size: 10px;
    font-weight: 700;
    color: #c7ad8d;
    background: rgba(255,255,255,0.08);
    padding: 2px 10px;
    border-radius: 4px;
    border: 1px solid rgba(199,173,141,0.3);
    font-family: Arial, sans-serif;
  }

  /* ===== FORM BODY & DATA TABLE ===== */
  .cert-body-content {
    margin: 4px 10px;
    position: relative;
    z-index: 4;
  }

  .cert-intro-line {
    text-align: center;
    font-size: 14.5px;
    font-weight: 700;
    color: #3d1a06;
    margin-bottom: 10px;
    font-family: 'Georgia', serif;
    font-style: italic;
  }

  /* Executive Center Data Table */
  .student-data-table-wrap {
    background: #ffffff;
    border: 2.5px solid #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -5px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(61,26,6,0.12), 0 2px 6px rgba(199,173,141,0.25);
    margin-bottom: 10px;
  }

  .data-grid-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
  }

  .data-grid-table th {
    background: linear-gradient(135deg, #3d1a06 0%, #251004 100%);
    color: #ffffff;
    padding: 8px 16px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-align: left;
    border-bottom: 2.5px solid #c7ad8d;
  }

  .th-content {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .data-grid-table tr {
    border-bottom: 1px solid rgba(199,173,141,0.3);
  }

  .data-grid-table tr:nth-child(even) {
    background-color: #fdfaf3;
  }

  .data-grid-table tr:last-child {
    border-bottom: none;
  }

  .data-grid-table td {
    padding: 8.5px 16px;
    vertical-align: middle;
    color: #3d1a06;
  }

  .label-cell {
    width: 32%;
    white-space: nowrap;
    font-weight: 700;
    color: #3d1a06;
    font-family: Arial, sans-serif;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .label-icon-box {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    background: #3d1a06;
    color: #c7ad8d;
    border-radius: 4px;
    margin-right: 8px;
    flex-shrink: 0;
  }

  .label-icon-box svg {
    width: 12px;
    height: 12px;
    fill: #c7ad8d;
  }

  .colon-cell {
    width: 2%;
    font-weight: 900;
    color: #c7ad8d;
  }

  .value-cell {
    width: 66%;
    font-weight: 900;
    color: #111827;
    font-size: 13.5px;
    font-family: 'Times New Roman', serif;
  }

  .split-value-grid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
  }

  .split-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .split-label {
    font-size: 11px;
    font-weight: 700;
    color: #3d1a06;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
  }

  .split-val {
    font-weight: 900;
    color: #111827;
    font-size: 13.5px;
    font-family: 'Times New Roman', serif;
  }

  /* Gold Star Citation Box */
  .merit-citation-box {
    background: #ffffff;
    border: 1.5px solid #c7ad8d;
    border-radius: 10px;
    padding: 10px 18px;
    text-align: center;
    margin-top: 8px;
    box-shadow: 0 4px 12px rgba(61,26,6,0.06);
    position: relative;
  }

  .citation-header-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #3d1a06;
    color: #c7ad8d;
    border: 1px solid #c7ad8d;
    padding: 2px 14px;
    border-radius: 15px;
    font-size: 9.5px;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
    margin-bottom: 6px;
  }

  .citation-text {
    font-size: 13.5px;
    line-height: 1.6;
    color: #3d1a06;
    font-family: 'Georgia', serif;
  }

  .citation-remarks {
    font-style: italic;
    font-weight: bold;
    color: #3d1a06;
    font-size: 14px;
    margin-top: 4px;
    background: rgba(199,173,141,0.12);
    padding: 4px 12px;
    border-radius: 6px;
    display: inline-block;
    border-left: 3px solid #c7ad8d;
  }

  /* ===== SIGNATURE SECTION ===== */
  .signatures-wrap {
    margin: 8px 10px 6px;
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
    margin-bottom: 6px;
  }

  .sig-title {
    font-size: 12px;
    font-weight: bold;
    color: #3d1a06;
    font-family: Arial, sans-serif;
  }

  .sig-val {
    font-size: 11.5px;
    color: #111827;
    margin-top: 2px;
    font-weight: 700;
  }

  /* Center Medal Seal in Signatures Row */
  .center-stamp-wrap {
    flex: 0 0 90px;
    position: relative;
    top: 6px;
    text-align: center;
  }

  .center-medal-seal {
    width: 85px;
    filter: drop-shadow(0 3px 6px rgba(61,26,6,0.25));
  }

  /* ===== CORE VALUES FOOTER STRIP ===== */
  .values-container {
    border-top: 1.5px solid #c7ad8d;
    padding-top: 8px;
    margin: 0 10px 6px;
    display: flex;
    justify-content: space-between;
  }

  .value-card {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 4px;
  }

  .value-icon-svg {
    width: 22px;
    height: 22px;
    fill: #3d1a06;
    flex-shrink: 0;
  }

  .value-text-group {
    display: flex;
    flex-direction: column;
  }

  .value-title {
    font-size: 9.5px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
  }

  .value-desc {
    font-size: 8px;
    color: #3d1a06;
    opacity: 0.85;
    line-height: 1.2;
    font-family: Arial, sans-serif;
  }

  /* ===== DARK FOOTER BAR ===== */
  .footer-contact-bar {
    background: #3d1a06;
    color: #ffffff;
    margin: 0 -40px -10px;
    padding: 9px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
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
  .btn-primary-print:hover { background: #542509; }
  .btn-close-print { background: #cbd5e1; color: #1e293b; }
  .btn-close-print:hover { background: #94a3b8; }
</style>
</head>
<body>

<!-- Screen-only Print Bar -->
<div class="print-actions-bar no-print">
    <button class="btn-action btn-primary-print" onclick="window.print()">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z"></path></svg>
        Print / Download Merit Certificate
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

  <!-- Top Left Ribbon Seal Badge with Margins -->
  <div class="top-left-ribbon-badge">
    <svg viewBox="0 0 140 180" xmlns="http://www.w3.org/2000/svg">
      <path d="M45 100 L25 170 L50 155 L75 170 L60 100 Z" fill="#3d1a06" stroke="#c7ad8d" stroke-width="1.5"/>
      <path d="M80 100 L65 170 L90 155 L115 170 L95 100 Z" fill="#251004" stroke="#c7ad8d" stroke-width="1.5"/>
      <circle cx="70" cy="65" r="58" fill="#c7ad8d" stroke="#ffffff" stroke-width="2"/>
      <circle cx="70" cy="65" r="54" fill="#3d1a06" stroke="#c7ad8d" stroke-width="2"/>
      <circle cx="70" cy="65" r="46" fill="#251004" stroke="#c7ad8d" stroke-width="1"/>
      <image href="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" x="25" y="20" width="90" height="90" />
    </svg>
  </div>

  <!-- Top Right Mosque Silhouette with Lanterns -->
  <div class="top-right-mosque">
    <svg viewBox="0 0 200 150" xmlns="http://www.w3.org/2000/svg">
      <line x1="140" y1="0" x2="140" y2="40" stroke="#c7ad8d" stroke-width="1.5"/>
      <polygon points="140,40 148,50 144,65 136,65 132,50" fill="#fdfaf3" stroke="#c7ad8d" stroke-width="1.5"/>
      <circle cx="140" cy="55" r="3" fill="#eab308"/>
      <line x1="175" y1="0" x2="175" y2="25" stroke="#c7ad8d" stroke-width="1.5"/>
      <polygon points="175,25 183,35 179,50 171,50 167,35" fill="#fdfaf3" stroke="#c7ad8d" stroke-width="1.5"/>
      <circle cx="175" cy="40" r="3" fill="#eab308"/>
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

    <!-- Perfectly Centered Header Section -->
    <div class="cert-header">
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

    <!-- Title Banner -->
    <div>
      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="50" height="15" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>

      <div class="cert-title-wrap">
        <div class="cert-meta-badge">No. MERT-{{ date('Y') }}-{{ str_pad($data['admission_no'] ?: '001', 4, '0', STR_PAD_LEFT) }}</div>
        <div class="cert-title">MERIT CERTIFICATE</div>
        <div class="cert-meta-badge">Date: {{ $data['issue_date'] }}</div>
      </div>

      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="35" height="10" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>
    </div>

    <!-- Form Body Content -->
    <div class="cert-body-content">
      <div class="cert-intro-line">This Certificate of Academic &amp; Extra-Curricular Excellence is proudly presented to</div>

      <!-- Executive Structured Data Table -->
      <div class="student-data-table-wrap">
        <table class="data-grid-table">
          <thead>
            <tr>
              <th colspan="3">
                <div class="th-content">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="#c7ad8d"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                  <span>OFFICIAL MERIT &amp; PERFORMANCE RECORD</span>
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- Student Name -->
            <tr>
              <td class="label-cell">
                <span class="label-icon-box">
                  <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </span>
                Name of Student
              </td>
              <td class="colon-cell">:</td>
              <td class="value-cell">{{ $data['student_name'] }}</td>
            </tr>

            <!-- Father Name -->
            <tr>
              <td class="label-cell">
                <span class="label-icon-box">
                  <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                </span>
                Father's Name
              </td>
              <td class="colon-cell">:</td>
              <td class="value-cell">{{ $data['father_name'] }}</td>
            </tr>

            <!-- Admission No & Roll No Split -->
            <tr>
              <td class="label-cell">
                <span class="label-icon-box">
                  <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                </span>
                Admission &amp; Roll #
              </td>
              <td class="colon-cell">:</td>
              <td class="value-cell">
                <div class="split-value-grid">
                  <div class="split-item">
                    <span class="split-label">Adm No:</span>
                    <span class="split-val">{{ $data['admission_no'] }}</span>
                  </div>
                  <div class="split-item">
                    <span class="split-label">Roll No:</span>
                    <span class="split-val">{{ $data['roll_no'] }}</span>
                  </div>
                  @if(!empty($data['date_of_birth']))
                  <div class="split-item">
                    <span class="split-label">D.O.B:</span>
                    <span class="split-val">{{ $data['date_of_birth'] }}</span>
                  </div>
                  @endif
                </div>
              </td>
            </tr>

            <!-- Class & Section -->
            <tr>
              <td class="label-cell">
                <span class="label-icon-box">
                  <svg viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                </span>
                Class &amp; Section
              </td>
              <td class="colon-cell">:</td>
              <td class="value-cell">{{ $data['class_name'] }} {{ $data['section'] }}</td>
            </tr>

            <!-- Academic Session -->
            <tr>
              <td class="label-cell">
                <span class="label-icon-box">
                  <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                </span>
                Academic Session
              </td>
              <td class="colon-cell">:</td>
              <td class="value-cell">{{ $data['session'] }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Gold Star Citation Box -->
      <div class="merit-citation-box">
        <div class="citation-header-badge">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="#c7ad8d"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          ACADEMIC &amp; EXTRA-CURRICULAR CITATION
        </div>
        <div class="citation-text">
          In recognition of outstanding academic performance, exemplary moral conduct, and active participation at <strong>{{ $globalSchoolInfo->school_name ?? 'Noor Ul Huda Superior School' }}</strong>.
        </div>
        @if(!empty($data['remarks']))
          <div class="citation-remarks">
            ⭐ Achievement / Award: "{{ $data['remarks'] }}"
          </div>
        @endif
      </div>

    </div>

    <!-- Signatures Section -->
    <div class="signatures-wrap">
      <div class="filigree-divider" style="margin-bottom: 12px;">
        <div class="filigree-line"></div>
        <svg width="40" height="12" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>

      <div class="signatures-grid">
        <!-- Class Teacher -->
        <div class="sig-box">
          <div class="sig-line"></div>
          <div class="sig-title">Class Teacher Signature</div>
          @if(!empty($data['class_teacher']))
            <div class="sig-val">{{ $data['class_teacher'] }}</div>
          @endif
        </div>

        <!-- Center Stamp Seal with Ribbon Tails -->
        <div class="center-stamp-wrap">
          <svg class="center-medal-seal" viewBox="0 0 140 160" xmlns="http://www.w3.org/2000/svg">
            <path d="M45 90 L30 150 L50 138 L70 150 L60 90 Z" fill="#3d1a06" stroke="#c7ad8d" stroke-width="1.5"/>
            <path d="M80 90 L70 150 L90 138 L110 150 L95 90 Z" fill="#251004" stroke="#c7ad8d" stroke-width="1.5"/>
            <circle cx="70" cy="55" r="50" fill="#c7ad8d" stroke="#ffffff" stroke-width="2"/>
            <circle cx="70" cy="55" r="46" fill="#3d1a06" stroke="#c7ad8d" stroke-width="2"/>
            <image href="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" x="30" y="15" width="80" height="80" />
          </svg>
        </div>

        <!-- Principal -->
        <div class="sig-box">
          <div class="sig-line"></div>
          <div class="sig-title">Principal Signature</div>
          @if(!empty($data['principal']))
            <div class="sig-val">{{ $data['principal'] }}</div>
          @endif
        </div>
      </div>
    </div>

    <!-- Core Values Section -->
    <div class="values-container">
      <div class="value-card">
        <svg class="value-icon-svg" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
        <div class="value-text-group">
          <span class="value-title">KNOWLEDGE</span>
          <span class="value-desc">Seek knowledge with sincerity.</span>
        </div>
      </div>

      <div class="value-card">
        <svg class="value-icon-svg" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 14a4 4 0 1 1 4-4 4 4 0 0 1-4 4z"/><polygon points="17,6 18.2,9.5 22,9.5 19,11.8 20.2,15.3 17,13 13.8,15.3 15,11.8 12,9.5 15.8,9.5"/></svg>
        <div class="value-text-group">
          <span class="value-title">FAITH</span>
          <span class="value-desc">Strengthen your faith in Allah.</span>
        </div>
      </div>

      <div class="value-card">
        <svg class="value-icon-svg" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
        <div class="value-text-group">
          <span class="value-title">CHARACTER</span>
          <span class="value-desc">Build good character for a better life.</span>
        </div>
      </div>

      <div class="value-card">
        <svg class="value-icon-svg" viewBox="0 0 24 24"><path d="M19 5h-3X3V3h12v2zm-7 15l-5.5 3 1.5-6.1L3 11.5l6.3-.5L12 5l2.7 6 6.3.5-4.8 5.4 1.5 6.1z"/></svg>
        <div class="value-text-group">
          <span class="value-title">EXCELLENCE</span>
          <span class="value-desc">Strive for excellence in everything.</span>
        </div>
      </div>
    </div>

    <!-- Bottom Dark Contact Bar -->
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
