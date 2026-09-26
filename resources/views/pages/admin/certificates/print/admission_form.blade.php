<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admission Form — {{ $data['student_name'] ?: 'Noor Ul Huda Superior School' }}</title>
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
    box-shadow: 0 10px 40px rgba(61,26,6,0.25);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  /* ===== OUTER BORDER WITH ROYAL BROWN & GOLD CORNERS ===== */
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
    width: 60px;
    height: 60px;
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
    padding: 38px 48px 12px;
    z-index: 2;
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
  }

  /* Header Top Row */
  .header-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
    position: relative;
    z-index: 3;
  }

  .official-logo-img {
    width: 105px;
    height: 105px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(61,26,6,0.15));
  }

  .header-center-text {
    flex: 1;
    text-align: center;
    padding: 0 10px;
  }

  .arabic-verse {
    font-size: 24px;
    font-weight: bold;
    color: #3d1a06;
    font-family: 'Amiri', 'Traditional Arabic', 'Georgia', serif;
    line-height: 1.1;
  }

  .verse-translation {
    font-size: 10.5px;
    color: #3d1a06;
    font-style: italic;
    margin-top: 1px;
    margin-bottom: 6px;
    opacity: 0.9;
  }

  .school-name {
    font-size: 32px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
    line-height: 1;
  }

  .school-subtitle-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin: 4px 0 6px;
  }

  .sub-line {
    height: 1.5px;
    width: 45px;
    background: #c7ad8d;
  }

  .school-subtitle {
    font-size: 13.5px;
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
    border: 1px solid #c7ad8d;
    border-radius: 20px;
    padding: 3px 20px;
    font-size: 10px;
    font-weight: bold;
    letter-spacing: 2.5px;
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
    max-width: 140px;
    height: 1px;
    background: linear-gradient(to right, transparent, #c7ad8d, transparent);
  }

  /* ===== CERTIFICATE TITLE BANNER ===== */
  .cert-title-wrap {
    background: #3d1a06;
    color: #ffffff;
    text-align: center;
    padding: 8px 20px;
    border-radius: 4px;
    border: 1px solid #c7ad8d;
    margin: 6px 40px;
    box-shadow: 0 3px 10px rgba(61,26,6,0.15);
  }

  .cert-title {
    font-size: 24px;
    font-weight: 900;
    color: #ffffff;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-family: 'Times New Roman', serif;
  }

  /* ===== ELEGANT DATA TABLE STYLING FOR ADMISSION FORM ===== */
  .student-table-container {
    margin: 10px 10px;
    border: 1.5px solid #c7ad8d;
    border-radius: 6px;
    overflow: hidden;
    background: #ffffff;
  }

  .cert-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
    color: #3d1a06;
  }

  .cert-table th {
    background: #3d1a06;
    color: #ffffff;
    padding: 9px 14px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
    border-bottom: 1.5px solid #c7ad8d;
    text-align: left;
  }

  .cert-table td {
    padding: 10px 14px;
    border-bottom: 1px solid #c7ad8d;
    vertical-align: middle;
  }

  .cert-table tr:last-child td {
    border-bottom: none;
  }

  .cert-table tr:nth-child(even) {
    background: #fdfaf3;
  }

  .table-label {
    font-weight: bold;
    color: #3d1a06;
    width: 22%;
  }

  .table-value {
    color: #000000;
    font-weight: 700;
    font-size: 14.5px;
  }

  /* ===== SIGNATURE SECTION ===== */
  .signatures-wrap {
    margin: 10px 10px 12px;
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

  .sig-pen-icon {
    width: 16px;
    height: 16px;
    margin-bottom: 4px;
    fill: #c7ad8d;
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
  }

  .sig-val {
    font-size: 11px;
    color: #000000;
    margin-top: 2px;
    font-weight: 600;
  }

  /* Center Ribbon Seal */
  .center-stamp {
    flex: 0 0 95px;
    text-align: center;
  }

  /* ===== CORE VALUES FOOTER STRIP ===== */
  .values-container {
    border-top: 1.5px solid #c7ad8d;
    padding-top: 10px;
    margin: 0 10px 10px;
    display: flex;
    justify-content: space-between;
  }

  .value-card {
    flex: 1;
    text-align: center;
    padding: 0 6px;
  }

  .value-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-bottom: 2px;
  }

  .value-icon-svg {
    width: 20px;
    height: 20px;
    fill: #3d1a06;
  }

  .value-title {
    font-size: 10.5px;
    font-weight: 900;
    color: #3d1a06;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
  }

  .value-desc {
    font-size: 8.5px;
    color: #3d1a06;
    opacity: 0.85;
    line-height: 1.3;
    font-family: Arial, sans-serif;
  }

  /* ===== DARK CHOCOLATE FOOTER BAR ===== */
  .footer-contact-bar {
    background: #3d1a06;
    color: #ffffff;
    margin: 0 -48px -12px;
    padding: 10px 48px;
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
        Print / Download Admission Form
    </button>
    <a href="javascript:window.close()" class="btn-action btn-close-print">✕ Close Tab</a>
</div>

<div class="cert-page">
  <!-- Outer & Inner Gold/Chocolate Decorative Frame -->
  <div class="border-outer"></div>
  <div class="border-inner"></div>

  <!-- Corner Filigree Ornaments -->
  <svg class="corner-svg corner-tl" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-tr" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-bl" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>
  <svg class="corner-svg corner-br" viewBox="0 0 100 100"><path d="M20 20 L80 20 C70 30 60 40 40 40 C40 60 30 70 20 80 Z M30 30 C40 30 50 35 50 50 C35 50 30 40 30 30 Z"/></svg>

  <div class="cert-inner">

    <!-- Header Section with Official School Logo -->
    <div class="header-top-row">
      <img src="{{ asset('assets/images/logo.png') }}" alt="Noor Ul Huda School Logo" class="official-logo-img">

      <div class="header-center-text">
        <div class="arabic-verse">رَّبِّ زِدْنِي عِلْمًا</div>
        <div class="verse-translation">"My Lord! Increase me in knowledge." (Quran 20:114)</div>

        <div class="school-name">NOOR UL HUDA</div>
        <div class="school-subtitle-wrap">
          <div class="sub-line"></div>
          <div class="school-subtitle">SUPERIOR SCHOOL</div>
          <div class="sub-line"></div>
        </div>
        <div class="tagline-badge">LEARN SUPERIOR &bull; BE SUPERIOR</div>
      </div>

      <img src="{{ asset('assets/images/logo.png') }}" alt="Noor Ul Huda School Logo" class="official-logo-img" style="opacity:0.9;">
    </div>

    <!-- Title Banner -->
    <div style="margin-top: 4px;">
      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="50" height="15" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>

      <div class="cert-title-wrap">
        <div class="cert-title">STUDENT ADMISSION FORM</div>
      </div>

      <div class="filigree-divider">
        <div class="filigree-line"></div>
        <svg width="35" height="10" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
        <div class="filigree-line"></div>
      </div>
    </div>

    <!-- Structured Data Table Container for Admission Form -->
    <div style="position: relative;">
      <div class="student-table-container">
        <table class="cert-table">
          <thead>
            <tr>
              <th colspan="4">STUDENT &amp; GUARDIAN ADMISSION RECORD</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="table-label">Student Name</td>
              <td class="table-value" style="width: 38%;">{{ $data['student_name'] }}</td>
              <td class="table-label">Father's Name</td>
              <td class="table-value">{{ $data['father_name'] }}</td>
            </tr>
            <tr>
              <td class="table-label">Admission No.</td>
              <td class="table-value">{{ $data['admission_no'] }}</td>
              <td class="table-label">Roll No.</td>
              <td class="table-value">{{ $data['roll_no'] }}</td>
            </tr>
            <tr>
              <td class="table-label">Class &amp; Section</td>
              <td class="table-value">{{ $data['class_name'] }} {{ $data['section'] }}</td>
              <td class="table-label">Academic Session</td>
              <td class="table-value">{{ $data['session'] }}</td>
            </tr>
            <tr>
              <td class="table-label">Date of Birth</td>
              <td class="table-value">{{ $data['date_of_birth'] }}</td>
              <td class="table-label">Admission Date</td>
              <td class="table-value">{{ $data['issue_date'] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Parent Undertaking Statement -->
    <div style="text-align: justify; font-size: 13.5px; color: #3d1a06; line-height: 1.8; margin: 16px 20px 10px;">
      <strong>Parent / Guardian Undertaking:</strong><br>
      I hereby declare that the particulars given above are true and accurate to the best of my knowledge. I agree to abide by all the rules, discipline, attendance guidelines, and fee policies of <strong>Noor Ul Huda Superior School</strong>, Dhanote, District Lodhran.
    </div>

    <!-- Filigree Divider above signatures -->
    <div class="filigree-divider" style="margin-bottom: 8px;">
      <div class="filigree-line"></div>
      <svg width="40" height="12" viewBox="0 0 100 30" fill="#c7ad8d"><path d="M50 0 C40 15 20 15 0 15 C20 15 40 15 50 30 C60 15 80 15 100 15 C80 15 60 15 50 0 Z"/></svg>
      <div class="filigree-line"></div>
    </div>

    <!-- Signatures & Center Stamp Row -->
    <div class="signatures-wrap">
      <div class="signatures-grid">
        <!-- Parent / Guardian Signature -->
        <div class="sig-box">
          <div class="sig-line"></div>
          <div class="sig-title">Parent / Guardian</div>
        </div>

        <!-- Admission Officer -->
        <div class="sig-box">
          <div class="sig-line"></div>
          <div class="sig-title">Admission Officer</div>
        </div>

        <!-- Center Stamp Seal with Official Logo Image -->
        <div class="center-stamp">
          <img src="{{ asset('assets/images/logo.png') }}" alt="Seal Stamp" style="width: 75px; height: 75px; object-fit: contain;">
        </div>

        <!-- Principal -->
        <div class="sig-box">
          <div class="sig-line"></div>
          <div class="sig-title">Principal</div>
          @if(!empty($data['principal']))
            <div class="sig-val">{{ $data['principal'] }}</div>
          @endif
        </div>
      </div>
    </div>

    <!-- Core Values Section -->
    <div class="values-container">
      <div class="value-card">
        <div class="value-header">
          <svg class="value-icon-svg" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
          <span class="value-title">KNOWLEDGE</span>
        </div>
        <div class="value-desc">Seek knowledge with sincerity.</div>
      </div>

      <div class="value-card">
        <div class="value-header">
          <svg class="value-icon-svg" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 14a4 4 0 1 1 4-4 4 4 0 0 1-4 4z"/><polygon points="17,6 18.2,9.5 22,9.5 19,11.8 20.2,15.3 17,13 13.8,15.3 15,11.8 12,9.5 15.8,9.5"/></svg>
          <span class="value-title">FAITH</span>
        </div>
        <div class="value-desc">Strengthen your faith in Allah.</div>
      </div>

      <div class="value-card">
        <div class="value-header">
          <svg class="value-icon-svg" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
          <span class="value-title">CHARACTER</span>
        </div>
        <div class="value-desc">Build good character for a better life.</div>
      </div>

      <div class="value-card">
        <div class="value-header">
          <svg class="value-icon-svg" viewBox="0 0 24 24"><path d="M19 5h-3X3V3h12v2zm-7 15l-5.5 3 1.5-6.1L3 11.5l6.3-.5L12 5l2.7 6 6.3.5-4.8 5.4 1.5 6.1z"/></svg>
          <span class="value-title">EXCELLENCE</span>
        </div>
        <div class="value-desc">Strive for excellence in everything.</div>
      </div>
    </div>

    <!-- Bottom Dark Chocolate Contact Bar -->
    <div class="footer-contact-bar">
      <div class="contact-item">
        <svg width="14" height="14" viewBox="0 0 24 24"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.27c1.21.49 2.53.76 3.88.76a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.35.27 2.67.76 3.88a1 1 0 01-.27 1.11l-2.37 2.4z"/></svg>
        03266850002
      </div>
      <div class="contact-item">
        <svg width="14" height="14" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
        Superiorschoolnps@gmail.com
      </div>
      <div class="contact-item">
        <svg width="14" height="14" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
        Dhanote, District Lodhran
      </div>
    </div>

  </div><!-- end cert-inner -->
</div><!-- end cert-page -->

</body>
</html>
