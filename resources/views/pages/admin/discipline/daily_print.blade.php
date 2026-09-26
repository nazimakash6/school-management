<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Daily Development Report — {{ $student->full_name }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
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
    background: #e2e8f0;
    font-family: 'Outfit', 'Times New Roman', serif;
    color: #3d1a06;
    line-height: 1.35;
  }

  @page {
    size: A4 portrait;
    margin: 4mm 5mm 4mm 5mm;
  }

  @media print {
    html, body {
      background: #ffffff !important;
      width: 210mm !important;
      height: 297mm !important;
      margin: 0 !important;
      padding: 0 !important;
      overflow: hidden !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    .no-print {
      display: none !important;
    }
    .daily-report-container {
      width: 100% !important;
      max-height: 288mm !important;
      margin: 0 !important;
      padding: 4mm 5mm !important;
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
  .action-bar-daily {
    width: 210mm;
    margin: 12px auto 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #3d1a06;
    padding: 10px 18px;
    border-radius: 8px;
    border: 1px solid #c7ad8d;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  }

  .action-bar-daily .status-text {
    color: #fdfaf3;
    font-size: 13px;
    font-weight: bold;
    font-family: Arial, sans-serif;
  }

  .btn-group-actions-daily {
    display: flex;
    gap: 8px;
  }

  .btn-act-daily {
    padding: 7px 15px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 12px;
    font-family: Arial, sans-serif;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .btn-print-daily { background: #22c55e; color: #ffffff; }
  .btn-print-daily:hover { background: #16a34a; }
  .btn-official-daily { background: #3b82f6; color: #ffffff; }
  .btn-official-daily:hover { background: #2563eb; }
  .btn-back-daily { background: #c7ad8d; color: #3d1a06; }
  .btn-back-daily:hover { background: #b89b78; }

  /* ===== MAIN A4 CONTAINER ===== */
  .daily-report-container {
    width: 210mm;
    min-height: 288mm;
    margin: 12px auto 20px;
    background: #fdfaf3;
    border: 3px double #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -5px;
    padding: 6mm 7mm;
    box-shadow: 0 10px 30px rgba(61,26,6,0.25);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* ===== HEADER SECTION ===== */
  .header-grid-daily {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #c7ad8d;
    padding-bottom: 6px;
    margin-bottom: 8px;
    gap: 12px;
  }

  .header-logo-wrapper-daily {
    width: 75px;
    height: 75px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .header-logo-daily {
    max-width: 75px;
    max-height: 75px;
    object-fit: contain;
  }

  .header-center-daily {
    flex: 1;
    text-align: center;
  }

  .arabic-bismillah {
    font-size: 20px;
    font-weight: bold;
    color: #3d1a06;
    font-family: 'Amiri', 'Traditional Arabic', serif;
    line-height: 1.1;
    margin-bottom: 2px;
  }

  .school-title-daily {
    font-size: 24px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    line-height: 1.1;
  }

  .school-subtitle-daily {
    font-size: 11px;
    color: #3d1a06;
    letter-spacing: 2px;
    font-weight: 700;
    text-transform: uppercase;
    margin-top: 2px;
  }

  /* ===== REPORT TITLE BANNER ===== */
  .report-title-banner {
    background: #3d1a06;
    color: #ffffff;
    text-align: center;
    padding: 6px 15px;
    border-radius: 4px;
    border: 1px solid #c7ad8d;
    margin: 4px 20px 8px;
    box-shadow: 0 2px 6px rgba(61,26,6,0.15);
  }

  .report-title-text {
    font-size: 17px;
    font-weight: 900;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: #ffffff;
  }

  /* ===== STUDENT PROFILE DATA CARD ===== */
  .student-profile-card {
    background: #ffffff;
    border: 1.5px solid #c7ad8d;
    border-radius: 8px;
    padding: 8px 12px;
    margin-bottom: 12px;
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .profile-grid-details {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px 14px;
    font-size: 12px;
  }

  .profile-item {
    display: flex;
    flex-direction: column;
  }

  .profile-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #3d1a06;
    letter-spacing: 0.5px;
    opacity: 0.8;
  }

  .profile-val {
    font-size: 12.5px;
    font-weight: 800;
    color: #000000;
  }

  .student-photo-wrapper {
    width: 65px;
    height: 75px;
    border: 1.5px solid #3d1a06;
    border-radius: 6px;
    overflow: hidden;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .student-photo-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* ===== STUDENT DEVELOPMENT TABLE (EXACT MATCH TO ATTACHED IMAGE) ===== */
  .development-table-wrapper {
    margin-bottom: 12px;
    border-radius: 14px;
    overflow: hidden;
    border: 2.5px solid #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -5px;
    box-shadow: 0 8px 25px rgba(61,26,6,0.12), 0 2px 6px rgba(199,173,141,0.25);
    background: #ffffff;
  }

  /* Header Box */
  .dev-table-header-box {
    background: linear-gradient(135deg, #3d1a06 0%, #240f03 100%);
    color: #ffffff;
    padding: 11px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    border-bottom: 2.5px solid #c7ad8d;
  }

  .dev-header-badge-icon {
    width: 46px;
    height: 46px;
    background: #ffffff;
    border: 2.5px solid #c7ad8d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 3px 8px rgba(0,0,0,0.25);
  }

  .dev-header-badge-icon svg {
    width: 26px;
    height: 26px;
    fill: #3d1a06;
  }

  .dev-header-title {
    font-size: 22px;
    font-weight: 900;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #ffffff;
  }

  .dev-header-title span {
    font-size: 17px;
    font-weight: 700;
    color: #c7ad8d;
  }

  /* Main Development Table */
  .dev-table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'Outfit', sans-serif;
    background: #ffffff;
  }

  .dev-table th {
    background: linear-gradient(135deg, #fdfaf3 0%, #f4ece1 100%);
    color: #3d1a06;
    padding: 11px 14px;
    font-size: 13.5px;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
    border-bottom: 2.5px solid #3d1a06;
    border-right: 1.5px solid #c7ad8d;
  }

  .dev-table th:last-child {
    border-right: none;
  }

  .dev-table td {
    padding: 11px 14px;
    border-bottom: 1.5px solid #ebdccb;
    border-right: 1.5px solid #c7ad8d;
    vertical-align: middle;
  }

  .dev-table tr:nth-child(even) {
    background: #faf5ec;
  }

  .dev-table tr:nth-child(odd) {
    background: #ffffff;
  }

  .dev-table tr:last-child td {
    border-bottom: none;
  }

  .dev-table td:last-child {
    border-right: none;
  }

  /* Area Column Details */
  .area-cell-content {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .cat-icon-box {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fdfaf3;
    border: 1.5px solid #c7ad8d;
    border-radius: 12px;
    padding: 4px;
    box-shadow: 0 3px 6px rgba(61,26,6,0.08);
  }

  .cat-icon-box svg {
    width: 42px;
    height: 42px;
  }

  .cat-text-wrap {
    display: flex;
    flex-direction: column;
  }

  .cat-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .cat-main-title {
    font-size: 16px;
    font-weight: 900;
    color: #1e1b18;
    line-height: 1.25;
  }

  .cat-star-badge {
    font-size: 10.5px;
    font-weight: 800;
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
    padding: 1.5px 8px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .cat-sub-details {
    font-size: 11.5px;
    font-weight: 600;
    color: #524840;
    margin-top: 3px;
    line-height: 1.2;
  }

  /* Marks Columns */
  .marks-max-cell, .marks-obtained-cell {
    text-align: center;
    width: 135px;
  }

  .marks-pill {
    display: inline-block;
    padding: 4px 16px;
    border-radius: 10px;
    font-size: 22px;
    font-weight: 900;
    min-width: 70px;
    box-shadow: 0 2px 5px rgba(61,26,6,0.08);
  }

  .max-pill {
    background: #fdfaf3;
    color: #3d1a06;
    border: 1.5px solid #c7ad8d;
  }

  .obtained-pill {
    background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%);
    color: #1e1b18;
    border: 2px solid #3d1a06;
  }

  /* Table Footer Row */
  .dev-table-footer-box {
    background: linear-gradient(135deg, #3d1a06 0%, #240f03 100%);
    color: #ffffff;
    padding: 9px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 2.5px solid #c7ad8d;
  }

  .dev-footer-title {
    flex: 1;
    text-align: center;
    font-size: 16.5px;
    font-weight: 900;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: #ffffff;
  }

  .dev-footer-total-badge {
    background: linear-gradient(135deg, #ffffff 0%, #fdfaf3 100%);
    color: #3d1a06;
    border: 2.5px solid #c7ad8d;
    border-radius: 12px;
    padding: 4px 24px;
    font-size: 27px;
    font-weight: 900;
    display: inline-flex;
    align-items: baseline;
    gap: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
  }

  .dev-footer-total-badge .slash-denom {
    font-size: 19px;
    font-weight: 800;
    color: #78350f;
  }

  /* ===== REMARKS & SIGNATURES SECTION ===== */
  .bottom-section-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 12px;
    margin-top: 4px;
  }

  .remarks-box-daily {
    background: #ffffff;
    border: 1.5px solid #c7ad8d;
    border-radius: 8px;
    padding: 8px 12px;
  }

  .remarks-header-title {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    color: #3d1a06;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 2px;
  }

  .remarks-body-text {
    font-size: 11.5px;
    color: #2d3748;
    line-height: 1.3;
    font-style: italic;
  }

  .signatures-card-daily {
    background: #ffffff;
    border: 1.5px solid #c7ad8d;
    border-radius: 8px;
    padding: 10px 12px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    text-align: center;
  }

  .sig-col-daily {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .sig-line-daily {
    width: 80%;
    border-top: 1.5px solid #3d1a06;
    margin-bottom: 4px;
  }

  .sig-title-daily {
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    color: #3d1a06;
    letter-spacing: 0.5px;
  }
</style>
</head>
<body>

<!-- SCREEN ACTION BAR -->
<div class="action-bar-daily no-print">
  <div class="status-text">
    🎓 Student Daily Development Report — {{ $student->full_name }}
  </div>
  <div class="btn-group-actions-daily">
    <button onclick="window.print()" class="btn-act-daily btn-print-daily">
      🖨️ Print Daily Report
    </button>
    <a href="{{ route('discipline.print', $discipline->id) }}" target="_blank" class="btn-act-daily btn-official-daily">
      📄 Official Certificate Report
    </a>
    <a href="{{ route('discipline.show', $discipline->id) }}" class="btn-act-daily btn-back-daily">
      ⬅️ Back to Web Profile
    </a>
  </div>
</div>

<!-- MAIN PRINT CONTAINER -->
<div class="daily-report-container">

  <div>
    <!-- HEADER ROW -->
    <div class="header-grid-daily">
      <div class="header-logo-wrapper-daily">
        @if(file_exists(public_path('assets/images/logo.png')))
          <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="header-logo-daily">
        @else
          <svg viewBox="0 0 100 100" class="header-logo-daily">
            <circle cx="50" cy="50" r="45" fill="#3d1a06" stroke="#c7ad8d" stroke-width="3"/>
            <path d="M50 20 L75 40 L75 70 L25 70 L25 40 Z" fill="none" stroke="#c7ad8d" stroke-width="4"/>
            <circle cx="50" cy="50" r="10" fill="#c7ad8d"/>
          </svg>
        @endif
      </div>

      <div class="header-center-daily">
        <div class="arabic-bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>
        <div class="school-title-daily">NOOR UL HUDA SUPERIOR SCHOOL</div>
        <div class="school-subtitle-daily">Excellence in Academic &amp; Islamic Character Building</div>
      </div>

      <div class="header-logo-wrapper-daily" style="visibility: hidden;">
        <!-- Placeholder for symmetric header alignment -->
      </div>
    </div>

    <!-- REPORT TITLE BANNER -->
    <div class="report-title-banner">
      <div class="report-title-text">STUDENT DAILY DEVELOPMENT REPORT</div>
    </div>

    <!-- STUDENT PROFILE SECTION -->
    <div class="student-profile-card">
      <div class="profile-grid-details">
        <div class="profile-item">
          <span class="profile-label">Student Name</span>
          <span class="profile-val">{{ $student->full_name }}</span>
        </div>
        <div class="profile-item">
          <span class="profile-label">Father's Name</span>
          <span class="profile-val">{{ $student->father_name ?? 'N/A' }}</span>
        </div>
        <div class="profile-item">
          <span class="profile-label">Admission No</span>
          <span class="profile-val">{{ $student->admission_no ?? 'N/A' }}</span>
        </div>
        <div class="profile-item">
          <span class="profile-label">Class &amp; Section</span>
          <span class="profile-val">{{ $student->class_name }} {{ $student->section_name ? "({$student->section_name})" : '' }}</span>
        </div>
        <div class="profile-item">
          <span class="profile-label">Roll Number</span>
          <span class="profile-val">{{ $student->roll_no ?? 'N/A' }}</span>
        </div>
        <div class="profile-item">
          <span class="profile-label">Date of Report</span>
          <span class="profile-val">{{ $discipline->entry_date ? $discipline->entry_date->format('d-M-Y') : date('d-M-Y') }}</span>
        </div>
        <div class="profile-item">
          <span class="profile-label">Date of Birth</span>
          <span class="profile-val">{{ $student->dob ? $student->dob->format('d-M-Y') : 'N/A' }}</span>
        </div>
        <div class="profile-item">
          <span class="profile-label">Gender</span>
          <span class="profile-val">{{ ucfirst($student->gender ?? 'N/A') }}</span>
        </div>
        <div class="profile-item">
          <span class="profile-label">Emergency Phone</span>
          <span class="profile-val">{{ $student->mobile_no ?? ($student->phone ?? 'N/A') }}</span>
        </div>
      </div>

      <div class="student-photo-wrapper">
        @if(!empty($student->photo) && file_exists(public_path($student->photo)))
          <img src="{{ asset($student->photo) }}" alt="{{ $student->full_name }}">
        @else
          <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="#3d1a06" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        @endif
      </div>
    </div>

    <!-- STUDENT DEVELOPMENT TABLE (100 MARKS) -->
    <div class="development-table-wrapper">
      <!-- Top Header Banner Box -->
      <div class="dev-table-header-box">
        <div class="dev-header-badge-icon">
          <!-- Person / Student Icon SVG -->
          <svg viewBox="0 0 24 24">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
          </svg>
        </div>
        <div class="dev-header-title">
          STUDENT DEVELOPMENT <span>(100 MARKS)</span>
        </div>
      </div>

      <!-- Main Table Content -->
      <table class="dev-table">
        <thead>
          <tr>
            <th style="text-align: left;">AREA</th>
            <th style="width: 135px; text-align: center;">MAX<br>MARKS</th>
            <th style="width: 135px; text-align: center;">OBTAINED<br>MARKS</th>
          </tr>
        </thead>
        <tbody>
          @foreach($developmentCategories as $cat)
            <tr>
              <!-- AREA COLUMN -->
              <td>
                <div class="area-cell-content">
                  <div class="cat-icon-box">
                    @if($cat['key'] === 'punctuality')
                      <!-- Clock / Attendance SVG -->
                      <svg viewBox="0 0 48 48" fill="none">
                        <circle cx="24" cy="24" r="18" fill="#FFFBEB" stroke="#3D1A06" stroke-width="3"/>
                        <path d="M24 12V24L30 30" stroke="#3D1A06" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    @elseif($cat['key'] === 'learning_skills')
                      <!-- Open Book SVG -->
                      <svg viewBox="0 0 48 48" fill="none">
                        <path d="M8 12C8 9.79086 9.79086 8 12 8H22V38H12C9.79086 38 8 36.2091 8 34V12Z" fill="#FFFBEB" stroke="#3D1A06" stroke-width="3" stroke-linejoin="round"/>
                        <path d="M40 12C40 9.79086 38.2091 8 36 8H26V38H36C38.2091 38 40 36.2091 40 34V12Z" fill="#FFFBEB" stroke="#3D1A06" stroke-width="3" stroke-linejoin="round"/>
                        <path d="M12 16H18" stroke="#3D1A06" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M12 22H18" stroke="#3D1A06" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M30 16H36" stroke="#3D1A06" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M30 22H36" stroke="#3D1A06" stroke-width="2.5" stroke-linecap="round"/>
                      </svg>
                    @elseif($cat['key'] === 'behaviour_character')
                      <!-- Two People SVG -->
                      <svg viewBox="0 0 48 48" fill="none">
                        <circle cx="18" cy="16" r="6" fill="#8B4513" stroke="#3D1A06" stroke-width="2"/>
                        <path d="M8 36C8 30.4772 12.4772 26 18 26C23.5228 26 28 30.4772 28 36" fill="#8B4513" stroke="#3D1A06" stroke-width="2.5"/>
                        <circle cx="32" cy="18" r="5" fill="#A0522D" stroke="#3D1A06" stroke-width="2"/>
                        <path d="M24 36C24 31.5817 27.5817 28 32 28C36.4183 28 40 31.5817 40 36" fill="#A0522D" stroke="#3D1A06" stroke-width="2"/>
                      </svg>
                    @elseif($cat['key'] === 'islamic_development')
                      <!-- Mosque SVG -->
                      <svg viewBox="0 0 48 48" fill="none">
                        <path d="M24 8C20 14 16 16 16 22H32C32 16 28 14 24 8Z" fill="#3D1A06" stroke="#3D1A06" stroke-width="2"/>
                        <path d="M12 22H36V40H12V22Z" fill="#FFFBEB" stroke="#3D1A06" stroke-width="2.5"/>
                        <path d="M20 40V30C20 27.7909 21.7909 26 24 26C26.2091 26 28 27.7909 28 30V40" fill="#3D1A06"/>
                        <path d="M8 18V40" stroke="#3D1A06" stroke-width="3" stroke-linecap="round"/>
                        <path d="M40 18V40" stroke="#3D1A06" stroke-width="3" stroke-linecap="round"/>
                        <path d="M24 4V8" stroke="#3D1A06" stroke-width="2"/>
                        <path d="M24 4C25 4 26 3 26 2" stroke="#3D1A06" stroke-width="1.5"/>
                      </svg>
                    @elseif($cat['key'] === 'co_curricular')
                      <!-- Trophy Cup SVG -->
                      <svg viewBox="0 0 48 48" fill="none">
                        <path d="M14 10H34V22C34 27.5228 29.5228 32 24 32C18.4772 32 14 27.5228 14 22V10Z" fill="#D97706" stroke="#3D1A06" stroke-width="2.5"/>
                        <path d="M24 32V40" stroke="#3D1A06" stroke-width="3"/>
                        <path d="M16 40H32" stroke="#3D1A06" stroke-width="3" stroke-linecap="round"/>
                        <path d="M14 14H8C6.89543 14 6 14.8954 6 16V18C6 21.3137 8.68629 24 12 24H14" stroke="#3D1A06" stroke-width="2.5"/>
                        <path d="M34 14H40C41.1046 14 42 14.8954 42 16V18C42 21.3137 39.3137 24 36 24H34" stroke="#3D1A06" stroke-width="2.5"/>
                        <polygon points="24,14 26,18 30,18 27,21 28,25 24,22 20,25 21,21 18,18 22,18" fill="#FFFBEB"/>
                      </svg>
                    @else
                      <!-- Polo Shirt / Uniform SVG -->
                      <svg viewBox="0 0 48 48" fill="none">
                        <path d="M14 12L20 8H28L34 12L42 16L38 24L34 22V40H14V22L10 24L6 16L14 12Z" fill="#C7AD8D" stroke="#3D1A06" stroke-width="2.5" stroke-linejoin="round"/>
                        <path d="M20 8L24 14L28 8" stroke="#3D1A06" stroke-width="2"/>
                        <path d="M24 14V22" stroke="#3D1A06" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="24" cy="17" r="1" fill="#3D1A06"/>
                      </svg>
                    @endif
                  </div>

                  <div class="cat-text-wrap">
                    <div class="cat-title-row">
                      <div class="cat-main-title">{{ $cat['number'] }}. {{ $cat['title'] }}</div>
                      @if(isset($cat['star_rating']))
                        <span class="cat-star-badge">
                          ★ {{ number_format($cat['star_rating'], 1) }} / 5.0
                        </span>
                      @endif
                    </div>
                    <div class="cat-sub-details">{{ $cat['sub'] }}</div>
                  </div>
                </div>
              </td>

              <!-- MAX MARKS -->
              <td class="marks-max-cell">
                <span class="marks-pill max-pill">{{ $cat['max_marks'] }}</span>
              </td>

              <!-- OBTAINED MARKS -->
              <td class="marks-obtained-cell">
                <span class="marks-pill obtained-pill">{{ $cat['obtained_marks'] }}</span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Bottom Footer Total Box -->
      <div class="dev-table-footer-box">
        <div class="dev-footer-title">STUDENT DEVELOPMENT TOTAL</div>
        <div class="dev-footer-total-badge">
          {{ $calculatedTotalObtained }} <span class="slash-denom">/100</span>
        </div>
      </div>
    </div>

  </div>

  <!-- REMARKS & SIGNATURES SECTION -->
  <div class="bottom-section-grid">
    <div class="remarks-box-daily">
      <div class="remarks-header-title">Teacher Remarks / Conduct Notes</div>
      <div class="remarks-body-text">
        {{ $discipline->remarks ?: 'Demonstrates active participation and good adherence to school discipline policies.' }}
      </div>
    </div>

    <div class="signatures-card-daily">
      <div class="sig-col-daily">
        <div class="sig-line-daily"></div>
        <div class="sig-title-daily">Class Teacher</div>
      </div>
      <div class="sig-col-daily">
        <div class="sig-line-daily"></div>
        <div class="sig-title-daily">Parent / Guardian</div>
      </div>
      <div class="sig-col-daily">
        <div class="sig-line-daily"></div>
        <div class="sig-title-daily">Principal</div>
      </div>
    </div>
  </div>

</div>

</body>
</html>
