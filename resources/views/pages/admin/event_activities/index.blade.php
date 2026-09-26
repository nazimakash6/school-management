@extends('layouts.app')

@section('title', 'Event Activities')

@section('content')
<div class="container-fluid px-0">
  <div class="content-header mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div>
      <h1 class="page-title h3 fw-bold mb-1">Event Activities & Competitions</h1>
      <p class="page-subtitle text-muted mb-0">Manage sub-activities, inter-house competitions, participating houses, and standings</p>
    </div>
    <div class="content-header-actions d-flex gap-2">
      <a href="{{ route('event-categories.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i data-lucide="tag" style="width:1rem;height:1rem;" class="me-1"></i> Categories
      </a>
      <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i data-lucide="calendar" style="width:1rem;height:1rem;" class="me-1"></i> Events Calendar
      </a>
      <a href="{{ route('event-activities.create', ['event_id' => request('event_id')]) }}" class="btn btn-primary btn-sm px-3">
        <i data-lucide="plus" style="width:1rem;height:1rem;" class="me-1"></i> Add Activity
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
      <i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;" class="me-2"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Filter Card -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('event-activities.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Search activity, venue..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <select name="event_id" class="form-select form-select-sm">
            <option value="">All Events</option>
            @foreach($events as $ev)
              <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>
                {{ $ev->title }} ({{ $ev->start_date ? $ev->start_date->format('M Y') : 'N/A' }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
          <select name="event_category_id" class="form-select form-select-sm">
            <option value="">All Categories</option>
            @if(isset($categoriesList) && count($categoriesList) > 0)
              @foreach($categoriesList as $catObj)
                <option value="{{ $catObj->id }}" {{ request('event_category_id') == $catObj->id ? 'selected' : '' }}>{{ $catObj->name }}</option>
              @endforeach
            @else
              @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
          <select name="status" class="form-select form-select-sm">
            <option value="">All Statuses</option>
            @foreach($statuses as $st)
              <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
            <i data-lucide="filter" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Filter
          </button>
          <a href="{{ route('event-activities.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Activities Table -->
  <div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 text-nowrap">
        <thead class="table-light">
          <tr>
            <th>Activity Name</th>
            <th>Event</th>
            <th>Category</th>
            <th>Participating Houses</th>
            <th>Date & Schedule</th>
            <th>Winner / Standings</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($activities as $act)
            <tr>
              <td>
                <div class="fw-bold text-dark fs-6">{{ $act->name }}</div>
                @if($act->venue)
                  <small class="text-muted d-block"><i data-lucide="map-pin" style="width:0.75rem;height:0.75rem;"></i> {{ $act->venue }}</small>
                @endif
              </td>
              <td>
                <a href="{{ route('events.show', $act->event_id) }}" class="fw-semibold text-primary text-decoration-none">
                  {{ $act->event?->title }}
                </a>
              </td>
              <td>
                <div class="d-flex flex-wrap gap-1">
                  @if($act->categories && $act->categories->count() > 0)
                    @foreach($act->categories as $cItem)
                      <span class="badge {{ $cItem->badge_class ?? 'bg-secondary' }} px-2 py-1">
                        {{ $cItem->name }}
                      </span>
                    @endforeach
                  @else
                    <span class="badge {{ $act->category_badge_class }} px-2.5 py-1">
                      {{ $act->category }}
                    </span>
                  @endif
                </div>
              </td>
              <td>
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
                  <span class="text-muted small">All Houses / Open</span>
                @endif
              </td>
              <td>
                <div class="small fw-semibold text-dark">
                  {{ $act->activity_date ? $act->activity_date->format('M d, Y') : ($act->event?->formatted_date_range ?: 'TBD') }}
                </div>
                @if($act->start_time)
                  <small class="text-muted">{{ $act->start_time }} {{ $act->end_time ? '- ' . $act->end_time : '' }}</small>
                @endif
              </td>
              <td>
                @if($act->winnerHouse)
                  <span class="badge bg-warning text-dark px-2.5 py-1" title="1st Place Winner">
                    🏆 {{ $act->winnerHouse->name }}
                  </span>
                @else
                  <span class="text-muted small">TBD / Pending</span>
                @endif
              </td>
              <td>
                <span class="badge {{ $act->status_badge_class }} px-2.5 py-1">
                  {{ $act->status }}
                </span>
              </td>
              <td class="text-end">
                <a href="{{ route('event-activities.show', $act->id) }}" class="btn btn-sm btn-outline-info me-1" title="View Activity">
                  <i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                <a href="{{ route('event-activities.edit', $act->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Activity">
                  <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                <form action="{{ route('event-activities.destroy', $act->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this activity?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Activity">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <i data-lucide="trophy" class="mb-2" style="width:2.5rem;height:2.5rem;"></i>
                <p class="mb-0">No event activities found matching your criteria.</p>
                <a href="{{ route('event-activities.create') }}" class="btn btn-sm btn-primary mt-2">Create First Activity</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($activities->hasPages())
      <div class="card-footer bg-white py-3 border-top d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div class="text-muted small">
          Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} entries
        </div>
        <div class="mb-0">
          {{ $activities->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
