@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sms.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">SMS</h1>
            <p class="page-subtitle">Send SMS to students, parents, and staff</p>
          </div>
          <div class="content-header-actions">
            <button class="btn btn-primary btn-sm"><i data-lucide="send" style="width:1rem;height:1rem;"></i> Send SMS</button>
          </div>
        </div>

        <form class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Recipients</label>
            <select class="form-select"><option>Select Group</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Template</label>
            <select class="form-select"><option>Select Template</option><option>Option 1</option><option>Option 2</option></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Message</label>
            <textarea class="form-control" rows="3">Type your message here...</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Schedule</label>
            <input type="datetime-local" class="form-control" value="">
          </div>
        </form>
      
@endsection
