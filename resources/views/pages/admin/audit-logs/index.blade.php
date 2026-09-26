@extends('layouts.app')

@section('title', 'System Audit Logs — EduCore ERP')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Reports &amp; Settings</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Audit Logs</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="activity" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">System Audit & Activity Logs</h3>
                    <p class="text-muted mb-0 fs-7">Real-time recording of administrator activities, logins, exam changes & security events</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex align-items-center gap-2">
            <a href="{{ route('audit-logs.export') }}" class="btn btn-outline-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="download" style="width:1rem;height:1rem;"></i>
                <span>Export CSV</span>
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                <i data-lucide="trash-2" style="width:1rem;height:1rem;"></i>
                <span>Clear Logs</span>
            </button>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i data-lucide="check-circle" class="text-success icon-md"></i>
            <div>{{ session('status') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small">Total Activity Today</div>
                        <div class="h3 font-weight-bold text-dark mb-0 mt-1">{{ number_format($totalLogsToday) }}</div>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-3">
                        <i data-lucide="activity" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small">Auth Logins</div>
                        <div class="h3 font-weight-bold text-dark mb-0 mt-1">{{ number_format($authEventsCount) }}</div>
                    </div>
                    <div class="p-3 bg-info-subtle text-info rounded-3">
                        <i data-lucide="user-check" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small">Security Events</div>
                        <div class="h3 font-weight-bold text-dark mb-0 mt-1">{{ number_format($securityEventsCount) }}</div>
                    </div>
                    <div class="p-3 bg-danger-subtle text-danger rounded-3">
                        <i data-lucide="shield-alert" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small">Total Records</div>
                        <div class="h3 font-weight-bold text-dark mb-0 mt-1">{{ number_format($logs->total()) }}</div>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-3">
                        <i data-lucide="database" class="icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('audit-logs.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-secondary">Event Category</label>
                    <select class="form-select form-select-sm" name="event_type">
                        <option value="all" {{ $eventType == 'all' || !$eventType ? 'selected' : '' }}>— All Event Types —</option>
                        <option value="auth" {{ $eventType == 'auth' ? 'selected' : '' }}>Auth &amp; Login</option>
                        <option value="examination" {{ $eventType == 'examination' ? 'selected' : '' }}>Examinations</option>
                        <option value="admission" {{ $eventType == 'admission' ? 'selected' : '' }}>Admissions</option>
                        <option value="fee" {{ $eventType == 'fee' ? 'selected' : '' }}>Fee &amp; Payments</option>
                        <option value="settings" {{ $eventType == 'settings' ? 'selected' : '' }}>Settings</option>
                        <option value="security" {{ $eventType == 'security' ? 'selected' : '' }}>Security</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small text-secondary">User Account</label>
                    <select class="form-select form-select-sm" name="user_id">
                        <option value="all" {{ $userId == 'all' || !$userId ? 'selected' : '' }}>— All Users —</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small text-secondary">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ $dateFrom }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small text-secondary">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ $dateTo }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="search" class="icon-xs"></i> Filter
                    </button>
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-light border" title="Reset Filters">
                        <i data-lucide="rotate-ccw" class="icon-xs"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 170px;">Timestamp</th>
                        <th>User</th>
                        <th>Event Category</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $badgeClass = 'bg-secondary-subtle text-secondary';
                            if ($log->event_type === 'auth') $badgeClass = 'bg-info-subtle text-info';
                            elseif ($log->event_type === 'examination') $badgeClass = 'bg-primary-subtle text-primary';
                            elseif ($log->event_type === 'admission') $badgeClass = 'bg-success-subtle text-success';
                            elseif ($log->event_type === 'fee') $badgeClass = 'bg-warning-subtle text-warning';
                            elseif ($log->event_type === 'security') $badgeClass = 'bg-danger-subtle text-danger';
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark small">{{ $log->created_at->format('d M, Y') }}</div>
                                <div class="text-muted text-xs">{{ $log->created_at->format('h:i:s A') }}</div>
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="fw-bold text-dark small">{{ $log->user->name }}</div>
                                    <div class="text-muted text-xs">{{ $log->user->role ?: 'User' }}</div>
                                @else
                                    <span class="badge bg-light text-muted border">System / Guest</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 fw-bold text-uppercase fs-8">
                                    {{ $log->event_type }}
                                </span>
                            </td>
                            <td><span class="font-monospace small fw-bold text-dark">{{ $log->action }}</span></td>
                            <td style="max-width: 320px;">
                                <div class="text-truncate text-secondary small" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace text-muted small"><i data-lucide="globe" class="icon-xs me-1"></i>{{ $log->ip_address }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('audit-logs.show', $log->id) }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1">
                                    <i data-lucide="eye" class="icon-xs"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i data-lucide="clipboard-list" class="icon-xl text-muted opacity-50 mb-2"></i>
                                <h6>No Audit Logs Found</h6>
                                <p class="small mb-0">No system activities match your selected search criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i data-lucide="alert-triangle" class="icon-md"></i> Clear Old Audit Logs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('audit-logs.clear') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <p class="text-secondary mb-3">Select the age threshold for clearing older audit log entries. This action cannot be undone.</p>

                    <label class="form-label fw-bold small text-secondary">Clear logs older than:</label>
                    <select class="form-select" name="days">
                        <option value="7">7 Days</option>
                        <option value="30" selected>30 Days</option>
                        <option value="90">90 Days</option>
                        <option value="0">All Historical Logs</option>
                    </select>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4">Clear Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
