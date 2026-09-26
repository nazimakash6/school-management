@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/parents-detail.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Parent Management Details</h1>
            <p class="page-subtitle">View complete parent management information</p>
          </div>
          <div class="content-header-actions">
            <a href="parents.html" class="btn btn-secondary btn-sm"><i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back</a>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="row g-4">
          <div class="col-lg-4">
            <div class="card">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3"><img src="https://ui-avatars.com/api/?name=Parent+Management&background=6366f1&color=fff" alt="Parent Management"></div>
                <h5 class="fw-bold mb-1">Parent Management</h5>
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
            <label class="form-label">Parent Name</label>
            <input type="text" class="form-control" value="Kamran Khan" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Relation</label>
            <select class="form-select" disabled><option>Father</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Student</label>
            <select class="form-select" disabled><option>Ahmed Khan</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Contact</label>
            <input type="tel" class="form-control" value="+92 300 1111111" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="kamran@email.com" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">Address</label>
            <textarea class="form-control" rows="3" disabled>123 Street, Lahore</textarea>
          </div>
        </form>              </div>
            </div>
          </div>
        </div>
          </div>
        </div>
      
@endsection
