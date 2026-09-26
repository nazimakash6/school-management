@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Notifications</h1>
            <p class="page-subtitle">Send and manage system notifications</p>
          </div>
          <div class="content-header-actions">
            <button class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:1rem;height:1rem;"></i> New Notification</button>
          </div>
        </div>

        <div class="filter-bar">
          <select class="form-select"><option>All Types</option><option>General</option><option>Fee</option><option>Exam</option><option>Event</option></select>
          <select class="form-select"><option>All Status</option><option>Sent</option><option>Scheduled</option><option>Draft</option></select>
          <button class="btn btn-secondary btn-sm ms-auto"><i data-lucide="filter" style="width:1rem;height:1rem;"></i> Filter</button>
        </div>
<div class="table-container">
          <table class="table">
            <thead><tr><th>Title</th><th>Type</th><th>Audience</th><th>Sent Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              <tr><td>Fee Payment Reminder</td><td>Fee</td><td>All Parents</td><td>Jul 2</td><td><span class="badge badge-success">Sent</span></td><td class="actions"><a href="notifications-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="notifications-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>Exam Schedule</td><td>Exam</td><td>Classes 8-10</td><td>Jul 1</td><td><span class="badge badge-success">Sent</span></td><td class="actions"><a href="notifications-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="notifications-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>Sports Day Notice</td><td>Event</td><td>All</td><td>Jun 30</td><td><span class="badge badge-info">Scheduled</span></td><td class="actions"><a href="notifications-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="notifications-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
            </tbody>
          </table>
        </div>
<nav class="mt-3 d-flex justify-content-between align-items-center">
          <span class="text-sm text-tertiary">Showing 1 to 4 of 48 entries</span>
          <ul class="pagination mb-0">
            <li class="page-item disabled"><a class="page-link" href="#"><i data-lucide="chevron-left" style="width:1rem;height:1rem;"></i></a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#"><i data-lucide="chevron-right" style="width:1rem;height:1rem;"></i></a></li>
          </ul>
        </nav>
      
@endsection
