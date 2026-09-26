@extends('layouts.app')

@section('title', 'Attendance Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
<div class="content-header mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1.5 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item text-muted">Academic Operations</li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Attendance Management</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2.5">
            <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <i data-lucide="check-square" style="width:1.5rem;height:1.5rem;"></i>
            </div>
            <div>
                <h3 class="mb-0 fw-bold text-dark tracking-tight">Daily Attendance Tracking</h3>
                <p class="text-muted mb-0 fs-7">Single daily attendance tracking for Students and Staff members</p>
            </div>
        </div>
    </div>
    <div class="content-header-actions d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" data-bs-toggle="modal" data-bs-target="#settingsModal">
            <i data-lucide="clock" style="width:1rem;height:1rem;"></i> Configure School Timings
        </button>
    </div>
</div>

@if (session('status'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i data-lucide="check-circle" class="me-1" style="width:1.1rem;height:1.1rem;"></i> {{ session('status') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i data-lucide="alert-triangle" class="me-1" style="width:1.1rem;height:1.1rem;"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($isAdmin)
<div class="alert alert-primary py-2 px-3 mb-3 d-flex flex-wrap align-items-center justify-content-between rounded-3 border-0 shadow-2xs" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
    <div class="d-flex align-items-center gap-2">
        <i data-lucide="shield-check" class="text-primary" style="width:1.25rem;height:1.25rem;"></i>
        <span class="fs-7 text-primary-900 fw-semibold">Admin Mode: 24/7 Full Attendance Modification Privilege Unlocked</span>
    </div>
    <span class="badge bg-primary text-white fs-8">Admin Access</span>
</div>
@elseif ($isAttendanceMarked)
<div class="alert alert-warning py-2 px-3 mb-3 d-flex flex-wrap align-items-center justify-content-between rounded-3 border-0 shadow-2xs" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);">
    <div class="d-flex align-items-center gap-2">
        <i data-lucide="lock" class="text-warning" style="width:1.25rem;height:1.25rem;"></i>
        <span class="fs-7 text-amber-900 fw-semibold">Attendance Recorded & Locked: Controls are disabled until tomorrow's school time. Only Admins can modify existing records.</span>
    </div>
    <span class="badge bg-warning text-dark fs-8">Locked</span>
</div>
@elseif (!$isSchoolOpen && $isToday)
<div class="alert alert-secondary py-2 px-3 mb-3 d-flex flex-wrap align-items-center justify-content-between rounded-3 border-0 shadow-2xs">
    <div class="d-flex align-items-center gap-2">
        <i data-lucide="clock" class="text-secondary" style="width:1.25rem;height:1.25rem;"></i>
        <span class="fs-7 text-secondary-900 fw-semibold">Attendance Locked: School has not opened yet today (Opens at {{ \Carbon\Carbon::parse($settings->school_open_time)->format('h:i A') }}).</span>
    </div>
    <span class="badge bg-secondary text-white fs-8">Before Open Time</span>
</div>
@endif

<!-- Timings Status Banner -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
    <div class="card-body p-3">
        <div class="row g-3 text-center align-items-center">
            <div class="col-6 col-md-4 border-end">
                <div class="p-2">
                    <span class="text-tertiary text-xs d-block text-uppercase fw-semibold mb-1">1. School Open Time</span>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="door-open" class="text-success" style="width:1.1rem;height:1.1rem;"></i>
                        {{ \Carbon\Carbon::parse($settings->school_open_time)->format('h:i A') }}
                    </h5>
                    <small class="text-muted text-xs">Arrival Begins</small>
                </div>
            </div>

            <div class="col-6 col-md-4 border-end">
                <div class="p-2">
                    <span class="text-tertiary text-xs d-block text-uppercase fw-semibold mb-1">2. School Start Time</span>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="book-open" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        {{ \Carbon\Carbon::parse($settings->school_start_time)->format('h:i A') }}
                    </h5>
                    <small class="text-muted text-xs">Classes Start (Late Cutoff)</small>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="p-2">
                    <span class="text-tertiary text-xs d-block text-uppercase fw-semibold mb-1">3. School End Time</span>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="bell" class="text-danger" style="width:1.1rem;height:1.1rem;"></i>
                        {{ \Carbon\Carbon::parse($settings->school_end_time)->format('h:i A') }}
                    </h5>
                    <small class="text-muted text-xs">School Dismissal</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mode & Date Filters -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <!-- User Type Navigation Tabs -->
    <ul class="nav nav-pills custom-pills bg-white p-1 rounded-3 shadow-sm">
        <li class="nav-item">
            <a class="nav-link {{ $userType === 'student' ? 'active' : '' }}" href="{{ route('attendance.index', array_merge(request()->query(), ['user_type' => 'student'])) }}">
                <i data-lucide="graduation-cap" style="width:1rem;height:1rem;" class="me-1"></i> Student Attendance
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $userType === 'staff' ? 'active' : '' }}" href="{{ route('attendance.index', array_merge(request()->query(), ['user_type' => 'staff'])) }}">
                <i data-lucide="user-check" style="width:1rem;height:1rem;" class="me-1"></i> Staff Attendance
            </a>
        </li>
    </ul>

    <!-- Date Selection & Status -->
    <div class="d-flex align-items-center gap-2">
        <form method="GET" action="{{ route('attendance.index') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="user_type" value="{{ $userType }}">
            @if ($userType === 'student')
                <input type="hidden" name="class_name" value="{{ $className }}">
                <input type="hidden" name="section_name" value="{{ $sectionName }}">
            @endif
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
        </form>

        @if ($isToday)
            <span class="badge bg-success-subtle text-success px-2 py-1 fs-7">
                <i data-lucide="clock" style="width:0.875rem;height:0.875rem;"></i> Today (Live)
            </span>
        @else
            <span class="badge bg-secondary-subtle text-secondary px-2 py-1 fs-7">
                Historical Date
            </span>
        @endif
    </div>
</div>

<!-- Filters Bar -->
<div class="filter-bar bg-white p-3 rounded-3 shadow-sm mb-4">
    <form method="GET" action="{{ route('attendance.index') }}" class="row g-2 align-items-end">
        <input type="hidden" name="user_type" value="{{ $userType }}">
        <input type="hidden" name="date" value="{{ $date }}">

        @if ($userType === 'student')
        <div class="col-md-3">
            <label class="text-xs text-tertiary mb-1 d-block">Class</label>
            <select name="class_name" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Classes</option>
                @foreach ($classList as $cls)
                <option value="{{ $cls }}" @selected($className === $cls)>{{ $cls }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="text-xs text-tertiary mb-1 d-block">Section</label>
            <select name="section_name" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Sections</option>
                @foreach (['A', 'B', 'C', 'D'] as $sec)
                <option value="{{ $sec }}" @selected($sectionName === $sec)>Section {{ $sec }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="{{ $userType === 'student' ? 'col-md-4' : 'col-md-8' }}">
            <label class="text-xs text-tertiary mb-1 d-block">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Search by name, ID, or roll number...">
        </div>

        <div class="col-md-2 d-flex gap-2 justify-content-end">
            <button type="submit" class="btn btn-secondary btn-sm w-100">Filter</button>
            <a href="{{ route('attendance.index', ['user_type' => $userType, 'date' => $date]) }}" class="btn btn-ghost btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Attendance Status Overview Card -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-white border-start border-4 {{ ($isAdmin || ($isSchoolOpen && !$isAttendanceMarked)) ? 'border-primary' : 'border-warning' }}">
    <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold mb-1 text-dark">Daily Attendance Status (Single Session)</h6>
            <p class="text-muted text-xs mb-0">Official daily recording opens after {{ \Carbon\Carbon::parse($settings->school_open_time)->format('h:i A') }}</p>
        </div>
        <div>
            @if ($isAdmin)
                <span class="badge bg-primary-subtle text-primary">
                    <i data-lucide="shield-check" style="width:0.875rem;height:0.875rem;"></i> Always Open (Admin Override)
                </span>
            @elseif (!$isSchoolOpen && $isToday)
                <span class="badge bg-secondary-subtle text-secondary">
                    <i data-lucide="lock" style="width:0.875rem;height:0.875rem;"></i> Locked (Before Open Time)
                </span>
            @elseif ($isAttendanceMarked)
                <span class="badge bg-danger-subtle text-danger">
                    <i data-lucide="lock" style="width:0.875rem;height:0.875rem;"></i> Marked & Locked Until Tomorrow
                </span>
            @else
                <span class="badge bg-success-subtle text-success">
                    <i data-lucide="check-circle" style="width:0.875rem;height:0.875rem;"></i> Ready to Mark
                </span>
            @endif
        </div>
    </div>
</div>

<!-- Attendance Form -->
<form method="POST" action="{{ route('attendance.mark') }}">
    @csrf
    <input type="hidden" name="user_type" value="{{ $userType }}">
    <input type="hidden" name="date" value="{{ $date }}">

    @if ($usersList->isNotEmpty())
    @php
        $allMarked = $usersList->every(function($u) use ($existingRecords) {
            $r = $existingRecords->get($u->id);
            return $r && (!is_null($r->session1_status) || !is_null($r->final_status));
        });
        $submitDisabled = !$isAdmin && ((!$isSchoolOpen && $isToday) || $allMarked);
    @endphp
    <!-- Quick Bulk Action Bar -->
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white d-flex flex-wrap align-items-center justify-content-between gap-3 border-start border-4 border-primary">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 bg-primary-subtle text-primary rounded-2 d-inline-flex align-items-center justify-content-center">
                <i data-lucide="layers" style="width:1.25rem;height:1.25rem;"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark">Quick Bulk Attendance Actions</h6>
                    <div id="selectedCountBadge"></div>
                </div>
                <small class="text-muted text-xs">Select checkboxes to mark specific students, or apply to all listed {{ $userType }}s</small>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-success btn-sm fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" onclick="markAttendanceBulk('present')" @disabled($submitDisabled)>
                <i data-lucide="check-circle-2" style="width:1rem;height:1rem;"></i> Mark Present
            </button>
            <button type="button" class="btn btn-outline-warning btn-sm fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" onclick="markAttendanceBulk('late')" @disabled($submitDisabled)>
                <i data-lucide="clock" style="width:1rem;height:1rem;"></i> Mark Late
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" onclick="markAttendanceBulk('absent')" @disabled($submitDisabled)>
                <i data-lucide="x-circle" style="width:1rem;height:1rem;"></i> Mark Absent
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs" onclick="markAttendanceBulk('leave')" @disabled($submitDisabled)>
                <i data-lucide="calendar" style="width:1rem;height:1rem;"></i> Mark Leave
            </button>
        </div>
    </div>
    @endif

    <div class="table-container bg-white rounded-3 shadow-sm mb-4">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;" class="text-center">
                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input" onchange="toggleSelectAll(this)" @disabled($submitDisabled)>
                    </th>
                    <th># ID</th>
                    <th>Name</th>
                    @if ($userType === 'student')
                    <th>Class / Sec</th>
                    @else
                    <th>Designation</th>
                    @endif
                    <th style="min-width:280px;">Attendance Status</th>
                    <th>Final Status</th>
                    <th>Email Alert</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usersList as $index => $user)
                @php
                    $rec = $existingRecords->get($user->id);
                    $status = is_object($rec?->session1_status) ? $rec->session1_status->value : ($rec?->session1_status ?: (is_object($rec?->final_status) ? $rec->final_status->value : $rec?->final_status));
                    $displayStatus = strtoupper($status ?: 'PENDING');
                    $hasMarked = !is_null($status);

                    if ($isAdmin) {
                        $rowDisabled = false;
                    } else {
                        $rowDisabled = (!$isSchoolOpen && $isToday) || $hasMarked;
                    }

                    if ($userType === 'student') {
                        $emEmail    = $user->father_email ?? ($user->guardian_email ?? ($user->email ?? ''));
                        $emName     = $user->father_name ?: ($user->first_name . ' ' . $user->last_name);
                        $emType     = 'Parent';
                        $emTemplate = 'absence';
                        $emSubject  = "Attendance Notice: {$user->first_name} {$user->last_name} marked {$displayStatus}";
                        $emMsg      = "Dear Parent,\n\nWe wish to inform you that your child {$user->first_name} {$user->last_name} (Class: " . ($user->class_name ?: 'N/A') . ") was marked {$displayStatus} today ({$date}).\n\nPlease contact the school administration for further clarification.\n\nBest Regards,\nSchool Administration";
                    } else {
                        $staffFullName = $user->first_name . ' ' . $user->last_name;
                        $staffDesig    = ucfirst($user->designation ?? 'Staff');
                        $staffId       = $user->employee_id ?: ('STF-' . $user->id);

                        $emPrincipalName  = $principalName ?: 'Principal';
                        $emPrincipalEmail = $principalEmail ?? '';
                        $emPrincipalSubject = "Staff Attendance Alert: {$staffFullName} ({$staffDesig}) — {$displayStatus}";
                        $emPrincipalMsg   = "Assalamualaikum {$emPrincipalName} Sahib,\n\nThis is to inform you that staff member {$staffFullName} ({$staffId} - {$staffDesig}) attendance for {$date} has been recorded as: {$displayStatus}.\n\nPlease review and take necessary action if required.\n\nRegards,\nSchool Administration";

                        $emAdminName  = $adminName ?: 'Admin';
                        $emAdminEmail = $adminEmail ?? '';
                        $emAdminSubject = "Staff Attendance Update: {$staffFullName} — {$displayStatus}";
                        $emAdminMsg   = "Dear Admin ({$emAdminName}),\n\nAttendance Update:\nStaff: {$staffFullName} ({$staffId} - {$staffDesig})\nDate: {$date}\nStatus: {$displayStatus}\n\nPlease take necessary action if required.\n\nRegards,\nSchool Administration";
                    }
                @endphp
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input row-select-checkbox" data-user-id="{{ $user->id }}" onchange="updateSelectedCount()" @disabled($rowDisabled)>
                    </td>
                    <td>
                        <strong class="text-dark">{{ $userType === 'student' ? $user->admission_no : ($user->employee_id ?: 'STF-'.$user->id) }}</strong>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">{{ $user->first_name }} {{ $user->last_name }}</span>
                    </td>
                    <td>
                        @if ($userType === 'student')
                        <span class="badge bg-light text-dark border">{{ $user->class_name ?: 'N/A' }} - {{ $user->section_name ?: 'A' }}</span>
                        @else
                        <span class="badge bg-light text-dark border">{{ ucfirst($user->designation ?? 'Staff') }}</span>
                        @endif
                    </td>

                    <!-- Attendance Selection -->
                    <td>
                        <input type="hidden" name="attendance[{{ $index }}][user_id]" value="{{ $user->id }}">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <input type="radio" class="btn-check" name="attendance[{{ $index }}][status]" id="s_p_{{ $user->id }}" value="present" @checked($status === 'present') @disabled($rowDisabled)>
                            <label class="btn btn-outline-success btn-xs" for="s_p_{{ $user->id }}">Present</label>

                            <input type="radio" class="btn-check" name="attendance[{{ $index }}][status]" id="s_l_{{ $user->id }}" value="late" @checked($status === 'late') @disabled($rowDisabled)>
                            <label class="btn btn-outline-warning btn-xs" for="s_l_{{ $user->id }}">Late</label>

                            <input type="radio" class="btn-check" name="attendance[{{ $index }}][status]" id="s_a_{{ $user->id }}" value="absent" @checked($status === 'absent') @disabled($rowDisabled)>
                            <label class="btn btn-outline-danger btn-xs" for="s_a_{{ $user->id }}">Absent</label>

                            <input type="radio" class="btn-check" name="attendance[{{ $index }}][status]" id="s_v_{{ $user->id }}" value="leave" @checked($status === 'leave') @disabled($rowDisabled)>
                            <label class="btn btn-outline-secondary btn-xs" for="s_v_{{ $user->id }}">Leave</label>
                        </div>
                        @if ($hasMarked)
                            <small class="text-muted d-block mt-1 text-xs">
                                <i data-lucide="clock" style="width:0.75rem;height:0.75rem;"></i> Marked at {{ \Carbon\Carbon::parse($rec->session1_time ?: $rec->updated_at)->format('h:i A') }}
                            </small>
                        @endif
                        @if ($rowDisabled && !$isAdmin && $hasMarked)
                            <small class="text-danger d-block mt-1 text-xs">
                                <i data-lucide="lock" style="width:0.75rem;height:0.75rem;"></i> Locked (Attendance already recorded)
                            </small>
                        @elseif ($rowDisabled && !$isAdmin && !$isSchoolOpen && $isToday)
                            <small class="text-secondary d-block mt-1 text-xs">
                                <i data-lucide="lock" style="width:0.75rem;height:0.75rem;"></i> Opens at {{ \Carbon\Carbon::parse($settings->school_open_time)->format('h:i A') }}
                            </small>
                        @endif
                    </td>

                    <!-- Final Status Badge -->
                    <td class="final-status-cell">
                        @if ($status === 'present')
                            <span class="badge bg-success-subtle text-success">Present</span>
                        @elseif ($status === 'late')
                            <span class="badge bg-warning-subtle text-warning">Late</span>
                        @elseif ($status === 'absent')
                            <span class="badge bg-danger-subtle text-danger">Absent</span>
                        @elseif ($status === 'half_day')
                            <span class="badge bg-info-subtle text-info">Half Day</span>
                        @elseif ($status === 'leave')
                            <span class="badge bg-secondary-subtle text-secondary">Leave</span>
                        @else
                            <span class="badge bg-light text-muted border">Pending</span>
                        @endif
                    </td>

                    <!-- Email Alert -->
                    <td>
                        @if ($userType === 'student')
                            <button type="button" class="btn btn-outline-primary btn-xs d-inline-flex align-items-center gap-1 rounded-pill px-2.5 shadow-2xs fw-semibold"
                                    onclick="openEmailModal({ name: @js($emName), email: @js($emEmail), type: @js($emType), template: @js($emTemplate), subject: @js($emSubject), message: @js($emMsg) })">
                                <i data-lucide="mail" style="width:12px;height:12px;"></i>
                                <span>Parent</span>
                            </button>
                        @else
                            <div class="d-flex flex-column gap-1">
                                <button type="button" class="btn btn-outline-primary btn-xs d-inline-flex align-items-center gap-1 rounded-pill px-2.5 shadow-2xs fw-semibold"
                                        onclick="openEmailModal({ name: @js($emPrincipalName), email: @js($emPrincipalEmail), type: 'Principal', template: 'staff_attendance', subject: @js($emPrincipalSubject), message: @js($emPrincipalMsg) })">
                                    <i data-lucide="mail" style="width:11px;height:11px;"></i>
                                    <span>Principal</span>
                                </button>
                                <button type="button" class="btn btn-outline-success btn-xs d-inline-flex align-items-center gap-1 rounded-pill px-2.5 shadow-2xs fw-semibold"
                                        onclick="openEmailModal({ name: @js($emAdminName), email: @js($emAdminEmail), type: 'Admin', template: 'staff_attendance', subject: @js($emAdminSubject), message: @js($emAdminMsg) })">
                                    <i data-lucide="mail" style="width:11px;height:11px;"></i>
                                    <span>Admin</span>
                                </button>
                            </div>
                        @endif
                    </td>

                    <!-- Remarks -->
                    <td>
                        <input type="text" name="attendance[{{ $index }}][remarks]" class="form-control form-control-sm" value="{{ $rec?->session1_remarks ?: ($rec?->session2_remarks ?: '') }}" placeholder="Optional notes..." @disabled($rowDisabled)>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i data-lucide="users" style="width:2.5rem;height:2.5rem;" class="mb-2 text-muted"></i>
                        <p class="mb-0">No {{ $userType }} records found matching criteria.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Submit Section Actions -->
    @if ($usersList->isNotEmpty())
    @php
        $allMarked = $usersList->every(function($u) use ($existingRecords) {
            $r = $existingRecords->get($u->id);
            return $r && (!is_null($r->session1_status) || !is_null($r->final_status));
        });
        $submitDisabled = !$isAdmin && ((!$isSchoolOpen && $isToday) || $allMarked);
    @endphp
    <div class="card border-0 shadow-sm p-3 bg-white d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="text-muted text-sm">
            Total {{ ucfirst($userType) }}s Listed: <strong>{{ $usersList->count() }}</strong>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" @disabled($submitDisabled)>
                <i data-lucide="check-circle" style="width:1rem;height:1rem;" class="me-1"></i> Save Daily Attendance
            </button>
        </div>
    </div>
    @endif
</form>

<!-- Modal for School Timings Configuration -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('attendance.settings') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="settingsModalLabel">Configure School Timings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">1. School Open Time</label>
                    <input type="time" name="school_open_time" class="form-control" value="{{ \Carbon\Carbon::parse($settings->school_open_time)->format('H:i') }}" required>
                    <small class="text-muted text-xs">Students & Staff arrival time</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">2. School Start Time</label>
                    <input type="time" name="school_start_time" class="form-control" value="{{ \Carbon\Carbon::parse($settings->school_start_time)->format('H:i') }}" required>
                    <small class="text-muted text-xs">Classes start (Late threshold)</small>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">3. School End Time</label>
                    <input type="time" name="school_end_time" class="form-control" value="{{ \Carbon\Carbon::parse($settings->school_end_time)->format('H:i') }}" required>
                    <small class="text-muted text-xs">School dismissal time</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Schedule Timings</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/attendance.js') }}?v={{ time() }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name^="attendance"]:checked').forEach(function(radio) {
            const row = radio.closest('tr');
            if (row) {
                const cell = row.querySelector('.final-status-cell');
                if (cell) {
                    const v = radio.value;
                    const badges = {
                        present: '<span class="badge bg-success-subtle text-success fw-semibold fs-7 px-2.5 py-1">Present</span>',
                        late: '<span class="badge bg-warning-subtle text-warning fw-semibold fs-7 px-2.5 py-1">Late</span>',
                        absent: '<span class="badge bg-danger-subtle text-danger fw-semibold fs-7 px-2.5 py-1">Absent</span>',
                        leave: '<span class="badge bg-secondary-subtle text-secondary fw-semibold fs-7 px-2.5 py-1">Leave</span>'
                    };
                    cell.innerHTML = badges[v] || '<span class="badge bg-light text-muted border fs-7 px-2.5 py-1">Pending</span>';
                }
            }
        });
    });
</script>
@endpush
