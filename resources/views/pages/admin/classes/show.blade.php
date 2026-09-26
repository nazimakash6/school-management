@extends ('layouts.app')

@section ('title', 'Class Details')

@push ('styles')
    <link rel="stylesheet" href="{{ asset('css/classes-detail.css') }}" />
@endpush

@section ('content')
    <div class="content-header">
        <div>
            <h1 class="page-title">Class Details</h1>
            <p class="page-subtitle">View complete class information and linked student count</p>
        </div>
        <div class="content-header-actions">
            <a href="{{ route('classes.index') }}" class="btn btn-secondary btn-sm"
                ><i data-lucide="arrow-left" style="width: 1rem; height: 1rem"></i> Back</a
            >
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <img
                                    src="https://ui-avatars.com/api/?name={{ urlencode($studentClass->name) }}&background=6366f1&color=fff"
                                    alt="{{ $studentClass->name }}"
                                />
                            </div>
                            <h5 class="fw-bold mb-1">{{ $studentClass->name }}</h5>
                            <p class="text-tertiary mb-3 text-capitalize">{{ $studentClass->status }}</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('classes.edit', $studentClass) }}" class="btn btn-secondary btn-sm"
                                    ><i data-lucide="pencil" style="width: 1rem; height: 1rem"></i> Edit</a
                                >
                                <form
                                    method="POST"
                                    action="{{ route('classes.destroy', $studentClass) }}"
                                    onsubmit="return confirm('Delete this class?');"
                                >
                                    @csrf
                                    @method ('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">
                                        <i data-lucide="trash-2" style="width: 1rem; height: 1rem"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0 fw-bold">Details</h5></div>
                        <div class="card-body">
                            <form class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Class Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $studentClass->name }}"
                                        disabled
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Academic Level</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $studentClass->level }}"
                                        disabled
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Academic Group</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $studentClass->group ?: 'General / None' }}"
                                        disabled
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sections</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        value="{{ $studentClass->section_count }}"
                                        disabled
                                    />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Sections Name</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse (($studentClass->section_names ?? []) as $sectionName)
                                            <span class="badge bg-secondary">{{ $sectionName }}</span>
                                        @empty
                                            <span class="text-tertiary">No section names added.</span>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Class Incharge</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ $studentClass->teacher ? $studentClass->teacher->first_name . ' ' . $studentClass->teacher->last_name : 'No Class Incharge assigned' }}"
                                        disabled
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Students Assigned</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        value="{{ $studentClass->students_count }}"
                                        disabled
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <input
                                        type="text"
                                        class="form-control text-capitalize"
                                        value="{{ $studentClass->status }}"
                                        disabled
                                    />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea
                                        class="form-control"
                                        rows="4"
                                        disabled
                                        >{{ $studentClass->description }}</textarea
                                    >
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
