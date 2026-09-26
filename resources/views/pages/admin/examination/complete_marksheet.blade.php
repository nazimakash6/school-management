<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complete Cumulative Marksheet — {{ $admission->student_name }} ({{ $admission->admission_no }})</title>
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
    margin: 8mm 8mm;
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
    font-size: 14px;
    font-weight: 900;
    letter-spacing: 2px;
    margin-top: 8px;
    padding: 4px 14px;
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
    width: 16%;
  }

  .val {
    color: #3d1a06;
    font-weight: 600;
    width: 34%;
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
    margin-top: 10px;
  }

  /* ===== MARKS & SUMMARY TABLES ===== */
  table.custom-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    margin-bottom: 15px;
    font-family: Arial, sans-serif;
    border: 1px solid #c7ad8d;
  }

  table.custom-table th {
    background: #3d1a06;
    color: #fdfaf3;
    font-weight: bold;
    padding: 6px 8px;
    text-align: center;
    border: 1px solid #c7ad8d;
    text-transform: uppercase;
    font-size: 10px;
  }

  table.custom-table td {
    padding: 6px 8px;
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
    gap: 10px;
    margin-bottom: 15px;
    font-family: Arial, sans-serif;
  }

  .sum-card {
    flex: 1;
    border: 1px solid #c7ad8d;
    background: #fdfaf3;
    padding: 8px;
    border-radius: 6px;
    text-align: center;
  }

  .sum-card .sum-lbl {
    font-size: 9px;
    font-weight: bold;
    color: #6b4423;
    text-transform: uppercase;
  }

  .sum-card .sum-val {
    font-size: 16px;
    font-weight: 900;
    color: #3d1a06;
    margin-top: 2px;
  }

  /* ===== REMARKS & SIGNATURES ===== */
  .remarks-box {
    border: 1px solid #c7ad8d;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 25px;
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
    margin-top: 35px;
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
    margin-top: 15px;
    border-top: 1px dashed #c7ad8d;
    padding-top: 6px;
  }
</style>
</head>
<body>

<!-- Screen Top Action Bar -->
<div class="action-bar no-print">
  <div class="status-text">
    📜 Official Complete Student Marksheet — {{ $admission->student_name }}
  </div>
  <div class="btn-group-actions d-flex gap-2" style="display: flex; gap: 8px;">
    <button onclick="window.print()" class="btn-act btn-print">
      🖨️ Print Marksheet
    </button>
    <a href="{{ route('examination.performance', ['admission_id' => $admission->id, 'academic_session_id' => $sessionId, 'class_name' => $className]) }}" class="btn-act btn-back">
      📊 Back to Performance
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
      <div class="report-title-banner">COMPLETE CUMULATIVE ACADEMIC MARKSHEET</div>
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
        <td class="lbl">Report Type:</td>
        <td class="val">All Exams Cumulative Marksheet</td>
      </tr>
      <tr>
        <td class="lbl">Academic Session:</td>
        <td class="val">{{ optional($admission->academicSession)->session_name ?: 'Active Session' }}</td>
        <td class="lbl">Date of Issue:</td>
        <td class="val">{{ date('d M, Y') }}</td>
      </tr>
    </table>
  </div>

  <!-- ===== EXAM TYPE BREAKDOWN SUMMARY ===== -->
  <div class="section-banner">Performance Breakdown By Exam Type</div>
  <table class="custom-table">
    <thead>
      <tr>
        <th class="td-left">Exam Type Category</th>
        <th style="width: 110px;">Exams Evaluated</th>
        <th style="width: 120px;">Total Max Marks</th>
        <th style="width: 130px;">Marks Obtained</th>
        <th style="width: 110px;">Percentage</th>
        <th style="width: 120px;">Performance Level</th>
      </tr>
    </thead>
    <tbody>
      @php $hasTypeData = false; @endphp
      @foreach($examTypes as $type)
        @php
          $stat = $examTypeStats[$type->name] ?? null;
          $count = $stat ? $stat['count'] : 0;
          $obt = $stat ? $stat['obtained'] : 0;
          $tot = $stat ? $stat['total'] : 0;
          $pct = $stat ? $stat['percentage'] : 0;
          if ($count > 0) $hasTypeData = true;

          $rating = 'N/A';
          $ratingColor = '#6b4423';
          if ($count > 0) {
            if ($pct >= 80) { $rating = 'OUTSTANDING'; $ratingColor = '#15803d'; }
            elseif ($pct >= 70) { $rating = 'EXCELLENT'; $ratingColor = '#0369a1'; }
            elseif ($pct >= 60) { $rating = 'GOOD'; $ratingColor = '#0d9488'; }
            elseif ($pct >= 40) { $rating = 'SATISFACTORY'; $ratingColor = '#d97706'; }
            else { $rating = 'NEEDS IMPROVEMENT'; $ratingColor = '#b91c1c'; }
          }
        @endphp
        <tr>
          <td class="td-left" style="font-weight: bold; color: #3d1a06;">{{ $type->name }} ({{ $type->code ?: 'TYPE' }})</td>
          <td style="font-weight: bold;">{{ $count }} Exam(s)</td>
          <td>{{ number_format($tot) }}</td>
          <td style="font-weight: bold;">{{ number_format($obt) }}</td>
          <td style="font-weight: bold; color: {{ $ratingColor }};">{{ $count > 0 ? $pct . '%' : 'N/A' }}</td>
          <td style="font-weight: bold; color: {{ $ratingColor }};">{{ $rating }}</td>
        </tr>
      @endforeach
      @if(!$hasTypeData)
        <tr>
          <td colspan="6" style="padding: 12px; color: #6b4423;">No exam evaluations found for this student.</td>
        </tr>
      @endif
    </tbody>
  </table>

  <!-- ===== DETAILED EXAM MARKS LOG ===== -->
  <div class="section-banner">Detailed Subject Marks Record</div>
  <table class="custom-table">
    <thead>
      <tr>
        <th style="width: 35px;">#</th>
        <th class="td-left">Exam Title</th>
        <th>Exam Type</th>
        <th class="td-left">Subject</th>
        <th style="width: 90px;">Total Marks</th>
        <th style="width: 100px;">Marks Obtained</th>
        <th style="width: 80px;">Percentage</th>
        <th style="width: 85px;">Status</th>
      </tr>
    </thead>
    <tbody>
      @php $counter = 0; @endphp
      @foreach($examTypeStats as $typeName => $stat)
        @foreach($stat['marks'] as $m)
          @php
            $counter++;
            $pct = $m->total_marks > 0 ? round(($m->marks_obtained / $m->total_marks) * 100, 1) : 0;
            $isPass = !$m->is_absent && $pct >= 40;
          @endphp
          <tr>
            <td>{{ $counter }}</td>
            <td class="td-left" style="font-weight: bold; color: #3d1a06;">{{ optional($m->examination)->title }}</td>
            <td><span style="font-weight: bold; color: #3d1a06;">{{ $typeName }}</span></td>
            <td class="td-left" style="font-weight: 600;">{{ $m->subject_name }}</td>
            <td>{{ number_format($m->total_marks) }}</td>
            <td>
              @if($m->is_absent)
                <span style="color: #b91c1c; font-weight: bold;">ABSENT</span>
              @else
                <span style="font-weight: bold;">{{ $m->marks_obtained }}</span>
              @endif
            </td>
            <td style="font-weight: bold; color: {{ $isPass ? '#15803d' : '#b91c1c' }};">{{ $pct }}%</td>
            <td>
              <span style="font-weight: bold; color: {{ $isPass ? '#15803d' : '#b91c1c' }};">
                {{ $m->is_absent ? 'ABSENT' : ($isPass ? 'PASS' : 'FAIL') }}
              </span>
            </td>
          </tr>
        @endforeach
      @endforeach
      @if($counter === 0)
        <tr>
          <td colspan="8" style="padding: 15px; color: #6b4423;">No individual subject marks recorded yet.</td>
        </tr>
      @endif
    </tbody>
  </table>

  <!-- ===== OVERALL CUMULATIVE SUMMARY CARDS ===== -->
  @php
    $overallPass = $totalMaxAll > 0 && $cumulativePct >= 40;
    $grade = 'F';
    if ($cumulativePct >= 80) $grade = 'A+';
    elseif ($cumulativePct >= 70) $grade = 'A';
    elseif ($cumulativePct >= 60) $grade = 'B';
    elseif ($cumulativePct >= 50) $grade = 'C';
    elseif ($cumulativePct >= 40) $grade = 'D';
  @endphp

  <div class="summary-grid">
    <div class="sum-card">
      <div class="sum-lbl">Total Maximum Marks</div>
      <div class="sum-val">{{ number_format($totalMaxAll) }}</div>
    </div>
    <div class="sum-card">
      <div class="sum-lbl">Total Obtained Marks</div>
      <div class="sum-val">{{ number_format($totalObtainedAll) }}</div>
    </div>
    <div class="sum-card">
      <div class="sum-lbl">Cumulative Percentage</div>
      <div class="sum-val" style="color: {{ $overallPass ? '#15803d' : '#b91c1c' }};">{{ $cumulativePct }}%</div>
    </div>
    <div class="sum-card">
      <div class="sum-lbl">Cumulative Grade</div>
      <div class="sum-val">{{ $totalMaxAll > 0 ? $grade : 'N/A' }}</div>
    </div>
    <div class="sum-card" style="background: {{ $overallPass ? '#f2fbf4' : '#fdf2f2' }}; border-color: {{ $overallPass ? '#a7f3d0' : '#fecaca' }};">
      <div class="sum-lbl" style="color: {{ $overallPass ? '#15803d' : '#b91c1c' }};">Overall Result Standing</div>
      <div class="sum-val" style="color: {{ $overallPass ? '#15803d' : '#b91c1c' }};">
        {{ $totalMaxAll > 0 ? ($overallPass ? 'PASSED & PROMOTED' : 'NEEDS REVISION') : 'PENDING' }}
      </div>
    </div>
  </div>

  <!-- ===== REMARKS & EVALUATION ===== -->
  <div class="remarks-box">
    <strong>Cumulative Academic Evaluation &amp; Teacher Remarks:</strong>
    <p style="margin-top: 4px; color: #3d1a06;">
      @if($cumulativePct >= 80)
        Exemplary academic achievement! The student displays comprehensive conceptual mastery, outstanding analytical capability, and consistent dedication across all evaluation terms.
      @elseif($cumulativePct >= 60)
        Commendable performance. Demonstrates solid understanding in key subjects with steady effort and active participation.
      @elseif($cumulativePct >= 40)
        Satisfactory performance. Achieved passing standard, but regular revision and targeted guidance are advised for higher achievement.
      @else
        Needs significant improvement and focused support. Parents are requested to consult with subject teachers to establish a study plan.
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
      <div class="sig-title">Controller of Examinations</div>
    </div>
    <div class="sig-col">
      <div class="sig-line"></div>
      <div class="sig-title">Principal Signature &amp; Stamp</div>
    </div>
  </div>

  <div class="footer-note">
    Official Computer-Generated Marksheet Document issued by {{ $globalSchoolInfo->school_name ?? 'Noor Ul Huda Superior School' }}.
  </div>

</div>

</body>
</html>
