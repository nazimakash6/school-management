@extends('layouts.app')

@section('title', 'Inventory Management - School Assets')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS -->
    <div class="content-header mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Logistics & Infrastructure</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Inventory & Assets</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5">
                <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i data-lucide="package" style="width:1.5rem;height:1.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-dark tracking-tight">School Inventory & Asset Management</h3>
                    <p class="text-muted mb-0 fs-7">Track IT assets, furniture, lab supplies, and stock with verified image proof</p>
                </div>
            </div>
        </div>
        <div class="content-header-actions">
            <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                <i data-lucide="plus-circle" style="width:1rem;height:1rem;"></i> Add Inventory Item
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="check-circle" class="text-success" style="width:1.25rem;height:1.25rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Analytics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm inventory-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Total Stock Value</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">Rs. {{ number_format($stats['total_value'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                        <i data-lucide="banknote" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm inventory-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Total Items Stocked</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total_items'] }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                        <i data-lucide="box" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm inventory-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Low Stock Alert</span>
                        <h3 class="fw-bold text-warning mb-0 mt-1">{{ $stats['low_stock'] }}</h3>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3">
                        <i data-lucide="alert-triangle" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm inventory-stat-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small text-uppercase">Items In Use</span>
                        <h3 class="fw-bold text-purple mb-0 mt-1">{{ $stats['in_use_count'] }}</h3>
                    </div>
                    <div class="p-3 bg-purple-subtle text-purple rounded-3">
                        <i data-lucide="check-circle" style="width:1.5rem;height:1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('inventory.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted border-end-0"><i data-lucide="search" style="width:0.9rem;height:0.9rem;"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search item name, code, supplier..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="Available" {{ request('status') === 'Available' ? 'selected' : '' }}>Available in Store</option>
                        <option value="In Use" {{ request('status') === 'In Use' ? 'selected' : '' }}>In Use</option>
                        <option value="Low Stock" {{ request('status') === 'Low Stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="Out of Stock" {{ request('status') === 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
                        <option value="Maintenance" {{ request('status') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="item_condition" class="form-select form-select-sm">
                        <option value="">All Conditions</option>
                        <option value="New / Excellent" {{ request('item_condition') === 'New / Excellent' ? 'selected' : '' }}>New / Excellent</option>
                        <option value="Good" {{ request('item_condition') === 'Good' ? 'selected' : '' }}>Good</option>
                        <option value="Fair" {{ request('item_condition') === 'Fair' ? 'selected' : '' }}>Fair</option>
                        <option value="Damaged / Repair Needed" {{ request('item_condition') === 'Damaged / Repair Needed' ? 'selected' : '' }}>Damaged / Repair</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center gap-1">
                        <i data-lucide="filter" style="width:0.85rem;height:0.85rem;"></i> Filter
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Inventory Table Card --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="layers" class="text-primary" style="width:1.25rem;height:1.25rem;"></i>
                Inventory Records List
            </h5>
            <span class="badge bg-light text-dark border">Showing {{ $items->count() }} of {{ $items->total() }} items</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-semibold border-bottom">
                    <tr>
                        <th class="ps-3" style="width: 70px;">Proof</th>
                        <th style="min-width: 220px;">Item Code &amp; Name</th>
                        <th style="min-width: 130px;">Category</th>
                        <th style="min-width: 120px;">Stock Qty</th>
                        <th style="min-width: 140px;">Location &amp; Condition</th>
                        <th style="min-width: 130px;">Unit Price / Cost</th>
                        <th style="min-width: 140px;">Status &amp; Supplier</th>
                        <th class="pe-3 text-end" style="min-width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($items as $item)
                        <tr>
                            {{-- Image Proof Thumbnail --}}
                            <td class="ps-3">
                                <div class="proof-thumb-wrapper" data-bs-toggle="modal" data-bs-target="#imageProofModal" onclick="openProofModal('{{ $item->image_proof_url }}', '{{ addslashes($item->item_name) }}', '{{ $item->image_proof_type ?: 'Proof' }}')">
                                    <img src="{{ $item->image_proof_url }}" alt="Proof" class="proof-thumb-img" loading="lazy">
                                    <div class="proof-thumb-overlay" title="Click to view image proof">
                                        <i data-lucide="zoom-in" style="width:1rem;height:1rem;"></i>
                                    </div>
                                </div>
                            </td>

                            {{-- Item Code & Name --}}
                            <td>
                                <div class="fw-bold text-dark mb-0">{{ $item->item_name }}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-dark bg-opacity-10 text-dark font-monospace text-uppercase" style="font-size:0.7rem;">
                                        {{ $item->item_code }}
                                    </span>
                                    @if($item->image_proof)
                                        <span class="text-success small fw-semibold" style="font-size:0.72rem;">
                                            <i data-lucide="shield-check" style="width:0.75rem;height:0.75rem;" class="me-1"></i>Verified Proof
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Category --}}
                            <td>
                                <span class="badge badge-category {{ $item->category_badge_class }}">
                                    {{ $item->category }}
                                </span>
                            </td>

                            {{-- Stock Qty --}}
                            <td>
                                <div class="fw-bold fs-6 text-dark">
                                    {{ $item->quantity }} <span class="fs-7 text-muted fw-normal">{{ $item->unit }}</span>
                                </div>
                                @if($item->quantity <= $item->min_quantity_alert)
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle" style="font-size: 0.68rem;">
                                        Low Stock Alert (Min: {{ $item->min_quantity_alert }})
                                    </span>
                                @endif
                            </td>

                            {{-- Location & Condition --}}
                            <td>
                                <div class="small fw-semibold text-dark">{{ $item->location ?: 'Main Store Room' }}</div>
                                <div class="mt-1">
                                    <span class="badge {{ $item->condition_badge_class }}" style="font-size: 0.7rem;">
                                        {{ $item->item_condition }}
                                    </span>
                                </div>
                            </td>

                            {{-- Unit Price & Total Cost --}}
                            <td>
                                <div class="fw-bold text-success">Rs. {{ number_format($item->total_cost, 2) }}</div>
                                <div class="text-muted small">Rs. {{ number_format($item->unit_price, 2) }} / {{ $item->unit }}</div>
                            </td>

                            {{-- Status & Supplier --}}
                            <td>
                                <div class="mb-1">
                                    <span class="badge {{ $item->status_badge_class }}">
                                        {{ $item->status }}
                                    </span>
                                </div>
                                <div class="text-muted small text-truncate" style="max-width: 140px;" title="{{ $item->supplier_name }}">
                                    {{ $item->supplier_name ?: 'N/A' }}
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="pe-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-light border" title="View Details & Proof">
                                        <i data-lucide="eye" style="width:0.9rem;height:0.9rem;"></i>
                                    </a>
                                    <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-light border" title="Edit Item">
                                        <i data-lucide="pencil" style="width:0.9rem;height:0.9rem;"></i>
                                    </a>
                                    <button type="button" class="btn btn-light border text-danger" onclick="confirmDelete({{ $item->id }})" title="Delete">
                                        <i data-lucide="trash-2" style="width:0.9rem;height:0.9rem;"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $item->id }}" action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-lucide="box" style="width:3rem;height:3rem;" class="mb-3 text-secondary opacity-50"></i>
                                    <h5>No Inventory Items Found</h5>
                                    <p class="small mb-3">Try adjusting your filters or create a new inventory record with image proof.</p>
                                    <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-sm">
                                        <i data-lucide="plus" style="width:0.9rem;height:0.9rem;"></i> Add Inventory Item
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} items</span>
                    <div>{{ $items->links() }}</div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal for Image Proof Preview --}}
<div class="modal fade" id="imageProofModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title fw-bold d-flex align-items-center gap-2" id="proofModalTitle">
                    <i data-lucide="shield-check" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
                    <span>Verified Image Proof</span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-black">
                <img id="proofModalImg" src="" alt="Proof Preview" class="img-fluid" style="max-height: 520px; object-fit: contain;">
            </div>
            <div class="modal-footer bg-light py-2 px-3 justify-content-between">
                <span class="badge bg-secondary text-white" id="proofModalBadge">Invoice/Receipt</span>
                <a id="proofModalOpenNewTab" href="#" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                    <i data-lucide="external-link" style="width:0.85rem;height:0.85rem;"></i> Open Full Image
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openProofModal(imgUrl, itemName, proofType) {
        document.getElementById('proofModalTitle').innerHTML = `<i data-lucide="shield-check" class="text-success" style="width:1.2rem;height:1.2rem;"></i> Image Proof: ${itemName}`;
        document.getElementById('proofModalImg').src = imgUrl;
        document.getElementById('proofModalBadge').textContent = proofType || 'Image Proof';
        document.getElementById('proofModalOpenNewTab').href = imgUrl;
        
        const modal = new bootstrap.Modal(document.getElementById('imageProofModal'));
        modal.show();
        if (window.lucide) lucide.createIcons();
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this inventory item?')) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }
</script>
@endpush
@endsection
