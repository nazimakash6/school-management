@extends('layouts.app')

@section('title', 'House Details - ' . $house->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/houses-detail.css') }}">
@endpush

@section('content')
<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title d-flex align-items-center gap-2">
            @if ($house->color)
            <span class="rounded-circle d-inline-block border" style="width:20px;height:20px;background-color:{{ $house->color }};"></span>
            @endif
            {{ $house->name }}
        </h1>
        <p class="page-subtitle text-muted">House overview and assigned student list</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('houses.edit', $house) }}" class="btn btn-primary btn-sm">
            <i data-lucide="pencil" style="width:1rem;height:1rem;"></i> Edit House
        </a>
        <a href="{{ route('houses.index') }}" class="btn btn-secondary btn-sm">
            <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back
        </a>
    </div>
</div>

<!-- House Details Card Box -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">House Profile</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <span class="text-tertiary text-sm d-block">House Name</span>
                        <span class="fw-semibold text-dark fs-6">{{ $house->name }}</span>
                    </div>

                    <div class="col-sm-6">
                        <span class="text-tertiary text-sm d-block">House Code</span>
                        <span class="badge bg-light text-dark border fs-6">{{ $house->code ?: 'N/A' }}</span>
                    </div>

                    <div class="col-sm-6">
                        <span class="text-tertiary text-sm d-block">House Master</span>
                        <span class="fw-medium text-dark">
                            @if ($house->master)
                            {{ $house->master->first_name }} {{ $house->master->last_name }} ({{ ucfirst($house->master->designation ?? 'Staff') }})
                            @else
                            <em class="text-muted">Unassigned</em>
                            @endif
                        </span>
                    </div>

                    <div class="col-sm-6">
                        <span class="text-tertiary text-sm d-block">Theme Color</span>
                        @if ($house->color)
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-block border" style="width:16px;height:16px;background-color:{{ $house->color }};"></span>
                            <code>{{ $house->color }}</code>
                        </div>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="col-12">
                        <span class="text-tertiary text-sm d-block">Description</span>
                        <p class="text-dark mb-0">{{ $house->description ?: 'No description provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Statistics & Status</h5>

                    <div class="mb-3">
                        <span class="text-tertiary text-sm d-block">Total Enrolled Students</span>
                        <h2 class="fw-bold text-primary mb-0">{{ $house->students_count }}</h2>
                    </div>

                    <div>
                        <span class="text-tertiary text-sm d-block mb-1">Current Status</span>
                        <span class="badge bg-{{ $house->status === 'active' ? 'success' : 'secondary' }}-subtle text-{{ $house->status === 'active' ? 'success' : 'secondary' }} fs-6 text-capitalize px-3 py-2">
                            {{ ucfirst($house->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enrolled Students Table -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="fw-bold mb-0">Enrolled Students in {{ $house->name }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Admission No</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                <tr>
                    <td><strong class="text-primary">{{ $student->admission_no }}</strong></td>
                    <td class="text-capitalize fw-medium">{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td class="text-capitalize">{{ $student->class_name ?: 'N/A' }}</td>
                    <td class="text-capitalize">{{ $student->section_name ?: 'N/A' }}</td>
                    <td class="text-capitalize">{{ $student->gender ?: 'N/A' }}</td>
                    <td>
                        <span class="badge bg-{{ ($student->status ?: 'active') === 'active' ? 'success' : 'secondary' }}-subtle text-{{ ($student->status ?: 'active') === 'active' ? 'success' : 'secondary' }} text-capitalize">
                            {{ ucfirst($student->status ?: 'active') }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('student-list.show', $student->id) }}" class="btn btn-ghost btn-icon-sm" title="View Student">
                            <i data-lucide="eye" style="width:1rem;height:1rem;"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-tertiary py-4">
                        No students allocated to this house yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($students->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $students->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/houses-detail.js') }}"></script>
@endpush
