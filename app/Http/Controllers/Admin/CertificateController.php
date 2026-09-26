<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Admission;
use App\Models\Student;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public const CERTIFICATE_TYPES = [
        'school_leaving' => [
            'title'       => 'School Leaving Certificate',
            'description' => 'Issued to students who have completed their studies and are leaving the school.',
            'icon'        => 'file-badge',
            'color'       => 'success',
        ],
        'character' => [
            'title'       => 'Character Certificate',
            'description' => 'Certifies the good moral character and conduct of the student.',
            'icon'        => 'shield-check',
            'color'       => 'primary',
        ],
        'merit' => [
            'title'       => 'Merit Certificate',
            'description' => 'Awarded to students who have achieved excellence in academics or co-curricular activities.',
            'icon'        => 'trophy',
            'color'       => 'warning',
        ],
        'remarks_signature' => [
            'title'       => 'Remarks & Signature',
            'description' => 'Student Remarks & Signature card for teacher feedback and parent signatures.',
            'icon'        => 'file-signature',
            'color'       => 'info',
        ],
        'experience' => [
            'title'       => 'Experience Letter',
            'description' => 'Official experience letter for school teachers and staff certifying employment tenure, designation & conduct.',
            'icon'        => 'briefcase',
            'color'       => 'dark',
        ],
        'appreciation' => [
            'title'       => 'Appreciation Certificate',
            'description' => 'Awarded to honor outstanding students, teachers, or staff members (e.g. Student/Teacher of the Month or Year).',
            'icon'        => 'award',
            'color'       => 'danger',
        ],
    ];

    public function index()
    {
        $types = self::CERTIFICATE_TYPES;
        return view('pages.admin.certificates.index', compact('types'));
    }

    public function create(Request $request)
    {
        $type  = $request->get('type', 'school_leaving');
        $types = self::CERTIFICATE_TYPES;

        if (!array_key_exists($type, $types)) {
            $type = 'school_leaving';
        }

        $academicSessions = AcademicSession::orderBy('session_name', 'desc')->get();

        if ($academicSessions->isEmpty()) {
            $s1 = AcademicSession::create([
                'session_name' => '2025-2026',
                'start_date'   => '2025-04-01',
                'end_date'     => '2026-03-31',
                'status'       => 'Active',
            ]);
            $s2 = AcademicSession::create([
                'session_name' => '2026-2027',
                'start_date'   => '2026-04-01',
                'end_date'     => '2027-03-31',
                'status'       => 'Active',
            ]);
            $academicSessions = collect([$s2, $s1]);
        }

        $classes = \App\Models\StudentClass::orderBy('id')->pluck('name');
        if ($classes->isEmpty()) {
            $classes = Admission::distinct()
                ->orderBy('class_name')
                ->pluck('class_name');
        }

        $staffMembers = \App\Models\Staff::orderBy('first_name')->get();

        return view('pages.admin.certificates.create', compact('type', 'types', 'classes', 'academicSessions', 'staffMembers'));
    }

    /**
     * AJAX: Return students belonging to a given class and academic session as JSON
     */
    public function getStudentsByClass(Request $request)
    {
        $className   = trim((string) $request->get('class_name', ''));
        $sessionId   = trim((string) $request->get('academic_session_id', ''));
        $sessionName = trim((string) $request->get('academic_session', ''));

        // Query Student model primarily for all students belonging to the class & session
        $query = Student::select([
                'id',
                'admission_no',
                'roll_no',
                'date_of_birth',
                'class_name',
                'section_name',
                'father_name',
                'first_name',
                'last_name',
                'student_photo',
                'academic_session_id',
                'status',
            ]);

        if (!empty($className)) {
            $query->where('class_name', $className);
        }

        if (!empty($sessionId)) {
            $query->where(function ($q) use ($sessionId, $sessionName) {
                $q->where('academic_session_id', $sessionId);
                if (!empty($sessionName)) {
                    $q->orWhereHas('academicSession', function ($sub) use ($sessionName) {
                        $sub->where('session_name', $sessionName);
                    });
                }
            });
        } elseif (!empty($sessionName)) {
            $query->whereHas('academicSession', function ($sub) use ($sessionName) {
                $sub->where('session_name', $sessionName);
            });
        }

        $students = $query
            ->orderByRaw("TRIM(CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')))")
            ->get();

        // Fallback to Admission model if Student query returns empty
        if ($students->isEmpty() && !empty($className)) {
            $admQuery = Admission::select([
                'id',
                'admission_no',
                'roll_no',
                'date_of_birth',
                'class_name',
                'section_name',
                'father_name',
                'first_name',
                'last_name',
                'student_photo',
                'academic_session_id',
                'admission_status as status',
            ])->where('class_name', $className);

            if (!empty($sessionId)) {
                $admQuery->where('academic_session_id', $sessionId);
            }

            $students = $admQuery
                ->orderByRaw("TRIM(CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')))")
                ->get();
        }

        $mapped = $students->map(fn ($s) => [
            'id'                => $s->id,
            'student_name'      => trim(($s->first_name ?? '') . ' ' . ($s->last_name ?? '')),
            'status'            => ucfirst(strtolower((string) ($s->status ?: 'Active'))),
            'father_name'       => $s->father_name,
            'admission_no'      => $s->admission_no,
            'roll_no'           => $s->roll_no,
            'date_of_birth'     => $s->date_of_birth
                ? \Carbon\Carbon::parse($s->date_of_birth)->format('d M, Y')
                : '',
            'class_name'        => $s->class_name,
            'section_name'      => $s->section_name,
            'student_photo_url' => $s->student_photo ? asset('storage/' . $s->student_photo) : null,
            'academic_session'  => $s->academicSession ? $s->academicSession->session_name : '',
        ]);

        return response()->json($mapped);
    }

    public function print(Request $request)
    {
        $type        = $request->get('type', 'school_leaving');
        $types       = self::CERTIFICATE_TYPES;
        $typeDetails = $types[$type] ?? $types['school_leaving'];

        $rawIssueDate = $request->get('issue_date');
        $issueDate    = now()->format('d M, Y');
        if (!empty($rawIssueDate)) {
            try {
                $issueDate = \Carbon\Carbon::parse($rawIssueDate)->format('d M, Y');
            } catch (\Throwable $e) {
                $issueDate = $rawIssueDate;
            }
        }

        $rawFromDate = $request->get('from_date');
        $fromDate    = '';
        if (!empty($rawFromDate)) {
            try {
                $fromDate = \Carbon\Carbon::parse($rawFromDate)->format('d M, Y');
            } catch (\Throwable $e) {
                $fromDate = $rawFromDate;
            }
        }

        $rawToDate = $request->get('to_date');
        $toDate    = '';
        if (!empty($rawToDate)) {
            try {
                $toDate = \Carbon\Carbon::parse($rawToDate)->format('d M, Y');
            } catch (\Throwable $e) {
                $toDate = $rawToDate;
            }
        }

        $data = [
            'staff_name'          => $request->get('staff_name', $request->get('student_name', '')),
            'student_name'        => $request->get('student_name', $request->get('staff_name', '')),
            'recipient_name'      => $request->get('recipient_name', $request->get('staff_name', $request->get('student_name', ''))),
            'recipient_type'      => $request->get('recipient_type', 'student'),
            'award_title'         => $request->get('award_title', 'STUDENT OF THE MONTH'),
            'award_period'        => $request->get('award_period', ''),
            'father_name'         => $request->get('father_name', ''),
            'cnic_no'             => $request->get('cnic_no', ''),
            'designation'         => $request->get('designation', ''),
            'from_date'           => $fromDate,
            'to_date'             => $toDate,
            'ref_no'              => $request->get('ref_no', 'NUH/AC/' . date('Y') . '/' . rand(100, 999)),
            'admission_no'        => $request->get('admission_no', ''),
            'roll_no'             => $request->get('roll_no', ''),
            'date_of_birth'       => $request->get('date_of_birth', ''),
            'class_name'          => $request->get('class_name', ''),
            'section'             => $request->get('section', ''),
            'session'             => $request->get('session') ?: '2025-2026',
            'issue_date'          => $issueDate,
            'class_teacher'       => $request->get('class_teacher', ''),
            'head_section'        => $request->get('head_section', ''),
            'principal'           => $request->get('principal') ?: 'Principal',
            'head_of_institution' => $request->get('head_of_institution') ?: 'Head of Institution',
            'reason'              => $request->get('reason', ''),
            'remarks'             => $request->get('remarks', ''),
            'teacher_remarks'     => $request->get('teacher_remarks', $request->get('remarks', '')),
            'principal_remarks'   => $request->get('principal_remarks', ''),
            'student_photo_url'   => $request->get('student_photo_url', ''),
        ];

        return view("pages.admin.certificates.print.{$type}", compact('data', 'typeDetails'));
    }
}
