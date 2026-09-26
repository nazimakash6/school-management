@extends('layouts.app')

@section('title', 'Edit Payroll Entry')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/payroll-create.css') }}">
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
    .select2-container--bootstrap-5 .select2-dropdown {
      border-color: #dee2e6;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
  </style>
@endpush

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Edit Payroll Record</h1>
      <p class="page-subtitle">Update salary slip entry for {{ $payroll->staff ? $payroll->staff->full_name : 'Staff Member' }}</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">
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
      <form action="{{ route('payroll.update', $payroll->id) }}" method="POST" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-6">
          <label for="staff_id" class="form-label fw-semibold">Select Staff Member <span
              class="text-danger">*</span></label>
          <select name="staff_id" id="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
            <option value="" data-salary="0" data-advance="0" data-dept="" data-desig="">-- Choose Staff Member --
            </option>
            @foreach ($staffMembers as $staff)
              @php
                $advanceBalance = $staff->active_advance_balance;
              @endphp
              <option value="{{ $staff->id }}" data-salary="{{ $staff->salary ?? 0 }}" data-advance="{{ $advanceBalance }}"
                data-dept="{{ $staff->formatted_department }}" data-desig="{{ $staff->formatted_designation }}"
                @selected(old('staff_id', $payroll->staff_id) == $staff->id)>
                {{ $staff->full_name }} (ID: {{ $staff->staff_id ?? 'STF-' . $staff->id }} | Salary: Rs.
                {{ number_format($staff->salary ?? 0, 2) }})
              </option>
            @endforeach
          </select>
          @error('staff_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label for="payroll_month" class="form-label fw-semibold">Payroll Month <span
              class="text-danger">*</span></label>
          <input type="month" name="payroll_month" id="payroll_month"
            class="form-control @error('payroll_month') is-invalid @enderror"
            value="{{ old('payroll_month', $payroll->payroll_month) }}" required>
          @error('payroll_month')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Existing Payroll Warning Alert -->
        <div id="existing_payroll_alert" class="col-md-12 d-none">
          <div class="alert alert-warning border border-warning d-flex align-items-center justify-content-between p-3 mb-0 shadow-sm rounded-3">
            <div>
              <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-1">
                <i data-lucide="alert-triangle" class="text-warning" style="width:1.2rem;height:1.2rem;"></i>
                Another Payroll Entry Already Exists
              </h6>
              <p class="mb-0 text-dark small" id="existing_payroll_text">
                A payroll record has already been created for this staff member for the selected month.
              </p>
            </div>
            <div>
              <a href="#" id="existing_payroll_link" target="_blank" class="btn btn-warning btn-sm fw-bold">
                View Payslip <i data-lucide="external-link" style="width:0.875rem;height:0.875rem;" class="ms-1"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Dynamic Staff & Advance Info Card -->
        <div class="col-md-12">
          <div id="staff_advance_info_card" class="p-3 bg-light rounded border d-none">
            <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
              <i data-lucide="user-check" class="text-primary" style="width:1rem;height:1rem;"></i>
              Staff &amp; Financial Overview
            </h6>
            <div class="row text-center g-2">
              <div class="col-md-4">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block">Basic Salary</small>
                  <span class="fw-bold text-primary fs-6" id="info_basic_salary">Rs. {{ number_format($payroll->basic_salary, 2) }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block">Unpaid Advance Balance</small>
                  <span class="fw-bold text-danger fs-6" id="info_advance_balance">Rs. 0.00</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block">Department &amp; Designation</small>
                  <span class="fw-semibold text-dark fs-6" id="info_dept_desig">{{ $payroll->staff ? ($payroll->staff->formatted_department . ' • ' . $payroll->staff->formatted_designation) : 'N/A' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Unpaid Salary Advances Breakdown Card -->
        <div id="unpaid_advances_card" class="col-md-12 d-none">
          <div class="card border border-danger-subtle bg-danger-subtle bg-opacity-10 shadow-sm">
            <div class="card-header bg-white py-2.5 d-flex justify-content-between align-items-center">
              <h6 class="mb-0 fw-bold text-danger d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                <i data-lucide="alert-circle" style="width:1.1rem;height:1.1rem;"></i>
                Active / Unpaid Salary Advances History
              </h6>
              <span class="badge bg-danger text-white rounded-pill px-2.5 py-1" id="unpaid_count_badge">0 Unpaid</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                  <thead class="table-light">
                    <tr>
                      <th class="ps-3">Voucher No</th>
                      <th>Advance Date</th>
                      <th>Reason / Purpose</th>
                      <th class="text-end">Sanctioned (Rs.)</th>
                      <th class="text-end">Repaid (Rs.)</th>
                      <th class="text-end pe-3">Unpaid Balance (Rs.)</th>
                    </tr>
                  </thead>
                  <tbody id="unpaid_advances_tbody">
                  </tbody>
                  <tfoot>
                    <tr class="table-danger fw-bold">
                      <td colspan="5" class="text-end ps-3">TOTAL UNPAID ADVANCE DEDUCTION:</td>
                      <td class="text-end pe-3 text-danger fs-6" id="unpaid_advances_total">Rs. 0.00</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <label for="basic_salary" class="form-label fw-semibold">Basic Salary (PKR)</label>
          <input type="number" step="0.01" name="basic_salary" id="basic_salary"
            class="form-control bg-light @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary', $payroll->basic_salary) }}"
            readonly required>
          @error('basic_salary')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="allowance" class="form-label fw-semibold">Allowances (PKR)</label>
          <input type="number" step="0.01" min="0" name="allowance" id="allowance"
            class="form-control @error('allowance') is-invalid @enderror" value="{{ old('allowance', $payroll->allowance) }}">
          @error('allowance')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="deduction" class="form-label fw-semibold">Total Deductions (PKR)</label>
          <input type="number" step="0.01" min="0" name="deduction" id="deduction"
            class="form-control @error('deduction') is-invalid @enderror" value="{{ old('deduction', $payroll->deduction) }}">
          <small class="text-muted" id="deduction_help_text">Includes auto-deducted active salary advance</small>
          @error('deduction')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-12">
          <div
            class="p-3 bg-primary-subtle border border-primary-subtle rounded d-flex justify-content-between align-items-center">
            <div>
              <span class="fw-bold fs-6 text-dark d-block">Net Disbursement Amount:</span>
            </div>
            <span class="fw-bold fs-4 text-primary" id="netSalaryDisplay">Rs. {{ number_format($payroll->net_salary, 2) }}</span>
          </div>
        </div>

        <div class="col-md-4">
          <label for="payment_method" class="form-label fw-semibold">Payment Method <span
              class="text-danger">*</span></label>
          <select name="payment_method" id="payment_method"
            class="form-select @error('payment_method') is-invalid @enderror" required>
            <option value="bank_transfer" @selected(old('payment_method', $payroll->payment_method) === 'bank_transfer')>Bank Transfer</option>
            <option value="cash" @selected(old('payment_method', $payroll->payment_method) === 'cash')>Cash</option>
            <option value="cheque" @selected(old('payment_method', $payroll->payment_method) === 'cheque')>Cheque</option>
          </select>
          @error('payment_method')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="payment_date" class="form-label fw-semibold">Payment Date</label>
          <input type="date" name="payment_date" id="payment_date"
            class="form-control @error('payment_date') is-invalid @enderror"
            value="{{ old('payment_date', $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : '') }}">
          @error('payment_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
          <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="paid" @selected(old('status', $payroll->status) === 'paid')>Paid</option>
            <option value="pending" @selected(old('status', $payroll->status) === 'pending')>Pending</option>
            <option value="cancelled" @selected(old('status', $payroll->status) === 'cancelled')>Cancelled</option>
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-12">
          <label for="notes" class="form-label fw-semibold">Notes / Remarks</label>
          <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2"
            placeholder="Additional notes regarding this payroll record">{{ old('notes', $payroll->notes) }}</textarea>
          @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 text-end mt-4">
          <a href="{{ route('payroll.index') }}" class="btn btn-secondary me-2">Cancel</a>
          <button type="submit" id="submit_payroll_btn" class="btn btn-primary">
            <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Update Payroll Entry
          </button>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
      $(document).ready(function () {
        const currentPayrollId = {{ $payroll->id }};
        const $staffSelect = $('#staff_id');
        const monthInput = document.getElementById('payroll_month');
        const basicSalaryInput = document.getElementById('basic_salary');
        const allowanceInput = document.getElementById('allowance');
        const deductionInput = document.getElementById('deduction');
        const netSalaryDisplay = document.getElementById('netSalaryDisplay');
        const notesTextarea = document.getElementById('notes');
        const submitBtn = document.getElementById('submit_payroll_btn');

        const infoCard = document.getElementById('staff_advance_info_card');
        const infoSalary = document.getElementById('info_basic_salary');
        const infoAdvance = document.getElementById('info_advance_balance');
        const infoDeptDesig = document.getElementById('info_dept_desig');
        const deductionHelp = document.getElementById('deduction_help_text');

        const existingAlert = document.getElementById('existing_payroll_alert');
        const existingText = document.getElementById('existing_payroll_text');
        const existingLink = document.getElementById('existing_payroll_link');

        const unpaidCard = document.getElementById('unpaid_advances_card');
        const unpaidTbody = document.getElementById('unpaid_advances_tbody');
        const unpaidCountBadge = document.getElementById('unpaid_count_badge');
        const unpaidTotalElem = document.getElementById('unpaid_advances_total');

        let isInitialLoad = true;

        $staffSelect.select2({
          theme: 'bootstrap-5',
          placeholder: '-- Choose Staff Member --',
          allowClear: true,
          width: '100%'
        });

        function formatMoney(amount) {
          const val = parseFloat(amount || 0);
          if (val < 0) {
            return '-Rs. ' + Math.abs(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
          }
          return 'Rs. ' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function updateNetSalary() {
          const basic = parseFloat(basicSalaryInput.value) || 0;
          const allowance = parseFloat(allowanceInput.value) || 0;
          const deduction = parseFloat(deductionInput.value) || 0;
          const net = basic + allowance - deduction;
          netSalaryDisplay.textContent = formatMoney(net);
          if (net < 0) {
            netSalaryDisplay.className = 'fw-bold fs-4 text-danger';
          } else {
            netSalaryDisplay.className = 'fw-bold fs-4 text-primary';
          }
        }

        function fetchStaffDetails() {
          const staffId = $staffSelect.val();
          const monthVal = monthInput ? monthInput.value : '';

          if (!staffId) {
            infoCard.classList.add('d-none');
            unpaidCard.classList.add('d-none');
            existingAlert.classList.add('d-none');
            if (submitBtn) submitBtn.disabled = false;
            updateNetSalary();
            return;
          }

          const url = "{{ route('payroll.staff-details') }}?staff_id=" + staffId + "&month=" + encodeURIComponent(monthVal) + "&payroll_id=" + currentPayrollId;

          fetch(url)
            .then(res => res.json())
            .then(data => {
              if (!data.success) return;

              const staff = data.staff;
              const existing = data.existing_payroll;
              const unpaidList = data.unpaid_advances || [];
              const totalUnpaid = parseFloat(data.total_unpaid_advance || 0);

              // 1. Staff Overview
              infoCard.classList.remove('d-none');
              infoSalary.textContent = formatMoney(staff.salary);
              infoAdvance.textContent = formatMoney(totalUnpaid);
              infoDeptDesig.textContent = staff.department + (staff.designation ? ' • ' + staff.designation : '');
              
              if (!isInitialLoad) {
                basicSalaryInput.value = staff.salary;
              }

              // 2. Existing Payroll Warning
              if (existing) {
                existingAlert.classList.remove('d-none');
                existingText.innerHTML = 'Another payroll entry for <strong>' + staff.full_name + '</strong> for month <strong>' + existing.payroll_month + '</strong> already exists with status <span class="badge bg-secondary">' + existing.status + '</span> (' + existing.formatted_net + ').';
                existingLink.href = existing.view_url;
                if (submitBtn) {
                  submitBtn.disabled = true;
                  submitBtn.title = "Another payroll already exists for this staff member in " + existing.payroll_month;
                }
              } else {
                existingAlert.classList.add('d-none');
                if (submitBtn) {
                  submitBtn.disabled = false;
                  submitBtn.title = "";
                }
              }

              // 3. Unpaid Advances Breakdown Table
              if (unpaidList.length > 0) {
                unpaidCard.classList.remove('d-none');
                unpaidCountBadge.textContent = unpaidList.length + ' Unpaid Record(s)';
                unpaidTotalElem.textContent = formatMoney(totalUnpaid);

                let rowsHtml = '';
                unpaidList.forEach(adv => {
                  rowsHtml += `
                    <tr>
                      <td class="ps-3 font-monospace fw-bold text-dark">${adv.voucher_no}</td>
                      <td>${adv.advance_date}</td>
                      <td>${adv.reason}</td>
                      <td class="text-end">Rs. ${parseFloat(adv.advance_amount).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                      <td class="text-end text-success">Rs. ${parseFloat(adv.repaid_amount).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                      <td class="text-end pe-3 fw-bold text-danger">Rs. ${parseFloat(adv.unpaid_balance).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
                    </tr>
                  `;
                });
                unpaidTbody.innerHTML = rowsHtml;

                if (!isInitialLoad) {
                  const suggestedDeduction = parseFloat(data.suggested_deduction || totalUnpaid);
                  deductionInput.value = suggestedDeduction;

                  if (totalUnpaid > staff.salary) {
                    const carryForward = totalUnpaid - suggestedDeduction;
                    deductionHelp.textContent = 'Auto-filled Rs. ' + suggestedDeduction.toLocaleString('en-US', {minimumFractionDigits:2}) + ' deduction (repaying advance up to monthly salary). Remaining ' + formatMoney(carryForward) + ' carries forward.';
                  } else {
                    deductionHelp.textContent = 'Auto-filled with active salary advance balance of ' + formatMoney(totalUnpaid);
                  }
                } else {
                  deductionHelp.textContent = 'Includes active salary advance breakdown. Current deduction is Rs. ' + parseFloat(deductionInput.value || 0).toLocaleString('en-US', {minimumFractionDigits:2});
                }
              } else {
                unpaidCard.classList.add('d-none');
                unpaidTbody.innerHTML = '';
                deductionHelp.textContent = 'No active salary advance';
              }

              isInitialLoad = false;
              updateNetSalary();
              if (window.lucide) {
                window.lucide.createIcons();
              }
            })
            .catch(err => {
              console.error('Error fetching staff details:', err);
              isInitialLoad = false;
            });
        }

        $staffSelect.on('change', function() {
          isInitialLoad = false;
          fetchStaffDetails();
        });
        
        $(monthInput).on('change input', function() {
          fetchStaffDetails();
        });

        allowanceInput.addEventListener('input', updateNetSalary);
        deductionInput.addEventListener('input', updateNetSalary);

        if ($staffSelect.val()) {
          fetchStaffDetails();
        } else {
          updateNetSalary();
        }
      });
    </script>
  @endpush
@endsection
