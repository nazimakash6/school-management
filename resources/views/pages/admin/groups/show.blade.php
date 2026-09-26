@extends('layouts.app')

@section('title', 'Group Details')

@section('content')
<div class="container-fluid px-0">
  <div class="content-header mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div>
      <h1 class="page-title h3 fw-bold mb-1">Group Details</h1>
      <p class="page-subtitle text-muted mb-0">Detailed view of academic stream {{ $group->name }} and its assigned subject tags</p>
    </div>
    <div class="content-header-actions d-flex gap-2">
      <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-warning btn-sm">
        <i data-lucide="pencil" style="width:1rem;height:1rem;" class="me-1"></i> Edit Group
      </a>
      <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Groups
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="card-title fw-bold mb-0 text-dark">
            <i data-lucide="folder-tree" class="me-2 text-primary" style="width:1.25rem;height:1.25rem;"></i>
            {{ $group->name }}
          </h5>
          <span class="badge {{ $group->status === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' }} px-3 py-1 text-capitalize">
            {{ $group->status }}
          </span>
        </div>
        <div class="card-body p-4">
          <div class="row g-4">
            <div class="col-md-6">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold">Group Name</span>
              <span class="fs-5 fw-bold text-dark">{{ $group->name }}</span>
            </div>

            <div class="col-md-6">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold">Total Assigned Subjects</span>
              <span class="fs-5 fw-bold text-primary">{{ $group->subject_count }} {{ Str::plural('Subject', $group->subject_count) }}</span>
            </div>

            <div class="col-12">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold mb-2">Subject Tags Mapped</span>
              <div class="d-flex flex-wrap gap-2 p-3 bg-light rounded border">
                @if(is_array($group->subjects) && count($group->subjects) > 0)
                  @foreach($group->subjects as $subjTag)
                    <span class="badge bg-primary text-white p-2 d-inline-flex align-items-center gap-1 rounded-pill fs-7 shadow-sm">
                      <i data-lucide="book-open" style="width:0.875rem;height:0.875rem;"></i>
                      <span>{{ $subjTag }}</span>
                    </span>
                  @endforeach
                @else
                  <span class="text-muted fs-7 italic">No subjects mapped to this group yet.</span>
                @endif
              </div>
            </div>

            <div class="col-12">
              <span class="text-muted d-block fs-7 text-uppercase fw-semibold mb-2">Description / Group Details</span>
              <p class="text-secondary bg-light p-3 rounded border mb-0">
                {{ $group->description ?: 'No description provided for this group.' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Enrolled Students Card Box -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i data-lucide="users" class="text-primary" style="width:1.25rem;height:1.25rem;"></i>
            Enrolled Students in {{ $group->name }}
          </h5>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-semibold fs-7">
            {{ $studentCount ?? 0 }} {{ Str::plural('Student', $studentCount ?? 0) }}
          </span>
        </div>
        <div class="card-body p-0">
          @if(isset($enrolledStudents) && count($enrolledStudents) > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Admission No</th>
                    <th>Student Name</th>
                    <th>Class & Section</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($enrolledStudents as $std)
                    <tr>
                      <td class="fw-bold text-primary">{{ $std->admission_no }}</td>
                      <td class="fw-bold text-dark">{{ $std->full_name }}</td>
                      <td>
                        {{ $std->studentClass ? $std->studentClass->name : ($std->class_name ?: 'N/A') }}
                        @if($std->section_name)
                          <span class="badge bg-primary-subtle text-primary ms-1">Sec {{ strtoupper($std->section_name) }}</span>
                        @endif
                      </td>
                      <td>
                        <span class="badge bg-success-subtle text-success text-capitalize">{{ $std->status ?: 'Active' }}</span>
                      </td>
                      <td class="text-end">
                        <a href="{{ route('student-list.show', $std->id) }}" class="btn btn-sm btn-outline-primary">
                          <i data-lucide="eye" style="width:0.875rem;height:0.875rem;" class="me-1"></i> View Profile
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="p-4 text-center text-muted">
              <i data-lucide="users-2" class="mb-2" style="width:2rem;height:2rem;"></i>
              <p class="mb-0">No students are currently assigned to this academic group.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
          <h6 class="card-title fw-bold mb-0 text-dark">Group Metadata</h6>
        </div>
        <div class="card-body p-3">
          <ul class="list-unstyled mb-0">
            <li class="d-flex justify-content-between py-2 border-bottom text-muted fs-7">
              <span>Created At:</span>
              <span class="fw-semibold text-dark">{{ $group->created_at ? $group->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
            </li>
            <li class="d-flex justify-content-between py-2 text-muted fs-7">
              <span>Last Updated:</span>
              <span class="fw-semibold text-dark">{{ $group->updated_at ? $group->updated_at->format('M d, Y h:i A') : 'N/A' }}</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
          <h6 class="card-title fw-bold mb-0 text-dark">Quick Actions</h6>
        </div>
        <div class="card-body p-3 d-flex flex-column gap-2">
          <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-outline-warning w-100 text-start">
            <i data-lucide="pencil" style="width:1rem;height:1rem;" class="me-2"></i> Modify Group Details
          </a>
          <form action="{{ route('groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger w-100 text-start">
              <i data-lucide="trash-2" style="width:1rem;height:1rem;" class="me-2"></i> Delete Group
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
