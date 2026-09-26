@extends('layouts.app')

@section('title', 'Group Management')

@section('content')
<div class="container-fluid px-0">
  <!-- Content Header -->
  <div class="content-header mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div>
      <h1 class="page-title h3 fw-bold mb-1">Group Management</h1>
      <p class="page-subtitle text-muted mb-0">Define and manage academic streams (Science, Arts, Commerce) and assign subject groups</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('groups.create') }}" class="btn btn-primary btn-sm px-3">
        <i data-lucide="plus" style="width:1rem;height:1rem;" class="me-1"></i> Add New Group
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

  <!-- Metrics Overview Cards -->
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 col-12">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Academic Groups</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalGroups }}</h3>
          </div>
          <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="folder-tree" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-12">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Group Students</span>
            <h3 class="fw-bold text-primary mb-0 mt-1">{{ $totalStudentsInGroups }}</h3>
          </div>
          <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="users" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-12">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Active Streams</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ $activeGroups }}</h3>
          </div>
          <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="check-circle" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-12">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Mapped Subjects</span>
            <h3 class="fw-bold text-info mb-0 mt-1">{{ $totalMappedSubjects }}</h3>
          </div>
          <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="tags" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Group Details & Student Distribution Cards Box -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
          <i data-lucide="layout-grid" class="text-primary" style="width:1.25rem;height:1.25rem;"></i>
          Group Details & Student Distribution
        </h5>
        <p class="text-muted small mb-0">Overview of enrolled student count per academic stream</p>
      </div>
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold fs-7">
        <i data-lucide="users" style="width:0.875rem;height:0.875rem;" class="me-1"></i>
        {{ $totalStudentsInGroups }} Total Group Enrolled
      </span>
    </div>
    <div class="card-body p-3">
      <div class="row g-3">
        @forelse($allGroupsWithCounts as $grpCard)
          @php
            $cnt = $grpCard->student_count ?? 0;
            $percent = $totalStudentsInGroups > 0 ? round(($cnt / $totalStudentsInGroups) * 100) : 0;
            $badgeColor = match(strtolower($grpCard->name)) {
              'science group', 'science' => 'primary',
              'arts group', 'arts' => 'success',
              'commerce group', 'commerce' => 'info',
              'computer science', 'ics' => 'warning',
              default => 'purple'
            };
          @endphp
          <div class="col-xl-4 col-md-6 col-12">
            <div class="card border bg-light-subtle rounded-3 h-100 shadow-sm transition-all">
              <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} p-2 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                      <i data-lucide="graduation-cap" style="width:1.25rem;height:1.25rem;"></i>
                    </div>
                    <div>
                      <h6 class="fw-bold mb-0 text-dark">{{ $grpCard->name }}</h6>
                      <span class="text-muted fs-7">{{ $grpCard->subject_count }} {{ Str::plural('subject', $grpCard->subject_count) }}</span>
                    </div>
                  </div>
                  <span class="badge {{ $grpCard->status === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} rounded-pill px-2 py-1 fs-7 text-capitalize">
                    {{ $grpCard->status }}
                  </span>
                </div>

                <div class="mt-3 bg-white p-3 rounded border">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted fs-7 fw-semibold text-uppercase">Enrolled Students</span>
                    <span class="fw-bold text-dark fs-5">{{ $cnt }} <small class="fs-7 text-muted fw-normal">students</small></span>
                  </div>
                  <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-{{ $badgeColor }}" role="progressbar" style="width: {{ max($percent, 4) }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mt-2 fs-7">
                    <span class="text-muted">{{ $percent }}% of total stream students</span>
                    <a href="{{ route('groups.show', $grpCard->id) }}" class="text-primary text-decoration-none fw-semibold d-inline-flex align-items-center gap-1">
                      View Group <i data-lucide="arrow-right" style="width:0.75rem;height:0.75rem;"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-3 text-muted">
            No groups available to display breakdown.
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Filter Bar -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('groups.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Search group name, subjects, or description..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-sm-6 col-md-4">
          <select name="status" class="form-select form-select-sm">
            <option value="all">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div class="col-12 col-sm-6 col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
            <i data-lucide="filter" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Filter
          </button>
          <a href="{{ route('groups.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Groups Table -->
  <div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive" style="max-width: 100%;">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Group Name</th>
            <th>Enrolled Students</th>
            <th style="min-width: 250px;">Assigned Subjects (Tags)</th>
            <th>Description</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($groups as $group)
            @php
              $studentCountForGroup = $groupStudentCounts[$group->name] ?? 0;
            @endphp
            <tr>
              <td>
                <div class="fw-bold text-dark fs-6">{{ $group->name }}</div>
                <small class="text-muted">{{ $group->subject_count }} {{ Str::plural('subject', $group->subject_count) }}</small>
              </td>
              <td>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fs-7 fw-semibold d-inline-flex align-items-center gap-1">
                  <i data-lucide="users" style="width:0.875rem;height:0.875rem;"></i>
                  {{ $studentCountForGroup }} {{ Str::plural('Student', $studentCountForGroup) }}
                </span>
              </td>
              <td>
                <div class="d-flex flex-wrap gap-1">
                  @if(is_array($group->subjects) && count($group->subjects) > 0)
                    @foreach($group->subjects as $subjTag)
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 fs-7">
                        <i data-lucide="book" style="width:0.75rem;height:0.75rem;" class="me-1"></i> {{ $subjTag }}
                      </span>
                    @endforeach
                  @else
                    <span class="text-muted fs-7 italic">No subjects assigned</span>
                  @endif
                </div>
              </td>
              <td>
                <span class="text-secondary fs-7">{{ Str::limit($group->description, 60, '...') ?: 'No description' }}</span>
              </td>
              <td>
                <span class="badge {{ $group->status === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-2 py-1 text-capitalize">
                  {{ $group->status }}
                </span>
              </td>
              <td class="text-end text-nowrap">
                <a href="{{ route('groups.show', $group->id) }}" class="btn btn-sm btn-outline-info me-1" title="View Details">
                  <i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Group">
                  <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                <form action="{{ route('groups.destroy', $group->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this group?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Group">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-muted">
                <i data-lucide="folder-tree" class="mb-2" style="width:2rem;height:2rem;"></i>
                <p class="mb-0">No academic groups found matching your search criteria.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($groups->hasPages())
      <div class="card-footer bg-white py-3 border-top d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div class="text-muted small">
          Showing {{ $groups->firstItem() }} to {{ $groups->lastItem() }} of {{ $groups->total() }} entries
        </div>
        <div class="mb-0">
          {{ $groups->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
