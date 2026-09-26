@extends('layouts.app')

@section('title', 'Subject Management')

@section('content')
<div class="container-fluid px-0">
  <!-- Content Header -->
  <div class="content-header mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div>
      <h1 class="page-title h3 fw-bold mb-1">Subject Management</h1>
      <p class="page-subtitle text-muted mb-0">Manage class-wise subjects, curriculum structure, and subject teachers</p>
    </div>
    <div class="content-header-actions d-flex gap-2">
      <a href="{{ route('subject-types.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i data-lucide="tags" style="width:1rem;height:1rem;" class="me-1"></i> Subject Types
      </a>
      <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm px-3">
        <i data-lucide="plus" style="width:1rem;height:1rem;" class="me-1"></i> Add Subject
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

  <!-- Top Metrics Overview -->
  <div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-6 col-12">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Subjects</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalSubjects }}</h3>
          </div>
          <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="book-open" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Subject Types</span>
            <h3 class="fw-bold text-primary mb-0 mt-1">{{ $subjectTypes->count() }}</h3>
          </div>
          <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="tags" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-md-6 col-12">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Classes Configured</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ count($classesList) }}</h3>
          </div>
          <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="layers" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Class-wise Subject Breakdown Cards -->
  <div class="mb-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
      <h5 class="fw-bold text-dark mb-0"><i data-lucide="grid" class="me-2 text-primary" style="width:1.25rem;height:1.25rem;"></i> Class-Wise Subject Breakdown</h5>
      <small class="text-muted">Click any class card to filter subjects</small>
    </div>
    <div class="row g-2 g-md-3">
      @foreach($classesList as $cName)
        @php
          $count = $classSubjectCounts[$cName] ?? 0;
          $isSelected = request('class_name') == $cName;
        @endphp
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
          <a href="{{ route('subjects.index', array_merge(request()->except('class_name'), ['class_name' => $isSelected ? 'all' : $cName])) }}" 
             class="text-decoration-none d-block">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3 h-100 transition-all {{ $isSelected ? 'bg-primary text-white shadow' : 'bg-white text-dark border' }}" style="cursor: pointer;">
              <div class="fw-bold fs-6 mb-1 text-truncate">{{ $cName }}</div>
              <div class="badge {{ $isSelected ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} px-2 py-1 rounded-pill text-truncate">
                {{ $count }} {{ Str::plural('Subject', $count) }}
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Filter Section -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('subjects.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Search subject code or name..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <select name="class_name" class="form-select form-select-sm">
            <option value="all">All Classes</option>
            @foreach($classesList as $c)
              <option value="{{ $c }}" {{ request('class_name') == $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <select name="subject_type_id" class="form-select form-select-sm">
            <option value="all">All Subject Types</option>
            @foreach($subjectTypes as $st)
              <option value="{{ $st->id }}" {{ request('subject_type_id') == $st->id ? 'selected' : '' }}>
                {{ $st->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
            <i data-lucide="filter" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Filter
          </button>
          <a href="{{ route('subjects.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Subjects Table -->
  <div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive" style="max-width: 100%;">
      <table class="table table-hover align-middle mb-0 text-nowrap">
        <thead class="table-light">
          <tr>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Class</th>
            <th>Type</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subjects as $subj)
            <tr>
              <td>
                <span class="fw-bold text-primary">{{ $subj->subject_code }}</span>
              </td>
              <td>
                <div class="fw-semibold text-dark">{{ $subj->subject_name }}</div>
              </td>
              <td>
                <span class="badge bg-light text-dark border">{{ $subj->class_name ?: 'All Classes' }}</span>
              </td>
              <td>
                <span class="badge {{ $subj->type_badge_class }} text-capitalize px-2 py-1">
                  {{ $subj->type_name }}
                </span>
              </td>
              <td>
                <span class="badge {{ $subj->status === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-2 py-1 text-capitalize">
                  {{ $subj->status }}
                </span>
              </td>
              <td class="text-end">
                <a href="{{ route('subjects.show', $subj->id) }}" class="btn btn-sm btn-outline-info me-1" title="View Details">
                  <i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                <a href="{{ route('subjects.edit', $subj->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Subject">
                  <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                <form action="{{ route('subjects.destroy', $subj->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this subject?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Subject">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">
                <i data-lucide="book-open" class="mb-2" style="width:2rem;height:2rem;"></i>
                <p class="mb-0">No subjects found matching your search criteria.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($subjects->hasPages())
      <div class="card-footer bg-white py-3 border-top d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div class="text-muted small">
          Showing {{ $subjects->firstItem() }} to {{ $subjects->lastItem() }} of {{ $subjects->total() }} entries
        </div>
        <div class="mb-0">
          {{ $subjects->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
