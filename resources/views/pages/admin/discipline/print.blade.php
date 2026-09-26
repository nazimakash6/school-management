<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Discipline & Character Report — {{ $student->full_name }}</title>
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
    .form-container-disc {
      width: 100% !important;
      max-height: 286mm !important;
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
  .action-bar-disc {
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

  .action-bar-disc .status-text {
    color: #fdfaf3;
    font-size: 13px;
    font-weight: bold;
    font-family: Arial, sans-serif;
  }

  .btn-group-actions-disc {
    display: flex;
    gap: 8px;
  }

  .btn-act-disc {
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

  .btn-print-disc { background: #22c55e; color: #ffffff; }
  .btn-print-disc:hover { background: #16a34a; }
  .btn-back-disc { background: #c7ad8d; color: #3d1a06; }
  .btn-back-disc:hover { background: #b89b78; }
  .btn-edit-disc { background: #f59e0b; color: #3d1a06; }
  .btn-edit-disc:hover { background: #d97706; color: #ffffff; }

  /* ===== MAIN A4 CONTAINER ===== */
  .form-container-disc {
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
  .header-grid-disc {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #c7ad8d;
    padding-bottom: 5px;
    margin-bottom: 5px;
    gap: 10px;
  }

  .header-logo-wrapper-disc {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .header-logo-disc {
    max-width: 70px;
    max-height: 70px;
    object-fit: contain;
  }

  .header-center-disc {
    flex: 1;
    text-align: center;
  }

  .bismillah-disc {
    font-family: 'Amiri', serif;
    font-size: 14px;
    font-weight: bold;
    color: #3d1a06;
    line-height: 1;
  }

  .school-name-disc {
    font-size: 19px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1px;
    text-transform: uppercase;
    line-height: 1.15;
    margin-top: 1px;
  }

  .school-tagline-disc {
    font-size: 8.5px;
    font-weight: bold;
    color: #3d1a06;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
    margin-top: 1px;
  }

  .form-title-pill-disc {
    font-size: 13px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1.5px;
    margin-top: 2px;
    text-transform: uppercase;
  }

  .session-subtitle-disc {
    font-size: 9.5px;
    font-weight: bold;
    color: #3d1a06;
    letter-spacing: 0.8px;
    font-family: Arial, sans-serif;
    margin-top: 1px;
  }

  .header-right-meta-disc {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    flex-shrink: 0;
  }

  .meta-lines-disc {
    font-size: 9px;
    font-family: Arial, sans-serif;
    color: #3d1a06;
    line-height: 1.45;
  }

  .meta-line-item-disc {
    border-bottom: 1px dotted #c7ad8d;
    padding-bottom: 1px;
    margin-bottom: 2px;
  }

  .meta-line-item-disc:last-child {
    border-bottom: none;
    margin-bottom: 0;
  }

  .meta-label-disc {
    font-weight: bold;
  }

  .photo-box-disc {
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

  .photo-box-disc img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* ===== SECTION BARS ===== */
  .section-bar-disc {
    background: #3d1a06;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 900;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 3px 8px;
    font-family: Arial, sans-serif;
    margin-top: 6px;
    margin-bottom: 3px;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    border: 1px solid #3d1a06;
  }

  /* ===== TABLES ===== */
  table.data-table-disc {
    width: 100%;
    border-collapse: collapse;
    font-size: 9.5px;
    margin-bottom: 4px;
    background: #ffffff;
    border: 1px solid #c7ad8d;
  }

  table.data-table-disc th {
    background: #3d1a06;
    color: #ffffff;
    font-size: 8.5px;
    font-family: Arial, sans-serif;
    font-weight: bold;
    padding: 3px 6px;
    text-align: left;
    text-transform: uppercase;
    border: 1px solid #3d1a06;
  }

  table.data-table-disc td {
    padding: 3px 6px;
    border: 1px solid #c7ad8d;
    vertical-align: middle;
  }

  .td-lbl-disc {
    font-weight: bold;
    color: #3d1a06;
    background: #fdfaf3;
    font-family: Arial, sans-serif;
    font-size: 8.5px;
  }

  .td-val-disc {
    color: #000000;
    font-weight: 600;
  }

  /* ===== PERIODIC STAR RATINGS CARDS GRID ===== */
  .rating-grid-disc {
    display: flex;
    gap: 6px;
    margin-bottom: 4px;
  }

  .rating-card-disc {
    flex: 1;
    background: #ffffff;
    border: 1.5px solid #c7ad8d;
    border-top: 3px solid #3d1a06;
    padding: 6px;
    text-align: center;
    border-radius: 4px;
  }

  .rating-card-disc.card-daily { border-top-color: #f59e0b; }
  .rating-card-disc.card-weekly { border-top-color: #3b82f6; }
  .rating-card-disc.card-monthly { border-top-color: #06b6d4; }
  .rating-card-disc.card-yearly { border-top-color: #10b981; }

  .rating-title-disc {
    font-size: 8.5px;
    font-family: Arial, sans-serif;
    font-weight: bold;
    text-transform: uppercase;
    color: #3d1a06;
    margin-bottom: 2px;
  }

  .rating-score-disc {
    font-size: 16px;
    font-weight: bold;
    color: #3d1a06;
    line-height: 1;
    margin: 2px 0;
  }

  .rating-stars-disc {
    display: flex;
    justify-content: center;
    gap: 1px;
    margin-top: 2px;
  }

  .rating-sub-disc {
    font-size: 7.5px;
    font-family: Arial, sans-serif;
    color: #666666;
    margin-top: 2px;
  }

  /* ===== SIGNATURES ===== */
  .signatures-row-disc {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 20px;
    padding-top: 2px;
    text-align: center;
  }

  .sig-col-disc {
    flex: 1;
    padding: 0 8px;
  }

  .sig-line-bar-disc {
    border-top: 1.5px solid #3d1a06;
    margin-bottom: 3px;
  }

  .sig-title-text-disc {
    font-size: 9px;
    font-weight: bold;
    color: #3d1a06;
    font-family: Arial, sans-serif;
  }

  .footer-notice-disc {
    text-align: center;
    font-size: 8px;
    font-family: Arial, sans-serif;
    font-style: italic;
    color: #3d1a06;
    margin-top: 10px;
    border-top: 1px solid #c7ad8d;
    padding-top: 2px;
  }
</style>
</head>
<body>

<!-- Screen Top Action Bar -->
<div class="action-bar-disc no-print">
  <div class="status-text">
    ✓ Student Discipline & Character Report (Printable Form)
  </div>
  <div class="btn-group-actions-disc">
    <button onclick="window.print()" class="btn-act-disc btn-print-disc">
      🖨️ Print Character Report
    </button>
    <a href="{{ route('discipline.show', $discipline->id) }}" class="btn-act-disc btn-back-disc">
      👤 View Web Profile
    </a>
    <a href="{{ route('discipline.index') }}" class="btn-act-disc btn-back-disc">
      📋 Discipline List
    </a>
  </div>
</div>

<div class="form-container-disc">

  <!-- ===== HEADER ===== -->
  <div class="header-grid-disc">
    <div class="header-logo-wrapper-disc">
      <img src="{{ $globalSchoolInfo->logo_url ?: asset('assets/images/logo.png') }}" alt="School Logo" class="header-logo-disc" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
    </div>

    <div class="header-center-disc">
      <div class="bismillah-disc">رَّبِّ زِدْنِي عِلْمًا</div>
      <div class="school-name-disc">{{ $globalSchoolInfo->school_name ?? 'SCHOOL MANAGEMENT SYSTEM' }}</div>
      <div class="school-tagline-disc">{{ $globalSchoolInfo->tagline ?? 'DISCIPLINE | EDUCATION | EXCELLENCE' }}</div>
      <div class="form-title-pill-disc">STUDENT DISCIPLINE & CHARACTER REPORT</div>
      <div class="session-subtitle-disc">OFFICIAL CONDUCT & DISCIPLINE EVALUATION CERTIFICATE</div>
    </div>

    <div class="header-right-meta-disc">
      <div class="meta-lines-disc">
        <div class="meta-line-item-disc">
          <span class="meta-label-disc">Adm No:</span> {{ $student->admission_no ?? 'N/A' }}
        </div>
        <div class="meta-line-item-disc">
          <span class="meta-label-disc">Roll No:</span> {{ $student->roll_no ?? 'N/A' }}
        </div>
        <div class="meta-line-item-disc">
          <span class="meta-label-disc">Report Date:</span> {{ date('d/m/Y') }}
        </div>
      </div>

      <div class="photo-box-disc">
        @if($student->photo || $student->student_photo)
          <img src="{{ asset('storage/' . ($student->photo ?: $student->student_photo)) }}" alt="Photo">
        @else
          STUDENT<br>PHOTO
        @endif
      </div>
    </div>
  </div>

  <!-- ===== 1. STUDENT PROFILE INFORMATION ===== -->
  <div class="section-bar-disc">1. STUDENT PROFILE INFORMATION</div>
  <table class="data-table-disc">
    <tr>
      <td class="td-lbl-disc" style="width: 15%;">Student Name</td>
      <td class="td-val-disc" style="width: 35%;">{{ $student->full_name }}</td>
      <td class="td-lbl-disc" style="width: 15%;">Father Name</td>
      <td class="td-val-disc" style="width: 35%;">{{ $student->father_name ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td class="td-lbl-disc">Class & Section</td>
      <td class="td-val-disc">{{ $student->class_name }} {{ $student->section_name ? "({$student->section_name})" : '' }}</td>
      <td class="td-lbl-disc">Roll / Admission No.</td>
      <td class="td-val-disc">Roll #: {{ $student->roll_no ?? 'N/A' }} | Adm #: {{ $student->admission_no }}</td>
    </tr>
    <tr>
      <td class="td-lbl-disc">Gender / Religion</td>
      <td class="td-val-disc">{{ ucfirst($student->gender ?? 'N/A') }} / {{ $student->religion ?? 'Islam' }}</td>
      <td class="td-lbl-disc">Academic Session</td>
      <td class="td-val-disc">{{ date('Y') }}-{{ date('Y') + 1 }}</td>
    </tr>
  </table>

  <!-- ===== 2. PERIODIC PERFORMANCE STAR RATINGS ===== -->
  <div class="section-bar-disc">2. PERIODIC PERFORMANCE STAR RATINGS</div>
  <div class="rating-grid-disc">
    <div class="rating-card-disc card-daily">
      <div class="rating-title-disc">Student Daily Average Star Rating</div>
      <div class="rating-score-disc" style="color: #d97706;">{{ number_format($overallDailyAvgStar, 1) }} <span style="font-size: 10px; font-weight: normal; color: #666;">/ 5.0</span></div>
      <div class="rating-stars-disc">
        @for($i = 1; $i <= 5; $i++)
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= round($overallDailyAvgStar) ? '#f59e0b' : '#cbd5e1' }}" stroke="{{ $i <= round($overallDailyAvgStar) ? '#f59e0b' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        @endfor
      </div>
      <div class="rating-sub-disc">Avg Daily Score: {{ number_format($overallAvgScore, 1) }}/100</div>
    </div>

    <div class="rating-card-disc card-weekly">
      <div class="rating-title-disc">Weekly Star Rating</div>
      <div class="rating-score-disc" style="color: #2563eb;">{{ number_format($weeklyStars, 1) }} <span style="font-size: 10px; font-weight: normal; color: #666;">/ 5.0</span></div>
      <div class="rating-stars-disc">
        @for($i = 1; $i <= 5; $i++)
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= round($weeklyStars) ? '#3b82f6' : '#cbd5e1' }}" stroke="{{ $i <= round($weeklyStars) ? '#3b82f6' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        @endfor
      </div>
      <div class="rating-sub-disc">Current Week Average</div>
    </div>

    <div class="rating-card-disc card-monthly">
      <div class="rating-title-disc">Monthly Star Rating</div>
      <div class="rating-score-disc" style="color: #0891b2;">{{ number_format($monthlyStars, 1) }} <span style="font-size: 10px; font-weight: normal; color: #666;">/ 5.0</span></div>
      <div class="rating-stars-disc">
        @for($i = 1; $i <= 5; $i++)
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= round($monthlyStars) ? '#06b6d4' : '#cbd5e1' }}" stroke="{{ $i <= round($monthlyStars) ? '#06b6d4' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        @endfor
      </div>
      <div class="rating-sub-disc">Current Month Average</div>
    </div>

    <div class="rating-card-disc card-yearly">
      <div class="rating-title-disc">Yearly Star Rating</div>
      <div class="rating-score-disc" style="color: #059669;">{{ number_format($yearlyStars, 1) }} <span style="font-size: 10px; font-weight: normal; color: #666;">/ 5.0</span></div>
      <div class="rating-stars-disc">
        @for($i = 1; $i <= 5; $i++)
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="{{ $i <= round($yearlyStars) ? '#10b981' : '#cbd5e1' }}" stroke="{{ $i <= round($yearlyStars) ? '#10b981' : '#cbd5e1' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        @endfor
      </div>
      <div class="rating-sub-disc">Academic Year Average</div>
    </div>
  </div>

  <!-- ===== 3. FULL PERFORMANCE HISTORY ===== -->
  <div class="section-bar-disc">3. FULL PERFORMANCE HISTORY</div>
  <table class="data-table-disc">
    <thead>
      <tr>
        <th style="width: 12%;">Date</th>
        <th style="width: 38%;">Evaluated Categories & Ratings</th>
        <th style="width: 14%; text-align: center;">Score</th>
        <th style="width: 16%; text-align: center;">Star Rating</th>
        <th style="width: 20%;">Teacher Remarks</th>
      </tr>
    </thead>
    <tbody>
      @forelse($studentDisciplines as $hist)
        <tr>
          <td class="td-val-disc" style="font-family: Arial, sans-serif; font-size: 8.5px;">
            {{ $hist->entry_date ? $hist->entry_date->format('d/m/Y') : '-' }}
          </td>
          <td>
            @if (!empty($hist->category_ratings) && is_array($hist->category_ratings))
              <div style="display: flex; flex-wrap: wrap; gap: 2px;">
                @foreach ($hist->category_ratings as $cr)
                  <span style="display: inline-block; background: #fef3c7; border: 1px solid #fde68a; color: #92400e; font-size: 7.5px; font-family: Arial, sans-serif; font-weight: bold; padding: 1px 3px; border-radius: 2px;">
                    {{ $cr['category'] }}: {{ number_format($cr['star_rating'], 1) }}★
                  </span>
                @endforeach
              </div>
            @else
              <span style="font-weight: 600; color: #3d1a06;">{{ $hist->category }}</span>
            @endif
          </td>
          <td style="text-align: center; font-weight: bold; font-family: Arial, sans-serif;">
            {{ (float)$hist->obtained_score == (int)$hist->obtained_score ? (int)$hist->obtained_score : number_format($hist->obtained_score, 1) }} / {{ (float)$hist->total_score == (int)$hist->total_score ? (int)$hist->total_score : number_format($hist->total_score, 1) }}
          </td>
          <td style="text-align: center;">
            <span style="display: inline-flex; align-items: center; gap: 2px; font-weight: bold; color: #b45309; font-size: 8.5px; font-family: Arial, sans-serif;">
              ⭐ {{ number_format($hist->star_rating, 1) }} Stars
            </span>
          </td>
          <td style="font-size: 8.5px; color: #333333;">
            {{ $hist->remarks ?: ($hist->title ?: 'Regular Evaluation') }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" style="text-align: center; color: #666; padding: 10px;">No performance history records available for this student.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <!-- ===== 4. OFFICIAL SIGNATURES & STAMP ===== -->
  <div class="signatures-row-disc">
    <div class="sig-col-disc">
      <div class="sig-line-bar-disc"></div>
      <div class="sig-title-text-disc">Class Teacher Signature</div>
    </div>
    <div class="sig-col-disc">
      <div class="sig-line-bar-disc"></div>
      <div class="sig-title-text-disc">Discipline Incharge Signature</div>
    </div>
    <div class="sig-col-disc">
      <div class="sig-line-bar-disc"></div>
      <div class="sig-title-text-disc">Principal Signature & Stamp</div>
    </div>
  </div>

  <div class="footer-notice-disc">
    Note: Official Computer Generated Student Discipline & Conduct Certificate &bull; Report Generated On: {{ date('F d, Y \a\t h:i A') }}
  </div>

</div>

</body>
</html>
