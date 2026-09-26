@extends('layouts.app')

@section('title', 'Skills Institute - Technical & Vocational Evaluations')

@push('styles')
<style>
.stat-card-skill {
    border: none;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-card-skill:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
}
.star-rating-badge {
    background: #fffbebf5;
    border: 1px solid #fde68a;
    color: #b45309;
    font-weight: 700;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.875rem;
}
.star-filled {
    color: #f59e0b;
    fill: #f59e0b;
}
.star-empty {
    color: #cbd5e1;
}
.badge-level-pill {
    font-weight: 600;
    font-size: 0.75rem;
    padding: 0.35em 0.75em;
    border-radius: 50rem;
}
.cert-code-tag {
    font-family: SFMono-Regular, Menlos, Monaco, Consolas, monospace;
    font-size: 0.75rem;
    background: #f1f5f9;
    color: #334155;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Vocational Training</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Skills Institute</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="award" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Skills Institute & Practical Evaluations</h3>
                    <p class="text-muted mb-0 fs-7">Assess student vocational skills, technical scores, star ratings, and certification badges</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <a href="{{ route('skills-institute.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="plus-circle" style="width:1rem;height:1rem;"></i> Assess Student Skill
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i data-lucide="check-circle-2" class="me-2" style="width:1.2rem;height:1.2rem;"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-skill p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Overall Avg Rating</span>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <h2 class="fw-bold text-dark mb-0">{{ number_format($avgStarRating, 1) }}</h2>
                            <span class="text-muted small">/ 5.0 Stars</span>
                        </div>
                        <div class="mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i data-lucide="star" style="width:0.875rem;height:0.875rem;" class="{{ $i <= round($avgStarRating) ? 'star-filled' : 'star-empty' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                        <i data-lucide="award" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-skill p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Monthly Avg Rating</span>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <h2 class="fw-bold text-primary mb-0">{{ number_format($monthlyAvgStars, 1) }}</h2>
                            <span class="text-muted small">/ 5.0 Stars</span>
                        </div>
                        <div class="mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i data-lucide="star" style="width:0.875rem;height:0.875rem;" class="{{ $i <= round($monthlyAvgStars) ? 'star-filled' : 'star-empty' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                        <i data-lucide="calendar" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-skill p-3 border-start border-success border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Master Skilled (5★)</span>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <h2 class="fw-bold text-success mb-0">{{ $masterSkilledCount }}</h2>
                            <span class="text-muted small">Students</span>
                        </div>
                        <span class="text-muted small">High performers</span>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                        <i data-lucide="zap" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-skill p-3 border-start border-info border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Evaluations</span>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <h2 class="fw-bold text-info mb-0">{{ $totalEvaluationsCount }}</h2>
                            <span class="text-muted small">Records</span>
                        </div>
                        <span class="text-muted small">Top: {{ $topCategory }}</span>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                        <i data-lucide="file-check-2" style="width:1.6rem;height:1.6rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('skills-institute.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Class Filter</label>
                    <select name="class_name" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->name }}" {{ request('class_name') == $cls->name ? 'selected' : '' }}>
                                {{ $cls->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Skill Category</label>
                    <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold mb-1">Evaluation Period</label>
                    <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Periods</option>
                        <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Search Student / Skill</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search by student, skill, code..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-1 text-end mt-4">
                    @if(request()->anyFilled(['class_name', 'category', 'period', 'search', 'date']))
                        <a href="{{ route('skills-institute.index') }}" class="btn btn-sm btn-light border text-danger" title="Clear Filters">
                            <i data-lucide="rotate-ccw" style="width:0.9rem;height:0.9rem;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Skills Evaluations Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="award" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                Student Skill Evaluations
            </h6>
            <span class="badge bg-light text-dark border">
                Showing {{ $skills->count() }} of {{ $skills->total() }} Records
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-semibold">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>Student Details</th>
                            <th>Skill & Category</th>
                            <th>Period & Type</th>
                            <th>Score & Star Rating</th>
                            <th>Badge Level</th>
                            <th>Certificate Code</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($skills as $s)
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">{{ $loop->iteration + ($skills->currentPage() - 1) * $skills->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                            {{ strtoupper(substr($s->student->first_name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('skills-institute.show', $s->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $s->student->full_name ?? 'N/A' }}
                                            </a>
                                            <div class="text-muted small">
                                                Roll #{{ $s->student->roll_no ?? 'N/A' }} &bull; <span class="badge bg-secondary bg-opacity-10 text-dark">{{ $s->student->class_name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $s->skill_name }}</div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary small mt-1">
                                        {{ $s->skill_category }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border text-capitalize">
                                        {{ $s->performance_period }}
                                    </span>
                                    <div class="text-muted small mt-1">
                                        {{ $s->assessment_type }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="star-rating-badge">
                                            <i data-lucide="star" style="width:0.95rem;height:0.95rem;" class="star-filled"></i>
                                            {{ number_format($s->star_rating, 1) }}
                                        </span>
                                        <small class="text-muted fw-semibold">
                                            ({{ number_format($s->obtained_score) }}/{{ number_format($s->total_score) }})
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $s->badge_class }} badge-level-pill">
                                        {{ $s->badge_level }}
                                    </span>
                                </td>
                                <td>
                                    @if($s->certificate_code)
                                        <span class="cert-code-tag">
                                            <i data-lucide="file-check" style="width:0.75rem;height:0.75rem;" class="me-1"></i>{{ $s->certificate_code }}
                                        </span>
                                    @else
                                        <span class="text-muted small">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $s->evaluation_date ? $s->evaluation_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('skills-institute.show', $s->id) }}" class="btn btn-light text-primary" title="View Scorecard & Certificate">
                                            <i data-lucide="eye" style="width:0.9rem;height:0.9rem;"></i>
                                        </a>
                                        <a href="{{ route('skills-institute.edit', $s->id) }}" class="btn btn-light text-secondary" title="Edit Skill Evaluation">
                                            <i data-lucide="edit-3" style="width:0.9rem;height:0.9rem;"></i>
                                        </a>
                                        <form action="{{ route('skills-institute.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this skill evaluation record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light text-danger" title="Delete">
                                                <i data-lucide="trash-2" style="width:0.9rem;height:0.9rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i data-lucide="award" style="width:3rem;height:3rem;" class="text-muted opacity-50"></i>
                                    </div>
                                    <h6>No Skill Evaluation Records Found</h6>
                                    <p class="small mb-3">Try adjusting your filters or record a new student skill assessment.</p>
                                    <a href="{{ route('skills-institute.create') }}" class="btn btn-primary btn-sm">
                                        <i data-lucide="plus" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Assess Student Skill
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($skills->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        Showing {{ $skills->firstItem() }} to {{ $skills->lastItem() }} of {{ $skills->total() }} entries
                    </span>
                    {{ $skills->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
