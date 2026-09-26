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
          <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Collect Payment</li>
        </ol>
      </nav>
      <h3 class="h4 fw-bold text-dark mb-0">Collect Fee Payment</h3>
      <p class="text-muted fs-7 mb-0">Select a student/invoice below to record a new fee payment transaction entry.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('fee-management.index') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i>
        <span>Back to Fee List</span>
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
    <!-- Left Column: Student & Invoice Information (Dynamic Preview) -->
    <div class="col-lg-5 col-xl-4">
      <!-- Student Preview Card -->
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom-0">
          <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="user" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
            Student Details
          </h6>
        </div>
        <div class="card-body pt-0">
          <div id="studentDetailBox">
            @if($selectedInvoice && $selectedInvoice->admission)
              @php $st = $selectedInvoice->admission; @endphp
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3">
                <div class="avatar bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.25rem;">
                  {{ strtoupper(substr($st->first_name, 0, 1)) }}
                </div>
                <div>
                  <h6 class="fw-bold mb-0 text-dark">{{ $st->first_name }} {{ $st->last_name }}</h6>
                  <span class="badge bg-secondary-subtle text-secondary fs-8">Adm No: {{ $st->admission_no }}</span>
                </div>
              </div>

              <div class="row g-2 fs-7">
                <div class="col-6 text-muted">Class & Section:</div>
                <div class="col-6 fw-semibold text-dark text-end">{{ $st->class_name }} {{ $st->section_name ? ('('.$st->section_name.')') : '' }}</div>

                <div class="col-6 text-muted">Roll No:</div>
                <div class="col-6 fw-semibold text-dark text-end">{{ $st->roll_no ?? 'N/A' }}</div>

                <div class="col-6 text-muted">Father / Guardian:</div>
                <div class="col-6 fw-semibold text-dark text-end">{{ $st->father_name ?? ($st->guardian_name ?? 'N/A') }}</div>

                <div class="col-6 text-muted">Contact No:</div>
                <div class="col-6 fw-semibold text-dark text-end">{{ $st->father_phone ?? ($st->guardian_primary_mobile_no ?? 'N/A') }}</div>
              </div>
            @else
              <div class="text-muted text-center py-4 fs-7">
                <i data-lucide="user-check" class="mb-2 d-block mx-auto text-secondary opacity-50" style="width:2rem;height:2rem;"></i>
                Select a student / fee invoice to view student details.
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Invoice Summary Card -->
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom-0">
          <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="file-text" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
            Invoice Overview
          </h6>
        </div>
        <div class="card-body pt-0">
          <div id="invoiceDetailBox" class="p-3 border rounded-3 bg-white">
            @if($selectedInvoice)
              <div class="d-flex justify-content-between text-muted fs-7 mb-2">
                <span>Invoice Number:</span>
                <span class="fw-bold text-dark" id="previewInvoiceNo">{{ $selectedInvoice->invoice_no }}</span>
              </div>
              <div class="d-flex justify-content-between text-muted fs-7 mb-2">
                <span>Fee Month / Term:</span>
                <span class="fw-semibold text-dark" id="previewFeeMonth">{{ $selectedInvoice->fee_month }}</span>
              </div>
              <div class="d-flex justify-content-between text-muted fs-7 mb-2">
                <span>Fee Type:</span>
                <span class="fw-semibold text-capitalize text-dark" id="previewFeeType">{{ str_replace('_', ' ', $selectedInvoice->fee_type) }}</span>
              </div>
              <hr class="my-2">
              <div class="d-flex justify-content-between text-muted fs-7 mb-1">
                <span>Gross Total Amount:</span>
                <span class="fw-semibold text-dark" id="previewGrossAmount">Rs. {{ number_format($selectedInvoice->amount, 2) }}</span>
              </div>
              <div class="d-flex justify-content-between text-success fs-7 mb-1">
                <span>Already Paid:</span>
                <span class="fw-bold" id="previewPaidAmount">Rs. {{ number_format($selectedInvoice->paid_amount, 2) }}</span>
              </div>
              <div class="d-flex justify-content-between text-danger fs-6 fw-bold pt-2 border-top">
                <span>Current Due Balance:</span>
                <span id="previewDueBalance">Rs. {{ number_format($selectedInvoice->due_balance, 2) }}</span>
              </div>
            @else
              <div class="text-muted text-center py-4 fs-7">
                <i data-lucide="file-text" class="mb-2 d-block mx-auto text-secondary opacity-50" style="width:2rem;height:2rem;"></i>
                Select a fee invoice to preview amount breakdown.
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Collection Form -->
    <div class="col-lg-7 col-xl-8">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
              <i data-lucide="banknote" class="text-success" style="width:1.25rem;height:1.25rem;"></i>
              Collect Fee Payment Form
            </h5>
            <small class="text-muted">Fill in the details to record a payment transaction entry.</small>
          </div>
          <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-semibold">
            Receipt #: {{ $receiptNo }}
          </span>
        </div>
        <div class="card-body p-4">
          <form action="{{ route('collect-payment.store') }}" method="POST">
            @csrf

            <!-- Select Invoice -->
            <div class="mb-4">
              <label for="fee_management_id" class="form-label fw-bold text-dark">
                Select Student / Fee Invoice <span class="text-danger">*</span>
              </label>
              <select name="fee_management_id" id="fee_management_id" class="form-select form-select-lg fw-semibold" required onchange="onInvoiceChange(this.value)">
                <option value="" {{ !$selectedInvoice ? 'selected' : '' }}>-- Select Student / Fee Invoice --</option>
                <optgroup label="Unpaid & Partial Invoices">
                  @foreach($unpaidInvoices as $inv)
                    @php $stName = $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name . ' - Adm: ' . $inv->admission->admission_no . ' (' . $inv->admission->class_name . ')') : 'Student'; @endphp
                    <option value="{{ $inv->id }}" {{ ($selectedInvoice && $selectedInvoice->id == $inv->id) ? 'selected' : '' }}
                            data-invno="{{ $inv->invoice_no }}"
                            data-month="{{ $inv->fee_month }}"
                            data-type="{{ str_replace('_', ' ', $inv->fee_type) }}"
                            data-gross="{{ number_format($inv->amount, 2) }}"
                            data-paid="{{ number_format($inv->paid_amount, 2) }}"
                            data-due="{{ $inv->due_balance }}"
                            data-formatted-due="{{ number_format($inv->due_balance, 2) }}"
                            data-stname="{{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'N/A' }}"
                            data-stno="{{ $inv->admission ? $inv->admission->admission_no : 'N/A' }}"
                            data-stclass="{{ $inv->admission ? ($inv->admission->class_name . ' ' . ($inv->admission->section_name ? '('.$inv->admission->section_name.')' : '')) : 'N/A' }}"
                            data-stroll="{{ $inv->admission->roll_no ?? 'N/A' }}"
                            data-stfather="{{ $inv->admission ? ($inv->admission->father_name ?: $inv->admission->guardian_name) : 'N/A' }}"
                            data-stphone="{{ $inv->admission ? ($inv->admission->father_phone ?: $inv->admission->guardian_primary_mobile_no) : 'N/A' }}">
                      {{ $inv->invoice_no }} - {{ $stName }} | Month: {{ $inv->fee_month }} | Due: Rs. {{ number_format($inv->due_balance, 2) }}
                    </option>
                  @endforeach
                </optgroup>
                <optgroup label="All Other Invoices">
                  @foreach($allInvoices as $inv)
                    @if(!in_array($inv->id, $unpaidInvoices->pluck('id')->toArray()))
                      @php $stName = $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name . ' - Adm: ' . $inv->admission->admission_no) : 'Student'; @endphp
                      <option value="{{ $inv->id }}" {{ ($selectedInvoice && $selectedInvoice->id == $inv->id) ? 'selected' : '' }}
                              data-invno="{{ $inv->invoice_no }}"
                              data-month="{{ $inv->fee_month }}"
                              data-type="{{ str_replace('_', ' ', $inv->fee_type) }}"
                              data-gross="{{ number_format($inv->amount, 2) }}"
                              data-paid="{{ number_format($inv->paid_amount, 2) }}"
                              data-due="{{ $inv->due_balance }}"
                              data-formatted-due="{{ number_format($inv->due_balance, 2) }}"
                              data-stname="{{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'N/A' }}"
                              data-stno="{{ $inv->admission ? $inv->admission->admission_no : 'N/A' }}"
                              data-stclass="{{ $inv->admission ? ($inv->admission->class_name . ' ' . ($inv->admission->section_name ? '('.$inv->admission->section_name.')' : '')) : 'N/A' }}"
                              data-stroll="{{ $inv->admission->roll_no ?? 'N/A' }}"
                              data-stfather="{{ $inv->admission ? ($inv->admission->father_name ?: $inv->admission->guardian_name) : 'N/A' }}"
                              data-stphone="{{ $inv->admission ? ($inv->admission->father_phone ?: $inv->admission->guardian_primary_mobile_no) : 'N/A' }}">
                        {{ $inv->invoice_no }} - {{ $stName }} ({{ strtoupper($inv->status) }})
                      </option>
                    @endif
                  @endforeach
                </optgroup>
              </select>
            </div>

            <!-- Payment Amount -->
            <div class="mb-4">
              <label for="payment_amount" class="form-label fw-bold text-dark">
                Payment Amount (PKR) <span class="text-danger">*</span>
              </label>
              <div class="input-group input-group-lg">
                <span class="input-group-text bg-light text-muted fw-bold">Rs.</span>
                <input type="number" step="0.01" min="0.01" name="payment_amount" id="payment_amount" class="form-control fw-bold text-success fs-4" placeholder="0.00" value="{{ old('payment_amount', $selectedInvoice ? ($selectedInvoice->due_balance > 0 ? $selectedInvoice->due_balance : $selectedInvoice->net_amount) : '') }}" required>
              </div>
              <small class="text-muted mt-1 d-block">Enter the exact amount collected from the parent/student.</small>
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
              <a href="{{ route('fee-management.index') }}" class="btn btn-light px-4">Cancel</a>
              <button type="submit" class="btn btn-success btn-lg px-5 fw-bold d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none;">
                <i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;"></i>
                <span>Collect & Save Payment</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function onInvoiceChange(invId) {
  var select = document.getElementById('fee_management_id');
  var opt = select.options[select.selectedIndex];

  if (!opt || !opt.value) {
    document.getElementById('payment_amount').value = '';
    document.getElementById('studentDetailBox').innerHTML = `
      <div class="text-muted text-center py-4 fs-7">
        <i data-lucide="user-check" class="mb-2 d-block mx-auto text-secondary opacity-50" style="width:2rem;height:2rem;"></i>
        Select a student / fee invoice to view student details.
      </div>
    `;
    document.getElementById('invoiceDetailBox').innerHTML = `
      <div class="text-muted text-center py-4 fs-7">
        <i data-lucide="file-text" class="mb-2 d-block mx-auto text-secondary opacity-50" style="width:2rem;height:2rem;"></i>
        Select a fee invoice to view amount breakdown.
      </div>
    `;
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }
    return;
  }

  var due = opt.getAttribute('data-due');
  var formattedDue = opt.getAttribute('data-formatted-due');
  var invno = opt.getAttribute('data-invno');
  var month = opt.getAttribute('data-month');
  var type = opt.getAttribute('data-type');
  var gross = opt.getAttribute('data-gross');
  var paid = opt.getAttribute('data-paid');
  var stname = opt.getAttribute('data-stname');
  var stno = opt.getAttribute('data-stno');
  var stclass = opt.getAttribute('data-stclass');
  var stroll = opt.getAttribute('data-stroll');
  var stfather = opt.getAttribute('data-stfather');
  var stphone = opt.getAttribute('data-stphone');

  // Update payment amount field
  if (due && parseFloat(due) > 0) {
    document.getElementById('payment_amount').value = due;
  }

  // Update Student Preview Box
  document.getElementById('studentDetailBox').innerHTML = `
    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3">
      <div class="avatar bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.25rem;">
        ${stname ? stname.charAt(0).toUpperCase() : 'S'}
      </div>
      <div>
        <h6 class="fw-bold mb-0 text-dark">${stname}</h6>
        <span class="badge bg-secondary-subtle text-secondary fs-8">Adm No: ${stno}</span>
      </div>
    </div>
    <div class="row g-2 fs-7">
      <div class="col-6 text-muted">Class & Section:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stclass}</div>
      <div class="col-6 text-muted">Roll No:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stroll}</div>
      <div class="col-6 text-muted">Father / Guardian:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stfather}</div>
      <div class="col-6 text-muted">Contact No:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stphone}</div>
    </div>
  `;

  // Update Invoice Overview Box
  document.getElementById('invoiceDetailBox').innerHTML = `
    <div class="d-flex justify-content-between text-muted fs-7 mb-2">
      <span>Invoice Number:</span>
      <span class="fw-bold text-dark">${invno}</span>
    </div>
    <div class="d-flex justify-content-between text-muted fs-7 mb-2">
      <span>Fee Month / Term:</span>
      <span class="fw-semibold text-dark">${month}</span>
    </div>
    <div class="d-flex justify-content-between text-muted fs-7 mb-2">
      <span>Fee Type:</span>
      <span class="fw-semibold text-capitalize text-dark">${type}</span>
    </div>
    <hr class="my-2">
    <div class="d-flex justify-content-between text-muted fs-7 mb-1">
      <span>Gross Total Amount:</span>
      <span class="fw-semibold text-dark">Rs. ${gross}</span>
    </div>
    <div class="d-flex justify-content-between text-success fs-7 mb-1">
      <span>Already Paid:</span>
      <span class="fw-bold">Rs. ${paid}</span>
    </div>
    <div class="d-flex justify-content-between text-danger fs-6 fw-bold pt-2 border-top">
      <span>Current Due Balance:</span>
      <span>Rs. ${formattedDue}</span>
    </div>
  `;

  if (typeof lucide !== 'undefined') { lucide.createIcons(); }
}
</script>
@endpush
@endsection
