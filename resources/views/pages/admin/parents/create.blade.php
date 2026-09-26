@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/parents-create.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Create Parent Management</h1>
            <p class="page-subtitle">Add a new parent management</p>
          </div>
          <div class="content-header-actions">
            <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="save" style="width:1rem;height:1rem;"></i> Save</button>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <form class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Parent Name</label>
            <input type="text" class="form-control" value="Kamran Khan" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Relation</label>
            <select class="form-select" ><option>Father</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Student</label>
            <select class="form-select" ><option>Ahmed Khan</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Contact</label>
            <input type="tel" class="form-control" value="+92 300 1111111" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="kamran@email.com" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Address</label>
            <textarea class="form-control" rows="3" >123 Street, Lahore</textarea>
          </div>
        </form>
          </div>
        </div>
      
@endsection
