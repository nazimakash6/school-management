@extends('layouts.app')

@section('title', 'Add Transport Route')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        border-color: #dee2e6;
        min-height: 38px;
        border-radius: 0.375rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Content Header --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Add Transport Route &amp; Vehicle
            </h1>
            <p class="text-muted mb-0">Register a new school bus route, fleet vehicle, driver allocation, and pickup itinerary</p>
        </div>
        <div>
            <a href="{{ route('transport.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Back to Routes</span>
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;"></i>
                <strong class="fw-bold">Please fix the following validation errors:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('transport.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- Main Info Column --}}
            <div class="col-lg-8">
                {{-- Section 1: Route & Vehicle Specifications --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="bus" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            1. Route &amp; Vehicle Specifications
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-dark small">Route Title <span class="text-danger">*</span></label>
                                <input type="text" name="route_title" class="form-control" placeholder="e.g. Route 1 - Gulberg & Model Town Express" value="{{ old('route_title') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Route Code</label>
                                <input type="text" name="route_code" class="form-control" placeholder="Auto-generated if blank" value="{{ old('route_code') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Vehicle Registration # <span class="text-danger">*</span></label>
                                <input type="text" name="vehicle_number" class="form-control font-monospace" placeholder="e.g. LEA-4892" value="{{ old('vehicle_number') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Vehicle Model / Make</label>
                                <input type="text" name="vehicle_model" class="form-control" placeholder="e.g. Toyota Coaster 2023 AC" value="{{ old('vehicle_model') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Vehicle Type <span class="text-danger">*</span></label>
                                <select name="vehicle_type" class="form-select" required>
                                    @foreach($vehicleTypes as $vt)
                                        <option value="{{ $vt }}" {{ old('vehicle_type') === $vt ? 'selected' : '' }}>{{ $vt }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Vehicle Ownership <span class="text-danger">*</span></label>
                                <select name="vehicle_ownership" class="form-select" required>
                                    @foreach($vehicleOwnerships as $vo)
                                        <option value="{{ $vo }}" {{ old('vehicle_ownership', 'School Owned') === $vo ? 'selected' : '' }}>{{ $vo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Total Passenger Capacity <span class="text-danger">*</span></label>
                                <input type="number" name="vehicle_capacity" class="form-control" min="1" value="{{ old('vehicle_capacity', 30) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Monthly Transport Fare (PKR) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="fare_amount" class="form-control" min="0" value="{{ old('fare_amount', '150.00') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Assigned Staff Driver --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user-check" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            2. Staff Driver Allocation
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-dark small">Select Staff Driver</label>
                                <select name="driver_id" id="driverSelect" class="form-select" onchange="onDriverSelected(this)">
                                    <option value="">-- Choose Driver from Staff Directory --</option>
                                    @foreach($drivers as $drv)
                                        <option value="{{ $drv->id }}" 
                                                data-name="{{ $drv->full_name }}" 
                                                data-contact="{{ $drv->mobile_no }}" 
                                                data-license="{{ $drv->driving_license_number }}"
                                                {{ old('driver_id') == $drv->id ? 'selected' : '' }}>
                                            {{ $drv->full_name }} ({{ $drv->designation ?: 'Staff' }}) - Tel: {{ $drv->mobile_no }} | Lic #: {{ $drv->driving_license_number ?: 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Driver Name</label>
                                <input type="text" name="driver_name" id="driverNameInput" class="form-control" placeholder="Driver Full Name" value="{{ old('driver_name') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Driver Contact Phone</label>
                                <input type="text" name="driver_contact" id="driverContactInput" class="form-control" placeholder="Mobile Number" value="{{ old('driver_contact') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Driving License Number</label>
                                <input type="text" name="driver_license" id="driverLicenseInput" class="form-control font-monospace" placeholder="License Number" value="{{ old('driver_license') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column: Stops & Remarks --}}
            <div class="col-lg-4">
                {{-- Stops Itinerary Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="map-pin" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            3. Pickup Stops &amp; Schedule
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="pickup_stops" class="form-control mb-3" rows="5" placeholder="1. Stop 1 Name (07:15 AM)&#10;2. Stop 2 Name (07:30 AM)&#10;3. Campus Arrival (08:00 AM)">{{ old('pickup_stops') }}</textarea>
                        <div class="text-muted small" style="font-size:0.75rem;">List major pickup stops and arrival times for student reference.</div>
                    </div>
                </div>

                {{-- Status & Submit Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="settings" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            4. Route Status &amp; Save
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Operational Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Maintenance" {{ old('status') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="Suspended" {{ old('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark small">Remarks / Notes</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="e.g. AC shuttle route for morning shift.">{{ old('note') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top p-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i data-lucide="save" style="width:1rem;height:1rem;"></i>
                            <span>Save Route</span>
                        </button>
                        <a href="{{ route('transport.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function onDriverSelected(selectElem) {
    if (!selectElem) return;
    const selectedOption = selectElem.options ? selectElem.options[selectElem.selectedIndex] : null;
    if (selectedOption && selectedOption.value) {
        document.getElementById('driverNameInput').value = selectedOption.getAttribute('data-name') || '';
        document.getElementById('driverContactInput').value = selectedOption.getAttribute('data-contact') || '';
        document.getElementById('driverLicenseInput').value = selectedOption.getAttribute('data-license') || '';
    } else {
        document.getElementById('driverNameInput').value = '';
        document.getElementById('driverContactInput').value = '';
        document.getElementById('driverLicenseInput').value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        const $driverSelect = $('#driverSelect');
        $driverSelect.select2({
            theme: 'bootstrap-5',
            placeholder: '-- Choose Driver from Staff Directory --',
            allowClear: true,
            width: '100%'
        });

        $driverSelect.on('change', function() {
            onDriverSelected(this);
        });
    }
});
</script>
@endpush
@endsection
