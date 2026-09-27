<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skill Evaluation Report — {{ $student->full_name ?? 'Student' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  /* ===== GLOBAL RESET & PRINT SETUP ===== */
  *, *::before, *::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  html, body {
    width: 100%;
    min-height: 100%;
    background: #0f172a;
    font-family: 'Plus Jakarta Sans', 'Segoe UI', -apple-system, sans-serif;
    color: #3d1a06;
    line-height: 1.35;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  @page {
    size: A4 portrait;
    margin: 5mm 5mm 5mm 5mm;
  }

  @media print {
    html, body {
      background: #ffffff !important;
      width: 210mm !important;
      height: 297mm !important;
      margin: 0 !important;
      padding: 0 !important;
      overflow: hidden !important;
    }
    .no-print {
      display: none !important;
    }
    .form-container {
      width: 100% !important;
      max-height: 286mm !important;
      margin: 0 !important;
      padding: 5mm 6mm !important;
      box-shadow: none !important;
      border: 2px solid #3d1a06 !important;
      outline: none !important;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
      overflow: hidden !important;
      background: #fdfaf3 !important;
    }
  }

  /* ===== TOP ACTION BAR (SCREEN ONLY) ===== */
  .action-bar {
    width: 210mm;
    margin: 16px auto 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #3d1a06;
    padding: 12px 20px;
    border-radius: 10px;
    border: 1px solid #c7ad8d;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
  }

  .action-bar .status-text {
    color: #fdfaf3;
    font-size: 13.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .btn-group-actions {
    display: flex;
    gap: 10px;
  }

  .btn-act {
    padding: 8px 16px;
    border-radius: 7px;
    font-weight: 700;
    font-size: 12.5px;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .btn-print { background: #16a34a; color: #ffffff; box-shadow: 0 2px 8px rgba(22,163,74,0.3); }
  .btn-print:hover { background: #15803d; transform: translateY(-1px); }
  .btn-cert { background: #c7ad8d; color: #3d1a06; box-shadow: 0 2px 8px rgba(199,173,141,0.3); }
  .btn-cert:hover { background: #b89b78; transform: translateY(-1px); }
  .btn-edit { background: #3b82f6; color: #ffffff; }
  .btn-edit:hover { background: #2563eb; }
  .btn-back { background: rgba(253,250,243,0.15); color: #fdfaf3; border: 1px solid rgba(199,173,141,0.4); }
  .btn-back:hover { background: rgba(253,250,243,0.25); }
  .btn-close-act { background: #dc2626; color: #ffffff; box-shadow: 0 2px 8px rgba(220,38,38,0.3); }
  .btn-close-act:hover { background: #b91c1c; transform: translateY(-1px); }

  /* ===== MAIN A4 CONTAINER ===== */
  .form-container {
    width: 210mm;
    min-height: 285mm;
    margin: 14px auto 30px;
    background: #fdfaf3;
    border: 3px double #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -6px;
    padding: 7mm 9mm;
    box-shadow: 0 15px 40px rgba(0,0,0,0.5);
    position: relative;
    overflow: hidden;
  }

  /* Decorative Corner Geometric Ornaments */
  .corner-ornament {
    position: absolute;
    width: 24px;
    height: 24px;
    border: 2px solid #c7ad8d;
    z-index: 5;
    pointer-events: none;
  }
  .corner-ornament.top-left { top: 8px; left: 8px; border-right: none; border-bottom: none; }
  .corner-ornament.top-right { top: 8px; right: 8px; border-left: none; border-bottom: none; }
  .corner-ornament.bottom-left { bottom: 8px; left: 8px; border-right: none; border-top: none; }
  .corner-ornament.bottom-right { bottom: 8px; right: 8px; border-left: none; border-top: none; }

  /* Background Watermark */
  .bg-watermark {
    position: absolute;
    top: 52%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 320px;
    opacity: 0.035;
    pointer-events: none;
    z-index: 1;
  }

  /* ===== HEADER SECTION ===== */
  .header-grid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #3d1a06;
    padding-bottom: 10px;
    margin-bottom: 10px;
    position: relative;
    z-index: 2;
  }

  .header-logo-wrapper {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid #c7ad8d;
    border-radius: 12px;
    background: #ffffff;
    padding: 4px;
    box-shadow: 0 4px 10px rgba(61,26,6,0.1);
  }

  .header-logo {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  .header-center {
    flex: 1;
    text-align: center;
    padding: 0 10px;
  }

  .bismillah {
    font-family: 'Amiri', serif;
    font-size: 17px;
    font-weight: 700;
    color: #3d1a06;
    line-height: 1.1;
    margin-bottom: 2px;
  }

  .school-name {
    font-family: 'Times New Roman', serif;
    font-size: 22px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1px;
    text-transform: uppercase;
    line-height: 1.15;
  }

  .school-tagline {
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 1px;
  }

  .report-title-badge {
    display: inline-block;
    background: #3d1a06;
    color: #fdfaf3;
    border: 1px solid #c7ad8d;
    padding: 4px 18px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-top: 6px;
    box-shadow: 0 3px 8px rgba(61,26,6,0.2);
  }

  .header-right-meta {
    width: 130px;
    background: #ffffff;
    border: 1px solid #c7ad8d;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 9.5px;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  .meta-item {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px dashed #e2e8f0;
    padding-bottom: 3px;
    margin-bottom: 3px;
  }
  .meta-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }

  .meta-item .lbl {
    font-weight: 700;
    color: #64748b;
  }

  .meta-item .val {
    font-weight: 800;
    color: #3d1a06;
  }

  /* ===== SECTION HEADERS ===== */
  .section-bar {
    background: linear-gradient(90deg, #3d1a06 0%, #5a270a 100%);
    color: #ffffff;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 5px 12px;
    margin-top: 10px;
    margin-bottom: 6px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 6px rgba(61,26,6,0.15);
  }

  .section-bar span.sec-num {
    background: #c7ad8d;
    color: #3d1a06;
    padding: 1px 7px;
    border-radius: 3px;
    font-weight: 900;
    margin-right: 6px;
  }

  /* ===== DATA TABLES ===== */
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10.5px;
    margin-bottom: 6px;
    background: #ffffff;
    border: 1px solid #c7ad8d;
    border-radius: 6px;
    overflow: hidden;
  }

  table.data-table td {
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    vertical-align: middle;
  }

  .td-lbl {
    font-weight: 700;
    color: #3d1a06;
    background: #fdfaf3;
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: 18%;
  }

  .td-val {
    color: #0f172a;
    font-weight: 600;
  }

  /* ===== KPI SCORE CARDS GRID ===== */
  .kpi-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin: 8px 0;
  }

  .kpi-card {
    background: #ffffff;
    border: 1px solid #c7ad8d;
    border-radius: 8px;
    padding: 8px 10px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    position: relative;
    overflow: hidden;
  }

  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3.5px;
    background: #3d1a06;
  }

  .kpi-card.highlight::before {
    background: #c7ad8d;
  }

  .kpi-title {
    font-size: 8.5px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 3px;
  }

  .kpi-value {
    font-size: 18px;
    font-weight: 900;
    color: #3d1a06;
    line-height: 1.1;
  }

  .kpi-sub {
    font-size: 8.5px;
    font-weight: 700;
    color: #16a34a;
    margin-top: 2px;
  }

  /* ===== PERFORMANCE BADGE & RATING BAR ===== */
  .badge-rating-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border: 1.5px solid #3d1a06;
    border-radius: 8px;
    padding: 8px 14px;
    margin: 8px 0;
  }

  .badge-pill-main {
    background: #3d1a06;
    color: #c7ad8d;
    border: 1px solid #c7ad8d;
    padding: 5px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .stars-row {
    display: flex;
    align-items: center;
    gap: 3px;
    color: #f59e0b;
    font-size: 16px;
  }

  .stars-row .num-text {
    font-size: 12px;
    font-weight: 800;
    color: #3d1a06;
    margin-left: 6px;
    font-family: Arial, sans-serif;
  }

  /* Progress Pill Bar */
  .progress-outer {
    height: 8px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 4px;
    width: 100%;
  }

  .progress-inner {
    height: 100%;
    background: linear-gradient(90deg, #3d1a06 0%, #c7ad8d 100%);
    border-radius: 10px;
  }

  /* ===== INSTRUCTOR REMARKS CARD ===== */
  .remarks-card {
    background: #ffffff;
    border-left: 4px solid #c7ad8d;
    border-top: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 12px;
    margin-top: 6px;
    position: relative;
  }

  .remarks-quote-mark {
    position: absolute;
    right: 12px;
    top: 6px;
    font-size: 28px;
    color: rgba(199,173,141,0.25);
    font-family: Georgia, serif;
    line-height: 1;
  }

  .remarks-text {
    font-size: 10.5px;
    font-style: italic;
    color: #3d1a06;
    line-height: 1.4;
  }

  /* ===== SIGNATURES SECTION ===== */
  .signatures-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 26px;
    padding-top: 4px;
    text-align: center;
    position: relative;
    z-index: 2;
  }

  .sig-col {
    flex: 1;
    padding: 0 14px;
  }

  .sig-line-bar {
    border-top: 1.5px dashed #3d1a06;
    margin-bottom: 5px;
  }

  .sig-title-text {
    font-size: 9.5px;
    font-weight: 800;
    color: #3d1a06;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .sig-subtitle {
    font-size: 8px;
    color: #64748b;
  }

  .stamp-box-placeholder {
    width: 60px;
    height: 60px;
    border: 1px dashed #c7ad8d;
    border-radius: 50%;
    margin: 0 auto 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 7.5px;
    color: #94a3b8;
    text-transform: uppercase;
  }

  .footer-notice {
    text-align: center;
    font-size: 8px;
    font-weight: 600;
    color: #64748b;
    margin-top: 10px;
    border-top: 1px solid #c7ad8d;
    padding-top: 4px;
    letter-spacing: 0.5px;
  }
</style>
</head>
<body>

<!-- Screen Top Action Bar -->
<div class="action-bar no-print">
  <div class="status-text">
    <span>🖨️</span> Official Student Skill Performance Report — Ready for Printing
  </div>
  <div class="btn-group-actions">
    <button onclick="window.print()" class="btn-act btn-print">
      🖨️ Print Evaluation Report
    </button>
    <a href="{{ route('skills-institute.certificate', $skill->id) }}" target="_blank" class="btn-act btn-cert">
      📜 Skill Certificate
    </a>
    <a href="{{ route('skills-institute.edit', $skill->id) }}" class="btn-act btn-edit">
      ✏️ Edit Evaluation
    </a>
    <a href="{{ route('skills-institute.show', $skill->id) }}" class="btn-act btn-back">
      👤 Scorecard View
    </a>
    <button onclick="if(window.opener || window.history.length > 1){ window.close(); } else { window.location.href='{{ route('skills-institute.show', $skill->id) }}'; }" class="btn-act btn-close-act">
      ✕ Close
    </button>
  </div>
</div>

<!-- Main A4 Document -->
<div class="form-container">

  <!-- Corner Ornaments -->
  <div class="corner-ornament top-left"></div>
  <div class="corner-ornament top-right"></div>
  <div class="corner-ornament bottom-left"></div>
  <div class="corner-ornament bottom-right"></div>

  <!-- Background Watermark -->
  <img src="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" class="bg-watermark" alt="Watermark" onerror="this.onerror=null; this.style.display='none';">

  <!-- ===== HEADER ===== -->
  <div class="header-grid">
    <div class="header-logo-wrapper">
      <img src="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" alt="School Logo" class="header-logo" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
    </div>

    <div class="header-center">
      <div class="bismillah">رَّبِّ زِدْنِي عِلْمًا</div>
      <div class="school-name">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}</div>
      <div class="school-tagline">{{ $globalSchoolInfo->tagline ?? 'DISCIPLINE | EDUCATION | EXCELLENCE' }}</div>
      <div>
        <span class="report-title-badge">SKILL EVALUATION REPORT</span>
      </div>
    </div>

    <div class="header-right-meta">
      <div class="meta-item">
        <span class="lbl">Report ID:</span>
        <span class="val">#SK-{{ str_pad($skill->id, 4, '0', STR_PAD_LEFT) }}</span>
      </div>
      <div class="meta-item">
        <span class="lbl">Eval Date:</span>
        <span class="val">{{ $skill->evaluation_date ? $skill->evaluation_date->format('d/m/Y') : '____/____' }}</span>
      </div>
      <div class="meta-item">
        <span class="lbl">Roll No:</span>
        <span class="val">{{ $student->roll_no ?: 'N/A' }}</span>
      </div>
      <div class="meta-item">
        <span class="lbl">Adm No:</span>
        <span class="val">{{ $student->admission_no ?: 'N/A' }}</span>
      </div>
    </div>
  </div>

  <!-- ===== 1. STUDENT & ACADEMIC PROFILE ===== -->
  <div class="section-bar">
    <div><span class="sec-num">1</span> STUDENT &amp; ACADEMIC PROFILE</div>
    <div style="font-size: 8.5px; opacity: 0.9;">SESSION: {{ $skill->academicSession?->name ?: 'Current Session' }}</div>
  </div>

  <table class="data-table">
    <tr>
      <td class="td-lbl">Student Name</td>
      <td class="td-val" style="font-size: 11px; font-weight: 800; color: #3d1a06;">{{ $student->full_name }}</td>
      <td class="td-lbl">Father's Name</td>
      <td class="td-val">{{ $student->father_name ?: 'N/A' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Class &amp; Section</td>
      <td class="td-val">{{ $student->class_name }} {{ $student->section_name ? '('.$student->section_name.')' : '' }}</td>
      <td class="td-lbl">Academic Session</td>
      <td class="td-val">{{ $skill->academicSession?->name ?: 'N/A' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Roll Number</td>
      <td class="td-val">{{ $student->roll_no ?: 'N/A' }}</td>
      <td class="td-lbl">Admission Number</td>
      <td class="td-val">{{ $student->admission_no ?: 'N/A' }}</td>
    </tr>
  </table>

  <!-- ===== 2. EVALUATED VOCATIONAL SKILLS BREAKDOWN ===== -->
  <div class="section-bar">
    <div><span class="sec-num">2</span> EVALUATED VOCATIONAL SKILLS &amp; SCORES</div>
    <div style="font-size: 8.5px; opacity: 0.9;">TOTAL SKILLS: {{ count($skill->skills_list) }}</div>
  </div>

  <table class="data-table">
    <thead>
      <tr style="background: #3d1a06; color: #ffffff;">
        <th style="padding: 5px 8px; font-size: 9px; width: 4%; text-align: center;">#</th>
        <th style="padding: 5px 8px; font-size: 9px; width: 28%;">Skill / Course Topic</th>
        <th style="padding: 5px 8px; font-size: 9px; width: 22%;">Category</th>
        <th style="padding: 5px 8px; font-size: 9px; width: 16%;">Assessment</th>
        <th style="padding: 5px 8px; font-size: 9px; width: 9%; text-align: center;">Max</th>
        <th style="padding: 5px 8px; font-size: 9px; width: 9%; text-align: center;">Obtained</th>
        <th style="padding: 5px 8px; font-size: 9px; width: 12%; text-align: center;">Rating</th>
      </tr>
    </thead>
    <tbody>
      @foreach($skill->skills_list as $idx => $item)
        <tr>
          <td style="text-align: center; font-weight: 700;">{{ $idx + 1 }}</td>
          <td style="font-weight: 800; color: #3d1a06;">{{ $item['skill_name'] }}</td>
          <td><span style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 600; font-size: 9px;">{{ $item['skill_category'] }}</span></td>
          <td>{{ $item['assessment_type'] }}</td>
          <td style="text-align: center;">{{ number_format($item['total_score'], 1) }}</td>
          <td style="text-align: center; font-weight: 800;">{{ number_format($item['obtained_score'], 1) }}</td>
          <td style="text-align: center; font-weight: 800; color: #d97706;">
            ★ {{ number_format($item['star_rating'], 1) }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <!-- ===== 3. PERFORMANCE ANALYTICS & SCORECARD ===== -->
  <div class="section-bar">
    <div><span class="sec-num">3</span> OVERALL PERFORMANCE SCORECARD &amp; ANALYTICS</div>
  </div>

  @php
    $total = $skill->total_score > 0 ? $skill->total_score : 100;
    $obtained = $skill->obtained_score;
    $percentage = round(($obtained / $total) * 100, 1);
  @endphp

  <!-- KPI Cards Grid -->
  <div class="kpi-cards-grid">
    <div class="kpi-card">
      <div class="kpi-title">Max Total Score</div>
      <div class="kpi-value">{{ number_format($total, 1) }}</div>
      <div class="kpi-sub" style="color: #64748b;">Maximum Points</div>
    </div>

    <div class="kpi-card highlight">
      <div class="kpi-title">Obtained Score</div>
      <div class="kpi-value" style="color: #3d1a06;">{{ number_format($obtained, 1) }}</div>
      <div class="kpi-sub">Actual Achieved</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-title">Score Percentage</div>
      <div class="kpi-value" style="color: #16a34a;">{{ $percentage }}%</div>
      <div class="progress-outer">
        <div class="progress-inner" style="width: {{ min(100, $percentage) }}%;"></div>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-title">Star Score</div>
      <div class="kpi-value" style="color: #f59e0b;">{{ number_format($skill->star_rating, 1) }}</div>
      <div class="kpi-sub" style="color: #d97706;">Out of 5.0 Rating</div>
    </div>
  </div>

  <!-- Competency Ribbon & Stars Bar -->
  <div class="badge-rating-container">
    <div>
      <span style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 2px;">Awarded Competency Badge:</span>
      <span class="badge-pill-main">
        🏆 {{ $skill->badge_level }}
      </span>
    </div>

    <div style="text-align: right;">
      <span style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 2px;">Practical Rating:</span>
      <div class="stars-row">
        @for($i = 1; $i <= 5; $i++)
          <span>{{ $i <= round($skill->star_rating) ? '★' : '☆' }}</span>
        @endfor
        <span class="num-text">({{ number_format($skill->star_rating, 1) }} / 5.0)</span>
      </div>
    </div>
  </div>

  <!-- ===== 4. INSTRUCTOR REMARKS ===== -->
  <div class="section-bar">
    <div><span class="sec-num">4</span> INSTRUCTOR EVALUATION &amp; REMARKS</div>
  </div>

  <div class="remarks-card">
    <div class="remarks-quote-mark">“</div>
    <div class="remarks-text">
      {{ $skill->instructor_notes ? '"' . $skill->instructor_notes . '"' : 'The student has demonstrated commendable skill execution and steady practical progress during this evaluation period. Recommended for further advanced vocational module training.' }}
    </div>
    <div style="margin-top: 4px; font-size: 9px; font-weight: 700; color: #64748b; text-align: right;">
      — {{ $skill->creator->name ?? 'Course Instructor' }}, Skills Institute
    </div>
  </div>

  <!-- ===== SIGNATURES ===== -->
  <div class="signatures-row">
    <div class="sig-col">
      <div class="sig-line-bar"></div>
      <div class="sig-title-text">Course Instructor</div>
      <div class="sig-subtitle">Skills Institute Faculty</div>
    </div>

    <div class="sig-col" style="flex: 0 0 90px;">
      <div class="stamp-box-placeholder">Official Stamp</div>
    </div>

    <div class="sig-col">
      <div class="sig-line-bar"></div>
      <div class="sig-title-text">Head of Department</div>
      <div class="sig-subtitle">Vocational Training Wing</div>
    </div>

    <div class="sig-col">
      <div class="sig-line-bar"></div>
      <div class="sig-title-text">Principal Signature</div>
      <div class="sig-subtitle">{{ $globalSchoolInfo->school_name ?? 'Noor Ul Huda Superior School' }}</div>
    </div>
  </div>

  <div class="footer-notice">
    Official Computer-Generated Skill Assessment Report &bull; Issue Date: {{ date('d M, Y') }} &bull; {{ $globalSchoolInfo->school_name ?? 'Noor Ul Huda Superior School' }}
  </div>

</div>

</body>
</html>
