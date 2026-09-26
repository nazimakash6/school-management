@extends('layouts.app')

@section('title', 'Homework Management')

@push('styles')
<style>
.stat-card {
    border: 1px solid #f1f5f9;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.08);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hw-table-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    background: #ffffff;
    overflow: hidden;
}
.hw-table thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    font-size: 0.75rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border-bottom: 2px solid #e2e8f0;
    padding: 1rem 1.25rem;
}
.hw-table tbody tr {
    transition: background-color 0.15s ease-in-out;
}
.hw-table tbody tr:hover {
    background-color: #f8fafc !important;
}
.hw-table tbody td {
    padding: 1.1rem 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.badge-status-active {
    background-color: #dcfce7;
    color: #15803d;
    font-weight: 600;
    font-size: 0.775rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    border: 1px solid #bbf7d0;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.badge-status-active::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #16a34a;
    display: inline-block;
}
.badge-status-closed {
    background-color: #f1f5f9;
    color: #64748b;
    font-weight: 600;
    font-size: 0.775rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.subject-pill {
    background-color: #f8fafc;
    color: #334155;
    font-size: 0.775rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}
.class-badge {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-weight: 700;
    font-size: 0.875rem;
    padding: 0.4rem 0.85rem;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.action-btn {
    width: 34px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Academic Operations</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Homework</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="book-open" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Daily Class Homework</h3>
                    <p class="text-muted mb-0 fs-7">Manage and assign daily class-wise homework diaries and subject tasks</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <a href="{{ route('homework.create') }}" class="btn btn-primary btn-sm px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="plus-circle" style="width:1rem;height:1rem;"></i> Assign Homework
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4 border-0 border-start border-4 border-success" role="alert">
            <div class="d-flex align-items-center">
                <i data-lucide="check-circle-2" class="me-2 text-success" style="width:1.25rem;height:1.25rem;"></i>
                <span class="fw-semibold text-dark">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Total Homeworks</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalCount) }}</h3>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i data-lucide="book-copy" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Active Homeworks</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($activeCount) }}</h3>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i data-lucide="check-circle" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Classes Assigned Today</span>
                        <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($classesWithHomeworkToday) }}</h3>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i data-lucide="school" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold">Due Today</span>
                        <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($dueTodayCount) }}</h3>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i data-lucide="clock" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('homework.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="class_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" onchange="this.form.submit()">
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search class or homework..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-1 text-end">
                    @if(request()->hasAny(['class_id', 'date', 'status', 'search']))
                        <a href="{{ route('homework.index') }}" class="btn btn-sm btn-light border text-danger" title="Clear Filters">
                            <i data-lucide="rotate-ccw" style="width:0.9rem;height:0.9rem;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="hw-table-card">
        <div class="table-responsive">
            <table class="table hw-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 170px;">ID & Assigned Date</th>
                        <th>Class & Assigned Subjects</th>
                        <th style="width: 150px;">Due Date</th>
                        <th style="width: 120px;">Status</th>
                        <th class="text-end" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($homeworks as $hw)
                        @php
                            $tasks = $hw->tasks;
                            $assignedDate = $hw->assigned_date;
                            $dueDate = $hw->due_date;
                            $classObj = $hw->studentClass;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-slate-100 text-dark border px-2 py-1 fw-bold" style="font-size:0.75rem;">#{{ $hw->id }}</span>
                                </div>
                                <div class="fw-bold text-dark fs-6">{{ $assignedDate ? $assignedDate->format('M d, Y') : '-' }}</div>
                                <div class="text-muted small fs-7"><i data-lucide="calendar-days" style="width:0.75rem;height:0.75rem;" class="me-1"></i>{{ $assignedDate ? $assignedDate->format('l') : '' }}</div>
                            </td>
                            <td>
                                <div class="mb-2">
                                    <span class="class-badge">
                                        <i data-lucide="graduation-cap" style="width:1rem;height:1rem;"></i>
                                        {{ $classObj->name ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    <span class="text-muted small me-1 fw-semibold" style="font-size:0.75rem;">Subjects ({{ count($tasks) }}):</span>
                                    @foreach($tasks as $t)
                                        <span class="subject-pill">
                                            {{ $t['subject_name'] ?? 'Subject' }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($dueDate)
                                    <div class="fw-semibold text-dark" style="font-size:0.85rem;">
                                        <i data-lucide="clock" class="me-1 text-danger" style="width:0.875rem;height:0.875rem;"></i>
                                        {{ $dueDate->format('M d, Y') }}
                                    </div>
                                    <div class="text-muted small" style="font-size:0.725rem;">{{ $dueDate->format('l') }}</div>
                                @else
                                    <span class="text-muted small">Not Specified</span>
                                @endif
                            </td>
                            <td>
                                @if($hw->status === 'active')
                                    <span class="badge-status-active">Active</span>
                                @else
                                    <span class="badge-status-closed">Closed</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1.5 align-items-center">
                                    <a href="{{ route('homework.show', $hw->id) }}" class="btn btn-sm btn-outline-primary action-btn" title="View Homework Details">
                                        <i data-lucide="eye" style="width:0.95rem;height:0.95rem;"></i>
                                    </a>
                                    <a href="{{ route('homework.print', $hw->id) }}" target="_blank" class="btn btn-sm btn-outline-success action-btn" title="Print Diary Sheet">
                                        <i data-lucide="printer" style="width:0.95rem;height:0.95rem;"></i>
                                    </a>
                                    <a href="{{ route('homework.edit', $hw->id) }}" class="btn btn-sm btn-outline-secondary action-btn" title="Edit Homework">
                                        <i data-lucide="pencil" style="width:0.95rem;height:0.95rem;"></i>
                                    </a>
                                    <form action="{{ route('homework.destroy', $hw->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete Homework Entry #{{ $hw->id }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger action-btn" title="Delete Homework Entry">
                                            <i data-lucide="trash-2" style="width:0.95rem;height:0.95rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <div class="p-3 bg-light d-inline-flex rounded-circle mb-3">
                                        <i data-lucide="book-x" class="text-secondary" style="width:2.5rem;height:2.5rem;opacity:0.6;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">No Homework Assignments Found</h5>
                                    <p class="small text-muted mb-3">Try adjusting your filter search criteria or create a new homework assignment.</p>
                                    <a href="{{ route('homework.create') }}" class="btn btn-primary btn-sm px-3 py-2 fw-semibold">
                                        <i data-lucide="plus" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Assign Homework Now
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($homeworks->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        Showing {{ $homeworks->firstItem() }} to {{ $homeworks->lastItem() }} of {{ $homeworks->total() }} entries
                    </span>
                    <div>
                        {{ $homeworks->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
