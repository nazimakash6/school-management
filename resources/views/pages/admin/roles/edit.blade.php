@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/roles-edit.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Edit Roles & Permissions</h1>
            <p class="page-subtitle">Update roles & permissions information</p>
          </div>
          <div class="content-header-actions">
            <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="save" style="width:1rem;height:1rem;"></i> Update</button>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <form class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Role Name</label>
            <input type="text" class="form-control" value="Accountant" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="3" >Manage fees and accounts</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Permissions</label>
            <select class="form-select" ><option>Select Permissions</option><option>Option 1</option><option>Option 2</option></select>
          </div>
        </form>
          </div>
        </div>
      
@endsection
