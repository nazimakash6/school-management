@extends('layouts.app')

@section('title', 'Subject Types')

@section('content')
<div class="container-fluid px-0">
  <div class="content-header mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div>
      <h1 class="page-title h3 fw-bold mb-1">Subject Types</h1>
      <p class="page-subtitle text-muted mb-0">Manage subject classifications (e.g. Core, Elective, Islamic, Optional)</p>
    </div>
    <div class="content-header-actions d-flex gap-2">
      <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Subjects List
      </a>
      <a href="{{ route('subject-types.create') }}" class="btn btn-primary btn-sm px-3">
        <i data-lucide="plus" style="width:1rem;height:1rem;" class="me-1"></i> Add Subject Type
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
      <i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;" class="me-2"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
      <i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;" class="me-2"></i>
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Search Card -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('subject-types.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, code, or status..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
            <i data-lucide="filter" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Filter
          </button>
          <a href="{{ route('subject-types.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Subject Types Table -->
  <div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 text-nowrap">
        <thead class="table-light">
          <tr>
            <th>Type Name</th>
            <th>Code</th>
            <th>Badge Preview</th>
            <th>Total Subjects Linked</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subjectTypes as $type)
            <tr>
              <td>
                <div class="fw-bold text-dark">{{ $type->name }}</div>
                @if($type->description)
                  <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $type->description }}</small>
                @endif
              </td>
              <td>
                <span class="badge bg-light text-dark border font-monospace">{{ $type->code }}</span>
              </td>
              <td>
                <span class="badge {{ $type->badge_class ?: 'bg-secondary text-white' }} px-3 py-1 text-capitalize">
                  {{ $type->name }}
                </span>
              </td>
              <td>
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1">
                  {{ $type->subjects_count }} {{ Str::plural('Subject', $type->subjects_count) }}
                </span>
              </td>
              <td>
                <span class="badge {{ $type->status === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-2 py-1 text-capitalize">
                  {{ $type->status }}
                </span>
              </td>
              <td class="text-end">
                <a href="{{ route('subject-types.edit', $type->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Subject Type">
                  <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i> Edit
                </a>
                <form action="{{ route('subject-types.destroy', $type->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this subject type?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Subject Type">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">
                <i data-lucide="tags" class="mb-2" style="width:2rem;height:2rem;"></i>
                <p class="mb-0">No subject types found.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($subjectTypes->hasPages())
      <div class="card-footer bg-white py-3 border-top d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div class="text-muted small">
          Showing {{ $subjectTypes->firstItem() }} to {{ $subjectTypes->lastItem() }} of {{ $subjectTypes->total() }} entries
        </div>
        <div class="mb-0">
          {{ $subjectTypes->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
