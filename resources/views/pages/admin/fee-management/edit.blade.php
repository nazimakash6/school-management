@extends('layouts.app')

@section('title', 'Edit Fee Invoice - ' . $invoice->invoice_no)

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Edit Fee Invoice</h1>
      <p class="page-subtitle">Update details for invoice {{ $invoice->invoice_no }}</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('fee-management.index') }}" class="btn btn-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to List
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3">
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm col-lg-9 mx-auto">
    <div class="card-body p-4">
      <form action="{{ route('fee-management.update', $invoice->id) }}" method="POST" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-4">
          <label for="academic_session_id" class="form-label fw-semibold">Academic Session <span class="text-danger">*</span></label>
          <select name="academic_session_id" id="academic_session_id" class="form-select @error('academic_session_id') is-invalid @enderror" required>
            <option value="">-- Select Academic Session --</option>
            @foreach($academicSessions as $session)
              <option value="{{ $session->id }}" @selected(old('academic_session_id', $invoice->academic_session_id ?: ($invoice->admission ? $invoice->admission->academic_session_id : '')) == $session->id)>
                {{ $session->session_name }}
              </option>
            @endforeach
          </select>
          @error('academic_session_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-5">
          <label for="admission_id" class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
          <select name="admission_id" id="admission_id" class="form-select @error('admission_id') is-invalid @enderror" required>
            @foreach($students as $st)
              <option value="{{ $st->id }}" 
                      data-class="{{ $st->class_name }}"
                      data-section="{{ $st->section_name }}"
                      data-father="{{ $st->father_name ?: $st->guardian_name }}"
                      data-no="{{ $st->admission_no }}"
                      data-session="{{ $st->academic_session_id }}"
                      @selected(old('admission_id', $invoice->admission_id) == $st->id)>
                {{ $st->first_name }} {{ $st->last_name }} (ID: {{ $st->admission_no }} | Class: {{ $st->class_name }})
              </option>
            @endforeach
          </select>
          @error('admission_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-3">
          <label for="fee_month" class="form-label fw-semibold">Fee Month <span class="text-danger">*</span></label>
          <input type="month" name="fee_month" id="fee_month" class="form-control @error('fee_month') is-invalid @enderror" value="{{ old('fee_month', $invoice->fee_month) }}" required>
          @error('fee_month')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label for="fee_type" class="form-label fw-semibold">Fee Type <span class="text-danger">*</span></label>
          <select name="fee_type" id="fee_type" class="form-select @error('fee_type') is-invalid @enderror" required>
            <option value="school_fee" @selected(old('fee_type', $invoice->fee_type) === 'school_fee')>School Fee</option>
            <option value="tuition" @selected(old('fee_type', $invoice->fee_type) === 'tuition')>Tuition Fee</option>
            <option value="admission" @selected(old('fee_type', $invoice->fee_type) === 'admission')>Admission Fee</option>
            <option value="examination" @selected(old('fee_type', $invoice->fee_type) === 'examination')>Examination Fee</option>
            <option value="transport" @selected(old('fee_type', $invoice->fee_type) === 'transport')>Transport Fee</option>
            <option value="hostel" @selected(old('fee_type', $invoice->fee_type) === 'hostel')>Hostel Fee</option>
            <option value="miscellaneous" @selected(old('fee_type', $invoice->fee_type) === 'miscellaneous')>Miscellaneous</option>
          </select>
          @error('fee_type')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label for="due_date" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
          <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" required>
          @error('due_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="amount" class="form-label fw-semibold">Total Fee Amount (PKR) <span class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $invoice->amount) }}" required>
          @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="discount" class="form-label fw-semibold">Discount Amount (PKR)</label>
          <input type="number" step="0.01" min="0" name="discount" id="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', $invoice->discount) }}">
          @error('discount')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="paid_amount" class="form-label fw-semibold">Paid Amount Received (PKR)</label>
          <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', $invoice->paid_amount) }}">
          @error('paid_amount')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
          <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
            <option value="cash" @selected(old('payment_method', $invoice->payment_method) === 'cash')>Cash</option>
            <option value="bank_transfer" @selected(old('payment_method', $invoice->payment_method) === 'bank_transfer')>Bank Transfer</option>
            <option value="online" @selected(old('payment_method', $invoice->payment_method) === 'online')>Online Payment</option>
            <option value="cheque" @selected(old('payment_method', $invoice->payment_method) === 'cheque')>Cheque</option>
          </select>
          @error('payment_method')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="payment_date" class="form-label fw-semibold">Payment Date</label>
          <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $invoice->payment_date ? $invoice->payment_date->format('Y-m-d') : '') }}">
          @error('payment_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
          <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="unpaid" @selected(old('status', $invoice->status) === 'unpaid')>Unpaid</option>
            <option value="partial" @selected(old('status', $invoice->status) === 'partial')>Partial</option>
            <option value="paid" @selected(old('status', $invoice->status) === 'paid')>Paid</option>
            <option value="cancelled" @selected(old('status', $invoice->status) === 'cancelled')>Cancelled</option>
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-12">
          <label for="notes" class="form-label fw-semibold">Remarks / Notes</label>
          <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $invoice->notes) }}</textarea>
          @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 text-end mt-4">
          <a href="{{ route('fee-management.index') }}" class="btn btn-secondary me-2">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Update Fee Invoice
          </button>
        </div>
      </form>
    </div>
  </div>

@push('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <style>
    .select2-container--bootstrap-5 .select2-selection {
      border-color: #dee2e6;
      padding: 0.375rem 0.75rem;
      font-size: 0.9rem;
      border-radius: 0.375rem;
      min-height: 38px;
    }
  </style>
@endpush

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#admission_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Search & Select Student --',
        allowClear: true,
        width: '100%'
      });
    });
  </script>
@endpush
@endsection
