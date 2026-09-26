@extends('layouts.app')

@section('title', 'Admin Dashboard - EduCore School ERP')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
.kpi-card {
    border: none;
    border-radius: 14px;
    background: #ffffff;
    padding: 1.25rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
}
.quick-action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1rem 0.5rem;
    background: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    text-decoration: none;
    color: #1e293b;
    transition: all 0.2s ease;
}
.quick-action-card:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-2px);
    color: #2563eb;
}
.quick-action-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Home</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Executive Dashboard</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="layout-dashboard" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Executive School Dashboard</h3>
                    <p class="text-muted mb-0 fs-7">Welcome back! Here is your real-time academic, financial & campus overview</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2 align-items-center">
            <a href="{{ route('admission.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="user-plus" style="width:1rem;height:1rem;"></i> New Admission
            </a>
            <a href="{{ route('meetings.create') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="calendar-plus" style="width:1rem;height:1rem;"></i> Schedule Meeting
            </a>
        </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Students -->
        <div class="col-xl-3 col-sm-6">
            <div class="kpi-card border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Active Students</span>
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                        <i data-lucide="users" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ number_format($totalStudents) }}</h2>
                <div class="d-flex align-items-center justify-content-between text-muted small">
                    <span>Enrolled Across Classes</span>
                    <a href="{{ route('student-list.index') }}" class="text-primary text-decoration-none fw-semibold">View All &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Card 2: Staff & Teachers -->
        <div class="col-xl-3 col-sm-6">
            <div class="kpi-card border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Teachers & Staff</span>
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                        <i data-lucide="user-check" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ number_format($totalStaff) }}</h2>
                <div class="d-flex align-items-center justify-content-between text-muted small">
                    <span>Academic & Admin Faculty</span>
                    <a href="{{ route('teacher.index') }}" class="text-success text-decoration-none fw-semibold">View Faculty &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Card 3: Fee Revenue (PKR) -->
        <div class="col-xl-3 col-sm-6">
            <div class="kpi-card border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Monthly Fee Revenue</span>
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i data-lucide="wallet" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">Rs. {{ number_format($feeCollectionThisMonth) }}</h2>
                <div class="d-flex align-items-center justify-content-between text-muted small">
                    <span>Pending: Rs. {{ number_format($feeTotalPending) }}</span>
                    <a href="{{ route('fee-management.index') }}" class="text-warning text-decoration-none fw-semibold">Manage &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Card 4: Attendance & Visitors -->
        <div class="col-xl-3 col-sm-6">
            <div class="kpi-card border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Attendance & Visitors</span>
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                        <i data-lucide="shield-check" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h2 class="fw-bold text-dark mb-1">{{ $attendancePercentage }}%</h2>
                    <span class="badge bg-warning text-dark small">{{ $visitorsInsideCount }} Visitors In</span>
                </div>
                <div class="d-flex align-items-center justify-content-between text-muted small">
                    <span>Daily Student Presence</span>
                    <a href="{{ route('visitors.index') }}" class="text-info text-decoration-none fw-semibold">Gate Log &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Actions Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i data-lucide="zap" class="text-warning" style="width: 1.2rem; height: 1.2rem;"></i>
                Quick Module Launcher
            </h6>
            <div class="row g-2">
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-5">
                    <a href="{{ route('admission.index') }}" class="quick-action-card">
                        <div class="quick-action-icon-box bg-primary bg-opacity-10 text-primary">
                            <i data-lucide="user-plus" style="width: 1.2rem; height: 1.2rem;"></i>
                        </div>
                        <span class="small fw-semibold text-center">Admissions</span>
                    </a>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-5">
                    <a href="{{ route('attendance.index') }}" class="quick-action-card">
                        <div class="quick-action-icon-box bg-success bg-opacity-10 text-success">
                            <i data-lucide="check-square" style="width: 1.2rem; height: 1.2rem;"></i>
                        </div>
                        <span class="small fw-semibold text-center">Attendance</span>
                    </a>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-5">
                    <a href="{{ route('fee-management.index') }}" class="quick-action-card">
                        <div class="quick-action-icon-box bg-warning bg-opacity-10 text-warning">
                            <i data-lucide="banknote" style="width: 1.2rem; height: 1.2rem;"></i>
                        </div>
                        <span class="small fw-semibold text-center">Fees</span>
                    </a>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-5">
                    <a href="{{ route('quran-module.index') }}" class="quick-action-card">
                        <div class="quick-action-icon-box bg-emerald bg-opacity-10 text-success" style="background-color: #ecfdf5; color: #10b981;">
                            <i data-lucide="book-open" style="width: 1.2rem; height: 1.2rem;"></i>
                        </div>
                        <span class="small fw-semibold text-center">Quran Hifz</span>
                    </a>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-5">
                    <a href="{{ route('skills-institute.index') }}" class="quick-action-card">
                        <div class="quick-action-icon-box bg-purple bg-opacity-10 text-purple" style="background-color: #f3e8ff; color: #a855f7;">
                            <i data-lucide="award" style="width: 1.2rem; height: 1.2rem;"></i>
                        </div>
                        <span class="small fw-semibold text-center">Skills Institute</span>
                    </a>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-5">
                    <a href="{{ route('meetings.index') }}" class="quick-action-card">
                        <div class="quick-action-icon-box bg-info bg-opacity-10 text-info">
                            <i data-lucide="calendar" style="width: 1.2rem; height: 1.2rem;"></i>
                        </div>
                        <span class="small fw-semibold text-center">Meetings</span>
                    </a>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-5">
                    <a href="{{ route('visitors.index') }}" class="quick-action-card">
                        <div class="quick-action-icon-box bg-danger bg-opacity-10 text-danger">
                            <i data-lucide="shield-check" style="width: 1.2rem; height: 1.2rem;"></i>
                        </div>
                        <span class="small fw-semibold text-center">Visitors</span>
                    </a>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-1-5">
                    <a href="{{ route('inventory.index') }}" class="quick-action-card">
                        <div class="quick-action-icon-box bg-secondary bg-opacity-10 text-secondary">
                            <i data-lucide="boxes" style="width: 1.2rem; height: 1.2rem;"></i>
                        </div>
                        <span class="small fw-semibold text-center">Inventory</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Row -->
    <div class="row g-4 mb-4">
        <!-- Monthly Revenue Trend Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="trending-up" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Monthly Fee Collection Trend (PKR)
                    </h6>
                    <span class="badge bg-light text-dark border">Last 6 Months</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 280px; position: relative;">
                        <canvas id="feeCollectionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Distribution Breakdown -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="pie-chart" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
                        Student Enrollment by Class
                    </h6>
                    <a href="{{ route('classes.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        @forelse($classDistribution as $cd)
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold text-dark small">{{ $cd->class_name }}</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">{{ $cd->total }} Students</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $perc = $totalStudents > 0 ? min(100, round(($cd->total / $totalStudents) * 100)) : 20;
                                    @endphp
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $perc }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small text-center py-4">No student class data available.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Activity Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Upcoming Scheduled Meetings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="calendar" class="text-info" style="width:1.2rem;height:1.2rem;"></i>
                        Upcoming Staff & PTM Meetings
                    </h6>
                    <a href="{{ route('meetings.index') }}" class="btn btn-sm btn-outline-primary">View All Meetings</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingMeetings as $m)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $m->title }}</h6>
                                    <span class="badge {{ $m->status_badge_class }}">{{ $m->status }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 text-muted small">
                                    <span><i data-lucide="calendar" style="width:0.8rem;height:0.8rem;" class="me-1"></i>{{ $m->meeting_date ? $m->meeting_date->format('M d, Y') : 'N/A' }}</span>
                                    <span><i data-lucide="clock" style="width:0.8rem;height:0.8rem;" class="me-1"></i>{{ $m->formatted_time_range }}</span>
                                    <span><i data-lucide="tag" style="width:0.8rem;height:0.8rem;" class="me-1"></i>{{ $m->meeting_type }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">No upcoming meetings scheduled.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Campus Visitors (Live Gate Entry) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="shield-check" class="text-danger" style="width:1.2rem;height:1.2rem;"></i>
                        Recent Security Gate Visitors
                    </h6>
                    <a href="{{ route('visitors.index') }}" class="btn btn-sm btn-outline-danger">Visitor Gate Register</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-3">Pass</th>
                                    <th>Visitor Name</th>
                                    <th>Purpose</th>
                                    <th>Check-In</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentVisitors as $rv)
                                    <tr>
                                        <td class="ps-3 fw-bold font-monospace small text-primary">{{ $rv->pass_code }}</td>
                                        <td>
                                            <strong class="text-dark d-block mb-0">{{ $rv->visitor_name }}</strong>
                                            <small class="text-muted">{{ $rv->phone }}</small>
                                        </td>
                                        <td class="small text-dark">{{ $rv->purpose }}</td>
                                        <td class="small text-muted">{{ $rv->check_in_time ? $rv->check_in_time->format('g:i A') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $rv->status_badge_class }}">
                                                {{ $rv->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">No recent visitor entries logged.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('feeCollectionChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartMonths) !!},
                datasets: [{
                    label: 'Fee Revenue (PKR)',
                    data: {!! json_encode($chartAmounts) !!},
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) {
                                return 'Rs. ' + val.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
