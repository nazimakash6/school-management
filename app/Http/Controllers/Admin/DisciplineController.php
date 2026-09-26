<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\StudentDiscipline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisciplineController extends Controller
{
    public function index(Request $request)
    {
        $academicSessions = AcademicSession::orderBy('session_name', 'desc')->get();
        $classes          = StudentClass::orderBy('name')->get();

        $query = StudentDiscipline::with(['student.academicSession', 'studentClass', 'creator'])
            ->latest('entry_date')
            ->latest('id');

        if ($request->filled('academic_session_id')) {
            $sessionId = $request->academic_session_id;
            $query->whereHas('student', function ($s) use ($sessionId) {
                $s->where('academic_session_id', $sessionId);
            });
        }

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

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('category')) {
            $query->where('category', 'like', "%{$request->category}%");
        }

        if ($request->filled('date')) {
            $query->whereDate('entry_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($s) use ($search) {
                      $s->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('roll_no', 'like', "%{$search}%")
                        ->orWhere('admission_no', 'like', "%{$search}%");
                  });
            });
        }

        $filterStudents = collect();
        if ($request->filled('class_name')) {
            $filterStudents = Student::where('class_name', $request->class_name)
                ->when($request->filled('academic_session_id'), function ($q) use ($request) {
                    $q->where('academic_session_id', $request->academic_session_id);
                })
                ->orderBy('first_name')
                ->get();
        }

        $disciplines = $query->paginate(15)->appends($request->query());

        $today       = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        $dailyAvgStar   = StudentDiscipline::whereDate('entry_date', $today)->avg('star_rating') ?: 0.0;
        $weeklyAvgStar  = StudentDiscipline::whereBetween('entry_date', [$startOfWeek, $endOfWeek])->avg('star_rating') ?: 0.0;
        $monthlyAvgStar = StudentDiscipline::whereMonth('entry_date', Carbon::now()->month)->whereYear('entry_date', Carbon::now()->year)->avg('star_rating') ?: 0.0;
        $yearlyAvgStar  = StudentDiscipline::whereYear('entry_date', Carbon::now()->year)->avg('star_rating') ?: 0.0;
        $totalRecordsCount = StudentDiscipline::count();

        return view('pages.admin.discipline.index', compact(
            'disciplines', 'academicSessions', 'classes', 'filterStudents',
            'dailyAvgStar', 'weeklyAvgStar', 'monthlyAvgStar', 'yearlyAvgStar', 'totalRecordsCount'
        ));
    }

    public function create()
    {
        $classes = StudentClass::orderBy('name')->get();
        return view('pages.admin.discipline.create', compact('classes'));
    }

    /**
     * AJAX: Returns students for a class (simple list, with optional session filter)
     */
    public function getStudentsByClass($className)
    {
        $query = Student::where('class_name', $className);
        if (request()->has('session_id') && request()->session_id) {
            $query->where('academic_session_id', request()->session_id);
        }

        $students = $query->orderBy('first_name')
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
     * AJAX: Get attendance score for a student for a given period.
     *
     * Periods:
     *   daily   → single date (max 2 pts)
     *   weekly  → Mon–Sun week containing the entry date (max = days_recorded × 2)
     *   monthly → full calendar month (max = days_recorded × 2)
     *
     * Per-day scoring:
     *   Both sessions Present → 2 pts
     *   One session Present   → 1 pt
     *   Leave                 → 0.3 pts
     *   Absent / no record    → 0 pts
     */
    public function getAttendanceScore(Request $request, $studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        $date   = $request->get('date', date('Y-m-d'));
        $period = $request->get('period', 'daily');

        $carbon = Carbon::parse($date);

        switch ($period) {
            case 'weekly':
                $from        = $carbon->copy()->startOfWeek();
                $to          = $carbon->copy()->endOfWeek();
                $periodLabel = 'Week of ' . $from->format('M d') . ' – ' . $to->format('M d, Y');
                break;
            case 'monthly':
                $from        = $carbon->copy()->startOfMonth();
                $to          = $carbon->copy()->endOfMonth();
                $periodLabel = $carbon->format('F Y');
                break;
            default:
                $from        = $carbon->copy();
                $to          = $carbon->copy();
                $periodLabel = $carbon->format('M d, Y');
                break;
        }

        $attendances = Attendance::where('user_type', 'student')
            ->where('user_id', $studentId)
            ->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->get();

        $totalDays  = $attendances->count();
        $totalMax   = $period === 'daily' ? 2 : ($totalDays * 2);

        if ($totalDays === 0) {
            return response()->json([
                'success'        => true,
                'has_record'     => false,
                'period'         => $period,
                'period_label'   => $periodLabel,
                'obtained_score' => 0,
                'total_score'    => $period === 'daily' ? 2 : 0,
                'star_rating'    => 0.0,
                'days_counted'   => 0,
                'remarks'        => "No attendance records found for {$student->first_name} in {$periodLabel}. Score: 0 — Rating: 0.0 Stars.",
            ]);
        }

        $obtained = 0.0;
        $presentBoth = 0; $presentOne = 0; $leaveDays = 0; $absentDays = 0;
        $s1daily = null; $s2daily = null;

        foreach ($attendances as $att) {
            $s1 = $this->statusLabel($att->session1_status);
            $s2 = $this->statusLabel($att->session2_status);
            $finalSt = $this->statusLabel($att->final_status);

            if ($s1 === 'present' && $s2 === 'present') {
                $obtained += 2; $presentBoth++;
            } elseif ($s1 === 'present' || $s2 === 'present') {
                $obtained += 1; $presentOne++;
            } elseif ($s1 === 'leave' || $s2 === 'leave' || $finalSt === 'leave') {
                $obtained += 0.3; $leaveDays++;
            } else {
                $absentDays++;
            }

            // Store daily session labels for single-day display
            if ($period === 'daily') {
                $s1daily = $s1;
                $s2daily = $s2;
            }
        }

        $starRating = $totalMax > 0 ? round(($obtained / $totalMax) * 5.0, 1) : 0.0;

        if ($period === 'daily') {
            $remarks = "Attendance on {$periodLabel}: Session 1 = " . ucfirst($s1daily ?? 'N/A')
                     . ", Session 2 = " . ucfirst($s2daily ?? 'N/A')
                     . ". Score: {$obtained}/2 — Rating: {$starRating}/5.0 Stars.";
        } else {
            $remarks = "Attendance ({$periodLabel}): {$totalDays} day(s) recorded — "
                     . "Both Present: {$presentBoth}d, One Session: {$presentOne}d, Leave: {$leaveDays}d, Absent: {$absentDays}d. "
                     . "Total Score: {$obtained}/{$totalMax} — Rating: {$starRating}/5.0 Stars.";
        }

        return response()->json([
            'success'        => true,
            'has_record'     => true,
            'period'         => $period,
            'period_label'   => $periodLabel,
            'days_counted'   => $totalDays,
            'obtained_score' => $obtained,
            'total_score'    => $totalMax,
            'star_rating'    => $starRating,
            'session1'       => $s1daily,
            'session2'       => $s2daily,
            'breakdown'      => compact('presentBoth', 'presentOne', 'leaveDays', 'absentDays'),
            'remarks'        => $remarks,
        ]);
    }


    /** Normalise a status value to a lowercase string */
    private function statusLabel($status): string
    {
        if (!$status) return 'absent';
        $val = $status instanceof \BackedEnum ? $status->value : (string) $status;
        return strtolower(trim($val));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'         => 'required|exists:students,id',
            'entry_date'         => 'required|date',
            'title'              => 'nullable|string|max:255',
            'remarks'            => 'nullable|string',
            'items'              => 'nullable|array',
            'items.*.category'   => 'nullable|string',
            'items.*.star_rating'=> 'nullable|numeric|min:0|max:5',
        ]);

        $student  = Student::findOrFail($request->student_id);
        $classObj = StudentClass::where('name', $student->class_name)->first();

        $categoryRatings = [];
        $categoryNames = [];
        $totalStars = 0;
        $count = 0;

        if ($request->filled('items') && is_array($request->items) && count($request->items) > 0) {
            foreach ($request->items as $item) {
                if (empty($item['category'])) continue;

                $cat        = $item['category'];
                $starRating = isset($item['star_rating']) && $item['star_rating'] !== '' ? floatval($item['star_rating']) : 5.0;
                $total      = isset($item['total_score']) && $item['total_score'] !== '' ? floatval($item['total_score']) : 100.0;
                $obtained   = isset($item['obtained_score']) && $item['obtained_score'] !== '' ? floatval($item['obtained_score']) : round(($starRating / 5.0) * $total, 1);
                $itemRemarks= !empty($item['remarks']) ? $item['remarks'] : '';

                $categoryRatings[] = [
                    'category'       => $cat,
                    'star_rating'    => $starRating,
                    'obtained_score' => $obtained,
                    'total_score'    => $total,
                    'remarks'        => $itemRemarks,
                ];

                $categoryNames[] = $cat;
                $totalStars += $starRating;
                $count++;
            }
        }

        if ($count === 0) {
            $cat        = $request->input('category', 'General Behavior');
            $total      = $request->filled('total_score') ? floatval($request->total_score) : 100.0;
            $starRating = $request->filled('star_rating') ? floatval($request->star_rating) : 5.0;
            $obtained   = $request->filled('obtained_score') ? floatval($request->obtained_score) : round(($starRating / 5.0) * $total, 1);

            $categoryRatings[] = [
                'category'       => $cat,
                'star_rating'    => $starRating,
                'obtained_score' => $obtained,
                'total_score'    => $total,
                'remarks'        => $request->remarks ?: '',
            ];
            $categoryNames[] = $cat;
            $totalStars = $starRating;
            $count = 1;
        }

        $avgStarRating = round($totalStars / $count, 1);
        $mainCategory = implode(', ', $categoryNames);
        $obtainedTotal = round(($avgStarRating / 5.0) * 100, 1);

        StudentDiscipline::create([
            'student_id'         => $student->id,
            'student_class_id'   => $classObj?->id,
            'entry_date'         => $request->entry_date,
            'category'           => $mainCategory,
            'category_ratings'   => $categoryRatings,
            'performance_period' => 'daily',
            'title'              => $request->title ?: "Performance Record ({$count} Categories)",
            'total_score'        => 100.00,
            'obtained_score'     => $obtainedTotal,
            'star_rating'        => $avgStarRating,
            'remarks'            => $request->remarks ?: "Performance evaluated across {$count} categories.",
            'created_by'         => Auth::id(),
        ]);

        return redirect()->route('discipline.create')
            ->with('success', "Performance record saved for {$student->full_name} — {$avgStarRating} Stars average ({$count} Categories)!");
    }

    public function show($id)
    {
        $discipline         = StudentDiscipline::with(['student', 'studentClass', 'creator'])->findOrFail($id);
        $student            = $discipline->student;
        $studentDisciplines = StudentDiscipline::where('student_id', $student->id)->latest('entry_date')->get();

        $startOfWeek  = Carbon::now()->startOfWeek();
        $endOfWeek    = Carbon::now()->endOfWeek();

        $overallDailyAvgStar = $studentDisciplines->avg('star_rating') ?: 0.0;
        $dailyStars          = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->isToday())->avg('star_rating') ?: $overallDailyAvgStar;
        $weeklyStars         = $studentDisciplines->whereBetween('entry_date', [$startOfWeek, $endOfWeek])->avg('star_rating') ?: 0.0;
        $monthlyStars        = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->month === Carbon::now()->month && $i->entry_date->year === Carbon::now()->year)->avg('star_rating') ?: 0.0;
        $yearlyStars         = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->year === Carbon::now()->year)->avg('star_rating') ?: 0.0;
        $overallAvgScore     = $studentDisciplines->avg('obtained_score') ?: 0;
        $totalEvaluationsCount = $studentDisciplines->count();

        return view('pages.admin.discipline.show', compact(
            'discipline', 'student', 'studentDisciplines',
            'overallDailyAvgStar', 'dailyStars', 'weeklyStars', 'monthlyStars', 'yearlyStars',
            'overallAvgScore', 'totalEvaluationsCount'
        ));
    }

    public function print($id)
    {
        $discipline         = StudentDiscipline::with(['student', 'studentClass', 'creator'])->findOrFail($id);
        $student            = $discipline->student;
        $studentDisciplines = StudentDiscipline::where('student_id', $student->id)->latest('entry_date')->get();

        $startOfWeek  = Carbon::now()->startOfWeek();
        $endOfWeek    = Carbon::now()->endOfWeek();

        $overallDailyAvgStar = $studentDisciplines->avg('star_rating') ?: 0.0;
        $dailyStars          = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->isToday())->avg('star_rating') ?: $overallDailyAvgStar;
        $weeklyStars         = $studentDisciplines->whereBetween('entry_date', [$startOfWeek, $endOfWeek])->avg('star_rating') ?: 0.0;
        $monthlyStars        = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->month === Carbon::now()->month && $i->entry_date->year === Carbon::now()->year)->avg('star_rating') ?: 0.0;
        $yearlyStars         = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->year === Carbon::now()->year)->avg('star_rating') ?: 0.0;
        $overallAvgScore     = $studentDisciplines->avg('obtained_score') ?: 0;
        $totalEvaluationsCount = $studentDisciplines->count();

        return view('pages.admin.discipline.print', compact(
            'discipline', 'student', 'studentDisciplines',
            'overallDailyAvgStar', 'dailyStars', 'weeklyStars', 'monthlyStars', 'yearlyStars',
            'overallAvgScore', 'totalEvaluationsCount'
        ));
    }

    public function dailyPrint($id)
    {
        $discipline         = StudentDiscipline::with(['student', 'studentClass', 'creator'])->findOrFail($id);
        $student            = $discipline->student;
        $studentDisciplines = StudentDiscipline::where('student_id', $student->id)->latest('entry_date')->get();

        $startOfWeek  = Carbon::now()->startOfWeek();
        $endOfWeek    = Carbon::now()->endOfWeek();

        $overallDailyAvgStar   = $studentDisciplines->avg('star_rating') ?: 0.0;
        $dailyStars            = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->isToday())->avg('star_rating') ?: $overallDailyAvgStar;
        $weeklyStars           = $studentDisciplines->whereBetween('entry_date', [$startOfWeek, $endOfWeek])->avg('star_rating') ?: 0.0;
        $monthlyStars          = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->month === Carbon::now()->month && $i->entry_date->year === Carbon::now()->year)->avg('star_rating') ?: 0.0;
        $yearlyStars           = $studentDisciplines->filter(fn($i) => $i->entry_date && $i->entry_date->year === Carbon::now()->year)->avg('star_rating') ?: 0.0;
        $overallAvgScore       = $studentDisciplines->avg('obtained_score') ?: 0;
        $totalEvaluationsCount = $studentDisciplines->count();

        // Standard 5 Categories for Daily Development Report (Total 100 Marks)
        $categoriesDef = [
            [
                'number'      => 1,
                'key'         => 'learning_skills',
                'title'       => 'Learning Skills',
                'sub'         => 'Reading, Writing, Homework, Presentation, Problem Solving',
                'max_marks'   => 25,
                'match_terms' => ['learning', 'reading', 'writing', 'homework', 'presentation', 'problem solving'],
            ],
            [
                'number'      => 2,
                'key'         => 'behaviour_character',
                'title'       => 'Behaviour & Character',
                'sub'         => 'Discipline, Respect, Responsibility, Teamwork, Honesty',
                'max_marks'   => 25,
                'match_terms' => ['behaviour', 'character', 'general behavior', 'conduct', 'respect', 'incident'],
            ],
            [
                'number'      => 3,
                'key'         => 'islamic_development',
                'title'       => 'Islamic Development',
                'sub'         => 'Quran Progress, Duas & Hadith, Tajweed, Islamic Behaviour',
                'max_marks'   => 20,
                'match_terms' => ['islamic', 'quran', 'duas', 'hadith', 'tajweed'],
            ],
            [
                'number'      => 4,
                'key'         => 'co_curricular',
                'title'       => 'Co-Curricular Activities',
                'sub'         => 'Sports, Art, Qirat / Naat, Speech / Participation',
                'max_marks'   => 20,
                'match_terms' => ['co-curricular', 'sports', 'art', 'qirat', 'naat', 'speech', 'activity'],
            ],
            [
                'number'      => 5,
                'key'         => 'personal_habits',
                'title'       => 'Personal Habits',
                'sub'         => 'Uniform, Cleanliness, Punctuality, Organization',
                'max_marks'   => 10,
                'match_terms' => ['personal', 'habits', 'uniform', 'cleanliness', 'punctuality'],
            ],
        ];

        $ratingsArray = is_array($discipline->category_ratings) ? $discipline->category_ratings : [];
        $overallStar  = $discipline->star_rating ?: 4.5;

        $developmentCategories = [];
        $calculatedTotalObtained = 0;

        if (!empty($ratingsArray)) {
            $count = count($ratingsArray);
            $baseMax = floor(100 / $count);
            $remainder = 100 - ($baseMax * $count);

            foreach ($ratingsArray as $idx => $item) {
                if (empty($item['category'])) continue;
                $catName  = $item['category'];
                $star     = isset($item['star_rating']) && $item['star_rating'] !== '' ? floatval($item['star_rating']) : $overallStar;
                $maxMarks = $baseMax + ($idx < $remainder ? 1 : 0);
                
                $ratio    = max(0.0, min(1.0, $star / 5.0));
                $obtained = round($maxMarks * $ratio);

                $catLower = strtolower($catName);
                $iconKey  = 'behaviour_character';
                $subText  = 'Discipline, Respect, Responsibility & Character Conduct';

                if (str_contains($catLower, 'punctual') || str_contains($catLower, 'attend')) {
                    $iconKey = 'punctuality';
                    $subText = 'Attendance Record, Punctuality & Time Management';
                } elseif (str_contains($catLower, 'uniform') || str_contains($catLower, 'clean')) {
                    $iconKey = 'personal_habits';
                    $subText = 'Uniform Dress Code, Cleanliness & Personal Hygiene';
                } elseif (str_contains($catLower, 'learn') || str_contains($catLower, 'home') || str_contains($catLower, 'class') || str_contains($catLower, 'work')) {
                    $iconKey = 'learning_skills';
                    $subText = 'Reading, Writing, Homework & Academic Focus';
                } elseif (str_contains($catLower, 'islam')) {
                    $iconKey = 'islamic_development';
                    $subText = 'Quran Progress, Duas, Tajweed & Islamic Behaviour';
                } elseif (str_contains($catLower, 'co-curr') || str_contains($catLower, 'sport') || str_contains($catLower, 'activ') || str_contains($catLower, 'art')) {
                    $iconKey = 'co_curricular';
                    $subText = 'Sports, Art, Qirat / Naat & Event Participation';
                }

                if (!empty($item['remarks'])) {
                    $subText = $item['remarks'];
                }

                $calculatedTotalObtained += $obtained;

                $developmentCategories[] = [
                    'number'         => $idx + 1,
                    'key'            => $iconKey,
                    'title'          => $catName,
                    'sub'            => $subText,
                    'star_rating'    => $star,
                    'max_marks'      => $maxMarks,
                    'obtained_marks' => $obtained,
                ];
            }
        } else {
            $categoriesDef = [
                [
                    'number'      => 1,
                    'key'         => 'learning_skills',
                    'title'       => 'Learning Skills',
                    'sub'         => 'Reading, Writing, Homework, Presentation, Problem Solving',
                    'max_marks'   => 25,
                    'star_rating' => $overallStar,
                ],
                [
                    'number'      => 2,
                    'key'         => 'behaviour_character',
                    'title'       => 'Behaviour & Character',
                    'sub'         => 'Discipline, Respect, Responsibility, Teamwork, Honesty',
                    'max_marks'   => 25,
                    'star_rating' => $overallStar,
                ],
                [
                    'number'      => 3,
                    'key'         => 'islamic_development',
                    'title'       => 'Islamic Development',
                    'sub'         => 'Quran Progress, Duas & Hadith, Tajweed, Islamic Behaviour',
                    'max_marks'   => 20,
                    'star_rating' => $overallStar,
                ],
                [
                    'number'      => 4,
                    'key'         => 'co_curricular',
                    'title'       => 'Co-Curricular Activities',
                    'sub'         => 'Sports, Art, Qirat / Naat, Speech / Participation',
                    'max_marks'   => 20,
                    'star_rating' => $overallStar,
                ],
                [
                    'number'      => 5,
                    'key'         => 'personal_habits',
                    'title'       => 'Personal Habits',
                    'sub'         => 'Uniform, Cleanliness, Punctuality, Organization',
                    'max_marks'   => 10,
                    'star_rating' => $overallStar,
                ],
            ];

            foreach ($categoriesDef as $cat) {
                $star = $cat['star_rating'];
                $obtained = round($cat['max_marks'] * ($star / 5.0));
                $calculatedTotalObtained += $obtained;

                $developmentCategories[] = array_merge($cat, [
                    'obtained_marks' => $obtained,
                ]);
            }
        }

        return view('pages.admin.discipline.daily_print', compact(
            'discipline', 'student', 'studentDisciplines',
            'overallDailyAvgStar', 'dailyStars', 'weeklyStars', 'monthlyStars', 'yearlyStars',
            'overallAvgScore', 'totalEvaluationsCount',
            'developmentCategories', 'calculatedTotalObtained'
        ));
    }

    public function edit($id)
    {
        $discipline = StudentDiscipline::with('student')->findOrFail($id);
        $classes    = StudentClass::orderBy('name')->get();
        $students   = Student::where('class_name', $discipline->student->class_name)->orderBy('first_name')->get();

        return view('pages.admin.discipline.edit', compact('discipline', 'classes', 'students'));
    }

    public function update(Request $request, $id)
    {
        $discipline = StudentDiscipline::findOrFail($id);

        $request->validate([
            'entry_date'         => 'required|date',
            'title'              => 'nullable|string|max:255',
            'remarks'            => 'nullable|string',
            'items'              => 'nullable|array',
            'items.*.category'   => 'nullable|string',
            'items.*.star_rating'=> 'nullable|numeric|min:0|max:5',
        ]);

        $categoryRatings = [];
        $categoryNames = [];
        $totalStars = 0;
        $count = 0;

        if ($request->filled('items') && is_array($request->items) && count($request->items) > 0) {
            foreach ($request->items as $item) {
                if (empty($item['category'])) continue;

                $cat        = $item['category'];
                $starRating = isset($item['star_rating']) && $item['star_rating'] !== '' ? floatval($item['star_rating']) : 5.0;
                $total      = isset($item['total_score']) && $item['total_score'] !== '' ? floatval($item['total_score']) : 100.0;
                $obtained   = isset($item['obtained_score']) && $item['obtained_score'] !== '' ? floatval($item['obtained_score']) : round(($starRating / 5.0) * $total, 1);
                $itemRemarks= !empty($item['remarks']) ? $item['remarks'] : '';

                $categoryRatings[] = [
                    'category'       => $cat,
                    'star_rating'    => $starRating,
                    'obtained_score' => $obtained,
                    'total_score'    => $total,
                    'remarks'        => $itemRemarks,
                ];

                $categoryNames[] = $cat;
                $totalStars += $starRating;
                $count++;
            }
        }

        if ($count === 0) {
            $cat        = $request->input('category', 'General Behavior');
            $total      = $request->filled('total_score') ? floatval($request->total_score) : 100.0;
            $starRating = $request->filled('star_rating') ? floatval($request->star_rating) : floatval($discipline->star_rating);
            $obtained   = $request->filled('obtained_score') ? floatval($request->obtained_score) : round(($starRating / 5.0) * $total, 1);

            $categoryRatings[] = [
                'category'       => $cat,
                'star_rating'    => $starRating,
                'obtained_score' => $obtained,
                'total_score'    => $total,
                'remarks'        => $request->remarks ?: '',
            ];
            $categoryNames[] = $cat;
            $totalStars = $starRating;
            $count = 1;
        }

        $avgStarRating = round($totalStars / $count, 1);
        $mainCategory = implode(', ', $categoryNames);
        $obtainedTotal = round(($avgStarRating / 5.0) * 100, 1);

        $discipline->update([
            'entry_date'         => $request->entry_date,
            'category'           => $mainCategory,
            'category_ratings'   => $categoryRatings,
            'performance_period' => 'daily',
            'title'              => $request->title,
            'total_score'        => 100.00,
            'obtained_score'     => $obtainedTotal,
            'star_rating'        => $avgStarRating,
            'remarks'            => $request->remarks,
        ]);

        return redirect()->route('discipline.index')->with('success', 'Performance record updated successfully!');
    }

    public function destroy($id)
    {
        StudentDiscipline::findOrFail($id)->delete();
        return redirect()->route('discipline.index')->with('success', 'Record deleted successfully!');
    }
}
