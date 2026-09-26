<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
  $isBlank = $isBlank ?? false;
  $titleName = $isBlank ? 'Blank Form' : ($admission->student_name ?: 'Admission Form');
  $admNo = !$isBlank && $admission->admission_no ? $admission->admission_no : '________________';
  $rollNo = !$isBlank && $admission->roll_no ? $admission->roll_no : '________________';
  $admDate = !$isBlank && $admission->admission_date ? optional($admission->admission_date)->format('d/m/Y') : '____/____/________';
  $sessionName = !$isBlank ? (optional($admission->academicSession)->session_name ?: ($admission->academic_session ?: '2026-2027')) : '________________';
@endphp
<title>Student Admission Form — {{ $titleName }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@700&display=swap" rel="stylesheet">
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
    font-family: 'Times New Roman', 'Georgia', serif;
    color: #3d1a06;
    line-height: 1.25;
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

  /* ===== TOP ACTION BAR (SCREEN ONLY) ===== */
  .action-bar {
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

  .action-bar .status-text {
    color: #fdfaf3;
    font-size: 13px;
    font-weight: bold;
    font-family: Arial, sans-serif;
  }

  .btn-group-actions {
    display: flex;
    gap: 8px;
  }

  .btn-act {
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

  .btn-print { background: #22c55e; color: #ffffff; }
  .btn-print:hover { background: #16a34a; }
  .btn-back { background: #c7ad8d; color: #3d1a06; }
  .btn-back:hover { background: #b89b78; }

  /* ===== MAIN A4 CONTAINER ===== */
  .form-container {
    width: 210mm;
    margin: 12px auto 20px;
    background: #fdfaf3;
    border: 3px double #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -5px;
    padding: 5mm 6mm;
    box-shadow: 0 10px 30px rgba(61,26,6,0.25);
    position: relative;
    overflow: hidden;
  }

  /* ===== HEADER SECTION ===== */
  .header-grid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #c7ad8d;
    padding-bottom: 5px;
    margin-bottom: 5px;
    gap: 10px;
  }

  .header-logo-wrapper {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .header-logo {
    max-width: 70px;
    max-height: 70px;
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
    font-size: 19px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1px;
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

  .form-title-pill {
    font-size: 13px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1.5px;
    margin-top: 2px;
    text-transform: uppercase;
  }

  .session-subtitle {
    font-size: 9.5px;
    font-weight: bold;
    color: #3d1a06;
    letter-spacing: 0.8px;
    font-family: Arial, sans-serif;
    margin-top: 1px;
  }

  .header-right-meta {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    flex-shrink: 0;
  }

  .meta-lines {
    font-size: 9px;
    font-family: Arial, sans-serif;
    color: #3d1a06;
    line-height: 1.45;
  }

  .meta-line-item {
    border-bottom: 1px dotted #c7ad8d;
    padding-bottom: 1px;
    margin-bottom: 2px;
  }

  .meta-line-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
  }

  .meta-label {
    font-weight: bold;
  }

  .photo-box {
    width: 65px;
    height: 75px;
    border: 1.5px dashed #c7ad8d;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 8px;
    font-family: Arial, sans-serif;
    color: #3d1a06;
    font-weight: bold;
    overflow: hidden;
    flex-shrink: 0;
  }

  .photo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* ===== SECTION HEADERS ===== */
  .section-bar {
    background: #3d1a06;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 900;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 3px 8px;
    font-family: Arial, sans-serif;
    margin-top: 4px;
    margin-bottom: 2px;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    border: 1px solid #3d1a06;
  }

  /* ===== TABLES ===== */
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9.5px;
    margin-bottom: 2px;
    background: #ffffff;
    border: 1px solid #c7ad8d;
  }

  table.data-table th {
    background: #3d1a06;
    color: #ffffff;
    font-size: 8.5px;
    font-family: Arial, sans-serif;
    font-weight: bold;
    padding: 2.5px 6px;
    text-align: left;
    text-transform: uppercase;
    border: 1px solid #3d1a06;
  }

  table.data-table td {
    padding: 2.5px 6px;
    border: 1px solid #c7ad8d;
    vertical-align: middle;
  }

  .td-lbl {
    font-weight: bold;
    color: #3d1a06;
    background: #fdfaf3;
    font-family: Arial, sans-serif;
    font-size: 8.5px;
  }

  .td-val {
    color: #000000;
    font-weight: 600;
  }

  /* Checkbox boxes for print */
  .chk-box {
    display: inline-block;
    width: 10px;
    height: 10px;
    border: 1px solid #3d1a06;
    text-align: center;
    line-height: 8px;
    font-size: 8px;
    font-weight: bold;
    margin-right: 3px;
    vertical-align: middle;
    background: #ffffff;
  }

  .chk-active {
    background: #3d1a06;
    color: #ffffff;
  }

  /* Date Boxes Grid */
  .date-box-grid {
    display: inline-flex;
    gap: 1.5px;
    vertical-align: middle;
  }
  .date-char {
    width: 11px;
    height: 13px;
    border: 1px solid #3d1a06;
    text-align: center;
    line-height: 11px;
    font-size: 8.5px;
    font-family: monospace;
    font-weight: bold;
    background: #ffffff;
  }

  /* ===== TWO COLUMN SIDE-BY-SIDE LAYOUT ===== */
  .two-col-row {
    display: flex;
    gap: 6px;
    margin-bottom: 2px;
  }
  .two-col-item {
    flex: 1;
  }

  /* ===== SIGNATURES SECTION ===== */
  .signatures-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 8px;
    padding-top: 2px;
    text-align: center;
  }

  .sig-col {
    flex: 1;
    padding: 0 6px;
  }

  .sig-line-bar {
    border-top: 1.5px solid #3d1a06;
    margin-bottom: 3px;
  }

  .sig-title-text {
    font-size: 9.5px;
    font-weight: bold;
    color: #3d1a06;
    font-family: Arial, sans-serif;
  }

  .footer-notice {
    text-align: center;
    font-size: 8px;
    font-family: Arial, sans-serif;
    font-style: italic;
    color: #3d1a06;
    margin-top: 4px;
    border-top: 1px solid #c7ad8d;
    padding-top: 2px;
  }
</style>
</head>
<body>

<!-- Screen Top Action Bar -->
<div class="action-bar no-print">
  <div class="status-text">
    @if($isBlank)
      📄 Blank Student Admission Form (Ready for Printing)
    @else
      ✓ Student Admission Saved — Official Admission Form Ready
    @endif
  </div>
  <div class="btn-group-actions">
    <button onclick="window.print()" class="btn-act btn-print">
      🖨️ Print {{ $isBlank ? 'Blank Form' : 'Admission Form' }}
    </button>
    @if(!$isBlank && isset($admission->id))
      <a href="{{ route('admission.show', $admission) }}" class="btn-act btn-back">
        👤 View Student Profile
      </a>
    @endif
    <a href="{{ route('admission.index') }}" class="btn-act btn-back">
      📋 Admission Dashboard
    </a>
    <a href="{{ route('admission.create') }}" class="btn-act btn-back">
      ➕ New Admission
    </a>
  </div>
</div>

<div class="form-container">

  <!-- ===== HEADER ===== -->
  <div class="header-grid">
    <div class="header-logo-wrapper">
      <img src="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" alt="School Logo" class="header-logo" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
    </div>

    <div class="header-center">
      <div class="bismillah">رَّبِّ زِدْنِي عِلْمًا</div>
      <div class="school-name">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}</div>
      <div class="school-tagline">{{ $globalSchoolInfo->tagline ?? 'DISCIPLINE | EDUCATION | EXCELLENCE' }}</div>
      <div class="form-title-pill">STUDENT ADMISSION FORM</div>
      <div class="session-subtitle">ACADEMIC SESSION {{ $sessionName }}</div>
    </div>

    <div class="header-right-meta">
      <div class="meta-lines">
        <div class="meta-line-item">
          <span class="meta-label">Adm No:</span> {{ $admNo }}
        </div>
        <div class="meta-line-item">
          <span class="meta-label">Roll No:</span> {{ $rollNo }}
        </div>
        <div class="meta-line-item">
          <span class="meta-label">Adm Date:</span> {{ $admDate }}
        </div>
      </div>

      <div class="photo-box">
        @if(!$isBlank && $admission->student_photo)
          <img src="{{ asset('storage/' . $admission->student_photo) }}" alt="Photo">
        @else
          STUDENT<br>PHOTO
        @endif
      </div>
    </div>
  </div>

  <!-- ===== 1. STUDENT INFORMATION ===== -->
  <div class="section-bar">1. STUDENT INFORMATION</div>
  <table class="data-table">
    <tr>
      <td class="td-lbl" style="width: 14%;">Student Name</td>
      <td class="td-val" style="width: 36%;">{{ !$isBlank ? $admission->student_name : '____________________________________' }}</td>
      <td class="td-lbl" style="width: 14%;">Date of Birth</td>
      <td class="td-val" style="width: 36%;">
        @php
          $dobStr = (!$isBlank && $admission->date_of_birth) ? optional($admission->date_of_birth)->format('dmY') : '        ';
          $d1 = substr($dobStr, 0, 1); $d2 = substr($dobStr, 1, 1);
          $m1 = substr($dobStr, 2, 1); $m2 = substr($dobStr, 3, 1);
          $y1 = substr($dobStr, 4, 1); $y2 = substr($dobStr, 5, 1); $y3 = substr($dobStr, 6, 1); $y4 = substr($dobStr, 7, 1);
        @endphp
        <span class="date-box-grid">
          <span class="date-char">{{ $d1 ?: 'D' }}</span><span class="date-char">{{ $d2 ?: 'D' }}</span>
          <span style="font-weight:bold;">/</span>
          <span class="date-char">{{ $m1 ?: 'M' }}</span><span class="date-char">{{ $m2 ?: 'M' }}</span>
          <span style="font-weight:bold;">/</span>
          <span class="date-char">{{ $y1 ?: 'Y' }}</span><span class="date-char">{{ $y2 ?: 'Y' }}</span><span class="date-char">{{ $y3 ?: 'Y' }}</span><span class="date-char">{{ $y4 ?: 'Y' }}</span>
        </span>
        @if(!$isBlank && $admission->date_of_birth)
          <span style="margin-left: 4px; font-size: 8.5px; color:#555;">({{ optional($admission->date_of_birth)->format('d M, Y') }})</span>
        @endif
      </td>
    </tr>
    <tr>
      <td class="td-lbl">Father Name</td>
      <td class="td-val">{{ !$isBlank ? $admission->father_name : '____________________________________' }}</td>
      <td class="td-lbl">Gender</td>
      <td class="td-val">
        @php $g = !$isBlank ? strtolower($admission->gender) : ''; @endphp
        <span class="chk-box {{ $g === 'male' ? 'chk-active' : '' }}">{{ $g === 'male' ? '✓' : '' }}</span> Male &nbsp;&nbsp;
        <span class="chk-box {{ $g === 'female' ? 'chk-active' : '' }}">{{ $g === 'female' ? '✓' : '' }}</span> Female &nbsp;&nbsp;
        <span class="chk-box {{ $g && !in_array($g, ['male','female']) ? 'chk-active' : '' }}">{{ $g && !in_array($g, ['male','female']) ? '✓' : '' }}</span> Other
      </td>
    </tr>
    <tr>
      <td class="td-lbl">B-Form / CNIC</td>
      <td class="td-val">{{ !$isBlank ? ($admission->cnic_bform ?: 'N/A') : '____________________________________' }}</td>
      <td class="td-lbl">Religion</td>
      <td class="td-val">{{ !$isBlank ? ($admission->religion ?: 'Islam') : '____________________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Blood Group</td>
      <td class="td-val">{{ !$isBlank ? ($admission->blood_group ?: 'N/A') : '________________' }}</td>
      <td class="td-lbl">Nationality</td>
      <td class="td-val">{{ !$isBlank ? ($admission->nationality ?: 'Pakistani') : '____________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Student Mobile</td>
      <td class="td-val">{{ !$isBlank ? ($admission->student_mobile_no ?: 'N/A') : '________________________' }}</td>
      <td class="td-lbl">Student Email</td>
      <td class="td-val">{{ !$isBlank ? ($admission->student_email ?: 'N/A') : '________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Previous School</td>
      <td class="td-val" colspan="3">{{ !$isBlank ? ($admission->previous_school ?: 'N/A') : '____________________________________________________________________________________' }}</td>
    </tr>
  </table>

  <!-- ===== 2. ACADEMIC INFORMATION ===== -->
  <div class="section-bar">2. ACADEMIC INFORMATION</div>
  <table class="data-table">
    <tr>
      <td class="td-lbl" style="width: 14%;">Academic Session</td>
      <td class="td-val" style="width: 36%;">{{ $sessionName }}</td>
      <td class="td-lbl" style="width: 14%;">Class Applying For</td>
      <td class="td-val" style="width: 36%;">{{ !$isBlank ? $admission->class_name : '________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Section</td>
      <td class="td-val">{{ !$isBlank ? strtoupper($admission->section_name ?: 'A') : '________' }}</td>
      <td class="td-lbl">Academic Group</td>
      <td class="td-val">{{ !$isBlank ? ($admission->group_name ?: ($admission->group ?: 'General')) : '________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Admission Type</td>
      <td class="td-val">
        @php $at = !$isBlank ? strtolower($admission->admission_type ?: '') : ''; @endphp
        <span class="chk-box {{ in_array($at, ['new', 'new_admission', 'regular']) ? 'chk-active' : '' }}">{{ in_array($at, ['new', 'new_admission', 'regular']) ? '✓' : '' }}</span> New &nbsp;
        <span class="chk-box {{ $at === 'transfer' ? 'chk-active' : '' }}">{{ $at === 'transfer' ? '✓' : '' }}</span> Transfer &nbsp;
        <span class="chk-box {{ in_array($at, ['re_admission', 'readmission']) ? 'chk-active' : '' }}">{{ in_array($at, ['re_admission', 'readmission']) ? '✓' : '' }}</span> Re-Admission
      </td>
      <td class="td-lbl">Roll No.</td>
      <td class="td-val">{{ !$isBlank ? ($admission->roll_no ?: 'Unassigned') : '________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Class Shift</td>
      <td class="td-val">{{ !$isBlank ? ucfirst($admission->class_shift ?: 'Morning') : '________________________' }}</td>
      <td class="td-lbl">Fee Plan</td>
      <td class="td-val">{{ !$isBlank ? ($admission->fee_plan ?: 'Standard Plan') : '________________________' }}</td>
    </tr>
  </table>

  <!-- ===== 3. PARENT / GUARDIAN INFORMATION ===== -->
  <div class="section-bar">3. PARENT / GUARDIAN INFORMATION</div>
  <table class="data-table">
    <tr>
      <th colspan="2" style="width: 50%;">FATHER INFORMATION</th>
      <th colspan="2" style="width: 50%;">MOTHER INFORMATION</th>
    </tr>
    <tr>
      <td class="td-lbl" style="width: 14%;">Father Name</td>
      <td class="td-val" style="width: 36%;">{{ !$isBlank ? $admission->father_name : '________________________' }}</td>
      <td class="td-lbl" style="width: 14%;">Mother Name</td>
      <td class="td-val" style="width: 36%;">{{ !$isBlank ? ($admission->mother_name ?: 'N/A') : '________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Father CNIC</td>
      <td class="td-val">{{ !$isBlank ? ($admission->father_cnic ?: ($admission->guardian_cnic ?: 'N/A')) : '________________________' }}</td>
      <td class="td-lbl">Mother CNIC</td>
      <td class="td-val">{{ !$isBlank ? ($admission->mother_cnic ?: 'N/A') : '________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Father Phone</td>
      <td class="td-val">{{ !$isBlank ? ($admission->father_phone ?: ($admission->guardian_primary_mobile_no ?: 'N/A')) : '________________________' }}</td>
      <td class="td-lbl">Mother Phone</td>
      <td class="td-val">{{ !$isBlank ? ($admission->mother_phone ?: 'N/A') : '________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Father Occupation</td>
      <td class="td-val">{{ !$isBlank ? ($admission->father_occupation ?: ($admission->guardian_occupation ?: 'N/A')) : '________________________' }}</td>
      <td class="td-lbl">Mother Occupation</td>
      <td class="td-val">{{ !$isBlank ? ($admission->mother_occupation ?: 'N/A') : '________________________' }}</td>
    </tr>
    <tr>
      <th colspan="4">GUARDIAN INFORMATION (If different from Father)</th>
    </tr>
    <tr>
      <td class="td-lbl">Guardian Name</td>
      <td class="td-val">{{ !$isBlank ? ($admission->guardian_name ?: 'N/A') : '________________________' }}</td>
      <td class="td-lbl">Relation / CNIC</td>
      <td class="td-val">
        @if(!$isBlank && $admission->guardian_name)
          {{ ucfirst($admission->guardian_relation ?: 'Guardian') }} @if($admission->guardian_cnic) ({{ $admission->guardian_cnic }}) @endif
        @else
          ________________________
        @endif
      </td>
    </tr>
  </table>

  <!-- ===== 4. CONTACT INFORMATION ===== -->
  <div class="section-bar">4. CONTACT INFORMATION</div>
  <table class="data-table">
    <tr>
      <td class="td-lbl" style="width: 14%;">Residential Address</td>
      <td class="td-val" colspan="3">{{ !$isBlank ? ($admission->current_address ?: ($admission->guardian_address ?: 'N/A')) : '____________________________________________________________________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Permanent Address</td>
      <td class="td-val" colspan="3">{{ !$isBlank ? ($admission->permanent_address ?: ($admission->current_address ?: ($admission->guardian_address ?: 'N/A'))) : '____________________________________________________________________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Emergency Contact</td>
      <td class="td-val" style="width: 36%;">
        @if(!$isBlank && $admission->emergency_contact_name)
          {{ $admission->emergency_contact_name }} @if($admission->emergency_contact_relation) ({{ $admission->emergency_contact_relation }}) @endif
        @else
          {{ !$isBlank ? 'N/A' : '________________________' }}
        @endif
      </td>
      <td class="td-lbl" style="width: 14%;">Emergency Phone</td>
      <td class="td-val" style="width: 36%;">{{ !$isBlank ? ($admission->emergency_contact_mobile_no ?: 'N/A') : '________________________' }}</td>
    </tr>
  </table>

  <!-- ===== 5 & 6. DOCUMENTS & FEE INFORMATION (SIDE BY SIDE) ===== -->
  <div class="two-col-row">
    <!-- 5. DOCUMENTS CHECKLIST -->
    <div class="two-col-item">
      <div class="section-bar">5. DOCUMENTS CHECKLIST</div>
      <table class="data-table">
        @php
          $bform  = !$isBlank && strtolower((string)$admission->bform_cnic_copy) === 'submitted';
          $gcnic  = !$isBlank && strtolower((string)$admission->guardian_cnic_copy) === 'submitted';
          $birthc = !$isBlank && strtolower((string)$admission->birth_certificate) === 'submitted';
          $slc    = !$isBlank && strtolower((string)$admission->school_leaving_certificate) === 'submitted';
          $photo  = !$isBlank && (bool)$admission->student_photo;
        @endphp
        <tr>
          <td><span class="chk-box {{ $bform ? 'chk-active' : '' }}">{{ $bform ? '✓' : '' }}</span> Student B-Form / CNIC Copy</td>
        </tr>
        <tr>
          <td><span class="chk-box {{ $gcnic ? 'chk-active' : '' }}">{{ $gcnic ? '✓' : '' }}</span> Father / Guardian CNIC Copy</td>
        </tr>
        <tr>
          <td><span class="chk-box {{ $birthc ? 'chk-active' : '' }}">{{ $birthc ? '✓' : '' }}</span> Birth Certificate Copy</td>
        </tr>
        <tr>
          <td><span class="chk-box {{ $slc ? 'chk-active' : '' }}">{{ $slc ? '✓' : '' }}</span> School Leaving Certificate (SLC)</td>
        </tr>
        <tr>
          <td><span class="chk-box {{ $photo ? 'chk-active' : '' }}">{{ $photo ? '✓' : '' }}</span> Student Photographs (2 Copies)</td>
        </tr>
      </table>
    </div>

    <!-- 6. FEE / ADMISSION INFORMATION -->
    <div class="two-col-item">
      <div class="section-bar">6. FEE / ADMISSION INFORMATION</div>
      @php
        $mFee = !$isBlank ? ($admission->monthly_fee ?? 0) : null;
        $rFee = !$isBlank ? ($admission->registration_fee ?? 0) : null;
        $disc = !$isBlank ? ($admission->scholarship_discount ?? 0) : null;
        $payable = !$isBlank ? max((($mFee ?: 0) + ($rFee ?: 0)) - ($disc ?: 0), 0) : null;
        $feeStat = !$isBlank ? strtolower($admission->fee_status ?? 'pending') : '';
      @endphp
      <table class="data-table">
        <tr>
          <td class="td-lbl" style="width: 55%;">Registration / Admission Fee</td>
          <td class="td-val" style="width: 45%; text-align: right;">{{ $rFee !== null ? 'Rs. ' . number_format($rFee) : 'Rs. __________' }}</td>
        </tr>
        <tr>
          <td class="td-lbl">Monthly Tuition Fee</td>
          <td class="td-val" style="text-align: right;">{{ $mFee !== null ? 'Rs. ' . number_format($mFee) : 'Rs. __________' }}</td>
        </tr>
        <tr>
          <td class="td-lbl">Scholarship / Discount</td>
          <td class="td-val" style="text-align: right; color: #16a34a;">{{ $disc !== null ? '-Rs. ' . number_format($disc) : 'Rs. __________' }}</td>
        </tr>
        <tr>
          <td class="td-lbl" style="font-weight:900; background:#3d1a06; color:#ffffff;">Total Payable Net Fee</td>
          <td class="td-val" style="font-weight:900; text-align:right; background:#fdfaf3; color:#3d1a06;">{{ $payable !== null ? 'Rs. ' . number_format($payable) : 'Rs. __________' }}</td>
        </tr>
        <tr>
          <td class="td-lbl">Fee Payment Status</td>
          <td class="td-val">
            <span class="chk-box {{ $feeStat === 'paid' ? 'chk-active' : '' }}">{{ $feeStat === 'paid' ? '✓' : '' }}</span> Paid &nbsp;
            <span class="chk-box {{ $feeStat === 'partial' ? 'chk-active' : '' }}">{{ $feeStat === 'partial' ? '✓' : '' }}</span> Partial &nbsp;
            <span class="chk-box {{ $feeStat === 'pending' ? 'chk-active' : '' }}">{{ $feeStat === 'pending' ? '✓' : '' }}</span> Pending
          </td>
        </tr>
      </table>
    </div>
  </div>

  <!-- ===== 7. ADDITIONAL INFORMATION ===== -->
  <div class="section-bar">7. ADDITIONAL INFORMATION</div>
  <table class="data-table">
    <tr>
      <td class="td-lbl" style="width: 14%;">Transport Required</td>
      <td class="td-val" style="width: 36%;">
        @php $tr = !$isBlank ? strtolower($admission->transportation_required ?: 'no') : ''; @endphp
        <span class="chk-box {{ in_array($tr, ['yes','1','required']) ? 'chk-active' : '' }}">{{ in_array($tr, ['yes','1','required']) ? '✓' : '' }}</span> Yes &nbsp;&nbsp;
        <span class="chk-box {{ $tr && !in_array($tr, ['yes','1','required']) ? 'chk-active' : '' }}">{{ $tr && !in_array($tr, ['yes','1','required']) ? '✓' : '' }}</span> No
      </td>
      <td class="td-lbl" style="width: 14%;">Hostel Required</td>
      <td class="td-val" style="width: 36%;">
        @php $host = !$isBlank ? strtolower($admission->hostel ?: '') : ''; @endphp
        <span class="chk-box {{ in_array($host, ['yes','1']) ? 'chk-active' : '' }}">{{ in_array($host, ['yes','1']) ? '✓' : '' }}</span> Yes &nbsp;&nbsp;
        <span class="chk-box {{ in_array($host, ['no','0']) ? 'chk-active' : '' }}">{{ in_array($host, ['no','0']) ? '✓' : '' }}</span> No
      </td>
    </tr>
    <tr>
      <td class="td-lbl">Medical Notes / Allergies</td>
      <td class="td-val" colspan="3">{{ !$isBlank ? ($admission->student_medical_notes ?: 'None reported') : '____________________________________________________________________________________' }}</td>
    </tr>
    <tr>
      <td class="td-lbl">Special Notes / Remarks</td>
      <td class="td-val" colspan="3">{{ !$isBlank ? ($admission->academic_notes ?: 'Regular Admission') : '____________________________________________________________________________________' }}</td>
    </tr>
  </table>

  <!-- ===== SIGNATURES ===== -->
  <div class="signatures-row">
    <div class="sig-col">
      <div class="sig-line-bar"></div>
      <div class="sig-title-text">Parent / Guardian Signature</div>
    </div>
    <div class="sig-col">
      <div class="sig-line-bar"></div>
      <div class="sig-title-text">Admission Incharge Signature</div>
    </div>
    <div class="sig-col">
      <div class="sig-line-bar"></div>
      <div class="sig-title-text">Principal Signature &amp; Stamp</div>
    </div>
  </div>

  <div class="footer-notice">
    Note: Please attach all required documents with this form. Incomplete forms will not be processed. &bull; Official Computer Generated Admission Form
  </div>

</div>

</body>
</html>
