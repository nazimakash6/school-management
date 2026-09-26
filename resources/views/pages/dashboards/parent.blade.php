@extends('layouts.app')

@section('title', 'Parent Portal - EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Home</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Parent Portal</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                    <i data-lucide="heart-handshake" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Parent & Guardian Portal</h3>
                    <p class="text-muted mb-0 fs-7">Welcome Parent! Monitor child attendance, fee vouchers, academic result cards & school meetings.</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('fee-management.index') }}" class="btn btn-teal text-white btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border: none;">
                <i data-lucide="credit-card" style="width:1rem;height:1rem;"></i> View Fee Vouchers
            </a>
            <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="calendar" style="width:1rem;height:1rem;"></i> PTM Meetings
            </a>
        </div>
    </div>

    <!-- Parent KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-teal border-4" style="border-left-color: #0d9488 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Children Enrolled</span>
                    <div class="bg-teal bg-opacity-10 p-2 rounded-3 text-teal" style="background-color: #ccfbf1; color: #0d9488;">
                        <i data-lucide="users" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">1 Student</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Child Academic Record</span>
                    <a href="{{ route('student-list.index') }}" class="text-teal text-decoration-none fw-semibold">View Roster &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Child Attendance %</span>
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                        <i data-lucide="check-circle-2" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">97.8%</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Regular Attendance</span>
                    <a href="{{ route('attendance.index') }}" class="text-success text-decoration-none fw-semibold">Log &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Fee Voucher Status</span>
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i data-lucide="wallet" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-success mb-1">Up To Date</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>No Pending Balance</span>
                    <a href="{{ route('fee-management.index') }}" class="text-warning text-decoration-none fw-semibold">Pay Voucher &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Parent Teacher Meetings</span>
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                        <i data-lucide="calendar" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ count($upcomingMeetings) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Scheduled PTMs</span>
                    <a href="{{ route('meetings.index') }}" class="text-info text-decoration-none fw-semibold">Meeting Schedule &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- Upcoming Parent Teacher Meetings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="calendar" class="text-teal" style="width:1.2rem;height:1.2rem;"></i>
                        Upcoming Parent Teacher Meetings (PTM)
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
                            <div class="p-4 text-center text-muted small">No upcoming parent meetings.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Report Cards -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="award" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
                        Academic Examination Reports
                    </h6>
                    <a href="{{ route('examination.index') }}" class="btn btn-sm btn-outline-success">View Report Cards</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingExams as $ex)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $ex->exam_name ?? 'Term Examination' }}</h6>
                                    <span class="badge bg-success bg-opacity-10 text-success">Scheduled</span>
                                </div>
                                <span class="text-muted small"><i data-lucide="book-open" style="width:0.8rem;height:0.8rem;" class="me-1"></i>Class: {{ $ex->class_name ?? 'All' }}</span>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">No recent examination report cards.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
