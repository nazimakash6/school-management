@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sections-create.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Create Sections</h1>
            <p class="page-subtitle">Add a new sections</p>
          </div>
          <div class="content-header-actions">
            <button type="submit" class="btn btn-primary btn-sm"><i data-lucide="save" style="width:1rem;height:1rem;"></i> Save</button>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <form class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Section Name</label>
            <input type="text" class="form-control" value="A" >
          </div>
          <div class="col-md-6">
            <label class="form-label">Class</label>
            <select class="form-select" ><option>Class 5</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Class Teacher</label>
            <select class="form-select" ><option>Sarah Ahmad</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Room</label>
            <input type="text" class="form-control" value="Room 101" >
          </div>
        </form>
          </div>
        </div>
      
@endsection
