@extends('layouts.app')

@section('title', 'Edit Meeting - ' . $meeting->title)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Meeting</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Edit Meeting Record</h1>
            <p class="text-muted small mb-0">Update schedule, agenda, minutes of meeting, and status</p>
        </div>
        <div>
            <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Meetings
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i data-lucide="alert-triangle" class="me-1" style="width:1rem;height:1rem;"></i> Validation Errors</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('meetings.update', $meeting->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Form Area -->
            <div class="col-lg-8">
                <!-- 1. General Meeting Details Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="calendar" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            1. Basic Meeting Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Meeting Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $meeting->title) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Meeting Type <span class="text-danger">*</span></label>
                                <select name="meeting_type" class="form-select @error('meeting_type') is-invalid @enderror" required>
                                    @foreach($types as $t)
                                        <option value="{{ $t }}" {{ old('meeting_type', $meeting->meeting_type) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Meeting Mode <span class="text-danger">*</span></label>
                                <select name="mode" class="form-select @error('mode') is-invalid @enderror" required>
                                    @foreach($modes as $m)
                                        <option value="{{ $m }}" {{ old('mode', $meeting->mode) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Meeting Date <span class="text-danger">*</span></label>
                                <input type="date" name="meeting_date" class="form-control @error('meeting_date') is-invalid @enderror" value="{{ old('meeting_date', $meeting->meeting_date ? $meeting->meeting_date->format('Y-m-d') : '') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $meeting->start_time) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">End Time</label>
                                <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', $meeting->end_time) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Venue / Room Location</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $meeting->location) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Virtual Meeting Link (Zoom / Meet)</label>
                                <input type="url" name="meeting_link" class="form-control" value="{{ old('meeting_link', $meeting->meeting_link) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Target Audience / Department <span class="text-danger">*</span></label>
                                <input type="text" name="target_audience" class="form-control @error('target_audience') is-invalid @enderror" value="{{ old('target_audience', $meeting->target_audience) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach($statuses as $st)
                                        <option value="{{ $st }}" {{ old('status', $meeting->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Agenda, Minutes of Meeting & Action Items Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="file-text" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            2. Agenda, Minutes of Meeting (MoM) & Action Items
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Meeting Agenda & Discussion Topics</label>
                            <textarea name="agenda" class="form-control" rows="4">{{ old('agenda', $meeting->agenda) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Minutes of Meeting (MoM)</label>
                            <textarea name="minutes_of_meeting" class="form-control" rows="4" placeholder="Record summary of decisions and discussions after meeting completion...">{{ old('minutes_of_meeting', $meeting->minutes_of_meeting) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Action Items & Tasks Assigned</label>
                            <textarea name="action_items" class="form-control" rows="3" placeholder="List action items and assigned deadlines...">{{ old('action_items', $meeting->action_items) }}</textarea>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-dark">Replace Attachment / Agenda Document</label>
                            @if($meeting->attachment_url)
                                <div class="mb-2">
                                    <a href="{{ $meeting->attachment_url }}" target="_blank" class="btn btn-sm btn-light border">
                                        <i data-lucide="paperclip" style="width:0.9rem;height:0.9rem;" class="me-1"></i> View Current Document
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.png,.jpg,.webp">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Action Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0">Save Changes</h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small">Update meeting agenda, status, or minutes of meeting.</p>
                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i data-lucide="save" style="width:1.25rem;height:1.25rem;"></i> Save Meeting Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
