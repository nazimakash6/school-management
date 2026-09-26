<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Models\ExamType;
use App\Models\ExamSchedule;
use App\Models\ExamMark;
use App\Models\Admission;
use App\Models\AcademicSession;
use App\Models\StudentClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ExaminationController extends Controller
{
    public function index(Request $request): View
    {
        $examTypeId = $request->input('exam_type_id');
        $className = $request->input('class_name');
        $sessionId = $request->input('academic_session_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Examination::with(['examType', 'academicSession'])
            ->withCount('marks')
            ->orderBy('id', 'desc');

        if ($examTypeId && $examTypeId !== 'all') {
            $query->where('exam_type_id', $examTypeId);
        }

        if ($className && $className !== 'all') {
            $query->where(function($q) use ($className) {
                $q->where('class_name', 'like', "%{$className}%")
                  ->orWhere('class_name', 'All Classes')
                  ->orWhere('class_name', 'All');
            });
        }

        if ($sessionId && $sessionId !== 'all') {
            $query->where('academic_session_id', $sessionId);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $examinations = $query->paginate(15)->withQueryString();

        $examTypes = ExamType::where('status', 'active')->orderBy('name')->get();
        $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        $academicSessions = AcademicSession::where('status', 'active')->orderBy('id', 'desc')->get();

        return view('pages.admin.examination.index', compact('examinations', 'examTypes', 'classes', 'academicSessions', 'examTypeId', 'className', 'sessionId', 'status', 'search'));
    }

    public function create(): View
    {
        $examTypes = ExamType::where('status', 'active')->orderBy('name')->get();
        $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        $academicSessions = AcademicSession::where('status', 'active')->orderBy('id', 'desc')->get();
        $subjects = Subject::orderBy('subject_name')->get();

        return view('pages.admin.examination.create', compact('examTypes', 'classes', 'academicSessions', 'subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $targetClasses = $request->input('target_classes', $request->input('class_name'));
        if (is_array($targetClasses)) {
            if (in_array('All Classes', $targetClasses) || in_array('All', $targetClasses)) {
                $classNameFinal = 'All Classes';
            } else {
                $classNameFinal = implode(', ', array_unique(array_filter($targetClasses)));
            }
        } else {
            $classNameFinal = trim((string) $targetClasses);
        }

        if (empty($classNameFinal)) {
            $classNameFinal = 'All Classes';
        }

        $request->merge(['class_name' => $classNameFinal]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_type_id' => 'required|exists:exam_types,id',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'class_name' => 'required|string',
            'section_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_marks' => 'required|numeric|min:1',
            'pass_marks' => 'required|numeric|min:0|lte:total_marks',
            'status' => 'required|in:scheduled,ongoing,completed,published',
            'description' => 'nullable|string',
            'subjects' => 'nullable|array',
            'subjects.*.name' => 'required_with:subjects|string',
            'subjects.*.max_marks' => 'required_with:subjects|numeric|min:1',
            'subjects.*.pass_marks' => 'required_with:subjects|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, &$examination) {
            $examination = Examination::create([
                'title' => $validated['title'],
                'exam_type_id' => $validated['exam_type_id'],
                'academic_session_id' => $validated['academic_session_id'] ?? null,
                'class_name' => $validated['class_name'],
                'section_name' => $validated['section_name'] ?? 'All',
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'total_marks' => $validated['total_marks'],
                'pass_marks' => $validated['pass_marks'],
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['subjects'])) {
                foreach ($validated['subjects'] as $sub) {
                    if (!empty($sub['name'])) {
                        ExamSchedule::create([
                            'examination_id' => $examination->id,
                            'subject_name' => $sub['name'],
                            'max_marks' => $sub['max_marks'] ?? $validated['total_marks'],
                            'pass_marks' => $sub['pass_marks'] ?? $validated['pass_marks'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('examination.show', $examination)
            ->with('status', 'Examination created successfully with subjects.');
    }

    public function show(Examination $examination): View
    {
        $examination->load(['examType', 'academicSession', 'schedules']);

        // Load student admissions for this exam target classes
        $excludedStatuses = ['rejected', 'inactive', 'cancelled'];
        if ($examination->isAllClasses()) {
            $students = Admission::where(function($q) use ($excludedStatuses) {
                    $q->whereNotIn('admission_status', $excludedStatuses)->orWhereNull('admission_status');
                })
                ->orderBy('class_name')
                ->orderBy('first_name')
                ->get();
        } else {
            $classesList = $examination->target_classes_array;
            $students = Admission::whereIn('class_name', $classesList)
                ->where(function($q) use ($excludedStatuses) {
                    $q->whereNotIn('admission_status', $excludedStatuses)->orWhereNull('admission_status');
                })
                ->orderBy('class_name')
                ->orderBy('first_name')
                ->get();
        }

        // Get marks entered for this exam
        $marks = ExamMark::where('examination_id', $examination->id)->get()->groupBy('admission_id');

        return view('pages.admin.examination.show', compact('examination', 'students', 'marks'));
    }

    public function edit(Examination $examination): View
    {
        $examination->load('schedules');
        $examTypes = ExamType::where('status', 'active')->orderBy('name')->get();
        $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        $academicSessions = AcademicSession::where('status', 'active')->orderBy('id', 'desc')->get();
        $subjects = Subject::orderBy('subject_name')->get();

        return view('pages.admin.examination.edit', compact('examination', 'examTypes', 'classes', 'academicSessions', 'subjects'));
    }

    public function update(Request $request, Examination $examination): RedirectResponse
    {
        $targetClasses = $request->input('target_classes', $request->input('class_name'));
        if (is_array($targetClasses)) {
            if (in_array('All Classes', $targetClasses) || in_array('All', $targetClasses)) {
                $classNameFinal = 'All Classes';
            } else {
                $classNameFinal = implode(', ', array_unique(array_filter($targetClasses)));
            }
        } else {
            $classNameFinal = trim((string) $targetClasses);
        }

        if (empty($classNameFinal)) {
            $classNameFinal = 'All Classes';
        }

        $request->merge(['class_name' => $classNameFinal]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_type_id' => 'required|exists:exam_types,id',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'class_name' => 'required|string',
            'section_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_marks' => 'required|numeric|min:1',
            'pass_marks' => 'required|numeric|min:0|lte:total_marks',
            'status' => 'required|in:scheduled,ongoing,completed,published',
            'description' => 'nullable|string',
            'subjects' => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated, $examination) {
            $examination->update([
                'title' => $validated['title'],
                'exam_type_id' => $validated['exam_type_id'],
                'academic_session_id' => $validated['academic_session_id'] ?? null,
                'class_name' => $validated['class_name'],
                'section_name' => $validated['section_name'] ?? 'All',
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'total_marks' => $validated['total_marks'],
                'pass_marks' => $validated['pass_marks'],
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
            ]);

            if (isset($validated['subjects'])) {
                $examination->schedules()->delete();
                foreach ($validated['subjects'] as $sub) {
                    if (!empty($sub['name'])) {
                        ExamSchedule::create([
                            'examination_id' => $examination->id,
                            'subject_name' => $sub['name'],
                            'max_marks' => $sub['max_marks'] ?? $validated['total_marks'],
                            'pass_marks' => $sub['pass_marks'] ?? $validated['pass_marks'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('examination.show', $examination)
            ->with('status', 'Examination updated successfully.');
    }

    public function destroy(Examination $examination): RedirectResponse
    {
        $examination->delete();
        return redirect()->route('examination.index')->with('status', 'Examination deleted successfully.');
    }

    // Bulk Marks Entry
    public function marks(Examination $examination): View
    {
        $examination->load(['examType', 'schedules']);

        // Default subjects if no schedule created
        $subjects = $examination->schedules->pluck('subject_name')->toArray();
        if (empty($subjects)) {
            $subjects = ['English', 'Mathematics', 'Science', 'Urdu', 'Islamiat', 'Computer'];
        }

        $excludedStatuses = ['rejected', 'inactive', 'cancelled'];
        if ($examination->isAllClasses()) {
            $students = Admission::where(function($q) use ($excludedStatuses) {
                    $q->whereNotIn('admission_status', $excludedStatuses)->orWhereNull('admission_status');
                })
                ->orderBy('class_name')
                ->orderBy('admission_no', 'asc')
                ->get();
        } else {
            $classesList = $examination->target_classes_array;
            $students = Admission::whereIn('class_name', $classesList)
                ->where(function($q) use ($excludedStatuses) {
                    $q->whereNotIn('admission_status', $excludedStatuses)->orWhereNull('admission_status');
                })
                ->orderBy('class_name')
                ->orderBy('admission_no', 'asc')
                ->get();
        }

        $existingMarks = ExamMark::where('examination_id', $examination->id)
            ->get()
            ->groupBy(function($item) {
                return $item->admission_id . '_' . $item->subject_name;
            });

        return view('pages.admin.examination.marks', compact('examination', 'students', 'subjects', 'existingMarks'));
    }

    public function saveMarks(Request $request, Examination $examination): RedirectResponse
    {
        $marksData = $request->input('marks', []);

        DB::transaction(function () use ($examination, $marksData) {
            foreach ($marksData as $admissionId => $subjectMarks) {
                foreach ($subjectMarks as $subjectName => $data) {
                    $isAbsent = isset($data['absent']) && $data['absent'] == '1';
                    $marksObtained = $isAbsent ? null : (isset($data['obtained']) && $data['obtained'] !== '' ? (float)$data['obtained'] : null);
                    $totalMarks = isset($data['total']) && $data['total'] !== '' ? (float)$data['total'] : $examination->total_marks;
                    $passMarks = isset($data['pass']) && $data['pass'] !== '' ? (float)$data['pass'] : $examination->pass_marks;
                    $remarks = $data['remarks'] ?? null;

                    ExamMark::updateOrCreate(
                        [
                            'examination_id' => $examination->id,
                            'admission_id' => $admissionId,
                            'subject_name' => $subjectName,
                        ],
                        [
                            'marks_obtained' => $marksObtained,
                            'total_marks' => $totalMarks,
                            'pass_marks' => $passMarks,
                            'is_absent' => $isAbsent,
                            'remarks' => $remarks,
                        ]
                    );
                }
            }

            // Update exam status to completed if appropriate
            if ($examination->status === 'scheduled') {
                $examination->update(['status' => 'ongoing']);
            }
        });

        return redirect()->route('examination.show', $examination)
            ->with('status', 'Student marks updated successfully.');
    }

    // Results Summary & Performance Analysis
    public function results(Request $request)
    {
        $className = $request->input('class_name');
        $examTypeId = $request->input('exam_type_id');
        $sessionId = $request->input('academic_session_id');

        $examTypes = ExamType::where('status', 'active')->orderBy('name')->get();
        $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        $academicSessions = AcademicSession::where('status', 'active')->orderBy('id', 'desc')->get();

        $query = Examination::with(['examType', 'academicSession', 'marks.admission']);

        if ($className && $className !== 'all') {
            $query->where(function($q) use ($className) {
                $q->where('class_name', 'like', "%{$className}%")
                  ->orWhere('class_name', 'All Classes')
                  ->orWhere('class_name', 'All');
            });
        }
        if ($examTypeId && $examTypeId !== 'all') {
            $query->where('exam_type_id', $examTypeId);
        }
        if ($sessionId && $sessionId !== 'all') {
            $query->where('academic_session_id', $sessionId);
        }

        $examinations = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('pages.admin.examination.partials.result-cards', compact('examinations'))->render();
            return response()->json([
                'html' => $html,
                'has_more' => $examinations->hasMorePages(),
                'next_page_url' => $examinations->nextPageUrl(),
            ]);
        }

        return view('pages.admin.examination.results', compact('examinations', 'examTypes', 'classes', 'academicSessions', 'className', 'examTypeId', 'sessionId'));
    }

    // AJAX filter options endpoint for Examination performance (cascading session -> classes -> students)
    public function filterOptions(Request $request): JsonResponse
    {
        $sessionId = $request->input('academic_session_id');
        $className = $request->input('class_name');

        // Fetch classes for selected academic session
        $classesQuery = StudentClass::where('status', 'active');
        if ($sessionId && $sessionId !== 'all') {
            $sessionClasses = Admission::where('academic_session_id', $sessionId)
                ->whereNotNull('class_name')
                ->distinct()
                ->pluck('class_name')
                ->toArray();
            
            $examClasses = Examination::where('academic_session_id', $sessionId)
                ->whereNotNull('class_name')
                ->pluck('class_name')
                ->toArray();
            
            $allSessionClasses = array_unique(array_merge($sessionClasses, $examClasses));
            
            if (!empty($allSessionClasses)) {
                $classesQuery->whereIn('name', $allSessionClasses);
            }
        }
        $classes = $classesQuery->orderBy('name')->pluck('name');

        // Fetch students matching session and class filters
        $studentsQuery = Admission::query();
        if ($sessionId && $sessionId !== 'all') {
            $studentsQuery->where('academic_session_id', $sessionId);
        }
        if ($className && $className !== 'all') {
            $studentsQuery->where('class_name', $className);
        }
        $studentsQuery->where(function($q) {
            $q->whereNotIn('admission_status', ['rejected', 'inactive', 'cancelled'])
              ->orWhereNull('admission_status');
        });

        $students = $studentsQuery->orderBy('first_name')->take(500)->get()->map(function($st) {
            return [
                'id' => $st->id,
                'text' => $st->student_name . ' (Adm: ' . $st->admission_no . ' | Class: ' . $st->class_name . ')',
                'student_name' => $st->student_name,
                'admission_no' => $st->admission_no,
                'class_name' => $st->class_name,
            ];
        });

        return response()->json([
            'classes' => $classes,
            'students' => $students,
        ]);
    }

    // Student Performance across exam types (Daily, 3rd Day, Weekly, Monthly, Mid, Term, Annual, Re-board)
    public function studentPerformance(Request $request): View
    {
        $admissionId = $request->input('admission_id');
        $className = $request->input('class_name');
        $sessionId = $request->input('academic_session_id');

        $classesQuery = StudentClass::where('status', 'active');
        if ($sessionId && $sessionId !== 'all') {
            $sessionClasses = Admission::where('academic_session_id', $sessionId)
                ->whereNotNull('class_name')
                ->distinct()
                ->pluck('class_name')
                ->toArray();
            $examClasses = Examination::where('academic_session_id', $sessionId)
                ->whereNotNull('class_name')
                ->pluck('class_name')
                ->toArray();
            $allSessionClasses = array_unique(array_merge($sessionClasses, $examClasses));
            if (!empty($allSessionClasses)) {
                $classesQuery->whereIn('name', $allSessionClasses);
            }
        }
        $classes = $classesQuery->orderBy('name')->get();

        $academicSessions = AcademicSession::where('status', 'active')->orderBy('id', 'desc')->get();
        $examTypes = ExamType::where('status', 'active')->orderBy('id')->get();

        $studentsQuery = Admission::query();
        if ($className && $className !== 'all') {
            $studentsQuery->where('class_name', $className);
        }
        if ($sessionId && $sessionId !== 'all') {
            $studentsQuery->where('academic_session_id', $sessionId);
        }
        $studentsQuery->where(function($q) {
            $q->whereNotIn('admission_status', ['rejected', 'inactive', 'cancelled'])
              ->orWhereNull('admission_status');
        });

        $students = $studentsQuery->orderBy('first_name')->take(200)->get();

        // If no student selected yet, auto-select first student
        if (!$admissionId && $students->isNotEmpty()) {
            $admissionId = $students->first()->id;
        }

        $selectedStudent = null;
        $performanceData = [];
        $examTypeStats = [];

        if ($admissionId) {
            $selectedStudent = Admission::find($admissionId);
            if ($selectedStudent) {
                // Get all marks for this student grouped by Exam Type
                $marksQuery = ExamMark::with(['examination.examType', 'examination.academicSession'])
                    ->where('admission_id', $selectedStudent->id);

                if ($sessionId && $sessionId !== 'all') {
                    $marksQuery->whereHas('examination', function($q) use ($sessionId) {
                        $q->where('academic_session_id', $sessionId);
                    });
                }

                $marks = $marksQuery->get();

                foreach ($examTypes as $type) {
                    $typeMarks = $marks->filter(function($m) use ($type) {
                        return optional($m->examination)->exam_type_id == $type->id;
                    });

                    $obtainedSum = $typeMarks->where('is_absent', false)->sum('marks_obtained');
                    $totalSum = $typeMarks->sum('total_marks');
                    $percentage = $totalSum > 0 ? round(($obtainedSum / $totalSum) * 100, 1) : 0;

                    $examTypeStats[$type->name] = [
                        'type' => $type,
                        'count' => $typeMarks->unique('examination_id')->count(),
                        'obtained' => $obtainedSum,
                        'total' => $totalSum,
                        'percentage' => $percentage,
                        'marks' => $typeMarks,
                    ];
                }
            }
        }

        return view('pages.admin.examination.performance', compact('students', 'selectedStudent', 'examTypes', 'examTypeStats', 'classes', 'academicSessions', 'className', 'sessionId', 'admissionId'));
    }

    // Official Student Marksheet Progress Report Card View & Print
    public function resultCard(Admission $admission, Request $request): View
    {
        $examinationId = $request->input('examination_id');
        $admission->load('academicSession');

        $examinations = Examination::where(function($q) use ($admission) {
            $q->where('class_name', 'like', "%{$admission->class_name}%")
              ->orWhere('class_name', 'All Classes')
              ->orWhere('class_name', 'All');
        })->get();

        $selectedExam = null;
        if ($examinationId) {
            $selectedExam = Examination::with('examType')->find($examinationId);
        } else {
            $selectedExam = Examination::with('examType')->where(function($q) use ($admission) {
                $q->where('class_name', 'like', "%{$admission->class_name}%")
                  ->orWhere('class_name', 'All Classes')
                  ->orWhere('class_name', 'All');
            })->latest()->first();
        }

        $marks = collect();
        if ($selectedExam) {
            $marks = ExamMark::where('examination_id', $selectedExam->id)
                ->where('admission_id', $admission->id)
                ->get();
        }

        return view('pages.admin.examination.result_card', compact('admission', 'selectedExam', 'examinations', 'marks'));
    }

    // Official Complete Cumulative Marksheet Print View
    public function completeMarksheet(Admission $admission, Request $request): View
    {
        $sessionId = $request->input('academic_session_id');
        $className = $request->input('class_name');
        $admission->load('academicSession');

        $examTypes = ExamType::where('status', 'active')->orderBy('id')->get();

        $marksQuery = ExamMark::with(['examination.examType', 'examination.academicSession'])
            ->where('admission_id', $admission->id);

        if ($sessionId && $sessionId !== 'all') {
            $marksQuery->whereHas('examination', function($q) use ($sessionId) {
                $q->where('academic_session_id', $sessionId);
            });
        }

        $allMarks = $marksQuery->get();

        $examTypeStats = [];
        $totalMaxAll = 0;
        $totalObtainedAll = 0;

        foreach ($examTypes as $type) {
            $typeMarks = $allMarks->filter(function($m) use ($type) {
                return optional($m->examination)->exam_type_id == $type->id;
            });

            $obtainedSum = $typeMarks->where('is_absent', false)->sum('marks_obtained');
            $totalSum = $typeMarks->sum('total_marks');
            $percentage = $totalSum > 0 ? round(($obtainedSum / $totalSum) * 100, 1) : 0;
            $examsCount = $typeMarks->unique('examination_id')->count();

            $totalMaxAll += $totalSum;
            $totalObtainedAll += $obtainedSum;

            $examTypeStats[$type->name] = [
                'type' => $type,
                'count' => $examsCount,
                'obtained' => $obtainedSum,
                'total' => $totalSum,
                'percentage' => $percentage,
                'marks' => $typeMarks,
            ];
        }

        $cumulativePct = $totalMaxAll > 0 ? round(($totalObtainedAll / $totalMaxAll) * 100, 1) : 0;

        return view('pages.admin.examination.complete_marksheet', compact(
            'admission',
            'sessionId',
            'className',
            'examTypes',
            'examTypeStats',
            'allMarks',
            'totalMaxAll',
            'totalObtainedAll',
            'cumulativePct'
        ));
    }
}
