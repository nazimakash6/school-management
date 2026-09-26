@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/help.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Help Center</h1>
            <p class="page-subtitle">Documentation, FAQs, and support</p>
          </div>
          <div class="content-header-actions">
            <button class="btn btn-primary btn-sm"><i data-lucide="message-circle" style="width:1rem;height:1rem;"></i> Contact Support</button>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-4">
            <div class="card card-hover h-100">
              <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3"><div class="stat-icon" style="background-color: var(--color-primary-100); color: var(--color-primary-700);"><i data-lucide="book-open" style="width:1.25rem;height:1.25rem;"></i></div><h5 class="mb-0 fw-bold">Getting Started</h5></div>
                <p class="text-secondary mb-0">Learn the basics of EduCore ERP</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="card card-hover h-100">
              <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3"><div class="stat-icon" style="background-color: var(--color-primary-100); color: var(--color-primary-700);"><i data-lucide="file-text" style="width:1.25rem;height:1.25rem;"></i></div><h5 class="mb-0 fw-bold">User Guide</h5></div>
                <p class="text-secondary mb-0">Detailed documentation for all modules</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="card card-hover h-100">
              <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3"><div class="stat-icon" style="background-color: var(--color-primary-100); color: var(--color-primary-700);"><i data-lucide="help-circle" style="width:1.25rem;height:1.25rem;"></i></div><h5 class="mb-0 fw-bold">FAQs</h5></div>
                <p class="text-secondary mb-0">Frequently asked questions</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="card card-hover h-100">
              <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3"><div class="stat-icon" style="background-color: var(--color-primary-100); color: var(--color-primary-700);"><i data-lucide="video" style="width:1.25rem;height:1.25rem;"></i></div><h5 class="mb-0 fw-bold">Video Tutorials</h5></div>
                <p class="text-secondary mb-0">Step-by-step video guides</p>
              </div>
            </div>
          </div>
        </div>
      
@endsection
