@extends('layouts.app')

@section('title', 'Edit Academic Plan')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">
  
  {{-- EXECUTIVE HEADER --}}
  <div class="content-header mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('academic-planning.index') }}" class="text-decoration-none text-muted">Academic Planning</a></li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Edit Plan</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
          <i data-lucide="pencil-line" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">Edit Academic Plan</h3>
          <p class="text-muted mb-0 fs-7">Update syllabus objectives, topic breakdowns, and timeline schedules</p>
        </div>
      </div>
    </div>
    <div class="content-header-actions d-flex gap-2 align-items-center">
      <a href="{{ route('academic-planning.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back
      </a>
      <a href="{{ route('academic-planning.show', $plan->id) }}" class="btn btn-outline-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
        <i data-lucide="eye" style="width:1rem;height:1rem;"></i> View Details
      </a>
      <button type="submit" form="academicPlanForm" class="btn btn-primary btn-sm px-4 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
        <i data-lucide="save" style="width:1rem;height:1rem;"></i> Update Plan
      </button>
    </div>
  </div>

  @if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <div class="fw-bold mb-1"><i data-lucide="alert-triangle" style="width:1.1rem;height:1.1rem;" class="me-1"></i> Please correct the following errors:</div>
      <ul class="mb-0 ps-3 fs-7">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <form id="academicPlanForm" action="{{ route('academic-planning.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
      
      {{-- PRIMARY DETAILS --}}
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
              <i data-lucide="info" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Basic Overview & Scope
            </h6>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              
              <div class="col-12">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Plan Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $plan->title) }}" placeholder="e.g. Annual Secondary Mathematics Curriculum 2026-2027" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Plan Type <span class="text-danger">*</span></label>
                <select name="plan_type" class="form-select @error('plan_type') is-invalid @enderror" required>
                  <option value="annual" @selected(old('plan_type', $plan->plan_type) === 'annual')>Annual Plan</option>
                  <option value="monthly" @selected(old('plan_type', $plan->plan_type) === 'monthly')>Monthly Plan</option>
                  <option value="weekly" @selected(old('plan_type', $plan->plan_type) === 'weekly')>Weekly Plan</option>
                  <option value="daily" @selected(old('plan_type', $plan->plan_type) === 'daily')>Daily Lesson Plan</option>
                  <option value="unit" @selected(old('plan_type', $plan->plan_type) === 'unit')>Unit Syllabus</option>
                </select>
                @error('plan_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Academic Session</label>
                <select name="academic_session" class="form-select">
                  <option value="">Select Academic Session...</option>
                  @foreach($sessions as $sess)
                    <option value="{{ $sess }}" @selected(old('academic_session', $plan->academic_session) === $sess)>{{ $sess }}</option>
                  @endforeach
                  <option value="2026-2027" @selected(old('academic_session', $plan->academic_session) === '2026-2027')>2026-2027</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Target Class</label>
                <select name="class_name" class="form-select">
                  <option value="">Select Target Class...</option>
                  @foreach($classes as $c)
                    <option value="{{ $c }}" @selected(old('class_name', $plan->class_name) === $c)>{{ $c }}</option>
                  @endforeach
                  <option value="All Classes" @selected(old('class_name', $plan->class_name) === 'All Classes')>All Classes</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Subject</label>
                <select name="subject_name" class="form-select">
                  <option value="">Select Subject...</option>
                  @foreach($subjects as $s)
                    <option value="{{ $s }}" @selected(old('subject_name', $plan->subject_name) === $s)>{{ $s }}</option>
                  @endforeach
                  <option value="General Curriculum" @selected(old('subject_name', $plan->subject_name) === 'General Curriculum')>General Curriculum</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Assigned Teacher</label>
                <select name="teacher_id" class="form-select">
                  <option value="">Assign Teacher (Optional)...</option>
                  @foreach($teachers as $t)
                    <option value="{{ $t->id }}" @selected(old('teacher_id', $plan->teacher_id) == $t->id)>{{ $t->full_name }} ({{ $t->staff_id }})</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $plan->start_date ? $plan->start_date->format('Y-m-d') : '') }}">
              </div>

              <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $plan->end_date ? $plan->end_date->format('Y-m-d') : '') }}">
              </div>

            </div>
          </div>
        </div>

        {{-- CURRICULUM DETAILS & TEXTAREAS --}}
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
              <i data-lucide="book-open" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Curriculum & Syllabus Breakdown
            </h6>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              
              <div class="col-12">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Learning Objectives & Goals</label>
                <textarea name="objectives" class="form-control" rows="3" placeholder="Specify key learning outcomes, competencies, and educational targets...">{{ old('objectives', $plan->objectives) }}</textarea>
              </div>

              <div class="col-12">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Syllabus Topics & Units Covered</label>
                <textarea name="topics_covered" class="form-control" rows="4" placeholder="Detail chapters, units, lessons, and core concepts to be taught...">{{ old('topics_covered', $plan->topics_covered) }}</textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Teaching Methodology & Strategy</label>
                <textarea name="teaching_methodology" class="form-control" rows="3" placeholder="Lectures, practical lab sessions, group discussions, media resources...">{{ old('teaching_methodology', $plan->teaching_methodology) }}</textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Assessment & Evaluation Plan</label>
                <textarea name="assessment_plan" class="form-control" rows="3" placeholder="Quizzes, assignments, monthly tests, mid-term and annual exams...">{{ old('assessment_plan', $plan->assessment_plan) }}</textarea>
              </div>

            </div>
          </div>
        </div>
      </div>

      {{-- SIDE PANEL ATTACHMENT & STATUS --}}
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
              <i data-lucide="settings" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Plan Publishing & Document
            </h6>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              
              <div class="col-12">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Publishing Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                  <option value="active" @selected(old('status', $plan->status) === 'active')>Active / Approved</option>
                  <option value="draft" @selected(old('status', $plan->status) === 'draft')>Draft</option>
                  <option value="completed" @selected(old('status', $plan->status) === 'completed')>Completed</option>
                  <option value="archived" @selected(old('status', $plan->status) === 'archived')>Archived</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-12">
                <label class="form-label text-xs font-semibold text-uppercase text-muted">Attach File / Syllabus Document</label>
                <input type="file" name="attachment_file" class="form-control form-control-sm">
                @if($plan->attachment)
                  <div class="mt-2 text-xs">
                    <span class="text-muted">Current File:</span>
                    <a href="{{ asset('storage/' . $plan->attachment) }}" target="_blank" class="text-primary fw-medium text-decoration-none ms-1">
                      <i data-lucide="paperclip" style="width:0.8rem;height:0.8rem;"></i> Download Attachment
                    </a>
                  </div>
                @endif
                <div class="form-text fs-8 text-muted mt-1">Allowed formats: PDF, DOCX, XLSX, JPG, PNG, ZIP (Max 10MB)</div>
              </div>

            </div>
          </div>
          <div class="card-footer bg-white py-3 px-4 border-top">
            <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
              <i data-lucide="check" style="width:1rem;height:1rem;"></i> Update Academic Plan
            </button>
          </div>
        </div>
      </div>

    </div>
  </form>

</div>
@endsection
