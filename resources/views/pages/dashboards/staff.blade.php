@extends('layouts.app')

@section('title', 'Staff Workspace - EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Home</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Staff Workspace</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                    <i data-lucide="briefcase" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Administrative & Operations Staff Workspace</h3>
                    <p class="text-muted mb-0 fs-7">Welcome Staff! Manage visitor entries, inventory, fee collections, and front-desk logbooks.</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('visitors.index') }}" class="btn btn-info text-white btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none;">
                <i data-lucide="user-check" style="width:1rem;height:1rem;"></i> Visitor Check-In
            </a>
            <a href="{{ route('fee-management.index') }}" class="btn btn-outline-info btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="banknote" style="width:1rem;height:1rem;"></i> Fee Vouchers
            </a>
        </div>
    </div>

    <!-- Staff KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Visitors Checked-In</span>
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                        <i data-lucide="shield-check" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ $visitorsInsideCount }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Currently On Campus</span>
                    <a href="{{ route('visitors.index') }}" class="text-info text-decoration-none fw-semibold">Gate Registry &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Monthly Fee Collections</span>
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                        <i data-lucide="wallet" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">Rs. {{ number_format($feeCollectionThisMonth) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Collected Receipts</span>
                    <a href="{{ route('fee-management.index') }}" class="text-warning text-decoration-none fw-semibold">Fee Desk &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Students Enrolled</span>
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                        <i data-lucide="users" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">{{ number_format($totalStudents) }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Active Student Records</span>
                    <a href="{{ route('student-list.index') }}" class="text-primary text-decoration-none fw-semibold">Lookup &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-secondary border-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Campus Inventory & Stock</span>
                    <div class="bg-secondary bg-opacity-10 p-2 rounded-3 text-secondary">
                        <i data-lucide="boxes" style="width: 1.4rem; height: 1.4rem;"></i>
                    </div>
                </div>
                <h2 class="fw-bold text-dark mb-1">Active</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Store Items & Assets</span>
                    <a href="{{ route('inventory.index') }}" class="text-secondary text-decoration-none fw-semibold">Inventory &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Security Gate Log -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="shield-check" class="text-info" style="width:1.2rem;height:1.2rem;"></i>
                Recent Campus Visitor Logs
            </h6>
            <a href="{{ route('visitors.index') }}" class="btn btn-sm btn-outline-info">Open Visitor Console</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="ps-3">Pass Code</th>
                            <th>Visitor Name</th>
                            <th>Purpose</th>
                            <th>Check-In Time</th>
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
@endsection
