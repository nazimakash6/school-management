<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuranModule;
use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $classes = StudentClass::orderBy('name')->get();
        return view('pages.admin.quran-module.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'             => 'required|exists:students,id',
            'entry_date'             => 'required|date',
            'category'               => 'required|string|in:Qaida,Nazra,Hifz,Tajweed,Hadith,Dua',
            'status'                 => 'required|string',
            'teacher_name'           => 'nullable|string|max:255',
            'para_no'                => 'nullable|integer|min:1|max:30',
            'surah_name'             => 'nullable|string|max:255',
            'ayah_from'              => 'nullable|integer|min:1',
            'ayah_to'                => 'nullable|integer|min:1',
            'lesson_name'            => 'nullable|string|max:255',
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

        $validated['student_class_id'] = $classObj?->id;
        $validated['created_by']       = Auth::id();

        QuranModule::create($validated);

        return redirect()->route('quran-module.index')
            ->with('success', 'Quran progress record created successfully!');
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
        $classes = StudentClass::orderBy('name')->get();
        $studentsInClass = Student::where('class_name', $record->student->class_name ?? '')->orderBy('first_name')->get();

        return view('pages.admin.quran-module.edit', compact('record', 'classes', 'studentsInClass'));
    }

    public function update(Request $request, $id)
    {
        $record = QuranModule::findOrFail($id);

        $validated = $request->validate([
            'student_id'             => 'required|exists:students,id',
            'entry_date'             => 'required|date',
            'category'               => 'required|string|in:Qaida,Nazra,Hifz,Tajweed,Hadith,Dua',
            'status'                 => 'required|string',
            'teacher_name'           => 'nullable|string|max:255',
            'para_no'                => 'nullable|integer|min:1|max:30',
            'surah_name'             => 'nullable|string|max:255',
            'ayah_from'              => 'nullable|integer|min:1',
            'ayah_to'                => 'nullable|integer|min:1',
            'lesson_name'            => 'nullable|string|max:255',
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

        $validated['student_class_id'] = $classObj?->id;

        $record->update($validated);

        return redirect()->route('quran-module.show', $record->id)
            ->with('success', 'Quran progress record updated successfully!');
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

    // AJAX: Get students for selected class name
    public function getStudentsByClass($className)
    {
        $students = Student::where('class_name', $className)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'roll_no']);

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
