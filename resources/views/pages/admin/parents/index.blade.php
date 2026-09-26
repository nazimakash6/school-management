@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/parents.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Parent Management</h1>
            <p class="page-subtitle">Parent profiles, guardians, and emergency contacts</p>
          </div>
          <div class="content-header-actions">
            <a href="parents-create.html" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:1rem;height:1rem;"></i> Add Parent</a>
          </div>
        </div>

        <div class="filter-bar">
          <input type="text" class="form-control" placeholder="Search parents...">
          <select class="form-select"><option>All Relations</option><option>Father</option><option>Mother</option><option>Guardian</option></select>
          <button class="btn btn-secondary btn-sm ms-auto"><i data-lucide="filter" style="width:1rem;height:1rem;"></i> Filter</button>
        </div>
<div class="table-container">
          <table class="table">
            <thead><tr><th>Parent Name</th><th>Relation</th><th>Student</th><th>Contact</th><th>Email</th><th>Actions</th></tr></thead>
            <tbody>
              <tr><td>Kamran Khan</td><td>Father</td><td>Ahmed Khan</td><td>+92 300 1111111</td><td>kamran@email.com</td><td class="actions"><a href="parents-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="parents-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>Ali Raza</td><td>Father</td><td>Fatima Ali</td><td>+92 300 2222222</td><td>ali@email.com</td><td class="actions"><a href="parents-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="parents-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>Raza Ahmed</td><td>Guardian</td><td>Hassan Raza</td><td>+92 300 3333333</td><td>raza@email.com</td><td class="actions"><a href="parents-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="parents-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
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
