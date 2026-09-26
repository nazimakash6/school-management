<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $perPage    = (int) $request->integer('per_page', 25);
        $search     = trim((string) $request->string('search'));
        $month      = trim((string) $request->string('month', 'all'));
        $department = trim((string) $request->string('department', 'all'));
        $status     = trim((string) $request->string('status', 'all'));

        if (! in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        if (! in_array($status, ['all', 'paid', 'pending', 'cancelled'], true)) {
            $status = 'all';
        }

        $query = Payroll::query()->with('staff')->latest('id');

        if ($search !== '') {
            $query->whereHas('staff', function (Builder $b) use ($search) {
                $b->where('staff_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($month !== 'all') {
            $query->where('payroll_month', $month);
        }

        if ($department !== 'all') {
            $query->whereHas('staff', function (Builder $b) use ($department) {
                $b->where('department', $department);
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payrolls = $query->paginate($perPage)->withQueryString();

        $totalDisbursed = Payroll::where('status', 'paid')->sum('net_salary');
        $totalPending   = Payroll::where('status', 'pending')->sum('net_salary');
        $paidCount      = Payroll::where('status', 'paid')->count();
        $pendingCount   = Payroll::where('status', 'pending')->count();
        $trashCount     = Payroll::onlyTrashed()->count();

        $months = Payroll::query()->distinct()->pluck('payroll_month')->sortDesc()->values();
        $departments = Staff::query()->distinct()->pluck('department')->filter()->values();

        return view('pages.admin.payroll.index', compact(
            'payrolls',
            'perPage',
            'search',
            'month',
            'department',
            'status',
            'totalDisbursed',
            'totalPending',
            'paidCount',
            'pendingCount',
            'trashCount',
            'months',
            'departments',
        ));
    }

    public function create(): View
    {
        $staffMembers = Staff::query()
            ->with('advances')
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('pages.admin.payroll.create', compact('staffMembers'));
    }

    public function getStaffDetails(Request $request): \Illuminate\Http\JsonResponse
    {
        $staffId   = (int) $request->integer('staff_id');
        $month     = trim((string) $request->string('month'));
        $payrollId = (int) $request->integer('payroll_id', 0);

        if (!$staffId) {
            return response()->json(['success' => false, 'message' => 'Staff ID is required']);
        }

        $staff = Staff::find($staffId);

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff member not found']);
        }

        $existingPayroll = null;
        if (!empty($month)) {
            $existingQuery = Payroll::where('staff_id', $staffId)
                ->where('payroll_month', $month);
            if ($payrollId > 0) {
                $existingQuery->where('id', '!=', $payrollId);
            }
            $existingPayroll = $existingQuery->first();
        }

        $unpaidAdvances = \App\Models\StaffAdvance::where('staff_id', $staffId)
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->filter(function ($adv) {
                return (float) $adv->advance_amount - (float) $adv->repaid_amount > 0;
            })
            ->values();

        $totalUnpaidAdvance = $unpaidAdvances->sum(function ($adv) {
            return max(0, (float) $adv->advance_amount - (float) $adv->repaid_amount);
        });

        $formattedAdvances = $unpaidAdvances->map(function ($adv) {
            $balance = max(0, (float) $adv->advance_amount - (float) $adv->repaid_amount);
            return [
                'id'             => $adv->id,
                'voucher_no'     => 'ADV-' . str_pad($adv->id, 5, '0', STR_PAD_LEFT),
                'advance_date'   => $adv->advance_date ? $adv->advance_date->format('d M, Y') : 'N/A',
                'reason'         => $adv->reason ?? 'Personal Loan / Advance',
                'advance_amount' => (float) $adv->advance_amount,
                'repaid_amount'  => (float) $adv->repaid_amount,
                'unpaid_balance' => $balance,
                'status'         => is_object($adv->status) ? $adv->status->value : $adv->status,
            ];
        });

        $suggestedDeduction = min($totalUnpaidAdvance, (float)($staff->salary ?? 0));

        return response()->json([
            'success'              => true,
            'staff'                => [
                'id'                => $staff->id,
                'full_name'         => $staff->full_name,
                'staff_code'        => $staff->staff_id ? 'STF-' . $staff->staff_id : 'STF-' . $staff->id,
                'salary'            => (float) ($staff->salary ?? 0),
                'department'        => $staff->formatted_department,
                'designation'       => $staff->formatted_designation,
                'bank_name'         => $staff->bank_name ?? 'N/A',
                'account_number'    => $staff->bank_account_number ?? 'N/A',
                'mobile_no'         => $staff->mobile_no ?? 'N/A',
            ],
            'existing_payroll'     => $existingPayroll ? [
                'id'            => $existingPayroll->id,
                'payroll_month' => $existingPayroll->payroll_month,
                'status'        => strtoupper($existingPayroll->status),
                'net_salary'    => (float) $existingPayroll->net_salary,
                'formatted_net' => $existingPayroll->formatted_net_salary,
                'view_url'      => route('payroll.show', $existingPayroll->id),
            ] : null,
            'unpaid_advances'      => $formattedAdvances,
            'total_unpaid_advance' => $totalUnpaidAdvance,
            'suggested_deduction'  => $suggestedDeduction,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id'       => ['required', 'exists:staff,id'],
            'payroll_month'  => ['required', 'string', 'max:20'],
            'basic_salary'   => ['required', 'numeric', 'min:0'],
            'allowance'      => ['nullable', 'numeric', 'min:0'],
            'deduction'      => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,cheque'],
            'payment_date'   => ['nullable', 'date'],
            'status'         => ['required', 'string', 'in:paid,pending,cancelled'],
            'notes'          => ['nullable', 'string'],
        ]);

        $validated['allowance'] = $validated['allowance'] ?? 0;
        $validated['deduction'] = $validated['deduction'] ?? 0;

        $existing = Payroll::query()
            ->where('staff_id', $validated['staff_id'])
            ->where('payroll_month', $validated['payroll_month'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['payroll_month' => 'Payroll record for this staff member and month already exists.']);
        }

        $payroll = Payroll::create($validated);
        $this->processAdvanceRepaymentOnPayrollPaid($payroll);

        return redirect()
            ->route('payroll.show', $payroll->id)
            ->with('success', 'Payroll record created successfully.');
    }

    public function show(int $id): View
    {
        $payroll = Payroll::with('staff')->findOrFail($id);

        return view('pages.admin.payroll.show', compact('payroll'));
    }

    public function edit(int $id): View
    {
        $payroll = Payroll::with('staff')->findOrFail($id);
        $staffMembers = Staff::query()->orderBy('first_name')->get();

        return view('pages.admin.payroll.edit', compact('payroll', 'staffMembers'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $payroll = Payroll::findOrFail($id);

        $validated = $request->validate([
            'staff_id'       => ['required', 'exists:staff,id'],
            'payroll_month'  => ['required', 'string', 'max:20'],
            'basic_salary'   => ['required', 'numeric', 'min:0'],
            'allowance'      => ['nullable', 'numeric', 'min:0'],
            'deduction'      => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,cheque'],
            'payment_date'   => ['nullable', 'date'],
            'status'         => ['required', 'string', 'in:paid,pending,cancelled'],
            'notes'          => ['nullable', 'string'],
        ]);

        $validated['allowance'] = $validated['allowance'] ?? 0;
        $validated['deduction'] = $validated['deduction'] ?? 0;

        $payroll->update($validated);
        $this->processAdvanceRepaymentOnPayrollPaid($payroll);

        return redirect()
            ->route('payroll.index')
            ->with('success', 'Payroll record updated successfully.');
    }

    public function statement(Request $request): View
    {
        $startDate   = trim((string) $request->string('start_date'));
        $endDate     = trim((string) $request->string('end_date'));
        $staffId     = (int) $request->integer('staff_id', 0);
        $department  = trim((string) $request->string('department', 'all'));
        $status      = trim((string) $request->string('status', 'all'));
        $month       = trim((string) $request->string('month', 'all'));
        $method      = trim((string) $request->string('method', 'all'));

        $query = Payroll::query()->with('staff')->latest('payment_date')->latest('id');

        if ($startDate !== '') {
            $query->where(function ($q) use ($startDate) {
                $q->whereDate('payment_date', '>=', $startDate)
                  ->orWhereDate('created_at', '>=', $startDate);
            });
        }

        if ($endDate !== '') {
            $query->where(function ($q) use ($endDate) {
                $q->whereDate('payment_date', '<=', $endDate)
                  ->orWhereDate('created_at', '<=', $endDate);
            });
        }

        if ($staffId > 0) {
            $query->where('staff_id', $staffId);
        }

        if ($department !== 'all') {
            $query->whereHas('staff', function (Builder $b) use ($department) {
                $b->where('department', $department);
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($month !== 'all') {
            $query->where('payroll_month', $month);
        }

        if ($method !== 'all') {
            $query->where('payment_method', $method);
        }

        $payrolls = $query->get();

        $totalBasic      = $payrolls->sum('basic_salary');
        $totalAllowance  = $payrolls->sum('allowance');
        $totalDeduction  = $payrolls->sum('deduction');
        $totalNetSalary  = $payrolls->sum('net_salary');
        $paidAmount      = $payrolls->where('status', 'paid')->sum('net_salary');
        $pendingAmount   = $payrolls->where('status', 'pending')->sum('net_salary');
        $cancelledAmount = $payrolls->where('status', 'cancelled')->sum('net_salary');

        $selectedStaff = $staffId > 0 ? Staff::find($staffId) : null;
        $staffMembers  = Staff::query()->where('status', 'active')->orderBy('first_name')->get();
        $departments   = Staff::query()->distinct()->pluck('department')->filter()->values();
        $months        = Payroll::query()->distinct()->pluck('payroll_month')->sortDesc()->values();

        return view('pages.admin.payroll.statement', compact(
            'payrolls',
            'startDate',
            'endDate',
            'staffId',
            'selectedStaff',
            'department',
            'status',
            'month',
            'method',
            'totalBasic',
            'totalAllowance',
            'totalDeduction',
            'totalNetSalary',
            'paidAmount',
            'pendingAmount',
            'cancelledAmount',
            'staffMembers',
            'departments',
            'months'
        ));
    }

    public function printStatement(Request $request): View
    {
        $startDate   = trim((string) $request->string('start_date'));
        $endDate     = trim((string) $request->string('end_date'));
        $staffId     = (int) $request->integer('staff_id', 0);
        $department  = trim((string) $request->string('department', 'all'));
        $status      = trim((string) $request->string('status', 'all'));
        $month       = trim((string) $request->string('month', 'all'));
        $method      = trim((string) $request->string('method', 'all'));

        $query = Payroll::query()->with('staff')->latest('payment_date')->latest('id');

        if ($startDate !== '') {
            $query->where(function ($q) use ($startDate) {
                $q->whereDate('payment_date', '>=', $startDate)
                  ->orWhereDate('created_at', '>=', $startDate);
            });
        }

        if ($endDate !== '') {
            $query->where(function ($q) use ($endDate) {
                $q->whereDate('payment_date', '<=', $endDate)
                  ->orWhereDate('created_at', '<=', $endDate);
            });
        }

        if ($staffId > 0) {
            $query->where('staff_id', $staffId);
        }

        if ($department !== 'all') {
            $query->whereHas('staff', function (Builder $b) use ($department) {
                $b->where('department', $department);
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($month !== 'all') {
            $query->where('payroll_month', $month);
        }

        if ($method !== 'all') {
            $query->where('payment_method', $method);
        }

        $payrolls = $query->get();

        $totalBasic      = $payrolls->sum('basic_salary');
        $totalAllowance  = $payrolls->sum('allowance');
        $totalDeduction  = $payrolls->sum('deduction');
        $totalNetSalary  = $payrolls->sum('net_salary');
        $paidAmount      = $payrolls->where('status', 'paid')->sum('net_salary');
        $pendingAmount   = $payrolls->where('status', 'pending')->sum('net_salary');
        $cancelledAmount = $payrolls->where('status', 'cancelled')->sum('net_salary');

        $selectedStaff = $staffId > 0 ? Staff::find($staffId) : null;

        return view('pages.admin.payroll.print-statement', compact(
            'payrolls',
            'startDate',
            'endDate',
            'staffId',
            'selectedStaff',
            'department',
            'status',
            'month',
            'method',
            'totalBasic',
            'totalAllowance',
            'totalDeduction',
            'totalNetSalary',
            'paidAmount',
            'pendingAmount',
            'cancelledAmount'
        ));
    }

    public function exportStatement(Request $request)
    {
        $startDate   = trim((string) $request->string('start_date'));
        $endDate     = trim((string) $request->string('end_date'));
        $staffId     = (int) $request->integer('staff_id', 0);
        $department  = trim((string) $request->string('department', 'all'));
        $status      = trim((string) $request->string('status', 'all'));
        $month       = trim((string) $request->string('month', 'all'));
        $method      = trim((string) $request->string('method', 'all'));

        $query = Payroll::query()->with('staff')->latest('payment_date')->latest('id');

        if ($startDate !== '') {
            $query->where(function ($q) use ($startDate) {
                $q->whereDate('payment_date', '>=', $startDate)
                  ->orWhereDate('created_at', '>=', $startDate);
            });
        }

        if ($endDate !== '') {
            $query->where(function ($q) use ($endDate) {
                $q->whereDate('payment_date', '<=', $endDate)
                  ->orWhereDate('created_at', '<=', $endDate);
            });
        }

        if ($staffId > 0) {
            $query->where('staff_id', $staffId);
        }

        if ($department !== 'all') {
            $query->whereHas('staff', function (Builder $b) use ($department) {
                $b->where('department', $department);
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($month !== 'all') {
            $query->where('payroll_month', $month);
        }

        if ($method !== 'all') {
            $query->where('payment_method', $method);
        }

        $payrolls = $query->get();

        $filename = 'payroll_statement_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payrolls) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Staff ID',
                'Staff Name',
                'Department',
                'Designation',
                'Payroll Month',
                'Basic Salary (PKR)',
                'Allowance (PKR)',
                'Deduction (PKR)',
                'Net Salary (PKR)',
                'Payment Status',
                'Payment Method',
                'Payment Date',
                'Bank Name',
                'Account Number',
            ]);

            foreach ($payrolls as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->staff ? $p->staff->staff_id : 'N/A',
                    $p->staff ? $p->staff->full_name : 'N/A',
                    $p->staff ? $p->staff->formatted_department : 'N/A',
                    $p->staff ? $p->staff->formatted_designation : 'N/A',
                    $p->payroll_month,
                    $p->basic_salary,
                    $p->allowance,
                    $p->deduction,
                    $p->net_salary,
                    strtoupper($p->status),
                    str_replace('_', ' ', $p->payment_method),
                    $p->payment_date ? $p->payment_date->format('Y-m-d') : 'Pending',
                    $p->staff && $p->staff->bank_name ? $p->staff->bank_name : 'N/A',
                    $p->staff && $p->staff->bank_account_number ? $p->staff->bank_account_number : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(int $id): RedirectResponse
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();

        return redirect()
            ->route('payroll.index')
            ->with('success', 'Payroll record moved to trash successfully.');
    }

    public function trash(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search  = trim((string) $request->string('search'));

        $query = Payroll::onlyTrashed()->with('staff')->latest('deleted_at');

        if ($search !== '') {
            $query->whereHas('staff', function (Builder $b) use ($search) {
                $b->where('staff_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $payrolls   = $query->paginate($perPage)->withQueryString();
        $trashCount = Payroll::onlyTrashed()->count();

        return view('pages.admin.payroll.trash', compact('payrolls', 'perPage', 'search', 'trashCount'));
    }

    public function restore(int $id): RedirectResponse
    {
        $payroll = Payroll::onlyTrashed()->findOrFail($id);
        $payroll->restore();

        return redirect()
            ->route('payroll.trash')
            ->with('success', 'Payroll record restored successfully.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $payroll = Payroll::onlyTrashed()->findOrFail($id);
        $payroll->forceDelete();

        return redirect()
            ->route('payroll.trash')
            ->with('success', 'Payroll record permanently deleted successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'selected_ids'   => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'min:1'],
            'bulk_action'    => ['required', 'in:trash,restore,force_delete,mark_paid'],
        ]);

        $ids    = Arr::map($validated['selected_ids'], fn ($id) => (int) $id);
        $action = $validated['bulk_action'];

        if ($action === 'trash') {
            Payroll::whereIn('id', $ids)->delete();

            return redirect()
                ->route('payroll.index')
                ->with('success', 'Selected payroll records moved to trash.');
        }

        if ($action === 'restore') {
            Payroll::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()
                ->route('payroll.trash')
                ->with('success', 'Selected payroll records restored.');
        }

        if ($action === 'force_delete') {
            Payroll::onlyTrashed()->whereIn('id', $ids)->forceDelete();

            return redirect()
                ->route('payroll.trash')
                ->with('success', 'Selected payroll records permanently deleted.');
        }

        if ($action === 'mark_paid') {
            $payrollsToMark = Payroll::whereIn('id', $ids)->get();
            foreach ($payrollsToMark as $p) {
                $p->update([
                    'status' => 'paid',
                    'payment_date' => now()->toDateString(),
                ]);
                $this->processAdvanceRepaymentOnPayrollPaid($p);
            }

            return redirect()
                ->route('payroll.index')
                ->with('success', 'Selected payroll records marked as Paid.');
        }

        return redirect()->back();
    }

    public function generateMonthly(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_month' => ['required', 'string', 'max:20'],
        ]);

        $targetMonth = $validated['target_month'];
        $staffMembers = Staff::query()->where('status', 'active')->get();

        $generatedCount = 0;
        $skippedCount   = 0;

        foreach ($staffMembers as $staff) {
            $exists = Payroll::query()
                ->where('staff_id', $staff->id)
                ->where('payroll_month', $targetMonth)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            $salary = (float) ($staff->salary ?: 40000);
            $advanceInstallment = $staff->active_advance_installment;
            $notes = "Auto-generated monthly payroll for {$targetMonth}";
            if ($advanceInstallment > 0) {
                $notes .= " (Includes salary advance deduction: Rs. " . number_format($advanceInstallment, 2) . ")";
            }

            Payroll::create([
                'staff_id'       => $staff->id,
                'payroll_month'  => $targetMonth,
                'basic_salary'   => $salary,
                'allowance'      => 0,
                'deduction'      => $advanceInstallment,
                'payment_method' => 'bank_transfer',
                'payment_date'   => null,
                'status'         => 'pending',
                'notes'          => $notes,
            ]);

            $generatedCount++;
        }

        return redirect()
            ->route('payroll.index')
            ->with('success', "Batch processing complete: {$generatedCount} payroll entries created for {$targetMonth}. ({$skippedCount} already existed).");
    }

    protected function processAdvanceRepaymentOnPayrollPaid(Payroll $payroll): void
    {
        if ($payroll->status === 'cancelled' || (float) $payroll->deduction <= 0) {
            return;
        }

        // Avoid creating duplicate repayments for the same payroll
        $alreadyProcessed = \App\Models\StaffAdvanceRepayment::where('payroll_id', $payroll->id)->exists();
        if ($alreadyProcessed) {
            return;
        }

        $deduction = (float) $payroll->deduction;

        // 1. Create a single repayment transaction for the staff member with full deduction amount
        \App\Models\StaffAdvanceRepayment::create([
            'staff_advance_id' => null,
            'staff_id'         => $payroll->staff_id,
            'payroll_id'       => $payroll->id,
            'amount'           => $deduction,
            'repayment_date'   => $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : date('Y-m-d'),
            'repayment_type'   => 'payroll_deduction',
            'notes'            => 'Auto-deducted from payroll for month ' . $payroll->payroll_month,
        ]);

        // 2. Update individual advance balances in FIFO order
        $advances = \App\Models\StaffAdvance::where('staff_id', $payroll->staff_id)
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('id', 'asc')
            ->get();

        $remainingDeduction = $deduction;
        foreach ($advances as $advance) {
            if ($remainingDeduction <= 0) {
                break;
            }
            $unpaidBalance = max(0, (float) $advance->advance_amount - (float) $advance->repaid_amount);
            $maxRepay = min($remainingDeduction, $unpaidBalance);
            if ($maxRepay > 0) {
                $advance->repaid_amount = (float) $advance->repaid_amount + $maxRepay;
                if ((float) $advance->repaid_amount >= (float) $advance->advance_amount) {
                    $advance->status = \App\Enum\StaffAdvanceStatusEnum::FULLY_REPAID;
                }
                $advance->save();
                $remainingDeduction -= $maxRepay;
            }
        }
    }
}
