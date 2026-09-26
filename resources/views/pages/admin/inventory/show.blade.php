@extends('layouts.app')

@section('title', 'Inventory Details - ' . $item->item_name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="package" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Inventory Asset Details
            </h1>
            <p class="text-muted mb-0">SKU: <span class="fw-bold font-monospace text-dark">{{ $item->item_code }}</span> · {{ $item->item_name }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Back to List</span>
            </a>
            <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                <i data-lucide="pencil" style="width: 1rem; height: 1rem;"></i>
                <span>Edit Item</span>
            </a>
            <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this inventory item?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-1">
                    <i data-lucide="trash-2" style="width: 1rem; height: 1rem;"></i>
                    <span>Delete</span>
                </button>
            </form>
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

    <div class="row g-4">
        {{-- Left Column: Image Proof Viewer --}}
        <div class="col-lg-5">
            @if($item->image_proof && $item->image_proof_url)
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="shield-check" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
                            Verified Image Proof
                        </h6>
                        <span class="badge bg-secondary text-white">{{ $item->image_proof_type ?: 'Receipt/Proof' }}</span>
                    </div>

                    <div class="card-body p-3 text-center bg-light d-flex flex-column align-items-center justify-content-center">
                        <div class="proof-large-container w-100 mb-3" style="max-height: 380px; overflow: hidden; position: relative;">
                            <img src="{{ $item->image_proof_url }}" alt="Verified Image Proof" class="img-fluid w-100" style="object-fit: cover; max-height: 360px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#imageProofModal" onclick="openProofModal('{{ $item->image_proof_url }}', '{{ addslashes($item->item_name) }}', '{{ $item->image_proof_type ?: 'Proof' }}')">
                        </div>

                        <div class="d-flex gap-2 w-100 justify-content-center">
                            <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#imageProofModal" onclick="openProofModal('{{ $item->image_proof_url }}', '{{ addslashes($item->item_name) }}', '{{ $item->image_proof_type ?: 'Proof' }}')">
                                <i data-lucide="zoom-in" style="width:0.9rem;height:0.9rem;"></i> Zoom / Expand Image Proof
                            </button>
                            <a href="{{ $item->image_proof_url }}" target="_blank" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                                <i data-lucide="external-link" style="width:0.9rem;height:0.9rem;"></i> Open Full Image
                            </a>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top py-3 text-muted small">
                        <div class="d-flex align-items-center gap-2 text-success fw-semibold">
                            <i data-lucide="check-circle" style="width:1rem;height:1rem;"></i>
                            <span>Proof document is attached &amp; verified for audit compliance</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="image-off" class="text-secondary" style="width:1.2rem;height:1.2rem;"></i>
                            Image Proof
                        </h6>
                        <span class="badge bg-light text-muted border">No Proof Attached</span>
                    </div>

                    <div class="card-body p-4 text-center bg-light d-flex flex-column align-items-center justify-content-center min-vh-25 py-5">
                        <div class="p-3 bg-white rounded-circle shadow-sm mb-3">
                            <i data-lucide="image-off" class="text-muted" style="width:2.5rem;height:2.5rem;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">No Image Proof Uploaded</h6>
                        <p class="text-muted small mb-3" style="max-width:280px;">No invoice receipt or physical photo was attached for this inventory item.</p>
                        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                            <i data-lucide="upload-cloud" style="width:0.9rem;height:0.9rem;"></i> Upload Image Proof
                        </a>
                    </div>

                    <div class="card-footer bg-white border-top py-3 text-muted small">
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i data-lucide="info" style="width:1rem;height:1rem;"></i>
                            <span>You can upload an image proof anytime by editing this item.</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: Item Information Breakdown --}}
        <div class="col-lg-7">
            {{-- General Overview Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="info" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Item Specifications &amp; Valuation
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="text-muted small fw-semibold text-uppercase d-block">Item Name</label>
                            <h4 class="fw-bold text-dark mb-1">{{ $item->item_name }}</h4>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                @foreach($item->categories_list as $cat)
                                    <span class="badge badge-category {{ \App\Models\Inventory::getCategoryBadgeClassForName($cat) }}">
                                        {{ $cat }}
                                    </span>
                                @endforeach
                                <span class="badge {{ $item->status_badge_class }}">
                                    {{ $item->status }}
                                </span>
                                <span class="badge {{ $item->condition_badge_class }}">
                                    Condition: {{ $item->item_condition }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <label class="text-muted small fw-semibold text-uppercase d-block">Total Asset Cost</label>
                            <h2 class="fw-bold text-success mb-0">Rs. {{ number_format($item->total_cost, 2) }}</h2>
                            <div class="text-muted small">Rs. {{ number_format($item->unit_price, 2) }} per {{ $item->unit }}</div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Current Stock Qty</span>
                            <div class="fw-bold fs-5 text-dark mt-1">{{ $item->quantity }} {{ $item->unit }}</div>
                        </div>

                        <div class="col-6 col-md-3">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Low Stock Alert Min</span>
                            <div class="fw-semibold text-dark mt-1">{{ $item->min_quantity_alert }} {{ $item->unit }}</div>
                        </div>

                        <div class="col-6 col-md-3">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Storage Location</span>
                            <div class="fw-semibold text-dark mt-1">{{ $item->location ?: 'Main Store Room' }}</div>
                        </div>

                        <div class="col-6 col-md-3">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Purchase Date</span>
                            <div class="fw-semibold text-dark mt-1">{{ $item->purchase_date ? $item->purchase_date->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Supplier & Procurement Details Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="truck" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                        Supplier &amp; Payment Audit Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Supplier Name</span>
                            <div class="fw-semibold text-dark mt-1">{{ $item->supplier_name ?: 'N/A' }}</div>
                        </div>

                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Supplier Contact</span>
                            <div class="fw-semibold text-dark mt-1">{{ $item->supplier_contact ?: 'N/A' }}</div>
                        </div>

                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Invoice / Receipt #</span>
                            <div class="fw-semibold text-dark font-monospace mt-1">{{ $item->invoice_no ?: 'N/A' }}</div>
                        </div>

                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Payment Status</span>
                            <div class="mt-1">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">
                                    {{ $item->payment_status ?: 'Paid' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Payment Method</span>
                            <div class="fw-semibold text-dark mt-1">{{ $item->payment_method ?: 'Bank Transfer' }}</div>
                        </div>

                        <div class="col-md-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block">Created By</span>
                            <div class="fw-semibold text-dark mt-1">{{ $item->creator->name ?? 'Admin' }}</div>
                        </div>
                    </div>

                    @if($item->notes)
                        <div class="mt-4">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Specifications &amp; Remarks</span>
                            <div class="p-3 bg-light rounded-3 text-dark border">
                                {{ $item->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image Proof Modal --}}
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
</script>
@endpush
@endsection
