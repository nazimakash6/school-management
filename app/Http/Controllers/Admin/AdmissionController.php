<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admission\StoreAdmissionRequest;
use App\Http\Requests\Admission\UpdateAdmissionRequest;
use App\Models\AcademicSession;
use App\Models\Admission;
use App\Models\StudentClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status', 'all'));
        $month = trim((string) $request->string('month', 'all'));
        $recent = trim((string) $request->string('recent', 'overall'));
        $className = trim((string) $request->string('class_name', 'all'));

        if (!in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        if (!in_array($status, ['all', 'active', 'pending', 'inactive'], true)) {
            $status = 'all';
        }

        $allowedRecentRanges = ['overall', '1m', '2m', '3m', '6m', '9m', '12m', '2y', '3y', '5y', '6y', '7y', '8y', '9y'];
        if (!in_array($recent, $allowedRecentRanges, true)) {
            $recent = 'overall';
        }

        $availableClasses = StudentClass::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->pluck('name');

        if ($className !== 'all' && !$availableClasses->contains($className)) {
            $className = 'all';
        }

        $query = Admission::query()->latest('admission_date')->latest();

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('admission_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('guardian_name', 'like', "%{$search}%")
                    ->orWhere('class_name', 'like', "%{$search}%")
                    ->orWhere('student_mobile_no', 'like', "%{$search}%")
                    ->orWhere('guardian_primary_mobile_no', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('admission_status', $status);
        }

        if ($className !== 'all') {
            $query->where('class_name', $className);
        }

        if ($month !== 'all') {
            $monthNumber = (int) $month;
            if ($monthNumber >= 1 && $monthNumber <= 12) {
                $query->whereMonth('admission_date', '=', $monthNumber, 'and');
            }
        }

        $this->applyRecentRangeFilter($query, $recent);

        $admissions = $query
            ->paginate($perPage)
            ->withQueryString();

        $totalAdmissions = Admission::query()->count('*');
        $activeAdmissions = Admission::query()->where('admission_status', 'active')->count('*');
        $pendingAdmissions = Admission::query()->where('admission_status', 'pending')->count('*');
        $selectedMonthCount = $this->resolveSelectedMonthCount($month);

        return view('pages.admin.admission.index', compact(
            'admissions',
            'perPage',
            'search',
            'status',
            'month',
            'recent',
            'className',
            'availableClasses',
            'totalAdmissions',
            'activeAdmissions',
            'pendingAdmissions',
            'selectedMonthCount',
        ));
    }

    public function trash(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status', 'all'));

        if (!in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        if (!in_array($status, ['all', 'active', 'pending', 'inactive'], true)) {
            $status = 'all';
        }

        $query = Admission::onlyTrashed()->latest('deleted_at');

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('admission_no', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%")
                    ->orWhere('class_name', 'like', "%{$search}%")
                    ->orWhere('contact_no', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $admissions = $query->paginate($perPage)->withQueryString();

        $trashCount = Admission::onlyTrashed()->count('*');
        $activeCount = Admission::query()->count('*');
        $trashedLast30Days = Admission::onlyTrashed()
            ->whereDate('deleted_at', '>=', now()->subDays(30)->toDateString())
            ->count('*');

        return view('pages.admin.admission.trash', compact(
            'admissions',
            'perPage',
            'search',
            'status',
            'trashCount',
            'activeCount',
            'trashedLast30Days',
        ));
    }

    public function create(): View
    {
        $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        $academicSessions = AcademicSession::where('status', 'Active')->orderBy('start_date', 'desc')->get();
        $groupsList = \App\Models\Group::where('status', 'active')->orderBy('name')->get();
        $existingStudents = \App\Models\Student::orderBy('first_name')->orderBy('last_name')->get();
        return view('pages.admin.admission.create', compact('classes', 'academicSessions', 'groupsList', 'existingStudents'));
    }

    public function store(StoreAdmissionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['group']) && !empty($data['group_name'])) {
            $data['group'] = $data['group_name'];
        } elseif (empty($data['group_name']) && !empty($data['group'])) {
            $data['group_name'] = $data['group'];
        }

        if (empty($data['guardian_name']) && !empty($data['father_name'])) {
            $data['guardian_name'] = $data['father_name'];
        }

        if (empty($data['guardian_primary_mobile_no']) && !empty($data['father_phone'])) {
            $data['guardian_primary_mobile_no'] = $data['father_phone'];
        }

        if (empty($data['guardian_address']) && !empty($data['current_address'])) {
            $data['guardian_address'] = $data['current_address'];
        }

        if ($request->hasFile('student_photo')) {
            $data['student_photo'] = $request->file('student_photo')->store('student_photos', 'public');
        }

        if ($request->hasFile('attached_documents')) {
            $attachedPaths = [];
            foreach ($request->file('attached_documents') as $file) {
                if ($file && $file->isValid()) {
                    $attachedPaths[] = $file->store('attached_documents', 'public');
                }
            }
            $data['attached_documents'] = $attachedPaths;
        }

        $admission = Admission::query()->create($data);

        return redirect()
            ->route('admission.print', $admission)
            ->with('status', 'Admission created successfully.');
    }

    public function print(Admission $admission): View
    {
        $admission->load('academicSession');
        return view('pages.admin.admission.print', compact('admission'));
    }

    public function blankForm(): View
    {
        $admission = new Admission();
        $isBlank = true;
        return view('pages.admin.admission.print', compact('admission', 'isBlank'));
    }

    public function show(Admission $admission): View
    {
        return view('pages.admin.admission.show', compact('admission'));
    }

    public function edit(Admission $admission): View
    {
        $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        $academicSessions = AcademicSession::where('status', 'Active')->orderBy('start_date', 'desc')->get();
        $groupsList = \App\Models\Group::where('status', 'active')->orderBy('name')->get();
        $student = \App\Models\Student::where('admission_no', $admission->admission_no)->first();
        $existingStudents = \App\Models\Student::when($student, fn($q) => $q->where('id', '!=', $student->id))->orderBy('first_name')->orderBy('last_name')->get();
        return view('pages.admin.admission.edit', compact('admission', 'classes', 'academicSessions', 'groupsList', 'existingStudents'));
    }

    public function update(UpdateAdmissionRequest $request, Admission $admission): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['group']) && !empty($data['group_name'])) {
            $data['group'] = $data['group_name'];
        } elseif (empty($data['group_name']) && !empty($data['group'])) {
            $data['group_name'] = $data['group'];
        }

        if (empty($data['guardian_name']) && !empty($data['father_name'])) {
            $data['guardian_name'] = $data['father_name'];
        }

        if (empty($data['guardian_primary_mobile_no']) && !empty($data['father_phone'])) {
            $data['guardian_primary_mobile_no'] = $data['father_phone'];
        }

        if (empty($data['guardian_address']) && !empty($data['current_address'])) {
            $data['guardian_address'] = $data['current_address'];
        }

        if ($request->hasFile('student_photo')) {
            $data['student_photo'] = $request->file('student_photo')->store('student_photos', 'public');
        }

        if ($request->hasFile('attached_documents')) {
            $existing = is_array($admission->attached_documents)
                ? $admission->attached_documents
                : (json_decode($admission->attached_documents, true) ?: []);

            foreach ($request->file('attached_documents') as $file) {
                if ($file && $file->isValid()) {
                    $existing[] = $file->store('attached_documents', 'public');
                }
            }
            $data['attached_documents'] = array_values($existing);
        } else {
            unset($data['attached_documents']);
        }

        $admission->update($data);

        return redirect()
            ->route('admission.show', $admission)
            ->with('status', 'Admission updated successfully.');
    }

    public function destroy(Admission $admission): RedirectResponse
    {
        $admission->delete();

        return redirect()
            ->route('admission.index')
            ->with('status', 'Admission moved to trash successfully.');
    }

    public function restore(int $id): RedirectResponse
    {
        $admission = Admission::onlyTrashed()->findOrFail($id);
        $admission->restore();

        return redirect()
            ->route('admission.trash')
            ->with('status', 'Admission restored successfully.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $admission = Admission::onlyTrashed()->findOrFail($id);
        $admission->forceDelete();

        return redirect()
            ->route('admission.trash')
            ->with('status', 'Admission permanently deleted.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'selected_ids' => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'min:1'],
            'bulk_action' => ['required', 'in:trash,restore,force_delete'],
        ]);

        $ids = Arr::map($validated['selected_ids'], fn($id) => (int) $id);
        $action = $validated['bulk_action'];

        if ($action === 'trash') {
            Admission::query()->whereKey($ids)->delete();

            return redirect()
                ->route('student-list.index')
                ->with('status', 'Selected admissions moved to trash.');
        }

        if ($action === 'restore') {
            Admission::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()
                ->route('admission.trash')
                ->with('status', 'Selected admissions restored successfully.');
        }

        Admission::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return redirect()
            ->route('admission.trash')
            ->with('status', 'Selected admissions permanently deleted.');
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status', 'all'));
        $month = trim((string) $request->string('month', 'all'));
        $recent = trim((string) $request->string('recent', 'overall'));
        $className = trim((string) $request->string('class_name', 'all'));

        $query = Admission::query()->latest('admission_date')->latest();

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('admission_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('guardian_name', 'like', "%{$search}%")
                    ->orWhere('class_name', 'like', "%{$search}%")
                    ->orWhere('student_mobile_no', 'like', "%{$search}%")
                    ->orWhere('guardian_primary_mobile_no', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('admission_status', $status);
        }

        if ($className !== 'all') {
            $query->where('class_name', $className);
        }

        if ($month !== 'all') {
            $monthNumber = (int) $month;
            if ($monthNumber >= 1 && $monthNumber <= 12) {
                $query->whereMonth('admission_date', '=', $monthNumber, 'and');
            }
        }

        $this->applyRecentRangeFilter($query, $recent);
        $admissions = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="admissions_' . date('Y_m_d_His') . '.csv"',
        ];

        $callback = function () use ($admissions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Admission No', 'Admission Date', 'First Name', 'Last Name', 'CNIC / B-Form',
                'Date of Birth', 'Gender', 'Blood Group', 'Religion', 'Nationality',
                'Student Mobile No', 'Student Email', 'Previous School', 'Medical Notes',
                'Father Name', 'Mother Name', 'Guardian Name', 'Guardian Relation',
                'Guardian CNIC', 'Guardian Occupation', 'Guardian Primary Mobile', 'Guardian Secondary Mobile',
                'Guardian Email', 'Guardian Address', 'Academic Session', 'Class Name',
                'Section Name', 'Roll No', 'Class Shift', 'Admission Type', 'Fee Plan',
                'Registration Fee', 'Monthly Fee', 'Quarterly Fee', 'Annual Fee',
                'Scholarship Discount', 'Academic Notes', 'Current Address', 'Permanent Address',
                'Transportation Required', 'Transportation Route', 'Emergency Contact Name',
                'Emergency Contact Relation', 'Emergency Contact Mobile', 'Admission Status', 'Admission Remarks'
            ]);

            foreach ($admissions as $adm) {
                fputcsv($file, [
                    $adm->admission_no,
                    $adm->admission_date ? $adm->admission_date->format('Y-m-d') : '',
                    $adm->first_name,
                    $adm->last_name,
                    $adm->cnic_bform,
                    $adm->date_of_birth ? $adm->date_of_birth->format('Y-m-d') : '',
                    $adm->gender,
                    $adm->blood_group,
                    $adm->religion,
                    $adm->nationality,
                    $adm->student_mobile_no,
                    $adm->student_email,
                    $adm->previous_school,
                    $adm->student_medical_notes,
                    $adm->father_name,
                    $adm->mother_name,
                    $adm->guardian_name,
                    $adm->guardian_relation,
                    $adm->guardian_cnic,
                    $adm->guardian_occupation,
                    $adm->guardian_primary_mobile_no,
                    $adm->guardian_secondary_mobile_no,
                    $adm->guardian_email,
                    $adm->guardian_address,
                    $adm->academic_session,
                    $adm->class_name,
                    $adm->section_name,
                    $adm->roll_no,
                    $adm->class_shift,
                    $adm->admission_type,
                    $adm->fee_plan,
                    $adm->registration_fee,
                    $adm->monthly_fee,
                    $adm->quarterly_fee,
                    $adm->annual_fee,
                    $adm->scholarship_discount,
                    $adm->academic_notes,
                    $adm->current_address,
                    $adm->permanent_address,
                    $adm->transportation_required ? 'Yes' : 'No',
                    $adm->transportation_route,
                    $adm->emergency_contact_name,
                    $adm->emergency_contact_relation,
                    $adm->emergency_contact_mobile_no,
                    $adm->admission_status,
                    $adm->admission_remarks,
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'admissions_' . date('Y_m_d_His') . '.csv', $headers);
    }

    public function sampleCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="admission_sample_import.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Admission No', 'Admission Date', 'First Name', 'Last Name', 'CNIC / B-Form',
                'Date of Birth', 'Gender', 'Blood Group', 'Religion', 'Nationality',
                'Student Mobile No', 'Student Email', 'Previous School', 'Medical Notes',
                'Father Name', 'Mother Name', 'Guardian Name', 'Guardian Relation',
                'Guardian CNIC', 'Guardian Occupation', 'Guardian Primary Mobile', 'Guardian Secondary Mobile',
                'Guardian Email', 'Guardian Address', 'Academic Session', 'Class Name',
                'Section Name', 'Roll No', 'Class Shift', 'Admission Type', 'Fee Plan',
                'Registration Fee', 'Monthly Fee', 'Quarterly Fee', 'Annual Fee',
                'Scholarship Discount', 'Academic Notes', 'Current Address', 'Permanent Address',
                'Transportation Required', 'Transportation Route', 'Emergency Contact Name',
                'Emergency Contact Relation', 'Emergency Contact Mobile', 'Admission Status', 'Admission Remarks'
            ]);

            fputcsv($file, [
                'ADM-9901', '2026-08-01', 'Ahmad', 'Raza', '35201-9999999-1',
                '2012-04-10', 'male', 'O+', 'Islam', 'Pakistani',
                '03004444444', 'ahmad.raza@example.com', 'City Grammar School', '',
                'Tariq Raza', 'Fatima Raza', 'Tariq Raza', 'Father',
                '35201-1111111-5', 'Business', '03004444444', '',
                'tariq@example.com', 'Street 4, Lahore', '2025-2026', 'Class 5',
                'A', '101', 'Morning', 'regular', 'monthly',
                '2000', '5000', '0', '0',
                '0', '', 'Street 4, Lahore', 'Street 4, Lahore',
                'No', '', 'Tariq Raza',
                'Father', '03004444444', 'active', 'Sample admission record'
            ]);

            fclose($file);
        };

        return response()->streamDownload($callback, 'admission_sample_import.csv', $headers);
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

            $admissionNo = $rowData['admission_no'] ?? null;
            if (empty($admissionNo) || Admission::query()->where('admission_no', $admissionNo)->exists()) {
                do {
                    $admissionNo = 'ADM-' . random_int(1000, 9999);
                } while (Admission::query()->where('admission_no', $admissionNo)->exists());
            }

            $gender = strtolower($rowData['gender'] ?? 'male');
            if (! in_array($gender, ['male', 'female', 'other'], true)) {
                $gender = 'male';
            }

            $status = strtolower($rowData['admission_status'] ?? $rowData['status'] ?? 'active');
            if (! in_array($status, ['active', 'pending', 'inactive'], true)) {
                $status = 'active';
            }

            $transReq = strtolower($rowData['transportation_required'] ?? 'no');
            $isTrans = in_array($transReq, ['yes', '1', 'true'], true);

            Admission::query()->create([
                'admission_no' => $admissionNo,
                'admission_date' => ! empty($rowData['admission_date']) ? date('Y-m-d', strtotime($rowData['admission_date'])) : date('Y-m-d'),
                'first_name' => $firstName,
                'last_name' => $rowData['last_name'] ?? null,
                'cnic_bform' => $rowData['cnic_bform'] ?? $rowData['cnic'] ?? null,
                'date_of_birth' => ! empty($rowData['date_of_birth']) ? date('Y-m-d', strtotime($rowData['date_of_birth'])) : (! empty($rowData['dob']) ? date('Y-m-d', strtotime($rowData['dob'])) : '2015-01-01'),
                'gender' => $gender,
                'blood_group' => $rowData['blood_group'] ?? null,
                'religion' => $rowData['religion'] ?? 'Islam',
                'nationality' => $rowData['nationality'] ?? 'Pakistani',
                'student_mobile_no' => $rowData['student_mobile_no'] ?? null,
                'student_email' => $rowData['student_email'] ?? null,
                'previous_school' => $rowData['previous_school'] ?? null,
                'student_medical_notes' => $rowData['student_medical_notes'] ?? null,
                'father_name' => $rowData['father_name'] ?? null,
                'father_cnic' => $rowData['father_cnic'] ?? $rowData['guardian_cnic'] ?? null,
                'father_phone' => $rowData['father_phone'] ?? $rowData['guardian_primary_mobile_no'] ?? null,
                'father_occupation' => $rowData['father_occupation'] ?? $rowData['guardian_occupation'] ?? null,
                'mother_name' => $rowData['mother_name'] ?? null,
                'mother_cnic' => $rowData['mother_cnic'] ?? null,
                'mother_phone' => $rowData['mother_phone'] ?? null,
                'mother_occupation' => $rowData['mother_occupation'] ?? null,
                'guardian_name' => $rowData['guardian_name'] ?? $rowData['father_name'] ?? $firstName,
                'guardian_relation' => $rowData['guardian_relation'] ?? 'Father',
                'guardian_cnic' => $rowData['guardian_cnic'] ?? null,
                'guardian_occupation' => $rowData['guardian_occupation'] ?? null,
                'guardian_primary_mobile_no' => $rowData['guardian_primary_mobile_no'] ?? $rowData['student_mobile_no'] ?? '03000000000',
                'guardian_secondary_mobile_no' => $rowData['guardian_secondary_mobile_no'] ?? null,
                'guardian_email' => $rowData['guardian_email'] ?? null,
                'guardian_address' => $rowData['guardian_address'] ?? 'N/A',
                'academic_session' => $rowData['academic_session'] ?? '2025-2026',
                'class_name' => $rowData['class_name'] ?? 'Class 1',
                'section_name' => $rowData['section_name'] ?? 'A',
                'roll_no' => $rowData['roll_no'] ?? null,
                'class_shift' => $rowData['class_shift'] ?? 'Morning',
                'admission_type' => strtolower($rowData['admission_type'] ?? 'regular'),
                'fee_plan' => strtolower($rowData['fee_plan'] ?? 'monthly'),
                'fee_status' => strtolower($rowData['fee_status'] ?? 'pending'),
                'registration_fee' => ! empty($rowData['registration_fee']) ? (float) $rowData['registration_fee'] : 0,
                'monthly_fee' => ! empty($rowData['monthly_fee']) ? (float) $rowData['monthly_fee'] : 0,
                'quarterly_fee' => ! empty($rowData['quarterly_fee']) ? (float) $rowData['quarterly_fee'] : 0,
                'annual_fee' => ! empty($rowData['annual_fee']) ? (float) $rowData['annual_fee'] : 0,
                'scholarship_discount' => ! empty($rowData['scholarship_discount']) ? (float) $rowData['scholarship_discount'] : 0,
                'academic_notes' => $rowData['academic_notes'] ?? null,
                'current_address' => $rowData['current_address'] ?? 'N/A',
                'permanent_address' => $rowData['permanent_address'] ?? 'N/A',
                'transportation_required' => $isTrans,
                'transportation_route' => $rowData['transportation_route'] ?? null,
                'hostel' => strtolower($rowData['hostel'] ?? 'no'),
                'emergency_contact_name' => $rowData['emergency_contact_name'] ?? 'N/A',
                'emergency_contact_relation' => $rowData['emergency_contact_relation'] ?? 'Parent',
                'emergency_contact_mobile_no' => $rowData['emergency_contact_mobile_no'] ?? '03000000000',
                'admission_status' => $status,
                'admission_remarks' => $rowData['admission_remarks'] ?? null,
                'is_confirmed' => true,
            ]);

            $importedCount++;
        }

        fclose($handle);

        $message = "Successfully imported {$importedCount} admission records.";
        if ($errorsCount > 0) {
            $message .= " {$errorsCount} rows were skipped due to missing first name.";
        }

        return redirect()->route('admission.index')->with('status', $message);
    }

    private function applyRecentRangeFilter(Builder $query, string $recent): void
    {
        if ($recent === 'overall') {
            return;
        }

        $map = [
            '1m' => ['unit' => 'months', 'value' => 1],
            '2m' => ['unit' => 'months', 'value' => 2],
            '3m' => ['unit' => 'months', 'value' => 3],
            '6m' => ['unit' => 'months', 'value' => 6],
            '9m' => ['unit' => 'months', 'value' => 9],
            '12m' => ['unit' => 'months', 'value' => 12],
            '2y' => ['unit' => 'years', 'value' => 2],
            '3y' => ['unit' => 'years', 'value' => 3],
            '5y' => ['unit' => 'years', 'value' => 5],
            '6y' => ['unit' => 'years', 'value' => 6],
            '7y' => ['unit' => 'years', 'value' => 7],
            '8y' => ['unit' => 'years', 'value' => 8],
            '9y' => ['unit' => 'years', 'value' => 9],
        ];

        if (!isset($map[$recent])) {
            return;
        }

        $config = $map[$recent];
        $startDate = now();

        if ($config['unit'] === 'months') {
            $startDate = $startDate->subMonths($config['value']);
        } else {
            $startDate = $startDate->subYears($config['value']);
        }

        $query->whereDate('admission_date', '>=', $startDate->toDateString());
    }

    private function resolveSelectedMonthCount(string $month): int
    {
        if ($month === 'all') {
            return Admission::query()->count('*');
        }

        $monthNumber = (int) $month;
        if ($monthNumber < 1 || $monthNumber > 12) {
            return Admission::query()->count('*');
        }

        return Admission::query()->whereMonth('admission_date', '=', $monthNumber, 'and')->count('*');
    }
}
