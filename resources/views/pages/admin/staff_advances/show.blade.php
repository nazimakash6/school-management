@extends('layouts.app')

@section('title', 'Staff Advance Details')

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Salary Advance Details</h1>
      <p class="page-subtitle">Advance record for {{ $advance->staff ? $advance->staff->full_name : 'Staff Member' }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff-advances.index') }}" class="btn btn-outline-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Advances
      </a>
      <a href="{{ route('staff-advances.create', ['staff_id' => $advance->staff_id]) }}" class="btn btn-success btn-sm">
        <i data-lucide="plus-circle" style="width:1rem;height:1rem;" class="me-1"></i> Issue More Advance
      </a>
      <a href="{{ route('staff-advances.print', $advance->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
        <i data-lucide="printer" style="width:1rem;height:1rem;" class="me-1"></i> Print Invoice
      </a>
      <a href="{{ route('staff-advances.edit', $advance->id) }}" class="btn btn-warning btn-sm">
        <i data-lucide="edit-2" style="width:1rem;height:1rem;" class="me-1"></i> Edit
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row g-4">
    <!-- Staff Info Column -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center p-4">
          <div class="avatar avatar-xl mb-3 mx-auto rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-size: 1.5rem;">
            {{ substr($advance->staff->first_name ?? 'S', 0, 1) }}{{ substr($advance->staff->last_name ?? 'M', 0, 1) }}
          </div>
          <h5 class="fw-bold text-dark mb-1">{{ $advance->staff ? $advance->staff->full_name : 'N/A' }}</h5>
          <span class="badge bg-light text-secondary border mb-3">{{ $advance->staff ? ($advance->staff->staff_id ?? 'STF-'.$advance->staff->id) : '' }}</span>

          <div class="text-start border-top pt-3 space-y-2" style="font-size: 0.9rem;">
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span class="text-muted">Department:</span>
              <span class="fw-semibold text-dark">{{ $advance->staff ? $advance->staff->formatted_department : 'N/A' }}</span>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span class="text-muted">Designation:</span>
              <span class="fw-semibold text-dark">{{ $advance->staff ? $advance->staff->formatted_designation : 'N/A' }}</span>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span class="text-muted">Base Salary:</span>
              <span class="fw-semibold text-dark">Rs. {{ number_format($advance->staff->salary ?? 0, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between py-1">
              <span class="text-muted">Mobile No:</span>
              <span class="fw-semibold text-dark">{{ $advance->staff->mobile_no ?? 'N/A' }}</span>
            </div>
          </div>
          <div class="mt-4 d-grid gap-2">
            <a href="{{ route('staff-advances.create', ['staff_id' => $advance->staff_id]) }}" class="btn btn-success btn-sm w-100">
              <i data-lucide="plus-circle" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Issue More Advance
            </a>
            <a href="{{ route('staff.show', $advance->staff_id) }}" class="btn btn-outline-primary btn-sm w-100">
              <i data-lucide="user" style="width:0.875rem;height:0.875rem;" class="me-1"></i> View Staff Profile
            </a>
          </div>
        </div>
      </div>

      <!-- Staff Overall Advance Summary Card -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
          <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i data-lucide="pie-chart" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
            Overall Advance Summary
          </h6>
        </div>
        <div class="card-body p-3" style="font-size: 0.9rem;">
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Total Advance Records:</span>
            <span class="fw-bold text-dark">{{ $staffAdvances->count() }}</span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Total Advance Taken:</span>
            <span class="fw-bold text-primary">Rs. {{ number_format($overallTotal, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Total Amount Repaid:</span>
            <span class="fw-bold text-success">Rs. {{ number_format($overallRepaid, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between py-2 border-bottom">
            <span class="text-muted">Total Pending Balance:</span>
            <span class="fw-bold text-danger">Rs. {{ number_format($overallPending, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between py-2 align-items-center">
            <span class="text-muted">Overall Account Status:</span>
            @if($overallPending <= 0)
              <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">Fully Cleared</span>
            @else
              <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">Outstanding Balance</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Advance Details & History Column -->
    <div class="col-lg-8">
      <!-- Current Advance Details Card -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold">Advance Details (#{{ $advance->id }})</h6>
          <span class="badge {{ $advance->status_badge_class }} border text-capitalize px-3 py-1">
            {{ str_replace('_', ' ', is_object($advance->status) ? $advance->status->value : $advance->status) }}
          </span>
        </div>
        <div class="card-body p-4">
          <div class="row g-3 mb-4 text-center">
            <div class="col-md-4">
              <div class="p-3 bg-light rounded border">
                <small class="text-muted d-block">Advance Issued</small>
                <h4 class="fw-bold text-primary mb-0">Rs. {{ number_format($advance->advance_amount, 2) }}</h4>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 bg-light rounded border">
                <small class="text-muted d-block">Total Repaid</small>
                <h4 class="fw-bold text-success mb-0">Rs. {{ number_format($advance->repaid_amount, 2) }}</h4>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 bg-light rounded border">
                <small class="text-muted d-block">Remaining Balance</small>
                <h4 class="fw-bold text-danger mb-0">Rs. {{ number_format($advance->remaining_balance, 2) }}</h4>
              </div>
            </div>
          </div>

          <!-- Progress Bar -->
          <div class="mb-4">
            <div class="d-flex justify-content-between mb-1">
              <span class="fw-semibold text-dark">Repayment Progress</span>
              <span class="fw-bold text-success">{{ $advance->repayment_progress }}%</span>
            </div>
            <div class="progress" style="height: 12px;">
              <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $advance->repayment_progress }}%;" aria-valuenow="{{ $advance->repayment_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>

          <div class="row g-3 border-top pt-3" style="font-size: 0.95rem;">
            <div class="col-md-6">
              <span class="text-muted d-block mb-1">Advance Date:</span>
              <span class="fw-semibold text-dark">{{ $advance->advance_date ? $advance->advance_date->format('F d, Y') : 'N/A' }}</span>
            </div>
            <div class="col-md-6">
              <span class="text-muted d-block mb-1">Disbursement Method:</span>
              <span class="fw-semibold text-dark text-capitalize">{{ str_replace('_', ' ', $advance->payment_method) }}</span>
            </div>
            <div class="col-md-12 mt-3">
              <span class="text-muted d-block mb-1">Reason / Purpose:</span>
              <span class="fw-semibold text-dark">{{ $advance->reason ?? 'Not specified' }}</span>
            </div>
          </div>

          @if($advance->notes)
            <div class="mt-4 p-3 bg-light rounded border">
              <h6 class="fw-bold text-secondary mb-1">Notes:</h6>
              <p class="mb-0 text-muted" style="white-space: pre-line;">{{ $advance->notes }}</p>
            </div>
          @endif

          @if($advance->remaining_balance > 0)
            <div class="mt-4 pt-3 border-top text-end">
              <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#repayModalShow">
                <i data-lucide="banknote" style="width:1rem;height:1rem;" class="me-1"></i> Record Repayment
              </button>
            </div>

            <!-- Repay Modal -->
            <div class="modal fade text-start" id="repayModalShow" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <form action="{{ route('staff-advances.repayment', $advance->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                      <h5 class="modal-title fs-6 fw-bold">Record Repayment</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label fw-medium">Repayment Amount (Rs.)</label>
                        <input type="number" step="0.01" min="1" max="{{ $advance->remaining_balance }}" name="repayment_amount" class="form-control" value="{{ $advance->remaining_balance }}" required>
                        <small class="text-muted">Maximum remaining balance: Rs. {{ number_format($advance->remaining_balance, 2) }}</small>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-success btn-sm">Save Repayment</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- Staff All Advance History Table Card -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i data-lucide="history" class="text-primary" style="width:1.1rem;height:1.1rem;"></i>
            All Advances History for {{ $advance->staff ? $advance->staff->full_name : 'Staff' }}
          </h6>
          <span class="badge bg-light text-dark border">{{ $staffAdvances->count() }} Total</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive custom-scroll-container">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
              <thead class="bg-light text-nowrap">
                <tr>
                  <th class="ps-3">Date</th>
                  <th>Advance Amount</th>
                  <th>Repaid Amount</th>
                  <th>Remaining Balance</th>
                  <th>Status</th>
                  <th class="text-end pe-3">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($staffAdvances as $stfAdv)
                  <tr class="{{ $stfAdv->id == $advance->id ? 'table-primary bg-primary-subtle' : '' }}">
                    <td class="ps-3 text-nowrap">
                      <div class="fw-semibold text-dark">
                        {{ $stfAdv->advance_date ? $stfAdv->advance_date->format('M d, Y') : 'N/A' }}
                        @if($stfAdv->id == $advance->id)
                          <span class="badge bg-primary text-white ms-1" style="font-size: 0.7rem;">Viewing</span>
                        @endif
                      </div>
                      <small class="text-muted">#ADV-{{ sprintf('%04d', $stfAdv->id) }}</small>
                    </td>
                    <td class="fw-bold text-primary text-nowrap">Rs. {{ number_format($stfAdv->advance_amount, 2) }}</td>
                    <td class="fw-bold text-success text-nowrap">Rs. {{ number_format($stfAdv->repaid_amount, 2) }}</td>
                    <td class="fw-bold text-danger text-nowrap">Rs. {{ number_format($stfAdv->remaining_balance, 2) }}</td>
                    <td class="text-nowrap">
                      <span class="badge {{ $stfAdv->status_badge_class }} border text-capitalize" style="font-size: 0.75rem;">
                        {{ str_replace('_', ' ', is_object($stfAdv->status) ? $stfAdv->status->value : $stfAdv->status) }}
                      </span>
                    </td>
                    <td class="text-end pe-3 text-nowrap">
                      @if($stfAdv->id == $advance->id)
                        <span class="badge bg-secondary-subtle text-secondary border">Current</span>
                      @else
                        <a href="{{ route('staff-advances.show', $stfAdv->id) }}" class="btn btn-xs btn-outline-info" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">
                          <i data-lucide="eye" style="width:0.8rem;height:0.8rem;" class="me-1"></i> View
                        </a>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No advance history found for this staff member.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

@push('styles')
  <style>
    .custom-scroll-container {
      max-height: 380px;
      overflow-x: auto;
      overflow-y: auto;
    }
    .custom-scroll-container thead th {
      position: sticky;
      top: 0;
      background-color: #f8f9fa !important;
      z-index: 2;
      box-shadow: inset 0 -1px 0 #dee2e6;
    }
    .custom-scroll-container::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    .custom-scroll-container::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 4px;
    }
    .custom-scroll-container::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }
    .custom-scroll-container::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }
  </style>
@endpush
@endsection
