@extends('layouts.app')

@section('title', 'Fee Management Trash')

@section('content')
  <div class="content-header mb-4">
    <div>
      <h1 class="page-title">Fee Invoices Trash</h1>
      <p class="page-subtitle">Manage soft-deleted student fee invoices and vouchers</p>
    </div>
    <div class="content-header-actions">
      <a href="{{ route('fee-management.index') }}" class="btn btn-secondary btn-sm">
        <i data-lucide="arrow-left" style="width:1rem;height:1rem;" class="me-1"></i> Back to Active Invoices
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Invoice No</th>
            <th>Student</th>
            <th>Fee Type & Month</th>
            <th>Net Amount</th>
            <th>Deleted At</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td>
                <span class="fw-bold text-secondary">{{ $inv->invoice_no }}</span>
              </td>
              <td>
                <div class="fw-semibold text-dark">
                  {{ $inv->admission ? ($inv->admission->first_name . ' ' . $inv->admission->last_name) : 'N/A' }}
                </div>
                <small class="text-muted">{{ $inv->admission ? $inv->admission->admission_no : '' }}</small>
              </td>
              <td>
                <span class="badge bg-light text-dark border text-capitalize me-1">{{ str_replace('_', ' ', $inv->fee_type) }}</span>
                <small class="text-muted d-block">{{ $inv->fee_month }}</small>
              </td>
              <td class="fw-semibold">
                Rs. {{ number_format($inv->net_amount, 2) }}
              </td>
              <td class="text-muted">
                {{ $inv->deleted_at ? $inv->deleted_at->format('M d, Y H:i') : '' }}
              </td>
              <td class="text-end text-nowrap">
                <form action="{{ route('fee-management.restore', $inv->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Restore Invoice">
                    <i data-lucide="rotate-ccw" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Restore
                  </button>
                </form>
                <form action="{{ route('fee-management.force-delete', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this fee invoice?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Permanently Delete">
                    <i data-lucide="trash-2" style="width:0.875rem;height:0.875rem;" class="me-1"></i> Delete Permanently
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">
                <i data-lucide="info" class="mb-2" style="width:2rem;height:2rem;"></i>
                <p class="mb-0">Trash is currently empty.</p>
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
