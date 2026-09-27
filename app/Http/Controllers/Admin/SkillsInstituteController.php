<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolInfo;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\StudentSkill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SkillsInstituteController extends Controller
{
    public const CATEGORIES = [
        'IT & Programming',
        'Robotics & Electronics',
        'Quranic & Tajweed',
        'Communication & Soft Skills',
        'Arts & Calligraphy',
        'Technical & Crafts',
        'Physical & Sports',
        'Science & Tinkering',
    ];

    public function index(Request $request)
    {
        $classes = StudentClass::orderBy('name')->get();
        $categories = self::CATEGORIES;

        $query = StudentSkill::with(['student', 'studentClass', 'creator'])
            ->latest('evaluation_date')
            ->latest('id');

        if ($request->filled('class_name')) {
            $className = $request->class_name;
            $query->where(function ($q) use ($className) {
                $q->whereHas('studentClass', function ($c) use ($className) {
                    $c->where('name', $className);
                })->orWhereHas('student', function ($s) use ($className) {
                    $s->where('class_name', $className);
                });
            });
        }

        if ($request->filled('category')) {
            $query->where('skill_category', $request->category);
        }

        if ($request->filled('period')) {
            $query->where('performance_period', $request->period);
        }

        if ($request->filled('date')) {
            $query->whereDate('evaluation_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('skill_name', 'like', "%{$search}%")
                  ->orWhere('instructor_notes', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($s) use ($search) {
                      $s->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('roll_no', 'like', "%{$search}%")
                        ->orWhere('admission_no', 'like', "%{$search}%");
                  });
            });
        }

        $skills = $query->paginate(15)->appends($request->query());

        // Overview Summary Statistics
        $allRecords = StudentSkill::all();
        $allSubSkills = collect();
        foreach ($allRecords as $rec) {
            foreach ($rec->skills_list as $item) {
                $allSubSkills->push($item);
            }
        }
        $totalEvaluationsCount = $allSubSkills->count();
        $avgStarRating        = $allSubSkills->avg('star_rating') ?: 0.0;
        $monthlyAvgStars       = StudentSkill::whereMonth('evaluation_date', Carbon::now()->month)
            ->whereYear('evaluation_date', Carbon::now()->year)
            ->avg('star_rating') ?: 0.0;
        $masterSkilledCount    = $allSubSkills->where('star_rating', '>=', 4.8)->count();

        $topCategoryRow = StudentSkill::select('skill_category', DB::raw('count(*) as total'))
            ->groupBy('skill_category')
            ->orderByDesc('total')
            ->first();
        $topCategory = $topCategoryRow?->skill_category ?? 'N/A';

        return view('pages.admin.skills-institute.index', compact(
            'skills', 'classes', 'categories',
            'totalEvaluationsCount', 'avgStarRating', 'monthlyAvgStars', 'masterSkilledCount', 'topCategory'
        ));
    }

    public function create()
    {
        $academicSessions = AcademicSession::where('status', 'Active')->orderBy('start_date', 'desc')->get();
        if ($academicSessions->isEmpty()) {
            $academicSessions = AcademicSession::orderBy('id', 'desc')->get();
        }
        $activeSession = AcademicSession::where('status', 'Active')->first() ?? $academicSessions->first();

        // Get classes related to active session
        $query = Student::where('status', 'active');
        if ($activeSession) {
            $query->where('academic_session_id', $activeSession->id);
        }
        $classNames = $query->whereNotNull('class_name')->distinct()->pluck('class_name');

        if ($classNames->isEmpty()) {
            $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        } else {
            $classes = StudentClass::whereIn('name', $classNames)->where('status', 'active')->orderBy('name')->get();
        }

        $categories = self::CATEGORIES;
        return view('pages.admin.skills-institute.create', compact('academicSessions', 'activeSession', 'classes', 'categories'));
    }

    public function store(Request $request)
    {
        // Support both multiple skills array (`skills`) and fallback single skill
        if ($request->has('skills') && is_array($request->skills)) {
            $request->validate([
                'academic_session_id'          => 'nullable|exists:academic_sessions,id',
                'student_id'                   => 'required|exists:students,id',
                'evaluation_date'              => 'required|date',
                'skills'                       => 'required|array|min:1',
                'skills.*.skill_category'      => 'required|string',
                'skills.*.skill_name'          => 'required|string|max:255',
                'skills.*.assessment_type'     => 'required|string',
                'skills.*.performance_period'  => 'required|in:daily,weekly,monthly,quarterly',
                'skills.*.obtained_score'      => 'required|numeric|min:0',
                'skills.*.total_score'         => 'required|numeric|min:0.1',
                'skills.*.instructor_notes'    => 'nullable|string',
            ]);

            $skillItems = $request->skills;
        } else {
            $request->validate([
                'academic_session_id' => 'nullable|exists:academic_sessions,id',
                'student_id'          => 'required|exists:students,id',
                'evaluation_date'     => 'required|date',
                'skill_category'      => 'required|string',
                'skill_name'          => 'required|string|max:255',
                'assessment_type'     => 'required|string',
                'performance_period'  => 'required|in:daily,weekly,monthly,quarterly',
                'obtained_score'      => 'required|numeric|min:0',
                'total_score'         => 'required|numeric|min:0.1',
                'instructor_notes'    => 'nullable|string',
            ]);

            $skillItems = [[
                'skill_category'     => $request->skill_category,
                'skill_name'         => $request->skill_name,
                'assessment_type'    => $request->assessment_type,
                'performance_period' => $request->performance_period,
                'total_score'        => $request->total_score,
                'obtained_score'     => $request->obtained_score,
                'instructor_notes'   => $request->instructor_notes,
            ]];
        }

        $student   = Student::findOrFail($request->student_id);
        $classObj  = StudentClass::where('name', $student->class_name)->first();
        $sessionId = $request->academic_session_id ?: $student->academic_session_id;

        $processedSkills = [];
        $sumObtained = 0;
        $sumTotal = 0;

        foreach ($skillItems as $item) {
            $total    = floatval($item['total_score']);
            $obtained = floatval($item['obtained_score']);
            $itemStar = StudentSkill::calculateStarRating($obtained, $total);
            $itemBadge = StudentSkill::determineBadgeLevel($itemStar);

            $sumObtained += $obtained;
            $sumTotal += $total;

            $processedSkills[] = [
                'skill_category'     => $item['skill_category'],
                'skill_name'         => $item['skill_name'],
                'assessment_type'    => $item['assessment_type'],
                'performance_period' => $item['performance_period'],
                'total_score'        => $total,
                'obtained_score'     => $obtained,
                'star_rating'        => $itemStar,
                'badge_level'        => $itemBadge,
                'instructor_notes'   => $item['instructor_notes'] ?? null,
            ];
        }

        $overallStarRating = StudentSkill::calculateStarRating($sumObtained, $sumTotal);
        $overallBadgeLevel = StudentSkill::determineBadgeLevel($overallStarRating);

        $firstItem = $processedSkills[0];
        $countSkills = count($processedSkills);

        $allSkillNames = implode(', ', array_column($processedSkills, 'skill_name'));
        if (strlen($allSkillNames) > 250) {
            $allSkillNames = substr($allSkillNames, 0, 247) . '...';
        }

        $allCategories = implode(', ', array_unique(array_column($processedSkills, 'skill_category')));
        if (strlen($allCategories) > 250) {
            $allCategories = substr($allCategories, 0, 247) . '...';
        }

        $skillRecord = StudentSkill::create([
            'student_id'          => $student->id,
            'academic_session_id' => $sessionId,
            'student_class_id'    => $classObj?->id,
            'evaluation_date'     => $request->evaluation_date,
            'skill_category'      => $allCategories,
            'skill_name'          => $allSkillNames,
            'assessment_type'     => $firstItem['assessment_type'],
            'performance_period'  => $firstItem['performance_period'],
            'total_score'         => $sumTotal,
            'obtained_score'      => $sumObtained,
            'star_rating'         => $overallStarRating,
            'badge_level'         => $overallBadgeLevel,
            'instructor_notes'    => $firstItem['instructor_notes'],
            'skills_data'         => $processedSkills,
            'created_by'          => Auth::id(),
        ]);

        $skillMsg = $countSkills > 1 ? "{$countSkills} skills evaluation record" : "Skill evaluation record";

        return redirect()->route('skills-institute.show', $skillRecord->id)
            ->with('success', "{$skillMsg} recorded successfully for {$student->full_name}!");
    }

    public function show($id)
    {
        $skill = StudentSkill::with(['student', 'studentClass', 'academicSession', 'creator'])->findOrFail($id);
        $student = $skill->student;

        $allStudentSkills = StudentSkill::where('student_id', $student->id)
            ->latest('evaluation_date')
            ->get();

        $allSubSkills = collect();
        foreach ($allStudentSkills as $rec) {
            foreach ($rec->skills_list as $item) {
                $allSubSkills->push($item);
            }
        }

        $avgStars = $allSubSkills->avg('star_rating') ?: 0.0;
        $totalEvaluations = $allSubSkills->count();
        $bestSkill = $allSubSkills->sortByDesc('star_rating')->first();

        $categoryBreakdown = $allSubSkills->groupBy('skill_category')->map(function ($group) {
            return [
                'count'     => $group->count(),
                'avg_stars' => round($group->avg('star_rating'), 1),
                'max_stars' => round($group->max('star_rating'), 1),
            ];
        });

        return view('pages.admin.skills-institute.show', compact(
            'skill', 'student', 'allStudentSkills', 'avgStars', 'totalEvaluations', 'bestSkill', 'categoryBreakdown'
        ));
    }

    public function edit($id)
    {
        $skill      = StudentSkill::with(['student', 'studentClass', 'academicSession'])->findOrFail($id);
        $academicSessions = AcademicSession::where('status', 'Active')->orderBy('start_date', 'desc')->get();
        if ($academicSessions->isEmpty()) {
            $academicSessions = AcademicSession::orderBy('id', 'desc')->get();
        }

        $currentSessionId = $skill->academic_session_id ?? $skill->student?->academic_session_id ?? $academicSessions->first()?->id;

        // Fetch classes corresponding to session
        $query = Student::where('status', 'active');
        if ($currentSessionId) {
            $query->where('academic_session_id', $currentSessionId);
        }
        $classNames = $query->whereNotNull('class_name')->distinct()->pluck('class_name');
        if ($classNames->isEmpty()) {
            $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        } else {
            $classes = StudentClass::whereIn('name', $classNames)->where('status', 'active')->orderBy('name')->get();
        }

        $categories = self::CATEGORIES;

        // Fetch students for selected session & class
        $currentClassName = $skill->studentClass?->name ?? $skill->student?->class_name;
        $studentsQuery = Student::where('status', 'active');
        if ($currentSessionId) {
            $studentsQuery->where('academic_session_id', $currentSessionId);
        }
        if ($currentClassName) {
            $studentsQuery->where('class_name', $currentClassName);
        }
        $students = $studentsQuery->orderBy('first_name')->get();

        return view('pages.admin.skills-institute.edit', compact('skill', 'academicSessions', 'currentSessionId', 'classes', 'categories', 'students'));
    }

    public function update(Request $request, $id)
    {
        $skill = StudentSkill::findOrFail($id);

        if ($request->has('skills') && is_array($request->skills)) {
            $request->validate([
                'academic_session_id'          => 'nullable|exists:academic_sessions,id',
                'student_id'                   => 'required|exists:students,id',
                'evaluation_date'              => 'required|date',
                'skills'                       => 'required|array|min:1',
                'skills.*.skill_category'      => 'required|string',
                'skills.*.skill_name'          => 'required|string|max:255',
                'skills.*.assessment_type'     => 'required|string',
                'skills.*.performance_period'  => 'required|in:daily,weekly,monthly,quarterly',
                'skills.*.obtained_score'      => 'required|numeric|min:0',
                'skills.*.total_score'         => 'required|numeric|min:0.1',
                'skills.*.instructor_notes'    => 'nullable|string',
            ]);

            $skillItems = $request->skills;
        } else {
            $request->validate([
                'academic_session_id' => 'nullable|exists:academic_sessions,id',
                'student_id'          => 'required|exists:students,id',
                'evaluation_date'     => 'required|date',
                'skill_category'      => 'required|string',
                'skill_name'          => 'required|string|max:255',
                'assessment_type'     => 'required|string',
                'performance_period'  => 'required|in:daily,weekly,monthly,quarterly',
                'obtained_score'      => 'required|numeric|min:0',
                'total_score'         => 'required|numeric|min:0.1',
                'instructor_notes'    => 'nullable|string',
            ]);

            $skillItems = [[
                'skill_category'     => $request->skill_category,
                'skill_name'         => $request->skill_name,
                'assessment_type'    => $request->assessment_type,
                'performance_period' => $request->performance_period,
                'total_score'        => $request->total_score,
                'obtained_score'     => $request->obtained_score,
                'instructor_notes'   => $request->instructor_notes,
            ]];
        }

        $student  = Student::findOrFail($request->student_id);
        $classObj = StudentClass::where('name', $student->class_name)->first();

        $processedSkills = [];
        $sumObtained = 0;
        $sumTotal = 0;

        foreach ($skillItems as $item) {
            $total    = floatval($item['total_score']);
            $obtained = floatval($item['obtained_score']);
            $itemStar = StudentSkill::calculateStarRating($obtained, $total);
            $itemBadge = StudentSkill::determineBadgeLevel($itemStar);

            $sumObtained += $obtained;
            $sumTotal += $total;

            $processedSkills[] = [
                'skill_category'     => $item['skill_category'],
                'skill_name'         => $item['skill_name'],
                'assessment_type'    => $item['assessment_type'],
                'performance_period' => $item['performance_period'],
                'total_score'        => $total,
                'obtained_score'     => $obtained,
                'star_rating'        => $itemStar,
                'badge_level'        => $itemBadge,
                'instructor_notes'   => $item['instructor_notes'] ?? null,
            ];
        }

        $overallStarRating = StudentSkill::calculateStarRating($sumObtained, $sumTotal);
        $overallBadgeLevel = StudentSkill::determineBadgeLevel($overallStarRating);

        $firstItem = $processedSkills[0];
        $countSkills = count($processedSkills);

        $allSkillNames = implode(', ', array_column($processedSkills, 'skill_name'));
        if (strlen($allSkillNames) > 250) {
            $allSkillNames = substr($allSkillNames, 0, 247) . '...';
        }

        $allCategories = implode(', ', array_unique(array_column($processedSkills, 'skill_category')));
        if (strlen($allCategories) > 250) {
            $allCategories = substr($allCategories, 0, 247) . '...';
        }

        $data = [
            'student_id'          => $student->id,
            'academic_session_id' => $request->academic_session_id ?: $student->academic_session_id,
            'student_class_id'    => $classObj?->id,
            'evaluation_date'     => $request->evaluation_date,
            'skill_category'      => $allCategories,
            'skill_name'          => $allSkillNames,
            'assessment_type'     => $firstItem['assessment_type'],
            'performance_period'  => $firstItem['performance_period'],
            'total_score'         => $sumTotal,
            'obtained_score'      => $sumObtained,
            'star_rating'         => $overallStarRating,
            'badge_level'         => $overallBadgeLevel,
            'instructor_notes'    => $firstItem['instructor_notes'],
            'skills_data'         => $processedSkills,
        ];

        $skill->update($data);

        return redirect()->route('skills-institute.show', $skill->id)
            ->with('success', 'Skill evaluation record updated successfully!');
    }

    public function destroy($id)
    {
        $skill = StudentSkill::findOrFail($id);
        $skill->delete();

        return redirect()->route('skills-institute.index')
            ->with('success', 'Skill evaluation record deleted successfully!');
    }

    public function print($id)
    {
        $skill = StudentSkill::with(['student', 'studentClass', 'academicSession', 'creator'])->findOrFail($id);
        $student = $skill->student;
        $globalSchoolInfo = SchoolInfo::first();

        return view('pages.admin.skills-institute.print', compact('skill', 'student', 'globalSchoolInfo'));
    }

    public function certificate($id)
    {
        $skill = StudentSkill::with(['student', 'studentClass', 'academicSession', 'creator'])->findOrFail($id);
        $student = $skill->student;
        $globalSchoolInfo = SchoolInfo::first();

        return view('pages.admin.skills-institute.print_certificate', compact('skill', 'student', 'globalSchoolInfo'));
    }

    /**
     * AJAX: Get classes for selected academic session
     */
    public function getClassesBySession($sessionId)
    {
        $query = Student::where('status', 'active');
        if ($sessionId && $sessionId !== 'all') {
            $query->where('academic_session_id', $sessionId);
        }

        $classNames = $query->whereNotNull('class_name')->distinct()->pluck('class_name');

        if ($classNames->isEmpty()) {
            $classes = StudentClass::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        } else {
            $classes = StudentClass::whereIn('name', $classNames)->where('status', 'active')->orderBy('name')->get(['id', 'name']);
        }

        return response()->json([
            'success' => true,
            'classes' => $classes,
        ]);
    }

    /**
     * AJAX: Returns list of students for a class (filtered optional by session_id)
     */
    public function getStudentsByClass(Request $request, $className)
    {
        $query = Student::where('status', 'active');

        if ($className !== 'all') {
            $query->where('class_name', $className);
        }

        if ($request->filled('session_id') && $request->session_id !== 'all') {
            $query->where('academic_session_id', $request->session_id);
        }

        $students = $query->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'roll_no', 'admission_no', 'class_name']);

        return response()->json([
            'success'  => true,
            'students' => $students->map(fn($s) => [
                'id'           => $s->id,
                'first_name'   => $s->first_name,
                'last_name'    => $s->last_name,
                'roll_no'      => $s->roll_no,
                'admission_no' => $s->admission_no,
                'class_name'   => $s->class_name,
            ]),
        ]);
    }

    /**
     * AJAX: Get student skills history summary
     */
    public function getStudentSkillsHistory($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $skills = StudentSkill::where('student_id', $studentId)->latest('evaluation_date')->get();
        $avgStars = $skills->avg('star_rating') ?: 0.0;

        return response()->json([
            'success'     => true,
            'total'       => $skills->count(),
            'avg_stars'   => round($avgStars, 1),
            'latest'      => $skills->first() ? [
                'skill_name'  => $skills->first()->skill_name,
                'category'    => $skills->first()->skill_category,
                'star_rating' => $skills->first()->star_rating,
                'badge_level' => $skills->first()->badge_level,
            ] : null,
        ]);
    }
}
