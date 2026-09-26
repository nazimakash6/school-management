@extends('layouts.app')

@section('title', 'Meetings Management - Staff, PTM & Council Meetings')

@push('styles')
<style>
.stat-card-meeting {
    border: none;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card-meeting:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
}
.type-badge {
    font-weight: 600;
    font-size: 0.75rem;
    padding: 0.3em 0.75em;
    border-radius: 50rem;
}
.mode-badge {
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
                    <li class="breadcrumb-item text-muted">Communication</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Meetings Hub</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="video" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Meetings & Conferences Hub</h3>
                    <p class="text-muted mb-0 fs-7">Schedule staff meetings, parent-teacher conferences, MoM, & action items</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <a href="{{ route('meetings.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="calendar-plus" style="width:1rem;height:1rem;"></i> Schedule Meeting
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
            <div class="stat-card-meeting p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Upcoming Scheduled</span>
                        <h2 class="fw-bold text-primary mb-0 mt-1">{{ $upcomingCount }}</h2>
                        <span class="text-muted small">Next meetings</span>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                        <i data-lucide="calendar-clock" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-meeting p-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Completed (This Month)</span>
                        <h2 class="fw-bold text-success mb-0 mt-1">{{ $completedThisMonth }}</h2>
                        <span class="text-muted small">Minutes recorded</span>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                        <i data-lucide="check-circle-2" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-meeting p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Staff Meetings</span>
                        <h2 class="fw-bold text-dark mb-0 mt-1">{{ $staffMeetingsCount }}</h2>
                        <span class="text-muted small">Internal reviews</span>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                        <i data-lucide="users" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-meeting p-3 border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">PTM Conferences</span>
                        <h2 class="fw-bold text-info mb-0 mt-1">{{ $ptmCount }}</h2>
                        <span class="text-muted small">Parent-Teacher</span>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                        <i data-lucide="message-square-code" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('meetings.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Meeting Type</label>
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Mode</label>
                    <select name="mode" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Modes</option>
                        @foreach($modes as $m)
                            <option value="{{ $m }}" {{ request('mode') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Search Keyword</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search by title, location, agenda..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-1 text-end mt-4">
                    @if(request()->anyFilled(['type', 'status', 'mode', 'search', 'date']))
                        <a href="{{ route('meetings.index') }}" class="btn btn-sm btn-light border text-danger" title="Clear Filters">
                            <i data-lucide="rotate-ccw" style="width:0.9rem;height:0.9rem;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Meetings Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="calendar" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                Scheduled & Conducted Meetings
            </h6>
            <span class="badge bg-light text-dark border">
                Showing {{ $meetings->count() }} of {{ $meetings->total() }} Meetings
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-semibold">
                        <tr>
                            <th class="ps-4" style="width: 40px;">#</th>
                            <th>Meeting Title & Category</th>
                            <th>Date & Time</th>
                            <th>Mode & Venue</th>
                            <th>Target Audience</th>
                            <th>Attendees</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meetings as $m)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">{{ $loop->iteration + ($meetings->currentPage() - 1) * $meetings->perPage() }}</td>
                                <td>
                                    <a href="{{ route('meetings.show', $m->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                        {{ $m->title }}
                                    </a>
                                    <span class="badge bg-primary bg-opacity-10 text-primary type-badge mt-1">
                                        {{ $m->meeting_type }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $m->meeting_date ? $m->meeting_date->format('M d, Y') : 'N/A' }}</div>
                                    <small class="text-muted"><i data-lucide="clock" style="width:0.8rem;height:0.8rem;" class="me-1"></i>{{ $m->formatted_time_range }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $m->mode_badge_class }} mode-badge d-inline-block mb-1">
                                        {{ $m->mode }}
                                    </span>
                                    <div class="text-muted small">
                                        @if($m->meeting_link)
                                            <a href="{{ $m->meeting_link }}" target="_blank" class="text-primary text-decoration-none me-1">
                                                <i data-lucide="video" style="width:0.8rem;height:0.8rem;"></i> Virtual Link
                                            </a>
                                        @else
                                            <i data-lucide="map-pin" style="width:0.8rem;height:0.8rem;" class="me-1"></i>{{ $m->location ?: 'Main Campus' }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark small fw-semibold">{{ $m->target_audience }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                        <i data-lucide="users" style="width:0.8rem;height:0.8rem;" class="me-1"></i>
                                        {{ $m->attendees->count() }} Invited
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $m->status_badge_class }} px-3 py-1 rounded-pill">
                                        {{ $m->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('meetings.show', $m->id) }}" class="btn btn-light text-primary" title="View Details, MoM & Attendees">
                                            <i data-lucide="eye" style="width:0.9rem;height:0.9rem;"></i>
                                        </a>
                                        <a href="{{ route('meetings.edit', $m->id) }}" class="btn btn-light text-secondary" title="Edit Meeting">
                                            <i data-lucide="edit-3" style="width:0.9rem;height:0.9rem;"></i>
                                        </a>
                                        <form action="{{ route('meetings.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this meeting?')">
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
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i data-lucide="calendar-x" style="width:3rem;height:3rem;" class="text-muted opacity-50"></i>
                                    </div>
                                    <h6>No Meeting Records Found</h6>
                                    <p class="small mb-3">Schedule a new staff meeting or parent-teacher conference.</p>
                                    <a href="{{ route('meetings.create') }}" class="btn btn-primary btn-sm">
                                        <i data-lucide="plus" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Schedule Meeting
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($meetings->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        Showing {{ $meetings->firstItem() }} to {{ $meetings->lastItem() }} of {{ $meetings->total() }} entries
                    </span>
                    {{ $meetings->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
