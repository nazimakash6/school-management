@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sections.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Sections</h1>
            <p class="page-subtitle">Manage class sections</p>
          </div>
          <div class="content-header-actions">
            <a href="sections-create.html" class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:1rem;height:1rem;"></i> Add Section</a>
          </div>
        </div>

        <div class="filter-bar">
          <select class="form-select"><option>All Classes</option><option>Class 1</option><option>Class 5</option><option>Class 8</option><option>Class 10</option></select>
          <button class="btn btn-secondary btn-sm ms-auto"><i data-lucide="filter" style="width:1rem;height:1rem;"></i> Filter</button>
        </div>
<div class="table-container">
          <table class="table">
            <thead><tr><th>Section</th><th>Class</th><th>Class Teacher</th><th>Students</th><th>Room</th><th>Actions</th></tr></thead>
            <tbody>
              <tr><td>A</td><td>Class 5</td><td>Sarah Ahmad</td><td>42</td><td>Room 101</td><td class="actions"><a href="sections-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="sections-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>B</td><td>Class 5</td><td>Ayesha Khan</td><td>38</td><td>Room 102</td><td class="actions"><a href="sections-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="sections-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>C</td><td>Class 5</td><td>Hassan Raza</td><td>40</td><td>Room 103</td><td class="actions"><a href="sections-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="sections-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>A</td><td>Class 8</td><td>Omar Farooq</td><td>35</td><td>Room 201</td><td class="actions"><a href="sections-detail.html" class="btn btn-ghost btn-icon-sm" title="View"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></a><a href="sections-edit.html" class="btn btn-ghost btn-icon-sm" title="Edit"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></a><button class="btn btn-ghost btn-icon-sm text-danger" title="Delete"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
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
