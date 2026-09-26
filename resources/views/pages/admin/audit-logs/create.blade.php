@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/audit-logs-create.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Create Audit Logs</h1>
            <p class="page-subtitle">Add a new audit logs</p>
          </div>
          <div class="content-header-actions">
            <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="save" style="width:1rem;height:1rem;"></i> Save</button>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <form class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" value="" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="3" ></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select class="form-select" ><option>Active</option><option>Option 1</option><option>Option 2</option></select>
          </div>
        </form>
          </div>
        </div>
      
@endsection
