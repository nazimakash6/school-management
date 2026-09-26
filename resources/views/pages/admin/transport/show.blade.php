@extends('layouts.app')

@section('title', 'Route Details - ' . $route->route_title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="bus" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Transport Route &amp; Fleet Details
            </h1>
            <p class="text-muted mb-0">Route Code: <span class="fw-bold font-monospace text-dark">{{ $route->route_code }}</span> · Vehicle: <span class="fw-bold font-monospace text-dark">{{ $route->vehicle_number }}</span></p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('transport.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Back to Routes</span>
            </a>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#assignStudentModal">
                <i data-lucide="user-plus" style="width: 1rem; height: 1rem;"></i>
                <span>Assign Student</span>
            </button>
            <a href="{{ route('transport.edit', $route->id) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i data-lucide="pencil" style="width: 1rem; height: 1rem;"></i>
                <span>Edit Route</span>
            </a>
            <form action="{{ route('transport.destroy', $route->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this transport route?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-1">
                    <i data-lucide="trash-2" style="width: 1rem; height: 1rem;"></i>
                    <span>Delete</span>
                </button>
            </form>
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

    <div class="row g-4">
        {{-- Left Column: Route Specifications & Driver Card --}}
        <div class="col-lg-4">
            {{-- Vehicle & Route Overview Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="bus" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Route Specifications
                    </h6>
                    <span class="badge {{ $route->status_badge_class }}">{{ $route->status }}</span>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-1">{{ $route->route_title }}</h5>
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <span class="badge {{ $route->vehicle_type_badge_class }}">{{ $route->vehicle_type }}</span>
                        <span class="badge bg-secondary bg-opacity-10 text-dark border">{{ $route->vehicle_ownership ?: 'School Owned' }}</span>
                        <span class="fw-bold font-monospace text-dark small">Reg: {{ $route->vehicle_number }}</span>
                    </div>

                    @php
                        $assigned = $route->assigned_students_count;
                        $capacity = $route->vehicle_capacity;
                        $percent = $capacity > 0 ? min(100, round(($assigned / $capacity) * 100)) : 0;
                    @endphp

                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-muted small fw-semibold text-uppercase">Seat Occupancy</span>
                            <span class="fw-bold text-dark fs-6">{{ $assigned }} / {{ $capacity }} Seats</span>
                        </div>
                        <div class="capacity-progress mb-2">
                            <div class="progress-bar {{ $percent >= 90 ? 'bg-danger' : ($percent >= 70 ? 'bg-warning' : 'bg-success') }}" role="progressbar" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="text-muted small" style="font-size:0.75rem;">Available Seats: <strong class="text-success">{{ $route->available_capacity }}</strong></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-success bg-opacity-10 border border-success-subtle rounded-3">
                        <span class="text-success fw-semibold small text-uppercase">Monthly Fare Rate</span>
                        <span class="fw-bold text-success fs-4">Rs. {{ number_format($route->fare_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Staff Driver Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="user-check" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Assigned Driver (Staff Member)
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="driver-avatar-box" style="width:52px;height:52px;font-size:1.2rem;">
                            <i data-lucide="user" style="width:1.5rem;height:1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $route->driver_name ?: 'No Driver Assigned' }}</h6>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border mt-1">Department: Transport</span>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Mobile Contact</span>
                            <span class="fw-bold text-dark">{{ $route->driver_contact ?: 'N/A' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small fw-semibold text-uppercase">Driving License</span>
                            <span class="fw-bold font-monospace text-dark">{{ $route->driver_license ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pickup Stops Itinerary --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="map-pin" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Pickup Stops &amp; Schedule
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-light rounded-3 border text-dark white-space-pre-line">
                        {{ $route->pickup_stops ?: 'No pickup stops listed for this route.' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Passenger Allocations Roster --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="users" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Allocated Student Passenger Roster
                    </h6>
                    <span class="badge bg-light text-dark border">{{ $route->allocations->count() }} students</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                            <tr>
                                <th class="ps-3">Student Name</th>
                                <th>Admission #</th>
                                <th>Pickup Stop</th>
                                <th>Pickup / Drop Time</th>
                                <th>Monthly Fare</th>
                                <th>Status</th>
                                <th class="pe-3 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($route->allocations as $alloc)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">
                                            {{ $alloc->student->full_name ?? ($alloc->student->first_name . ' ' . $alloc->student->last_name) }}
                                        </div>
                                    </td>
                                    <td class="font-monospace small fw-semibold">{{ $alloc->student->admission_number ?? 'N/A' }}</td>
                                    <td class="small fw-semibold text-dark">
                                        <i data-lucide="map-pin" class="text-primary me-1" style="width:0.75rem;height:0.75rem;"></i>
                                        {{ $alloc->stop_name ?: 'Main Stop' }}
                                    </td>
                                    <td class="small">
                                        <div class="fw-semibold text-success">{{ $alloc->pickup_time ?: '07:30 AM' }}</div>
                                        <div class="text-muted" style="font-size:0.7rem;">Drop: {{ $alloc->drop_time ?: '02:30 PM' }}</div>
                                    </td>
                                    <td class="fw-bold text-dark">Rs. {{ number_format($alloc->monthly_fare, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $alloc->status_badge_class }}">{{ $alloc->status }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <form action="{{ route('transport.remove-student', $alloc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove student from this transport route?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i data-lucide="user-minus" style="width:0.85rem;height:0.85rem;"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i data-lucide="users" style="width:2.5rem;height:2.5rem;" class="mb-2 opacity-50 text-secondary"></i>
                                        <div class="fw-semibold">No Students Allocated to this Route</div>
                                        <p class="small mb-3">Click 'Assign Student' to add passengers to this route roster.</p>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignStudentModal">
                                            <i data-lucide="user-plus" style="width:0.85rem;height:0.85rem;"></i> Assign Student Now
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Assign Student Transport Modal --}}
<div class="modal fade" id="assignStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form action="{{ route('transport.assign-student') }}" method="POST">
                @csrf
                <input type="hidden" name="transport_id" value="{{ $route->id }}">
                <div class="modal-header bg-primary text-white py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="user-check" style="width:1.2rem;height:1.2rem;"></i>
                        Assign Student to '{{ $route->route_title }}'
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
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
                        <input type="number" step="0.01" name="monthly_fare" class="form-control" value="{{ $route->fare_amount }}">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Confirm Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
