@extends('layouts.app')

@section('title', 'Create Payroll Entry')

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
      <h1 class="page-title">Create Payroll Record</h1>
      <p class="page-subtitle">Generate individual salary slip entry for a staff member</p>
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
      <form action="{{ route('payroll.store') }}" method="POST" class="row g-3">
        @csrf

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
                @selected(old('staff_id') == $staff->id)>
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
            value="{{ old('payroll_month', date('Y-m')) }}" required>
          @error('payroll_month')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Dynamic Staff & Advance Info Card -->
        <div class="col-md-12">
          <div id="staff_advance_info_card" class="p-3 bg-light rounded border d-none">
            <h6 class="fw-bold text-dark mb-2">Staff & Advance Overview</h6>
            <div class="row text-center g-2">
              <div class="col-md-4">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block">Basic Salary</small>
                  <span class="fw-bold text-primary fs-6" id="info_basic_salary">Rs. 0.00</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block">Advance Balance</small>
                  <span class="fw-bold text-danger fs-6" id="info_advance_balance">Rs. 0.00</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block">Department & Designation</small>
                  <span class="fw-semibold text-dark fs-6" id="info_dept_desig">N/A</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <label for="basic_salary" class="form-label fw-semibold">Basic Salary (PKR)</label>
          <input type="number" step="0.01" name="basic_salary" id="basic_salary"
            class="form-control bg-light @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary', 0) }}"
            readonly required>
          @error('basic_salary')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="allowance" class="form-label fw-semibold">Allowances (PKR)</label>
          <input type="number" step="0.01" min="0" name="allowance" id="allowance"
            class="form-control @error('allowance') is-invalid @enderror" value="{{ old('allowance', 0) }}">
          @error('allowance')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="deduction" class="form-label fw-semibold">Total Deductions (PKR)</label>
          <input type="number" step="0.01" min="0" name="deduction" id="deduction" readonly
            class="form-control @error('deduction') is-invalid @enderror" value="{{ old('deduction', 0) }}">
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
            <span class="fw-bold fs-4 text-primary" id="netSalaryDisplay">Rs. 0.00</span>
          </div>
        </div>

        <div class="col-md-4">
          <label for="payment_method" class="form-label fw-semibold">Payment Method <span
              class="text-danger">*</span></label>
          <select name="payment_method" id="payment_method"
            class="form-select @error('payment_method') is-invalid @enderror" required>
            <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank Transfer</option>
            <option value="cash" @selected(old('payment_method') === 'cash')>Cash</option>
            <option value="cheque" @selected(old('payment_method') === 'cheque')>Cheque</option>
          </select>
          @error('payment_method')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="payment_date" class="form-label fw-semibold">Payment Date</label>
          <input type="date" name="payment_date" id="payment_date"
            class="form-control @error('payment_date') is-invalid @enderror"
            value="{{ old('payment_date', date('Y-m-d')) }}">
          @error('payment_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
          <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="paid" @selected(old('status') === 'paid')>Paid</option>
            <option value="pending" @selected(old('status', 'pending') === 'pending')>Pending</option>
            <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-12">
          <label for="notes" class="form-label fw-semibold">Notes / Remarks</label>
          <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2"
            placeholder="Additional notes regarding this payroll record">{{ old('notes') }}</textarea>
          @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 text-end mt-4">
          <a href="{{ route('payroll.index') }}" class="btn btn-secondary me-2">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Save Payroll Entry
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
        const $staffSelect = $('#staff_id');

        $staffSelect.select2({
          theme: 'bootstrap-5',
          placeholder: '-- Choose Staff Member --',
          allowClear: true,
          width: '100%'
        });

        const basicSalaryInput = document.getElementById('basic_salary');
        const allowanceInput = document.getElementById('allowance');
        const deductionInput = document.getElementById('deduction');
        const netSalaryDisplay = document.getElementById('netSalaryDisplay');
        const notesTextarea = document.getElementById('notes');

        const infoCard = document.getElementById('staff_advance_info_card');
        const infoSalary = document.getElementById('info_basic_salary');
        const infoAdvance = document.getElementById('info_advance_balance');
        const infoDeptDesig = document.getElementById('info_dept_desig');
        const deductionHelp = document.getElementById('deduction_help_text');

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

        function handleStaffChange() {
          const staffSelectElem = $staffSelect[0];
          if (!staffSelectElem || staffSelectElem.selectedIndex < 0) {
            infoCard.classList.add('d-none');
            basicSalaryInput.value = 0;
            deductionInput.value = 0;
            updateNetSalary();
            return;
          }

          const selectedOption = staffSelectElem.options[staffSelectElem.selectedIndex];
          if (!selectedOption || !staffSelectElem.value) {
            infoCard.classList.add('d-none');
            basicSalaryInput.value = 0;
            deductionInput.value = 0;
            updateNetSalary();
            return;
          }

          const salary = parseFloat(selectedOption.getAttribute('data-salary') || 0);
          const advance = parseFloat(selectedOption.getAttribute('data-advance') || 0);
          const dept = selectedOption.getAttribute('data-dept') || '';
          const desig = selectedOption.getAttribute('data-desig') || '';

          infoCard.classList.remove('d-none');
          infoSalary.textContent = formatMoney(salary);
          infoAdvance.textContent = formatMoney(advance);
          infoDeptDesig.textContent = dept + (desig ? ' • ' + desig : '');

          basicSalaryInput.value = salary;

          // Auto-set deduction to advance amount if advance exists
          if (advance > 0) {
            deductionInput.value = advance;
            deductionHelp.textContent = 'Auto-filled with active salary advance balance of ' + formatMoney(advance);
            if (!notesTextarea.value || notesTextarea.value.includes('Auto-deducted salary advance')) {
              notesTextarea.value = 'Auto-deducted salary advance: ' + formatMoney(advance);
            }
          } else {
            deductionInput.value = 0;
            deductionHelp.textContent = 'No active salary advance';
          }

          updateNetSalary();
        }

        $staffSelect.on('change', handleStaffChange);
        allowanceInput.addEventListener('input', updateNetSalary);
        deductionInput.addEventListener('input', updateNetSalary);

        // Run initial calculation if staff is selected on load
        if ($staffSelect.val()) {
          handleStaffChange();
        } else {
          updateNetSalary();
        }
      });
    </script>
  @endpush
@endsection