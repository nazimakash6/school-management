@extends('layouts.app')

@section('title', 'Add New Inventory Item')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Content Header --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i data-lucide="plus-circle" class="text-primary" style="width: 1.75rem; height: 1.75rem;"></i>
                Add New Inventory Item
            </h1>
            <p class="text-muted mb-0">Record equipment, furniture, or stationery with verified image proof</p>
        </div>
        <div>
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i data-lucide="alert-circle" style="width:1.25rem;height:1.25rem;"></i>
                <strong class="fw-bold">Please fix the following validation errors:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            {{-- Main Form Column --}}
            <div class="col-lg-8">
                {{-- Section 1: Item Basic Info --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="box" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            1. Item Specifications &amp; Stock
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-dark small">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="item_name" class="form-control" placeholder="e.g. Dell OptiPlex Desktop Workstation" value="{{ old('item_name') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Item Code / SKU</label>
                                <input type="text" name="item_code" class="form-control" placeholder="Auto-generated if empty" value="{{ old('item_code') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark small">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantityInput" class="form-control" min="0" value="{{ old('quantity', 1) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark small">Unit Type <span class="text-danger">*</span></label>
                                <select name="unit" class="form-select" required>
                                    <option value="Pcs" {{ old('unit') === 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                    <option value="Units" {{ old('unit') === 'Units' ? 'selected' : '' }}>Units</option>
                                    <option value="Sets" {{ old('unit') === 'Sets' ? 'selected' : '' }}>Sets</option>
                                    <option value="Boxes" {{ old('unit') === 'Boxes' ? 'selected' : '' }}>Boxes</option>
                                    <option value="Reams" {{ old('unit') === 'Reams' ? 'selected' : '' }}>Reams</option>
                                    <option value="Kg" {{ old('unit') === 'Kg' ? 'selected' : '' }}>Kg</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-dark small">Low Stock Alert</label>
                                <input type="number" name="min_quantity_alert" class="form-control" min="0" value="{{ old('min_quantity_alert', 5) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Pricing & Location --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="banknote" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            2. Financials, Location &amp; Condition
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Unit Price (PKR) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="unit_price" id="unitPriceInput" class="form-control" min="0" value="{{ old('unit_price', '0.00') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Calculated Total Cost (PKR)</label>
                                <input type="text" id="totalCostDisplay" class="form-control bg-light fw-bold text-success" value="Rs. 0.00" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Storage Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. IT Lab 1, Store Room B" value="{{ old('location') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Physical Condition <span class="text-danger">*</span></label>
                                <select name="item_condition" class="form-select" required>
                                    <option value="New / Excellent" {{ old('item_condition') === 'New / Excellent' ? 'selected' : '' }}>New / Excellent</option>
                                    <option value="Good" {{ old('item_condition', 'Good') === 'Good' ? 'selected' : '' }}>Good</option>
                                    <option value="Fair" {{ old('item_condition') === 'Fair' ? 'selected' : '' }}>Fair</option>
                                    <option value="Damaged / Repair Needed" {{ old('item_condition') === 'Damaged / Repair Needed' ? 'selected' : '' }}>Damaged / Repair Needed</option>
                                    <option value="Disposed" {{ old('item_condition') === 'Disposed' ? 'selected' : '' }}>Disposed</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="Available" {{ old('status', 'Available') === 'Available' ? 'selected' : '' }}>Available in Store</option>
                                    <option value="In Use" {{ old('status') === 'In Use' ? 'selected' : '' }}>In Use</option>
                                    <option value="Low Stock" {{ old('status') === 'Low Stock' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="Out of Stock" {{ old('status') === 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
                                    <option value="Maintenance" {{ old('status') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Supplier & Purchase Info --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="truck" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            3. Supplier &amp; Invoice Details
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Supplier Name</label>
                                <input type="text" name="supplier_name" class="form-control" placeholder="e.g. TechCorp Solutions" value="{{ old('supplier_name') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Supplier Contact Phone</label>
                                <input type="text" name="supplier_contact" class="form-control" placeholder="e.g. +1 (555) 234-5678" value="{{ old('supplier_contact') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Invoice / Receipt No.</label>
                                <input type="text" name="invoice_no" class="form-control" placeholder="e.g. INV-2026-8841" value="{{ old('invoice_no') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Payment Status</label>
                                <select name="payment_status" class="form-select">
                                    <option value="Paid" {{ old('payment_status', 'Paid') === 'Paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="Pending" {{ old('payment_status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Partial" {{ old('payment_status') === 'Partial' ? 'selected' : '' }}>Partial</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <option value="Bank Transfer" {{ old('payment_method', 'Bank Transfer') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Cheque" {{ old('payment_method') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="Credit Card" {{ old('payment_method') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column: Image Proof Upload & Notes --}}
            <div class="col-lg-4">
                {{-- Image Proof Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="shield-check" class="text-success" style="width:1.1rem;height:1.1rem;"></i>
                            4. Image Proof Upload
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Proof Document Type</label>
                            <select name="image_proof_type" class="form-select">
                                <option value="Invoice/Receipt">Invoice / Cash Receipt</option>
                                <option value="Physical Item Photo">Physical Item Inspection Photo</option>
                                <option value="Delivery Proof">Delivery Note &amp; Proof</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Upload Proof Image (JPG, PNG, WEBP)</label>
                            <div class="dropzone-upload-box" onclick="document.getElementById('imageProofInput').click()">
                                <i data-lucide="upload-cloud" class="text-primary mb-2" style="width:2.5rem;height:2.5rem;"></i>
                                <div class="fw-semibold text-dark small mb-1">Click to browse or drag file</div>
                                <div class="text-muted" style="font-size:0.75rem;">Supports JPEG, PNG, WEBP (Max 5MB)</div>
                                <input type="file" name="image_proof" id="imageProofInput" class="d-none" accept="image/*" onchange="previewImageProof(this)">
                            </div>
                        </div>

                        {{-- Preview Box --}}
                        <div id="imagePreviewContainer" class="proof-large-container p-2 text-center" style="display: none;">
                            <img id="imagePreviewImg" src="" alt="Proof Preview" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                            <div class="mt-2 d-flex justify-content-between align-items-center px-2">
                                <span class="badge bg-success text-white small" id="previewFileName">Proof Attached</span>
                                <button type="button" class="btn btn-link text-danger btn-sm p-0" onclick="removeImageProof()">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="file-text" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
                            5. Remarks &amp; Notes
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <textarea name="notes" class="form-control" rows="4" placeholder="Enter warranty details, serial numbers, specifications, or maintenance guidelines...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="card-footer bg-light border-top p-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i data-lucide="save" style="width:1rem;height:1rem;"></i>
                            <span>Save Inventory Item</span>
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('quantityInput');
    const priceInput = document.getElementById('unitPriceInput');
    const totalDisplay = document.getElementById('totalCostDisplay');

    function updateTotal() {
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = qty * price;
        totalDisplay.value = 'Rs. ' + total.toFixed(2);
    }

    qtyInput.addEventListener('input', updateTotal);
    priceInput.addEventListener('input', updateTotal);
    updateTotal();
});

function previewImageProof(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreviewImg').src = e.target.result;
            document.getElementById('previewFileName').textContent = input.files[0].name;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImageProof() {
    document.getElementById('imageProofInput').value = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
}
</script>
@endpush
@endsection
