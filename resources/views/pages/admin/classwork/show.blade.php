@extends('layouts.app')

@section('title', 'Classwork Details')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('classwork.index') }}">Classwork</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Classwork #{{ $classwork->id }}</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">{{ $classwork->title ?: 'Classwork Details' }}</h1>
            <p class="text-muted small mb-0">{{ $classwork->studentClass->name ?? 'Class' }} — Assigned on {{ $classwork->assigned_date ? $classwork->assigned_date->format('M d, Y') : '' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('classwork.print', $classwork->id) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 d-inline-flex align-items-center gap-1.5 fw-semibold">
                <i data-lucide="printer" style="width:1rem;height:1rem;"></i> Print PDF
            </a>
            <a href="{{ route('classwork.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" class="me-1" style="width:1rem;height:1rem;"></i> Back to List
            </a>
            <a href="{{ route('classwork.edit', $classwork->id) }}" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border: none;">
                <i data-lucide="pencil" class="me-1" style="width:1rem;height:1rem;"></i> Edit
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center justify-content-between" role="alert">
            <div class="d-flex align-items-center">
                <i data-lucide="check-circle-2" class="me-2" style="width:1.2rem;height:1.2rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Details Box -->
        <div class="col-lg-8">
            @php
                $tasks = $classwork->tasks;
            @endphp

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i data-lucide="book-open" class="text-teal me-2" style="width:1.25rem;height:1.25rem;color:#0d9488;"></i>
                    Included Subjects & Tasks ({{ count($tasks) }})
                </h5>
            </div>

            @forelse($tasks as $item)
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-white px-3 py-1 fs-6 fw-bold" style="background-color:#0d9488;">
                                {{ $item['subject_name'] ?? 'Subject' }}
                            </span>
                            @if(!empty($item['subject_code']))
                                <span class="badge bg-light text-secondary border" style="font-size:0.75rem;">{{ $item['subject_code'] }}</span>
                            @endif
                        </div>
                        <div>
                            @if(($item['status'] ?? 'active') === 'active')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1">Closed</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @if(!empty($item['title']))
                            <h5 class="fw-bold text-dark mb-3">{{ $item['title'] }}</h5>
                        @endif

                        <div class="bg-light p-3 rounded-3 mb-3">
                            <h6 class="fw-semibold text-secondary mb-2" style="font-size:0.85rem;">Task Instructions & Details</h6>
                            <p class="text-dark mb-0 lh-base fs-6" style="white-space: pre-wrap;">{{ $item['description'] ?? 'No description provided.' }}</p>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small">
                            <div>
                                <i data-lucide="calendar" class="me-1 text-primary" style="width:0.875rem;height:0.875rem;"></i>
                                <strong>Submission Due Date:</strong> {{ !empty($item['due_date']) ? \Carbon\Carbon::parse($item['due_date'])->format('M d, Y (D)') : 'No Deadline' }}
                            </div>

                            @if(!empty($item['attachment']))
                                <a href="{{ Storage::url($item['attachment']) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i data-lucide="download" class="me-1" style="width:0.875rem;height:0.875rem;"></i> View Worksheet / Attachment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm rounded-3 p-4 text-center text-muted">
                    <p class="mb-0">No subject tasks found for this classwork record.</p>
                </div>
            @endforelse
        </div>

        <!-- Sidebar Info Box -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">Classwork Record Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Classwork ID</span>
                            <span class="fw-bold text-dark">#{{ $classwork->id }}</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Class Name</span>
                            <span class="fw-semibold text-primary fs-6">{{ $classwork->studentClass->name ?? 'N/A' }}</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Assigned Date</span>
                            <span class="fw-semibold text-dark">{{ $classwork->assigned_date ? $classwork->assigned_date->format('M d, Y (D)') : '-' }}</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Due Date</span>
                            <span class="fw-semibold text-dark">{{ $classwork->due_date ? $classwork->due_date->format('M d, Y (D)') : 'Not Set' }}</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Total Subjects Included</span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ count($tasks) }} Subjects</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2">
                            <span class="text-muted small">Assigned By</span>
                            <span class="fw-semibold text-dark">{{ $classwork->creator->name ?? 'System Administrator' }}</span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small">Created At</span>
                            <span class="fw-semibold text-dark">{{ $classwork->created_at ? $classwork->created_at->format('M d, Y g:i A') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light p-3 border-top">
                    <a href="{{ route('classwork.print', $classwork->id) }}" target="_blank" class="btn btn-primary w-100 btn-sm mb-2 shadow-sm fw-semibold" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); border: none;">
                        <i data-lucide="printer" class="me-1" style="width:0.875rem;height:0.875rem;"></i> Print Classwork PDF Sheet
                    </a>
                    <form action="{{ route('classwork.destroy', $classwork->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete Classwork #{{ $classwork->id }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                            <i data-lucide="trash-2" class="me-1" style="width:0.875rem;height:0.875rem;"></i> Delete Classwork Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
