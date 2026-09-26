@extends('layouts.app')

@section('title', 'Academic Plan Details')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">
  
  {{-- EXECUTIVE HEADER --}}
  <div class="content-header mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('academic-planning.index') }}" class="text-decoration-none text-muted">Academic Planning</a></li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Plan Details</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
          <i data-lucide="book-open-check" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">{{ $plan->title }}</h3>
          <p class="text-muted mb-0 fs-7">Scope: {{ $plan->class_name ?? 'All Classes' }} &bull; {{ $plan->subject_name ?? 'General' }} &bull; {{ $plan->plan_type_label }}</p>
        </div>
      </div>
    </div>
    <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center">
      <a href="{{ route('academic-planning.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back
      </a>
      <a href="{{ route('academic-planning.edit', $plan->id) }}" class="btn btn-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
        <i data-lucide="pencil" style="width:1rem;height:1rem;"></i> Edit Plan
      </a>
      <form action="{{ route('academic-planning.destroy', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to move this plan to trash?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
          <i data-lucide="trash-2" style="width:1rem;height:1rem;"></i> Trash
        </button>
      </form>
    </div>
  </div>

  <div class="row g-4">
    
    {{-- MAIN DETAILS --}}
    <div class="col-lg-8">
      
      {{-- OBJECTIVES --}}
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
          <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i data-lucide="target" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Educational Objectives & Outcomes
          </h6>
        </div>
        <div class="card-body p-4">
          @if($plan->objectives)
            <p class="text-dark mb-0 fs-7 style-line-height">{!! nl2br(e($plan->objectives)) !!}</p>
          @else
            <span class="text-muted fs-7">No specific objectives defined for this academic plan.</span>
          @endif
        </div>
      </div>

      {{-- SYLLABUS TOPICS --}}
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
          <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i data-lucide="list-checks" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Curriculum Units & Topics Breakdown
          </h6>
        </div>
        <div class="card-body p-4">
          @if($plan->topics_covered)
            <div class="bg-light p-3 rounded-3 border text-dark fs-7 font-monospace">{!! nl2br(e($plan->topics_covered)) !!}</div>
          @else
            <span class="text-muted fs-7">No detailed topic breakdown provided.</span>
          @endif
        </div>
      </div>

      {{-- METHODOLOGY & ASSESSMENT --}}
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom">
              <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i data-lucide="presentation" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Teaching Methodology
              </h6>
            </div>
            <div class="card-body p-4 fs-7">
              @if($plan->teaching_methodology)
                {!! nl2br(e($plan->teaching_methodology)) !!}
              @else
                <span class="text-muted">Not specified.</span>
              @endif
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom">
              <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i data-lucide="clipboard-check" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Assessment & Testing Plan
              </h6>
            </div>
            <div class="card-body p-4 fs-7">
              @if($plan->assessment_plan)
                {!! nl2br(e($plan->assessment_plan)) !!}
              @else
                <span class="text-muted">Not specified.</span>
              @endif
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- SIDE PANEL METADATA --}}
    <div class="col-lg-4">
      
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
          <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i data-lucide="file-text" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Plan Parameters
          </h6>
        </div>
        <div class="card-body p-4">
          <ul class="list-group list-group-flush fs-7">
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
              <span class="text-muted">Plan Type</span>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-8">{{ $plan->plan_type_label }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
              <span class="text-muted">Target Class</span>
              <span class="fw-semibold text-dark">{{ $plan->class_name ?? 'All Classes' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
              <span class="text-muted">Subject</span>
              <span class="fw-semibold text-dark">{{ $plan->subject_name ?? 'General' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
              <span class="text-muted">Academic Session</span>
              <span class="fw-semibold text-dark">{{ $plan->academic_session ?? 'N/A' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
              <span class="text-muted">Assigned Teacher</span>
              <span class="fw-semibold text-dark">{{ $plan->teacher ? $plan->teacher->full_name : 'Unassigned' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
              <span class="text-muted">Start Date</span>
              <span class="fw-semibold text-dark">{{ $plan->start_date ? $plan->start_date->format('d M Y') : 'N/A' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
              <span class="text-muted">End Date</span>
              <span class="fw-semibold text-dark">{{ $plan->end_date ? $plan->end_date->format('d M Y') : 'N/A' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
              <span class="text-muted">Date Created</span>
              <span class="text-muted">{{ $plan->created_at->format('d M Y h:i A') }}</span>
            </li>
          </ul>
        </div>
      </div>

      {{-- ATTACHMENT CARD --}}
      @if($plan->attachment)
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
              <i data-lucide="paperclip" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Attached Document
            </h6>
          </div>
          <div class="card-body p-4 text-center">
            <i data-lucide="file-check" style="width:3rem;height:3rem;" class="text-primary mb-2"></i>
            <h6 class="fw-bold text-dark mb-1">Syllabus / Resource File</h6>
            <span class="fs-8 text-muted d-block mb-3">Stored safely in school ERP storage</span>
            <a href="{{ asset('storage/' . $plan->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold">
              <i data-lucide="download" style="width:1rem;height:1rem;"></i> Download File
            </a>
          </div>
        </div>
      @endif

    </div>

  </div>

</div>
@endsection
