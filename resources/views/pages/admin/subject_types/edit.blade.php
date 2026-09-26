@extends('layouts.app')

@section('title', 'Edit Subject Type')

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Edit Subject Type</h1>
      <p class="page-subtitle">Update subject type details and badge styling</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('subject-types.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Subject Types
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <h6 class="fw-bold mb-2">Please correct the following errors:</h6>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form action="{{ route('subject-types.update', $subjectType->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label fw-semibold">Type Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $subjectType->name) }}" required>
          </div>

          <div class="col-md-6">
            <label for="code" class="form-label fw-semibold">Unique Code / Slug <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $subjectType->code) }}" required>
          </div>

          <div class="col-md-6">
            <label for="badge_class" class="form-label fw-semibold">Badge Style / Color Class</label>
            <select name="badge_class" id="badge_class" class="form-select">
              <option value="bg-primary text-white" {{ old('badge_class', $subjectType->badge_class) == 'bg-primary text-white' ? 'selected' : '' }}>Primary Blue</option>
              <option value="bg-info text-white" {{ old('badge_class', $subjectType->badge_class) == 'bg-info text-white' ? 'selected' : '' }}>Info Cyan</option>
              <option value="bg-success text-white" {{ old('badge_class', $subjectType->badge_class) == 'bg-success text-white' ? 'selected' : '' }}>Success Green</option>
              <option value="bg-warning text-dark" {{ old('badge_class', $subjectType->badge_class) == 'bg-warning text-dark' ? 'selected' : '' }}>Warning Yellow</option>
              <option value="bg-danger text-white" {{ old('badge_class', $subjectType->badge_class) == 'bg-danger text-white' ? 'selected' : '' }}>Danger Red</option>
              <option value="bg-secondary text-white" {{ old('badge_class', $subjectType->badge_class) == 'bg-secondary text-white' ? 'selected' : '' }}>Secondary Dark</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select" required>
              <option value="active" {{ old('status', $subjectType->status) == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ old('status', $subjectType->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <div class="col-md-12">
            <label for="description" class="form-label fw-semibold">Description / Notes</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $subjectType->description) }}</textarea>
          </div>

          <div class="col-md-12 text-end mt-4">
            <button type="submit" class="btn btn-primary px-4">
              <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Update Subject Type
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
