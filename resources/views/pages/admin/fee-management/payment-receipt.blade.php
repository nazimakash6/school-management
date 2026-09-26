<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->receipt_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 13px; }
        .receipt-card { max-width: 650px; margin: 30px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 30px; }
        @media print {
            body { background: #fff; }
            .receipt-card { box-shadow: none; margin: 0; max-width: 100%; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center my-3 no-print">
            <button onclick="window.print()" class="btn btn-primary px-4 fw-bold">Print Receipt</button>
            <a href="{{ route('fee-management.show', $payment->fee_management_id) }}" class="btn btn-outline-secondary ms-2">Back to Invoice</a>
        </div>

        <div class="receipt-card">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                <div>
                    <h4 class="fw-bold mb-1 text-primary">SCHOOL MANAGEMENT SYSTEM</h4>
                    <p class="text-muted mb-0 fs-7">Official Payment Collection Receipt</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-success fs-6 px-3 py-2">RECEIPT</span>
                    <h6 class="fw-bold mt-2 mb-0 text-dark">{{ $payment->receipt_no }}</h6>
                    <small class="text-muted">Date: {{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}</small>
                </div>
            </div>

            <!-- Student & Invoice Info -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <div class="p-2 bg-light rounded border">
                        <small class="text-muted d-block uppercase fw-bold fs-8">STUDENT DETAILS</small>
                        <strong class="text-dark">{{ $payment->admission ? ($payment->admission->first_name . ' ' . $payment->admission->last_name) : 'Student' }}</strong>
                        <div class="text-muted fs-7">Admission No: {{ $payment->admission ? $payment->admission->admission_no : 'N/A' }}</div>
                        <div class="text-muted fs-7">Class: {{ $payment->admission ? $payment->admission->class_name : 'N/A' }} {{ $payment->admission && $payment->admission->section_name ? ('('.$payment->admission->section_name.')') : '' }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 bg-light rounded border">
                        <small class="text-muted d-block uppercase fw-bold fs-8">INVOICE DETAILS</small>
                        <strong class="text-dark">Invoice #: {{ $payment->feeManagement ? $payment->feeManagement->invoice_no : 'N/A' }}</strong>
                        <div class="text-muted fs-7">Fee Type: {{ $payment->feeManagement ? str_replace('_', ' ', $payment->feeManagement->fee_type) : '' }}</div>
                        <div class="text-muted fs-7">Fee Month: {{ $payment->feeManagement ? $payment->feeManagement->fee_month : '' }}</div>
                    </div>
                </div>
            </div>

            <!-- Transaction Details Table -->
            <table class="table table-bordered mb-3">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th>Payment Method</th>
                        <th class="text-end">Amount Collected</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Fee Payment Received
                            @if($payment->note)
                                <small class="d-block text-muted">Note: {{ $payment->note }}</small>
                            @endif
                        </td>
                        <td class="text-capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                        <td class="text-end fw-bold text-success">Rs. {{ number_format($payment->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Invoice Balance Summary -->
            @if($payment->feeManagement)
                <div class="p-3 bg-light rounded border mb-4">
                    <div class="d-flex justify-content-between text-muted fs-7 mb-1">
                        <span>Invoice Net Total:</span>
                        <span>Rs. {{ number_format($payment->feeManagement->net_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted fs-7 mb-1">
                        <span>Total Paid to Date:</span>
                        <span class="text-success fw-semibold">Rs. {{ number_format($payment->feeManagement->paid_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-1 text-dark fs-7">
                        <span>Remaining Due Balance:</span>
                        <span class="{{ $payment->feeManagement->due_balance > 0 ? 'text-danger' : 'text-success' }}">
                            Rs. {{ number_format($payment->feeManagement->due_balance, 2) }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- Signatures -->
            <div class="row pt-4 mt-4 border-top">
                <div class="col-6 text-center">
                    <div class="border-top w-75 mx-auto pt-1 text-muted fs-7">Depositor Signature</div>
                </div>
                <div class="col-6 text-center">
                    <div class="border-top w-75 mx-auto pt-1 text-muted fs-7">Authorized Accounts Officer</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
