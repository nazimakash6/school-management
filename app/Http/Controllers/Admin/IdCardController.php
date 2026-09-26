<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolInfo;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;

class IdCardController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AcademicSession::orderBy('id', 'desc')->get();
        $activeSession = AcademicSession::where('status', 'Active')->first() ?? $sessions->first();
        $schoolInfo = SchoolInfo::first();
        $classes = \App\Models\StudentClass::orderBy('id')->get();

        $selectedSessionId = $request->get('session_id', $activeSession?->id);

        $students = Student::with('academicSession')
            ->when($selectedSessionId, function ($q) use ($selectedSessionId) {
                return $q->where('academic_session_id', $selectedSessionId);
            })
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $staffs = Staff::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('pages.admin.id-cards.index', compact(
            'sessions',
            'selectedSessionId',
            'students',
            'staffs',
            'schoolInfo',
            'classes'
        ));
    }

    public function getStudentsBySession(Request $request, $sessionId)
    {
        $query = Student::where('status', 'active');
        if ($sessionId && $sessionId !== 'all') {
            $query->where('academic_session_id', $sessionId);
        }

        $className = $request->get('class_name');
        if ($className && $className !== 'all') {
            $query->where('class_name', $className);
        }

        $students = $query->select('id', 'first_name', 'last_name', 'admission_no', 'class_name', 'section_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->full_name,
                    'admission_no' => $s->admission_no,
                    'class_name' => $s->class_name,
                    'section_name' => $s->section_name,
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    public function getStudentData($id)
    {
        $student = Student::with('academicSession')->findOrFail($id);
        $schoolInfo = SchoolInfo::first();

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_no' => $student->admission_no,
                'class_name' => $student->class_name ?: 'N/A',
                'section_name' => $student->section_name ?: 'N/A',
                'roll_no' => $student->roll_no ?: 'N/A',
                'father_name' => $student->father_name ?: ($student->guardian_name ?: 'N/A'),
                'phone' => $student->guardian_primary_mobile_no ?: ($student->emergency_contact_mobile_no ?: 'N/A'),
                'dob' => $student->date_of_birth ? $student->date_of_birth->format('d M, Y') : 'N/A',
                'blood_group' => $student->blood_group ?: 'N/A',
                'address' => $student->current_address ?: 'N/A',
                'session' => $student->academicSession->session_name ?? '2025-2026',
                'photo' => $student->student_photo ? asset('storage/' . $student->student_photo) : null,
            ],
            'school' => [
                'school_name' => $schoolInfo->school_name ?? 'Al Huda Educational Complex',
                'tagline' => $schoolInfo->tagline ?? 'Excellence in Education & Character Building',
                'logo_url' => $schoolInfo->logo_url ?? null,
                'phone' => $schoolInfo->phone ?? '+92 42 35881234',
                'address' => $schoolInfo->full_address ?? 'Main Campus, Lahore',
                'website' => $schoolInfo->website ?? 'https://educore.edu.pk',
                'principal_name' => $schoolInfo->principal_name ?? 'Principal',
                'stamp_url' => $schoolInfo->stamp_url ?? null,
            ]
        ]);
    }

    public function getStaffData($id)
    {
        $staff = Staff::findOrFail($id);
        $schoolInfo = SchoolInfo::first();

        return response()->json([
            'success' => true,
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->full_name,
                'staff_id' => $staff->staff_id,
                'department' => $staff->formatted_department ?: ($staff->department ?: 'General'),
                'designation' => $staff->formatted_designation ?: ($staff->designation ?: 'Staff Member'),
                'phone' => $staff->mobile_no ?: 'N/A',
                'cnic' => $staff->cnic ?: 'N/A',
                'blood_group' => $staff->blood_group ?: 'N/A',
                'joining_date' => $staff->joining_date ? date('d M, Y', strtotime($staff->joining_date)) : 'N/A',
                'emergency_contact' => $staff->emergency_contact_number ?: ($staff->mobile_no ?: 'N/A'),
                'address' => $staff->current_address ?: 'N/A',
                'photo' => $staff->profile_picture ? asset('storage/' . $staff->profile_picture) : null,
            ],
            'school' => [
                'school_name' => $schoolInfo->school_name ?? 'Al Huda Educational Complex',
                'tagline' => $schoolInfo->tagline ?? 'Excellence in Education & Character Building',
                'logo_url' => $schoolInfo->logo_url ?? null,
                'phone' => $schoolInfo->phone ?? '+92 42 35881234',
                'address' => $schoolInfo->full_address ?? 'Main Campus, Lahore',
                'website' => $schoolInfo->website ?? 'https://educore.edu.pk',
                'principal_name' => $schoolInfo->principal_name ?? 'Principal',
                'stamp_url' => $schoolInfo->stamp_url ?? null,
            ]
        ]);
    }
}
