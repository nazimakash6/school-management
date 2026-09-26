@extends('layouts.app')

@section('title', 'Transport & Fleet Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Logistics & Infrastructure</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Transport & Fleet</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="bus" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">School Transport & Fleet Management</h3>
                    <p class="text-muted mb-0 fs-7">Manage bus routes, vehicle fleet, staff drivers, seat capacity, and student transport fees</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-outline-success btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium" data-bs-toggle="modal" data-bs-target="#addDriverStaffModal">
                <i data-lucide="user-plus" style="width:1rem;height:1rem;"></i> Add Driver in Staff
            </button>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;" data-bs-toggle="modal" data-bs-target="#assignStudentModal">
                <i data-lucide="user-check" style="width:1rem;height:1rem;"></i> Assign Student Transport
            </button>
            <a href="{{ route('transport.create') }}" class="btn btn-outline-primary btn-sm px-3 shadow-2xs d-inline-flex align-items-center gap-1.5 fw-medium">
                <i data-lucide="plus-circle" style="width:1rem;height:1rem;"></i> Add Route / Vehicle
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="check-circle" class="text-success" style="width:1.25rem;height:1.25rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="alert-circle" class="text-danger" style="width:1.25rem;height:1.25rem;"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Summary Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm transport-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Transport Routes</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total_routes'] }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                        <i data-lucide="route" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm transport-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Active Passengers</span>
                        <h3 class="fw-bold text-info mb-0 mt-1">{{ $stats['total_passengers'] }}</h3>
                    </div>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-3">
                        <i data-lucide="users" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm transport-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Total Fleet Seats</span>
                        <h3 class="fw-bold text-purple mb-0 mt-1">{{ $stats['total_capacity'] }}</h3>
                    </div>
                    <div class="p-3 bg-purple-subtle text-purple rounded-3">
                        <i data-lucide="armchair" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm transport-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Monthly Revenue</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">Rs. {{ number_format($stats['total_revenue'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                        <i data-lucide="banknote" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Container --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-tabs-transport border-0" id="transportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab === 'routes' ? 'active' : '' }}" href="{{ route('transport.index', ['tab' => 'routes']) }}">
                        <i data-lucide="bus" style="width:1.1rem;height:1.1rem;" class="me-2"></i>
                        Transport Routes &amp; Vehicles ({{ $stats['total_routes'] }})
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab === 'roster' ? 'active' : '' }}" href="{{ route('transport.index', ['tab' => 'roster']) }}">
                        <i data-lucide="user-check" style="width:1.1rem;height:1.1rem;" class="me-2"></i>
                        Student Passenger Roster ({{ $stats['total_passengers'] }})
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            @if($activeTab === 'routes')
                {{-- TAB 1: ROUTES & VEHICLES CATALOG --}}
                <div class="p-3 bg-light border-bottom">
                    <form action="{{ route('transport.index') }}" method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="tab" value="routes">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0"><i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search route title, vehicle #, driver..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <select name="vehicle_type" class="form-select form-select-sm">
                                <option value="">All Vehicle Types</option>
                                <option value="Bus" {{ request('vehicle_type') === 'Bus' ? 'selected' : '' }}>School Bus</option>
                                <option value="Coaster" {{ request('vehicle_type') === 'Coaster' ? 'selected' : '' }}>Toyota Coaster</option>
                                <option value="Van" {{ request('vehicle_type') === 'Van' ? 'selected' : '' }}>Passenger Van</option>
                                <option value="Auto Ricksha" {{ request('vehicle_type') === 'Auto Ricksha' ? 'selected' : '' }}>Auto Ricksha</option>
                                <option value="Chandi Gari" {{ request('vehicle_type') === 'Chandi Gari' ? 'selected' : '' }}>Chandi Gari</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Maintenance" {{ request('status') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-1">
                                <i data-lucide="filter" style="width:0.85rem;height:0.85rem;"></i> Filter Routes
                            </button>
                            <a href="{{ route('transport.index', ['tab' => 'routes']) }}" class="btn btn-outline-secondary btn-sm px-3">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                            <tr>
                                <th class="ps-3" style="min-width: 130px;">Route Code</th>
                                <th style="min-width: 240px;">Route Title &amp; Stops</th>
                                <th style="min-width: 200px;">Vehicle &amp; Capacity</th>
                                <th style="min-width: 200px;">Assigned Driver Staff</th>
                                <th style="min-width: 120px;">Monthly Fare</th>
                                <th style="min-width: 110px;">Status</th>
                                <th class="pe-3 text-end" style="min-width: 130px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($routes as $route)
                                @php
                                    $assigned = $route->assigned_students_count;
                                    $capacity = $route->vehicle_capacity;
                                    $percent = $capacity > 0 ? min(100, round(($assigned / $capacity) * 100)) : 0;
                                @endphp
                                <tr>
                                    {{-- Route Code --}}
                                    <td class="ps-3">
                                        <span class="badge bg-dark bg-opacity-10 text-dark font-monospace text-uppercase" style="font-size:0.75rem;">
                                            {{ $route->route_code }}
                                        </span>
                                    </td>

                                    {{-- Title & Stops --}}
                                    <td>
                                        <a href="{{ route('transport.show', $route->id) }}" class="fw-bold text-dark text-decoration-none hover-primary mb-1 d-block">
                                            {{ $route->route_title }}
                                        </a>
                                        <div class="text-muted small text-truncate" style="max-width: 280px; font-size:0.75rem;">
                                            <i data-lucide="map-pin" class="text-secondary me-1" style="width:0.75rem;height:0.75rem;"></i>
                                            {{ Str::limit(str_replace("\n", " · ", $route->pickup_stops), 60) ?: 'No stops listed' }}
                                        </div>
                                    </td>

                                    {{-- Vehicle & Capacity --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge {{ $route->vehicle_type_badge_class }}">
                                                {{ $route->vehicle_type }}
                                            </span>
                                            <span class="fw-bold text-dark font-monospace small">{{ $route->vehicle_number }}</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between text-muted small" style="font-size:0.72rem;">
                                            <span>Occupancy:</span>
                                            <span class="fw-bold text-dark">{{ $assigned }} / {{ $capacity }} Seats</span>
                                        </div>
                                        <div class="capacity-progress mt-1">
                                            <div class="progress-bar {{ $percent >= 90 ? 'bg-danger' : ($percent >= 70 ? 'bg-warning' : 'bg-success') }}" role="progressbar" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </td>

                                    {{-- Driver --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="driver-avatar-box">
                                                <i data-lucide="user" style="width:1.2rem;height:1.2rem;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark small">{{ $route->driver_name ?: 'Driver Unassigned' }}</div>
                                                <div class="text-muted small" style="font-size:0.72rem;">
                                                    <i data-lucide="phone" style="width:0.7rem;height:0.7rem;"></i> {{ $route->driver_contact ?: 'N/A' }}
                                                </div>
                                                @if($route->driver_license)
                                                    <div class="text-muted font-monospace" style="font-size:0.68rem;">Lic: {{ $route->driver_license }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Fare --}}
                                    <td>
                                        <div class="fw-bold text-success fs-6">Rs. {{ number_format($route->fare_amount, 2) }}</div>
                                        <div class="text-muted small" style="font-size:0.7rem;">per month</div>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="badge {{ $route->status_badge_class }}">
                                            {{ $route->status }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="pe-3 text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-light border text-primary" title="Assign Student" onclick="openQuickAssignModal({{ $route->id }}, '{{ addslashes($route->route_title) }}', {{ $route->fare_amount }})">
                                                <i data-lucide="user-plus" style="width:0.9rem;height:0.9rem;"></i>
                                            </button>
                                            <a href="{{ route('transport.show', $route->id) }}" class="btn btn-light border" title="View Details & Roster">
                                                <i data-lucide="eye" style="width:0.9rem;height:0.9rem;"></i>
                                            </a>
                                            <a href="{{ route('transport.edit', $route->id) }}" class="btn btn-light border" title="Edit Route">
                                                <i data-lucide="pencil" style="width:0.9rem;height:0.9rem;"></i>
                                            </a>
                                            <button type="button" class="btn btn-light border text-danger" onclick="confirmRouteDelete({{ $route->id }})" title="Delete Route">
                                                <i data-lucide="trash-2" style="width:0.9rem;height:0.9rem;"></i>
                                            </button>
                                        </div>

                                        <form id="delete-route-form-{{ $route->id }}" action="{{ route('transport.destroy', $route->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-lucide="bus" style="width:3rem;height:3rem;" class="mb-3 text-secondary opacity-50"></i>
                                            <h5>No Transport Routes Found</h5>
                                            <p class="small mb-3">Add transport routes or filter criteria.</p>
                                            <a href="{{ route('transport.create') }}" class="btn btn-primary btn-sm">
                                                <i data-lucide="plus" style="width:0.9rem;height:0.9rem;"></i> Add New Route
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($routes->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Showing {{ $routes->firstItem() }} to {{ $routes->lastItem() }} of {{ $routes->total() }} routes</span>
                            <div>{{ $routes->links() }}</div>
                        </div>
                    </div>
                @endif

            @else
                {{-- TAB 2: STUDENT PASSENGER ROSTER --}}
                <div class="p-3 bg-light border-bottom">
                    <form action="{{ route('transport.index') }}" method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="tab" value="roster">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0"><i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i></span>
                                <input type="text" name="roster_search" class="form-control border-start-0" placeholder="Search student name, admission #, pickup stop, route..." value="{{ request('roster_search') }}">
                            </div>
                        </div>

                        <div class="col-md-6 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-1">
                                <i data-lucide="filter" style="width:0.85rem;height:0.85rem;"></i> Filter Roster
                            </button>
                            <a href="{{ route('transport.index', ['tab' => 'roster']) }}" class="btn btn-outline-secondary btn-sm px-3">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                            <tr>
                                <th class="ps-3" style="min-width: 200px;">Student Passenger</th>
                                <th style="min-width: 220px;">Assigned Route &amp; Code</th>
                                <th style="min-width: 200px;">Pickup Stop</th>
                                <th style="min-width: 140px;">Pickup / Drop Time</th>
                                <th style="min-width: 120px;">Monthly Fare</th>
                                <th style="min-width: 110px;">Status</th>
                                <th class="pe-3 text-end" style="min-width: 110px;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($roster as $item)
                                <tr>
                                    {{-- Student --}}
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">
                                            {{ $item->student->full_name ?? ($item->student->first_name . ' ' . $item->student->last_name) }}
                                        </div>
                                        <div class="text-muted small">
                                            Adm #: <span class="fw-semibold text-dark">{{ $item->student->admission_number ?? 'N/A' }}</span>
                                        </div>
                                    </td>

                                    {{-- Route --}}
                                    <td>
                                        <a href="{{ route('transport.show', $item->transport_id) }}" class="fw-semibold text-dark text-decoration-none hover-primary">
                                            {{ $item->route->route_title ?? 'N/A' }}
                                        </a>
                                        <div class="text-muted small font-monospace" style="font-size:0.72rem;">{{ $item->route->route_code ?? '' }}</div>
                                    </td>

                                    {{-- Pickup Stop --}}
                                    <td>
                                        <div class="small fw-semibold text-dark">
                                            <i data-lucide="map-pin" class="text-primary me-1" style="width:0.8rem;height:0.8rem;"></i>
                                            {{ $item->stop_name ?: 'Main Stop' }}
                                        </div>
                                    </td>

                                    {{-- Pickup / Drop Time --}}
                                    <td class="small">
                                        <div class="fw-semibold text-success">
                                            <i data-lucide="clock" style="width:0.75rem;height:0.75rem;"></i> {{ $item->pickup_time ?: '07:30 AM' }}
                                        </div>
                                        <div class="text-muted" style="font-size:0.72rem;">Drop: {{ $item->drop_time ?: '02:30 PM' }}</div>
                                    </td>

                                    {{-- Monthly Fare --}}
                                    <td>
                                        <span class="fw-bold text-dark">Rs. {{ number_format($item->monthly_fare, 2) }}</span>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="badge {{ $item->status_badge_class }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>

                                    {{-- Action --}}
                                    <td class="pe-3 text-end">
                                        <form action="{{ route('transport.remove-student', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove student from transport route?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Unassign Student">
                                                <i data-lucide="user-minus" style="width:0.85rem;height:0.85rem;"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-lucide="users" style="width:3rem;height:3rem;" class="mb-3 text-secondary opacity-50"></i>
                                            <h5>No Student Passengers Allocated</h5>
                                            <p class="small mb-3">Assign students to transport routes to build your passenger roster.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($roster->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Showing {{ $roster->firstItem() }} to {{ $roster->lastItem() }} of {{ $roster->total() }} passenger allocations</span>
                            <div>{{ $roster->links() }}</div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Add Driver to Staff Modal --}}
<div class="modal fade" id="addDriverStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form action="{{ route('transport.add-driver-staff') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="user-plus" style="width:1.2rem;height:1.2rem;"></i>
                        Add New Driver to Staff Directory
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="e.g. Salim" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="e.g. Khan">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Mobile Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile_no" class="form-control" placeholder="e.g. 0300-1234567" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Official / Contact Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. salim.driver@school.edu" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Driving License Number <span class="text-danger">*</span></label>
                            <input type="text" name="driving_license_number" class="form-control" placeholder="e.g. HTV-LHR-998877" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">License Expiry Date</label>
                            <input type="date" name="driving_license_expiry" class="form-control" value="{{ \Carbon\Carbon::now()->addYears(3)->format('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">CNIC Number</label>
                            <input type="text" name="cnic" class="form-control" placeholder="e.g. 35202-1234567-9">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Monthly Salary (PKR)</label>
                            <input type="number" name="salary" class="form-control" value="45000">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 d-inline-flex align-items-center gap-2">
                        <i data-lucide="check-circle" style="width:1rem;height:1rem;"></i>
                        <span>Register Driver in Staff</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Assign Student Transport Modal --}}
<div class="modal fade" id="assignStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form action="{{ route('transport.assign-student') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="user-check" style="width:1.2rem;height:1.2rem;"></i>
                        Assign Student to Transport Route
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select Route <span class="text-danger">*</span></label>
                        <select name="transport_id" id="assignRouteSelect" class="form-select" onchange="updateDefaultFare(this)" required>
                            <option value="">-- Choose Transport Route --</option>
                            @foreach($availableRoutes as $ar)
                                <option value="{{ $ar->id }}" data-fare="{{ $ar->fare_amount }}">
                                    {{ $ar->route_title }} ({{ $ar->vehicle_number }}) - Available Seats: {{ $ar->available_capacity }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select Student <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->first_name }} {{ $st->last_name }} (Adm #: {{ $st->admission_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Pickup Stop Name</label>
                        <input type="text" name="stop_name" class="form-control" placeholder="e.g. Model Town Park Main Gate">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Pickup Time</label>
                            <input type="text" name="pickup_time" class="form-control" value="07:30 AM">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Drop Time</label>
                            <input type="text" name="drop_time" class="form-control" value="02:30 PM">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-dark small">Monthly Fare Amount (PKR)</label>
                        <input type="number" step="0.01" name="monthly_fare" id="assignMonthlyFareInput" class="form-control" placeholder="150.00">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Assign Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openQuickAssignModal(routeId, routeTitle, fare) {
        const select = document.getElementById('assignRouteSelect');
        select.value = routeId;
        document.getElementById('assignMonthlyFareInput').value = fare;
        const modal = new bootstrap.Modal(document.getElementById('assignStudentModal'));
        modal.show();
    }

    function updateDefaultFare(selectElem) {
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const fare = selectedOption.getAttribute('data-fare');
        if (fare) {
            document.getElementById('assignMonthlyFareInput').value = fare;
        }
    }

    function confirmRouteDelete(id) {
        if (confirm('Are you sure you want to delete this transport route?')) {
            document.getElementById(`delete-route-form-${id}`).submit();
        }
    }
</script>
@endpush
@endsection
