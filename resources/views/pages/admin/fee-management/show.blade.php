@extends('layouts.app')

@section('title', 'Fee Invoice - ' . $invoice->invoice_no)

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/fee-invoice-detail.css') }}?v={{ time() }}">
@endpush

@section('content')
  <div class="admission-page-wrapper py-4 px-3 px-md-4 no-print">
    <div class="container-fluid max-width-1600">

      {{-- BREADCRUMB & HEADER ACTIONS --}}
      <div class="profile-header-card fade-up mb-4 p-3 bg-white rounded-3 border shadow-sm">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-1 fs-7">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('fee-management.index') }}" class="text-decoration-none text-muted">Fee Management</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Invoice Details</li>
              </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">Student Fee Voucher / Challan</h4>
          </div>
          <div class="d-flex gap-2 flex-wrap align-items-center">
            <a href="{{ route('fee-management.print', $invoice->id) }}" target="_blank" class="btn btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-bold" style="background-color: #c7ad8d; border-color: #c7ad8d; color: #3d1a06;">
              <i data-lucide="printer" style="width:1rem;height:1rem;"></i> Print Voucher
            </a>
            <a href="{{ route('fee-management.collect-payment', $invoice->id) }}" class="btn btn-success text-white btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-bold" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none;">
              <i data-lucide="banknote" style="width:1rem;height:1rem;"></i> Collect Payment
            </a>
            <a href="{{ route('fee-management.edit', $invoice) }}" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1.5">
              <i data-lucide="pencil" style="width:1rem;height:1rem;"></i> Edit
            </a>
            <a href="{{ route('fee-management.index') }}" class="btn btn-ghost btn-sm px-3 d-inline-flex align-items-center gap-1.5">
              <i data-lucide="arrow-left" style="width:1rem;height:1rem;"></i> Back to List
            </a>
          </div>
        </div>
      </div>

      {{-- HERO INVOICE CARD --}}
      <div class="invoice-hero-card d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="invoice-badge-no"><i data-lucide="receipt" style="width:0.875rem;height:0.875rem;" class="me-1"></i> INVOICE • {{ $invoice->invoice_no }}</span>
            @if ($invoice->status === 'paid')
              <span class="badge text-white px-2.5 py-1 rounded-pill fs-7" style="background-color: #3d1a06;"><i data-lucide="check-circle-2" style="width:0.8rem;height:0.8rem;" class="me-1"></i> PAID</span>
            @elseif ($invoice->status === 'partial')
              <span class="badge bg-amber-500 text-white px-2.5 py-1 rounded-pill fs-7"><i data-lucide="pie-chart" style="width:0.8rem;height:0.8rem;" class="me-1"></i> PARTIAL PAYMENT</span>
            @elseif ($invoice->status === 'unpaid')
              <span class="badge bg-rose-500 text-white px-2.5 py-1 rounded-pill fs-7"><i data-lucide="alert-circle" style="width:0.8rem;height:0.8rem;" class="me-1"></i> UNPAID</span>
            @else
              <span class="badge bg-slate-500 text-white px-2.5 py-1 rounded-pill fs-7"><i data-lucide="x-circle" style="width:0.8rem;height:0.8rem;" class="me-1"></i> CANCELLED</span>
            @endif
          </div>
          <h2 class="fw-bold mb-1 text-white">
            {{ $invoice->admission ? ($invoice->admission->first_name . ' ' . $invoice->admission->last_name) : 'Student Record' }}
          </h2>
          <p class="text-slate-300 mb-0 fs-7">
            Adm No: <span class="fw-semibold text-white me-3">{{ $invoice->admission ? $invoice->admission->admission_no : '—' }}</span>
            Class: <span class="fw-semibold text-white me-3">{{ $invoice->admission ? ($invoice->admission->class_name . ' ' . ($invoice->admission->section_name ? '(' . $invoice->admission->section_name . ')' : '')) : '—' }}</span>
            Month: <span class="fw-semibold text-white">{{ $invoice->fee_month }}</span>
          </p>
        </div>
        <div class="text-end">
          <div class="text-slate-400 text-xs text-uppercase fw-semibold mb-1">Outstanding Balance Due</div>
          <div class="invoice-hero-amount">Rs. {{ number_format($invoice->due_balance, 2) }}</div>
          <div class="text-slate-300 text-xs mt-1">Net Payable: Rs. {{ number_format($invoice->net_amount, 2) }}</div>
        </div>
      </div>

      {{-- SCREEN INFORMATION CARDS GRID --}}
      <div class="row g-4 mb-4">
        {{-- Student Profile --}}
        <div class="col-lg-6">
          <div class="invoice-info-card">
            <div class="invoice-info-card-title d-flex align-items-center justify-content-between">
              <span><i data-lucide="user-check" style="width:1rem;height:1rem;" class="me-1.5 text-primary"></i> Student Profile</span>
              <span class="badge bg-light text-muted border fw-normal fs-7">Academic Information</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Student Full Name</span>
              <span class="invoice-detail-value">{{ $invoice->admission ? ($invoice->admission->first_name . ' ' . $invoice->admission->last_name) : 'N/A' }}</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Father / Guardian</span>
              <span class="invoice-detail-value">{{ $invoice->admission ? ($invoice->admission->father_name ?: $invoice->admission->guardian_name) : 'N/A' }}</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Admission Number</span>
              <span class="invoice-detail-value"><code class="bg-light px-2 py-0.5 rounded text-dark fs-7">{{ $invoice->admission ? $invoice->admission->admission_no : 'N/A' }}</code></span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Class & Section</span>
              <span class="invoice-detail-value">{{ $invoice->admission ? ($invoice->admission->class_name . ' ' . ($invoice->admission->section_name ? '(' . $invoice->admission->section_name . ')' : '')) : 'N/A' }}</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Contact Phone</span>
              <span class="invoice-detail-value">{{ $invoice->admission ? ($invoice->admission->mobile_number ?: 'N/A') : 'N/A' }}</span>
            </div>
          </div>
        </div>

        {{-- Invoice & Payment Summary --}}
        <div class="col-lg-6">
          <div class="invoice-info-card">
            <div class="invoice-info-card-title d-flex align-items-center justify-content-between">
              <span><i data-lucide="file-text" style="width:1rem;height:1rem;" class="me-1.5 text-primary"></i> Voucher & Billing Details</span>
              <span class="badge bg-light text-muted border fw-normal fs-7">Challan Meta</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Voucher / Invoice No</span>
              <span class="invoice-detail-value text-primary font-monospace">{{ $invoice->invoice_no }}</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Fee Classification</span>
              <span class="invoice-detail-value text-capitalize">{{ str_replace('_', ' ', $invoice->fee_type) }}</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Billing Session / Month</span>
              <span class="invoice-detail-value">{{ $invoice->fee_month }}</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Due Date</span>
              <span class="invoice-detail-value text-danger">{{ $invoice->due_date ? $invoice->due_date->format('F d, Y') : 'N/A' }}</span>
            </div>
            <div class="invoice-detail-row">
              <span class="invoice-detail-label">Payment Date / Status</span>
              <span class="invoice-detail-value">
                @if($invoice->payment_date)
                  {{ $invoice->payment_date->format('d M, Y') }} ({{ str_replace('_', ' ', $invoice->payment_method) }})
                @else
                  <span class="text-muted fw-normal">Pending Settlement</span>
                @endif
              </span>
            </div>
          </div>
        </div>
      </div>

      {{-- FEE BREAKDOWN TABLE --}}
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i data-lucide="calculator" style="width:1.1rem;height:1.1rem;" class="text-primary"></i> Fee Structure & Outstanding Ledger
          </h6>
          <span class="text-muted fs-7">Currency: PKR</span>
        </div>
        <div class="table-responsive">
          <table class="table table-invoice-breakdown align-middle mb-0">
            <thead>
              <tr>
                <th>Description</th>
                <th>Category</th>
                <th class="text-end" style="width: 30%;">Amount (PKR)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="fw-medium text-dark"><i data-lucide="book-open" style="width:1rem;height:1rem;" class="me-2 text-primary"></i> Base Fee ({{ str_replace('_', ' ', $invoice->fee_type) }})</td>
                <td><span class="badge bg-light text-dark border">Standard Fee</span></td>
                <td class="text-end fw-semibold text-dark">Rs. {{ number_format($invoice->amount, 2) }}</td>
              </tr>
              @if($invoice->discount > 0)
                <tr>
                  <td class="fw-medium" style="color: #6b4d3b;"><i data-lucide="tag" style="width:1rem;height:1rem;" class="me-2 text-secondary"></i> Less: Scholarship / Concession Discount</td>
                  <td><span class="badge border" style="background-color: #f5eee0; color: #5c2c10; border-color: #c7ad8d !important;">Discount</span></td>
                  <td class="text-end fw-bold" style="color: #6b4d3b;">- Rs. {{ number_format($invoice->discount, 2) }}</td>
                </tr>
              @endif
              <tr class="table-light">
                <td class="fw-bold text-dark" colspan="2">Net Payable Total Fee</td>
                <td class="text-end fw-bold text-dark">Rs. {{ number_format($invoice->net_amount, 2) }}</td>
              </tr>
              <tr>
                <td class="fw-medium" style="color: #3d1a06;" colspan="2"><i data-lucide="check-circle" style="width:1rem;height:1rem;" class="me-2" style="color: #3d1a06;"></i> Total Paid Amount Received</td>
                <td class="text-end fw-bold" style="color: #3d1a06;">Rs. {{ number_format($invoice->paid_amount, 2) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="{{ $invoice->due_balance > 0 ? 'bg-danger-subtle text-danger' : 'text-dark' }}" style="{{ $invoice->due_balance > 0 ? '' : 'background-color: #f5eee0; color: #3d1a06;' }}">
                <th colspan="2" class="fs-6 fw-bold">REMAINING OUTSTANDING BALANCE DUE</th>
                <th class="text-end fs-5 fw-extrabold">Rs. {{ number_format($invoice->due_balance, 2) }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      @if(isset($previousUnpaid) && $previousUnpaid->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4 border-danger">
          <div class="card-header bg-danger-subtle py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-danger d-flex align-items-center gap-2">
              <i data-lucide="alert-triangle" style="width:1.1rem;height:1.1rem;" class="text-danger"></i> Previous Pending Unpaid Transactions / Arrears
            </h6>
            <span class="badge bg-danger text-white fs-7">{{ $previousUnpaid->count() }} Unpaid {{ Str::plural('Voucher', $previousUnpaid->count()) }}</span>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 fs-7">
              <thead class="table-light">
                <tr>
                  <th>Voucher #</th>
                  <th>Fee Type</th>
                  <th>Fee Month</th>
                  <th>Due Date</th>
                  <th>Invoice Amount</th>
                  <th>Paid</th>
                  <th class="text-end">Remaining Due Balance</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($previousUnpaid as $prev)
                  <tr>
                    <td class="fw-bold text-primary font-monospace">{{ $prev->invoice_no }}</td>
                    <td class="text-capitalize">{{ str_replace('_', ' ', $prev->fee_type) }}</td>
                    <td>{{ $prev->fee_month }}</td>
                    <td class="text-danger">{{ $prev->due_date ? $prev->due_date->format('d M Y') : 'N/A' }}</td>
                    <td>Rs. {{ number_format($prev->amount, 2) }}</td>
                    <td style="color: #3d1a06;">Rs. {{ number_format($prev->paid_amount, 2) }}</td>
                    <td class="text-end fw-bold text-danger">Rs. {{ number_format($prev->due_balance, 2) }}</td>
                    <td class="text-end">
                      <a href="{{ route('fee-management.show', $prev->id) }}" class="btn btn-outline-primary btn-sm py-0.5 px-2 rounded-pill fs-8" target="_blank">
                        View Voucher
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      {{-- SIBLINGS FEE DETAILS & PAYMENT HISTORY --}}
      @if(!empty($siblingFeeSummaries) && count($siblingFeeSummaries) > 0)
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
          <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center" style="background-color: #f8f4ed;">
            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2" style="color: #3d1a06;">
              <i data-lucide="users" style="width:1.2rem;height:1.2rem;color: #3d1a06;"></i> Siblings Fee & Payment Records
            </h6>
            <span class="badge text-white px-2 py-1 fs-7" style="background-color: #3d1a06;">
              {{ count($siblingFeeSummaries) }} {{ Str::plural('Sibling', count($siblingFeeSummaries)) }}
            </span>
          </div>
          <div class="card-body p-4">
            @foreach($siblingFeeSummaries as $sibData)
              @php
                $sib = $sibData['student'];
                $sibInvoices = $sibData['invoices'];
              @endphp
              <div class="border rounded-3 p-3 mb-3 bg-light-subtle">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                  <div>
                    <h6 class="fw-bold mb-0 text-dark">
                      {{ $sib->first_name }} {{ $sib->last_name }}
                    </h6>
                    <small class="text-muted">
                      Adm No: <strong>{{ $sib->admission_no }}</strong> &bull; Class: <strong>{{ $sib->class_name }}</strong> {{ $sib->section_name ? '('.$sib->section_name.')' : '' }}
                    </small>
                  </div>
                  <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                    <div class="text-end">
                      <small class="text-muted d-block fs-8">Total Fee</small>
                      <span class="fw-semibold text-dark">Rs. {{ number_format($sibData['total_invoiced'], 2) }}</span>
                    </div>
                    <div class="text-end">
                      <small class="text-muted d-block fs-8">Total Paid</small>
                      <span class="fw-bold text-success">Rs. {{ number_format($sibData['total_paid'], 2) }}</span>
                    </div>
                    <div class="text-end">
                      <small class="text-muted d-block fs-8">Remaining Due</small>
                      <span class="fw-bold {{ $sibData['total_due'] > 0 ? 'text-danger' : 'text-muted' }}">Rs. {{ number_format($sibData['total_due'], 2) }}</span>
                    </div>
                  </div>
                </div>

                @if($sibInvoices->isNotEmpty())
                  <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 fs-7 bg-white">
                      <thead class="table-light">
                        <tr>
                          <th>Voucher #</th>
                          <th>Fee Type</th>
                          <th>Fee Month</th>
                          <th>Fee Amount</th>
                          <th>Paid Amount</th>
                          <th>Due Balance</th>
                          <th>Status</th>
                          <th class="text-end">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($sibInvoices as $sInv)
                          <tr>
                            <td class="fw-bold text-primary font-monospace">{{ $sInv->invoice_no }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $sInv->fee_type) }}</td>
                            <td>{{ $sInv->fee_month }}</td>
                            <td class="fw-semibold">Rs. {{ number_format($sInv->net_amount, 2) }}</td>
                            <td class="text-success fw-semibold">Rs. {{ number_format($sInv->paid_amount, 2) }}</td>
                            <td class="fw-bold {{ $sInv->due_balance > 0 ? 'text-danger' : 'text-muted' }}">
                              Rs. {{ number_format($sInv->due_balance, 2) }}
                            </td>
                            <td>
                              <span class="badge {{ $sInv->status_badge_class }} text-capitalize px-2 py-0.5 fs-8">
                                {{ $sInv->status }}
                              </span>
                            </td>
                            <td class="text-end">
                              <a href="{{ route('fee-management.show', $sInv->id) }}" class="btn btn-outline-secondary btn-sm py-0 px-2 fs-8 me-1" target="_blank" title="View Voucher">
                                View
                              </a>
                              @if($sInv->due_balance > 0)
                                <a href="{{ route('fee-management.collect-payment', $sInv->id) }}" class="btn btn-sm text-white py-0 px-2 fs-8" style="background-color: #3d1a06;" title="Collect Payment">
                                  Collect Fee
                                </a>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @else
                  <p class="text-muted fs-7 mb-0 italic">No fee invoices recorded for this sibling yet.</p>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @if($invoice->notes)
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-light mb-4">
          <div class="d-flex align-items-center gap-2 mb-1">
            <i data-lucide="file-text" style="width:1rem;height:1rem;" class="text-secondary"></i>
            <span class="fw-bold text-xs text-uppercase text-secondary">Remarks / Special Notes</span>
          </div>
          <p class="mb-0 text-dark fs-7" style="white-space: pre-line;">{{ $invoice->notes }}</p>
        </div>
      @endif

      {{-- PAYMENT TRANSACTIONS HISTORY TABLE --}}
      <div class="card border-0 shadow-sm rounded-3 mb-4 fade-up">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i data-lucide="history" class="text-primary" style="width:1.2rem;height:1.2rem;"></i>
            Payment Transactions History / Receipts
          </h5>
          <a href="{{ route('fee-management.collect-payment', $invoice->id) }}" class="btn btn-success btn-sm text-white d-inline-flex align-items-center gap-1 fw-bold" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none;">
            <i data-lucide="plus" style="width:0.875rem;height:0.875rem;"></i> Collect Payment
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light text-muted fs-7">
                <tr>
                  <th class="ps-3">Receipt #</th>
                  <th>Payment Date</th>
                  <th>Payment Method</th>
                  <th>Amount Collected</th>
                  <th>Notes / Remarks</th>
                  <th class="text-end pe-3">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($invoice->payments as $payment)
                  <tr>
                    <td class="ps-3 fw-bold text-primary">
                      {{ $payment->receipt_no }}
                    </td>
                    <td class="fs-7 text-dark fw-semibold">
                      {{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}
                    </td>
                    <td>
                      <span class="badge bg-secondary-subtle text-secondary text-capitalize px-2 py-1">
                        {{ str_replace('_', ' ', $payment->payment_method) }}
                      </span>
                    </td>
                    <td class="fw-bold" style="color: #3d1a06;">
                      Rs. {{ number_format($payment->amount, 2) }}
                    </td>
                    <td class="fs-7 text-muted">
                      {{ $payment->note ?: '—' }}
                    </td>
                    <td class="text-end pe-3 text-nowrap">
                      <a href="{{ route('fee-management.payment-receipt', $payment->id) }}" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Print Receipt">
                        <i data-lucide="printer" style="width:0.875rem;height:0.875rem;"></i>
                      </a>
                      <a href="{{ route('collect-payment.edit', $payment->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Payment">
                        <i data-lucide="edit-2" style="width:0.875rem;height:0.875rem;"></i>
                      </a>
                      <form action="{{ route('collect-payment.destroy', $payment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment record?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Payment">
                          <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted fs-7">
                      <i data-lucide="info" class="mb-1" style="width:1.5rem;height:1.5rem;"></i>
                      <p class="mb-0">No payment transaction records found for this invoice yet.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
              @if($invoice->payments->isNotEmpty())
                <tfoot class="bg-light border-top">
                  <tr>
                    <td colspan="3" class="fw-bold text-end ps-3 fs-7">Total Paid Collected:</td>
                    <td class="fw-bold fs-6" style="color: #3d1a06;">Rs. {{ number_format($invoice->payments->sum('amount'), 2) }}</td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              @endif
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  @if($invoice->due_balance > 0 && $invoice->status !== 'cancelled')
    <!-- PAYMENT RECORD MODAL -->
    <div class="modal fade text-start no-print" id="payModalShow" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <form action="{{ route('fee-management.payment', $invoice->id) }}" method="POST">
            @csrf
            <div class="modal-header bg-light border-bottom py-3">
              <h5 class="modal-title fs-6 fw-bold text-dark d-flex align-items-center gap-2">
                <i data-lucide="banknote" style="color: #3d1a06; width:1.2rem;height:1.2rem;"></i> Record Fee Payment
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
              <div class="mb-3">
                <label class="form-label fw-semibold text-dark">Payment Amount (Rs.)</label>
                <input type="number" step="0.01" min="1" max="{{ $invoice->due_balance }}" name="payment_amount" class="form-control form-control-lg fw-bold text-primary" value="{{ $invoice->due_balance }}" required>
                <small class="text-muted mt-1 d-block">Maximum remaining due balance: <strong>Rs. {{ number_format($invoice->due_balance, 2) }}</strong></small>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-dark">Payment Method</label>
                  <select name="payment_method" class="form-select" required>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="online">Online Payment</option>
                    <option value="cheque">Cheque</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold text-dark">Payment Date</label>
                  <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
              </div>
            </div>
            <div class="modal-footer bg-light border-top py-3">
              <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-dark btn-sm px-4 fw-semibold" style="background-color: #3d1a06; border-color: #3d1a06;">
                Confirm & Save Payment
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endsection
