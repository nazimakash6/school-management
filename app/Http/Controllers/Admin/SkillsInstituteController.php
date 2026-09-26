<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
                  ->orWhere('certificate_code', 'like', "%{$search}%")
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
        $totalEvaluationsCount = StudentSkill::count();
        $avgStarRating        = StudentSkill::avg('star_rating') ?: 0.0;
        $monthlyAvgStars       = StudentSkill::whereMonth('evaluation_date', Carbon::now()->month)
            ->whereYear('evaluation_date', Carbon::now()->year)
            ->avg('star_rating') ?: 0.0;
        $masterSkilledCount    = StudentSkill::where('star_rating', '>=', 4.8)->count();

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
        $classes = StudentClass::orderBy('name')->get();
        $categories = self::CATEGORIES;
        return view('pages.admin.skills-institute.create', compact('classes', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'         => 'required|exists:students,id',
            'evaluation_date'    => 'required|date',
            'skill_category'     => 'required|string',
            'skill_name'         => 'required|string|max:255',
            'assessment_type'    => 'required|string',
            'performance_period' => 'required|in:daily,weekly,monthly,quarterly',
            'obtained_score'     => 'required|numeric|min:0',
            'total_score'        => 'required|numeric|min:0.1',
            'certificate_code'   => 'nullable|string|max:100',
            'instructor_notes'   => 'nullable|string',
            'image_proof'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $student  = Student::findOrFail($request->student_id);
        $total    = floatval($request->total_score);
        $obtained = floatval($request->obtained_score);

        $starRating = StudentSkill::calculateStarRating($obtained, $total);
        $badgeLevel = StudentSkill::determineBadgeLevel($starRating);
        $classObj   = StudentClass::where('name', $student->class_name)->first();

        $imagePath = null;
        if ($request->hasFile('image_proof')) {
            $imagePath = $request->file('image_proof')->store('skill_proofs', 'public');
        }

        $certCode = $request->certificate_code ?: ('SKL-' . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $request->skill_category), 0, 3)) . '-' . date('Y') . '-' . sprintf('%04d', rand(1, 9999)));

        StudentSkill::create([
            'student_id'         => $student->id,
            'student_class_id'   => $classObj?->id,
            'evaluation_date'    => $request->evaluation_date,
            'skill_category'     => $request->skill_category,
            'skill_name'         => $request->skill_name,
            'assessment_type'    => $request->assessment_type,
            'performance_period' => $request->performance_period,
            'total_score'        => $total,
            'obtained_score'     => $obtained,
            'star_rating'        => $starRating,
            'badge_level'        => $badgeLevel,
            'certificate_code'   => $certCode,
            'instructor_notes'   => $request->instructor_notes,
            'image_proof'        => $imagePath,
            'created_by'         => Auth::id(),
        ]);

        return redirect()->route('skills-institute.index')
            ->with('success', "Skill evaluation recorded for {$student->full_name} — {$starRating} Stars ({$badgeLevel})!");
    }

    public function show($id)
    {
        $skill = StudentSkill::with(['student', 'studentClass', 'creator'])->findOrFail($id);
        $student = $skill->student;

        $allStudentSkills = StudentSkill::where('student_id', $student->id)
            ->latest('evaluation_date')
            ->get();

        $avgStars = $allStudentSkills->avg('star_rating') ?: 0.0;
        $totalEvaluations = $allStudentSkills->count();
        $bestSkill = $allStudentSkills->sortByDesc('star_rating')->first();

        $categoryBreakdown = $allStudentSkills->groupBy('skill_category')->map(function ($group) {
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
        $skill      = StudentSkill::with('student')->findOrFail($id);
        $classes    = StudentClass::orderBy('name')->get();
        $categories = self::CATEGORIES;
        $students   = Student::where('class_name', $skill->student->class_name)->orderBy('first_name')->get();

        return view('pages.admin.skills-institute.edit', compact('skill', 'classes', 'categories', 'students'));
    }

    public function update(Request $request, $id)
    {
        $skill = StudentSkill::findOrFail($id);

        $request->validate([
            'evaluation_date'    => 'required|date',
            'skill_category'     => 'required|string',
            'skill_name'         => 'required|string|max:255',
            'assessment_type'    => 'required|string',
            'performance_period' => 'required|in:daily,weekly,monthly,quarterly',
            'obtained_score'     => 'required|numeric|min:0',
            'total_score'        => 'required|numeric|min:0.1',
            'certificate_code'   => 'nullable|string|max:100',
            'instructor_notes'   => 'nullable|string',
            'image_proof'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $total    = floatval($request->total_score);
        $obtained = floatval($request->obtained_score);
        $starRating = StudentSkill::calculateStarRating($obtained, $total);
        $badgeLevel = StudentSkill::determineBadgeLevel($starRating);

        $data = [
            'evaluation_date'    => $request->evaluation_date,
            'skill_category'     => $request->skill_category,
            'skill_name'         => $request->skill_name,
            'assessment_type'    => $request->assessment_type,
            'performance_period' => $request->performance_period,
            'total_score'        => $total,
            'obtained_score'     => $obtained,
            'star_rating'        => $starRating,
            'badge_level'        => $badgeLevel,
            'certificate_code'   => $request->certificate_code,
            'instructor_notes'   => $request->instructor_notes,
        ];

        if ($request->hasFile('image_proof')) {
            if ($skill->image_proof) {
                Storage::disk('public')->delete($skill->image_proof);
            }
            $data['image_proof'] = $request->file('image_proof')->store('skill_proofs', 'public');
        }

        $skill->update($data);

        return redirect()->route('skills-institute.index')
            ->with('success', 'Skill evaluation record updated successfully!');
    }

    public function destroy($id)
    {
        $skill = StudentSkill::findOrFail($id);
        if ($skill->image_proof) {
            Storage::disk('public')->delete($skill->image_proof);
        }
        $skill->delete();

        return redirect()->route('skills-institute.index')
            ->with('success', 'Skill evaluation record deleted successfully!');
    }

    /**
     * AJAX: Returns list of students for a class
     */
    public function getStudentsByClass($className)
    {
        $students = Student::where('class_name', $className)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'roll_no', 'admission_no', 'class_name']);

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
