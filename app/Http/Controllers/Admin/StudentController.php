<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admission\StoreAdmissionRequest;
use App\Models\AcademicSession;
use App\Models\Admission;
use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status', 'all'));
        $month = trim((string) $request->string('month', 'all'));
        $recent = trim((string) $request->string('recent', 'overall'));
        $className = trim((string) $request->string('class_name', 'all'));
        $academicSessionId = trim((string) $request->string('academic_session_id', 'all'));

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

        $academicSessions = AcademicSession::query()
            ->orderBy('start_date', 'desc')
            ->get();

        $query = Student::query()->with(['studentClass', 'academicSession'])->latest('created_at');

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('admission_no', 'like', "%{$search}%")
                    ->orWhere('roll_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%")
                    ->orWhere('guardian_primary_mobile_no', 'like', "%{$search}%")
                    ->orWhere('current_address', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($academicSessionId !== 'all') {
            $query->where('academic_session_id', $academicSessionId);
        }

        if ($className !== 'all') {
            $query->where(function (Builder $b) use ($className) {
                $b->where('class_name', $className)
                  ->orWhereHas('studentClass', function (Builder $innerB) use ($className) {
                      $innerB->where('name', $className);
                  });
            });
        }

        if ($month !== 'all') {
            $monthNumber = (int) $month;
            if ($monthNumber >= 1 && $monthNumber <= 12) {
                $query->whereMonth('created_at', '=', $monthNumber);
            }
        }

        $this->applyRecentRangeFilter($query, $recent);

        $admissions = $query
            ->paginate($perPage)
            ->withQueryString();

        $totalAdmissions = Student::query()->count();
        $activeAdmissions = Student::query()->where('status', 'active')->count();
        $pendingAdmissions = Student::query()->where('status', 'pending')->count();
        $selectedMonthCount = $this->resolveSelectedMonthCount($month);

        return view('pages.admin.student-list.index', compact(
            'admissions',
            'perPage',
            'search',
            'status',
            'month',
            'recent',
            'className',
            'availableClasses',
            'academicSessionId',
            'academicSessions',
            'totalAdmissions',
            'activeAdmissions',
            'pendingAdmissions',
            'selectedMonthCount',
        ));
    }



    public function show($id): View
    {
        $student = Student::with(['studentClass', 'admission'])->findOrFail($id);
        return view('pages.admin.student-list.show', compact('student'));
    }

    public function print($id): View
    {
        $student = Student::with(['studentClass', 'admission.academicSession'])->findOrFail($id);
        return view('pages.admin.student-list.print', compact('student'));
    }

    public function edit($id): View
    {
        $student = Student::findOrFail($id);
        $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        $academicSessions = AcademicSession::where('status', 'Active')->orderBy('start_date', 'desc')->get();
        $groupsList = \App\Models\Group::where('status', 'active')->orderBy('name')->get();
        $existingStudents = Student::where('id', '!=', $student->id)->orderBy('first_name')->orderBy('last_name')->get();
        return view('pages.admin.student-list.edit', compact('student', 'classes', 'academicSessions', 'groupsList', 'existingStudents'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $cnicFields = ['cnic_bform', 'father_cnic', 'mother_cnic', 'guardian_cnic'];
        $phoneFields = ['student_mobile_no', 'father_phone', 'mother_phone', 'guardian_primary_mobile_no', 'guardian_secondary_mobile_no', 'emergency_contact_mobile_no'];

        foreach ($cnicFields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $cleaned = preg_replace('/[^0-9]/', '', (string) $request->input($field));
                $request->merge([$field => $cleaned !== '' ? $cleaned : null]);
            }
        }
        foreach ($phoneFields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $cleaned = preg_replace('/[^0-9]/', '', (string) $request->input($field));
                $request->merge([$field => $cleaned !== '' ? $cleaned : null]);
            }
        }

        $validated = $request->validate([
            'academic_session_id' => ['required', 'exists:academic_sessions,id'],
            'admission_no' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('students', 'admission_no')->ignore($student->id)],
            'admission_date' => ['required', 'date'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'cnic_bform' => ['nullable', 'string', 'digits:13'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', \Illuminate\Validation\Rule::in(['Male', 'Female', 'Other', 'male', 'female', 'other'])],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'student_mobile_no' => ['nullable', 'string', 'digits:11'],
            'student_email' => ['nullable', 'email', 'max:255'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'student_medical_notes' => ['nullable', 'string', 'max:1000'],
            'sibling_id' => ['nullable', 'integer'],

            'father_name' => ['required', 'string', 'max:255'],
            'father_cnic' => ['nullable', 'string', 'digits:13'],
            'father_phone' => ['nullable', 'string', 'digits:11'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_cnic' => ['nullable', 'string', 'digits:13'],
            'mother_phone' => ['nullable', 'string', 'digits:11'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_cnic' => ['nullable', 'string', 'digits:13'],
            'guardian_occupation' => ['nullable', 'string', 'max:255'],
            'guardian_primary_mobile_no' => ['nullable', 'string', 'digits:11'],
            'guardian_secondary_mobile_no' => ['nullable', 'string', 'digits:11'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'guardian_address' => ['nullable', 'string', 'max:500'],

            'class_name' => ['required', 'string', 'max:255'],
            'section_name' => ['nullable', 'string', 'max:255'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'group' => ['nullable', 'string', 'max:255'],
            'roll_no' => ['nullable', 'string', 'max:50'],
            'class_shift' => ['nullable', 'string', 'max:100'],
            'fee_plan' => ['nullable', 'string', 'max:100'],
            'fee_status' => ['nullable', 'string', 'max:100'],
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'quarterly_fee' => ['nullable', 'numeric', 'min:0'],
            'six_monthly_fee' => ['nullable', 'numeric', 'min:0'],
            'annual_fee' => ['nullable', 'numeric', 'min:0'],
            'scholarship_discount' => ['nullable', 'max:255'],

            'academic_notes' => ['nullable', 'string'],

            'current_address' => ['required', 'string', 'max:500'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'transportation_required' => ['nullable', 'string'],
            'hostel' => ['nullable', \Illuminate\Validation\Rule::in(['yes', 'no'])],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],
            'emergency_contact_mobile_no' => ['nullable', 'string', 'digits:11'],
            'status' => ['nullable', 'string', 'max:50'],
            'admission_remarks' => ['nullable', 'string'],

            'student_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,avif,webp', 'max:2048'],
        ], [
            'cnic_bform.digits' => 'Student CNIC / B-Form must be exactly 13 digits.',
            'father_cnic.digits' => 'Father CNIC must be exactly 13 digits.',
            'mother_cnic.digits' => 'Mother CNIC must be exactly 13 digits.',
            'guardian_cnic.digits' => 'Guardian CNIC must be exactly 13 digits.',
            'student_mobile_no.digits' => 'Student Mobile number must be exactly 11 digits.',
            'father_phone.digits' => 'Father Phone number must be exactly 11 digits.',
            'mother_phone.digits' => 'Mother Phone number must be exactly 11 digits.',
            'guardian_primary_mobile_no.digits' => 'Guardian Primary Mobile number must be exactly 11 digits.',
            'guardian_secondary_mobile_no.digits' => 'Guardian Secondary Mobile number must be exactly 11 digits.',
            'emergency_contact_mobile_no.digits' => 'Emergency Contact Phone number must be exactly 11 digits.',
        ]);

        if ($request->hasFile('student_photo')) {
            $validated['student_photo'] = $request->file('student_photo')->store('student_photos', 'public');
        }

        if (!isset($validated['group']) && isset($validated['group_name'])) {
            $validated['group'] = $validated['group_name'];
        }

        $student->update($validated);

        // Synchronize with Admissions table if linked admission exists
        $admission = Admission::where('admission_no', $student->admission_no)->first();
        if ($admission) {
            $admissionData = $validated;
            if (isset($validated['status'])) {
                $admissionData['admission_status'] = $validated['status'];
            }
            $admission->update($admissionData);
        }

        return redirect()
            ->route('student-list.show', $student->id)
            ->with('status', 'Student information updated successfully.');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $admission = Admission::where('admission_no', $student->admission_no)->first();
        if ($admission) {
            $admission->delete();
        } else {
            $student->delete();
        }

        return redirect()
            ->route('student-list.index')
            ->with('status', 'Student moved to trash.');
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status', 'all'));
        $month = trim((string) $request->string('month', 'all'));
        $recent = trim((string) $request->string('recent', 'overall'));
        $className = trim((string) $request->string('class_name', 'all'));

        $query = Student::query()->with('studentClass')->latest('created_at');

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('admission_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%")
                    ->orWhere('guardian_primary_mobile_no', 'like', "%{$search}%")
                    ->orWhere('current_address', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($className !== 'all') {
            $query->whereHas('studentClass', function (Builder $b) use ($className) {
                $b->where('name', $className);
            });
        }

        if ($month !== 'all') {
            $monthNumber = (int) $month;
            if ($monthNumber >= 1 && $monthNumber <= 12) {
                $query->whereMonth('created_at', '=', $monthNumber);
            }
        }

        $this->applyRecentRangeFilter($query, $recent);

        $students = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_' . date('Y_m_d_His') . '.csv"',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Admission No', 'First Name', 'Last Name', 'Class Name', 'Roll No',
                'Gender', 'Date of Birth', 'Parent / Guardian Name', 'Parent Contact', 'Address', 'Status', 'Created At'
            ]);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->admission_no,
                    $student->first_name,
                    $student->last_name,
                    $student->studentClass ? $student->studentClass->name : '',
                    $student->roll_no,
                    $student->gender,
                    $student->date_of_birth ? (is_string($student->date_of_birth) ? $student->date_of_birth : $student->date_of_birth->format('Y-m-d')) : '',
                    $student->father_name ?: $student->guardian_name,
                    $student->guardian_primary_mobile_no,
                    $student->current_address,
                    $student->status,
                    $student->created_at ? $student->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'students_' . date('Y_m_d_His') . '.csv', $headers);
    }

    private function applyRecentRangeFilter(Builder $query, string $recent): void
    {
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

        $query->whereDate('created_at', '>=', $startDate->toDateString());
    }

    private function resolveSelectedMonthCount(string $month): int
    {
        if ($month === 'all') {
            return Student::query()->count();
        }

        $monthNumber = (int) $month;
        if ($monthNumber < 1 || $monthNumber > 12) {
            return Student::query()->count();
        }

        return Student::query()->whereMonth('created_at', '=', $monthNumber)->count();
    }
}
