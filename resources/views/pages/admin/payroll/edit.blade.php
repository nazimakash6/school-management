@extends('layouts.app')

@section('title', 'Edit Payroll Entry')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/payroll-edit.css') }}">
@endpush

@section('content')
  <div class="content-header">
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

  <div class="card">
    <div class="card-body">
      <form action="{{ route('payroll.update', $payroll) }}" method="POST" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-6">
          <label for="staff_id" class="form-label fw-medium">Staff Member <span class="text-danger">*</span></label>
          <select name="staff_id" id="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
            @foreach ($staffMembers as $staff)
              <option value="{{ $staff->id }}" data-salary="{{ $staff->salary }}" @selected(old('staff_id', $payroll->staff_id) == $staff->id)>
                {{ $staff->full_name }} (ID: {{ $staff->staff_id }} • {{ $staff->formatted_department }} - {{ $staff->formatted_designation }})
              </option>
            @endforeach
          </select>
          @error('staff_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label for="payroll_month" class="form-label fw-medium">Payroll Month <span class="text-danger">*</span></label>
          <input type="month" name="payroll_month" id="payroll_month" class="form-control @error('payroll_month') is-invalid @enderror"
            value="{{ old('payroll_month', $payroll->payroll_month) }}" required>
          @error('payroll_month')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="basic_salary" class="form-label fw-medium">Basic Salary (PKR) <span class="text-danger">*</span></label>
          <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror"
            value="{{ old('basic_salary', $payroll->basic_salary) }}" required>
          @error('basic_salary')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="allowance" class="form-label fw-medium">Allowances (PKR)</label>
          <input type="number" step="0.01" name="allowance" id="allowance" class="form-control @error('allowance') is-invalid @enderror"
            value="{{ old('allowance', $payroll->allowance) }}">
          @error('allowance')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="deduction" class="form-label fw-medium">Deductions (PKR)</label>
          <input type="number" step="0.01" name="deduction" id="deduction" class="form-control @error('deduction') is-invalid @enderror"
            value="{{ old('deduction', $payroll->deduction) }}">
          @error('deduction')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-12">
          <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-6">Calculated Net Salary:</span>
            <span class="fw-bold fs-5 text-primary" id="netSalaryDisplay">Rs. {{ number_format($payroll->net_salary, 2) }}</span>
          </div>
        </div>

        <div class="col-md-4">
          <label for="payment_method" class="form-label fw-medium">Payment Method <span class="text-danger">*</span></label>
          <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
            <option value="bank_transfer" @selected(old('payment_method', $payroll->payment_method) === 'bank_transfer')>Bank Transfer</option>
            <option value="cash" @selected(old('payment_method', $payroll->payment_method) === 'cash')>Cash</option>
            <option value="cheque" @selected(old('payment_method', $payroll->payment_method) === 'cheque')>Cheque</option>
          </select>
          @error('payment_method')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="payment_date" class="form-label fw-medium">Payment Date</label>
          <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror"
            value="{{ old('payment_date', $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : '') }}">
          @error('payment_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
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
          <label for="notes" class="form-label fw-medium">Notes / Remarks</label>
          <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2"
            placeholder="Additional notes regarding this payroll record">{{ old('notes', $payroll->notes) }}</textarea>
          @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 text-end mt-4">
          <a href="{{ route('payroll.index') }}" class="btn btn-secondary me-2">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="save" style="width:1rem;height:1rem;" class="me-1"></i> Update Payroll Entry
          </button>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var basicSalaryInput = document.getElementById('basic_salary');
        var allowanceInput = document.getElementById('allowance');
        var deductionInput = document.getElementById('deduction');
        var netSalaryDisplay = document.getElementById('netSalaryDisplay');

        function updateNetSalary() {
          var basic = parseFloat(basicSalaryInput.value) || 0;
          var allowance = parseFloat(allowanceInput.value) || 0;
          var deduction = parseFloat(deductionInput.value) || 0;
          var net = Math.max(0, basic + allowance - deduction);
          netSalaryDisplay.textContent = 'Rs. ' + net.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        basicSalaryInput.addEventListener('input', updateNetSalary);
        allowanceInput.addEventListener('input', updateNetSalary);
        deductionInput.addEventListener('input', updateNetSalary);

        updateNetSalary();
      });
    </script>
  @endpush
@endsection
