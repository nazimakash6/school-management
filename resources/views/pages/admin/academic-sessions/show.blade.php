@extends('layouts.app')

@section('title', '')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/academic-sessions-detail.css') }}">
@endpush

@section('content')



    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('academic-sessions.index') }}" class="text-decoration-none text-muted">Academic Setup</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Session Details</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="calendar" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Academic Session Details</h3>
                    <p class="text-muted mb-0 fs-7">Detailed overview of session {{ $academicSession->session_name }}</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('academic-sessions.edit', $academicSession->id) }}" class="btn btn-outline-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="pencil" style="width:1rem;height:1rem;"></i> Edit Session
            </a>
            <a href="{{ route('academic-sessions.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Sessions
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar avatar-xl mx-auto mb-3"><img
                                    src="https://ui-avatars.com/api/?name=Academic+Sessions&background=6366f1&color=fff"
                                    alt="Academic Sessions"></div>
                            <h5 class="fw-bold mb-1">Academic Sessions</h5>
                            <p class="text-tertiary mb-3">{{ $academicSession->status }}</p>
                            <div class="d-flex gap-2 justify-content-center"><a href="{{ route('academic-sessions.edit', $academicSession->id) }}" class="btn btn-secondary btn-sm"><i
                                        data-lucide="pencil" style="width:1rem;height:1rem;"></i> Edit</a><button
                                    class="btn btn-danger btn-sm"><i data-lucide="trash-2"
                                        style="width:1rem;height:1rem;"></i> Delete</button></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 fw-bold">Details</h5>
                        </div>
                        <div class="card-body">
                            <form class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Session Name</label>
                                    <input type="text" class="form-control" value="{{ $academicSession->session_name }}"
                                        disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" class="form-control"
                                        value="{{ $academicSession->start_date->format('d-m-Y') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="text" class="form-control"
                                        value="{{ $academicSession->end_date->format('d-m-Y') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" disabled>
                                        <option {{ $academicSession->status == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option {{ $academicSession->status == 'Inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
