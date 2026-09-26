@extends ('layouts.app')

@section ('title', 'Create Academic Sessions')

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/academic-sessions-create.css') }}" />
@endpush

@section ('content')
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('academic-sessions.index') }}" class="text-decoration-none text-muted">Academic Setup</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Create Session</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="calendar-plus" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Create Academic Session</h3>
                    <p class="text-muted mb-0 fs-7">Add a new school academic year with start/end duration dates</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2">
            <a href="{{ route('academic-sessions.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to Sessions
            </a>
        </div>
    </div>

    @include ('partials.error-alerts')

    <div class="card">
        <div class="card-body">
            <form class="row g-3" method="POST" action="{{ route('academic-sessions.store') }}">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Session Name <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ old('session_name') }}"
                        name="session_name"
                        placeholder="e.g., 2025-2026"
                    />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input
                        type="date"
                        class="form-control"
                        value="{{ old('start_date', '2025-04-01') }}"
                        name="start_date"
                    />
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <input
                        type="date"
                        class="form-control"
                        value="{{ old('end_date', '2026-03-31') }}"
                        name="end_date"
                    />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status">
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="content-header-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i data-lucide="save" style="width: 1rem; height: 1rem"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
