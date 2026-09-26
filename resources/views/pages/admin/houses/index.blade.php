@extends('layouts.app')

@section('title', 'Houses')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/houses.css') }}">
@endpush

@section('content')
<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Houses Management</h1>
        <p class="page-subtitle text-muted">Manage school houses, house masters, and student allocations</p>
    </div>
    <div class="content-header-actions">
        <a href="{{ route('houses.create') }}" class="btn btn-primary btn-sm">
            <i data-lucide="plus" style="width:1rem;height:1rem;"></i> Add House
        </a>
    </div>
</div>

@if (session('status'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('status') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i data-lucide="home" style="width: 1.5rem; height: 1.5rem;"></i>
                </div>
                <div>
                    <span class="text-tertiary text-sm d-block">Total Houses</span>
                    <h3 class="fw-bold mb-0">{{ $totalHouses }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i data-lucide="check-circle-2" style="width: 1.5rem; height: 1.5rem;"></i>
                </div>
                <div>
                    <span class="text-tertiary text-sm d-block">Active Houses</span>
                    <h3 class="fw-bold mb-0">{{ $activeHouses }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="p-3 bg-info-subtle text-info rounded-3">
                    <i data-lucide="users" style="width: 1.5rem; height: 1.5rem;"></i>
                </div>
                <div>
                    <span class="text-tertiary text-sm d-block">Total Allocated Students</span>
                    <h3 class="fw-bold mb-0">{{ $houses->sum('students_count') }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar bg-white p-3 rounded-3 shadow-sm mb-4">
    <form method="GET" action="{{ route('houses.index') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="search" class="text-sm text-tertiary mb-1 d-block">Search</label>
            <input type="text" id="search" name="search" class="form-control" value="{{ $search }}" placeholder="Search by name, code, or description">
        </div>

        <div class="col-md-3">
            <label for="status" class="text-sm text-tertiary mb-1 d-block">Status</label>
            <select class="form-select" name="status" id="status">
                <option value="all" @selected($status === 'all')>All Status</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="perPage" class="text-sm text-tertiary mb-1 d-block">Show Records</label>
            <select class="form-select" name="per_page" id="perPage">
                @foreach ([25, 50, 100] as $size)
                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2 justify-content-end">
            <button class="btn btn-secondary btn-sm w-100" type="submit">
                <i data-lucide="filter" style="width:1rem;height:1rem;"></i> Apply
            </button>
            <a href="{{ route('houses.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="table-container bg-white rounded-3 shadow-sm">
    <table class="table align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>House Name</th>
                <th>Code</th>
                <th>Theme Color</th>
                <th>House Master</th>
                <th>Total Students</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($houses as $house)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        @if ($house->color)
                        <span class="rounded-circle d-inline-block border" style="width:14px;height:14px;background-color:{{ $house->color }};"></span>
                        @endif
                        <strong class="text-dark">{{ $house->name }}</strong>
                    </div>
                </td>
                <td><span class="badge bg-light text-dark border">{{ $house->code ?: 'N/A' }}</span></td>
                <td>
                    @if ($house->color)
                    <code class="px-2 py-1 bg-light rounded text-dark">{{ $house->color }}</code>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if ($house->master)
                    <span>{{ $house->master->first_name }} {{ $house->master->last_name }}</span>
                    @else
                    <span class="text-muted">Unassigned</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-info-subtle text-info fw-semibold px-2 py-1">
                        {{ $house->students_count }} Students
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $house->status === 'active' ? 'success' : 'secondary' }}-subtle text-{{ $house->status === 'active' ? 'success' : 'secondary' }} text-capitalize px-2 py-1">
                        {{ ucfirst($house->status) }}
                    </span>
                </td>
                <td class="text-end">
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="{{ route('houses.show', $house) }}" class="btn btn-ghost btn-icon-sm" title="View Details">
                            <i data-lucide="eye" style="width:1rem;height:1rem;"></i>
                        </a>
                        <a href="{{ route('houses.edit', $house) }}" class="btn btn-ghost btn-icon-sm" title="Edit House">
                            <i data-lucide="pencil" style="width:1rem;height:1rem;"></i>
                        </a>
                        <form method="POST" action="{{ route('houses.destroy', $house) }}" onsubmit="return confirm('Are you sure you want to delete this house?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-ghost btn-icon-sm text-danger" type="submit" title="Delete House">
                                <i data-lucide="trash-2" style="width:1rem;height:1rem;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-tertiary py-5">
                    <i data-lucide="home" class="mb-2 text-muted" style="width: 2.5rem; height: 2.5rem;"></i>
                    <p class="mb-1">No houses found.</p>
                    <a href="{{ route('houses.create') }}" class="btn btn-sm btn-primary mt-2">Create First House</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<nav class="mt-3 d-flex justify-content-between align-items-center">
    <span class="text-sm text-tertiary">
        @if ($houses->total() > 0)
        Showing {{ $houses->firstItem() }} to {{ $houses->lastItem() }} of {{ $houses->total() }} house entries
        @else
        Showing 0 house entries
        @endif
    </span>

    @if ($houses->hasPages())
    <ul class="pagination mb-0">
        <li class="page-item {{ $houses->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $houses->previousPageUrl() ?: '#' }}" @if($houses->onFirstPage()) aria-disabled="true" @endif><i data-lucide="chevron-left" style="width:1rem;height:1rem;"></i></a>
        </li>

        @foreach ($houses->getUrlRange(1, $houses->lastPage()) as $page => $url)
        <li class="page-item {{ $page === $houses->currentPage() ? 'active' : '' }}">
            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
        @endforeach

        <li class="page-item {{ $houses->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $houses->nextPageUrl() ?: '#' }}" @if(!$houses->hasMorePages()) aria-disabled="true" @endif><i data-lucide="chevron-right" style="width:1rem;height:1rem;"></i></a>
        </li>
    </ul>
    @endif
</nav>

@endsection

@push('scripts')
<script src="{{ asset('js/houses.js') }}"></script>
@endpush
