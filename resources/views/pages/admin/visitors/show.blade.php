@extends('layouts.app')

@section('title', 'Visitor Pass #' . $visitor->pass_code)

@push('styles')
<style>
.visitor-badge-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 16px;
    color: #ffffff;
}
.barcode-box {
    background: #ffffff;
    color: #000000;
    font-family: monospace;
    font-weight: 700;
    letter-spacing: 2px;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    display: inline-block;
}
@media print {
    body * {
        visibility: hidden;
    }
    #printableVisitorPass, #printableVisitorPass * {
        visibility: visible;
    }
    #printableVisitorPass {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('visitors.index') }}">Visitors</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Visitor Pass Card</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0">Visitor Security Gate Pass</h1>
            <p class="text-muted small mb-0">Official entry pass receipt, identity verification & gate timestamp</p>
        </div>
        <div class="d-flex gap-2">
            @if($visitor->status === 'Checked-In')
                <form action="{{ route('visitors.check-out', $visitor->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm fw-bold shadow-sm">
                        <i data-lucide="log-out" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Instant Check-Out
                    </button>
                </form>
            @endif
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-semibold shadow-sm">
                <i data-lucide="printer" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Print Pass Badge
            </button>
            <a href="{{ route('visitors.edit', $visitor->id) }}" class="btn btn-outline-warning btn-sm">
                <i data-lucide="pencil" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Edit
            </a>
            <a href="{{ route('visitors.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:0.9rem;height:0.9rem;" class="me-1"></i> Back to Register
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm no-print" role="alert">
            <i data-lucide="check-circle-2" class="me-2" style="width:1.2rem;height:1.2rem;"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div id="printableVisitorPass">
        <!-- Pass Hero Card -->
        <div class="visitor-badge-card p-4 mb-4 shadow">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="barcode-box">
                            <i data-lucide="qr-code" style="width:1rem;height:1rem;" class="me-1"></i> {{ $visitor->pass_code }}
                        </div>
                        <span class="badge {{ $visitor->status_badge_class }} px-3 py-2 rounded-pill fs-6">
                            {{ $visitor->status }}
                        </span>
                        <span class="badge {{ $visitor->meet_type_badge_class }} px-3 py-2 rounded-pill fs-6">
                            Meet Target: {{ $visitor->meet_type }}
                        </span>
                    </div>

                    <h2 class="fw-bold text-white mb-1">{{ $visitor->visitor_name }}</h2>
                    <p class="text-white-50 mb-3">
                        <i data-lucide="phone" style="width:1rem;height:1rem;" class="me-1"></i> Phone: <strong>{{ $visitor->phone }}</strong>
                        @if($visitor->cnic_id)
                            <span class="mx-2">&bull;</span> CNIC / ID: <strong>{{ $visitor->cnic_id }}</strong>
                        @endif
                    </p>

                    <div class="d-flex flex-wrap gap-4 text-white-50 small border-top border-secondary pt-3">
                        <span><i data-lucide="calendar" style="width:0.9rem;height:0.9rem;" class="me-1 text-primary"></i> Visit Date: <strong class="text-white">{{ $visitor->visit_date ? $visitor->visit_date->format('M d, Y') : 'N/A' }}</strong></span>
                        <span><i data-lucide="door-open" style="width:0.9rem;height:0.9rem;" class="me-1 text-info"></i> Gate: <strong class="text-white">{{ $visitor->gate_no }}</strong></span>
                        <span><i data-lucide="users" style="width:0.9rem;height:0.9rem;" class="me-1 text-warning"></i> Persons: <strong class="text-white">{{ $visitor->num_persons }} Person(s)</strong></span>
                        @if($visitor->vehicle_no)
                            <span><i data-lucide="car" style="width:0.9rem;height:0.9rem;" class="me-1 text-success"></i> Vehicle: <strong class="text-white">{{ $visitor->vehicle_no }}</strong></span>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end text-center">
                    @if($visitor->id_proof_url)
                        <img src="{{ $visitor->id_proof_url }}" alt="Visitor Proof" class="rounded-3 border border-light shadow" style="max-height: 140px; object-fit: cover;">
                    @else
                        <div class="p-4 bg-white bg-opacity-10 rounded-3 d-inline-block text-center border border-secondary" style="min-width: 140px;">
                            <i data-lucide="user-check" style="width:3rem;height:3rem;" class="text-white-50 mb-1"></i>
                            <span class="d-block text-white-50 small">Verified Gate Entry</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Side: Target Person Details -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user-check" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            Target Person / Department Details
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @if($visitor->meet_type === 'Student' && $visitor->student)
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle fw-bold fs-4">
                                    {{ strtoupper(substr($visitor->student->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">{{ $visitor->student->first_name }} {{ $visitor->student->last_name }}</h5>
                                    <span class="badge bg-primary mt-1">Class: {{ $visitor->student->class_name }}</span>
                                    <small class="text-muted d-block mt-1">Roll #: {{ $visitor->student->roll_no }} &bull; Adm #: {{ $visitor->student->admission_no }}</small>
                                </div>
                            </div>
                        @elseif($visitor->meet_type === 'Staff' && $visitor->staff)
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-3">
                                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle fw-bold fs-4">
                                    {{ strtoupper(substr($visitor->staff->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">{{ $visitor->staff->first_name }} {{ $visitor->staff->last_name }}</h5>
                                    <span class="badge bg-info text-dark mt-1">{{ $visitor->staff->designation ?: 'Staff' }}</span>
                                    <small class="text-muted d-block mt-1">Dept: {{ $visitor->staff->department ?: 'General' }} &bull; {{ $visitor->staff->email }}</small>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <h6 class="fw-bold text-dark mb-1">Office / Target Description</h6>
                                <p class="text-dark mb-0">{{ $visitor->person_to_meet ?: 'General School Inquiry' }}</p>
                            </div>
                        @endif

                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted w-40">Purpose of Visit:</th>
                                <td class="fw-bold text-dark">{{ $visitor->purpose }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Issued By Guard / Admin:</th>
                                <td class="text-dark">{{ $visitor->creator ? $visitor->creator->name : 'Security Officer' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Side: Check-In & Check-Out Timestamps -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="clock" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
                            Entry / Exit Timestamps & Duration
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4 text-center">
                            <div class="col-6">
                                <div class="p-3 bg-success bg-opacity-10 border border-success rounded-3">
                                    <span class="text-success small fw-semibold text-uppercase d-block">Check-In Time</span>
                                    <h4 class="fw-bold text-success mb-0 mt-1">
                                        {{ $visitor->check_in_time ? $visitor->check_in_time->format('g:i A') : 'N/A' }}
                                    </h4>
                                    <small class="text-muted">{{ $visitor->check_in_time ? $visitor->check_in_time->format('d M Y') : '' }}</small>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="p-3 {{ $visitor->check_out_time ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-light border' }} rounded-3">
                                    <span class="{{ $visitor->check_out_time ? 'text-danger' : 'text-muted' }} small fw-semibold text-uppercase d-block">Check-Out Time</span>
                                    <h4 class="fw-bold {{ $visitor->check_out_time ? 'text-danger' : 'text-muted' }} mb-0 mt-1">
                                        {{ $visitor->check_out_time ? $visitor->check_out_time->format('g:i A') : 'Still Inside' }}
                                    </h4>
                                    <small class="text-muted">{{ $visitor->check_out_time ? $visitor->check_out_time->format('d M Y') : 'Active Pass' }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold text-dark">Total Stay Duration:</span>
                                <span class="badge bg-dark fs-6">{{ $visitor->duration_text }}</span>
                            </div>
                        </div>

                        @if($visitor->remarks)
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Gatekeeper / Security Notes:</h6>
                                <div class="p-3 bg-light rounded-3 text-dark small">{{ $visitor->remarks }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
