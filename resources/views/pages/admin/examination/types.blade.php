@extends('layouts.app')

@section('title', 'Exam Types Management')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('examination.index') }}">Examination</a></li>
                    <li class="breadcrumb-item active">Exam Types</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Exam Types Management</h1>
            <p class="text-muted small mb-0">Manage exam categories (Daily Test, 3rd Day Test, Weekly Test, Monthly Test, Mid Test, Annual Exam, etc.)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('examination.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Exams
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTypeModal">
                <i data-lucide="plus" style="width:1rem;height:1rem;" class="me-1"></i> Add Exam Type
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Exam Types Navigation Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.index') }}"><i data-lucide="list" style="width:1rem;height:1rem;" class="me-1"></i> Examinations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="{{ route('exam-types.index') }}"><i data-lucide="tag" style="width:1rem;height:1rem;" class="me-1"></i> Exam Types</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.results') }}"><i data-lucide="bar-chart-2" style="width:1rem;height:1rem;" class="me-1"></i> Class Results</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.performance') }}"><i data-lucide="trending-up" style="width:1rem;height:1rem;" class="me-1"></i> Student Performance</a>
        </li>
    </ul>

    <!-- Exam Types Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Exam Type Name</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Associated Exams</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examTypes as $index => $type)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $type->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary font-monospace">{{ $type->code ?: 'N/A' }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ $type->description ?: 'No description provided.' }}
                            </td>
                            <td>
                                <span class="badge bg-info text-dark rounded-pill fs-7">{{ $type->examinations_count }} Exam(s)</span>
                            </td>
                            <td>
                                @if($type->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editTypeModal{{ $type->id }}" 
                                            title="Edit">
                                        <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                                    </button>
                                    <form action="{{ route('exam-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exam type?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" {{ $type->examinations_count > 0 ? 'disabled' : '' }}>
                                            <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade text-start" id="editTypeModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('exam-types.update', $type) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Edit Exam Type</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark">Exam Type Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $type->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark">Code / Abbreviation</label>
                                                        <input type="text" name="code" class="form-control" value="{{ $type->code }}" placeholder="e.g. DAILY, MONTHLY, FINAL">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark">Description</label>
                                                        <textarea name="description" class="form-control" rows="2">{{ $type->description }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-dark">Status <span class="text-danger">*</span></label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="active" @selected($type->status === 'active')>Active</option>
                                                            <option value="inactive" @selected($type->status === 'inactive')>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No exam types found. Click "Add Exam Type" to create one.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Type Modal -->
<div class="modal fade" id="createTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('exam-types.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Exam Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Exam Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Daily Test, 3rd Day Test, Weekly Test, Monthly Test" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Code / Abbreviation</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. DAILY, 3DAY, WEEKLY, MONTHLY">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief description or evaluation details..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Exam Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
