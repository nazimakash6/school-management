@extends('layouts.app')

@section('title', 'Edit Payment Receipt - School Management System')

@section('content')
<div class="container-fluid px-4 py-3">
  <!-- Page Header -->
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('fee-management.index', ['tab' => 'payments']) }}" class="text-decoration-none text-muted">Fee Management</a></li>
          <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Edit Payment</li>
        </ol>
      </nav>
      <h3 class="h4 fw-bold text-dark mb-0">Edit Payment Receipt</h3>
      <p class="text-muted fs-7 mb-0">Update collected fee payment record <strong>#{{ $payment->receipt_no }}</strong></p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('fee-management.index', ['tab' => 'payments']) }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i>
        <span>Back to Payments</span>
      </a>
    </div>
  </div>

  @if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i data-lucide="alert-triangle" class="me-1" style="width:1.25rem;height:1.25rem;"></i>
      <strong class="d-block mb-1">Please fix the following errors:</strong>
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row g-4 justify-content-center">
    <!-- Left Column: Receipt Summary -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom-0">
          <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="receipt" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
            Receipt Summary
          </h6>
        </div>
        <div class="card-body pt-0">
          <div class="p-3 bg-light rounded-3 mb-3">
            <div class="text-muted fs-7 mb-1">Receipt Number</div>
            <div class="h5 fw-bold text-primary mb-0">{{ $payment->receipt_no }}</div>
          </div>

          <div class="mb-3">
            <small class="text-muted d-block fs-8">STUDENT NAME</small>
            <div class="fw-semibold text-dark">
              {{ $payment->admission ? ($payment->admission->first_name . ' ' . $payment->admission->last_name) : 'N/A' }}
            </div>
            @if($payment->admission)
              <small class="text-muted">{{ $payment->admission->admission_no }} • Class: {{ $payment->admission->class_name }}</small>
            @endif
          </div>

          <div class="mb-3">
            <small class="text-muted d-block fs-8">INVOICE NUMBER</small>
            @if($payment->feeManagement)
              <a href="{{ route('fee-management.show', $payment->fee_management_id) }}" class="fw-semibold text-decoration-none" target="_blank">
                #{{ $payment->feeManagement->invoice_no }}
              </a>
              <small class="text-muted d-block">{{ $payment->feeManagement->fee_month }} ({{ str_replace('_', ' ', $payment->feeManagement->fee_type) }})</small>
            @else
              <span class="text-muted">N/A</span>
            @endif
          </div>

          <div class="border-top pt-3">
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted fs-7">Original Invoice Net Amount:</span>
              <span class="fw-bold text-dark">
                Rs. {{ $payment->feeManagement ? number_format($payment->feeManagement->net_amount, 2) : '0.00' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Edit Form -->
    <div class="col-lg-8 col-xl-7">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="edit-3" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
            Update Payment Details
          </h5>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="{{ route('collect-payment.update', $payment->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <!-- Payment Amount -->
              <div class="col-md-6">
                <label for="amount" class="form-label fw-semibold fs-7 text-dark">
                  Payment Amount (Rs.) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted">Rs.</span>
                  <input type="number" 
                         step="0.01" 
                         min="0.01" 
                         name="amount" 
                         id="amount" 
                         class="form-control @error('amount') is-invalid @enderror" 
                         value="{{ old('amount', $payment->amount) }}" 
                         required>
                </div>
                @error('amount')
                  <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                @enderror
              </div>

              <!-- Payment Method -->
              <div class="col-md-6">
                <label for="payment_method" class="form-label fw-semibold fs-7 text-dark">
                  Payment Method <span class="text-danger">*</span>
                </label>
                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                  <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                  <option value="bank_transfer" {{ old('payment_method', $payment->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                  <option value="online" {{ old('payment_method', $payment->payment_method) == 'online' ? 'selected' : '' }}>Online Payment</option>
                  <option value="cheque" {{ old('payment_method', $payment->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                </select>
                @error('payment_method')
                  <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                @enderror
              </div>

              <!-- Payment Date -->
              <div class="col-md-6">
                <label for="payment_date" class="form-label fw-semibold fs-7 text-dark">
                  Payment Date <span class="text-danger">*</span>
                </label>
                <input type="date" 
                       name="payment_date" 
                       id="payment_date" 
                       class="form-control @error('payment_date') is-invalid @enderror" 
                       value="{{ old('payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d') : date('Y-m-d')) }}" 
                       required>
                @error('payment_date')
                  <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                @enderror
              </div>

              <!-- Note / Remarks -->
              <div class="col-12">
                <label for="note" class="form-label fw-semibold fs-7 text-dark">Notes / Remarks</label>
                <textarea name="note" 
                          id="note" 
                          rows="3" 
                          class="form-control @error('note') is-invalid @enderror" 
                          placeholder="Add any additional remarks or transaction reference IDs...">{{ old('note', $payment->note) }}</textarea>
                @error('note')
                  <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-12 pt-3 border-top d-flex align-items-center justify-content-end gap-2">
                <a href="{{ route('fee-management.index', ['tab' => 'payments']) }}" class="btn btn-light px-4">
                  Cancel
                </a>
                <button type="submit" class="btn text-white px-4 d-inline-flex align-items-center gap-2" style="background-color: #3d1a06; border-color: #3d1a06;">
                  <i data-lucide="check-circle" style="width:1rem;height:1rem;"></i>
                  <span>Save Changes</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
