@extends('layouts.app')

@section('title', $eventActivity->name . ' - Activity Details')

@section('content')
<div class="container-fluid px-4 py-3">
  <!-- Header -->
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
          <li class="breadcrumb-item"><a href="{{ route('events.show', $eventActivity->event_id) }}">{{ $eventActivity->event?->title }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $eventActivity->name }}</li>
        </ol>
      </nav>
      <h1 class="h3 fw-bold text-dark mb-0">{{ $eventActivity->name }}</h1>
      <p class="text-muted small mb-0">Category: {{ $eventActivity->category }} | Parent Event: {{ $eventActivity->event?->title }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('event-activities.edit', $eventActivity->id) }}" class="btn btn-warning btn-sm fw-semibold">
        <i data-lucide="pencil" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Edit Activity & Results
      </a>
      <a href="{{ route('event-activities.index', ['event_id' => $eventActivity->event_id]) }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Back to Activities
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

  <div class="row g-4">
    <div class="col-lg-8">
      <!-- Overview Card -->
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="trophy" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
            Activity Overview
          </h6>
          <span class="badge {{ $eventActivity->status_badge_class }} px-3 py-1">
            {{ $eventActivity->status }}
          </span>
        </div>
        <div class="card-body p-4">
          <div class="row g-4">
            <div class="col-md-6">
              <span class="text-muted small d-block">Parent Event</span>
              <a href="{{ route('events.show', $eventActivity->event_id) }}" class="fw-bold text-primary text-decoration-none fs-6">
                {{ $eventActivity->event?->title }}
              </a>
            </div>

            <div class="col-md-6">
              <span class="text-muted small d-block">Activity Category</span>
              <span class="badge {{ $eventActivity->category_badge_class }} px-3 py-1 fs-6">
                {{ $eventActivity->category }}
              </span>
            </div>

            <div class="col-md-6">
              <span class="text-muted small d-block">Venue / Location</span>
              <span class="fw-bold text-dark fs-6">
                <i data-lucide="map-pin" style="width:1rem;height:1rem;" class="me-1 text-danger"></i>
                {{ $eventActivity->venue ?: 'TBD' }}
              </span>
            </div>

            <div class="col-md-6">
              <span class="text-muted small d-block">Scheduled Date & Time</span>
              <span class="fw-bold text-dark fs-6">
                <i data-lucide="calendar" style="width:1rem;height:1rem;" class="me-1 text-info"></i>
                {{ $eventActivity->activity_date ? $eventActivity->activity_date->format('M d, Y') : ($eventActivity->event?->formatted_date_range ?: 'TBD') }}
                @if($eventActivity->start_time)
                  ({{ $eventActivity->start_time }} {{ $eventActivity->end_time ? '- ' . $eventActivity->end_time : '' }})
                @endif
              </span>
            </div>

            <!-- Participating Houses -->
            <div class="col-12">
              <hr>
              <h6 class="fw-bold text-dark mb-2">Participating Houses</h6>
              @php $pHouses = $eventActivity->participating_houses; @endphp
              @if($pHouses->isNotEmpty())
                <div class="d-flex flex-wrap gap-2">
                  @foreach($pHouses as $house)
                    <div class="card border p-2 px-3 shadow-sm d-flex flex-row align-items-center gap-2" style="border-left: 4px solid {{ $house->color ?: '#6366f1' }} !important;">
                      <div>
                        <div class="fw-bold text-dark small">{{ $house->name }}</div>
                        <span class="text-muted" style="font-size:0.75rem;">Code: {{ $house->code }}</span>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <p class="text-muted small italic">All houses participating / Open Activity.</p>
              @endif
            </div>

            <!-- Rules & Notes -->
            <div class="col-12">
              <hr>
              <h6 class="fw-bold text-dark mb-2">Rules, Guidelines & Notes</h6>
              <div class="bg-light p-3 rounded border text-secondary" style="white-space: pre-line;">
                {{ $eventActivity->rules_notes ?: 'No specific rules or notes set for this activity.' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- House Winners & Standings -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
          <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="award" class="text-warning" style="width:1.2rem;height:1.2rem;"></i>
            House Standings & Results
          </h6>
        </div>
        <div class="card-body p-4">
          <div class="d-flex flex-column gap-3">
            <div class="p-3 border rounded-3 bg-warning-subtle text-dark border-warning">
              <span class="small fw-semibold text-uppercase d-block text-warning-emphasis">🥇 1st Place (Winner)</span>
              <h5 class="fw-bold mb-0 mt-1">
                {{ $eventActivity->winnerHouse ? $eventActivity->winnerHouse->name : 'Not Announced' }}
              </h5>
            </div>

            <div class="p-3 border rounded-3 bg-light text-dark">
              <span class="small fw-semibold text-uppercase d-block text-secondary">🥈 2nd Place (Runner-Up)</span>
              <h5 class="fw-bold mb-0 mt-1">
                {{ $eventActivity->runnerUpHouse ? $eventActivity->runnerUpHouse->name : 'Not Announced' }}
              </h5>
            </div>

            <div class="p-3 border rounded-3 bg-light text-dark">
              <span class="small fw-semibold text-uppercase d-block text-danger-emphasis">🥉 3rd Place</span>
              <h5 class="fw-bold mb-0 mt-1">
                {{ $eventActivity->thirdPlaceHouse ? $eventActivity->thirdPlaceHouse->name : 'Not Announced' }}
              </h5>
            </div>
          </div>
          <div class="mt-4">
            <a href="{{ route('event-activities.edit', $eventActivity->id) }}" class="btn btn-outline-primary w-100 btn-sm fw-semibold">
              <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Update Results
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
