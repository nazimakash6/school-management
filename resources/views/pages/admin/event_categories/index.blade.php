@extends ('layouts.app')

@section ('title', 'Event Categories')

@section ('content')
    <div class="container-fluid px-0">
        <div
            class="content-header mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3"
        >
            <div>
                <h1 class="page-title h3 fw-bold mb-1">Event Categories</h1>
                <p class="page-subtitle text-muted mb-0">Manage reusable categories for Academic Planning & Events and Event Activities</p>
            </div>
            <div class="content-header-actions d-flex gap-2">
                <a href="{{ route('event-categories.create') }}" class="btn btn-primary btn-sm px-3">
                    <i data-lucide="plus" style="width: 1rem; height: 1rem" class="me-1"></i> Add Category
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i data-lucide="check-circle" style="width: 1.25rem; height: 1.25rem" class="me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i data-lucide="alert-triangle" style="width: 1.25rem; height: 1.25rem" class="me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('event-categories.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <input
                            type="text"
                            name="search"
                            class="form-control form-control-sm"
                            placeholder="Search category name, code, description..."
                            value="{{ request('search') }}"
                        />
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                            <i data-lucide="filter" style="width: 0.875rem; height: 0.875rem" class="me-1"></i> Filter
                        </button>
                        <a href="{{ route('event-categories.index') }}" class="btn btn-sm btn-outline-secondary"
                            >Reset</a
                        >
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 60px">#</th>
                            <th>Category Name</th>
                            <th class="text-center">Events Count</th>
                            <th class="text-center">Activities Count</th>
                            <th>Status</th>
                            <th class="text-end pe-3" style="width: 130px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="ps-3 text-muted fw-semibold">
                                    {{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $category->name }}</div>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $category->events_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info fw-semibold px-2 py-1">
                                        {{ $category->activities_count }}
                                    </span>
                                </td>
                                <td>
                                    @if ($category->status)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a
                                            href="{{ route('event-categories.edit', $category) }}"
                                            class="btn btn-outline-secondary"
                                            title="Edit"
                                        >
                                            <i data-lucide="edit-3" style="width: 0.875rem; height: 0.875rem"></i>
                                        </a>
                                        <form
                                            action="{{ route('event-categories.destroy', $category) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this category?');"
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i data-lucide="trash-2" style="width: 0.875rem; height: 0.875rem"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i
                                        data-lucide="tag"
                                        style="width: 2.5rem; height: 2.5rem"
                                        class="mb-2 d-block mx-auto text-secondary opacity-50"
                                    ></i>
                                    No event categories found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="card-footer bg-white border-top p-3">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>
@endsection
