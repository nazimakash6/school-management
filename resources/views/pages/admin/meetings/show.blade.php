@extends('layouts.app')

@section('title', 'Meeting Details - ' . $meeting->title)

@push('styles')
<style>
.meeting-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    color: #ffffff;
}
.mom-box {
    background: #f8fafc;
    border-left: 4px solid #3b82f6;
    border-radius: 8px;
    padding: 1.25rem;
}
.action-box {
    background: #fffbebf5;
    border-left: 4px solid #f59e0b;
    border-radius: 8px;
    padding: 1.25rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Meeting Details</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Meeting & Conference Details</h1>
            <p class="text-muted small mb-0">Agenda, Minutes of Meeting (MoM), Action items, & Attendee roster</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#momModal">
                <i data-lucide="edit-3" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Record Minutes (MoM)
            </button>
            <a href="{{ route('meetings.edit', $meeting->id) }}" class="btn btn-outline-warning btn-sm">
                <i data-lucide="pencil" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Edit Meeting
            </a>
            <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Back to Catalog
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

    <!-- Hero Card -->
    <div class="meeting-hero p-4 mb-4 shadow">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary bg-opacity-25 text-white px-3 py-1 rounded-pill">
                        {{ $meeting->meeting_type }}
                    </span>
                    <span class="badge {{ $meeting->mode_badge_class }} px-3 py-1">
                        {{ $meeting->mode }}
                    </span>
                    <span class="badge {{ $meeting->status_badge_class }} px-3 py-1 rounded-pill">
                        {{ $meeting->status }}
                    </span>
                </div>

                <h2 class="fw-bold text-white mb-2">{{ $meeting->title }}</h2>

                <div class="text-white-50 small d-flex flex-wrap gap-4 mt-3">
                    <span><i data-lucide="calendar" style="width:1rem;height:1rem;" class="me-1"></i> <strong>{{ $meeting->meeting_date ? $meeting->meeting_date->format('l, F d, Y') : 'N/A' }}</strong></span>
                    <span><i data-lucide="clock" style="width:1rem;height:1rem;" class="me-1"></i> <strong>{{ $meeting->formatted_time_range }}</strong></span>
                    <span><i data-lucide="users" style="width:1rem;height:1rem;" class="me-1"></i> <strong>{{ $meeting->target_audience }}</strong></span>
                </div>
            </div>

            <div class="col-lg-4 text-lg-end">
                @if($meeting->meeting_link)
                    <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-info btn-lg fw-bold text-dark me-2 mb-2 shadow">
                        <i data-lucide="video" style="width:1.2rem;height:1.2rem;" class="me-1"></i> Join Virtual Meeting
                    </a>
                @elseif($meeting->location)
                    <div class="p-3 bg-white bg-opacity-10 rounded-3 d-inline-block text-start">
                        <span class="text-white-50 small d-block">Physical Venue</span>
                        <strong class="text-white fs-6"><i data-lucide="map-pin" style="width:1rem;height:1rem;" class="text-warning me-1"></i>{{ $meeting->location }}</strong>
                    </div>
                @endif

                @if($meeting->attachment_url)
                    <div class="mt-2">
                        <a href="{{ $meeting->attachment_url }}" target="_blank" class="btn btn-outline-light btn-sm">
                            <i data-lucide="paperclip" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Agenda Attachment PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 text-center">
                <span class="text-muted small text-uppercase">Total Invited</span>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalInvited }}</h3>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 text-center border-start border-success border-4">
                <span class="text-muted small text-uppercase">Attended</span>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ $attendedCount }}</h3>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 text-center border-start border-info border-4">
                <span class="text-muted small text-uppercase">Excused</span>
                <h3 class="fw-bold text-info mb-0 mt-1">{{ $excusedCount }}</h3>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 text-center border-start border-danger border-4">
                <span class="text-muted small text-uppercase">Absent</span>
                <h3 class="fw-bold text-danger mb-0 mt-1">{{ $absentCount }}</h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Side: Agenda, MoM & Action Items -->
        <div class="col-lg-7">
            <!-- Agenda Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="list-checks" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Meeting Agenda & Outline
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($meeting->agenda)
                        <div class="text-dark" style="white-space: pre-line;">{{ $meeting->agenda }}</div>
                    @else
                        <p class="text-muted small mb-0">No specific agenda outline provided.</p>
                    @endif
                </div>
            </div>

            <!-- Minutes of Meeting (MoM) Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="file-text" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Minutes of Meeting (MoM)
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#momModal">
                        <i data-lucide="edit" style="width:0.85rem;height:0.85rem;" class="me-1"></i> Edit MoM
                    </button>
                </div>
                <div class="card-body p-4">
                    @if($meeting->minutes_of_meeting)
                        <div class="mom-box">
                            <div class="text-dark" style="white-space: pre-line;">{{ $meeting->minutes_of_meeting }}</div>
                        </div>
                    @else
                        <div class="p-3 bg-light text-center rounded-3">
                            <p class="text-muted small mb-2">No Minutes of Meeting (MoM) recorded yet.</p>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#momModal">
                                Record Meeting Minutes
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Items Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="check-square" class="text-warning" style="width:1.2rem;height:1.2rem;"></i>
                        Action Items & Key Tasks
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($meeting->action_items)
                        <div class="action-box">
                            <div class="text-dark" style="white-space: pre-line;">{{ $meeting->action_items }}</div>
                        </div>
                    @else
                        <p class="text-muted small mb-0">No specific action items assigned.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side: Attendees List & Status -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="users" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Invited Attendees Roster ({{ $meeting->attendees->count() }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-3">Name & Role</th>
                                    <th>Status</th>
                                    <th class="pe-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($meeting->attendees as $att)
                                    <tr>
                                        <td class="ps-3">
                                            <strong class="text-dark d-block mb-0">{{ $att->name }}</strong>
                                            <small class="text-muted">{{ $att->role ?: 'Staff Member' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $att->status_badge_class }}">
                                                {{ $att->attendance_status }}
                                            </span>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle py-0 px-2" type="button" data-bs-toggle="dropdown">
                                                    Mark
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end small">
                                                    <li>
                                                        <form action="{{ route('meetings.update-attendee', [$meeting->id, $att->id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="attendance_status" value="Attended">
                                                            <button type="submit" class="dropdown-item text-success"><i data-lucide="check" style="width:0.8rem;height:0.8rem;" class="me-1"></i> Attended</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('meetings.update-attendee', [$meeting->id, $att->id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="attendance_status" value="Excused">
                                                            <button type="submit" class="dropdown-item text-info"><i data-lucide="info" style="width:0.8rem;height:0.8rem;" class="me-1"></i> Excused</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('meetings.update-attendee', [$meeting->id, $att->id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="attendance_status" value="Absent">
                                                            <button type="submit" class="dropdown-item text-danger"><i data-lucide="x" style="width:0.8rem;height:0.8rem;" class="me-1"></i> Absent</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted small">No specific attendees added.</td>
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

<!-- Modal: Record Minutes of Meeting (MoM) -->
<div class="modal fade" id="momModal" tabindex="-1" aria-labelledby="momModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('meetings.update-minutes', $meeting->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark" id="momModalLabel">
                        <i data-lucide="file-text" class="text-primary me-1"></i> Record Minutes of Meeting (MoM)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-content-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Meeting Status</label>
                        <select name="status" class="form-select">
                            <option value="Completed" {{ $meeting->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="In-Progress" {{ $meeting->status == 'In-Progress' ? 'selected' : '' }}>In-Progress</option>
                            <option value="Scheduled" {{ $meeting->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="Postponed" {{ $meeting->status == 'Postponed' ? 'selected' : '' }}>Postponed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Minutes of Meeting (MoM)</label>
                        <textarea name="minutes_of_meeting" class="form-control" rows="5" placeholder="Enter key decisions made, discussions summarized, and notes recorded during the meeting...">{{ old('minutes_of_meeting', $meeting->minutes_of_meeting) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Action Items & Follow-up Tasks</label>
                        <textarea name="action_items" class="form-control" rows="3" placeholder="List tasks assigned with responsible staff names and deadlines...">{{ old('action_items', $meeting->action_items) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save MoM & Action Items</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
