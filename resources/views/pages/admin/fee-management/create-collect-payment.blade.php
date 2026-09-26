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
      <p class="text-muted fs-7 mb-0">Select a student below to view pending fee invoices, collect full or partial lump-sum payments.</p>
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

  @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i data-lucide="alert-triangle" class="me-1" style="width:1.25rem;height:1.25rem;"></i>
      {{ session('warning') }}
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

  @php
    // Group all pending/unpaid/partial invoices by admission_id
    $pendingInvoicesByStudent = [];
    foreach($unpaidInvoices as $inv) {
        if (!$inv->admission_id) continue;
        $admId = $inv->admission_id;
        if (!isset($pendingInvoicesByStudent[$admId])) {
            $st = $inv->admission;
            $pendingInvoicesByStudent[$admId] = [
                'student_id'   => $admId,
                'student_name' => $st ? ($st->first_name . ' ' . $st->last_name) : 'N/A',
                'admission_no' => $st ? $st->admission_no : 'N/A',
                'class_name'   => $st ? ($st->class_name . ' ' . ($st->section_name ? '('.$st->section_name.')' : '')) : 'N/A',
                'roll_no'      => $st ? ($st->roll_no ?? 'N/A') : 'N/A',
                'father_name'  => $st ? ($st->father_name ?: $st->guardian_name) : 'N/A',
                'phone'        => $st ? ($st->father_phone ?: $st->guardian_primary_mobile_no) : 'N/A',
                'invoices'     => [],
            ];
        }
        $pendingInvoicesByStudent[$admId]['invoices'][] = [
            'id'            => $inv->id,
            'invoice_no'    => $inv->invoice_no,
            'fee_month'     => $inv->fee_month,
            'fee_type'      => str_replace('_', ' ', $inv->fee_type),
            'net_amount'    => (float) $inv->net_amount,
            'paid_amount'   => (float) $inv->paid_amount,
            'due_balance'   => (float) $inv->due_balance,
            'due_date'      => $inv->due_date ? $inv->due_date->format('M d, Y') : 'N/A',
            'status'        => $inv->status,
        ];
    }
  @endphp

  <form action="{{ route('collect-payment.store') }}" method="POST" id="collectPaymentForm">
    @csrf

    <div class="row g-4">
      <!-- Left Column: Student Selection & Overview -->
      <div class="col-lg-5 col-xl-4">
        <!-- Student Selection Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-header bg-white py-3 border-bottom-0">
            <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
              <i data-lucide="user" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
              Select Student / Invoice
            </h6>
          </div>
          <div class="card-body pt-0">
            <div class="mb-3">
              <label for="student_selector" class="form-label fw-semibold fs-7 text-dark">
                Choose Student <span class="text-danger">*</span>
              </label>
              <select id="student_selector" class="form-select form-select-lg" onchange="onStudentSelect(this.value)">
                <option value="">-- Select Student --</option>
                <optgroup label="Students with Pending Invoices">
                  @foreach($pendingInvoicesByStudent as $admId => $stData)
                    @php 
                      $totalPendingDue = array_sum(array_column($stData['invoices'], 'due_balance'));
                      $invCount = count($stData['invoices']);
                    @endphp
                    <option value="{{ $admId }}" {{ ($selectedInvoice && $selectedInvoice->admission_id == $admId) ? 'selected' : '' }}>
                      {{ $stData['student_name'] }} (Adm: {{ $stData['admission_no'] }} - {{ $stData['class_name'] }}) | {{ $invCount }} {{ Str::plural('Voucher', $invCount) }} Pending (Rs. {{ number_format($totalPendingDue, 2) }})
                    </option>
                  @endforeach
                </optgroup>
                <optgroup label="All Other Students / Invoices">
                  @foreach($allInvoices as $inv)
                    @if(!isset($pendingInvoicesByStudent[$inv->admission_id]))
                      @php $stName = $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name . ' - Adm: ' . $inv->admission->admission_no) : 'Student'; @endphp
                      <option value="single_{{ $inv->id }}" {{ ($selectedInvoice && $selectedInvoice->id == $inv->id) ? 'selected' : '' }}>
                        {{ $inv->invoice_no }} - {{ $stName }} ({{ strtoupper($inv->status) }})
                      </option>
                    @endif
                  @endforeach
                </optgroup>
              </select>
            </div>

            <!-- Student Preview Box -->
            <div id="studentDetailBox">
              <div class="text-muted text-center py-4 fs-7">
                <i data-lucide="user-check" class="mb-2 d-block mx-auto text-secondary opacity-50" style="width:2rem;height:2rem;"></i>
                Select a student above to load their pending fee invoices.
              </div>
            </div>
          </div>
        </div>

        <!-- Total Payment Summary Card -->
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-header bg-white py-3 border-bottom-0">
            <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
              <i data-lucide="calculator" class="text-success" style="width:1.1rem;height:1.1rem;"></i>
              Payment Summary
            </h6>
          </div>
          <div class="card-body pt-0">
            <div class="p-3 border rounded-3 bg-light text-center">
              <div class="text-muted fs-7 mb-1">Total Selected Amount to Pay</div>
              <div class="display-6 fw-bold text-success mb-1" id="totalPaymentDisplay">Rs. 0.00</div>
              <div class="text-muted fs-8" id="selectedInvoiceCountLabel">0 invoices selected</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Pending Invoices Table & Collection Form -->
      <div class="col-lg-7 col-xl-8">
        <!-- Multi-Invoice Selection Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="layers" class="text-primary" style="width:1.25rem;height:1.25rem;"></i>
                Pending Invoices Collection
              </h5>
              <small class="text-muted">Check invoices to pay or enter a partial lump sum amount below.</small>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 fw-semibold">
              Receipt #: {{ $receiptNo }}
            </span>
          </div>

          <!-- Lump Sum Quick Input Strip -->
          <div id="lumpSumStrip" class="p-3 border-bottom bg-light d-none">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
              <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 440px;">
                <label for="lump_sum_amount" class="form-label mb-0 fw-bold fs-7 text-nowrap" style="color: #3d1a06;">
                  Lump Sum Cash (کل نقد):
                </label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-white fw-bold">Rs.</span>
                  <input type="number" 
                         step="0.01" 
                         min="0" 
                         id="lump_sum_amount" 
                         class="form-control form-control-sm fw-bold text-success fs-6" 
                         placeholder="Enter cash amount (e.g. 2500)" 
                         oninput="distributeLumpSum(this.value)">
                </div>
              </div>
              <div class="d-flex align-items-center gap-1">
                <button type="button" class="btn btn-sm text-white fw-semibold px-3" style="background-color: #3d1a06;" onclick="distributeLumpSum(document.getElementById('lump_sum_amount').value)">
                  Auto-Distribute
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold" onclick="resetToFullDues()">
                  Reset Full Dues
                </button>
              </div>
            </div>
            <small class="text-muted fs-8 d-block mt-1">Entering a lump sum (e.g. 2,500 PKR) will automatically distribute the payment to the oldest pending invoices first.</small>
          </div>

          <div class="card-body p-0">
            <div id="invoicesTableContainer">
              <div class="text-muted text-center py-5 fs-7">
                <i data-lucide="file-text" class="mb-2 d-block mx-auto text-secondary opacity-50" style="width:2.5rem;height:2.5rem;"></i>
                Select a student from the left column to view their pending invoices.
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Details & Submission Form Card -->
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-header bg-white py-3 border-bottom">
            <h6 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
              <i data-lucide="credit-card" class="text-dark" style="width:1.1rem;height:1.1rem;"></i>
              Payment Method & Remarks
            </h6>
          </div>
          <div class="card-body p-4">
            <div class="row g-3 mb-4">
              <!-- Payment Date -->
              <div class="col-md-6">
                <label for="payment_date" class="form-label fw-semibold text-dark fs-7">
                  Payment Date <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted"><i data-lucide="calendar" style="width:1rem;height:1rem;"></i></span>
                  <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                </div>
              </div>

              <!-- Payment Method -->
              <div class="col-md-6">
                <label for="payment_method" class="form-label fw-semibold text-dark fs-7">
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

              <!-- Notes / Remarks -->
              <div class="col-12">
                <label for="note" class="form-label fw-semibold text-dark fs-7">
                  Transaction Note / Remarks <small class="text-muted fw-normal">(Optional)</small>
                </label>
                <textarea name="note" id="note" class="form-control" rows="2" placeholder="Add transaction reference, cheque no, or payment remarks here...">{{ old('note') }}</textarea>
              </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
              <a href="{{ route('fee-management.index') }}" class="btn btn-light px-4">Cancel</a>
              <button type="submit" id="submitBtn" class="btn text-white btn-lg px-5 fw-bold d-inline-flex align-items-center gap-2" style="background-color: #3d1a06; border-color: #3d1a06;" disabled>
                <i data-lucide="check-circle" style="width:1.25rem;height:1.25rem;"></i>
                <span id="submitBtnText">Collect & Save Payment</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

@push('scripts')
<script>
var pendingInvoicesData = @json($pendingInvoicesByStudent);
var allInvoicesRaw = @json($allInvoices);

function onStudentSelect(val) {
  var container = document.getElementById('invoicesTableContainer');
  var studentBox = document.getElementById('studentDetailBox');
  var lumpSumStrip = document.getElementById('lumpSumStrip');

  document.getElementById('lump_sum_amount').value = '';

  if (!val) {
    lumpSumStrip.classList.add('d-none');
    container.innerHTML = `
      <div class="text-muted text-center py-5 fs-7">
        <i data-lucide="file-text" class="mb-2 d-block mx-auto text-secondary opacity-50" style="width:2.5rem;height:2.5rem;"></i>
        Select a student from the left column to view their pending invoices.
      </div>
    `;
    studentBox.innerHTML = `
      <div class="text-muted text-center py-4 fs-7">
        <i data-lucide="user-check" class="mb-2 d-block mx-auto text-secondary opacity-50" style="width:2rem;height:2rem;"></i>
        Select a student above to load their pending fee invoices.
      </div>
    `;
    recalcTotal();
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }
    return;
  }

  if (val.startsWith('single_')) {
    lumpSumStrip.classList.add('d-none');
    var invId = parseInt(val.replace('single_', ''));
    var inv = allInvoicesRaw.find(function(i) { return i.id === invId; });
    if (inv) {
      renderSingleInvoiceUI(inv);
    }
    return;
  }

  var admId = parseInt(val);
  var stData = pendingInvoicesData[admId];

  if (!stData) {
    lumpSumStrip.classList.add('d-none');
    container.innerHTML = `<div class="p-4 text-center text-muted">No pending invoices found.</div>`;
    return;
  }

  lumpSumStrip.classList.remove('d-none');

  // Render Student Info Box
  studentBox.innerHTML = `
    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3">
      <div class="avatar bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.25rem;background-color:#3d1a06 !important;">
        ${stData.student_name.charAt(0).toUpperCase()}
      </div>
      <div>
        <h6 class="fw-bold mb-0 text-dark">${stData.student_name}</h6>
        <span class="badge bg-secondary-subtle text-secondary fs-8">Adm No: ${stData.admission_no}</span>
      </div>
    </div>
    <div class="row g-2 fs-7">
      <div class="col-6 text-muted">Class & Section:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stData.class_name}</div>
      <div class="col-6 text-muted">Roll No:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stData.roll_no}</div>
      <div class="col-6 text-muted">Father / Guardian:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stData.father_name}</div>
      <div class="col-6 text-muted">Contact No:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stData.phone}</div>
    </div>
  `;

  // Render Multi-Invoice Table
  var rowsHtml = '';
  stData.invoices.forEach(function(inv, idx) {
    rowsHtml += `
      <tr>
        <td class="ps-3">
          <input type="checkbox" class="form-check-input invoice-chk" id="chk_${inv.id}" data-id="${inv.id}" data-due="${inv.due_balance}" checked onchange="toggleInvoiceInput(${inv.id})">
        </td>
        <td>
          <span class="fw-bold text-primary font-monospace">${inv.invoice_no}</span>
          <small class="text-muted d-block">${inv.fee_month}</small>
        </td>
        <td class="text-capitalize fw-medium fs-7">${inv.fee_type}</td>
        <td class="fs-7">Rs. ${inv.net_amount.toFixed(2)}</td>
        <td class="fs-7 text-success">Rs. ${inv.paid_amount.toFixed(2)}</td>
        <td class="fw-bold text-danger fs-7">Rs. ${inv.due_balance.toFixed(2)}</td>
        <td class="pe-3" style="width:160px;">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light fs-8">Rs.</span>
            <input type="number" 
                   step="0.01" 
                   min="0.01" 
                   max="${inv.due_balance}"
                   name="invoice_payments[${inv.id}]" 
                   id="pay_input_${inv.id}" 
                   class="form-control form-control-sm fw-bold text-success pay-amt-input" 
                   value="${inv.due_balance.toFixed(2)}" 
                   oninput="recalcTotal()">
          </div>
        </td>
      </tr>
    `;
  });

  container.innerHTML = `
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 fs-7">
        <thead class="table-light">
          <tr>
            <th class="ps-3" style="width:40px;">
              <input type="checkbox" class="form-check-input" id="selectAllChk" checked onchange="toggleSelectAll(this.checked)">
            </th>
            <th>Voucher #</th>
            <th>Fee Type</th>
            <th>Net Amount</th>
            <th>Paid</th>
            <th>Due Balance</th>
            <th class="pe-3">Collecting Amount</th>
          </tr>
        </thead>
        <tbody>
          ${rowsHtml}
        </tbody>
      </table>
    </div>
  `;

  recalcTotal();
  if (typeof lucide !== 'undefined') { lucide.createIcons(); }
}

function renderSingleInvoiceUI(inv) {
  var studentBox = document.getElementById('studentDetailBox');
  var container = document.getElementById('invoicesTableContainer');

  var st = inv.admission;
  var stName = st ? (st.first_name + ' ' + st.last_name) : 'N/A';
  var stNo = st ? st.admission_no : 'N/A';
  var stClass = st ? (st.class_name + (st.section_name ? ' ('+st.section_name+')' : '')) : 'N/A';
  var stFather = st ? (st.father_name || st.guardian_name) : 'N/A';
  var stPhone = st ? (st.father_phone || st.guardian_primary_mobile_no) : 'N/A';

  studentBox.innerHTML = `
    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3">
      <div class="avatar bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.25rem;background-color:#3d1a06 !important;">
        ${stName.charAt(0).toUpperCase()}
      </div>
      <div>
        <h6 class="fw-bold mb-0 text-dark">${stName}</h6>
        <span class="badge bg-secondary-subtle text-secondary fs-8">Adm No: ${stNo}</span>
      </div>
    </div>
    <div class="row g-2 fs-7">
      <div class="col-6 text-muted">Class & Section:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stClass}</div>
      <div class="col-6 text-muted">Father / Guardian:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stFather}</div>
      <div class="col-6 text-muted">Contact No:</div>
      <div class="col-6 fw-semibold text-dark text-end">${stPhone}</div>
    </div>
  `;

  var net = parseFloat(inv.amount) - parseFloat(inv.discount || 0);
  var paid = parseFloat(inv.paid_amount || 0);
  var due = Math.max(0, net - paid);

  container.innerHTML = `
    <div class="p-3">
      <table class="table table-hover align-middle mb-0 fs-7">
        <thead class="table-light">
          <tr>
            <th class="ps-3" style="width:40px;">
              <input type="checkbox" class="form-check-input" id="chk_${inv.id}" data-id="${inv.id}" data-due="${due}" checked onchange="toggleInvoiceInput(${inv.id})">
            </th>
            <th>Voucher #</th>
            <th>Fee Type</th>
            <th>Net Amount</th>
            <th>Paid</th>
            <th>Due Balance</th>
            <th class="pe-3">Collecting Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="ps-3"></td>
            <td class="fw-bold text-primary font-monospace">${inv.invoice_no}</td>
            <td class="text-capitalize fw-medium fs-7">${inv.fee_type} (${inv.fee_month})</td>
            <td class="fs-7">Rs. ${net.toFixed(2)}</td>
            <td class="fs-7 text-success">Rs. ${paid.toFixed(2)}</td>
            <td class="fw-bold text-danger fs-7">Rs. ${due.toFixed(2)}</td>
            <td class="pe-3" style="width:160px;">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-light fs-8">Rs.</span>
                <input type="number" 
                       step="0.01" 
                       min="0.01" 
                       name="invoice_payments[${inv.id}]" 
                       id="pay_input_${inv.id}" 
                       class="form-control form-control-sm fw-bold text-success pay-amt-input" 
                       value="${due.toFixed(2)}" 
                       oninput="recalcTotal()">
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  `;

  recalcTotal();
  if (typeof lucide !== 'undefined') { lucide.createIcons(); }
}

function distributeLumpSum(lumpSumVal) {
  var lumpSum = parseFloat(lumpSumVal);
  var chks = document.querySelectorAll('.invoice-chk');

  if (isNaN(lumpSum) || lumpSum <= 0) {
    return;
  }

  var remainingToDistribute = lumpSum;

  chks.forEach(function(chk) {
    var invId = chk.getAttribute('data-id');
    var due = parseFloat(chk.getAttribute('data-due')) || 0;
    var input = document.getElementById('pay_input_' + invId);

    if (remainingToDistribute > 0 && due > 0) {
      chk.checked = true;
      input.disabled = false;

      if (remainingToDistribute >= due) {
        input.value = due.toFixed(2);
        remainingToDistribute -= due;
      } else {
        input.value = remainingToDistribute.toFixed(2);
        remainingToDistribute = 0;
      }
    } else {
      chk.checked = false;
      input.disabled = true;
      input.value = '0.00';
    }
  });

  recalcTotal();
}

function resetToFullDues() {
  document.getElementById('lump_sum_amount').value = '';
  var chks = document.querySelectorAll('.invoice-chk');
  chks.forEach(function(chk) {
    chk.checked = true;
    var invId = chk.getAttribute('data-id');
    var due = parseFloat(chk.getAttribute('data-due')) || 0;
    var input = document.getElementById('pay_input_' + invId);
    if (input) {
      input.disabled = false;
      input.value = due.toFixed(2);
    }
  });
  var selectAll = document.getElementById('selectAllChk');
  if (selectAll) selectAll.checked = true;
  recalcTotal();
}

function toggleSelectAll(checked) {
  var chks = document.querySelectorAll('.invoice-chk');
  chks.forEach(function(chk) {
    chk.checked = checked;
    var invId = chk.getAttribute('data-id');
    toggleInvoiceInput(invId);
  });
  recalcTotal();
}

function toggleInvoiceInput(invId) {
  var chk = document.getElementById('chk_' + invId);
  var input = document.getElementById('pay_input_' + invId);
  if (!input || !chk) return;

  if (chk.checked) {
    input.disabled = false;
    if (!input.value || parseFloat(input.value) <= 0) {
      input.value = chk.getAttribute('data-due') || '0.00';
    }
  } else {
    input.disabled = true;
  }
  recalcTotal();
}

function recalcTotal() {
  var inputs = document.querySelectorAll('.pay-amt-input');
  var total = 0;
  var count = 0;

  inputs.forEach(function(inp) {
    if (!inp.disabled && inp.value) {
      var val = parseFloat(inp.value);
      if (val > 0) {
        total += val;
        count++;
      }
    }
  });

  document.getElementById('totalPaymentDisplay').innerText = 'Rs. ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  document.getElementById('selectedInvoiceCountLabel').innerText = count + ' ' + (count === 1 ? 'invoice selected' : 'invoices selected');

  var btn = document.getElementById('submitBtn');
  var btnText = document.getElementById('submitBtnText');

  if (total > 0 && count > 0) {
    btn.disabled = false;
    btnText.innerText = 'Collect Payments (Rs. ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ')';
  } else {
    btn.disabled = true;
    btnText.innerText = 'Collect & Save Payment';
  }
}

// Auto select if selectedInvoice was passed
document.addEventListener('DOMContentLoaded', function() {
  var selector = document.getElementById('student_selector');
  if (selector && selector.value) {
    onStudentSelect(selector.value);
  }
});
</script>
@endpush
@endsection
