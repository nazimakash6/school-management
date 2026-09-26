@extends('layouts.app')

@section('title', 'Edit Fee Invoice - ' . $invoice->invoice_no)

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Edit Fee Invoice</h1>
      <p class="page-subtitle">Update details for fee voucher <strong>#{{ $invoice->invoice_no }}</strong></p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('fee-management.show', $invoice->id) }}" class="btn btn-outline-secondary btn-sm me-1">
        <i data-lucide="eye" style="width:1rem;height:1rem;" class="me-1"></i> View Invoice
      </a>
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

  <div class="card border-0 shadow-sm col-lg-10 mx-auto">
    <div class="card-body p-4">
      <form action="{{ route('fee-management.update', $invoice->id) }}" method="POST" class="row g-3" id="feeInvoiceForm">
        @csrf
        @method('PUT')

        {{-- 1. ACADEMIC SESSION, CLASS FILTER, AND STUDENT DROPDOWN --}}
        <div class="col-md-4">
          <label for="academic_session_id" class="form-label fw-semibold">Academic Session <span class="text-danger">*</span></label>
          <select name="academic_session_id" id="academic_session_id" class="form-select @error('academic_session_id') is-invalid @enderror" required>
            <option value="">-- Select Academic Session --</option>
            @foreach($academicSessions as $session)
              @php
                $sessStatus = is_object($session->status) ? ($session->status->value ?? $session->status->name) : $session->status;
              @endphp
              <option value="{{ $session->id }}" @selected(old('academic_session_id', $invoice->academic_session_id) == $session->id)>
                {{ $session->session_name }}
              </option>
            @endforeach
          </select>
          @error('academic_session_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="class_filter" class="form-label fw-semibold">Class Filter</label>
          <select id="class_filter" class="form-select">
            <option value="">-- All Classes --</option>
            @foreach($classes as $c)
              @php
                $cName = is_object($c) ? ($c->name ?? $c->class_name) : $c;
              @endphp
              @if($cName)
                <option value="{{ $cName }}" {{ ($invoice->admission && $invoice->admission->class_name == $cName) ? 'selected' : '' }}>{{ $cName }}</option>
              @endif
            @endforeach
          </select>
          <small class="text-muted fs-8">Filter students by class</small>
        </div>

        <div class="col-md-4">
          <label for="admission_id" class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
          <select name="admission_id" id="admission_id" class="form-select @error('admission_id') is-invalid @enderror" required>
            <option value="" data-class="" data-section="" data-father="" data-no="" data-session="" data-fee-plan="Monthly" data-base-fee="0" data-discount="0" data-net-fee="0" data-reg-fee="0" data-unpaid='[]' data-siblings='[]'>-- Search & Select Student --</option>
            @foreach($students as $st)
              @php
                $stStudent = \App\Models\Student::where('admission_no', $st->admission_no)->first() ?: $st;
                $planName = $stStudent->fee_plan ?: ($st->fee_plan ?: 'Monthly');
                $planLower = strtolower(trim($planName));
                
                if (in_array($planLower, ['monthly', 'month'])) {
                    $baseFee = (float) ($stStudent->monthly_fee ?: ($st->monthly_fee ?? 0));
                    $formattedPlan = 'Monthly';
                } elseif (in_array($planLower, ['quarterly', 'quarter'])) {
                    $baseFee = (float) ($stStudent->quarterly_fee ?: ($st->quarterly_fee ?? 0));
                    $formattedPlan = 'Quarterly';
                } elseif (in_array($planLower, ['six-monthly', 'six monthly', 'six_monthly', 'six month', '6 months'])) {
                    $baseFee = (float) ($stStudent->six_monthly_fee ?: ($st->six_monthly_fee ?? 0));
                    $formattedPlan = 'Six Monthly';
                } elseif (in_array($planLower, ['annual', 'annually', 'year', 'yearly'])) {
                    $baseFee = (float) ($stStudent->annual_fee ?: ($st->annual_fee ?? 0));
                    $formattedPlan = 'Annual';
                } else {
                    $baseFee = (float) ($stStudent->monthly_fee ?: ($stStudent->quarterly_fee ?: ($stStudent->six_monthly_fee ?: ($stStudent->annual_fee ?: 0))));
                    $formattedPlan = ucfirst($planName);
                }

                $discount = (float) ($stStudent->scholarship_discount ?: ($st->scholarship_discount ?? 0));
                $regFee   = (float) ($stStudent->registration_fee ?: ($st->registration_fee ?? 0));
                $netFee   = max(0, $baseFee - $discount);

                // Unpaid invoices for this student (excluding current invoice being edited)
                $stUnpaid = isset($unpaidInvoices[$st->id]) ? $unpaidInvoices[$st->id]->where('id', '!=', $invoice->id)->map(function($inv) {
                    return [
                        'id' => $inv->id,
                        'invoice_no' => $inv->invoice_no,
                        'fee_month' => $inv->fee_month,
                        'fee_type' => ucwords(str_replace('_', ' ', $inv->fee_type)),
                        'due_date' => $inv->due_date ? $inv->due_date->format('d M Y') : 'N/A',
                        'amount' => (float) $inv->amount,
                        'discount' => (float) $inv->discount,
                        'net_amount' => (float) $inv->net_amount,
                        'paid_amount' => (float) $inv->paid_amount,
                        'due_balance' => (float) $inv->due_balance,
                    ];
                })->values()->toArray() : [];

                // Attached Siblings Array
                $siblingsArr = [];
                foreach ($st->siblings as $sib) {
                    $sibStudent = \App\Models\Student::where('admission_no', $sib->admission_no)->first() ?: $sib;
                    $sPlanName  = $sibStudent->fee_plan ?: 'Monthly';
                    $sPlanLower = strtolower(trim($sPlanName));

                    if (in_array($sPlanLower, ['monthly', 'month'])) {
                        $sBaseFee = (float) ($sibStudent->monthly_fee ?? 0);
                        $sFormattedPlan = 'Monthly';
                    } elseif (in_array($sPlanLower, ['quarterly', 'quarter'])) {
                        $sBaseFee = (float) ($sibStudent->quarterly_fee ?? 0);
                        $sFormattedPlan = 'Quarterly';
                    } elseif (in_array($sPlanLower, ['six-monthly', 'six monthly', 'six_monthly', 'six month', '6 months'])) {
                        $sBaseFee = (float) ($sibStudent->six_monthly_fee ?? 0);
                        $sFormattedPlan = 'Six Monthly';
                    } elseif (in_array($sPlanLower, ['annual', 'annually', 'year', 'yearly'])) {
                        $sBaseFee = (float) ($sibStudent->annual_fee ?? 0);
                        $sFormattedPlan = 'Annual';
                    } else {
                        $sBaseFee = (float) ($sibStudent->monthly_fee ?: ($sibStudent->quarterly_fee ?: ($sibStudent->six_monthly_fee ?: ($sibStudent->annual_fee ?: 0))));
                        $sFormattedPlan = ucfirst($sPlanName);
                    }

                    $sDiscount = (float) ($sibStudent->scholarship_discount ?? 0);
                    $sRegFee   = (float) ($sibStudent->registration_fee ?? 0);
                    $sNetFee   = max(0, $sBaseFee - $sDiscount);

                    $sibAdmId = \App\Models\Admission::where('admission_no', $sib->admission_no)->value('id') ?: $sib->id;
                    $sibUnpaid = isset($unpaidInvoices[$sibAdmId]) ? $unpaidInvoices[$sibAdmId]->where('id', '!=', $invoice->id)->map(function($inv) {
                        return [
                            'id' => $inv->id,
                            'invoice_no' => $inv->invoice_no,
                            'fee_month' => $inv->fee_month,
                            'fee_type' => ucwords(str_replace('_', ' ', $inv->fee_type)),
                            'due_date' => $inv->due_date ? $inv->due_date->format('d M Y') : 'N/A',
                            'amount' => (float) $inv->amount,
                            'discount' => (float) $inv->discount,
                            'net_amount' => (float) $inv->net_amount,
                            'paid_amount' => (float) $inv->paid_amount,
                            'due_balance' => (float) $inv->due_balance,
                        ];
                    })->values()->toArray() : [];

                    $siblingsArr[] = [
                        'id' => $sib->id,
                        'name' => $sib->full_name,
                        'admission_no' => $sib->admission_no,
                        'class_sec' => $sib->class_name . ($sib->section_name ? ' (' . $sib->section_name . ')' : ''),
                        'fee_plan' => $sFormattedPlan,
                        'base_fee' => $sBaseFee,
                        'discount' => $sDiscount,
                        'net_fee' => $sNetFee,
                        'reg_fee' => $sRegFee,
                        'unpaid' => $sibUnpaid,
                    ];
                }
              @endphp
              <option value="{{ $st->id }}" 
                      data-class="{{ $st->class_name }}"
                      data-section="{{ $st->section_name }}"
                      data-father="{{ $st->father_name ?: $st->guardian_name }}"
                      data-no="{{ $st->admission_no }}"
                      data-session="{{ $st->academic_session_id }}"
                      data-fee-plan="{{ $formattedPlan }}"
                      data-base-fee="{{ $baseFee }}"
                      data-discount="{{ $discount }}"
                      data-net-fee="{{ $netFee }}"
                      data-reg-fee="{{ $regFee }}"
                      data-unpaid='@json($stUnpaid)'
                      data-siblings='@json($siblingsArr)'
                      @selected(old('admission_id', $invoice->admission_id) == $st->id)>
                {{ $st->first_name }} {{ $st->last_name }} (ID: {{ $st->admission_no }} | Class: {{ $st->class_name }})
              </option>
            @endforeach
          </select>
          @error('admission_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- 2. STUDENT & FEE PLAN PREVIEW BOX --}}
        <div class="col-md-12">
          <div id="student_info_box" class="p-3 bg-light rounded border d-none">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                <i data-lucide="user-check" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                Student &amp; Fee Plan Profile
              </h6>
              <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-7 fw-semibold" id="st_fee_plan_badge">
                Plan: Monthly
              </span>
            </div>
            <div class="row text-center g-2">
              <div class="col-md-3">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block fs-8">Admission No</small>
                  <span class="fw-bold text-primary" id="st_adm_no">-</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block fs-8">Class &amp; Section</small>
                  <span class="fw-bold text-dark" id="st_class_sec">-</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block fs-8">Father / Guardian</small>
                  <span class="fw-bold text-dark" id="st_father">-</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block fs-8">Plan Base Fee / Net</small>
                  <span class="fw-bold text-success" id="st_fee_display">Rs. 0.00</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- 3. PREVIOUS PENDING DUES / ARREARS BOX --}}
        <div class="col-md-12">
          <div id="previous_dues_box" class="p-3 bg-danger-subtle border border-danger-subtle rounded d-none">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold text-danger mb-0 d-flex align-items-center gap-1.5">
                <i data-lucide="alert-triangle" style="width:1.1rem;height:1.1rem;" class="text-danger"></i>
                Previous Pending Dues / Unpaid Arrears
              </h6>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="include_previous_dues">
                <label class="form-check-label fw-bold text-dark text-sm ms-1" for="include_previous_dues">
                  Include Previous Pending Dues in Voucher Total
                </label>
              </div>
            </div>
            <div id="previous_dues_container"></div>
            <div class="mt-2 pt-2 border-top border-danger-subtle text-end">
              <span class="fw-bold text-danger" id="previous_dues_total_display">Total Previous Pending Dues: Rs. 0.00</span>
            </div>
          </div>
        </div>

        {{-- 4. ATTACHED SIBLING INFO BOX --}}
        <div class="col-md-12">
          <div id="sibling_info_box" class="p-3 bg-info-subtle border border-info-subtle rounded d-none">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                <i data-lucide="users" style="width:1.1rem;height:1.1rem;" class="text-info"></i>
                Attached Sibling(s) Fee Record
              </h6>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="include_sibling_fee">
                <label class="form-check-label fw-bold text-dark text-sm ms-1" for="include_sibling_fee">
                  Include Sibling Fee in Voucher Total
                </label>
              </div>
            </div>
            <div id="siblings_list_container"></div>
            <div class="mt-2 pt-2 border-top border-info-subtle d-flex justify-content-between align-items-center text-sm">
              <span class="text-muted" id="sibling_calculation_breakdown">Student Fee: Rs. 0.00 | Sibling Fee: Rs. 0.00</span>
              <span class="fw-bold text-primary" id="combined_total_display">Combined Total: Rs. 0.00</span>
            </div>
          </div>
        </div>

        {{-- 5. FEE CLASSIFICATION AND DATES --}}
        <div class="col-md-4">
          <label for="fee_type" class="form-label fw-semibold">Fee Type <span class="text-danger">*</span></label>
          <select name="fee_type" id="fee_type" class="form-select @error('fee_type') is-invalid @enderror" required>
            <option value="school_fee" @selected(old('fee_type', $invoice->fee_type) === 'school_fee')>School Fee / Class Fee</option>
            <option value="tuition" @selected(old('fee_type', $invoice->fee_type) === 'tuition')>Tuition Fee (Separate)</option>
            <option value="admission" @selected(old('fee_type', $invoice->fee_type) === 'admission')>Admission / Registration Fee</option>
            <option value="examination" @selected(old('fee_type', $invoice->fee_type) === 'examination')>Examination Fee</option>
            <option value="transport" @selected(old('fee_type', $invoice->fee_type) === 'transport')>Transport Fee</option>
            <option value="hostel" @selected(old('fee_type', $invoice->fee_type) === 'hostel')>Hostel Fee</option>
            <option value="miscellaneous" @selected(old('fee_type', $invoice->fee_type) === 'miscellaneous')>Miscellaneous</option>
          </select>
          @error('fee_type')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="fee_month" class="form-label fw-semibold">Fee Month <span class="text-danger">*</span></label>
          <input type="month" name="fee_month" id="fee_month" class="form-control @error('fee_month') is-invalid @enderror" value="{{ old('fee_month', $invoice->fee_month) }}" required>
          @error('fee_month')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4">
          <label for="due_date" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
          <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" required>
          @error('due_date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- 6. AMOUNTS AND DISCOUNTS --}}
        <div class="col-md-4">
          <label for="amount" class="form-label fw-semibold">Total Fee Amount (PKR) <span class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $invoice->amount) }}" placeholder="0.00" required>
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

        {{-- 7. DETAILED DYNAMIC CALCULATION & BALANCE PREVIEW CARD --}}
        <div class="col-md-12">
          <div class="p-3 bg-light border rounded shadow-sm" id="calcSummaryCard">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
              <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                <i data-lucide="calculator" style="width:1.1rem;height:1.1rem;" class="text-primary"></i>
                Fee Calculation Breakdown &amp; Real-Time Balance Preview
              </h6>
              <span class="badge bg-secondary rounded-pill text-uppercase fs-8" id="calcStatusBadge">{{ strtoupper($invoice->status) }}</span>
            </div>
            
            <div class="row text-center g-2">
              <div class="col-md-3">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block fs-8">1. Gross Total Fee</small>
                  <span class="fw-bold text-dark fs-6" id="calcGrossDisplay">Rs. {{ number_format($invoice->amount, 2) }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block fs-8">2. Less: Discount</small>
                  <span class="fw-bold text-success fs-6" id="calcDiscountDisplay">- Rs. {{ number_format($invoice->discount, 2) }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block fs-8">3. Net Payable Fee</small>
                  <span class="fw-bold text-primary fs-6" id="calcNetDisplay">Rs. {{ number_format($invoice->net_amount, 2) }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-white p-2 rounded border shadow-sm">
                  <small class="text-muted d-block fs-8">4. Paid Amount Received</small>
                  <span class="fw-bold text-emerald-600 fs-6" style="color: #059669;" id="calcPaidDisplay">Rs. {{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
              </div>
            </div>

            <div class="mt-3 p-2.5 rounded border d-flex justify-content-between align-items-center flex-wrap gap-2" id="calcBalanceBar" style="background-color: #f8fafc;">
              <div>
                <span class="fw-bold fs-7 d-block" id="calcBalanceTitle">Remaining Outstanding Due Balance:</span>
                <small class="text-muted fs-8" id="calcBalanceSubtitle">Formula: Net Payable Amount - Paid Amount Received</small>
              </div>
              <div class="text-end d-flex align-items-center gap-2">
                <span class="fw-extrabold fs-4" id="calcBalanceDisplay">Rs. {{ number_format($invoice->due_balance, 2) }}</span>
                <span class="badge fs-8 px-2.5 py-1.5 rounded-pill" id="calcBalanceBadge">{{ strtoupper($invoice->status) }}</span>
              </div>
            </div>
          </div>
        </div>

        {{-- 8. PAYMENT METHOD & STATUS --}}
        <div class="col-md-4">
          <label for="payment_method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
          <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
            <option value="cash" @selected(old('payment_method', $invoice->payment_method ?: 'cash') === 'cash')>Cash</option>
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
          <label for="notes" class="form-label fw-semibold">Remarks / Voucher Notes</label>
          <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Additional details or remarks about this fee voucher">{{ old('notes', $invoice->notes) }}</textarea>
          @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 text-end mt-4">
          <a href="{{ route('fee-management.index') }}" class="btn btn-secondary me-2">Cancel</a>
          <button type="submit" class="btn text-white px-4 fw-bold" style="background-color: #3d1a06; border-color: #3d1a06;">
            <i data-lucide="check-circle" style="width:1rem;height:1rem;" class="me-1"></i> Update Fee Invoice
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
      const $studentSelect = $('#admission_id');
      const classFilterSelect = document.getElementById('class_filter');
      const sessionSelect = document.getElementById('academic_session_id');

      const infoBox = document.getElementById('student_info_box');
      const stAdmNo = document.getElementById('st_adm_no');
      const stClassSec = document.getElementById('st_class_sec');
      const stFather = document.getElementById('st_father');
      const stFeePlanBadge = document.getElementById('st_fee_plan_badge');
      const stFeeDisplay = document.getElementById('st_fee_display');

      const previousDuesBox = document.getElementById('previous_dues_box');
      const previousDuesContainer = document.getElementById('previous_dues_container');
      const includePreviousDuesCheckbox = document.getElementById('include_previous_dues');
      const previousDuesTotalDisplay = document.getElementById('previous_dues_total_display');

      const siblingInfoBox = document.getElementById('sibling_info_box');
      const siblingsListContainer = document.getElementById('siblings_list_container');
      const includeSiblingFeeCheckbox = document.getElementById('include_sibling_fee');
      const siblingBreakdownText = document.getElementById('sibling_calculation_breakdown');
      const combinedTotalDisplay = document.getElementById('combined_total_display');

      const feeTypeSelect = document.getElementById('fee_type');
      const amountInput = document.getElementById('amount');
      const discountInput = document.getElementById('discount');
      const paidAmountInput = document.getElementById('paid_amount');
      const statusSelect = document.getElementById('status');

      // Calculation summary DOM elements
      const calcStatusBadge = document.getElementById('calcStatusBadge');
      const calcGrossDisplay = document.getElementById('calcGrossDisplay');
      const calcDiscountDisplay = document.getElementById('calcDiscountDisplay');
      const calcNetDisplay = document.getElementById('calcNetDisplay');
      const calcPaidDisplay = document.getElementById('calcPaidDisplay');
      const calcBalanceBar = document.getElementById('calcBalanceBar');
      const calcBalanceTitle = document.getElementById('calcBalanceTitle');
      const calcBalanceSubtitle = document.getElementById('calcBalanceSubtitle');
      const calcBalanceDisplay = document.getElementById('calcBalanceDisplay');
      const calcBalanceBadge = document.getElementById('calcBalanceBadge');

      let currentStudentUnpaid = [];
      let currentStudentSiblings = [];
      let isManualAmountChange = true;

      // Initialize Select2
      $studentSelect.select2({
        theme: 'bootstrap-5',
        placeholder: '-- Search Student by Name or Admission No --',
        allowClear: true,
        width: '100%'
      });

      // Class Filter Logic
      $('#class_filter').on('change', function () {
        const selectedClass = $(this).val();
        $studentSelect.find('option').each(function () {
          const optClass = $(this).data('class');
          if (!selectedClass || optClass === selectedClass || $(this).val() === '') {
            $(this).prop('disabled', false).show();
          } else {
            $(this).prop('disabled', true).hide();
          }
        });
        if ($studentSelect.val()) {
          const selectedOpt = $studentSelect.find('option:selected');
          if (selectedOpt.prop('disabled')) {
            $studentSelect.val('').trigger('change');
          }
        }
        $studentSelect.select2({
          theme: 'bootstrap-5',
          placeholder: '-- Search Student by Name or Admission No --',
          allowClear: true,
          width: '100%'
        });
      });

      // Student Selection Change
      $studentSelect.on('change', function () {
        const selectedOption = $(this).find('option:selected');
        const admId = $(this).val();

        if (!admId) {
          infoBox.classList.add('d-none');
          previousDuesBox.classList.add('d-none');
          siblingInfoBox.classList.add('d-none');
          currentStudentUnpaid = [];
          currentStudentSiblings = [];
          updateCalculations();
          return;
        }

        const admNo = selectedOption.data('no') || '-';
        const className = selectedOption.data('class') || '-';
        const sectionName = selectedOption.data('section') || '';
        const fatherName = selectedOption.data('father') || '-';
        const session = selectedOption.data('session');
        const feePlan = selectedOption.data('fee-plan') || 'Monthly';
        const baseFee = parseFloat(selectedOption.data('base-fee')) || 0;
        const discount = parseFloat(selectedOption.data('discount')) || 0;
        const netFee = parseFloat(selectedOption.data('net-fee')) || 0;

        currentStudentUnpaid = selectedOption.data('unpaid') || [];
        currentStudentSiblings = selectedOption.data('siblings') || [];

        // Update Student Info Box
        stAdmNo.textContent = admNo;
        stClassSec.textContent = className + (sectionName ? ` (${sectionName})` : '');
        stFather.textContent = fatherName;
        stFeePlanBadge.textContent = `Plan: ${feePlan}`;
        stFeeDisplay.textContent = `Rs. ${netFee.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        infoBox.classList.remove('d-none');

        // Render Previous Pending Dues
        if (currentStudentUnpaid.length > 0) {
          let duesHtml = '<div class="table-responsive"><table class="table table-sm table-bordered bg-white mb-0 fs-8"><thead><tr class="table-light"><th>Voucher #</th><th>Month</th><th>Type</th><th>Due Date</th><th>Amount</th><th>Paid</th><th>Pending Due</th></tr></thead><tbody>';
          let totalPrevDues = 0;
          currentStudentUnpaid.forEach(inv => {
            totalPrevDues += parseFloat(inv.due_balance);
            duesHtml += `<tr><td class="fw-bold font-monospace">${inv.invoice_no}</td><td>${inv.fee_month}</td><td>${inv.fee_type}</td><td class="text-danger">${inv.due_date}</td><td>Rs. ${parseFloat(inv.net_amount).toFixed(2)}</td><td class="text-success">Rs. ${parseFloat(inv.paid_amount).toFixed(2)}</td><td class="fw-bold text-danger">Rs. ${parseFloat(inv.due_balance).toFixed(2)}</td></tr>`;
          });
          duesHtml += '</tbody></table></div>';
          previousDuesContainer.innerHTML = duesHtml;
          previousDuesTotalDisplay.textContent = `Total Previous Pending Dues: Rs. ${totalPrevDues.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
          previousDuesBox.classList.remove('d-none');
        } else {
          previousDuesBox.classList.add('d-none');
        }

        // Render Sibling Fee Info
        if (currentStudentSiblings.length > 0) {
          let sibHtml = '<div class="table-responsive"><table class="table table-sm table-bordered bg-white mb-0 fs-8"><thead><tr class="table-light"><th>Sibling Name</th><th>Adm No</th><th>Class</th><th>Fee Plan</th><th>Net Fee</th><th>Unpaid Arrears</th></tr></thead><tbody>';
          currentStudentSiblings.forEach(sib => {
            let sibUnpaidTotal = 0;
            if (sib.unpaid && sib.unpaid.length > 0) {
              sib.unpaid.forEach(u => sibUnpaidTotal += parseFloat(u.due_balance));
            }
            sibHtml += `<tr><td class="fw-bold text-dark">${sib.name}</td><td>${sib.admission_no}</td><td>${sib.class_sec}</td><td><span class="badge bg-secondary-subtle text-secondary">${sib.fee_plan}</span></td><td class="fw-bold text-primary">Rs. ${parseFloat(sib.net_fee).toFixed(2)}</td><td class="${sibUnpaidTotal > 0 ? 'fw-bold text-danger' : 'text-muted'}">${sibUnpaidTotal > 0 ? 'Rs. ' + sibUnpaidTotal.toFixed(2) : 'No Arrears'}</td></tr>`;
          });
          sibHtml += '</tbody></table></div>';
          siblingsListContainer.innerHTML = sibHtml;
          siblingInfoBox.classList.remove('d-none');
        } else {
          siblingInfoBox.classList.add('d-none');
        }

        if (sessionSelect && session) {
          $(sessionSelect).val(session);
        }

        updateCalculations();
      });

      // Recalculate Summary Card
      function updateCalculations() {
        const grossAmount = parseFloat(amountInput.value) || 0;
        const discountAmount = parseFloat(discountInput.value) || 0;
        const paidAmount = parseFloat(paidAmountInput.value) || 0;

        const netPayable = Math.max(0, grossAmount - discountAmount);
        const dueBalance = Math.max(0, netPayable - paidAmount);

        let calculatedStatus = statusSelect.value;
        if (statusSelect.value !== 'cancelled') {
          if (paidAmount >= netPayable && netPayable > 0) {
            calculatedStatus = 'paid';
          } else if (paidAmount > 0) {
            calculatedStatus = 'partial';
          } else {
            calculatedStatus = 'unpaid';
          }
        }

        calcGrossDisplay.textContent = `Rs. ${grossAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        calcDiscountDisplay.textContent = `- Rs. ${discountAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        calcNetDisplay.textContent = `Rs. ${netPayable.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        calcPaidDisplay.textContent = `Rs. ${paidAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        calcBalanceDisplay.textContent = `Rs. ${dueBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

        calcStatusBadge.className = 'badge rounded-pill text-uppercase fs-8 ';
        calcBalanceBadge.className = 'badge fs-8 px-2.5 py-1.5 rounded-pill ';

        if (calculatedStatus === 'paid') {
          calcStatusBadge.classList.add('bg-success', 'text-white');
          calcBalanceBadge.classList.add('bg-success', 'text-white');
          calcStatusBadge.textContent = 'Fully Paid';
          calcBalanceBadge.textContent = 'Paid';
          calcBalanceBar.style.backgroundColor = '#f0fdf4';
          calcBalanceBar.style.borderColor = '#bbf7d0';
          calcBalanceTitle.className = 'fw-bold fs-7 d-block text-success';
          calcBalanceTitle.textContent = 'Account Fully Settled (No Due Balance)';
        } else if (calculatedStatus === 'partial') {
          calcStatusBadge.classList.add('bg-warning', 'text-dark');
          calcBalanceBadge.classList.add('bg-warning', 'text-dark');
          calcStatusBadge.textContent = 'Partial Payment';
          calcBalanceBadge.textContent = 'Partial';
          calcBalanceBar.style.backgroundColor = '#fffbeb';
          calcBalanceBar.style.borderColor = '#fef08a';
          calcBalanceTitle.className = 'fw-bold fs-7 d-block text-warning-emphasis';
          calcBalanceTitle.textContent = 'Partial Payment Received - Remaining Due Balance:';
        } else if (calculatedStatus === 'cancelled') {
          calcStatusBadge.classList.add('bg-secondary', 'text-white');
          calcBalanceBadge.classList.add('bg-secondary', 'text-white');
          calcStatusBadge.textContent = 'Cancelled';
          calcBalanceBadge.textContent = 'Cancelled';
          calcBalanceBar.style.backgroundColor = '#f8fafc';
          calcBalanceBar.style.borderColor = '#e2e8f0';
          calcBalanceTitle.className = 'fw-bold fs-7 d-block text-secondary';
          calcBalanceTitle.textContent = 'Voucher Cancelled';
        } else {
          calcStatusBadge.classList.add('bg-danger', 'text-white');
          calcBalanceBadge.classList.add('bg-danger', 'text-white');
          calcStatusBadge.textContent = 'Unpaid';
          calcBalanceBadge.textContent = 'Unpaid';
          calcBalanceBar.style.backgroundColor = '#fef2f2';
          calcBalanceBar.style.borderColor = '#fecaca';
          calcBalanceTitle.className = 'fw-bold fs-7 d-block text-danger';
          calcBalanceTitle.textContent = 'Remaining Outstanding Due Balance:';
        }
      }

      // Input Event Listeners
      [amountInput, discountInput, paidAmountInput].forEach(input => {
        input.addEventListener('input', function() {
          isManualAmountChange = true;
          updateCalculations();
        });
      });

      statusSelect.addEventListener('change', updateCalculations);

      // Trigger student change on load for edit mode
      if ($studentSelect.val()) {
        $studentSelect.trigger('change');
      }

      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    });
  </script>
@endpush
@endsection
