<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Official Progress Report Card — {{ $admission->student_name }} ({{ $admission->admission_no }})</title>
<style>
  /* ===== RESET & PRINT SETUP ===== */
  * { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { width: 100%; min-height: 100%; }

  body {
    font-family: 'Times New Roman', 'Georgia', serif;
    background: #e8dfd1;
    color: #3d1a06;
    line-height: 1.3;
  }

  @page {
    size: A4 portrait;
    margin: 10mm 10mm;
  }

  @media print {
    body { background: #fdfaf3 !important; }
    .no-print { display: none !important; }
    .report-card-container {
      box-shadow: none !important;
      margin: 0 auto !important;
      width: 100% !important;
      padding: 10px !important;
      border: 2px solid #c7ad8d !important;
      background: #fdfaf3 !important;
    }
  }

  /* ===== MAIN CONTAINER ===== */
  .report-card-container {
    width: 210mm;
    min-height: 297mm;
    margin: 20px auto;
    background: #fdfaf3;
    color: #3d1a06;
    border: 3px double #3d1a06;
    outline: 2px solid #c7ad8d;
    outline-offset: -5px;
    padding: 20px 24px;
    box-shadow: 0 10px 30px rgba(61,26,6,0.15);
    position: relative;
  }

  /* ===== TOP ACTION BAR (SCREEN ONLY) ===== */
  .action-bar {
    width: 210mm;
    margin: 15px auto 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #3d1a06;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

  .action-bar .status-text {
    color: #fdfaf3;
    font-size: 14px;
    font-family: Arial, sans-serif;
    font-weight: bold;
  }

  .btn-act {
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 13px;
    font-family: Arial, sans-serif;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.2s;
  }

  .btn-print { background: #22c55e; color: #ffffff; }
  .btn-print:hover { background: #16a34a; }
  .btn-back { background: #c7ad8d; color: #3d1a06; }
  .btn-back:hover { background: #b09575; }

  /* ===== HEADER SECTION ===== */
  .header-grid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #3d1a06;
    padding-bottom: 12px;
    margin-bottom: 15px;
  }

  .header-logo {
    width: 90px;
    height: 90px;
    object-fit: contain;
  }

  .header-center {
    flex: 1;
    text-align: center;
    padding: 0 10px;
  }

  .school-name {
    font-size: 24px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1px;
    text-transform: uppercase;
  }

  .school-tagline {
    font-size: 10px;
    font-weight: bold;
    color: #6b4423;
    letter-spacing: 2px;
    margin-top: 2px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
  }

  .report-title-banner {
    background: #3d1a06;
    color: #fdfaf3;
    font-size: 15px;
    font-weight: 900;
    letter-spacing: 2px;
    margin-top: 8px;
    padding: 4px 12px;
    border-radius: 4px;
    display: inline-block;
    text-transform: uppercase;
  }

  /* ===== STUDENT INFO BOX ===== */
  .info-box {
    border: 1px solid #c7ad8d;
    background: #fdfaf3;
    border-radius: 6px;
    padding: 10px 14px;
    margin-bottom: 15px;
    font-family: Arial, sans-serif;
    font-size: 12px;
  }

  .info-table {
    width: 100%;
    border-collapse: collapse;
  }

  .info-table td {
    padding: 5px 6px;
    vertical-align: middle;
    border-bottom: 1px dashed #e4d5c3;
  }

  .info-table tr:last-child td {
    border-bottom: none;
  }

  .lbl {
    font-weight: bold;
    color: #3d1a06;
    width: 15%;
  }

  .val {
    color: #3d1a06;
    font-weight: 600;
    width: 35%;
  }

  /* ===== SECTION HEADING BANNER ===== */
  .section-banner {
    background: #3d1a06;
    color: #fdfaf3;
    font-weight: bold;
    font-size: 12px;
    padding: 6px 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-family: Arial, sans-serif;
    border-radius: 4px 4px 0 0;
  }

  /* ===== MARKS TABLE ===== */
  table.marks-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    margin-bottom: 15px;
    font-family: Arial, sans-serif;
    border: 1px solid #c7ad8d;
  }

  table.marks-table th {
    background: #3d1a06;
    color: #fdfaf3;
    font-weight: bold;
    padding: 8px 10px;
    text-align: center;
    border: 1px solid #c7ad8d;
    text-transform: uppercase;
    font-size: 11px;
  }

  table.marks-table td {
    padding: 8px 10px;
    border: 1px solid #c7ad8d;
    text-align: center;
    vertical-align: middle;
    color: #3d1a06;
    background: #fdfaf3;
  }

  .td-left { text-align: left !important; }

  /* ===== SUMMARY STATS BOX ===== */
  .summary-grid {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    font-family: Arial, sans-serif;
  }

  .sum-card {
    flex: 1;
    border: 1px solid #c7ad8d;
    background: #fdfaf3;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
  }

  .sum-card .sum-lbl {
    font-size: 10px;
    font-weight: bold;
    color: #6b4423;
    text-transform: uppercase;
  }

  .sum-card .sum-val {
    font-size: 18px;
    font-weight: 900;
    color: #3d1a06;
    margin-top: 2px;
  }

  /* ===== REMARKS & SIGNATURES ===== */
  .remarks-box {
    border: 1px solid #c7ad8d;
    padding: 10px 14px;
    border-radius: 6px;
    margin-bottom: 30px;
    font-family: Arial, sans-serif;
    font-size: 11px;
    background: #fdfaf3;
    color: #3d1a06;
  }

  .remarks-box strong {
    color: #3d1a06;
  }

  .signatures-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 40px;
    padding-top: 10px;
    text-align: center;
    font-family: Arial, sans-serif;
  }

  .sig-col {
    flex: 1;
    padding: 0 15px;
  }

  .sig-line {
    border-top: 1.5px solid #3d1a06;
    margin-bottom: 4px;
  }

  .sig-title {
    font-size: 11px;
    font-weight: bold;
    color: #3d1a06;
  }

  .footer-note {
    text-align: center;
    font-size: 9px;
    font-family: Arial, sans-serif;
    color: #6b4423;
    margin-top: 20px;
    border-top: 1px dashed #c7ad8d;
    padding-top: 6px;
  }
</style>
</head>
<body>

<!-- Screen Top Action Bar -->
<div class="action-bar no-print">
  <div class="status-text">
    📄 Official Student Progress Report Card / Marksheet
  </div>
  <div class="btn-group-actions d-flex gap-2">
    <button onclick="window.print()" class="btn-act btn-print">
      🖨️ Print Marksheet
    </button>
    <a href="{{ route('examination.show', $selectedExam->id ?? 1) }}" class="btn-act btn-back">
      📋 Back to Exam
    </a>
  </div>
</div>

<div class="report-card-container">

  <!-- ===== HEADER ===== -->
  <div class="header-grid">
    <img src="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" alt="School Logo" class="header-logo">

    <div class="header-center">
      <div style="font-size: 16px; font-weight: bold; font-family: 'Amiri', serif; color: #3d1a06;">رَّبِّ زِدْنِي عِلْمًا</div>
      <div class="school-name">{{ $globalSchoolInfo->school_name ?? 'NOOR UL HUDA SUPERIOR SCHOOL' }}</div>
      <div class="school-tagline">{{ $globalSchoolInfo->tagline ?? 'DISCIPLINE | EDUCATION | EXCELLENCE' }}</div>
      <div class="report-title-banner">STUDENT PROGRESS REPORT CARD</div>
      <div style="font-size: 12px; font-weight: bold; color: #3d1a06; font-family: Arial, sans-serif; margin-top: 4px;">
        ACADEMIC SESSION {{ optional($admission->academicSession)->session_name ?: '2026-2027' }}
      </div>
    </div>

    <div style="width: 90px; text-align: right;">
      <div style="width: 80px; height: 90px; border: 1px solid #c7ad8d; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #3d1a06; font-family: Arial, sans-serif; background:#fdfaf3;">
        @if($admission->student_photo)
          <img src="{{ asset('storage/' . $admission->student_photo) }}" alt="Photo" style="width:100%;height:100%;object-fit:cover;">
        @else
          PHOTO
        @endif
      </div>
    </div>
  </div>

  <!-- ===== STUDENT INFORMATION ===== -->
  <div class="info-box">
    <table class="info-table">
      <tr>
        <td class="lbl">Student Name:</td>
        <td class="val">{{ $admission->student_name ?: ($admission->first_name . ' ' . $admission->last_name) }}</td>
        <td class="lbl">Admission No:</td>
        <td class="val"><span style="font-family: monospace;">{{ $admission->admission_no }}</span></td>
      </tr>
      <tr>
        <td class="lbl">Father Name:</td>
        <td class="val">{{ $admission->father_name }}</td>
        <td class="lbl">Roll Number:</td>
        <td class="val"><span style="font-family: monospace;">{{ $admission->roll_no ?: 'Unassigned' }}</span></td>
      </tr>
      <tr>
        <td class="lbl">Class &amp; Section:</td>
        <td class="val">{{ $admission->class_name }} ({{ $admission->section_name ?: 'A' }})</td>
        <td class="lbl">Exam Title:</td>
        <td class="val">{{ $selectedExam->title ?? 'All Examinations' }}</td>
      </tr>
      <tr>
        <td class="lbl">Exam Type:</td>
        <td class="val">{{ optional($selectedExam->examType)->name ?: 'Standard Evaluation' }}</td>
        <td class="lbl">Date of Issue:</td>
        <td class="val">{{ date('d M, Y') }}</td>
      </tr>
    </table>
  </div>

  <!-- ===== MARKS TABLE ===== -->
  @php
    $totalMax = 0;
    $totalObtained = 0;
    $passedCount = 0;
    $failedCount = 0;
  @endphp

  <div class="section-banner">Academic Performance Breakdown</div>
  <table class="marks-table">
    <thead>
      <tr>
        <th style="width: 40px;">#</th>
        <th class="td-left">Subject Name</th>
        <th style="width: 100px;">Total Marks</th>
        <th style="width: 100px;">Pass Marks</th>
        <th style="width: 110px;">Marks Obtained</th>
        <th style="width: 80px;">Grade</th>
        <th style="width: 90px;">Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($marks as $idx => $m)
        @php
          $subMax = $m->total_marks;
          $subPass = $m->pass_marks;
          $subObt = $m->is_absent ? 0 : $m->marks_obtained;
          $totalMax += $subMax;
          $totalObtained += $subObt;

          $pct = $subMax > 0 ? ($subObt / $subMax) * 100 : 0;
          $isSubPass = !$m->is_absent && $pct >= 40;

          if ($isSubPass) { $passedCount++; } else { $failedCount++; }

          $grade = 'F';
          if ($pct >= 80) $grade = 'A+';
          elseif ($pct >= 70) $grade = 'A';
          elseif ($pct >= 60) $grade = 'B';
          elseif ($pct >= 50) $grade = 'C';
          elseif ($pct >= 40) $grade = 'D';
        @endphp
        <tr>
          <td>{{ $idx + 1 }}</td>
          <td class="td-left" style="font-weight: bold; color: #3d1a06;">{{ $m->subject_name }}</td>
          <td>{{ number_format($subMax) }}</td>
          <td>{{ number_format($subPass) }}</td>
          <td>
            @if($m->is_absent)
              <span style="color: #b91c1c; font-weight: bold;">ABSENT</span>
            @else
              <span style="font-weight: bold;">{{ $subObt }}</span>
            @endif
          </td>
          <td style="font-weight: bold;">{{ $m->is_absent ? '-' : $grade }}</td>
          <td>
            <span style="font-weight: bold; color: {{ $isSubPass ? '#15803d' : '#b91c1c' }};">
              {{ $m->is_absent ? 'ABSENT' : ($isSubPass ? 'PASS' : 'FAIL') }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" style="padding: 20px; color: #6b4423;">No evaluation marks found for this examination.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <!-- ===== SUMMARY CARDS ===== -->
  @php
    $overallPct = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0;
    $overallPass = $marks->isNotEmpty() && $failedCount === 0 && $overallPct >= 40;

    $overallGrade = 'F';
    if ($overallPct >= 80) $overallGrade = 'A+';
    elseif ($overallPct >= 70) $overallGrade = 'A';
    elseif ($overallPct >= 60) $overallGrade = 'B';
    elseif ($overallPct >= 50) $overallGrade = 'C';
    elseif ($overallPct >= 40) $overallGrade = 'D';
  @endphp

  <div class="summary-grid">
    <div class="sum-card">
      <div class="sum-lbl">Total Maximum Marks</div>
      <div class="sum-val">{{ number_format($totalMax) }}</div>
    </div>
    <div class="sum-card">
      <div class="sum-lbl">Total Marks Obtained</div>
      <div class="sum-val">{{ number_format($totalObtained) }}</div>
    </div>
    <div class="sum-card">
      <div class="sum-lbl">Percentage</div>
      <div class="sum-val" style="color: {{ $overallPass ? '#15803d' : '#b91c1c' }};">{{ $overallPct }}%</div>
    </div>
    <div class="sum-card">
      <div class="sum-lbl">Overall Grade</div>
      <div class="sum-val">{{ $marks->isNotEmpty() ? $overallGrade : 'N/A' }}</div>
    </div>
    <div class="sum-card" style="background: {{ $overallPass ? '#f2fbf4' : '#fdf2f2' }}; border-color: {{ $overallPass ? '#a7f3d0' : '#fecaca' }};">
      <div class="sum-lbl" style="color: {{ $overallPass ? '#15803d' : '#b91c1c' }};">Result Status</div>
      <div class="sum-val" style="color: {{ $overallPass ? '#15803d' : '#b91c1c' }};">
        {{ $marks->isNotEmpty() ? ($overallPass ? 'PROMOTED / PASS' : 'FAILED') : 'PENDING' }}
      </div>
    </div>
  </div>

  <!-- ===== TEACHER REMARKS ===== -->
  <div class="remarks-box">
    <strong>Class Teacher Remarks &amp; Evaluation Feedback:</strong>
    <p style="margin-top: 4px; color: #3d1a06;">
      @if($overallPct >= 80)
        Outstanding academic performance! Exhibits exceptional diligence, problem-solving skills, and exemplary classroom conduct.
      @elseif($overallPct >= 60)
        Good performance. Shows consistent effort and progress across core subjects.
      @elseif($overallPct >= 40)
        Satisfactory performance. Regular practice and focused revision are encouraged in weaker subjects.
      @else
        Needs significant improvement. Parent-teacher consultation is recommended to plan structured academic support.
      @endif
    </p>
  </div>

  <!-- ===== SIGNATURES ===== -->
  <div class="signatures-row">
    <div class="sig-col">
      <div class="sig-line"></div>
      <div class="sig-title">Class Teacher Signature</div>
    </div>
    <div class="sig-col">
      <div class="sig-line"></div>
      <div class="sig-title">Examination Controller</div>
    </div>
    <div class="sig-col">
      <div class="sig-line"></div>
      <div class="sig-title">Principal Signature &amp; Stamp</div>
    </div>
  </div>

  <div class="footer-note">
    Note: This is an official computer-generated Progress Report Card issued by {{ $globalSchoolInfo->school_name ?? 'Noor Ul Huda Superior School' }}.
  </div>

</div>

</body>
</html>
