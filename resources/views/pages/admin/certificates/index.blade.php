@extends('layouts.app')
@section('title', 'Certificate Management - Noor Ul Huda Superior School')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Academic Operations</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Certificates</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="award" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">Official Certificate Center</h3>
                    <p class="text-muted mb-0 fs-7">Generate and print official school certificates, leaving certificates & performance awards</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate Type Cards -->
    <div class="row g-4">
        @foreach($types as $key => $type)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 certificate-type-card" style="transition: all .2s;">
                <div class="card-body p-4 text-center d-flex flex-column">
                    <div class="mx-auto mb-3 rounded-3 d-flex align-items-center justify-content-center bg-{{ $type['color'] }} bg-opacity-10" style="width:72px;height:72px;">
                        <i data-lucide="{{ $type['icon'] }}" class="text-{{ $type['color'] }}" style="width:2rem;height:2rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ $type['title'] }}</h5>
                    <p class="text-muted small flex-grow-1 mb-4">{{ $type['description'] }}</p>
                    <a href="{{ route('certificates.create', ['type' => $key]) }}" class="btn btn-{{ $type['color'] }} fw-semibold w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="file-plus" style="width:1rem;height:1rem;"></i>
                        Generate Certificate
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Info Banner -->
    <div class="alert alert-info border-0 shadow-sm rounded-3 mt-4 d-flex align-items-center gap-3">
        <i data-lucide="info" style="width:1.5rem;height:1.5rem;" class="text-info flex-shrink-0"></i>
        <div>
            <strong>How it works:</strong> Select a certificate type above → Fill in the student details → Click <strong>"Generate PDF"</strong> → Your browser will open a print-ready certificate — use <kbd>Ctrl+P</kbd> to save as PDF or print directly.
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.certificate-type-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -6px rgba(0,0,0,0.12) !important;
}
</style>
@endpush
