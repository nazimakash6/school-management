@extends('layouts.app')

@section('title', 'Teacher Portal - EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Home</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Teacher Dashboard</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <i data-lucide="book-open" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Teacher Portal & Class Workspace</h3>
                    <p class="text-muted mb-0 fs-7">Welcome Teacher! Mark attendance, manage homework assignments, and record student grades.</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('homework.index') }}" class="btn btn-success btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none;">
                <i data-lucide="plus-circle" style="width:1rem;height:1rem;"></i> Assign Homework
            </a>
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-success btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="check-square" style="width:1rem;height:1rem;"></i> Mark Attendance
            </a>
        </div>
    </div>

    <!-- Teacher KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Assigned Class Students</span>
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                        <i data-lucide="users" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ number_format($totalStudents) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Active Student Roster</span>
                    <a href="{{ route('student-list.index') }}" class="text-success text-decoration-none fw-semibold">View Students &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Today's Class Attendance</span>
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                        <i data-lucide="check-circle-2" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ $attendancePercentage }}%</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Present in Class</span>
                    <a href="{{ route('attendance.index') }}" class="text-primary text-decoration-none fw-semibold">Mark Attendance &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Active Homeworks</span>
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i data-lucide="file-text" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ count($homeworkList) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Pending Evaluation</span>
                    <a href="{{ route('homework.index') }}" class="text-warning text-decoration-none fw-semibold">Homework List &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Upcoming Term Exams</span>
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                        <i data-lucide="award" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ count($upcomingExams) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Marks Entry Ready</span>
                    <a href="{{ route('examination.index') }}" class="text-info text-decoration-none fw-semibold">Grade Marks &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Content Modules -->
    <div class="row g-4 mb-4">
        <!-- Assigned Homework List -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="file-text" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
                        Recent Class Homework Assignments
                    </h6>
                    <a href="{{ route('homework.index') }}" class="btn btn-sm btn-outline-success">Manage Homework</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($homeworkList as $hw)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $hw->title }}</h6>
                                    <span class="badge bg-success bg-opacity-10 text-success">{{ $hw->studentClass->name ?? (count($hw->tasks) . ' Subjects') }}</span>
                                </div>
                                <p class="text-muted small mb-0"><i data-lucide="calendar" style="width:0.8rem;height:0.8rem;" class="me-1"></i>Due Date: {{ $hw->due_date ? $hw->due_date->format('M d, Y') : ($hw->submission_date ? \Carbon\Carbon::parse($hw->submission_date)->format('M d, Y') : 'N/A') }}</p>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">No active homework assignments.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Examinations & Grading -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="edit-3" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Exam Grading & Results Processing
                    </h6>
                    <a href="{{ route('examination.index') }}" class="btn btn-sm btn-outline-primary">Grading Portal</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingExams as $ex)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $ex->exam_name ?? 'Class Test' }}</h6>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $ex->status ?? 'Scheduled' }}</span>
                                </div>
                                <span class="text-muted small"><i data-lucide="bookmark" style="width:0.8rem;height:0.8rem;" class="me-1"></i>Class: {{ $ex->class_name ?? 'All' }} | Max Marks: {{ $ex->max_marks ?? 100 }}</span>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">No pending exam grading.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
