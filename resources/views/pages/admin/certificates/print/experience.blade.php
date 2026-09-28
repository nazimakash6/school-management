<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Experience Letter — {{ $data['staff_name'] ?: 'Noor Ul Huda Superior School' }}</title>
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
    line-height: 1.4;
  }

  /* ===== PRINT A4 PORTRAIT PAGE SETUP ===== */
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
      padding: 5mm !important;
      box-sizing: border-box !important;
      overflow: hidden !important;
      page-break-before: avoid !important;
      page-break-after: avoid !important;
      page-break-inside: avoid !important;
    }
  }

  /* ===== PORTRAIT CONTAINER ===== */
  .cert-page {
    width: 210mm;
    max-width: 210mm;
    height: 297mm;
    max-height: 297mm;
    margin: 15px auto;
    background: #fdfaf3;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 45px rgba(61,26,6,0.35);
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
  }

  /* Watermark Mosque Background Illusion */
  .watermark-bg {
    position: absolute;
    top: 50%;
    left: 55%;
    transform: translate(-50%, -50%);
    width: 440px;
    height: 440px;
    opacity: 0.045;
    pointer-events: none;
    z-index: 1;
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
    width: 62px;
    height: 62px;
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
    padding: 36px 44px 12px;
    z-index: 2;
    display: flex;
    flex-direction: column;
    height: auto;
    justify-content: flex-start;
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

  .official-logo-wrap {
    position: relative;
    width: 105px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .official-logo-img {
    width: 98px;
    height: 98px;
    object-fit: contain;
    filter: drop-shadow(0 3px 6px rgba(61,26,6,0.2));
  }

  .header-center-text {
    flex: 1;
    text-align: center;
    padding: 0 10px;
  }

  .arabic-verse {
    font-size: 23px;
    font-weight: bold;
    color: #3d1a06;
    font-family: 'Amiri', 'Traditional Arabic', 'Georgia', serif;
    line-height: 1.1;
    text-shadow: 0 1px 1px rgba(199, 173, 141, 0.3);
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
    font-size: 29px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 2.5px;
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
    width: 45px;
    background: #c7ad8d;
  }

  .school-subtitle {
    font-size: 13px;
    color: #3d1a06;
    letter-spacing: 3.5px;
    font-weight: 800;
    font-family: 'Times New Roman', serif;
    text-transform: uppercase;
  }

  .tagline-badge {
    display: inline-block;
    background: #3d1a06;
    color: #ffffff;
    border: 1.5px solid #c7ad8d;
    border-radius: 20px;
    padding: 3px 20px;
    font-size: 9.5px;
    font-weight: 800;
    letter-spacing: 2px;
    font-family: Arial, sans-serif;
    box-shadow: 0 2px 5px rgba(61,26,6,0.15);
  }

  .staff-profile-photo-img {
    width: 85px;
    height: 102px;
    object-fit: cover;
    border: 2px solid #3d1a06;
    outline: 1.5px solid #c7ad8d;
    outline-offset: -2px;
    border-radius: 6px;
    box-shadow: 0 3px 6px rgba(61,26,6,0.15);
    margin-top: 10px;
    margin-right: 15px;
  }

  .header-right-emblem {
    width: 85px;
    height: 102px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 6px;
    margin-right: 12px;
    filter: drop-shadow(0 3px 6px rgba(61, 26, 6, 0.18));
  }

  /* Filigree Ornament Divider */
  .filigree-divider {
    text-align: center;
    margin: 4px 0;
    color: #c7ad8d;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .filigree-line {
    flex: 1;
    max-width: 150px;
    height: 1px;
    background: linear-gradient(to right, transparent, #c7ad8d, transparent);
  }

  /* ===== CERTIFICATE TITLE BANNER ===== */
  .cert-title-wrap {
    text-align: center;
    margin: 4px 0;
  }

  .cert-title {
    font-size: 27px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 4px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
    text-shadow: 0 1px 2px rgba(61,26,6,0.1);
  }

  /* ===== REF NO & DATE ROW ===== */
  .ref-date-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 10px 10px 16px;
    font-size: 14px;
    font-weight: bold;
    color: #3d1a06;
    font-family: 'Times New Roman', serif;
  }

  .ref-fill-line, .date-fill-line {
    display: inline-block;
    border-bottom: 1.5px solid #3d1a06;
    padding: 0 12px;
    min-width: 140px;
    text-align: center;
    color: #000000;
    font-weight: 800;
  }

  /* ===== MAIN CONTENT WRAPPER WITH AWESOME LEFT PILLAR ===== */
  .experience-main-layout {
    display: flex;
    gap: 22px;
    margin: 4px 5px 14px;
    align-items: stretch;
  }

  /* Premium Left Vertical Pillar Box (Knowledge, Faith, Character, Excellence) */
  .left-pillar-column {
    width: 120px;
    background: linear-gradient(180deg, #3d1a06 0%, #1f0c03 100%);
    border: 2px solid #c7ad8d;
    outline: 1px solid #3d1a06;
    outline-offset: -4px;
    border-radius: 10px;
    color: #ffffff;
    padding: 24px 8px;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: center;
    gap: 12px;
    box-shadow: inset 0 0 10px rgba(199, 173, 141, 0.2), 0 4px 15px rgba(61, 26, 6, 0.35);
    flex-shrink: 0;
  }

  .pillar-item {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    width: 100%;
  }

  .pillar-icon-wrap {
    width: 38px;
    height: 38px;
    background: radial-gradient(circle, #52240b 0%, #3d1a06 100%);
    border: 1.5px solid #c7ad8d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c7ad8d;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  }

  .pillar-label {
    font-size: 9.5px;
    font-weight: 900;
    letter-spacing: 1.8px;
    color: #ffffff;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
  }

  .pillar-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 82%;
    gap: 4px;
    margin: 2px 0;
  }

  .pillar-div-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, transparent, #c7ad8d, transparent);
  }

  .pillar-star {
    color: #c7ad8d;
    font-size: 7px;
  }

  /* Right Experience Letter Text Content */
  .experience-body-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding-top: 4px;
    padding-left: 5px;
  }

  .to-whom-wrap {
    margin-bottom: 22px;
  }

  .to-whom-heading {
    font-size: 18px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
    border-bottom: 2.5px solid #c7ad8d;
    padding-bottom: 3px;
    display: inline-block;
  }

  .exp-paragraph {
    font-size: 15.5px;
    color: #1a0a03;
    line-height: 2.15;
    text-align: justify;
    margin-bottom: 20px;
    font-family: 'Times New Roman', serif;
  }

  .bold-data-text {
    font-weight: 900;
    color: #000000;
    font-size: 16px;
    text-decoration: none;
    border-bottom: none;
  }

  /* ===== SIGNATURES & STAMP ROW ===== */
  .signatures-stamp-wrapper {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin: 25px 10px 18px;
    padding: 0 10px;
  }

  .sig-block {
    text-align: center;
    width: 200px;
  }

  .pencil-icon-wrap {
    color: #c7ad8d;
    margin-bottom: 6px;
  }

  .sig-line {
    width: 100%;
    border-top: 1.5px solid #3d1a06;
    margin-bottom: 6px;
  }

  .sig-title-bold {
    font-size: 12px;
    font-weight: 900;
    color: #3d1a06;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-family: Arial, sans-serif;
  }

  .sig-sub-school {
    font-size: 10.5px;
    color: #3d1a06;
    font-weight: 600;
  }

  /* Center Circular Official Seal Stamp */
  .center-official-stamp {
    width: 95px;
    height: 95px;
    border: 2px solid #3d1a06;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    background: #ffffff;
    box-shadow: 0 3px 10px rgba(61,26,6,0.15);
    outline: 1.5px solid #c7ad8d;
    outline-offset: -4px;
  }

  .center-stamp-logo-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 1px 2px rgba(61,26,6,0.15));
  }

  .stamp-inner-circle {
    width: 100%;
    height: 100%;
    border: 1px dashed #c7ad8d;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #3d1a06;
    padding: 2px;
  }

  .stamp-text-small {
    font-size: 7px;
    font-weight: 900;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
  }

  /* ===== DARK CHOCOLATE FOOTER BAR ===== */
  .footer-contact-bar {
    background: #3d1a06;
    color: #ffffff;
    margin: 0 -44px -12px;
    padding: 9px 44px;
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
        Print / Download Experience Letter
    </button>
    <a href="javascript:window.close()" class="btn-action btn-close-print">✕ Close Tab</a>
</div>

<div class="cert-page">
  <!-- Watermark Mosque Background -->
  <svg class="watermark-bg" viewBox="0 0 200 200" fill="none" stroke="#3d1a06">
    <circle cx="100" cy="100" r="80" stroke-width="1.5" stroke-dasharray="4 4"/>
    <path d="M100 30 L100 170 M30 100 L170 100 M50 50 L150 150 M50 150 L150 50" stroke-width="0.8"/>
  </svg>

  <!-- Outer & Inner Gold/Chocolate Decorative Frame -->
  <div class="border-outer"></div>
  <div class="border-inner"></div>

  <!-- Corner Filigree Ornaments -->
  <svg class="corner-svg corner-tl" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-tr" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-bl" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-br" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>

  <div class="cert-inner">

    <!-- Header Section with Official School Logo & Verse -->
    <div>
      <div class="header-top-row">
        <!-- Left Official Logo Wrap -->
        <div class="official-logo-wrap">
          <img src="{{ asset('assets/images/logo.png') }}" alt="Noor Ul Huda School Logo" class="official-logo-img">
        </div>

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

        <!-- Right Staff Profile Photo / Decorative Emblem -->
        @if(!empty($data['student_photo_url']))
          <img src="{{ $data['student_photo_url'] }}" alt="Staff Photo" class="staff-profile-photo-img">
        @else
          <div class="header-right-emblem">
            <svg width="80" height="95" viewBox="0 0 100 115" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="goldGrad" x1="0" y1="0" x2="100" y2="115" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#f5e6d3"/>
                  <stop offset="40%" stop-color="#c7ad8d"/>
                  <stop offset="100%" stop-color="#8c6c45"/>
                </linearGradient>
                <linearGradient id="innerBgGrad" x1="0" y1="0" x2="0" y2="100" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#ffffff"/>
                  <stop offset="100%" stop-color="#fdfaf3"/>
                </linearGradient>
              </defs>
              <!-- Outer Shield Outer Frame -->
              <path d="M50 4 L92 20 V60 C92 86 50 111 50 111 C50 111 8 86 8 60 V20 L50 4 Z" fill="url(#goldGrad)" stroke="#3d1a06" stroke-width="2.5"/>
              <!-- Inner Shield Area -->
              <path d="M50 11 L84 24 V58 C84 79 50 101 50 101 C50 101 16 79 16 58 V24 L50 11 Z" fill="url(#innerBgGrad)" stroke="#c7ad8d" stroke-width="1.5"/>
              <path d="M50 14 L81 26 V56 C81 76 50 97 50 97 C50 97 19 76 19 56 V26 L50 14 Z" fill="none" stroke="#c7ad8d" stroke-width="1" stroke-dasharray="3 2"/>

              <!-- Central Ornamental Circle -->
              <circle cx="50" cy="48" r="23" fill="#fdfaf3" stroke="#c7ad8d" stroke-width="1.5"/>
              <circle cx="50" cy="48" r="20" fill="none" stroke="#3d1a06" stroke-width="1"/>

              <!-- Book & Quill Emblem in Center -->
              <!-- Open Book -->
              <path d="M37 52 C41 49 46 50 50 52 C54 50 59 49 63 52 V40 C59 38 54 39 50 41 C46 39 41 38 37 40 Z" fill="#3d1a06"/>
              <path d="M50 41 V52" stroke="#fdfaf3" stroke-width="1.2"/>
              <!-- Feather Quill -->
              <path d="M55 31 Q62 26 66 34 Q59 38 53 43 L51 41 Z" fill="#c7ad8d" stroke="#3d1a06" stroke-width="0.8"/>
              <!-- Crown Accent Above Circle -->
              <path d="M42 21 L45 25 L50 20 L55 25 L58 21 L56 27 H44 Z" fill="#c7ad8d" stroke="#3d1a06" stroke-width="0.8"/>

              <!-- Lower Ribbon Banner -->
              <path d="M22 78 L34 73 L50 76 L66 73 L78 78 L74 88 L50 83 L26 88 Z" fill="#3d1a06" stroke="#c7ad8d" stroke-width="1.2"/>
              <path d="M34 73 L50 76 L66 73" stroke="#c7ad8d" stroke-width="1" fill="none"/>
              <!-- Decorative Dots on Ribbon -->
              <circle cx="34" cy="81" r="1.5" fill="#c7ad8d"/>
              <circle cx="50" cy="79.5" r="1.8" fill="#c7ad8d"/>
              <circle cx="66" cy="81" r="1.5" fill="#c7ad8d"/>
            </svg>
          </div>
        @endif
      </div>

      <!-- Filigree Ornament Divider -->
      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="48" height="14" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>

      <!-- Title Banner -->
      <div class="cert-title-wrap">
        <div class="cert-title">EXPERIENCE LETTER</div>
      </div>

      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="34" height="10" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>

      <!-- Ref No & Date Row -->
      <div class="ref-date-row">
        <div>Ref No.: <span class="ref-fill-line">{{ $data['ref_no'] }}</span></div>
        <div>Date: <span class="date-fill-line">{{ $data['issue_date'] }}</span></div>
      </div>
    </div>

    <!-- MAIN EXPERIENCE CONTENT LAYOUT WITH AWESOME LEFT PILLAR -->
    <div class="experience-main-layout">

      <!-- Premium Left Vertical Pillar Box with 4 Core Values -->
      <div class="left-pillar-column">
        
        <!-- 1. Knowledge -->
        <div class="pillar-item">
          <div class="pillar-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
              <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
          </div>
          <div class="pillar-label">Knowledge</div>
        </div>

        <div class="pillar-divider">
          <div class="pillar-div-line"></div>
          <div class="pillar-star">✦</div>
          <div class="pillar-div-line"></div>
        </div>

        <!-- 2. Faith -->
        <div class="pillar-item">
          <div class="pillar-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
          </div>
          <div class="pillar-label">Faith</div>
        </div>

        <div class="pillar-divider">
          <div class="pillar-div-line"></div>
          <div class="pillar-star">✦</div>
          <div class="pillar-div-line"></div>
        </div>

        <!-- 3. Character -->
        <div class="pillar-item">
          <div class="pillar-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
          </div>
          <div class="pillar-label">Character</div>
        </div>

        <div class="pillar-divider">
          <div class="pillar-div-line"></div>
          <div class="pillar-star">✦</div>
          <div class="pillar-div-line"></div>
        </div>

        <!-- 4. Excellence -->
        <div class="pillar-item">
          <div class="pillar-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
              <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
              <path d="M4 22h16"></path>
              <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
              <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
              <path d="M18 2H6v7a6 6 0 0 0 12 0V2z"></path>
            </svg>
          </div>
          <div class="pillar-label">Excellence</div>
        </div>

      </div>

      <!-- Right Experience Letter Body Text -->
      <div class="experience-body-content">

        <div class="to-whom-wrap">
          <div class="to-whom-heading">TO WHOM IT MAY CONCERN</div>
        </div>

        <div class="exp-paragraph">
          This is to certify that Mr./Ms. <strong class="bold-data-text">{{ $data['staff_name'] }}</strong>
          @if(!empty($data['father_name']))
            S/o, D/o <strong class="bold-data-text">{{ $data['father_name'] }}</strong>
          @endif
          @if(!empty($data['cnic_no']))
            bearing CNIC No. <strong class="bold-data-text">{{ $data['cnic_no'] }}</strong>,
          @endif
          was employed as <strong class="bold-data-text">{{ $data['designation'] }}</strong>
          in <strong style="color: #3d1a06;">Noor Ul Huda Superior School</strong>
          @if(!empty($data['from_date']) || !empty($data['to_date']))
            from <strong class="bold-data-text">{{ $data['from_date'] ?: '___________' }}</strong>
            to <strong class="bold-data-text">{{ $data['to_date'] ?: 'Present' }}</strong>.
          @else
            during his/her tenure of service.
          @endif
          During his/her tenure, we found him/her to be a sincere, hardworking, dedicated and responsible individual. He/She always exhibited a professional attitude and shown great commitment towards his/her duties.
        </div>

        <div class="exp-paragraph">
          We wish him/her all the best for his/her future endeavors.
        </div>

        <div class="exp-paragraph">
          This experience letter is issued upon the request of the employee for whatever legal purpose it may serve.
        </div>

      </div>

    </div>

    <!-- SIGNATURES & OFFICIAL STAMP FOOTER ROW -->
    <div>
      <div class="signatures-stamp-wrapper">
        
        <!-- Left: PRINCIPAL Signature -->
        <div class="sig-block">
          <div class="sig-line"></div>
          <div class="sig-title-bold">PRINCIPAL</div>
          <div class="sig-sub-school">{{ $globalSchoolInfo->school_name ?? 'Noor Ul Huda Superior School' }}</div>
        </div>

        <!-- Center: Official School Logo -->
        <div class="center-official-stamp">
          <img src="{{ asset('assets/images/logo.png') }}" alt="School Logo" class="center-stamp-logo-img">
        </div>

        <!-- Right: HEAD OF INSTITUTION Signature -->
        <div class="sig-block">
          <div class="sig-line"></div>
          <div class="sig-title-bold">HEAD OF INSTITUTION</div>
          <div class="sig-sub-school">{{ $globalSchoolInfo->school_name ?? 'Noor Ul Huda Superior School' }}</div>
        </div>

      </div>

      <!-- Bottom Dark Chocolate Contact Bar -->
      <div class="footer-contact-bar">
        <div class="contact-item">
          <svg width="13" height="13" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.27c1.21.49 2.53.76 3.88.76a1 1 0 011 1V20a1 1 0 01-1 1C10.07 21 3 13.93 3 5a1 1 0 011-1h3.5a1 1 0 011 1c0 1.35.27 2.67.76 3.88a1 1 0 01-.27 1.11l-2.2 2.2z"/></svg>
          <span>{{ $globalSchoolInfo->phone ?? '03266850002' }}</span>
        </div>
        <div class="contact-item">
          <svg width="13" height="13" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
          <span>{{ $globalSchoolInfo->email ?? 'Superiorschoolnps@gmail.com' }}</span>
        </div>
        <div class="contact-item">
          <svg width="13" height="13" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <span>{{ $globalSchoolInfo->address ?? 'Dhanote, District Lodhran' }}</span>
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>
