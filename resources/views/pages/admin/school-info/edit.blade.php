@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/school-info-edit.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Edit School Information</h1>
            <p class="page-subtitle">Update school information information</p>
          </div>
          <div class="content-header-actions">
            <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="save" style="width:1rem;height:1rem;"></i> Update</button>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <form class="row g-3">
          <div class="col-md-6">
            <label class="form-label">School Name</label>
            <input type="text" class="form-control" value="EduCore International School" >
          </div>
          <div class="col-md-6">
            <label class="form-label">School Code</label>
            <input type="text" class="form-control" value="ECS-001" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="info@educore.edu" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="tel" class="form-control" value="+92 300 1234567" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Address</label>
            <textarea class="form-control" rows="3" >123 Education Road, Lahore</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Website</label>
            <input type="url" class="form-control" value="https://educore.edu" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Established Year</label>
            <input type="number" class="form-control" value="1995" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Affiliation Board</label>
            <select class="form-select" ><option>BISE</option><option>Option 1</option><option>Option 2</option></select>
          </div>
        </form>
          </div>
        </div>
      
@endsection
