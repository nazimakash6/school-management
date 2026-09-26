<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\QuranModule;
use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuranModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = QuranModule::with(['student', 'studentClass'])->latest('entry_date')->latest('id');

        // Search by student name or roll number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('roll_no', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by class
        if ($request->filled('class_name')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_name', $request->class_name);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->paginate(10)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_records'          => QuranModule::count(),
            'hifz_students'          => QuranModule::where('category', 'Hifz')->distinct('student_id')->count('student_id'),
            'nazra_students'         => QuranModule::where('category', 'Nazra')->distinct('student_id')->count('student_id'),
            'avg_score'              => round(QuranModule::avg('score') ?? 0, 1),
            'total_memorized_parahs' => QuranModule::where('category', 'Hifz')->max('total_parahs_memorized') ?? 0,
        ];

        $classes = StudentClass::orderBy('name')->get();

        return view('pages.admin.quran-module.index', compact('records', 'stats', 'classes'));
    }

    public function create()
    {
        $academicSessions = AcademicSession::orderBy('id', 'desc')->get();
        $activeSession = AcademicSession::where('status', 'Active')->first() ?? $academicSessions->first();

        if ($activeSession) {
            $classNames = Student::where('academic_session_id', $activeSession->id)
                ->where('status', 'active')
                ->whereNotNull('class_name')
                ->distinct()
                ->pluck('class_name');

            if ($classNames->isNotEmpty()) {
                $classes = StudentClass::whereIn('name', $classNames)->where('status', 'active')->orderBy('name')->get();
            } else {
                $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
            }
        } else {
            $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        }

        return view('pages.admin.quran-module.create', compact('academicSessions', 'activeSession', 'classes'));
    }

    public function store(Request $request)
    {
        $categories = $request->input('categories');
        if (!$categories && $request->filled('category')) {
            $categories = [$request->input('category')];
        }
        $request->merge(['categories' => $categories]);

        $validated = $request->validate([
            'student_id'             => 'required|exists:students,id',
            'entry_date'             => 'required|date',
            'categories'             => 'required|array|min:1',
            'categories.*'           => 'required|string|in:Qaida,Nazra,Hifz,Tajweed,Hadith,Dua',
            'status'                 => 'required|string',
            'teacher_name'           => 'nullable|string|max:255',
            'para_no'                => 'nullable|integer|min:1|max:30',
            'surah_name'             => 'nullable|string|max:255',
            'ayah_from'              => 'nullable|integer|min:1',
            'ayah_to'                => 'nullable|integer|min:1',
            'lesson_name'            => 'nullable|string|max:255',
            'qaida_lesson_name'      => 'nullable|string|max:255',
            'tajweed_lesson_name'    => 'nullable|string|max:255',
            'hadith_lesson_name'     => 'nullable|string|max:255',
            'dua_lesson_name'        => 'nullable|string|max:255',
            'sabaq'                  => 'nullable|string',
            'sabqi'                  => 'nullable|string',
            'manzil'                 => 'nullable|string',
            'total_parahs_memorized' => 'nullable|integer|min:0|max:30',
            'score'                  => 'nullable|numeric|min:0|max:100',
            'mistakes_count'         => 'nullable|integer|min:0',
            'remarks'                => 'nullable|string',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $classObj = StudentClass::where('name', $student->class_name)->first();

        $createdCount = 0;

        DB::transaction(function () use ($validated, $student, $classObj, &$createdCount) {
            foreach ($validated['categories'] as $cat) {
                $recordData = [
                    'student_id'       => $validated['student_id'],
                    'student_class_id' => $classObj?->id,
                    'entry_date'       => $validated['entry_date'],
                    'category'         => $cat,
                    'status'           => $validated['status'],
                    'teacher_name'     => $validated['teacher_name'] ?? null,
                    'score'            => $validated['score'] ?? 100.0,
                    'mistakes_count'   => $validated['mistakes_count'] ?? 0,
                    'remarks'          => $validated['remarks'] ?? null,
                    'created_by'       => Auth::id(),
                ];

                if ($cat === 'Hifz') {
                    $recordData['sabaq']                  = $validated['sabaq'] ?? null;
                    $recordData['sabqi']                  = $validated['sabqi'] ?? null;
                    $recordData['manzil']                 = $validated['manzil'] ?? null;
                    $recordData['total_parahs_memorized'] = $validated['total_parahs_memorized'] ?? 0;
                } elseif ($cat === 'Nazra') {
                    $recordData['para_no']    = $validated['para_no'] ?? null;
                    $recordData['surah_name'] = $validated['surah_name'] ?? null;
                    $recordData['ayah_from']  = $validated['ayah_from'] ?? null;
                    $recordData['ayah_to']    = $validated['ayah_to'] ?? null;
                } elseif ($cat === 'Qaida') {
                    $recordData['lesson_name'] = $validated['qaida_lesson_name'] ?? ($validated['lesson_name'] ?? null);
                } elseif ($cat === 'Tajweed') {
                    $recordData['lesson_name'] = $validated['tajweed_lesson_name'] ?? ($validated['lesson_name'] ?? null);
                } elseif ($cat === 'Hadith') {
                    $recordData['lesson_name'] = $validated['hadith_lesson_name'] ?? ($validated['lesson_name'] ?? null);
                } elseif ($cat === 'Dua') {
                    $recordData['lesson_name'] = $validated['dua_lesson_name'] ?? ($validated['lesson_name'] ?? null);
                }

                QuranModule::create($recordData);
                $createdCount++;
            }
        });

        $message = $createdCount > 1
            ? "{$createdCount} Quran progress records created successfully!"
            : "Quran progress record created successfully!";

        return redirect()->route('quran-module.index')
            ->with('success', $message);
    }

    public function show($id)
    {
        $record = QuranModule::with(['student', 'studentClass', 'creator'])->findOrFail($id);

        // Fetch historical records for this student
        $studentHistory = QuranModule::where('student_id', $record->student_id)
            ->latest('entry_date')
            ->latest('id')
            ->get();

        // Calculate student's overall stats
        $studentStats = [
            'total_entries'     => $studentHistory->count(),
            'max_hifz_parahs'   => $studentHistory->where('category', 'Hifz')->max('total_parahs_memorized') ?? 0,
            'avg_score'         => round($studentHistory->avg('score') ?? 0, 1),
            'latest_category'   => $record->category,
        ];

        return view('pages.admin.quran-module.show', compact('record', 'studentHistory', 'studentStats'));
    }

    public function edit($id)
    {
        $record = QuranModule::with(['student'])->findOrFail($id);
        $academicSessions = AcademicSession::orderBy('id', 'desc')->get();
        $selectedSessionId = $record->student->academic_session_id ?? null;

        if ($selectedSessionId) {
            $classNames = Student::where('academic_session_id', $selectedSessionId)
                ->where('status', 'active')
                ->whereNotNull('class_name')
                ->distinct()
                ->pluck('class_name');

            if ($classNames->isNotEmpty()) {
                $classes = StudentClass::whereIn('name', $classNames)->where('status', 'active')->orderBy('name')->get();
            } else {
                $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
            }
        } else {
            $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        }

        $studentsInClass = Student::where('class_name', $record->student->class_name ?? '')
            ->when($selectedSessionId, function ($q) use ($selectedSessionId) {
                return $q->where('academic_session_id', $selectedSessionId);
            })
            ->orderBy('first_name')
            ->get();

        return view('pages.admin.quran-module.edit', compact('record', 'academicSessions', 'selectedSessionId', 'classes', 'studentsInClass'));
    }

    public function update(Request $request, $id)
    {
        $record = QuranModule::findOrFail($id);

        $categories = $request->input('categories');
        if (!$categories && $request->filled('category')) {
            $categories = [$request->input('category')];
        }
        $request->merge(['categories' => $categories]);

        $validated = $request->validate([
            'student_id'             => 'required|exists:students,id',
            'entry_date'             => 'required|date',
            'categories'             => 'required|array|min:1',
            'categories.*'           => 'required|string|in:Qaida,Nazra,Hifz,Tajweed,Hadith,Dua',
            'status'                 => 'required|string',
            'teacher_name'           => 'nullable|string|max:255',
            'para_no'                => 'nullable|integer|min:1|max:30',
            'surah_name'             => 'nullable|string|max:255',
            'ayah_from'              => 'nullable|integer|min:1',
            'ayah_to'                => 'nullable|integer|min:1',
            'lesson_name'            => 'nullable|string|max:255',
            'qaida_lesson_name'      => 'nullable|string|max:255',
            'tajweed_lesson_name'    => 'nullable|string|max:255',
            'hadith_lesson_name'     => 'nullable|string|max:255',
            'dua_lesson_name'        => 'nullable|string|max:255',
            'sabaq'                  => 'nullable|string',
            'sabqi'                  => 'nullable|string',
            'manzil'                 => 'nullable|string',
            'total_parahs_memorized' => 'nullable|integer|min:0|max:30',
            'score'                  => 'nullable|numeric|min:0|max:100',
            'mistakes_count'         => 'nullable|integer|min:0',
            'remarks'                => 'nullable|string',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $classObj = StudentClass::where('name', $student->class_name)->first();

        $selectedCategories = $validated['categories'];
        $primaryCategory = $selectedCategories[0];

        DB::transaction(function () use ($record, $validated, $student, $classObj, $selectedCategories, $primaryCategory) {
            $baseData = [
                'student_id'       => $validated['student_id'],
                'student_class_id' => $classObj?->id,
                'entry_date'       => $validated['entry_date'],
                'status'           => $validated['status'],
                'teacher_name'     => $validated['teacher_name'] ?? null,
                'score'            => $validated['score'] ?? 100.0,
                'mistakes_count'   => $validated['mistakes_count'] ?? 0,
                'remarks'          => $validated['remarks'] ?? null,
            ];

            // 1. Update primary record
            $primaryData = array_merge($baseData, ['category' => $primaryCategory]);
            $primaryData = $this->attachCategoryDetails($primaryData, $primaryCategory, $validated);
            $record->update($primaryData);

            // 2. Create additional records for extra selected categories if any
            for ($i = 1; $i < count($selectedCategories); $i++) {
                $cat = $selectedCategories[$i];
                $extraData = array_merge($baseData, [
                    'category'   => $cat,
                    'created_by' => Auth::id(),
                ]);
                $extraData = $this->attachCategoryDetails($extraData, $cat, $validated);
                QuranModule::create($extraData);
            }
        });

        $count = count($selectedCategories);
        $message = $count > 1
            ? "Record updated and {$count} progress entries saved successfully!"
            : "Quran progress record updated successfully!";

        return redirect()->route('quran-module.show', $record->id)
            ->with('success', $message);
    }

    private function attachCategoryDetails(array $data, string $cat, array $validated): array
    {
        if ($cat === 'Hifz') {
            $data['sabaq']                  = $validated['sabaq'] ?? null;
            $data['sabqi']                  = $validated['sabqi'] ?? null;
            $data['manzil']                 = $validated['manzil'] ?? null;
            $data['total_parahs_memorized'] = $validated['total_parahs_memorized'] ?? 0;
        } elseif ($cat === 'Nazra') {
            $data['para_no']    = $validated['para_no'] ?? null;
            $data['surah_name'] = $validated['surah_name'] ?? null;
            $data['ayah_from']  = $validated['ayah_from'] ?? null;
            $data['ayah_to']    = $validated['ayah_to'] ?? null;
        } elseif ($cat === 'Qaida') {
            $data['lesson_name'] = $validated['qaida_lesson_name'] ?? ($validated['lesson_name'] ?? null);
        } elseif ($cat === 'Tajweed') {
            $data['lesson_name'] = $validated['tajweed_lesson_name'] ?? ($validated['lesson_name'] ?? null);
        } elseif ($cat === 'Hadith') {
            $data['lesson_name'] = $validated['hadith_lesson_name'] ?? ($validated['lesson_name'] ?? null);
        } elseif ($cat === 'Dua') {
            $data['lesson_name'] = $validated['dua_lesson_name'] ?? ($validated['lesson_name'] ?? null);
        }

        return $data;
    }

    public function destroy($id)
    {
        $record = QuranModule::findOrFail($id);
        $record->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
        }

        return redirect()->route('quran-module.index')
            ->with('success', 'Quran progress record deleted successfully!');
    }

    // AJAX: Get classes for selected academic session
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
            'classes' => $classes
        ]);
    }

    // AJAX: Get students for selected class name and optional session
    public function getStudentsByClass(Request $request, $className)
    {
        $query = Student::where('class_name', $className)->where('status', 'active');

        if ($request->filled('session_id') && $request->session_id !== 'all') {
            $query->where('academic_session_id', $request->session_id);
        }

        $students = $query->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'roll_no']);

        return response()->json([
            'success'  => true,
            'students' => $students
        ]);
    }

    // AJAX: Get student latest progress for auto-filling
    public function getStudentLatestProgress($studentId)
    {
        $latest = QuranModule::where('student_id', $studentId)
            ->latest('entry_date')
            ->first();

        return response()->json([
            'success' => true,
            'latest'  => $latest
        ]);
    }
}

