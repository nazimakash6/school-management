@extends('layouts.app')

@section('title', 'Quran Module - Student Progress')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quran-module.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Academics & Faith</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Quran Tracker</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="book-marked" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Quranic Studies & Progress Tracker</h3>
                    <p class="text-muted mb-0 fs-7">Track and evaluate student Hifz, Nazra, Qaida, Tajweed, Hadith, and Dua progress</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <a href="{{ route('quran-module.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="plus-circle" style="width:1rem;height:1rem;"></i> Add Progress Record
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="check-circle" class="text-success" style="width:1.25rem;height:1.25rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Analytics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm quran-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Total Records</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total_records'] }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                        <i data-lucide="book-open" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm quran-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Hifz Students</span>
                        <h3 class="fw-bold text-purple mb-0 mt-1">{{ $stats['hifz_students'] }}</h3>
                    </div>
                    <div class="p-3 bg-purple-subtle text-purple rounded-3">
                        <i data-lucide="award" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm quran-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Nazra Students</span>
                        <h3 class="fw-bold text-primary mb-0 mt-1">{{ $stats['nazra_students'] }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                        <i data-lucide="bookmark" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm quran-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Avg Score</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ $stats['avg_score'] }}%</h3>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                        <i data-lucide="star" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('quran-module.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted border-end-0"><i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search student name or roll..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <option value="Hifz" {{ request('category') === 'Hifz' ? 'selected' : '' }}>Hifz (Memorization)</option>
                        <option value="Nazra" {{ request('category') === 'Nazra' ? 'selected' : '' }}>Nazra (Recitation)</option>
                        <option value="Qaida" {{ request('category') === 'Qaida' ? 'selected' : '' }}>Qaida (Basics)</option>
                        <option value="Tajweed" {{ request('category') === 'Tajweed' ? 'selected' : '' }}>Tajweed Rules</option>
                        <option value="Hadith" {{ request('category') === 'Hadith' ? 'selected' : '' }}>Hadith</option>
                        <option value="Dua" {{ request('category') === 'Dua' ? 'selected' : '' }}>Masnoon Duas</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="class_name" class="form-select form-select-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->name }}" {{ request('class_name') === $cls->name ? 'selected' : '' }}>{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Excellent" {{ request('status') === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="Needs Improvement" {{ request('status') === 'Needs Improvement' ? 'selected' : '' }}>Needs Improvement</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-1">
                        <i data-lucide="filter" style="width:0.85rem;height:0.85rem;"></i> Filter
                    </button>
                    <a href="{{ route('quran-module.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Records Table Card --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="list" class="text-primary" style="width:1.25rem;height:1.25rem;"></i>
                Student Progress Records
            </h5>
            <span class="badge bg-light text-dark border">Showing {{ $records->count() }} of {{ $records->total() }} entries</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                    <tr>
                        <th class="ps-3" style="min-width: 180px;">Student</th>
                        <th style="min-width: 140px;">Category &amp; Status</th>
                        <th style="min-width: 250px;">Current Lesson / Details</th>
                        <th style="min-width: 110px;">Score &amp; Mistakes</th>
                        <th style="min-width: 140px;">Teacher &amp; Date</th>
                        <th class="pe-3 text-end" style="min-width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($records as $rec)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 2.3rem; height: 2.3rem; font-size: 0.9rem;">
                                        {{ strtoupper(substr($rec->student->first_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $rec->student->full_name ?? 'N/A' }}</div>
                                        <div class="text-muted small">
                                            Roll: <span class="fw-medium text-dark">{{ $rec->student->roll_no ?? 'N/A' }}</span> · 
                                            {{ $rec->student->class_name ?? 'Class N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex flex-column align-items-start gap-1">
                                    <span class="badge badge-category {{ $rec->category_badge_class }}">
                                        {{ $rec->category }}
                                    </span>
                                    <span class="badge {{ $rec->status_badge_class }}">
                                        {{ $rec->status }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                @if($rec->category === 'Hifz')
                                    <div class="fw-semibold text-dark">
                                        Sabaq: {{ $rec->sabaq ?: "Para {$rec->para_no} ({$rec->surah_name} {$rec->formatted_ayah_range})" }}
                                    </div>
                                    @if($rec->sabqi)
                                        <div class="text-muted small">Sabqi: {{ $rec->sabqi }}</div>
                                    @endif
                                    <div class="mt-1">
                                        <span class="badge bg-purple-subtle text-purple border-purple-subtle small">
                                            📖 {{ $rec->total_parahs_memorized }} / 30 Parahs Memorized
                                        </span>
                                    </div>
                                @elseif($rec->category === 'Nazra')
                                    <div class="fw-semibold text-dark">
                                        Para {{ $rec->para_no ?? '—' }} · {{ $rec->surah_name ?: 'Quran Recitation' }}
                                        @if($rec->formatted_ayah_range)
                                            <span class="text-muted small">({{ $rec->formatted_ayah_range }})</span>
                                        @endif
                                    </div>
                                    @if($rec->remarks)
                                        <div class="text-muted small text-truncate" style="max-width: 280px;">{{ $rec->remarks }}</div>
                                    @endif
                                @else
                                    <div class="fw-semibold text-dark">{{ $rec->lesson_name ?: "{$rec->category} Practice" }}</div>
                                    @if($rec->remarks)
                                        <div class="text-muted small text-truncate" style="max-width: 280px;">{{ $rec->remarks }}</div>
                                    @endif
                                @endif
                            </td>

                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold border border-success-subtle">
                                        Score: {{ number_format($rec->score, 1) }}%
                                    </span>
                                    @if($rec->mistakes_count > 0)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">
                                            ⚠️ {{ $rec->mistakes_count }} Mistakes
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border">No Mistakes</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="fw-medium text-dark small">{{ $rec->teacher_name ?: 'Assigned Qari' }}</div>
                                <div class="text-muted small">
                                    <i data-lucide="calendar" style="width:0.75rem;height:0.75rem;" class="me-1"></i>
                                    {{ $rec->entry_date ? $rec->entry_date->format('M d, Y') : 'N/A' }}
                                </div>
                            </td>

                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('quran-module.show', $rec->id) }}" class="btn btn-light border" title="View Details">
                                        <i data-lucide="eye" style="width:0.9rem;height:0.9rem;"></i>
                                    </a>
                                    <a href="{{ route('quran-module.edit', $rec->id) }}" class="btn btn-light border" title="Edit Record">
                                        <i data-lucide="pencil" style="width:0.9rem;height:0.9rem;"></i>
                                    </a>
                                    <button type="button" class="btn btn-light border text-danger" onclick="confirmDelete({{ $rec->id }})" title="Delete">
                                        <i data-lucide="trash-2" style="width:0.9rem;height:0.9rem;"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $rec->id }}" action="{{ route('quran-module.destroy', $rec->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-lucide="book-open" style="width:3rem;height:3rem;" class="mb-3 text-secondary opacity-50"></i>
                                    <h5>No Quran Progress Records Found</h5>
                                    <p class="small mb-3">Try adjusting your filter parameters or create a new student progress entry.</p>
                                    <a href="{{ route('quran-module.create') }}" class="btn btn-primary btn-sm">
                                        <i data-lucide="plus" style="width:0.9rem;height:0.9rem;"></i> Add Progress Record
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} entries</span>
                    <div>{{ $records->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this Quran progress record?')) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }
</script>
@endpush
@endsection
