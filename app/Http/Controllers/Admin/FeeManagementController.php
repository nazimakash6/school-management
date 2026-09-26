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

    public function create(Request $request): View
    {
        $students = Admission::with('academicSession')->orderBy('first_name')->get();
        $academicSessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $selectedAdmissionId = $request->get('admission_id');

        return view('pages.admin.fee-management.create', compact('students', 'academicSessions', 'selectedAdmissionId'));
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

        FeeManagement::create($validated);

        return redirect()
            ->route('fee-management.index')
            ->with('success', 'Fee invoice generated successfully.');
    }

    public function show($id): View
    {
        $invoice = FeeManagement::with(['admission', 'academicSession'])->findOrFail($id);

        return view('pages.admin.fee-management.show', compact('invoice'));
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
        $invoice = FeeManagement::findOrFail($id);

        $validated = $request->validate([
            'payment_amount' => ['required', 'numeric', 'min:1', 'max:' . $invoice->due_balance],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,online,cheque'],
            'payment_date'   => ['required', 'date'],
        ]);

        $invoice->paid_amount += (float) $validated['payment_amount'];
        $invoice->payment_method = $validated['payment_method'];
        $invoice->payment_date = $validated['payment_date'];

        $invoice->save();

        return redirect()
            ->route('fee-management.index')
            ->with('success', 'Payment of Rs. ' . number_format($validated['payment_amount'], 2) . ' recorded successfully.');
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
