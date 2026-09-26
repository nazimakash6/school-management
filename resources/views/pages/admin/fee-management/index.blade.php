@extends('layouts.app')

@section('title', 'Fee Management')

@section('content')
  {{-- PROFESSIONAL HEADER CARD WITH BREADCRUMB & ACTIONS BELOW TAGLINE --}}
  <div class="content-header mb-4 flex-column align-items-start gap-3">
    <div class="w-100">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1.5 fs-7">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted"><i data-lucide="home" style="width:0.875rem;height:0.875rem;" class="me-1"></i>Dashboard</a></li>
          <li class="breadcrumb-item text-muted">HR & Finance</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Fee Management</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2.5">
        <div class="p-2.5 rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
          <i data-lucide="wallet" style="width:1.5rem;height:1.5rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark tracking-tight">Student Fee Management & Invoicing</h3>
          <p class="text-muted mb-0 fs-7">Track student fee collections, vouchers, discounts, and outstanding dues</p>
        </div>
      </div>
    </div>
    <div class="content-header-actions d-flex gap-2 flex-wrap align-items-center pt-2 border-top w-100">
      <a href="{{ route('fee-management.statement') }}" class="btn btn-outline-teal btn-sm text-teal-700 border-teal-600">
        <i data-lucide="file-spreadsheet" style="width:1rem;height:1rem;" class="me-1"></i> Fee Statement
      </a>
      <a href="{{ route('fee-management.trash') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="archive" style="width:1rem;height:1rem;" class="me-1"></i> Trash ({{ $trashCount }})
      </a>
      <a href="{{ route('collect-payment.create') }}" class="btn btn-success btn-sm ms-auto d-inline-flex align-items-center gap-1.5" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none;">
        <i data-lucide="banknote" style="width:1rem;height:1rem;"></i> Collect Payment
      </a>
      <a href="{{ route('fee-management.create') }}" class="btn btn-primary btn-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
        <i data-lucide="plus" style="width:1rem;height:1rem;" class="me-1"></i> Create Fee Invoice
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Statistics Cards -->
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Fees Collected</span>
            <h3 class="fw-bold text-success mb-0 mt-1">Rs. {{ number_format($totalCollected, 2) }}</h3>
          </div>
          <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="wallet" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Outstanding Dues</span>
            <h3 class="fw-bold text-danger mb-0 mt-1">Rs. {{ number_format($totalDues, 2) }}</h3>
          </div>
          <div class="rounded-circle bg-danger-subtle text-danger p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="alert-circle" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Paid Invoices</span>
            <h3 class="fw-bold text-primary mb-0 mt-1">{{ $paidCount }}</h3>
          </div>
          <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="check-circle-2" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex align-items-center justify-content-between">
          <div>
            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Unpaid / Partial</span>
            <h3 class="fw-bold text-warning mb-0 mt-1">{{ $unpaidCount + $partialCount }}</h3>
          </div>
          <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i data-lucide="clock" style="width: 1.5rem; height: 1.5rem;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- TWO TABS FOR RECORD SEPARATION --}}
  <ul class="nav nav-tabs nav-tabs-bordered mb-4 bg-white p-2 rounded-3 border shadow-sm" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link fs-6 {{ request('tab') !== 'payments' ? 'active fw-bold text-primary' : 'text-muted' }}" href="{{ route('fee-management.index', array_merge(request()->except('tab'), ['tab' => 'invoices'])) }}">
        <i data-lucide="file-text" style="width:1.1rem;height:1.1rem;" class="me-1.5"></i>
        Fee Invoices Record ({{ $invoices->total() }})
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link fs-6 {{ request('tab') === 'payments' ? 'active fw-bold text-success' : 'text-muted' }}" href="{{ route('fee-management.index', array_merge(request()->except('tab'), ['tab' => 'payments'])) }}">
        <i data-lucide="receipt" style="width:1.1rem;height:1.1rem;" class="me-1.5"></i>
        Collect Payments Record ({{ $paymentsCount }})
      </a>
    </li>
  </ul>

  @if(request('tab') === 'payments')
    {{-- TAB 2: COLLECT PAYMENTS RECORD TABLE --}}
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
          <i data-lucide="receipt" class="text-success" style="width:1.2rem;height:1.2rem;"></i>
          Payment Receipts & Transaction History
        </h5>
        <a href="{{ route('collect-payment.create') }}" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
          <i data-lucide="plus" style="width:0.875rem;height:0.875rem;"></i> New Payment Collection
        </a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-3">Receipt No</th>
              <th>Invoice No</th>
              <th>Student Name</th>
              <th>Payment Date</th>
              <th>Payment Method</th>
              <th>Amount Collected</th>
              <th>Notes / Remarks</th>
              <th class="text-end pe-3">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $pm)
              <tr>
                <td class="ps-3 fw-bold text-primary">
                  {{ $pm->receipt_no }}
                </td>
                <td>
                  @if($pm->feeManagement)
                    <a href="{{ route('fee-management.show', $pm->fee_management_id) }}" class="fw-semibold text-decoration-none">
                      {{ $pm->feeManagement->invoice_no }}
                    </a>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  <div class="fw-semibold text-dark">
                    {{ $pm->admission ? ($pm->admission->first_name . ' ' . $pm->admission->last_name) : 'Student' }}
                  </div>
                  <small class="text-muted">
                    {{ $pm->admission ? ($pm->admission->admission_no . ' • Class: ' . $pm->admission->class_name) : '' }}
                  </small>
                </td>
                <td class="fs-7 text-dark fw-semibold">
                  {{ $pm->payment_date ? $pm->payment_date->format('M d, Y') : 'N/A' }}
                </td>
                <td>
                  <span class="badge bg-secondary-subtle text-secondary text-capitalize px-2 py-1">
                    {{ str_replace('_', ' ', $pm->payment_method) }}
                  </span>
                </td>
                <td class="fw-bold text-success">
                  Rs. {{ number_format($pm->amount, 2) }}
                </td>
                <td class="fs-7 text-muted">
                  {{ $pm->note ?: '—' }}
                </td>
                <td class="text-end pe-3">
                  <a href="{{ route('fee-management.payment-receipt', $pm->id) }}" target="_blank" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1" title="Print Receipt">
                    <i data-lucide="printer" style="width:0.875rem;height:0.875rem;"></i> Receipt
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                  <i data-lucide="info" class="mb-2" style="width:2rem;height:2rem;"></i>
                  <p class="mb-0">No collected payment receipts found in the system yet.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($payments->hasPages())
        <div class="card-footer bg-white py-2 border-0">
          {{ $payments->links() }}
        </div>
      @endif
    </div>
  @else
    {{-- TAB 1: FEE INVOICES RECORD TABLE --}}
    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body py-3">
        <form method="GET" action="{{ route('fee-management.index') }}" class="row g-2 align-items-center">
          <input type="hidden" name="tab" value="invoices">
          <div class="col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search invoice, student or ID..." value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <select name="class_name" class="form-select form-select-sm">
              <option value="all">All Classes</option>
              @foreach($classesList as $c)
                <option value="{{ $c }}" {{ request('class_name') == $c ? 'selected' : '' }}>{{ $c }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <select name="fee_type" class="form-select form-select-sm">
              <option value="all">All Fee Types</option>
              <option value="school_fee" {{ request('fee_type') == 'school_fee' ? 'selected' : '' }}>School Fee</option>
              <option value="tuition" {{ request('fee_type') == 'tuition' ? 'selected' : '' }}>Tuition Fee</option>
              <option value="admission" {{ request('fee_type') == 'admission' ? 'selected' : '' }}>Admission Fee</option>
              <option value="examination" {{ request('fee_type') == 'examination' ? 'selected' : '' }}>Examination Fee</option>
              <option value="transport" {{ request('fee_type') == 'transport' ? 'selected' : '' }}>Transport Fee</option>
              <option value="hostel" {{ request('fee_type') == 'hostel' ? 'selected' : '' }}>Hostel Fee</option>
              <option value="miscellaneous" {{ request('fee_type') == 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
            </select>
          </div>
          <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
              <option value="all">All Status</option>
              <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
              <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
              <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
            <a href="{{ route('fee-management.index') }}" class="btn btn-light btn-sm">Reset</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Invoice No</th>
              <th>Student</th>
              <th>Fee Details</th>
              <th>Amount</th>
              <th>Paid</th>
              <th>Due Balance</th>
              <th>Due Date</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $inv)
              <tr>
                <td>
                  <span class="fw-bold text-primary">{{ $inv->invoice_no }}</span>
                </td>
                <td>
                  <div class="fw-semibold text-dark">
                    {{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'N/A' }}
                  </div>
                  <small class="text-muted">
                    {{ $inv->admission ? ($inv->admission->admission_no . ' • Class: ' . $inv->admission->class_name) : '' }}
                  </small>
                </td>
                <td>
                  <span class="badge bg-light text-dark border text-capitalize me-1">{{ str_replace('_', ' ', $inv->fee_type) }}</span>
                  <small class="text-muted d-block">{{ $inv->fee_month }}</small>
                </td>
                <td>
                  <span class="fw-semibold">Rs. {{ number_format($inv->net_amount, 2) }}</span>
                  @if($inv->discount > 0)
                    <small class="text-success d-block">Disc: Rs. {{ number_format($inv->discount, 2) }}</small>
                  @endif
                </td>
                <td class="text-success fw-semibold">
                  Rs. {{ number_format($inv->paid_amount, 2) }}
                </td>
                <td class="fw-bold {{ $inv->due_balance > 0 ? 'text-danger' : 'text-muted' }}">
                  Rs. {{ number_format($inv->due_balance, 2) }}
                </td>
                <td>
                  <small class="{{ $inv->due_date && $inv->due_date->isPast() && $inv->due_balance > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                    {{ $inv->due_date ? $inv->due_date->format('M d, Y') : 'N/A' }}
                  </small>
                </td>
                <td>
                  <span class="badge {{ $inv->status_badge_class }} text-capitalize px-2 py-1">
                    {{ $inv->status }}
                  </span>
                </td>
                <td class="text-end text-nowrap">
                  <a href="{{ route('fee-management.show', $inv->id) }}" class="btn btn-sm btn-outline-info me-1" title="View / Print Voucher">
                    <i data-lucide="file-text" style="width:0.875rem;height:0.875rem;"></i>
                  </a>
                  <a href="{{ route('fee-management.edit', $inv->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Invoice">
                    <i data-lucide="edit-2" style="width:0.875rem;height:0.875rem;"></i>
                  </a>
                  <form action="{{ route('fee-management.destroy', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Move this invoice to trash?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Trash Invoice">
                      <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                  <i data-lucide="info" class="mb-2" style="width:2rem;height:2rem;"></i>
                  <p class="mb-0">No fee invoices found matching your criteria.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($invoices->hasPages())
        <div class="card-footer bg-white py-2 border-0">
          {{ $invoices->links() }}
        </div>
      @endif
    </div>
  @endif
@endsection
