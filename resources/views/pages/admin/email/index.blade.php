@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/email.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Email</h1>
            <p class="page-subtitle">Send emails and manage email templates</p>
          </div>
          <div class="content-header-actions">
            <button class="btn btn-primary btn-sm"><i data-lucide="send" style="width:1rem;height:1rem;"></i> Compose Email</button>
          </div>
        </div>

        <form class="row g-3">
          <div class="col-md-6">
            <label class="form-label">To</label>
            <select class="form-select"><option>Select Recipients</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Subject</label>
            <input type="text" class="form-control" value="">
          </div>
          <div class="col-md-6">
            <label class="form-label">Template</label>
            <select class="form-select"><option>Select Template</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Body</label>
            <textarea class="form-control" rows="3">Email body...</textarea>
          </div>
        </form>
      
@endsection
