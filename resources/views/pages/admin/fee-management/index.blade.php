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
      <a href="{{ route('fee-management.create') }}" class="btn btn-primary btn-sm ms-auto" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
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

  <!-- Filter Section -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
      <form method="GET" action="{{ route('fee-management.index') }}" class="row g-2 align-items-center">
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
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary w-100">
            <i data-lucide="filter" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Filter
          </button>
          <a href="{{ route('fee-management.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                @php
                  $stName = $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'Student';
                  $stEmail = $inv->admission ? ($inv->admission->father_email ?? ($inv->admission->guardian_email ?? ($inv->admission->email ?? ''))) : '';
                  $amtFormatted = number_format($inv->due_balance > 0 ? $inv->due_balance : $inv->net_amount, 2);
                  $dueDateFormatted = $inv->due_date ? $inv->due_date->format('M d, Y') : 'Due';
                  $feeSubject = "Fee Reminder: Rs. {$amtFormatted} due for {$stName} (Invoice #{$inv->invoice_no})";
                  $feeMsg = "Dear Parent,\n\nThis is a friendly reminder that a fee payment of Rs. {$amtFormatted} for {$stName} (Invoice: {$inv->invoice_no}) is due on {$dueDateFormatted}.\n\nKindly deposit the payment at your earliest convenience to avoid late charges.\n\nThank you,\nAccounts Department";
                @endphp
                <button type="button" class="btn btn-sm btn-outline-primary me-1" title="Send Email Fee Reminder"
                        onclick="openEmailModal({
                          name: @js($stName),
                          email: @js($stEmail),
                          type: 'Parent',
                          template: 'fee',
                          subject: @js($feeSubject),
                          message: @js($feeMsg)
                        })">
                  <i data-lucide="mail" style="width:13px;height:13px;"></i>
                </button>
                <a href="{{ route('fee-management.show', $inv->id) }}" class="btn btn-sm btn-outline-info me-1" title="View / Print Voucher">
                  <i data-lucide="file-text" style="width:0.875rem;height:0.875rem;"></i>
                </a>
                @if($inv->due_balance > 0 && $inv->status !== 'cancelled')
                  <button type="button" class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#payModal{{ $inv->id }}" title="Collect Payment">
                    <i data-lucide="banknote" style="width:0.875rem;height:0.875rem;"></i>
                  </button>
                @endif
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

                <!-- Quick Payment Modal -->
                @if($inv->due_balance > 0 && $inv->status !== 'cancelled')
                  <div class="modal fade text-start" id="payModal{{ $inv->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <form action="{{ route('fee-management.payment', $inv->id) }}" method="POST">
                          @csrf
                          <div class="modal-header">
                            <h5 class="modal-title fs-6 fw-bold">Collect Fee Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="p-3 bg-light rounded border mb-3">
                              <div class="d-flex justify-content-between text-muted fs-7">
                                <span>Invoice No:</span>
                                <span class="fw-bold text-dark">{{ $inv->invoice_no }}</span>
                              </div>
                              <div class="d-flex justify-content-between text-muted fs-7">
                                <span>Student:</span>
                                <span class="fw-semibold text-dark">{{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : '' }}</span>
                              </div>
                              <div class="d-flex justify-content-between text-muted fs-7">
                                <span>Net Total Amount:</span>
                                <span class="fw-semibold text-dark">Rs. {{ number_format($inv->net_amount, 2) }}</span>
                              </div>
                              <div class="d-flex justify-content-between fs-7 mt-1 border-top pt-1">
                                <span class="fw-bold">Due Balance:</span>
                                <span class="fw-bold text-danger">Rs. {{ number_format($inv->due_balance, 2) }}</span>
                              </div>
                            </div>

                            <div class="mb-3">
                              <label class="form-label fw-semibold">Payment Amount (PKR) <span class="text-danger">*</span></label>
                              <input type="number" step="0.01" min="1" max="{{ $inv->due_balance }}" name="payment_amount" class="form-control" value="{{ $inv->due_balance }}" required>
                              <small class="text-muted">Max payable: Rs. {{ number_format($inv->due_balance, 2) }}</small>
                            </div>

                            <div class="row g-2 mb-3">
                              <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                  <option value="cash">Cash</option>
                                  <option value="bank_transfer">Bank Transfer</option>
                                  <option value="online">Online Payment</option>
                                  <option value="cheque">Cheque</option>
                                </select>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm">Save Payment</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                @endif
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
@endsection
