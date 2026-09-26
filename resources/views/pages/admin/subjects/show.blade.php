@extends('layouts.app')

@section('title', 'Subject Details')

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Subject Details</h1>
      <p class="page-subtitle">View detailed curriculum configuration for {{ $subject->subject_name }}</p>
    </div>
    <div class="content-header-actions d-flex gap-2">
      <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-warning btn-sm">
        <i data-lucide="pencil" style="width:1rem;height:1rem;" class="me-1"></i> Edit Subject
      </a>
      <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Subjects
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="card-title fw-bold mb-0 text-dark">
            <i data-lucide="book-open" class="me-2 text-primary" style="width:1.25rem;height:1.25rem;"></i>
            {{ $subject->subject_name }}
          </h5>
          <span class="badge {{ $subject->type_badge_class }} px-3 py-1 text-capitalize">
            {{ $subject->type_name }}
          </span>
        </div>
        <div class="card-body p-4">
          <div class="row g-4">
            <div class="col-md-6">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold">Subject Code</span>
              <span class="fs-5 fw-bold text-primary">{{ $subject->subject_code }}</span>
            </div>

            <div class="col-md-6">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold">Assigned Class</span>
              <span class="fs-5 fw-bold text-dark">{{ $subject->class_name ?: 'All Classes' }}</span>
            </div>

            <div class="col-md-6">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold">Subject Type</span>
              <span class="fs-5 fw-bold text-dark">{{ $subject->type_name }}</span>
            </div>

            <div class="col-md-6">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold">Status</span>
              <span class="badge {{ $subject->status === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-3 py-1 text-capitalize">
                {{ $subject->status }}
              </span>
            </div>

            <div class="col-md-6">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold">Subject Teacher</span>
              @if($subject->teacher)
                <span class="fs-6 fw-bold text-dark">
                  <i data-lucide="user-check" style="width:1rem;height:1rem;" class="me-1 text-primary"></i>
                  {{ $subject->teacher->first_name }} {{ $subject->teacher->last_name }}
                </span>
              @else
                <span class="text-muted fs-6 italic">No teacher assigned yet</span>
              @endif
            </div>

            <div class="col-12">
              <hr>
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold mb-2">Description / Curriculum Overview</span>
              <p class="text-secondary bg-light p-3 rounded border mb-0">
                {{ $subject->description ?: 'No description provided for this subject.' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
          <h6 class="card-title fw-bold mb-0 text-dark">Quick Actions</h6>
        </div>
        <div class="card-body p-3 d-flex flex-column gap-2">
          <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-outline-warning w-100 text-start">
            <i data-lucide="pencil" style="width:1rem;height:1rem;" class="me-2"></i> Modify Subject Details
          </a>
          <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subject?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger w-100 text-start">
              <i data-lucide="trash-2" style="width:1rem;height:1rem;" class="me-2"></i> Remove Subject
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
