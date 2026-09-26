@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sections-detail.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Sections Details</h1>
            <p class="page-subtitle">View complete sections information</p>
          </div>
          <div class="content-header-actions">
            <a href="sections.html" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back</a>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="row g-4">
          <div class="col-lg-4">
            <div class="card">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3"><img src="https://ui-avatars.com/api/?name=Sections&background=6366f1&color=fff" alt="Sections"></div>
                <h5 class="fw-bold mb-1">Sections</h5>
                <p class="text-tertiary mb-3">Active</p>
                <div class="d-flex gap-2 justify-content-center"><button class="btn btn-secondary btn-sm"><i data-lucide="pencil" style="width:1rem;height:1rem;"></i> Edit</button><button class="btn btn-danger btn-sm"><i data-lucide="trash-2" style="width:1rem;height:1rem;"></i> Delete</button></div>
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header"><h5 class="mb-0 fw-bold">Details</h5></div>
              <div class="card-body">
<form class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Section Name</label>
            <input type="text" class="form-control" value="A" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Class</label>
            <select class="form-select" disabled><option>Class 5</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Class Teacher</label>
            <select class="form-select" disabled><option>Sarah Ahmad</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Room</label>
            <input type="text" class="form-control" value="Room 101" disabled>
          </div>
        </form>              </div>
            </div>
          </div>
        </div>
          </div>
        </div>
      
@endsection
