@extends('layouts.app')

@section('title', 'Create Group')

@section('content')
<div class="container-fluid px-0">
  <div class="content-header mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div>
      <h1 class="page-title h3 fw-bold mb-1">Create Academic Group</h1>
      <p class="page-subtitle text-muted mb-0">Define a new group (e.g., Science, Arts) and map its subjects</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Groups
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
      <form action="{{ route('groups.store') }}" method="POST" id="groupForm">
        @csrf

        <div class="row g-3">
          <!-- Group Name -->
          <div class="col-md-6">
            <label for="name" class="form-label fw-semibold">Group Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Science Group, Arts Group, Commerce Group" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Status -->
          <div class="col-md-6">
            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
              <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <!-- Tag-type Subject Field -->
          <div class="col-md-12">
            <label for="subjectTagInput" class="form-label fw-semibold">
              Subjects (Tag Field) <span class="text-muted font-normal">(Add subjects associated with this group)</span>
            </label>
            
            <div class="border rounded-3 p-3 bg-light-subtle">
              <!-- Active Tag Pills Container -->
              <div id="subjectTagContainer" class="d-flex flex-wrap gap-2 mb-3 align-items-center min-h-38">
                <!-- Tags will be dynamically rendered here -->
              </div>

              <!-- Input for typing custom subject tags -->
              <div class="input-group input-group-sm mb-2">
                <span class="input-group-text bg-white"><i data-lucide="tag" style="width:0.875rem;height:0.875rem;"></i></span>
                <input type="text" id="subjectTagInput" class="form-control" placeholder="Type subject name & press Enter or comma (e.g. Physics, Chemistry)...">
                <button type="button" class="btn btn-primary" id="addSubjectTagBtn">
                  <i data-lucide="plus" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Add Subject
                </button>
              </div>
              <small class="text-muted d-block mb-3">Press <strong>Enter</strong> or <strong>Comma (,)</strong> after typing to add a subject tag.</small>

              <!-- Quick Add Suggestions from Available Subjects -->
              @if(isset($availableSubjects) && count($availableSubjects) > 0)
                <div>
                  <span class="fs-7 fw-semibold text-uppercase text-muted d-block mb-2">Quick Add Existing Subjects:</span>
                  <div class="d-flex flex-wrap gap-1">
                    @foreach($availableSubjects as $availSubj)
                      <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2 fs-7 quick-add-subject-btn" data-subject="{{ $availSubj }}">
                        + {{ $availSubj }}
                      </button>
                    @endforeach
                  </div>
                </div>
              @endif
            </div>
          </div>

          <!-- Group Description -->
          <div class="col-md-12">
            <label for="description" class="form-label fw-semibold">Description / Notes</label>
            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Provide a brief overview of this academic stream/group">{{ old('description') }}</textarea>
          </div>

          <!-- Submit Buttons -->
          <div class="col-md-12 text-end mt-4">
            <button type="submit" class="btn btn-primary px-4">
              <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Save Group
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('subjectTagContainer');
    const input = document.getElementById('subjectTagInput');
    const addBtn = document.getElementById('addSubjectTagBtn');
    const quickAddBtns = document.querySelectorAll('.quick-add-subject-btn');

    // Existing tags from old input
    const initialTags = @json(old('subject', []));

    function renderTag(tagName) {
      const cleanName = tagName.trim();
      if (!cleanName) return;

      // Avoid duplicates
      const existing = container.querySelectorAll('input[name="subject[]"]');
      for (let i = 0; i < existing.length; i++) {
        if (existing[i].value.toLowerCase() === cleanName.toLowerCase()) {
          return;
        }
      }

      const badge = document.createElement('span');
      badge.className = 'badge bg-primary text-white p-2 d-inline-flex align-items-center gap-2 rounded-pill fs-7 shadow-sm';
      badge.innerHTML = `
        <i data-lucide="book-open" style="width:0.875rem;height:0.875rem;"></i>
        <span>${cleanName}</span>
        <button type="button" class="btn-close btn-close-white ms-1" style="font-size:0.65rem;" aria-label="Remove"></button>
        <input type="hidden" name="subject[]" value="${cleanName}">
      `;

      badge.querySelector('.btn-close').addEventListener('click', function() {
        badge.remove();
      });

      container.appendChild(badge);
      if (window.lucide) lucide.createIcons();
    }

    // Populate initial tags
    if (Array.isArray(initialTags)) {
      initialTags.forEach(tag => renderTag(tag));
    }

    function handleAdd() {
      const val = input.value;
      if (val) {
        const parts = val.split(',');
        parts.forEach(p => renderTag(p));
        input.value = '';
      }
    }

    addBtn.addEventListener('click', function(e) {
      e.preventDefault();
      handleAdd();
    });

    input.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        handleAdd();
      }
    });

    quickAddBtns.forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const subj = this.getAttribute('data-subject');
        if (subj) renderTag(subj);
      });
    });
  });
</script>
@endpush
