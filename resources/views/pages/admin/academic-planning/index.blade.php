@extends('layouts.app')

@section('title', 'Academic Planning')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectAll = document.getElementById('select-all');
  const checkboxes = document.querySelectorAll('.record-checkbox');
  const bulkBtn = document.getElementById('bulkTrashBtn');

  function updateBulkBtnState() {
    const checkedCount = document.querySelectorAll('.record-checkbox:checked').length;
    if (bulkBtn) {
      bulkBtn.disabled = checkedCount === 0;
    }
  }

  if (selectAll) {
    selectAll.addEventListener('change', function () {
      checkboxes.forEach(cb => cb.checked = selectAll.checked);
      updateBulkBtnState();
    });
  }

  checkboxes.forEach(cb => {
    cb.addEventListener('change', function () {
      if (selectAll) {
        selectAll.checked = (checkboxes.length > 0 && document.querySelectorAll('.record-checkbox:checked').length === checkboxes.length);
      }
      updateBulkBtnState();
    });
  });

  updateBulkBtnState();
});
</script>
@endpush

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">
  
  {{-- FLASH MESSAGES --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <div class="d-flex align-items-center gap-2">
        <i data-lucide="check-circle-2" style="width:1.25rem;height:1.25rem;"></i>
        <div>{{ session('success') }}</div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <div class="d-flex align-items-center gap-2">
        <i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;"></i>
        <div>{{ session('error') }}</div>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- EXECUTIVE HEADER WITH ACTIONS IN HEADING TAGLINE --}}
  <div class="content-header mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item text-muted">Academic Management</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Academic Planning</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
          <i data-lucide="book-open-check" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">Academic Planning & Curriculum Management</h3>
          <p class="text-muted mb-0 fs-7">Annual, monthly, weekly, and daily planning</p>
        </div>
      </div>
    </div>
    <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center">
      <a href="{{ route('academic-planning.trash') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
        <i data-lucide="archive" style="width:1rem;height:1rem;"></i> Trash
        @if($trashCount > 0)
          <span class="badge bg-danger text-white rounded-pill ms-1 fs-8 px-2">{{ $trashCount }}</span>
        @endif
      </a>
      <a href="{{ route('academic-planning.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
        <i data-lucide="plus" style="width:1rem;height:1rem;"></i> Create Plan
      </a>
    </div>
  </div>

  {{-- SUMMARY STAT KPI CARDS --}}
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
        <div class="d-flex align-items-center justify-content-between">
          <span class="text-muted text-xs text-uppercase font-semibold">Total Plans</span>
          <span class="p-2 bg-primary-subtle text-primary rounded-2"><i data-lucide="layers" style="width:1.1rem;height:1.1rem;"></i></span>
        </div>
        <h3 class="fw-bold text-dark mb-0 mt-2">{{ number_format($totalPlans) }}</h3>
        <span class="fs-7 text-muted">All active curriculum records</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
        <div class="d-flex align-items-center justify-content-between">
          <span class="text-muted text-xs text-uppercase font-semibold">Active Status</span>
          <span class="p-2 bg-success-subtle text-success rounded-2"><i data-lucide="check-circle-2" style="width:1.1rem;height:1.1rem;"></i></span>
        </div>
        <h3 class="fw-bold text-success mb-0 mt-2">{{ number_format($activePlans) }}</h3>
        <span class="fs-7 text-emerald-600">Currently in effect</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
        <div class="d-flex align-items-center justify-content-between">
          <span class="text-muted text-xs text-uppercase font-semibold">Annual Curriculums</span>
          <span class="p-2 bg-info-subtle text-info rounded-2"><i data-lucide="calendar" style="width:1.1rem;height:1.1rem;"></i></span>
        </div>
        <h3 class="fw-bold text-info mb-0 mt-2">{{ number_format($annualPlans) }}</h3>
        <span class="fs-7 text-muted">Full session master plans</span>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 bg-white rounded-3 border shadow-sm h-100">
        <div class="d-flex align-items-center justify-content-between">
          <span class="text-muted text-xs text-uppercase font-semibold">Monthly Modules</span>
          <span class="p-2 bg-warning-subtle text-warning rounded-2"><i data-lucide="calendar-range" style="width:1.1rem;height:1.1rem;"></i></span>
        </div>
        <h3 class="fw-bold text-warning mb-0 mt-2">{{ number_format($monthlyPlans) }}</h3>
        <span class="fs-7 text-amber-600">Term/month breakdowns</span>
      </div>
    </div>
  </div>

  {{-- FILTER CARD --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom">
      <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
        <i data-lucide="filter" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Filter Academic Plans
      </h6>
    </div>
    <div class="card-body p-4">
      <form method="GET" action="{{ route('academic-planning.index') }}" class="row g-3">
        <div class="col-md-3">
          <label class="form-label text-xs font-semibold text-uppercase text-muted">Search Keyword</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Title, topic, objective..." value="{{ $search }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-xs font-semibold text-uppercase text-muted">Plan Type</label>
          <select name="plan_type" class="form-select form-select-sm">
            <option value="all" @selected($planType === 'all')>All Types</option>
            <option value="annual" @selected($planType === 'annual')>Annual Plan</option>
            <option value="monthly" @selected($planType === 'monthly')>Monthly Plan</option>
            <option value="weekly" @selected($planType === 'weekly')>Weekly Plan</option>
            <option value="daily" @selected($planType === 'daily')>Daily Lesson Plan</option>
            <option value="unit" @selected($planType === 'unit')>Unit Syllabus</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label text-xs font-semibold text-uppercase text-muted">Class</label>
          <select name="class_name" class="form-select form-select-sm">
            <option value="all" @selected($className === 'all')>All Classes</option>
            @foreach($classes as $c)
              <option value="{{ $c }}" @selected($className === $c)>{{ $c }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label text-xs font-semibold text-uppercase text-muted">Subject</label>
          <select name="subject_name" class="form-select form-select-sm">
            <option value="all" @selected($subjectName === 'all')>All Subjects</option>
            @foreach($subjects as $s)
              <option value="{{ $s }}" @selected($subjectName === $s)>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-1">
          <label class="form-label text-xs font-semibold text-uppercase text-muted">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="all" @selected($status === 'all')>All</option>
            <option value="active" @selected($status === 'active')>Active</option>
            <option value="draft" @selected($status === 'draft')>Draft</option>
            <option value="completed" @selected($status === 'completed')>Completed</option>
            <option value="archived" @selected($status === 'archived')>Archived</option>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-primary btn-sm px-3 flex-grow-1">
            <i data-lucide="search" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Filter
          </button>
          <a href="{{ route('academic-planning.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
        </div>
      </form>
    </div>
  </div>

  {{-- PLANS DATA TABLE FORM WITH MULTI TRASH ACTION --}}
  <form id="bulkTrashForm" action="{{ route('academic-planning.bulk-trash') }}" method="POST">
    @csrf
    @method('DELETE')
    
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
          <h6 class="mb-0 fw-bold text-dark">Academic Plans Directory</h6>
          <button type="submit" id="bulkTrashBtn" class="btn btn-outline-danger btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" onclick="return confirm('Are you sure you want to move selected plans to trash?');" disabled>
            <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i> Move Selected to Trash
          </button>
        </div>
        <span class="badge bg-light text-dark border">Showing {{ $plans->firstItem() ?? 0 }} - {{ $plans->lastItem() ?? 0 }} of {{ $plans->total() }}</span>
      </div>
      
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 40px;" class="text-center">
                <input type="checkbox" id="select-all" class="form-check-input cursor-pointer">
              </th>
              <th style="width: 40px;">#</th>
              <th>Plan Title</th>
              <th>Type</th>
              <th>Class / Subject</th>
              <th>Assigned Teacher</th>
              <th>Duration Scope</th>
              <th>Status</th>
              <th class="text-center" style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($plans as $index => $plan)
              <tr>
                <td class="text-center">
                  <input type="checkbox" name="ids[]" value="{{ $plan->id }}" class="form-check-input record-checkbox cursor-pointer">
                </td>
                <td class="text-muted fs-7">{{ $plans->firstItem() + $index }}</td>
                <td>
                  <a href="{{ route('academic-planning.show', $plan->id) }}" class="fw-bold text-decoration-none text-dark d-block">
                    {{ $plan->title }}
                  </a>
                  @if($plan->academic_session)
                    <span class="text-xs text-muted"><i data-lucide="calendar" style="width:0.75rem;height:0.75rem;" class="me-0.5"></i> {{ $plan->academic_session }}</span>
                  @endif
                  @if($plan->attachment)
                    <span class="badge bg-light text-primary border ms-1 fs-8"><i data-lucide="paperclip" style="width:0.75rem;height:0.75rem;"></i> Attachment</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-8">
                    {{ $plan->plan_type_label }}
                  </span>
                </td>
                <td>
                  <div class="fw-semibold text-dark fs-7">{{ $plan->class_name ?? 'All Classes' }}</div>
                  <div class="text-xs text-muted">{{ $plan->subject_name ?? 'General' }}</div>
                </td>
                <td>
                  @if($plan->teacher)
                    <div class="fs-7 text-dark fw-medium">{{ $plan->teacher->full_name }}</div>
                    <div class="text-xs text-muted">{{ $plan->teacher->formatted_department }}</div>
                  @else
                    <span class="text-muted fs-7">—</span>
                  @endif
                </td>
                <td class="fs-7">
                  @if($plan->start_date && $plan->end_date)
                    {{ $plan->start_date->format('d M Y') }} - {{ $plan->end_date->format('d M Y') }}
                  @elseif($plan->start_date)
                    From {{ $plan->start_date->format('d M Y') }}
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  <span class="badge {{ $plan->status_badge_class }} rounded-pill px-2.5 py-1 fs-8 text-capitalize">
                    {{ $plan->status }}
                  </span>
                </td>
                <td class="text-center">
                  <div class="d-flex align-items-center justify-content-center gap-1">
                    <a href="{{ route('academic-planning.show', $plan->id) }}" class="btn btn-ghost btn-icon-sm text-primary" title="View Plan Details">
                      <i data-lucide="eye" style="width:1rem;height:1rem;"></i>
                    </a>
                    <a href="{{ route('academic-planning.edit', $plan->id) }}" class="btn btn-ghost btn-icon-sm text-secondary" title="Edit Plan">
                      <i data-lucide="pencil" style="width:1rem;height:1rem;"></i>
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-5">
                  <i data-lucide="book-open" style="width:2.5rem;height:2.5rem;" class="mb-2 text-muted opacity-50"></i>
                  <p class="mb-0 fw-semibold">No academic plans found</p>
                  <span class="fs-7">Create a new plan or adjust your search filter criteria.</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($plans->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top">
          {{ $plans->links() }}
        </div>
      @endif
    </div>
  </form>

</div>
@endsection
