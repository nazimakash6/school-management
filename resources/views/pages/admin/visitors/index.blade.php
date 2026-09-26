@extends('layouts.app')

@section('title', 'Visitor Management System - Security & Gate Entry')

@push('styles')
<style>
.stat-card-visitor {
    border: none;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card-visitor:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
}
.pass-badge {
    font-family: monospace;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.meet-badge {
    font-size: 0.75rem;
    padding: 0.25em 0.65em;
    border-radius: 6px;
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
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Campus Security</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Visitor Management</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="shield-check" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Visitor Management System</h3>
                    <p class="text-muted mb-0 fs-7">Track campus visitors, gate entry/exit logs, student pickup requests & staff meetings</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <a href="{{ route('visitors.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="user-plus" style="width:1rem;height:1rem;"></i> Register New Visitor
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i data-lucide="check-circle-2" class="me-2" style="width:1.2rem;height:1.2rem;"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-visitor p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Inside Campus Now</span>
                        <h2 class="fw-bold text-warning mb-0 mt-1">{{ $currentlyInsideCount }}</h2>
                        <span class="text-muted small">Active Checked-In Pass</span>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                        <i data-lucide="user-check" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-visitor p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Visitors Today</span>
                        <h2 class="fw-bold text-primary mb-0 mt-1">{{ $totalToday }}</h2>
                        <span class="text-muted small">Registered entries</span>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                        <i data-lucide="users" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-visitor p-3 border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Student Pickups / Meetings</span>
                        <h2 class="fw-bold text-info mb-0 mt-1">{{ $studentVisitsToday }}</h2>
                        <span class="text-muted small">Parent / Guardian entries</span>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                        <i data-lucide="graduation-cap" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-visitor p-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Staff & Official Visits</span>
                        <h2 class="fw-bold text-success mb-0 mt-1">{{ $staffVisitsToday }}</h2>
                        <span class="text-muted small">Vendors, Admins & Guests</span>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                        <i data-lucide="briefcase" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('visitors.index') }}" class="row g-2 align-items-center">
                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Campus Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Checked-In" {{ request('status') == 'Checked-In' ? 'selected' : '' }}>Checked-In (Inside)</option>
                        <option value="Checked-Out" {{ request('status') == 'Checked-Out' ? 'selected' : '' }}>Checked-Out (Left)</option>
                        <option value="Blocked" {{ request('status') == 'Blocked' ? 'selected' : '' }}>Blocked / Denied</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Meet Target</label>
                    <select name="meet_type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        @foreach($meetTypes as $mt)
                            <option value="{{ $mt }}" {{ request('meet_type') == $mt ? 'selected' : '' }}>{{ $mt }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Purpose of Visit</label>
                    <select name="purpose" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Purposes</option>
                        @foreach($purposes as $p)
                            <option value="{{ $p }}" {{ request('purpose') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Search Keyword</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, CNIC, pass code, student..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-1 text-end mt-4">
                    @if(request()->anyFilled(['status', 'meet_type', 'purpose', 'search', 'date']))
                        <a href="{{ route('visitors.index') }}" class="btn btn-sm btn-light border text-danger" title="Clear Filters">
                            <i data-lucide="rotate-ccw" style="width:0.9rem;height:0.9rem;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Visitors Register Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="shield-check" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                Visitor Gate Log Register
            </h6>
            <span class="badge bg-light text-dark border">
                Showing {{ $visitors->count() }} of {{ $visitors->total() }} Records
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-semibold">
                        <tr>
                            <th class="ps-4">Pass Code</th>
                            <th>Visitor Details</th>
                            <th>To Meet</th>
                            <th>Purpose & Gate</th>
                            <th>Check-In / Check-Out</th>
                            <th>Status & Duration</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitors as $v)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-dark text-white pass-badge">
                                        {{ $v->pass_code }}
                                    </span>
                                    <div class="text-muted small mt-1">
                                        <i data-lucide="users" style="width:0.75rem;height:0.75rem;" class="me-1"></i>{{ $v->num_persons }} Person(s)
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('visitors.show', $v->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                        {{ $v->visitor_name }}
                                    </a>
                                    <div class="text-muted small">
                                        <i data-lucide="phone" style="width:0.75rem;height:0.75rem;" class="me-1"></i>{{ $v->phone }}
                                        @if($v->cnic_id)
                                            <span class="mx-1">&bull;</span> CNIC: {{ $v->cnic_id }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $v->meet_type_badge_class }} meet-badge mb-1">
                                        {{ $v->meet_type }}
                                    </span>
                                    @if($v->meet_type === 'Student' && $v->student)
                                        <div class="fw-semibold text-dark small">
                                            <i data-lucide="graduation-cap" style="width:0.8rem;height:0.8rem;" class="text-primary me-1"></i>
                                            {{ $v->student->first_name }} {{ $v->student->last_name }} (Roll #{{ $v->student->roll_no }})
                                        </div>
                                    @elseif($v->meet_type === 'Staff' && $v->staff)
                                        <div class="fw-semibold text-dark small">
                                            <i data-lucide="user" style="width:0.8rem;height:0.8rem;" class="text-info me-1"></i>
                                            {{ $v->staff->first_name }} {{ $v->staff->last_name }} ({{ $v->staff->designation }})
                                        </div>
                                    @else
                                        <div class="text-muted small">{{ $v->person_to_meet ?: 'General Inquiry' }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $v->purpose }}</div>
                                    <small class="text-muted"><i data-lucide="door-open" style="width:0.75rem;height:0.75rem;" class="me-1"></i>{{ $v->gate_no }}</small>
                                    @if($v->vehicle_no)
                                        <div class="text-primary small" style="font-size:0.75rem;"><i data-lucide="car" style="width:0.75rem;height:0.75rem;" class="me-1"></i>{{ $v->vehicle_no }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">
                                        <i data-lucide="log-in" style="width:0.8rem;height:0.8rem;" class="text-success me-1"></i>
                                        In: {{ $v->check_in_time ? $v->check_in_time->format('g:i A') : 'N/A' }}
                                    </div>
                                    <div class="text-muted small">
                                        <i data-lucide="log-out" style="width:0.8rem;height:0.8rem;" class="text-danger me-1"></i>
                                        Out: {{ $v->check_out_time ? $v->check_out_time->format('g:i A') : 'Still Inside' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $v->status_badge_class }} px-3 py-1 rounded-pill mb-1 d-inline-block">
                                        {{ $v->status }}
                                    </span>
                                    <div class="text-muted small" style="font-size:0.75rem;">
                                        <i data-lucide="clock" style="width:0.75rem;height:0.75rem;" class="me-1"></i>{{ $v->duration_text }}
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        @if($v->status === 'Checked-In')
                                            <form action="{{ route('visitors.check-out', $v->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success fw-bold me-1" title="Instant Check-Out">
                                                    <i data-lucide="log-out" style="width:0.85rem;height:0.85rem;" class="me-1"></i> Check Out
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('visitors.show', $v->id) }}" class="btn btn-light text-primary" title="View Pass & Print Receipt">
                                            <i data-lucide="id-card" style="width:0.9rem;height:0.9rem;"></i>
                                        </a>
                                        <a href="{{ route('visitors.edit', $v->id) }}" class="btn btn-light text-secondary" title="Edit Entry">
                                            <i data-lucide="edit-3" style="width:0.9rem;height:0.9rem;"></i>
                                        </a>
                                        <form action="{{ route('visitors.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this visitor record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light text-danger" title="Delete">
                                                <i data-lucide="trash-2" style="width:0.9rem;height:0.9rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i data-lucide="shield-alert" style="width:3rem;height:3rem;" class="text-muted opacity-50"></i>
                                    </div>
                                    <h6>No Visitor Entries Found</h6>
                                    <p class="small mb-3">Register new visitor entry at the security gate.</p>
                                    <a href="{{ route('visitors.create') }}" class="btn btn-primary btn-sm">
                                        <i data-lucide="user-plus" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Register Visitor
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($visitors->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        Showing {{ $visitors->firstItem() }} to {{ $visitors->lastItem() }} of {{ $visitors->total() }} entries
                    </span>
                    {{ $visitors->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
