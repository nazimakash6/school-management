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
        'overall_performance' => [
            'title'       => 'Overall Performance Certificate',
            'description' => 'Comprehensive student report & progress card combining examination, discipline, skills, attendance & overall remarks.',
            'icon'        => 'clipboard-check',
            'color'       => 'primary',
        ],
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

        $cleanClass = preg_replace('/^class\s+/i', '', $className);

        // Helper to apply class filter flexibly
        $applyClassFilter = function ($q) use ($className, $cleanClass) {
            if (!empty($className)) {
                $q->where(function ($sub) use ($className, $cleanClass) {
                    $sub->where('class_name', $className)
                        ->orWhere('class_name', 'Class ' . $cleanClass)
                        ->orWhere('class_name', $cleanClass);
                });
            }
        };

        // 1. Primary Query on Student model
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
            'academic_session_id',
            'status',
        ]);

        $applyClassFilter($query);

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

        // 2. Fallback A: Query Student model WITHOUT restricting strictly to session if empty
        if ($students->isEmpty() && !empty($className)) {
            $queryNoSess = Student::select([
                'id',
                'admission_no',
                'roll_no',
                'date_of_birth',
                'class_name',
                'section_name',
                'father_name',
                'first_name',
                'last_name',
                'academic_session_id',
                'status',
            ]);
            $applyClassFilter($queryNoSess);

            $students = $queryNoSess
                ->orderByRaw("TRIM(CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,'')))")
                ->get();
        }

        // 3. Fallback B: Fallback to Admission model if still empty
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
                'academic_session_id',
                'admission_status as status',
            ]);
            $applyClassFilter($admQuery);

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
            'student_photo_url' => isset($s->student_photo) && $s->student_photo ? asset('storage/' . $s->student_photo) : null,
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
            'report_type'         => $request->get('report_type', 'Monthly'),
            'focus_area'          => $request->get('focus_area', ''),
            'performance_tracker' => $request->get('performance_tracker', []),
            'skills_attributes'   => $request->get('skills_attributes', []),
            'learning_highlights' => $request->get('learning_highlights', ''),
            'areas_to_improve'    => $request->get('areas_to_improve', ''),
            'attendance_total'    => $request->get('attendance_total', ''),
            'attendance_present'  => $request->get('attendance_present', ''),
            'attendance_absent'   => $request->get('attendance_absent', ''),
            'attendance_pct'      => $request->get('attendance_pct', ''),
            'academic_stars'      => $request->get('academic_stars', '5'),
            'islamic_stars'       => $request->get('islamic_stars', '5'),
            'personal_stars'      => $request->get('personal_stars', '5'),
            'behaviour_stars'     => $request->get('behaviour_stars', '5'),
            'cocurricular_stars'  => $request->get('cocurricular_stars', '5'),
            'overall_progress'    => $request->get('overall_progress', 'GOOD'),
            'parent_remarks'      => $request->get('parent_remarks', ''),
            'action_plan'         => $request->get('action_plan', ''),
            'subject_teacher'     => $request->get('subject_teacher', 'Subject Teacher'),
            'parent_guardian'     => $request->get('parent_guardian', 'Parent / Guardian'),
            'date_period'         => $request->get('date_period', $issueDate),
        ];

        return view("pages.admin.certificates.print.{$type}", compact('data', 'typeDetails'));
    }
}
