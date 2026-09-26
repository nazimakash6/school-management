<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreStaffRequest;
use App\Http\Requests\Staff\UpdateStaffRequest;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search = trim((string) $request->string('search'));
        $department = trim((string) $request->string('department', 'all'));
        $status = trim((string) $request->string('status', 'all'));

        if (! in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        $departments = Staff::query()
            ->select('department')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        if ($department !== 'all' && ! $departments->contains($department)) {
            $department = 'all';
        }

        if (! in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $query = Staff::query()->latest();
        $this->applyFilters($query, $search, $department, $status);

        $staffMembers = $query->paginate($perPage)->withQueryString();

        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'active')->count();
        $inactiveStaff = Staff::where('status', 'inactive')->count();
        $totalPayroll = Staff::where('status', 'active')->sum('salary');

        return view('pages.admin.staff.index', compact(
            'staffMembers',
            'perPage',
            'search',
            'department',
            'status',
            'departments',
            'totalStaff',
            'activeStaff',
            'inactiveStaff',
            'totalPayroll'
        ));
    }

    public function trash(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search = trim((string) $request->string('search'));
        $department = trim((string) $request->string('department', 'all'));
        $status = trim((string) $request->string('status', 'all'));

        if (! in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        $departments = Staff::onlyTrashed()
            ->select('department')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        if ($department !== 'all' && ! $departments->contains($department)) {
            $department = 'all';
        }

        if (! in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $query = Staff::onlyTrashed()->latest('deleted_at');
        $this->applyFilters($query, $search, $department, $status);

        $staffMembers = $query->paginate($perPage)->withQueryString();
        $trashCount = Staff::onlyTrashed()->count('*');
        $activeCount = Staff::query()->count('*');
        $trashedLast30Days = Staff::onlyTrashed()
            ->whereDate('deleted_at', '>=', now()->subDays(30)->toDateString())
            ->count('*');

        return view('pages.admin.staff.trash', compact(
            'staffMembers',
            'perPage',
            'search',
            'department',
            'status',
            'departments',
            'trashCount',
            'activeCount',
            'trashedLast30Days',
        ));
    }

    public function create(): View
    {
        return view('pages.admin.staff.create');
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cv')) {
            $data['cv'] = $request->file('cv')->store('staff/cv', 'public');
        }

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('staff/profile-pictures', 'public');
        }

        do {
            $staffId = 'SF-' . random_int(1000, 9999);
        } while (Staff::query()->where('staff_id', $staffId)->exists());

        $data['staff_id'] = $staffId;

        Staff::query()->create($data);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff created successfully.');
    }

    public function show(Staff $staff): View
    {
        return view('pages.admin.staff.show', compact('staff'));
    }

    public function print(Staff $staff): View
    {
        return view('pages.admin.staff.print', compact('staff'));
    }

    public function edit(Staff $staff): View
    {
        return view('pages.admin.staff.edit', compact('staff'));
    }

    public function update(UpdateStaffRequest $request, Staff $staff): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cv')) {
            if ($staff->cv) {
                Storage::disk('public')->delete($staff->cv);
            }

            $data['cv'] = $request->file('cv')->store('staff/cv', 'public');
        }

        if ($request->hasFile('profile_picture')) {
            if ($staff->profile_picture) {
                Storage::disk('public')->delete($staff->profile_picture);
            }

            $data['profile_picture'] = $request->file('profile_picture')->store('staff/profile-pictures', 'public');
        }

        $staff->update($data);

        return redirect()
            ->route('staff.show', $staff)
            ->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        Staff::query()->whereKey($staff->getKey())->delete();

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff moved to trash successfully.');
    }

    public function restore(int $id): RedirectResponse
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);
        $staff->restore();

        return redirect()
            ->route('staff.trash')
            ->with('success', 'Staff restored successfully.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $staff = Staff::onlyTrashed()->findOrFail($id);

        if ($staff->cv) {
            Storage::disk('public')->delete($staff->cv);
        }

        if ($staff->profile_picture) {
            Storage::disk('public')->delete($staff->profile_picture);
        }

        $staff->forceDelete();

        return redirect()
            ->route('staff.trash')
            ->with('success', 'Staff permanently deleted successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'min:1'],
            'bulk_action' => ['required', 'in:trash,restore,force_delete'],
        ]);

        $ids = Arr::map($validated['selected_ids'], fn ($id) => (int) $id);
        $action = $validated['bulk_action'];

        if ($action === 'trash') {
            Staff::query()->whereKey($ids)->delete();

            return redirect()
                ->route('staff.index')
                ->with('success', 'Selected staff records moved to trash successfully.');
        }

        if ($action === 'restore') {
            Staff::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()
                ->route('staff.trash')
                ->with('success', 'Selected staff records restored successfully.');
        }

        $staffMembers = Staff::onlyTrashed()->whereIn('id', $ids)->get();

        foreach ($staffMembers as $staff) {
            if ($staff->cv) {
                Storage::disk('public')->delete($staff->cv);
            }

            if ($staff->profile_picture) {
                Storage::disk('public')->delete($staff->profile_picture);
            }
        }

        Staff::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return redirect()
            ->route('staff.trash')
            ->with('success', 'Selected staff records permanently deleted successfully.');
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $search = trim((string) $request->string('search'));
        $department = trim((string) $request->string('department', 'all'));
        $status = trim((string) $request->string('status', 'all'));

        $query = Staff::query()->latest();
        $this->applyFilters($query, $search, $department, $status);
        $staffMembers = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="staff_records_' . date('Y_m_d_His') . '.csv"',
        ];

        $callback = function () use ($staffMembers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Staff ID', 'First Name', 'Last Name', 'Gender', 'DOB', 'CNIC',
                'Marital Status', 'Blood Group', 'Religion', 'Nationality', 'Mobile No',
                'Alternate Mobile No', 'Email', 'Current Address', 'Permanent Address',
                'Joining Date', 'Leaving Date', 'Emergency Contact Name', 'Emergency Contact Number',
                'Emergency Contact Relation', 'Department', 'Designation', 'Qualification',
                'Experience', 'Employment Type', 'Shift', 'Salary', 'Salary Type',
                'Bank Name', 'Bank Account Title', 'Bank Account Number', 'IBAN',
                'Status', 'Note'
            ]);

            foreach ($staffMembers as $staff) {
                fputcsv($file, [
                    $staff->staff_id,
                    $staff->first_name,
                    $staff->last_name,
                    $staff->gender,
                    $staff->dob,
                    $staff->cnic,
                    $staff->marital_status,
                    $staff->blood_group,
                    $staff->religion,
                    $staff->nationality,
                    $staff->mobile_no,
                    $staff->alternate_mobile_no,
                    $staff->email,
                    $staff->current_address,
                    $staff->permanent_address,
                    $staff->joining_date,
                    $staff->leaving_date,
                    $staff->emergency_contact_name,
                    $staff->emergency_contact_number,
                    $staff->emergency_contact_relation,
                    $staff->department,
                    $staff->designation,
                    $staff->qualification,
                    $staff->experience,
                    $staff->employment_type,
                    $staff->shift,
                    $staff->salary,
                    $staff->salary_type,
                    $staff->bank_name,
                    $staff->bank_account_title,
                    $staff->bank_account_number,
                    $staff->iban,
                    $staff->status,
                    $staff->note,
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'staff_records_' . date('Y_m_d_His') . '.csv', $headers);
    }

    public function sampleCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="staff_sample_import.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Staff ID', 'First Name', 'Last Name', 'Gender', 'DOB', 'CNIC',
                'Marital Status', 'Blood Group', 'Religion', 'Nationality', 'Mobile No',
                'Alternate Mobile No', 'Email', 'Current Address', 'Permanent Address',
                'Joining Date', 'Leaving Date', 'Emergency Contact Name', 'Emergency Contact Number',
                'Emergency Contact Relation', 'Department', 'Designation', 'Qualification',
                'Experience', 'Employment Type', 'Shift', 'Salary', 'Salary Type',
                'Bank Name', 'Bank Account Title', 'Bank Account Number', 'IBAN',
                'Status', 'Note'
            ]);

            fputcsv($file, [
                'SF-9901', 'Ali', 'Khan', 'male', '1990-05-15', '35201-1111111-1',
                'married', 'A+', 'Islam', 'Pakistani', '03001111111',
                '03211111111', 'ali.khan@example.com', 'House 1, Lahore', 'House 1, Lahore',
                '2022-01-01', '', 'Kamran Khan', '03002222222',
                'Father', 'teaching', 'teacher', 'M.Sc Physics',
                '4 Years', 'full_time', 'Morning', '65000', 'monthly',
                'HBL', 'Ali Khan', '1234567890', 'PK36HABB1234567890',
                'active', 'Sample staff record'
            ]);

            fclose($file);
        };

        return response()->streamDownload($callback, 'staff_sample_import.csv', $headers);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimetypes:text/csv,text/plain,application/csv,text/comma-separated-values,application/excel,application/vnd.ms-excel,application/vnd.msexcel'],
        ]);

        $file = $request->file('import_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return redirect()->back()->withErrors(['import_file' => 'Failed to open CSV file.']);
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return redirect()->back()->withErrors(['import_file' => 'CSV file is empty or invalid.']);
        }

        $headerMap = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '_', $h), '_'));
        }, $header);

        $importedCount = 0;
        $errorsCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 1 && empty($row[0])) {
                continue;
            }

            $rowData = [];
            foreach ($headerMap as $index => $key) {
                $rowData[$key] = isset($row[$index]) ? trim($row[$index]) : null;
            }

            $firstName = $rowData['first_name'] ?? $rowData['first'] ?? null;
            if (empty($firstName)) {
                $errorsCount++;
                continue;
            }

            $staffId = $rowData['staff_id'] ?? null;
            if (empty($staffId) || Staff::query()->where('staff_id', $staffId)->exists()) {
                do {
                    $staffId = 'SF-' . random_int(1000, 9999);
                } while (Staff::query()->where('staff_id', $staffId)->exists());
            }

            $gender = strtolower($rowData['gender'] ?? 'male');
            if (! in_array($gender, ['male', 'female', 'other'], true)) {
                $gender = 'male';
            }

            $status = strtolower($rowData['status'] ?? 'active');
            if (! in_array($status, ['active', 'inactive'], true)) {
                $status = 'active';
            }

            Staff::query()->create([
                'staff_id' => $staffId,
                'first_name' => $firstName,
                'last_name' => $rowData['last_name'] ?? null,
                'gender' => $gender,
                'dob' => ! empty($rowData['dob']) ? date('Y-m-d', strtotime($rowData['dob'])) : '1995-01-01',
                'cnic' => $rowData['cnic'] ?? '35201-0000000-0',
                'marital_status' => strtolower($rowData['marital_status'] ?? 'single'),
                'blood_group' => $rowData['blood_group'] ?? null,
                'religion' => $rowData['religion'] ?? 'Islam',
                'nationality' => $rowData['nationality'] ?? 'Pakistani',
                'mobile_no' => $rowData['mobile_no'] ?? '03000000000',
                'alternate_mobile_no' => $rowData['alternate_mobile_no'] ?? null,
                'email' => ! empty($rowData['email']) ? $rowData['email'] : 'staff_' . uniqid() . '@example.com',
                'current_address' => $rowData['current_address'] ?? 'N/A',
                'permanent_address' => $rowData['permanent_address'] ?? 'N/A',
                'joining_date' => ! empty($rowData['joining_date']) ? date('Y-m-d', strtotime($rowData['joining_date'])) : date('Y-m-d'),
                'leaving_date' => ! empty($rowData['leaving_date']) ? date('Y-m-d', strtotime($rowData['leaving_date'])) : null,
                'emergency_contact_name' => $rowData['emergency_contact_name'] ?? 'N/A',
                'emergency_contact_number' => $rowData['emergency_contact_number'] ?? '03000000000',
                'emergency_contact_relation' => $rowData['emergency_contact_relation'] ?? 'Relative',
                'department' => strtolower($rowData['department'] ?? 'administration'),
                'designation' => strtolower($rowData['designation'] ?? 'teacher'),
                'qualification' => $rowData['qualification'] ?? 'N/A',
                'experience' => $rowData['experience'] ?? null,
                'employment_type' => strtolower($rowData['employment_type'] ?? 'full_time'),
                'shift' => $rowData['shift'] ?? 'Morning',
                'salary' => ! empty($rowData['salary']) ? (float) $rowData['salary'] : 0,
                'salary_type' => strtolower($rowData['salary_type'] ?? 'monthly'),
                'bank_name' => $rowData['bank_name'] ?? null,
                'bank_account_title' => $rowData['bank_account_title'] ?? null,
                'bank_account_number' => $rowData['bank_account_number'] ?? null,
                'iban' => $rowData['iban'] ?? null,
                'status' => $status,
                'note' => $rowData['note'] ?? null,
            ]);

            $importedCount++;
        }

        fclose($handle);

        $message = "Successfully imported {$importedCount} staff records.";
        if ($errorsCount > 0) {
            $message .= " {$errorsCount} rows were skipped due to missing required data.";
        }

        return redirect()->route('staff.index')->with('success', $message);
    }

    private function applyFilters(Builder $query, string $search, string $department, string $status): void
    {
        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('staff_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        if ($department !== 'all') {
            $query->where('department', $department);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }
    }
}
