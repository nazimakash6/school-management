@extends('layouts.app')

@section('title', $event->title . ' - Event Showcase')

@push('styles')
<style>
.event-hero-banner {
    position: relative;
    height: 320px;
    border-radius: 16px;
    overflow: hidden;
    background-size: cover;
    background-position: center;
    color: #ffffff;
}
.event-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.3) 0%, rgba(15, 23, 42, 0.88) 100%);
    padding: 2rem;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
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
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Showcase</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Event Details & Overview</h1>
            <p class="text-muted small mb-0">Official announcement flyer, agenda outline & venue information</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm fw-semibold">
                <i data-lucide="printer" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Print Announcement
            </button>
            <a href="{{ route('events.edit', $event->id) }}" class="btn btn-warning btn-sm fw-semibold">
                <i data-lucide="pencil" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Edit Event
            </a>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Back to Calendar
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

    <!-- Hero Banner Card -->
    <div class="event-hero-banner mb-4 shadow" style="background-image: url('{{ $event->banner_url }}');">
        <div class="event-hero-overlay">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge {{ $event->event_type_badge_class }} px-3 py-2 fs-6">
                    {{ $event->event_type }}
                </span>
                <span class="badge {{ $event->status_badge_class }} px-3 py-2 fs-6">
                    {{ $event->status }}
                </span>
                <span class="badge bg-light text-dark px-3 py-2 fs-6">
                    Target: {{ $event->target_audience }}
                </span>
            </div>

            <h1 class="fw-bold text-white mb-2 display-6">{{ $event->title }}</h1>

            <div class="d-flex flex-wrap gap-4 text-white-50 small">
                <span><i data-lucide="calendar" style="width:1rem;height:1rem;" class="me-1 text-primary"></i> <strong class="text-white">{{ $event->formatted_date_range }}</strong></span>
                <span><i data-lucide="clock" style="width:1rem;height:1rem;" class="me-1 text-info"></i> <strong class="text-white">{{ $event->formatted_time_range }}</strong></span>
                <span><i data-lucide="map-pin" style="width:1rem;height:1rem;" class="me-1 text-danger"></i> <strong class="text-white">{{ $event->location }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-4">
        <!-- Left: Agenda & Description -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="file-text" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Event Description & Program Agenda
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($event->description)
                        <div class="text-dark fs-6" style="line-height: 1.7; whitespace: pre-line;">
                            {{ $event->description }}
                        </div>
                    @else
                        <p class="text-muted italic mb-0">No detailed description has been added for this event yet.</p>
                    @endif
                </div>
            </div>

            <!-- Event Sub-Activities & Competitions Section -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="trophy" class="text-warning" style="width:1.2rem;height:1.2rem;"></i>
                        Event Activities & Inter-House Competitions
                    </h6>
                    <a href="{{ route('event-activities.create', ['event_id' => $event->id]) }}" class="btn btn-primary btn-sm px-3">
                        <i data-lucide="plus" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Add Sub-Activity
                    </a>
                </div>
                <div class="card-body p-4">
                    @if($event->activities && $event->activities->count() > 0)
                        <div class="row g-3">
                            @foreach($event->activities as $act)
                                <div class="col-12">
                                    <div class="card border shadow-none rounded-3 p-3">
                                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-2">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <h6 class="fw-bold text-dark mb-0">{{ $act->name }}</h6>
                                                    <span class="badge {{ $act->category_badge_class }} text-capitalize px-2 py-0.5" style="font-size:0.75rem;">{{ $act->category }}</span>
                                                    <span class="badge {{ $act->status_badge_class }} text-capitalize px-2 py-0.5" style="font-size:0.75rem;">{{ $act->status }}</span>
                                                </div>
                                                @if($act->venue)
                                                    <small class="text-muted"><i data-lucide="map-pin" style="width:0.75rem;height:0.75rem;"></i> {{ $act->venue }}</small>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('event-activities.show', $act->id) }}" class="btn btn-sm btn-outline-info py-1 px-2" title="View Details">
                                                    <i data-lucide="eye" style="width:0.85rem;height:0.85rem;"></i>
                                                </a>
                                                <a href="{{ route('event-activities.edit', $act->id) }}" class="btn btn-sm btn-outline-warning py-1 px-2" title="Edit / Standings">
                                                    <i data-lucide="pencil" style="width:0.85rem;height:0.85rem;"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top mt-2">
                                            <!-- Participating Houses -->
                                            <div>
                                                <span class="text-muted small d-block mb-1">Participating Houses:</span>
                                                @php $pHouses = $act->participating_houses; @endphp
                                                @if($pHouses->isNotEmpty())
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($pHouses as $h)
                                                            <span class="badge bg-light text-dark border px-2 py-1" style="border-left: 3px solid {{ $h->color ?: '#6366f1' }} !important;">
                                                                {{ $h->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted small italic">All Houses</span>
                                                @endif
                                            </div>

                                            <!-- Standings Winner -->
                                            <div>
                                                @if($act->winnerHouse)
                                                    <span class="badge bg-warning text-dark px-2.5 py-1" title="Winner House">
                                                        🏆 Winner: {{ $act->winnerHouse->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">Winner: Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i data-lucide="trophy" class="mb-2" style="width:2rem;height:2rem;"></i>
                            <p class="mb-2 small">No sub-activities or house competitions added for this event yet.</p>
                            <a href="{{ route('event-activities.create', ['event_id' => $event->id]) }}" class="btn btn-sm btn-outline-primary">
                                + Add First Activity
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Event Meta Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="info" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                        Event Specifications
                    </h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                            <span class="text-muted small">Category:</span>
                            <span class="badge {{ $event->event_type_badge_class }}">{{ $event->event_type }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                            <span class="text-muted small">Status:</span>
                            <span class="badge {{ $event->status_badge_class }}">{{ $event->status }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                            <span class="text-muted small">Target Audience:</span>
                            <span class="fw-bold text-dark small">{{ $event->target_audience }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                            <span class="text-muted small">Organizing Department:</span>
                            <span class="fw-bold text-dark small">{{ $event->organizer ?: 'School Admin' }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                            <span class="text-muted small">Venue Location:</span>
                            <span class="fw-bold text-dark small">{{ $event->location }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                            <span class="text-muted small">Allocated Budget:</span>
                            <span class="fw-bold text-warning small">
                                {{ $event->budget_pkr ? 'Rs. ' . number_format($event->budget_pkr) . ' PKR' : 'N/A' }}
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted small">Created By:</span>
                            <span class="text-dark small">{{ $event->creator ? $event->creator->name : 'System Admin' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
