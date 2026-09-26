@extends ('layouts.app')

@section ('title', 'Events & Calendar Management System')

@push ('styles')
    <style>
        .stat-card-event {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }
        .stat-card-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }
        .event-card {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }
        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.1);
        }
        .event-banner-img {
            height: 160px;
            width: 100%;
            object-fit: cover;
        }
    </style>
@endpush

@section ('content')
    <div class="container-fluid px-4 py-3">
        <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
        <div class="content-header mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1.5 fs-7">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"
                                ><i data-lucide="home" style="width: 0.875rem; height: 0.875rem" class="me-1"></i
                                >Dashboard</a
                            >
                        </li>
                        <li class="breadcrumb-item text-muted">School Life</li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                            Events Calendar
                        </li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2.5">
                    <div
                        class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center"
                        style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)"
                    >
                        <i data-lucide="calendar" style="width: 1.5rem; height: 1.5rem"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold text-dark tracking-tight">Events & School Calendar System</h3>
                        <p class="text-muted mb-0 fs-7">Schedule academic galas, sports tournaments, PTMs, cultural & Islamic events</p>
                    </div>
                </div>
            </div>
            <div class="content-header-actions">
                <a
                    href="{{ route('events.create') }}"
                    class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold"
                    style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none"
                >
                    <i data-lucide="calendar-plus" style="width: 1rem; height: 1rem"></i> Schedule New Event
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i data-lucide="check-circle-2" class="me-2" style="width: 1.2rem; height: 1.2rem"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Upcoming Events</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $upcomingCount }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                            <i data-lucide="clock" style="width: 1.6rem; height: 1.6rem"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Completed This Month</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $completedMonth }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                            <i data-lucide="check-circle-2" style="width: 1.6rem; height: 1.6rem"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Sports & Tournaments</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $sportsCount }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                            <i data-lucide="trophy" style="width: 1.6rem; height: 1.6rem"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Total Budget Allocated</span>
                            <h4 class="fw-bold mb-0 text-dark">Rs. {{ number_format($totalBudgetPkr) }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                            <i data-lucide="wallet" style="width: 1.6rem; height: 1.6rem"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('events.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Event Category</label>
                        <select
                            name="event_category_id"
                            class="form-select form-select-sm"
                            onchange="this.form.submit()"
                        >
                            <option value="">All Categories</option>
                            @if (isset($categoriesList) && count($categoriesList) > 0)
                                @foreach ($categoriesList as $cat)
                                    <option
                                        value="{{ $cat->id }}"
                                        {{ request('event_category_id') == $cat->id ? 'selected' : '' }}
                                    >
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            @else
                                @foreach ($eventTypes as $et)
                                    <option value="{{ $et }}" {{ request('event_type') == $et ? 'selected' : '' }}>
                                        {{ $et }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Event Status</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $st)
                                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-semibold mb-1">Search Events</label>
                        <div class="input-group input-group-sm">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search by title, location, organizer..."
                                value="{{ request('search') }}"
                            />
                            <button class="btn btn-primary" type="submit">
                                <i data-lucide="search" style="width: 0.9rem; height: 0.9rem"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-1 text-end mt-4">
                        @if (request()->anyFilled(['event_type', 'status', 'search']))
                            <a
                                href="{{ route('events.index') }}"
                                class="btn btn-sm btn-light border text-danger"
                                title="Clear Filters"
                            >
                                <i data-lucide="rotate-ccw" style="width: 0.9rem; height: 0.9rem"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Events Grid Cards -->
        <div class="row g-4 mb-4">
            @forelse ($events as $e)
                <div class="col-xl-4 col-md-6">
                    <div class="event-card h-100 d-flex flex-column">
                        <div class="position-relative">
                            <img src="{{ $e->banner_url }}" alt="{{ $e->title }}" class="event-banner-img" />
                            <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1">
                                @if($e->categories && $e->categories->count() > 0)
                                    @foreach($e->categories as $catItem)
                                        <span class="badge {{ $catItem->badge_class ?? 'bg-primary' }} px-2.5 py-1 shadow-sm">
                                            {{ $catItem->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="badge {{ $e->event_type_badge_class }} px-3 py-1 shadow-sm">
                                        {{ $e->event_type }}
                                    </span>
                                @endif
                            </div>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge {{ $e->status_badge_class }} px-3 py-1 shadow-sm">
                                    {{ $e->status }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold text-dark mb-2">
                                <a href="{{ route('events.show', $e->id) }}" class="text-dark text-decoration-none">
                                    {{ $e->title }}
                                </a>
                            </h5>

                            <p class="text-muted small mb-3 flex-grow-1">
                                {{ Str::limit($e->description ?: 'No detailed description provided for this school event.', 110) }}
                            </p>

                            <div class="d-flex flex-column gap-2 text-muted small border-top pt-3 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i
                                        data-lucide="calendar"
                                        style="width: 0.9rem; height: 0.9rem"
                                        class="text-primary"
                                    ></i>
                                    <span class="fw-semibold text-dark">{{ $e->formatted_date_range }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="clock" style="width: 0.9rem; height: 0.9rem" class="text-info"></i>
                                    <span>{{ $e->formatted_time_range }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i
                                        data-lucide="map-pin"
                                        style="width: 0.9rem; height: 0.9rem"
                                        class="text-danger"
                                    ></i>
                                    <span class="text-truncate">{{ $e->location }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i
                                        data-lucide="users"
                                        style="width: 0.9rem; height: 0.9rem"
                                        class="text-success"
                                    ></i>
                                    <span>Target: <strong>{{ $e->target_audience }}</strong></span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <div>
                                    @if ($e->budget_pkr)
                                        <span class="badge bg-warning text-dark fw-bold"
                                            >Rs. {{ number_format($e->budget_pkr) }} PKR</span
                                        >
                                    @endif
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a
                                        href="{{ route('events.show', $e->id) }}"
                                        class="btn btn-light text-primary"
                                        title="View Showcase"
                                    >
                                        <i data-lucide="eye" style="width: 0.9rem; height: 0.9rem"></i>
                                    </a>
                                    <a
                                        href="{{ route('events.edit', $e->id) }}"
                                        class="btn btn-light text-secondary"
                                        title="Edit Event"
                                    >
                                        <i data-lucide="pencil" style="width: 0.9rem; height: 0.9rem"></i>
                                    </a>
                                    <form
                                        action="{{ route('events.destroy', $e->id) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this event?');"
                                    >
                                        @csrf
                                        @method ('DELETE')
                                        <button type="submit" class="btn btn-light text-danger" title="Delete">
                                            <i data-lucide="trash-2" style="width: 0.9rem; height: 0.9rem"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center text-muted card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <i
                            data-lucide="calendar-x"
                            style="width: 3rem; height: 3rem"
                            class="text-muted opacity-50 mb-3"
                        ></i>
                        <h6>No School Events Found</h6>
                        <p class="small mb-3">Schedule a new academic, sports, or cultural event on the school calendar.</p>
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
                            <i data-lucide="calendar-plus" style="width: 0.9rem; height: 0.9rem" class="me-1"></i>
                            Schedule Event
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($events->hasPages())
            <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm">
                <span class="text-muted small"
                    >Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events</span
                >
                {{ $events->links() }}
            </div>
        @endif
    </div>
@endsection
