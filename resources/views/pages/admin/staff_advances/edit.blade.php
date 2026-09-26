@extends('layouts.app')

@section('title', 'Edit Staff Salary Advance')

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Edit Salary Advance</h1>
      <p class="page-subtitle">Update salary advance parameters</p>
    </div>
    <div>
      <a href="{{ route('staff-advances.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to List
      </a>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm col-lg-8 mx-auto">
    <div class="card-body p-4">
      <form action="{{ route('staff-advances.update', $advance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label fw-semibold">Staff Member <span class="text-danger">*</span></label>
          <select name="staff_id" id="staff_select" class="form-select @error('staff_id') is-invalid @enderror" required>
            <option value="" data-salary="0" data-advance="0">-- Select Staff Member --</option>
            @foreach($staffList as $stf)
              @php
                $otherAdvanceSum = $stf->advances
                  ->filter(fn($a) => $a->id != $advance->id && in_array(is_object($a->status) ? $a->status->value : $a->status, ['approved', 'pending']))
                  ->sum(fn($a) => (float) $a->advance_amount - (float) $a->repaid_amount);
                $salary = (float)($stf->salary ?? 0);
              @endphp
              <option value="{{ $stf->id }}" 
                      data-salary="{{ $salary }}" 
                      data-advance="{{ $otherAdvanceSum }}"
                      {{ (old('staff_id', $advance->staff_id) == $stf->id) ? 'selected' : '' }}>
                {{ $stf->full_name }} (ID: {{ $stf->staff_id ?? 'STF-'.$stf->id }} | Basic Salary: Rs. {{ number_format($salary, 2) }})
              </option>
            @endforeach
          </select>
          @error('staff_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Dynamic Staff Financial Summary Card -->
        <div id="staff_financial_summary" class="mb-4 p-3 bg-light rounded border d-none">
          <h6 class="fw-bold text-dark mb-2">Staff Financial Overview</h6>
          <div class="row text-center g-2">
            <div class="col-md-4">
              <div class="bg-white p-2 rounded border shadow-sm">
                <small class="text-muted d-block">Basic Salary</small>
                <span class="fw-bold text-primary fs-6" id="display_basic_salary">Rs. 0.00</span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="bg-white p-2 rounded border shadow-sm">
                <small class="text-muted d-block">Already Taken Advance</small>
                <span class="fw-bold text-warning fs-6" id="display_advance_taken">Rs. 0.00</span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="bg-white p-2 rounded border shadow-sm">
                <small class="text-muted d-block">Net Remaining Salary</small>
                <span class="fw-bold text-success fs-6" id="display_remaining_salary">Rs. 0.00</span>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Advance Amount (Rs.) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="1" name="advance_amount" id="advance_amount_input" class="form-control @error('advance_amount') is-invalid @enderror" value="{{ old('advance_amount', $advance->advance_amount) }}" placeholder="e.g. 25000" required>
            @error('advance_amount')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Advance Date <span class="text-danger">*</span></label>
            <input type="date" name="advance_date" class="form-control @error('advance_date') is-invalid @enderror" value="{{ old('advance_date', $advance->advance_date ? $advance->advance_date->format('Y-m-d') : '') }}" required>
            @error('advance_date')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
              <option value="cash" {{ old('payment_method', $advance->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
              <option value="bank_transfer" {{ old('payment_method', $advance->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
              <option value="cheque" {{ old('payment_method', $advance->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
            </select>
            @error('payment_method')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            @php $currentStatus = is_object($advance->status) ? $advance->status->value : $advance->status; @endphp
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
              <option value="approved" {{ old('status', $currentStatus) == 'approved' ? 'selected' : '' }}>Approved (Active)</option>
              <option value="pending" {{ old('status', $currentStatus) == 'pending' ? 'selected' : '' }}>Pending Approval</option>
              <option value="rejected" {{ old('status', $currentStatus) == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Reason / Purpose</label>
          <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason', $advance->reason) }}" placeholder="e.g. Medical emergency, family event, personal loan">
          @error('reason')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold">Additional Notes</label>
          <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Optional comments or approval details">{{ old('notes', $advance->notes) }}</textarea>
          @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">
          <a href="{{ route('staff-advances.index') }}" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="check" style="width:1rem;height:1rem;" class="me-1"></i> Save Changes
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
    .select2-container--bootstrap-5 .select2-dropdown {
      border-color: #dee2e6;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
  </style>
@endpush

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function () {
      const $staffSelect = $('#staff_select');
      const summaryCard = document.getElementById('staff_financial_summary');
      const displaySalary = document.getElementById('display_basic_salary');
      const displayAdvance = document.getElementById('display_advance_taken');
      const displayRemaining = document.getElementById('display_remaining_salary');
      const advanceInput = document.getElementById('advance_amount_input');

      // Initialize Select2 with search
      $staffSelect.select2({
        theme: 'bootstrap-5',
        placeholder: '-- Search & Select Staff Member --',
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

      function updateSummary() {
        const staffSelectElem = $staffSelect[0];
        if (!staffSelectElem || staffSelectElem.selectedIndex < 0) {
          summaryCard.classList.add('d-none');
          return;
        }

        const selectedOption = staffSelectElem.options[staffSelectElem.selectedIndex];
        if (!selectedOption || !staffSelectElem.value) {
          summaryCard.classList.add('d-none');
          return;
        }

        const salary = parseFloat(selectedOption.getAttribute('data-salary') || 0);
        const existingAdvance = parseFloat(selectedOption.getAttribute('data-advance') || 0);
        const currentAdvanceInput = parseFloat(advanceInput.value || 0);

        summaryCard.classList.remove('d-none');
        displaySalary.textContent = formatMoney(salary);
        displayAdvance.textContent = formatMoney(existingAdvance);

        const totalAdvanceDeduction = existingAdvance + currentAdvanceInput;
        const netRemaining = salary - totalAdvanceDeduction;

        displayRemaining.textContent = formatMoney(netRemaining);
        if (netRemaining < 0) {
          displayRemaining.className = 'fw-bold text-danger fs-6';
        } else {
          displayRemaining.className = 'fw-bold text-success fs-6';
        }
      }

      $staffSelect.on('change', updateSummary);
      advanceInput.addEventListener('input', updateSummary);

      // Initial check on load
      if ($staffSelect.val()) {
        updateSummary();
      }
    });
  </script>
@endpush
@endsection
