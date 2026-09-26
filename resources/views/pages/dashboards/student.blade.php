@extends('layouts.app')

@section('title', 'Student Portal - EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Home</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Student Portal</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
                    <i data-lucide="graduation-cap" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Student Academic Portal</h3>
                    <p class="text-muted mb-0 fs-7">Welcome Student! View your personal attendance, assigned homework, exam results & fee vouchers.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Student KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-pink border-4" style="border-left-color: #ec4899 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">My Attendance %</span>
                    <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger">
                        <i data-lucide="check-circle" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">96.5%</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Present Count: 42 Days</span>
                    <span class="text-success fw-semibold">Good Standing</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Assigned Homeworks</span>
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                        <i data-lucide="book-open" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ count($homeworkList) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Active Subject Tasks</span>
                    <a href="{{ route('homework.index') }}" class="text-primary text-decoration-none fw-semibold">View Homework &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Upcoming Exams</span>
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i data-lucide="award" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ count($upcomingExams) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Term Date Sheet</span>
                    <a href="{{ route('examination.index') }}" class="text-warning text-decoration-none fw-semibold">Results & Date Sheet &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Fee Payment Status</span>
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                        <i data-lucide="shield-check" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-success mb-1">Paid</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Current Month Fee</span>
                    <a href="{{ route('fee-management.index') }}" class="text-success text-decoration-none fw-semibold">Receipts &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- Homework Assignments -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="file-text" class="text-pink" style="width:1.2rem;height:1.2rem;"></i>
                        Pending Homework Assignments
                    </h6>
                    <a href="{{ route('homework.index') }}" class="btn btn-sm btn-outline-primary">Open Homework Portal</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($homeworkList as $hw)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $hw->title }}</h6>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $hw->studentClass->name ?? (count($hw->tasks) . ' Subjects') }}</span>
                                </div>
                                <p class="text-muted small mb-0"><i data-lucide="calendar" style="width:0.8rem;height:0.8rem;" class="me-1"></i>Due: {{ $hw->due_date ? $hw->due_date->format('M d, Y') : ($hw->submission_date ? \Carbon\Carbon::parse($hw->submission_date)->format('M d, Y') : 'N/A') }}</p>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">No pending homework assignments.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Examinations & Date Sheet -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="award" class="text-warning" style="width:1.2rem;height:1.2rem;"></i>
                        Examination Schedule & Result Cards
                    </h6>
                    <a href="{{ route('examination.index') }}" class="btn btn-sm btn-outline-warning">View Exam Portal</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingExams as $ex)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $ex->exam_name ?? 'Term Test' }}</h6>
                                    <span class="badge bg-warning bg-opacity-10 text-warning">{{ $ex->status ?? 'Scheduled' }}</span>
                                </div>
                                <span class="text-muted small"><i data-lucide="book-open" style="width:0.8rem;height:0.8rem;" class="me-1"></i>Subject: {{ $ex->subject_name ?? 'General' }}</span>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">No upcoming exams.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
