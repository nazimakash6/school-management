@extends('layouts.app')

@section('title', 'Edit Event Category')

@section('content')
<div class="container-fluid px-0">
  <div class="content-header mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h1 class="page-title h3 fw-bold mb-1">Edit Event Category</h1>
      <p class="page-subtitle text-muted mb-0">Update category details for Academic Planning & Events and Event Activities</p>
    </div>
    <div>
      <a href="{{ route('event-categories.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Categories
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
      <div class="fw-bold mb-1"><i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;" class="me-1"></i> Please check form validation errors:</div>
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
      <form action="{{ route('event-categories.update', $eventCategory) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label for="name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $eventCategory->name) }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12 col-md-6">
            <label for="code" class="form-label fw-semibold">Code / Key (Optional)</label>
            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $eventCategory->code) }}">
            <div class="form-text">Unique identifier or shortcode.</div>
            @error('code')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12 col-md-6">
            <label for="badge_class" class="form-label fw-semibold">Badge Class (Bootstrap Color)</label>
            <select name="badge_class" id="badge_class" class="form-select @error('badge_class') is-invalid @enderror">
              <option value="bg-primary" {{ old('badge_class', $eventCategory->badge_class) == 'bg-primary' ? 'selected' : '' }}>Primary (Blue)</option>
              <option value="bg-success" {{ old('badge_class', $eventCategory->badge_class) == 'bg-success' ? 'selected' : '' }}>Success (Green)</option>
              <option value="bg-info" {{ old('badge_class', $eventCategory->badge_class) == 'bg-info' ? 'selected' : '' }}>Info (Cyan)</option>
              <option value="bg-warning text-dark" {{ old('badge_class', $eventCategory->badge_class) == 'bg-warning text-dark' ? 'selected' : '' }}>Warning (Yellow)</option>
              <option value="bg-danger" {{ old('badge_class', $eventCategory->badge_class) == 'bg-danger' ? 'selected' : '' }}>Danger (Red)</option>
              <option value="bg-dark" {{ old('badge_class', $eventCategory->badge_class) == 'bg-dark' ? 'selected' : '' }}>Dark (Black)</option>
              <option value="bg-secondary" {{ old('badge_class', $eventCategory->badge_class) == 'bg-secondary' ? 'selected' : '' }}>Secondary (Gray)</option>
            </select>
            @error('badge_class')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12 col-md-6">
            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
              <option value="1" {{ old('status', $eventCategory->status) == '1' ? 'selected' : '' }}>Active</option>
              <option value="0" {{ old('status', $eventCategory->status) == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $eventCategory->description) }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="mt-4 d-flex gap-2 justify-content-end">
          <a href="{{ route('event-categories.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
          <button type="submit" class="btn btn-primary px-4">
            <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Update Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
