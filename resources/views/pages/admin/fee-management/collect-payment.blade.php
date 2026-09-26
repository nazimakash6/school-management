@extends('layouts.app')

@section('title', 'Collect Fee Payment - School Management System')

@section('content')
<div class="container-fluid px-4 py-3">
  <!-- Page Header -->
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('fee-management.index') }}" class="text-decoration-none text-muted">Fee Management</a></li>
          <li class="breadcrumb-item"><a href="{{ route('fee-management.show', $invoice->id) }}" class="text-decoration-none text-muted">{{ $invoice->invoice_no }}</a></li>
          <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Collect Payment</li>
        </ol>
      </nav>
      <h3 class="h4 fw-bold text-dark mb-0">Collect Fee Payment</h3>
      <p class="text-muted fs-7 mb-0">Record a new payment transaction entry against Invoice <strong>#{{ $invoice->invoice_no }}</strong></p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('fee-management.show', $invoice->id) }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i>
        <span>Back to Invoice</span>
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i data-lucide="check-circle" class="me-1" style="width:1.25rem;height:1.25rem;"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

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

  <div class="row g-4">
    <!-- Left Column: Student & Invoice Information -->
    <div class="col-lg-5 col-xl-4">
      <!-- Student Card -->
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom-0">
          <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="user" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
            Student Details
          </h6>
        </div>
        <div class="card-body pt-0">
          @if($student)
            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3">
              <div class="avatar bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.25rem;">
                {{ strtoupper(substr($student->first_name, 0, 1)) }}
              </div>
              <div>
                <h6 class="fw-bold mb-0 text-dark">{{ $student->first_name }} {{ $student->last_name }}</h6>
                <span class="badge bg-secondary-subtle text-secondary fs-8">Adm No: {{ $student->admission_no }}</span>
              </div>
            </div>

            <div class="row g-2 fs-7">
              <div class="col-6 text-muted">Class & Section:</div>
              <div class="col-6 fw-semibold text-dark text-end">{{ $student->class_name }} {{ $student->section_name ? ('('.$student->section_name.')') : '' }}</div>

              <div class="col-6 text-muted">Roll No:</div>
              <div class="col-6 fw-semibold text-dark text-end">{{ $student->roll_no ?? 'N/A' }}</div>

              <div class="col-6 text-muted">Fee Plan:</div>
              <div class="col-6 fw-semibold text-dark text-end text-capitalize">{{ str_replace('_', ' ', $student->fee_plan ?? 'monthly') }}</div>

              <div class="col-6 text-muted">Father / Guardian:</div>
              <div class="col-6 fw-semibold text-dark text-end">{{ $student->father_name ?? ($student->guardian_name ?? 'N/A') }}</div>

              <div class="col-6 text-muted">Contact No:</div>
              <div class="col-6 fw-semibold text-dark text-end">{{ $student->father_phone ?? ($student->guardian_primary_mobile_no ?? 'N/A') }}</div>
            </div>
          @else
            <div class="text-muted text-center py-3 fs-7">No student information attached.</div>
          @endif
        </div>
      </div>

      <!-- Current Invoice Summary Card -->
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom-0">
          <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="file-text" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
            Invoice Overview
          </h6>
        </div>
        <div class="card-body pt-0">
          <div class="p-3 border rounded-3 bg-white">
            <div class="d-flex justify-content-between text-muted fs-7 mb-2">
              <span>Invoice Number:</span>
              <span class="fw-bold text-dark">{{ $invoice->invoice_no }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted fs-7 mb-2">
              <span>Fee Type:</span>
              <span class="fw-semibold text-capitalize text-dark">{{ str_replace('_', ' ', $invoice->fee_type) }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted fs-7 mb-2">
              <span>Fee Month / Term:</span>
              <span class="fw-semibold text-dark">{{ $invoice->fee_month }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted fs-7 mb-2">
              <span>Due Date:</span>
              <span class="fw-semibold text-dark">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted fs-7 mb-3">
              <span>Status:</span>
              <span class="badge {{ $invoice->status_badge_class }} text-capitalize px-2 py-1">{{ $invoice->status }}</span>
            </div>

            <hr class="my-2">

            <div class="d-flex justify-content-between text-muted fs-7 mb-1">
              <span>Gross Total Amount:</span>
              <span class="fw-semibold text-dark">Rs. {{ number_format($invoice->amount, 2) }}</span>
            </div>
            @if($invoice->discount > 0)
              <div class="d-flex justify-content-between text-success fs-7 mb-1">
                <span>Scholarship / Discount:</span>
                <span class="fw-semibold">- Rs. {{ number_format($invoice->discount, 2) }}</span>
              </div>
            @endif
            <div class="d-flex justify-content-between fw-bold text-dark fs-7 mb-2 pt-1 border-top">
              <span>Net Amount:</span>
              <span>Rs. {{ number_format($invoice->net_amount, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between text-success fs-7 mb-1">
              <span>Already Paid:</span>
              <span class="fw-bold">Rs. {{ number_format($invoice->paid_amount, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between text-danger fs-6 fw-bold pt-2 border-top">
              <span>Current Due Balance:</span>
              <span>Rs. {{ number_format($invoice->due_balance, 2) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Other Pending Dues Summary Alert -->
      @if($totalOtherDues > 0)
        <div class="card border-warning border-start border-4 shadow-sm rounded-3">
          <div class="card-body p-3">
            <div class="d-flex align-items-start gap-2">
              <i data-lucide="alert-circle" class="text-warning flex-shrink-0 mt-1" style="width:1.2rem;height:1.2rem;"></i>
              <div>
                <h6 class="fw-bold text-dark mb-1">Other Pending Dues Found</h6>
                <p class="fs-7 text-muted mb-2">This student (or linked sibling) has <strong>{{ $otherUnpaidInvoices->count() }}</strong> other pending invoice(s) totaling <strong class="text-danger">Rs. {{ number_format($totalOtherDues, 2) }}</strong>.</p>
                <div class="list-group list-group-flush fs-7 border rounded bg-light">
                  @foreach($otherUnpaidInvoices as $otherInv)
                    <a href="{{ route('fee-management.collect-payment', $otherInv->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3">
                      <div>
                        <span class="fw-semibold text-dark">{{ $otherInv->invoice_no }}</span>
                        <small class="text-muted d-block">{{ $otherInv->fee_month }} ({{ str_replace('_', ' ', $otherInv->fee_type) }})</small>
                      </div>
                      <span class="badge bg-danger">Rs. {{ number_format($otherInv->due_balance, 2) }}</span>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>

    <!-- Right Column: Dedicated Payment Collection Form -->
    <div class="col-lg-7 col-xl-8">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
              <i data-lucide="banknote" class="text-success" style="width:1.25rem;height:1.25rem;"></i>
              New Payment Collection Form
            </h5>
            <small class="text-muted">This transaction will be logged as a separate receipt entry in the database.</small>
          </div>
          <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-semibold">
            Receipt #: {{ $receiptNo }}
          </span>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('fee-management.store-payment', $invoice->id) }}" method="POST">
            @csrf

            <!-- Payment Amount -->
            <div class="mb-4">
              <label for="payment_amount" class="form-label fw-bold text-dark">
                Payment Amount (PKR) <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-lg">
                <span class="input-group-text bg-light text-muted fw-bold">Rs.</span>
                <input type="number" step="0.01" min="0.01" name="payment_amount" id="payment_amount" class="form-control fw-bold text-success fs-4" value="{{ old('payment_amount', $invoice->due_balance > 0 ? $invoice->due_balance : $invoice->net_amount) }}" required>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-1">
                <small class="text-muted">Enter the exact amount collected from the parent/student.</small>
                <div class="btn-group btn-group-sm">
                  @if($invoice->due_balance > 0)
                    <button type="button" class="btn btn-outline-secondary btn-xs" onclick="document.getElementById('payment_amount').value = {{ $invoice->due_balance }}">Full Due (Rs. {{ number_format($invoice->due_balance, 2) }})</button>
                    <button type="button" class="btn btn-outline-secondary btn-xs" onclick="document.getElementById('payment_amount').value = {{ round($invoice->due_balance / 2, 2) }}">Half (Rs. {{ number_format(round($invoice->due_balance / 2, 2), 2) }})</button>
                  @endif
                </div>
              </div>
            </div>

            <div class="row g-3 mb-4">
              <!-- Payment Date -->
              <div class="col-md-6">
                <label for="payment_date" class="form-label fw-semibold text-dark">
                  Payment Date <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted"><i data-lucide="calendar" style="width:1rem;height:1rem;"></i></span>
                  <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                </div>
              </div>

              <!-- Payment Method -->
              <div class="col-md-6">
                <label for="payment_method" class="form-label fw-semibold text-dark">
                  Payment Method <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted"><i data-lucide="credit-card" style="width:1rem;height:1rem;"></i></span>
                  <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                    <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Notes / Remarks -->
            <div class="mb-4">
              <label for="note" class="form-label fw-semibold text-dark">
                Transaction Note / Remarks <small class="text-muted fw-normal">(Optional)</small>
              </label>
              <textarea name="note" id="note" class="form-control" rows="3" placeholder="Add transaction reference, cheque no, or payment remarks here...">{{ old('note') }}</textarea>
            </div>

            <!-- Submit Button Bar -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
              <a href="{{ route('fee-management.show', $invoice->id) }}" class="btn btn-light px-4">Cancel</a>
              <button type="submit" class="btn btn-success btn-lg px-5 fw-bold d-inline-flex align-items-center gap-2">
                <i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;"></i>
                <span>Save & Record Payment</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
