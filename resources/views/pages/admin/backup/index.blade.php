@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/backup.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Backup & Restore</h1>
            <p class="page-subtitle">Manage database backups and restoration</p>
          </div>
          <div class="content-header-actions">
            <button class="btn btn-primary btn-sm"><i data-lucide="download" style="width:1rem;height:1rem;"></i> Backup Now</button>
          </div>
        </div>

        <div class="filter-bar">
          <select class="form-select"><option>All Types</option><option>Manual</option><option>Automatic</option></select>
          <button class="btn btn-secondary btn-sm ms-auto"><i data-lucide="filter" style="width:1rem;height:1rem;"></i> Filter</button>
        </div>
<div class="table-container">
          <table class="table">
            <thead><tr><th>Backup Date</th><th>Type</th><th>Size</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              <tr><td>Jul 3, 2026 02:00</td><td>Automatic</td><td>2.4 GB</td><td><span class="badge badge-success">Successful</span></td><td class="actions"><a href="backup-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="backup-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>Jul 2, 2026 02:00</td><td>Automatic</td><td>2.3 GB</td><td><span class="badge badge-success">Successful</span></td><td class="actions"><a href="backup-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="backup-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>Jul 1, 2026 14:30</td><td>Manual</td><td>2.3 GB</td><td><span class="badge badge-success">Successful</span></td><td class="actions"><a href="backup-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="backup-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
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
