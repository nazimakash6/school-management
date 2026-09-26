@extends('layouts.app')

@section('title', 'Principal Dashboard - EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Home</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Principal Dashboard</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                    <i data-lucide="award" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Academic Principal Command Center</h3>
                    <p class="text-muted mb-0 fs-7">Welcome Principal! Overview of academic progress, faculty attendance & examinations.</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('meetings.create') }}" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                <i data-lucide="calendar-plus" style="width:1rem;height:1rem;"></i> Call Staff Meeting
            </a>
            <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="file-text" style="width:1rem;height:1rem;"></i> Audit Logs
            </a>
        </div>
    </div>

    <!-- Principal KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-indigo border-4" style="border-left-color: #4f46e5 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Enrolled Students</span>
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                        <i data-lucide="users" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ number_format($totalStudents) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Overall Campus Strength</span>
                    <a href="{{ route('student-list.index') }}" class="text-primary text-decoration-none fw-semibold">Roster &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Active Faculty / Teachers</span>
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                        <i data-lucide="user-check" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ number_format($totalStaff) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Academic Teaching Staff</span>
                    <a href="{{ route('teacher.index') }}" class="text-success text-decoration-none fw-semibold">Faculty &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Campus Attendance %</span>
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i data-lucide="check-square" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ $attendancePercentage }}%</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Today's Overall Attendance</span>
                    <a href="{{ route('attendance.index') }}" class="text-warning text-decoration-none fw-semibold">Attendance &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Scheduled Examinations</span>
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                        <i data-lucide="file-check-2" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ count($upcomingExams) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Upcoming Exam Sessions</span>
                    <a href="{{ route('examination.index') }}" class="text-info text-decoration-none fw-semibold">Exams &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grids -->
    <div class="row g-4 mb-4">
        <!-- Upcoming Examinations -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="file-spread-sheet" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Upcoming Examinations & Term Tests
                    </h6>
                    <a href="{{ route('examination.index') }}" class="btn btn-sm btn-outline-primary">Manage Exams</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingExams as $ex)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $ex->exam_name ?? 'Term Exam' }}</h6>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $ex->status ?? 'Scheduled' }}</span>
                                </div>
                                <p class="text-muted small mb-0"><i data-lucide="book-open" style="width:0.85rem;height:0.85rem;" class="me-1"></i>Class: {{ $ex->class_name ?? 'All Classes' }} | Subject: {{ $ex->subject_name ?? 'General' }}</p>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">No upcoming exams scheduled.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Staff & PTM Meetings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="calendar" class="text-indigo" style="width:1.2rem;height:1.2rem;"></i>
                        Staff Meetings & Academic Conferences
                    </h6>
                    <a href="{{ route('meetings.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingMeetings as $m)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $m->title }}</h6>
                                    <span class="badge {{ $m->status_badge_class }}">{{ $m->status }}</span>
                                </div>
                                <span class="text-muted small"><i data-lucide="clock" style="width:0.8rem;height:0.8rem;" class="me-1"></i>{{ $m->formatted_time_range }} | {{ $m->meeting_type }}</span>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">No upcoming meetings.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
