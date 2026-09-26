@extends('layouts.app')

@section('title', '')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lost-found.css') }}">
@endpush

@section('content')



        <div class="content-header">
          <div>
            <h1 class="page-title">Lost & Found</h1>
            <p class="page-subtitle">Item register and claim tracking</p>
          </div>
          <div class="content-header-actions">
            <button class="btn btn-primary btn-sm"><i data-lucide="plus" style="width:1rem;height:1rem;"></i> Register Item</button>
          </div>
        </div>

        <div class="filter-bar">
          <select class="form-select"><option>All Status</option><option>Lost</option><option>Found</option><option>Claimed</option></select>
          <button class="btn btn-secondary btn-sm ms-auto"><i data-lucide="filter" style="width:1rem;height:1rem;"></i> Filter</button>
        </div>
<div class="table-container">
          <table class="table">
            <thead><tr><th>Item</th><th>Category</th><th>Location</th><th>Date</th><th>Reported By</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              <tr><td>Blue Water Bottle</td><td>Accessories</td><td>Playground</td><td>Jul 2</td><td>Ahmed</td><td><span class="badge badge-success">Found</span></td><td class="actions"><button class="btn btn-ghost btn-icon-sm"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></button><button class="btn btn-ghost btn-icon-sm"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></button><button class="btn btn-ghost btn-icon-sm text-danger"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>Math Notebook</td><td>Stationery</td><td>Class 5-A</td><td>Jul 1</td><td>Fatima</td><td><span class="badge badge-warning">Lost</span></td><td class="actions"><button class="btn btn-ghost btn-icon-sm"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></button><button class="btn btn-ghost btn-icon-sm"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></button><button class="btn btn-ghost btn-icon-sm text-danger"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
              <tr><td>Black Wallet</td><td>Accessories</td><td>Cafeteria</td><td>Jun 30</td><td>Hassan</td><td><span class="badge badge-secondary">Claimed</span></td><td class="actions"><button class="btn btn-ghost btn-icon-sm"><i data-lucide="eye" style="width:0.875rem;height:0.875rem;"></i></button><button class="btn btn-ghost btn-icon-sm"><i data-lucide="pencil" style="width:0.875rem;height:0.875rem;"></i></button><button class="btn btn-ghost btn-icon-sm text-danger"><i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i></button></td></tr>
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
