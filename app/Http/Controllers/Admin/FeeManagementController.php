<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Admission;
use App\Models\FeeManagement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeManagementController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search  = trim((string) $request->string('search'));
        $status  = trim((string) $request->string('status', 'all'));
        $feeType = trim((string) $request->string('fee_type', 'all'));
        $className = trim((string) $request->string('class_name', 'all'));

        $query = FeeManagement::with(['admission', 'academicSession'])->latest();

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('admission', function (Builder $b) use ($search) {
                      $b->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_no', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($feeType !== 'all') {
            $query->where('fee_type', $feeType);
        }

        if ($className !== 'all') {
            $query->whereHas('admission', function (Builder $b) use ($className) {
                $b->where('class_name', $className);
            });
        }

        $invoices = $query->paginate($perPage)->withQueryString();

        // Fetch Payments Records for Tab 2
        $paymentsQuery = \App\Models\FeePayment::with(['feeManagement', 'admission'])->latest('payment_date')->latest('id');

        if ($search !== '') {
            $paymentsQuery->where(function (Builder $q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                  ->orWhereHas('feeManagement', function (Builder $b) use ($search) {
                      $b->where('invoice_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('admission', function (Builder $b) use ($search) {
                      $b->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_no', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $paymentsQuery->paginate(25, ['*'], 'payments_page')->withQueryString();
        $paymentsCount = \App\Models\FeePayment::count();

        // Statistics
        $totalCollected = FeeManagement::where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalDues = FeeManagement::whereIn('status', ['unpaid', 'partial'])
            ->get()
            ->sum(fn ($f) => $f->due_balance);
        $paidCount = FeeManagement::where('status', 'paid')->count();
        $unpaidCount = FeeManagement::where('status', 'unpaid')->count();
        $partialCount = FeeManagement::where('status', 'partial')->count();
        $trashCount = FeeManagement::onlyTrashed()->count();

        $studentsList = Admission::orderBy('first_name')->get();
        $classesList = Admission::query()
            ->select('class_name')
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name');

        return view('pages.admin.fee-management.index', compact(
            'invoices',
            'payments',
            'paymentsCount',
            'perPage',
            'search',
            'status',
            'feeType',
            'className',
            'totalCollected',
            'totalDues',
            'paidCount',
            'unpaidCount',
            'partialCount',
            'trashCount',
            'studentsList',
            'classesList'
        ));
    }

    public function createCollectPayment(Request $request): View
    {
        $unpaidInvoices = FeeManagement::with(['admission', 'academicSession'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get();

        $allInvoices = FeeManagement::with(['admission', 'academicSession'])
            ->orderBy('invoice_no', 'desc')
            ->get();

        $selectedInvoiceId = $request->get('invoice_id');
        $selectedInvoice = $selectedInvoiceId ? FeeManagement::with(['admission', 'academicSession'])->find($selectedInvoiceId) : null;

        $latestPaymentId = \App\Models\FeePayment::max('id') + 1;
        $receiptNo = 'RCT-' . date('Ym') . '-' . str_pad($latestPaymentId, 4, '0', STR_PAD_LEFT);

        return view('pages.admin.fee-management.create-collect-payment', compact('unpaidInvoices', 'allInvoices', 'selectedInvoice', 'receiptNo'));
    }

    public function storeGeneralCollectPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method'    => ['required', 'string', 'in:cash,bank_transfer,online,cheque'],
            'payment_date'      => ['required', 'date'],
            'note'              => ['nullable', 'string', 'max:500'],
            'invoice_payments'  => ['nullable', 'array'],
            'invoice_payments.*'=> ['nullable', 'numeric', 'min:0'],
            'fee_management_id' => ['nullable', 'exists:fee_managements,id'],
            'payment_amount'    => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $invoicePayments = array_filter($validated['invoice_payments'] ?? [], fn ($amt) => (float) $amt > 0);

        if (empty($invoicePayments) && !empty($validated['fee_management_id']) && !empty($validated['payment_amount'])) {
            $invoicePayments[$validated['fee_management_id']] = $validated['payment_amount'];
        }

        if (empty($invoicePayments)) {
            return redirect()->back()->withInput()->with('warning', 'Please select at least one invoice and enter a valid payment amount.');
        }

        $totalCollected = 0;
        $processedCount = 0;

        foreach ($invoicePayments as $invoiceId => $amount) {
            $amount = (float) $amount;
            if ($amount <= 0) continue;

            $invoice = FeeManagement::find($invoiceId);
            if (!$invoice) continue;

            $latestPaymentId = \App\Models\FeePayment::max('id') + 1;
            $receiptNo = 'RCT-' . date('Ym') . '-' . str_pad($latestPaymentId, 4, '0', STR_PAD_LEFT);

            \App\Models\FeePayment::create([
                'fee_management_id' => $invoice->id,
                'admission_id'      => $invoice->admission_id,
                'receipt_no'        => $receiptNo,
                'amount'            => $amount,
                'payment_method'    => $validated['payment_method'],
                'payment_date'      => $validated['payment_date'],
                'note'              => $validated['note'] ?? null,
            ]);

            // Recalculate total paid & status for invoice
            $totalPaid = (float) \App\Models\FeePayment::where('fee_management_id', $invoice->id)->sum('amount');
            $invoice->paid_amount = $totalPaid;
            $invoice->payment_method = $validated['payment_method'];
            $invoice->payment_date = $validated['payment_date'];
            $invoice->save();

            $totalCollected += $amount;
            $processedCount++;
        }

        $msg = 'Payment of Rs. ' . number_format($totalCollected, 2) . ' collected successfully across ' . $processedCount . ' invoice(s).';

        return redirect()
            ->route('fee-management.index', ['tab' => 'payments'])
            ->with('success', $msg);
    }

    public function create(Request $request): View
    {
        $students = Admission::with('academicSession')->orderBy('first_name')->get();
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        
        $classes = \App\Models\StudentClass::where('status', 'active')->orderBy('name')->get();
        if ($classes->isEmpty()) {
            $classes = Admission::query()
                ->select('class_name')
                ->whereNotNull('class_name')
                ->where('class_name', '!=', '')
                ->distinct()
                ->orderBy('class_name')
                ->get()
                ->map(fn($item) => (object) ['name' => $item->class_name, 'id' => $item->class_name]);
        }

        $unpaidInvoices = FeeManagement::whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get()
            ->groupBy('admission_id');

        $selectedAdmissionId = $request->get('admission_id');

        return view('pages.admin.fee-management.create', compact('students', 'academicSessions', 'classes', 'unpaidInvoices', 'selectedAdmissionId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'admission_id'        => ['required', 'exists:admissions,id'],
            'academic_session_id' => ['nullable', 'exists:academic_sessions,id'],
            'fee_type'            => ['required', 'string', 'max:50'],
            'fee_month'           => ['required', 'string', 'max:20'],
            'amount'              => ['required', 'numeric', 'min:0'],
            'discount'            => ['nullable', 'numeric', 'min:0'],
            'paid_amount'         => ['nullable', 'numeric', 'min:0'],
            'due_date'            => ['required', 'date'],
            'payment_date'        => ['nullable', 'date'],
            'payment_method'      => ['required', 'string', 'in:cash,bank_transfer,online,cheque'],
            'status'              => ['required', 'string', 'in:unpaid,partial,paid,cancelled'],
            'notes'               => ['nullable', 'string'],
        ]);

        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        // Auto generate unique invoice number
        $latestId = FeeManagement::max('id') + 1;
        $validated['invoice_no'] = 'INV-' . date('Ym') . '-' . str_pad($latestId, 4, '0', STR_PAD_LEFT);

        $invoice = FeeManagement::create($validated);

        return redirect()
            ->route('fee-management.show', $invoice->id)
            ->with('success', 'Fee invoice generated successfully.');
    }

    public function show($id): View
    {
        $invoice = FeeManagement::with(['admission', 'academicSession', 'payments' => function ($q) {
            $q->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
        }])->findOrFail($id);

        // Auto backfill single FeePayment record if paid_amount > 0 and no payments record exists yet
        if ((float) $invoice->paid_amount > 0 && $invoice->payments->isEmpty()) {
            $latestPaymentId = \App\Models\FeePayment::max('id') + 1;
            $receiptNo = 'RCT-' . date('Ym') . '-' . str_pad($latestPaymentId, 4, '0', STR_PAD_LEFT);
            \App\Models\FeePayment::create([
                'fee_management_id' => $invoice->id,
                'admission_id'      => $invoice->admission_id,
                'receipt_no'        => $receiptNo,
                'amount'            => $invoice->paid_amount,
                'payment_method'    => $invoice->payment_method ?? 'cash',
                'payment_date'      => $invoice->payment_date ? $invoice->payment_date->toDateString() : now()->toDateString(),
                'note'              => 'Initial Payment Record',
            ]);
            $invoice->load('payments');
        }
        
        // Fetch previous unpaid/partial invoices for this student and siblings (excluding current invoice)
        $studentIds = [$invoice->admission_id];
        $siblingFeeSummaries = [];
        if ($invoice->admission) {
            $siblings = $invoice->admission->siblings;
            foreach ($siblings as $sib) {
                $sibAdmId = Admission::where('admission_no', $sib->admission_no)->value('id') ?: $sib->id;
                $studentIds[] = $sibAdmId;

                $sibInvoices = FeeManagement::where('admission_id', $sibAdmId)
                    ->with('payments')
                    ->orderBy('due_date', 'desc')
                    ->get();

                $totalInvoiced = $sibInvoices->sum(fn($inv) => $inv->net_amount);
                $totalPaid     = $sibInvoices->sum('paid_amount');
                $totalDue      = $sibInvoices->sum(fn($inv) => $inv->due_balance);

                $siblingFeeSummaries[] = [
                    'student'        => $sib,
                    'admission_id'   => $sibAdmId,
                    'total_invoiced' => $totalInvoiced,
                    'total_paid'     => $totalPaid,
                    'total_due'      => $totalDue,
                    'invoices'       => $sibInvoices,
                ];
            }
        }
        $studentIds = array_unique(array_filter($studentIds));

        $previousUnpaid = FeeManagement::whereIn('admission_id', $studentIds)
            ->where('id', '!=', $invoice->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get();

        return view('pages.admin.fee-management.show', compact('invoice', 'previousUnpaid', 'siblingFeeSummaries'));
    }

    public function printVoucher($id): View
    {
        $invoice = FeeManagement::with(['admission', 'academicSession', 'payments' => function ($q) {
            $q->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
        }])->findOrFail($id);

        $studentIds = [$invoice->admission_id];
        $siblingFeeSummaries = [];
        if ($invoice->admission) {
            $siblings = $invoice->admission->siblings;
            foreach ($siblings as $sib) {
                $sibAdmId = Admission::where('admission_no', $sib->admission_no)->value('id') ?: $sib->id;
                $studentIds[] = $sibAdmId;

                $sibInvoices = FeeManagement::where('admission_id', $sibAdmId)
                    ->with('payments')
                    ->orderBy('due_date', 'desc')
                    ->get();

                $totalInvoiced = $sibInvoices->sum(fn($inv) => $inv->net_amount);
                $totalPaid     = $sibInvoices->sum('paid_amount');
                $totalDue      = $sibInvoices->sum(fn($inv) => $inv->due_balance);

                $siblingFeeSummaries[] = [
                    'student'        => $sib,
                    'admission_id'   => $sibAdmId,
                    'total_invoiced' => $totalInvoiced,
                    'total_paid'     => $totalPaid,
                    'total_due'      => $totalDue,
                    'invoices'       => $sibInvoices,
                ];
            }
        }
        $studentIds = array_unique(array_filter($studentIds));

        $previousUnpaid = FeeManagement::whereIn('admission_id', $studentIds)
            ->where('id', '!=', $invoice->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get();

        return view('pages.admin.fee-management.print', compact('invoice', 'previousUnpaid', 'siblingFeeSummaries'));
    }

    public function collectPaymentForm($id): View|RedirectResponse
    {
        $invoice = FeeManagement::with(['admission', 'academicSession', 'payments' => function ($q) {
            $q->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
        }])->findOrFail($id);

        $student = $invoice->admission;

        // Fetch unpaid/partial invoices for this student and siblings
        $studentIds = [$invoice->admission_id];
        if ($student) {
            foreach ($student->siblings as $sib) {
                $sibAdmId = Admission::where('admission_no', $sib->admission_no)->value('id') ?: $sib->id;
                $studentIds[] = $sibAdmId;
            }
        }
        $studentIds = array_unique(array_filter($studentIds));

        $otherUnpaidInvoices = FeeManagement::whereIn('admission_id', $studentIds)
            ->where('id', '!=', $invoice->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get();

        $totalOtherDues = $otherUnpaidInvoices->sum(fn ($i) => $i->due_balance);

        // Auto generate next Receipt Number
        $latestPaymentId = \App\Models\FeePayment::max('id') + 1;
        $receiptNo = 'RCT-' . date('Ym') . '-' . str_pad($latestPaymentId, 4, '0', STR_PAD_LEFT);

        return view('pages.admin.fee-management.collect-payment', compact('invoice', 'student', 'otherUnpaidInvoices', 'totalOtherDues', 'receiptNo'));
    }

    public function collectPaymentFormGeneral(Request $request): RedirectResponse
    {
        $invoiceId = $request->get('invoice_id');
        if ($invoiceId) {
            return redirect()->route('fee-management.collect-payment', $invoiceId);
        }

        $unpaidInvoice = FeeManagement::whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc')
            ->first();

        if ($unpaidInvoice) {
            return redirect()->route('fee-management.collect-payment', $unpaidInvoice->id);
        }

        $latestInvoice = FeeManagement::latest()->first();
        if ($latestInvoice) {
            return redirect()->route('fee-management.collect-payment', $latestInvoice->id);
        }

        return redirect()->route('fee-management.index')->with('warning', 'No invoices found to collect payment.');
    }

    public function storePayment(Request $request, $id): RedirectResponse
    {
        $invoice = FeeManagement::findOrFail($id);

        $validated = $request->validate([
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,online,cheque'],
            'payment_date'   => ['required', 'date'],
            'note'           => ['nullable', 'string', 'max:500'],
        ]);

        // Auto generate receipt_no
        $latestPaymentId = \App\Models\FeePayment::max('id') + 1;
        $receiptNo = 'RCT-' . date('Ym') . '-' . str_pad($latestPaymentId, 4, '0', STR_PAD_LEFT);

        // Create distinct FeePayment entry in database
        \App\Models\FeePayment::create([
            'fee_management_id' => $invoice->id,
            'admission_id'      => $invoice->admission_id,
            'receipt_no'        => $receiptNo,
            'amount'            => $validated['payment_amount'],
            'payment_method'    => $validated['payment_method'],
            'payment_date'      => $validated['payment_date'],
            'note'              => $validated['note'] ?? null,
        ]);

        // Recalculate invoice total paid amount from all distinct payments
        $totalPaid = (float) $invoice->payments()->sum('amount');
        $invoice->paid_amount = $totalPaid;
        $invoice->payment_method = $validated['payment_method'];
        $invoice->payment_date = $validated['payment_date'];
        $invoice->save(); // booted model event updates status ('paid', 'partial', 'unpaid')

        return redirect()
            ->route('fee-management.show', $invoice->id)
            ->with('success', 'Payment of Rs. ' . number_format($validated['payment_amount'], 2) . ' recorded successfully (Receipt #' . $receiptNo . ').');
    }

    public function printPaymentReceipt($paymentId): View|RedirectResponse
    {
        $payment = \App\Models\FeePayment::findOrFail($paymentId);
        if ($payment->fee_management_id) {
            return $this->printVoucher($payment->fee_management_id);
        }
        return redirect()->route('fee-management.index')->with('warning', 'Associated fee invoice not found.');
    }

    public function editPayment($id): View
    {
        $payment = \App\Models\FeePayment::with(['feeManagement.admission', 'admission'])->findOrFail($id);
        return view('pages.admin.fee-management.edit-payment', compact('payment'));
    }

    public function updatePayment(Request $request, $id): RedirectResponse
    {
        $payment = \App\Models\FeePayment::findOrFail($id);

        $validated = $request->validate([
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,online,cheque'],
            'payment_date'   => ['required', 'date'],
            'note'           => ['nullable', 'string', 'max:500'],
        ]);

        $payment->update([
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date'   => $validated['payment_date'],
            'note'           => $validated['note'] ?? null,
        ]);

        // Recalculate parent fee_management invoice totals & status
        $invoice = FeeManagement::find($payment->fee_management_id);
        if ($invoice) {
            $totalPaid = (float) \App\Models\FeePayment::where('fee_management_id', $invoice->id)->sum('amount');
            $invoice->paid_amount = $totalPaid;
            $invoice->save();
        }

        return redirect()
            ->route('fee-management.index', ['tab' => 'payments'])
            ->with('success', 'Payment record (Receipt #' . $payment->receipt_no . ') updated successfully.');
    }

    public function destroyPayment($id): RedirectResponse
    {
        $payment = \App\Models\FeePayment::findOrFail($id);
        $receiptNo = $payment->receipt_no;
        $invoiceId = $payment->fee_management_id;

        $payment->delete();

        // Recalculate parent fee_management invoice totals & status
        $invoice = FeeManagement::find($invoiceId);
        if ($invoice) {
            $totalPaid = (float) \App\Models\FeePayment::where('fee_management_id', $invoice->id)->sum('amount');
            $invoice->paid_amount = $totalPaid;
            $invoice->save();
        }

        return redirect()
            ->route('fee-management.index', ['tab' => 'payments'])
            ->with('success', 'Payment receipt #' . $receiptNo . ' deleted successfully.');
    }

    public function edit($id): View
    {
        $invoice = FeeManagement::with(['admission', 'academicSession'])->findOrFail($id);
        $students = Admission::with('academicSession')->orderBy('first_name')->get();
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();

        return view('pages.admin.fee-management.edit', compact('invoice', 'students', 'academicSessions'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $invoice = FeeManagement::findOrFail($id);

        $validated = $request->validate([
            'admission_id'        => ['required', 'exists:admissions,id'],
            'academic_session_id' => ['nullable', 'exists:academic_sessions,id'],
            'fee_type'            => ['required', 'string', 'max:50'],
            'fee_month'           => ['required', 'string', 'max:20'],
            'amount'              => ['required', 'numeric', 'min:0'],
            'discount'            => ['nullable', 'numeric', 'min:0'],
            'paid_amount'         => ['nullable', 'numeric', 'min:0'],
            'due_date'            => ['required', 'date'],
            'payment_date'        => ['nullable', 'date'],
            'payment_method'      => ['required', 'string', 'in:cash,bank_transfer,online,cheque'],
            'status'              => ['required', 'string', 'in:unpaid,partial,paid,cancelled'],
            'notes'               => ['nullable', 'string'],
        ]);

        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $invoice->update($validated);

        return redirect()
            ->route('fee-management.index')
            ->with('success', 'Fee invoice updated successfully.');
    }

    public function recordPayment(Request $request, $id): RedirectResponse
    {
        return $this->storePayment($request, $id);
    }

    public function destroy($id): RedirectResponse
    {
        $invoice = FeeManagement::findOrFail($id);
        $invoice->delete();

        return redirect()
            ->route('fee-management.index')
            ->with('success', 'Fee invoice moved to trash.');
    }

    public function trash(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search  = trim((string) $request->string('search'));

        $query = FeeManagement::onlyTrashed()->with('admission')->latest('deleted_at');

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('admission', function (Builder $b) use ($search) {
                      $b->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_no', 'like', "%{$search}%");
                  });
            });
        }

        $invoices   = $query->paginate($perPage)->withQueryString();
        $trashCount = FeeManagement::onlyTrashed()->count();

        return view('pages.admin.fee-management.trash', compact('invoices', 'perPage', 'search', 'trashCount'));
    }

    public function restore($id): RedirectResponse
    {
        $invoice = FeeManagement::onlyTrashed()->findOrFail($id);
        $invoice->restore();

        return redirect()
            ->route('fee-management.trash')
            ->with('success', 'Fee invoice restored successfully.');
    }

    public function statement(Request $request): View
    {
        $startDate   = trim((string) $request->string('start_date'));
        $endDate     = trim((string) $request->string('end_date'));
        $admissionId = (int) $request->integer('admission_id', 0);
        $className   = trim((string) $request->string('class_name', 'all'));
        $feeType     = trim((string) $request->string('fee_type', 'all'));
        $status      = trim((string) $request->string('status', 'all'));
        $method      = trim((string) $request->string('payment_method', 'all'));

        $query = FeeManagement::query()->with('admission')->latest('created_at')->latest('id');

        if ($startDate !== '') {
            $query->where(function ($q) use ($startDate) {
                $q->whereDate('due_date', '>=', $startDate)
                  ->orWhereDate('payment_date', '>=', $startDate)
                  ->orWhereDate('created_at', '>=', $startDate);
            });
        }

        if ($endDate !== '') {
            $query->where(function ($q) use ($endDate) {
                $q->whereDate('due_date', '<=', $endDate)
                  ->orWhereDate('payment_date', '<=', $endDate)
                  ->orWhereDate('created_at', '<=', $endDate);
            });
        }

        if ($admissionId > 0) {
            $query->where('admission_id', $admissionId);
        }

        if ($className !== 'all') {
            $query->whereHas('admission', function (Builder $b) use ($className) {
                $b->where('class_name', $className);
            });
        }

        if ($feeType !== 'all') {
            $query->where('fee_type', $feeType);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($method !== 'all') {
            $query->where('payment_method', $method);
        }

        $invoices = $query->get();

        // Calculate summary metrics
        $totalGrossAmount = $invoices->sum('amount');
        $totalDiscount    = $invoices->sum('discount');
        $totalNetAmount   = $invoices->sum(fn ($i) => $i->net_amount);
        $totalPaidAmount  = $invoices->where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalDueBalance  = $invoices->where('status', '!=', 'cancelled')->sum(fn ($i) => $i->due_balance);

        $paidInvoicesCount      = $invoices->where('status', 'paid')->count();
        $unpaidInvoicesCount    = $invoices->where('status', 'unpaid')->count();
        $partialInvoicesCount   = $invoices->where('status', 'partial')->count();
        $pendingInvoicesCount   = $unpaidInvoicesCount + $partialInvoicesCount;
        $cancelledInvoicesCount = $invoices->where('status', 'cancelled')->count();

        $selectedStudent = $admissionId > 0 ? Admission::find($admissionId) : null;
        $studentsList    = Admission::orderBy('first_name')->get();
        $classesList     = Admission::query()
            ->select('class_name')
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name');

        return view('pages.admin.fee-management.statement', compact(
            'invoices',
            'startDate',
            'endDate',
            'admissionId',
            'selectedStudent',
            'className',
            'feeType',
            'status',
            'method',
            'totalGrossAmount',
            'totalDiscount',
            'totalNetAmount',
            'totalPaidAmount',
            'totalDueBalance',
            'paidInvoicesCount',
            'unpaidInvoicesCount',
            'partialInvoicesCount',
            'pendingInvoicesCount',
            'cancelledInvoicesCount',
            'studentsList',
            'classesList'
        ));
    }

    public function exportStatement(Request $request)
    {
        $startDate   = trim((string) $request->string('start_date'));
        $endDate     = trim((string) $request->string('end_date'));
        $admissionId = (int) $request->integer('admission_id', 0);
        $className   = trim((string) $request->string('class_name', 'all'));
        $feeType     = trim((string) $request->string('fee_type', 'all'));
        $status      = trim((string) $request->string('status', 'all'));
        $method      = trim((string) $request->string('payment_method', 'all'));

        $query = FeeManagement::query()->with('admission')->latest('created_at')->latest('id');

        if ($startDate !== '') {
            $query->where(function ($q) use ($startDate) {
                $q->whereDate('due_date', '>=', $startDate)
                  ->orWhereDate('payment_date', '>=', $startDate)
                  ->orWhereDate('created_at', '>=', $startDate);
            });
        }

        if ($endDate !== '') {
            $query->where(function ($q) use ($endDate) {
                $q->whereDate('due_date', '<=', $endDate)
                  ->orWhereDate('payment_date', '<=', $endDate)
                  ->orWhereDate('created_at', '<=', $endDate);
            });
        }

        if ($admissionId > 0) {
            $query->where('admission_id', $admissionId);
        }

        if ($className !== 'all') {
            $query->whereHas('admission', function (Builder $b) use ($className) {
                $b->where('class_name', $className);
            });
        }

        if ($feeType !== 'all') {
            $query->where('fee_type', $feeType);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($method !== 'all') {
            $query->where('payment_method', $method);
        }

        $invoices = $query->get();

        $filename = 'fee_statement_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Invoice No',
                'Admission No',
                'Student Name',
                'Class',
                'Section',
                'Fee Type',
                'Fee Month',
                'Due Date',
                'Payment Date',
                'Gross Amount (PKR)',
                'Discount (PKR)',
                'Net Amount (PKR)',
                'Paid Amount (PKR)',
                'Due Balance (PKR)',
                'Payment Method',
                'Status',
            ]);

            foreach ($invoices as $inv) {
                $student = $inv->admission;
                fputcsv($file, [
                    $inv->invoice_no,
                    $student ? $student->admission_no : 'N/A',
                    $student ? ($student->first_name . ' ' . $student->last_name) : 'N/A',
                    $student ? $student->class_name : 'N/A',
                    $student ? $student->section : 'N/A',
                    str_replace('_', ' ', $inv->fee_type),
                    $inv->fee_month,
                    $inv->due_date ? $inv->due_date->format('Y-m-d') : 'N/A',
                    $inv->payment_date ? $inv->payment_date->format('Y-m-d') : 'N/A',
                    $inv->amount,
                    $inv->discount,
                    $inv->net_amount,
                    $inv->paid_amount,
                    $inv->due_balance,
                    str_replace('_', ' ', $inv->payment_method),
                    strtoupper($inv->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printStatement(Request $request): View
    {
        $startDate   = trim((string) $request->string('start_date'));
        $endDate     = trim((string) $request->string('end_date'));
        $admissionId = (int) $request->integer('admission_id', 0);
        $className   = trim((string) $request->string('class_name', 'all'));
        $feeType     = trim((string) $request->string('fee_type', 'all'));
        $status      = trim((string) $request->string('status', 'all'));
        $method      = trim((string) $request->string('payment_method', 'all'));

        $query = FeeManagement::query()->with('admission')->latest('created_at')->latest('id');

        if ($startDate !== '') {
            $query->where(function ($q) use ($startDate) {
                $q->whereDate('due_date', '>=', $startDate)
                  ->orWhereDate('payment_date', '>=', $startDate)
                  ->orWhereDate('created_at', '>=', $startDate);
            });
        }

        if ($endDate !== '') {
            $query->where(function ($q) use ($endDate) {
                $q->whereDate('due_date', '<=', $endDate)
                  ->orWhereDate('payment_date', '<=', $endDate)
                  ->orWhereDate('created_at', '<=', $endDate);
            });
        }

        if ($admissionId > 0) {
            $query->where('admission_id', $admissionId);
        }

        if ($className !== 'all') {
            $query->whereHas('admission', function (Builder $b) use ($className) {
                $b->where('class_name', $className);
            });
        }

        if ($feeType !== 'all') {
            $query->where('fee_type', $feeType);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($method !== 'all') {
            $query->where('payment_method', $method);
        }

        $invoices = $query->get();

        $totalGrossAmount = $invoices->sum('amount');
        $totalDiscount    = $invoices->sum('discount');
        $totalNetAmount   = $invoices->sum(fn ($i) => $i->net_amount);
        $totalPaidAmount  = $invoices->where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalDueBalance  = $invoices->where('status', '!=', 'cancelled')->sum(fn ($i) => $i->due_balance);

        $selectedStudent = $admissionId > 0 ? Admission::find($admissionId) : null;

        return view('pages.admin.fee-management.print-statement', compact(
            'invoices',
            'startDate',
            'endDate',
            'selectedStudent',
            'className',
            'feeType',
            'status',
            'method',
            'totalGrossAmount',
            'totalDiscount',
            'totalNetAmount',
            'totalPaidAmount',
            'totalDueBalance'
        ));
    }
}
