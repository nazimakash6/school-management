@extends('layouts.app')

@section('title', 'Examination Management')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Academic Operations</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Examinations</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="file-text" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Examination & Grading System</h3>
                    <p class="text-muted mb-0 fs-7">Create exams, select exam types, record student marks & analyze performance</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex gap-2 flex-wrap">
            <a href="{{ route('exam-types.index') }}" class="btn btn-outline-primary btn-sm">
                <i data-lucide="tag" style="width:1rem;height:1rem;"></i> Exam Types
            </a>
            <a href="{{ route('examination.create') }}" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="plus" style="width:1rem;height:1rem;"></i> New Examination
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="{{ route('examination.index') }}"><i data-lucide="list" style="width:1rem;height:1rem;" class="me-1"></i> Examinations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('exam-types.index') }}"><i data-lucide="tag" style="width:1rem;height:1rem;" class="me-1"></i> Exam Types</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.results') }}"><i data-lucide="bar-chart-2" style="width:1rem;height:1rem;" class="me-1"></i> Class Results</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('examination.performance') }}"><i data-lucide="trending-up" style="width:1rem;height:1rem;" class="me-1"></i> Student Performance</a>
        </li>
    </ul>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('examination.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Exam Type</label>
                    <select name="exam_type_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">— All Exam Types —</option>
                        @foreach($examTypes as $type)
                            <option value="{{ $type->id }}" @selected($examTypeId == $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Target Class</label>
                    <select name="class_name" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">— All Classes —</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->name }}" @selected($className == $cls->name)>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Academic Session</label>
                    <select name="academic_session_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">— All Sessions —</option>
                        @foreach($academicSessions as $sess)
                            <option value="{{ $sess->id }}" @selected($sessionId == $sess->id)>Session {{ $sess->session_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 align-self-end">
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search exam title...">
                    <button type="submit" class="btn btn-secondary btn-sm"><i data-lucide="search" style="width:1rem;height:1rem;"></i></button>
                    @if($examTypeId || $className || $sessionId || $search)
                        <a href="{{ route('examination.index') }}" class="btn btn-outline-danger btn-sm" title="Clear Filters"><i data-lucide="x" style="width:1rem;height:1rem;"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Examinations Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Examination Title</th>
                            <th>Exam Type</th>
                            <th>Target Class</th>
                            <th>Academic Session</th>
                            <th>Total Marks</th>
                            <th>Pass Marks</th>
                            <th>Marks Entered</th>
                            <th>Status</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examinations as $exam)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark fs-6">{{ $exam->title }}</div>
                                <div class="text-muted small">
                                    <i data-lucide="calendar" style="width:0.875rem;height:0.875rem;" class="me-1"></i>
                                    {{ $exam->start_date ? $exam->start_date->format('M d, Y') : 'N/A' }} 
                                    @if($exam->end_date) — {{ $exam->end_date->format('M d, Y') }} @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                    {{ optional($exam->examType)->name ?: 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($exam->isAllClasses())
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill"><i data-lucide="layers" style="width:0.75rem;height:0.75rem;" class="me-1"></i> All Classes</span>
                                @else
                                    @foreach($exam->target_classes_array as $tCls)
                                        <span class="badge bg-light text-dark border me-1 mb-1">{{ $tCls }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ optional($exam->academicSession)->session_name ? 'Session ' . $exam->academicSession->session_name : 'N/A' }}
                                </span>
                            </td>
                            <td><span class="fw-bold text-dark">{{ number_format($exam->total_marks) }}</span></td>
                            <td><span class="text-secondary">{{ number_format($exam->pass_marks) }}</span></td>
                            <td>
                                @if($exam->marks_count > 0)
                                    <span class="badge bg-success text-white rounded-pill"><i data-lucide="check-circle" style="width:0.75rem;height:0.75rem;" class="me-1"></i> {{ $exam->marks_count }} Record(s)</span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill">Pending Entry</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $stBadges = [
                                        'scheduled' => 'bg-info-subtle text-info border-info-subtle',
                                        'ongoing' => 'bg-warning-subtle text-warning border-warning-subtle',
                                        'completed' => 'bg-success-subtle text-success border-success-subtle',
                                        'published' => 'bg-primary-subtle text-primary border-primary-subtle',
                                    ];
                                @endphp
                                <span class="badge {{ $stBadges[$exam->status] ?? 'bg-secondary' }} border rounded-pill text-capitalize">
                                    {{ $exam->status }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group">
                                    <a href="{{ route('examination.marks', $exam) }}" class="btn btn-sm btn-outline-success" title="Enter / Edit Marks">
                                        <i data-lucide="edit-3" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Marks
                                    </a>
                                    <a href="{{ route('examination.show', $exam) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i>
                                    </a>
                                    <a href="{{ route('examination.edit', $exam) }}" class="btn btn-sm btn-outline-secondary" title="Edit Exam">
                                        <i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i>
                                    </a>
                                    <form action="{{ route('examination.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this examination?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Exam">
                                            <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i data-lucide="file-x" style="width:2.5rem;height:2.5rem;" class="text-secondary mb-2 d-block mx-auto"></i>
                                No examinations found matching your criteria. Click "New Examination" to create one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top">
                {{ $examinations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
