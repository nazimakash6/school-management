<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Report & Progress Card — {{ $data['student_name'] ?: 'Noor Ul Huda Superior School' }}</title>
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

  /* ===== PRINT PAGE SETUP (A4 PORTRAIT) ===== */
  @page {
    size: A4 portrait;
    margin: 0;
  }

  @media print {
    body { background: #fff !important; }
    .no-print { display: none !important; }
    .cert-page {
      box-shadow: none !important;
      margin: 0 auto !important;
      width: 100% !important;
      height: 100% !important;
      page-break-after: avoid;
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
  .btn-back { background: #edf2f7; color: #4a5568; }
  .btn-back:hover { background: #e2e8f0; }

  /* ===== MAIN PORTRAIT CONTAINER ===== */
  .cert-page {
    width: 210mm;
    min-height: 297mm;
    margin: 20px auto;
    background: #fdfaf3; /* Warm ivory matching reference image */
    position: relative;
    box-shadow: 0 12px 45px rgba(61, 26, 6, 0.25);
    padding: 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* Outer Framed Border with Corner Accents */
  .outer-frame {
    width: 100%;
    height: 100%;
    border: 4px solid #3d1a06;
    border-radius: 12px;
    padding: 10px;
    position: relative;
    background: #fdfaf3;
    display: flex;
    flex-direction: column;
    gap: 8px;
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
    margin-top: 4px;
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
  }
  .header-logo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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
  <a href="{{ route('certificates.index') }}" class="btn-action btn-back">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Back to Certificates
  </a>
</div>

<!-- PORTRAIT A4 CONTAINER -->
<div class="cert-page">
  <div class="outer-frame">
    <div class="inner-frame"></div>

    <!-- 1. HEADER SECTION -->
    <div class="header-container">
      <div class="header-ayah">رَّبِّ زِدْنِي عِلْمًا</div>
      <div class="header-translation">"My Lord! Increase me in knowledge." (Quran 20:114)</div>

      <div class="header-main-grid">
        <!-- School Logo -->
        <div class="header-logo-box">
          <div class="header-school-seal">
            <span class="header-school-seal-text">NOOR UL HUDA<br>• SCHOOL •</span>
          </div>
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
            <span class="info-value">{{ $data['student_name'] ?: '________________________' }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">🏫 Class & Section :</span>
            <span class="info-value">{{ trim(($data['class_name'] ?? '') . ' ' . ($data['section'] ?? '')) ?: '________________________' }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">🆔 Roll No. :</span>
            <span class="info-value">{{ $data['roll_no'] ?: '__________' }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">📅 Date / Period :</span>
            <span class="info-value">{{ $data['date_period'] ?: date('F Y') }}</span>
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
          {{ $data['focus_area'] ?: 'General Academics & Moral Conduct' }}
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
              <th style="width:32%;">SUBJECT / AREA</th>
              <th title="Excellent">EXCELLENT<br>★</th>
              <th title="Good">GOOD<br>👍</th>
              <th title="Average">AVERAGE<br>-</th>
              <th title="Needs Improvement">NEEDS IMP.<br>!</th>
              <th style="width:28%;">REMARKS</th>
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
              $ptData = $data['performance_tracker'] ?? [];
            @endphp
            @foreach($defaultSubjects as $idx => $subjItem)
              @php
                $subjKey = $subjItem['name'];
                $rowRating = '';
                $rowRemark = '';
                
                // Find matching item from submitted array
                if (is_array($ptData)) {
                  foreach($ptData as $ptItem) {
                    if (is_array($ptItem) && isset($ptItem['subject']) && $ptItem['subject'] === $subjKey) {
                      $rowRating = strtolower($ptItem['rating'] ?? '');
                      $rowRemark = $ptItem['remarks'] ?? '';
                      break;
                    }
                  }
                }
                // Fallback default rating
                if (empty($rowRating)) {
                  $rowRating = ($idx === 0 || $idx === 4) ? 'excellent' : 'good';
                }
              @endphp
              <tr>
                <td class="subject-name">
                  <span style="margin-right:2px;">{{ $subjItem['icon'] }}</span> {{ $subjItem['name'] }}
                </td>
                <td class="cell-check mark-star">{{ $rowRating === 'excellent' ? '★' : '' }}</td>
                <td class="cell-check mark-thumb">{{ $rowRating === 'good' ? '👍' : '' }}</td>
                <td class="cell-check mark-avg">{{ $rowRating === 'average' ? '✓' : '' }}</td>
                <td class="cell-check mark-needs">{{ $rowRating === 'needs_improvement' ? '!' : '' }}</td>
                <td class="cell-remarks">{{ $rowRemark }}</td>
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
              $skData = $data['skills_attributes'] ?? [];
            @endphp
            @foreach($defaultSkills as $sIdx => $skItem)
              @php
                $skKey = $skItem['name'];
                $skRating = '';
                $skRemark = '';
                
                if (is_array($skData)) {
                  foreach($skData as $skElement) {
                    if (is_array($skElement) && isset($skElement['skill']) && $skElement['skill'] === $skKey) {
                      $skRating = strtolower($skElement['rating'] ?? '');
                      $skRemark = $skElement['remarks'] ?? '';
                      break;
                    }
                  }
                }
                if (empty($skRating)) {
                  $skRating = ($sIdx % 2 === 0) ? 'excellent' : 'good';
                }
              @endphp
              <tr>
                <td class="subject-name">
                  <span style="margin-right:2px;">{{ $skItem['icon'] }}</span> {{ $skItem['name'] }}
                </td>
                <td class="cell-check mark-star">{{ $skRating === 'excellent' ? '★' : '' }}</td>
                <td class="cell-check mark-thumb">{{ $skRating === 'good' ? '👍' : '' }}</td>
                <td class="cell-check mark-avg">{{ $skRating === 'average' ? '✓' : '' }}</td>
                <td class="cell-check mark-needs">{{ $skRating === 'needs_improvement' ? '!' : '' }}</td>
                <td class="cell-remarks">{{ $skRemark }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- 4. MIDDLE 3-BOX ROW (LEARNING HIGHLIGHTS, AREAS TO IMPROVE, ATTENDANCE) -->
    <div class="middle-grid">
      <div class="box-card">
        <div class="box-card-header">
          <span>🏅</span>
          <span>LEARNING HIGHLIGHTS</span>
        </div>
        <div class="box-card-content">
          {{ $data['learning_highlights'] ?: 'Shows great interest in Quranic recitation & Mathematics. Consistently completes home assignments on time.' }}
        </div>
      </div>

      <div class="box-card">
        <div class="box-card-header">
          <span>📈</span>
          <span>AREAS TO IMPROVE</span>
        </div>
        <div class="box-card-content">
          {{ $data['areas_to_improve'] ?: 'Needs slight improvement in English vocabulary & handwriting neatness.' }}
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
            <span class="val">{{ $data['attendance_total'] ?: '25' }}</span>
          </div>
          <div class="attendance-row">
            <span>Present Days :</span>
            <span class="val">{{ $data['attendance_present'] ?: '24' }}</span>
          </div>
          <div class="attendance-row">
            <span>Absent Days :</span>
            <span class="val">{{ $data['attendance_absent'] ?: '1' }}</span>
          </div>
          <div class="attendance-row" style="margin-top:2px;">
            <span>Attendance % :</span>
            <span class="val" style="color:#166534;font-weight:900;">{{ $data['attendance_pct'] ?: '96%' }}</span>
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
          <!-- Academic -->
          <div class="star-category-item">
            <span class="star-cat-icon">🎓</span>
            <span class="star-cat-title">ACADEMIC</span>
            <div class="stars-render">
              @php $aCount = (int)($data['academic_stars'] ?? 5); @endphp
              @for($i=1; $i<=5; $i++) {{ $i <= $aCount ? '★' : '☆' }} @endfor
            </div>
          </div>

          <!-- Islamic Development -->
          <div class="star-category-item">
            <span class="star-cat-icon">🕌</span>
            <span class="star-cat-title">ISLAMIC DEV.</span>
            <div class="stars-render">
              @php $iCount = (int)($data['islamic_stars'] ?? 5); @endphp
              @for($i=1; $i<=5; $i++) {{ $i <= $iCount ? '★' : '☆' }} @endfor
            </div>
          </div>

          <!-- Personal Development -->
          <div class="star-category-item">
            <span class="star-cat-icon">👤</span>
            <span class="star-cat-title">PERSONAL DEV.</span>
            <div class="stars-render">
              @php $pCount = (int)($data['personal_stars'] ?? 5); @endphp
              @for($i=1; $i<=5; $i++) {{ $i <= $pCount ? '★' : '☆' }} @endfor
            </div>
          </div>

          <!-- Behaviour -->
          <div class="star-category-item">
            <span class="star-cat-icon">⭐</span>
            <span class="star-cat-title">BEHAVIOUR</span>
            <div class="stars-render">
              @php $bCount = (int)($data['behaviour_stars'] ?? 5); @endphp
              @for($i=1; $i<=5; $i++) {{ $i <= $bCount ? '★' : '☆' }} @endfor
            </div>
          </div>

          <!-- Co-Curricular -->
          <div class="star-category-item">
            <span class="star-cat-icon">🏆</span>
            <span class="star-cat-title">CO-CURRICULAR</span>
            <div class="stars-render">
              @php $cCount = (int)($data['cocurricular_stars'] ?? 5); @endphp
              @for($i=1; $i<=5; $i++) {{ $i <= $cCount ? '★' : '☆' }} @endfor
            </div>
          </div>
        </div>

        <!-- Overall Progress Circle -->
        <div class="overall-status-box">
          <div class="overall-status-title">OVERALL PROGRESS</div>
          <div class="overall-circle">
            👍
          </div>
          <div class="overall-text">{{ $data['overall_progress'] ?: 'GOOD' }}</div>
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
          {{ $data['teacher_remarks'] ?: 'Demonstrates exemplary academic performance and excellent moral conduct in school.' }}
        </div>
      </div>

      <div class="box-card">
        <div class="box-card-header">
          <span>👤</span>
          <span>PARENT'S REMARKS</span>
        </div>
        <div class="box-card-content">
          {{ $data['parent_remarks'] ?: 'Very satisfied with student progress and school environment.' }}
        </div>
      </div>

      <div class="box-card">
        <div class="box-card-header">
          <span>🎯</span>
          <span>ACTION PLAN / NEXT STEPS</span>
        </div>
        <div class="box-card-content">
          {{ $data['action_plan'] ?: 'Continue reading practice, participate in upcoming Tajweed competition & sports gala.' }}
        </div>
      </div>
    </div>

    <!-- 7. SIGNATURES & STAMP SECTION -->
    <div class="sig-section">
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-title">✏️ Subject Teacher</div>
      </div>

      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-title">✏️ Class Teacher</div>
      </div>

      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-title">👤 Parent / Guardian</div>
      </div>

      <div class="sig-box">
        <div class="sig-line"></div>
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
      <span>•</span>
      <span>CHARACTER</span>
      <span>•</span>
      <span>EXCELLENCE</span>
    </div>

  </div>
</div>

</body>
</html>
