<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAdvance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffAdvanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = StaffAdvance::with('staff');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $advances = $query->orderBy('advance_date', 'desc')->paginate(15)->withQueryString();

        $allAdvances = StaffAdvance::all();
        $totalAdvanced = $allAdvances->sum('advance_amount');
        $totalRepaid = $allAdvances->sum('repaid_amount');
        $outstandingBalance = max(0, $totalAdvanced - $totalRepaid);
        $activeCount = $allAdvances->whereIn('status', ['approved'])->count();

        $trashCount = StaffAdvance::onlyTrashed()->count();

        return view('pages.admin.staff_advances.index', compact(
            'advances',
            'totalAdvanced',
            'totalRepaid',
            'outstandingBalance',
            'activeCount',
            'trashCount'
        ));
    }

    public function create(Request $request): View
    {
        $staffList = Staff::with('advances')->orderBy('first_name')->get();
        $selectedStaffId = $request->get('staff_id');
        return view('pages.admin.staff_advances.create', compact('staffList', 'selectedStaffId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id'            => 'required|exists:staff,id',
            'advance_amount'      => 'required|numeric|min:1',
            'advance_date'        => 'required|date',
            'payment_method'      => 'required|string|in:cash,bank_transfer,cheque',
            'status'              => 'required|in:pending,approved,fully_repaid,rejected,cancelled',
            'reason'              => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
        ]);

        $validated['repaid_amount'] = 0;
        $validated['monthly_installment'] = 0;

        $advance = StaffAdvance::create($validated);

        return redirect()->route('staff-advances.show', $advance->id)
            ->with('success', 'Staff salary advance recorded successfully.');
    }

    public function show($id): View
    {
        $advance = StaffAdvance::with('staff')->findOrFail($id);

        $staffAdvances = StaffAdvance::where('staff_id', $advance->staff_id)
            ->orderBy('advance_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $overallTotal = $staffAdvances->sum('advance_amount');
        $overallRepaid = $staffAdvances->sum('repaid_amount');
        $overallPending = max(0, $overallTotal - $overallRepaid);

        return view('pages.admin.staff_advances.show', compact(
            'advance',
            'staffAdvances',
            'overallTotal',
            'overallRepaid',
            'overallPending'
        ));
    }

    public function edit($id): View
    {
        $advance = StaffAdvance::findOrFail($id);
        $staffList = Staff::orderBy('first_name')->get();
        return view('pages.admin.staff_advances.edit', compact('advance', 'staffList'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $advance = StaffAdvance::findOrFail($id);

        $validated = $request->validate([
            'staff_id'            => 'required|exists:staff,id',
            'advance_amount'      => 'required|numeric|min:1',
            'repaid_amount'       => 'required|numeric|min:0|max:' . $request->advance_amount,
            'advance_date'        => 'required|date',
            'payment_method'      => 'required|string|in:cash,bank_transfer,cheque',
            'status'              => 'required|in:pending,approved,fully_repaid,rejected,cancelled',
            'reason'              => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
        ]);

        $advance->update($validated);

        return redirect()->route('staff-advances.index')
            ->with('success', 'Salary advance updated successfully.');
    }

    public function recordRepayment(Request $request, $id): RedirectResponse
    {
        $advance = StaffAdvance::findOrFail($id);

        $maxRepayable = max(0, (float) $advance->advance_amount - (float) $advance->repaid_amount);

        $validated = $request->validate([
            'repayment_amount' => 'required|numeric|min:0.01|max:' . $maxRepayable,
        ]);

        $newRepaid = (float) $advance->repaid_amount + (float) $validated['repayment_amount'];
        $advance->repaid_amount = $newRepaid;

        if ($advance->repaid_amount >= (float) $advance->advance_amount) {
            $advance->status = \App\Enum\StaffAdvanceStatusEnum::FULLY_REPAID;
        }

        $advance->save();

        return redirect()->back()
            ->with('success', 'Repayment of Rs. ' . number_format($validated['repayment_amount'], 2) . ' recorded successfully.');
    }

    public function print($id): View
    {
        $advance    = StaffAdvance::with('staff')->findOrFail($id);
        $schoolInfo = \App\Models\SchoolInfo::first();

        return view('pages.admin.staff_advances.print', compact('advance', 'schoolInfo'));
    }

    public function destroy($id): RedirectResponse
    {
        $advance = StaffAdvance::findOrFail($id);
        $advance->delete();

        return redirect()->route('staff-advances.index')
            ->with('success', 'Salary advance record moved to trash.');
    }

    public function trash(): View
    {
        $advances = StaffAdvance::onlyTrashed()->with('staff')->orderBy('deleted_at', 'desc')->paginate(15);
        return view('pages.admin.staff_advances.trash', compact('advances'));
    }

    public function restore($id): RedirectResponse
    {
        $advance = StaffAdvance::onlyTrashed()->findOrFail($id);
        $advance->restore();

        return redirect()->route('staff-advances.trash')
            ->with('success', 'Salary advance restored successfully.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $advance = StaffAdvance::onlyTrashed()->findOrFail($id);
        $advance->forceDelete();

        return redirect()->route('staff-advances.trash')
            ->with('success', 'Salary advance permanently deleted.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'selected_ids'   => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'min:1'],
            'bulk_action'    => ['required', 'in:trash,restore,force_delete'],
        ]);

        $ids = array_map('intval', $validated['selected_ids']);
        $action = $validated['bulk_action'];

        if ($action === 'trash') {
            StaffAdvance::whereIn('id', $ids)->delete();

            return redirect()->route('staff-advances.index')
                ->with('success', count($ids) . ' salary advance record(s) moved to trash.');
        }

        if ($action === 'restore') {
            StaffAdvance::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()->route('staff-advances.trash')
                ->with('success', count($ids) . ' salary advance record(s) restored successfully.');
        }

        if ($action === 'force_delete') {
            StaffAdvance::onlyTrashed()->whereIn('id', $ids)->forceDelete();

            return redirect()->route('staff-advances.trash')
                ->with('success', count($ids) . ' salary advance record(s) permanently deleted.');
        }

        return redirect()->back();
    }
}
