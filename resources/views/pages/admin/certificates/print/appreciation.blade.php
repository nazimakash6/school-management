<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appreciation Certificate — {{ $data['recipient_name'] ?: ($data['student_name'] ?: 'Noor Ul Huda Superior School') }}</title>

<!-- Google Fonts for Calligraphy & Certificate Typography -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cinzel:wght@600;700;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,400&display=swap" rel="stylesheet">

<style>
  /* ===== RESET & BASE ===== */
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { width: 100%; height: 100%; }

  body {
    font-family: 'Times New Roman', 'Georgia', serif;
    background: #e2e8f0;
    color: #3d1a06;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  /* ===== PRINT PAGE SETUP (LANDSCAPE A4) ===== */
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
      width: 297mm !important;
      height: 210mm !important;
      page-break-after: avoid;
    }
  }

  /* ===== CERTIFICATE CONTAINER (A4 LANDSCAPE) ===== */
  .cert-page {
    width: 297mm;
    height: 210mm;
    margin: 15px auto;
    background: #fbf6ee;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 45px rgba(0,0,0,0.35);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* Geometric Tile Pattern Header Background Overlay */
  .tile-pattern-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 65px;
    background-color: #2b1204;
    background-image: radial-gradient(#c7ad8d 0.75px, transparent 0.75px), radial-gradient(#c7ad8d 0.75px, #2b1204 0.75px);
    background-size: 15px 15px;
    background-position: 0 0, 7.5px 7.5px;
    opacity: 0.25;
    mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 100%);
    -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 100%);
    pointer-events: none;
    z-index: 1;
  }

  /* ===== OUTER BORDER WITH CURVED MAHOGANY & GOLD ===== */
  .border-outer {
    position: absolute;
    inset: 10px;
    border: 6px solid #3d1a06;
    outline: 2.5px solid #c7ad8d;
    outline-offset: -4px;
    border-radius: 16px;
    pointer-events: none;
    z-index: 10;
  }

  .border-inner {
    position: absolute;
    inset: 22px;
    border: 1.2px solid #c7ad8d;
    border-radius: 10px;
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
  .corner-tl { top: 22px; left: 22px; }
  .corner-tr { top: 22px; right: 22px; transform: scaleX(-1); }
  .corner-bl { bottom: 22px; left: 22px; transform: scaleY(-1); }
  .corner-br { bottom: 22px; right: 22px; transform: scale(-1); }

  /* ===== CERTIFICATE CONTENT INNER ===== */
  .cert-inner {
    position: relative;
    padding: 24px 32px 0px;
    z-index: 2;
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
  }

  /* ===== TOP HEADER SECTION ===== */
  .header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    margin-top: 10px;
  }

  /* Top Left School Seal Logo */
  .header-left-logo-wrap {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: #fbf6ee;
    border: 3px solid #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -2px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(61,26,6,0.2);
    margin-left: 10px;
    margin-top: 8px;
  }

  .official-logo-img {
    width: 78px;
    height: 78px;
    object-fit: contain;
  }

  /* Top Center School Text */
  .header-center-text {
    flex: 1;
    text-align: center;
    padding: 0 15px;
    margin-top: 6px;
  }

  .arabic-verse {
    font-family: 'Traditional Arabic', 'Scheherazade', 'Amiri', serif;
    font-size: 18px;
    color: #3d1a06;
    font-weight: bold;
    line-height: 1.1;
  }

  .verse-translation {
    font-size: 8.5px;
    color: #4a240d;
    font-style: italic;
    margin-bottom: 2px;
    letter-spacing: 0.5px;
  }

  .school-name {
    font-family: 'Cinzel', 'Times New Roman', serif;
    font-size: 25px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 3px;
    text-transform: uppercase;
    line-height: 1.1;
  }

  .school-subtitle-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin: 2px 0;
  }

  .sub-line {
    width: 50px;
    height: 1.5px;
    background: #c7ad8d;
  }

  .school-subtitle {
    font-family: 'Cinzel', serif;
    font-size: 12px;
    font-weight: 700;
    color: #3d1a06;
    letter-spacing: 4px;
    text-transform: uppercase;
  }

  .tagline-badge {
    display: inline-block;
    background: #3d1a06;
    color: #c7ad8d;
    font-size: 8.5px;
    font-weight: bold;
    padding: 2px 16px;
    border-radius: 12px;
    letter-spacing: 2.5px;
    font-family: Arial, sans-serif;
    box-shadow: 0 2px 5px rgba(61,26,6,0.15);
    border: 1px solid #c7ad8d;
  }

  /* Top Right Mosque Silhouette & Hanging Lanterns */
  .header-right-lanterns {
    width: 110px;
    text-align: right;
    margin-right: 10px;
    position: relative;
  }

  .mosque-lantern-svg {
    width: 100px;
    height: 80px;
    filter: drop-shadow(0 2px 4px rgba(61,26,6,0.15));
  }

  /* ===== MAIN TITLE BANNER SECTION ===== */
  .main-title-section {
    text-align: center;
    margin: -2px 0 4px;
  }

  .script-title-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
  }

  .leaf-flourish-svg {
    width: 38px;
    height: 24px;
    fill: #c7ad8d;
  }

  .script-award-category {
    font-family: 'Great Vibes', 'Alex Brush', cursive;
    font-size: 46px;
    color: #3d1a06;
    line-height: 1;
    font-weight: 400;
  }

  .bold-appreciation-title {
    font-family: 'Cinzel', 'Georgia', serif;
    font-size: 28px;
    font-weight: 900;
    color: #a67c52;
    letter-spacing: 7px;
    text-transform: uppercase;
    margin-top: -6px;
  }

  .presented-to-line {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 3.5px;
    color: #3d1a06;
    margin-top: 5px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  .diamond-bullet {
    color: #c7ad8d;
    font-size: 12px;
  }

  /* ===== MIDDLE GRID (LEFT QUOTE/BOOKS + CENTER CITATION + RIGHT QUOTE/FLOWERS) ===== */
  .middle-content-grid {
    display: grid;
    grid-template-columns: 195px 1fr 195px;
    gap: 15px;
    align-items: center;
    margin-top: -2px;
  }

  /* --- LEFT COLUMN: QUOTE CARD + BOOKS STACK --- */
  .left-side-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
  }

  .quote-card {
    background: rgba(255, 255, 255, 0.75);
    border: 1px solid rgba(199, 173, 141, 0.7);
    border-radius: 8px;
    padding: 9px 12px;
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    color: #3d1a06;
    line-height: 1.4;
    position: relative;
    box-shadow: 0 2px 6px rgba(61,26,6,0.06);
    width: 100%;
  }

  .quote-icon {
    font-size: 14px;
    color: #c7ad8d;
    font-weight: bold;
    line-height: 1;
  }

  .quote-heart {
    color: #c7ad8d;
    font-size: 9px;
    margin-top: 3px;
  }

  .side-illustration-img {
    width: 175px;
    height: 125px;
    object-fit: contain;
    mix-blend-mode: multiply;
    filter: drop-shadow(0 3px 6px rgba(61,26,6,0.12));
    border-radius: 6px;
  }

  /* --- CENTER COLUMN: RECIPIENT NAME & CITATION --- */
  .center-citation-col {
    text-align: center;
    padding: 0 5px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-self: center;
    transform: translateY(-28px);
  }

  .recipient-name-wrap {
    margin: 0 0 8px;
  }

  .recipient-name-text {
    font-family: 'Playfair Display', 'Times New Roman', serif;
    font-size: 38px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1.5px;
    display: inline-block;
    border-bottom: 2.5px stroke #c7ad8d;
    padding: 0 14px 4px;
    line-height: 1.2;
  }

  .particulars-subline {
    font-size: 14px;
    color: #3d1a06;
    margin-bottom: 14px;
    font-weight: 700;
  }

  .bold-data-value {
    color: #3d1a06;
    font-weight: 800;
  }

  .citation-main-text {
    font-size: 15px;
    line-height: 1.6;
    color: #3d1a06;
    margin-bottom: 10px;
    max-width: 98%;
    margin-left: auto;
    margin-right: auto;
    font-weight: 600;
  }

  .script-closing-note {
    font-family: 'Great Vibes', 'Alex Brush', cursive;
    font-size: 28px;
    color: #3d1a06;
    margin-top: 4px;
  }

  .heart-accent {
    color: #c7ad8d;
    font-size: 10px;
    margin-top: 2px;
  }

  /* --- RIGHT COLUMN: QUOTE CARD + FLOWERS VASE --- */
  .right-side-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
  }

  /* ===== BOTTOM SIGNATURES & SEAL ROW ===== */
  .bottom-signatures-row {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding: 0 35px 4px;
    margin-top: 2px;
  }

  .sig-block {
    text-align: center;
    width: 150px;
  }

  .pencil-icon-wrap {
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2px;
  }

  .pencil-icon-wrap svg {
    color: #3d1a06;
    opacity: 0.7;
  }

  .sig-line {
    height: 1px;
    background: #3d1a06;
    margin-bottom: 4px;
    position: relative;
  }

  .sig-title-text {
    font-size: 9.5px;
    font-weight: 800;
    color: #3d1a06;
    letter-spacing: 0.8px;
  }

  /* Bottom Center Medal Seal Stamp */
  .center-seal-wrapper {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 0px;
    transform: translateY(-10px);
  }

  .center-medal-seal {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: radial-gradient(circle, #fffdf9 0%, #f7efdf 70%, #c7ad8d 100%);
    border: 3px stroke #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -2px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(61,26,6,0.25);
    z-index: 5;
  }

  .center-medal-logo {
    width: 52px;
    height: 52px;
    object-fit: contain;
  }

  /* Medal Ribbon Tails */
  .medal-ribbons {
    position: absolute;
    bottom: -10px;
    display: flex;
    gap: 8px;
    z-index: 4;
  }

  .ribbon-tail {
    width: 14px;
    height: 22px;
    background: #3d1a06;
    border: 1px solid #c7ad8d;
    clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);
  }

  /* ===== BOTTOM DARK MAHOGANY BAR ===== */
  .bottom-dark-bar {
    background: #2b1204;
    color: #c7ad8d;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 25px;
    font-size: 8.5px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
    border-top: 1.5px solid #c7ad8d;
    border-radius: 6px;
    margin: 0 14px 26px;
    position: relative;
    z-index: 6;
  }

  .bar-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .bar-divider {
    color: #c7ad8d;
    opacity: 0.5;
  }

  /* ===== PRINT FLOATING CONTROL BAR ===== */
  .floating-print-bar {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    background: #3d1a06;
    padding: 10px 18px;
    border-radius: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .btn-print {
    background: #c7ad8d;
    color: #3d1a06;
    border: none;
    padding: 8px 18px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
  }

  .btn-print:hover {
    background: #dfc8a9;
    transform: translateY(-1px);
  }

  .btn-back {
    color: #fdfaf3;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 5px;
  }
</style>
</head>
<body>

  <!-- Floating Print Control Bar (Screen only) -->
  <div class="floating-print-bar no-print">
    <a href="javascript:history.back()" class="btn-back">← Back</a>
    <button onclick="window.print()" class="btn-print">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
      Print Certificate
    </button>
  </div>

  <div class="cert-page">

    <!-- Geometric Tile Pattern Background Top Overlay -->
    <div class="tile-pattern-bg"></div>

    <!-- Outer & Inner Golden Borders -->
    <div class="border-outer"></div>
    <div class="border-inner"></div>

    <!-- Corner Filigree SVG Overlays -->
    <svg class="corner-svg corner-tl" viewBox="0 0 100 100"><path d="M0,0 L100,0 L100,10 L10,10 L10,100 L0,100 Z M20,20 L80,20 L80,25 L25,25 L25,80 L20,80 Z"/></svg>
    <svg class="corner-svg corner-tr" viewBox="0 0 100 100"><path d="M0,0 L100,0 L100,10 L10,10 L10,100 L0,100 Z M20,20 L80,20 L80,25 L25,25 L25,80 L20,80 Z"/></svg>
    <svg class="corner-svg corner-bl" viewBox="0 0 100 100"><path d="M0,0 L100,0 L100,10 L10,10 L10,100 L0,100 Z M20,20 L80,20 L80,25 L25,25 L25,80 L20,80 Z"/></svg>
    <svg class="corner-svg corner-br" viewBox="0 0 100 100"><path d="M0,0 L100,0 L100,10 L10,10 L10,100 L0,100 Z M20,20 L80,20 L80,25 L25,25 L25,80 L20,80 Z"/></svg>

    <div class="cert-inner">

      <!-- TOP HEADER SECTION -->
      <div class="header-row">

        <!-- Top Left School Logo Seal -->
        <div class="header-left-logo-wrap">
          <img src="{{ asset('assets/images/logo.png') }}" alt="School Logo" class="official-logo-img">
        </div>

        <!-- Top Center School Details -->
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

        <!-- Top Right Hanging Gold Lanterns Vector -->
        <div class="header-right-lanterns">
          <svg class="mosque-lantern-svg" viewBox="0 0 120 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Mosque Domes Silhouette Light Fill -->
            <path d="M10 80 Q30 50 50 80 Q70 40 90 80 Q105 60 115 80 V95 H5 V80 Z" fill="#3d1a06" opacity="0.08"/>
            <!-- Hanging Chain 1 -->
            <line x1="55" y1="0" x2="55" y2="35" stroke="#c7ad8d" stroke-width="1.2"/>
            <!-- Lantern 1 -->
            <path d="M55 35 L48 42 H62 L55 35 Z" fill="#3d1a06"/>
            <path d="M48 42 L46 62 H64 L62 42 Z" fill="url(#lanternGlow1)" stroke="#c7ad8d" stroke-width="1"/>
            <path d="M46 62 L55 70 L64 62 Z" fill="#3d1a06"/>
            <circle cx="55" cy="52" r="4" fill="#ffd700"/>

            <!-- Hanging Chain 2 -->
            <line x1="88" y1="0" x2="88" y2="22" stroke="#c7ad8d" stroke-width="1.2"/>
            <!-- Lantern 2 -->
            <path d="M88 22 L82 28 H94 L88 22 Z" fill="#3d1a06"/>
            <path d="M82 28 L80 46 H96 L94 28 Z" fill="url(#lanternGlow1)" stroke="#c7ad8d" stroke-width="1"/>
            <path d="M80 46 L88 53 L96 46 Z" fill="#3d1a06"/>
            <circle cx="88" cy="37" r="3.5" fill="#ffd700"/>

            <defs>
              <linearGradient id="lanternGlow1" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#fff5cc"/>
                <stop offset="50%" stop-color="#e6b800"/>
                <stop offset="100%" stop-color="#996600"/>
              </linearGradient>
            </defs>
          </svg>
        </div>

      </div>

      <!-- MAIN TITLE BANNER SECTION -->
      <div class="main-title-section">
        <div class="script-title-wrap">
          <!-- Left Leaf Flourish -->
          <svg class="leaf-flourish-svg" viewBox="0 0 50 30"><path d="M45,15 Q30,5 15,15 Q30,25 45,15 Z M30,15 Q20,8 5,12 Q18,20 30,15 Z"/></svg>

          @php
            $rawTitle = trim((string)($data['award_title'] ?: 'APPRECIATION'));
            // Extract category word e.g. "Teacher", "Student", "Staff" or "Special"
            $categoryWord = 'Teacher';
            if (stripos($rawTitle, 'student') !== false) {
                $categoryWord = 'Student';
            } elseif (stripos($rawTitle, 'staff') !== false) {
                $categoryWord = 'Staff';
            } elseif (stripos($rawTitle, 'teacher') !== false) {
                $categoryWord = 'Teacher';
            } else {
                $categoryWord = 'Special';
            }
          @endphp

          <div class="script-award-category">{{ $categoryWord }}</div>

          <!-- Right Leaf Flourish -->
          <svg class="leaf-flourish-svg" viewBox="0 0 50 30" style="transform: scaleX(-1);"><path d="M45,15 Q30,5 15,15 Q30,25 45,15 Z M30,15 Q20,8 5,12 Q18,20 30,15 Z"/></svg>
        </div>

        <div class="bold-appreciation-title">APPRECIATION</div>

        <div class="presented-to-line">
          <span class="diamond-bullet">❖</span>
          THIS CERTIFICATE IS PROUDLY PRESENTED TO
          <span class="diamond-bullet">❖</span>
        </div>
      </div>

      <!-- MIDDLE CONTENT GRID (3 COLUMNS) -->
      <div class="middle-content-grid">

        <!-- LEFT SIDE COLUMN: QUOTE CARD + STACK OF BOOKS & PENS -->
        <div class="left-side-col">
          <div class="quote-card">
            <span class="quote-icon">“</span>
            @if(stripos($categoryWord, 'student') !== false)
              Excellence is not a skill, it is an attitude. Success belongs to those who work hard.
            @else
              A good teacher can inspire hope, ignite imagination, and instill a love of learning.
            @endif
            <div class="quote-heart">♥</div>
          </div>
          <img src="{{ asset('assets/images/cert_books.png') }}" alt="Books & Pens" class="side-illustration-img">
        </div>

        <!-- CENTER COLUMN: RECIPIENT NAME & APPRECIATION CITATION -->
        <div class="center-citation-col">
          
          <div class="recipient-name-wrap">
            <div class="recipient-name-text">{{ $data['recipient_name'] ?: ($data['student_name'] ?: 'Recipient Name') }}</div>
          </div>

          <div class="particulars-subline">
            @php
              $parts = [];
              if (!empty($data['father_name'])) {
                  $parts[] = 'S/o, D/o: <strong class="bold-data-value">'.e($data['father_name']).'</strong>';
              }
              $desig = trim($data['designation'] ?? '');
              $cls   = trim($data['class_name'] ?? '');
              $sec   = trim($data['section'] ?? '');

              if ($desig !== '') {
                  $isClassDesig = (stripos($desig, 'class') === 0);
                  if (!$cls || !$isClassDesig) {
                      $parts[] = '<strong class="bold-data-value">'.e($desig).'</strong>';
                  }
              }
              if ($cls !== '') {
                  $clsClean = preg_replace('/^class\s+/i', '', $cls);
                  $parts[] = 'Class: <strong class="bold-data-value">Class '.e($clsClean).'</strong>' . ($sec ? ' ('.e($sec).')' : '');
              }
            @endphp
            {!! implode('&nbsp; • &nbsp;', $parts) !!}
          </div>

          <div class="citation-main-text">
            @if(!empty($data['remarks']))
              {{ $data['remarks'] }}
            @else
              In recognition of your dedication, hard work and valuable contribution towards shaping young minds and building a better future. Your passion, patience and guidance make a lasting difference in the lives of our students.
            @endif
          </div>

          <div class="script-closing-note">
            @if(stripos($categoryWord, 'student') !== false)
              Thank you for being an exemplary student.
            @else
              Thank you for being an inspiring educator.
            @endif
          </div>
          <div class="heart-accent">♥</div>

        </div>

        <!-- RIGHT SIDE COLUMN: QUOTE CARD + WHITE FLOWER VASE -->
        <div class="right-side-col">
          <div class="quote-card">
            <span class="quote-icon">“</span>
            @if(stripos($categoryWord, 'student') !== false)
              Education is the passport to the future, for tomorrow belongs to those who prepare for it today.
            @else
              You don't just teach lessons, you shape characters and change lives.
            @endif
            <div class="quote-heart">♥</div>
          </div>
          <img src="{{ asset('assets/images/cert_flowers.png') }}" alt="Flowers & Desk Plaque" class="side-illustration-img">
        </div>

      </div>

      <!-- BOTTOM SIGNATURES & OFFICIAL SEAL ROW -->
      <div>
        <div class="bottom-signatures-row">

          <!-- Left: Principal Signature Block -->
          <div class="sig-block">
            <div class="pencil-icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
            </div>
            <div class="sig-line"></div>
            <div class="sig-title-text">{{ strtoupper($data['principal'] ?: 'Principal') }}</div>
          </div>

          <!-- Center: Medal Seal Stamp -->
          <div class="center-seal-wrapper">
            <div class="center-medal-seal">
              <img src="{{ asset('assets/images/logo.png') }}" alt="Seal Logo" class="center-medal-logo">
            </div>
            <div class="medal-ribbons">
              <div class="ribbon-tail"></div>
              <div class="ribbon-tail"></div>
            </div>
          </div>

          <!-- Right: Head of Institution Signature Block -->
          <div class="sig-block">
            <div class="pencil-icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
            </div>
            <div class="sig-line"></div>
            <div class="sig-title-text">{{ strtoupper($data['head_of_institution'] ?: 'Head of Institution') }}</div>
          </div>

        </div>

        <!-- BOTTOM DARK MAHOGANY BAR -->
        <div class="bottom-dark-bar">
          <div class="bar-item">📖 KNOWLEDGE</div>
          <span class="bar-divider">|</span>
          <div class="bar-item">🌙 FAITH</div>
          <span class="bar-divider">|</span>
          <div class="bar-item">🛡️ CHARACTER</div>
          <span class="bar-divider">|</span>
          <div class="bar-item">🏆 EXCELLENCE</div>
        </div>

      </div>

    </div>
  </div>

</body>
</html>
