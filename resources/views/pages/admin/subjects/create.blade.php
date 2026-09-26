@extends ('layouts.app')

@section ('title', 'Add New Subject')

@section ('content')
    <div class="content-header mb-4">
        <div>
            <h1 class="page-title">Add New Subject</h1>
            <p class="page-subtitle">Configure curriculum subject details, subject type, and teacher assignment</p>
        </div>
        <div class="content-header-actions">
            <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem" class="me-1"></i> Back to Subjects
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <h6 class="fw-bold mb-2">Please correct the following errors:</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="subject_code" class="form-label fw-semibold"
                            >Subject Code <span class="text-danger">*</span></label
                        >
                        <input
                            type="text"
                            name="subject_code"
                            id="subject_code"
                            class="form-control"
                            value="{{ old('subject_code') }}"
                            placeholder="e.g. MATH-101"
                            required
                        />
                    </div>

                    <div class="col-md-6">
                        <label for="subject_name" class="form-label fw-semibold"
                            >Subject Name <span class="text-danger">*</span></label
                        >
                        <input
                            type="text"
                            name="subject_name"
                            id="subject_name"
                            class="form-control"
                            value="{{ old('subject_name') }}"
                            placeholder="e.g. Mathematics"
                            required
                        />
                    </div>

                    <div class="col-md-6">
                        <label for="class_name" class="form-label fw-semibold"
                            >Target Class <span class="text-danger">*</span></label
                        >
                        <select name="class_name" id="class_name" class="form-select" required>
                            <option value="">Select Target Class...</option>
                            @foreach ($classesList as $c)
                                <option value="{{ $c }}" {{ old('class_name') == $c ? 'selected' : '' }}>
                                    {{ $c }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="subject_type_id" class="form-label fw-semibold mb-0"
                                >Subject Type <span class="text-danger">*</span></label
                            >
                            <a href="{{ route('subject-types.create') }}" class="small text-decoration-none"
                                >+ Add Type</a
                            >
                        </div>
                        <select name="subject_type_id" id="subject_type_id" class="form-select" required>
                            <option value="">Select Subject Type...</option>
                            @foreach ($subjectTypes as $st)
                                <option value="{{ $st->id }}" {{ old('subject_type_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="staff_id" class="form-label fw-semibold">Subject Teacher</label>
                        <select name="staff_id" id="staff_id" class="form-select">
                            <option value="">-- Assign Subject Teacher (Optional) --</option>
                            @foreach ($teachersList as $teacher)
                                <option
                                    value="{{ $teacher->id }}"
                                    {{ old('staff_id') == $teacher->id ? 'selected' : '' }}
                                >
                                    {{ $teacher->first_name }} {{ $teacher->last_name }} ({{ $teacher->designation ?: 'Teacher' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold"
                            >Status <span class="text-danger">*</span></label
                        >
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label fw-semibold">Subject Description / Notes</label>
                        <textarea
                            name="description"
                            id="description"
                            class="form-control"
                            rows="3"
                            placeholder="Optional curriculum overview or syllabus details"
                            >{{ old('description') }}</textarea
                        >
                    </div>

                    <div class="col-md-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i data-lucide="save" style="width: 1rem; height: 1rem" class="me-1"></i> Save Subject
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
