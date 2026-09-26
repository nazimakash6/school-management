<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classwork Sheet #{{ $classwork->id }} - {{ $classwork->studentClass->name ?? 'Class' }} - {{ $classwork->assigned_date ? $classwork->assigned_date->format('d M Y') : '' }}</title>
    <!-- Google Fonts & Bootstrap 5 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .printable-page {
            max-width: 850px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid #e2e8f0;
            position: relative;
        }

        /* Top Action Bar */
        .print-action-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(8px);
            padding: 10px 16px;
            border-radius: 30px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
        }
        .btn-print-action {
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-print-primary {
            background: #0d9488;
            color: #ffffff;
        }
        .btn-print-primary:hover {
            background: #0f766e;
            color: #ffffff;
        }
        .btn-print-success {
            background: #10b981;
            color: #ffffff;
        }
        .btn-print-success:hover {
            background: #059669;
            color: #ffffff;
        }
        .btn-print-secondary {
            background: #475569;
            color: #ffffff;
        }
        .btn-print-secondary:hover {
            background: #334155;
            color: #ffffff;
        }

        /* Header Styles */
        .school-header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .school-logo {
            max-height: 75px;
            width: auto;
            object-fit: contain;
        }
        .school-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .school-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }
        .doc-badge {
            background: #ccfbf1;
            color: #0f766e;
            border: 1px solid #99f6e4;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 6px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        /* Information Grid */
        .info-grid {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .info-item-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
        }
        .info-item-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
        }

        /* Daily Class Diary Table */
        .diary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .diary-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
        }
        .diary-table td {
            padding: 12px;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            vertical-align: top;
        }

        /* Signature Footer */
        .signatures-area {
            margin-top: 45px;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .sig-box {
            text-align: center;
            width: 200px;
        }
        .sig-line {
            border-top: 1.5px dashed #64748b;
            margin-bottom: 6px;
        }
        .sig-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
        }

        /* Print Media Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
            }
            .printable-page {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Buttons -->
    <div class="print-action-bar no-print">
        <button onclick="window.print()" class="btn-print-action btn-print-primary" title="Print document">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print
        </button>
        <button onclick="downloadPDF()" class="btn-print-action btn-print-success" title="Download document as PDF">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Download PDF
        </button>
        <a href="javascript:window.close()" class="btn-print-action btn-print-secondary">
            ✕ Close
        </a>
    </div>

    <div class="printable-page">
        <!-- School Official Header -->
        <div class="school-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                @if(isset($schoolInfo->logo) && $schoolInfo->logo)
                    <img src="{{ asset('storage/' . $schoolInfo->logo) }}" alt="School Logo" class="school-logo">
                @else
                    <div class="bg-teal text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width:55px;height:55px;font-weight:800;font-size:1.5rem;background-color:#0d9488;">
                        {{ substr($schoolInfo->school_name ?? $schoolInfo->name ?? 'SCH', 0, 1) }}
                    </div>
                @endif
                <div>
                    <h2 class="school-title">{{ $schoolInfo->school_name ?? $schoolInfo->name ?? 'Al-Huda School System' }}</h2>
                    <p class="school-subtitle mb-0">
                        {{ $schoolInfo->address ?? 'Main Campus, Educational Complex' }}
                        @if(isset($schoolInfo->phone) && $schoolInfo->phone) | Tel: {{ $schoolInfo->phone }} @endif
                        @if(isset($schoolInfo->email) && $schoolInfo->email) | {{ $schoolInfo->email }} @endif
                    </p>
                </div>
            </div>
            <div class="text-end">
                <span class="doc-badge">Classwork Sheet</span>
                <div class="small text-muted mt-1">Classwork ID: #{{ $classwork->id }}</div>
            </div>
        </div>

        @php
            $tasks = $classwork->tasks;
        @endphp

        <!-- Metadata Summary Bar -->
        <div class="info-grid">
            <div class="row g-3">
                <div class="col-3">
                    <div class="info-item-label">Class</div>
                    <div class="info-item-value text-teal" style="color:#0d9488;">{{ $classwork->studentClass->name ?? 'N/A' }}</div>
                </div>
                <div class="col-3">
                    <div class="info-item-label">Assigned Date</div>
                    <div class="info-item-value">{{ $classwork->assigned_date ? $classwork->assigned_date->format('M d, Y (D)') : '-' }}</div>
                </div>
                <div class="col-3">
                    <div class="info-item-label">Submission Due Date</div>
                    <div class="info-item-value text-danger">{{ $classwork->due_date ? $classwork->due_date->format('M d, Y (D)') : 'Not Set' }}</div>
                </div>
                <div class="col-3">
                    <div class="info-item-label">Assigned By</div>
                    <div class="info-item-value">{{ $classwork->creator->name ?? 'Class Teacher' }}</div>
                </div>
            </div>
        </div>

        <!-- Complete Daily Classwork Schedule Table -->
        <div class="mt-3">
            <h6 class="fw-bold text-dark text-uppercase mb-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                Daily Classwork Sheet ({{ count($tasks) }} {{ Str::plural('Subject', count($tasks)) }})
            </h6>
            <table class="diary-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Subject</th>
                        <th style="width: 55%;">Classwork Task & Instructions</th>
                        <th style="width: 20%;">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['subject_name'] ?? 'Subject' }}</strong>
                                @if(!empty($item['subject_code']))
                                    <div class="small text-muted">{{ $item['subject_code'] }}</div>
                                @endif
                            </td>
                            <td>
                                @if(!empty($item['title']))
                                    <div class="fw-semibold text-dark mb-1">{{ $item['title'] }}</div>
                                @endif
                                <div class="text-secondary" style="white-space: pre-wrap;">{{ $item['description'] ?? '' }}</div>
                                @if(!empty($item['attachment']))
                                    <div class="small text-teal mt-1" style="color:#0d9488;">📎 Attachment Included</div>
                                @endif
                            </td>
                            <td>
                                {{ !empty($item['due_date']) ? \Carbon\Carbon::parse($item['due_date'])->format('M d, Y') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No subject tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- General Student Instructions -->
        <div class="mt-4 p-3 bg-light border rounded" style="font-size: 0.8rem; color: #475569;">
            <strong>Instructions for Students & Teachers:</strong>
            <ul class="mb-0 ps-3 mt-1">
                <li>Complete all assigned classwork exercises during class hours neatly in your respective subject notebooks.</li>
                <li>Submit completed classwork for teacher verification and marking.</li>
                <li>Teachers are requested to inspect and stamp classwork entries daily.</li>
            </ul>
        </div>

        <!-- Signatures Area -->
        <div class="signatures-area">
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-title">Subject Teacher Signature</div>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-title">Class Teacher Signature</div>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-title">Principal / Admin Stamp</div>
            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            window.print();
        }
    </script>
</body>
</html>
