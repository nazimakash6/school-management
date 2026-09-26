<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Models\Admission;
use App\Models\Staff;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\SubjectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $perPage       = (int) $request->integer('per_page', 25);
        $search        = trim((string) $request->string('search'));
        $className     = trim((string) $request->string('class_name', 'all'));
        $subjectTypeId = trim((string) $request->string('subject_type_id', 'all'));

        $query = Subject::with(['studentClass', 'teacher', 'subjectType'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('subject_code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%");
            });
        }

        if ($className !== 'all') {
            $query->where('class_name', $className);
        }

        if ($subjectTypeId !== 'all') {
            $query->where('subject_type_id', $subjectTypeId);
        }

        $subjects = $query->paginate($perPage)->withQueryString();

        // Statistics
        $totalSubjects = Subject::count();
        $subjectTypes  = SubjectType::orderBy('name')->get();

        // Get all unique classes for dropdowns and breakdown
        $classesList = StudentClass::pluck('name')->merge(
            Admission::distinct()->pluck('class_name')
        )->filter()->unique()->sort()->values();

        // Class-wise Subject Count Breakdown
        $classSubjectCounts = [];
        foreach ($classesList as $cName) {
            $classSubjectCounts[$cName] = Subject::where('class_name', $cName)->count();
        }

        $teachersList = Staff::where('designation', 'like', '%teacher%')->orderBy('first_name')->get();

        return view('pages.admin.subjects.index', compact(
            'subjects',
            'perPage',
            'search',
            'className',
            'subjectTypeId',
            'subjectTypes',
            'totalSubjects',
            'classesList',
            'classSubjectCounts',
            'teachersList'
        ));
    }

    public function create(): View
    {
        $classesList = StudentClass::pluck('name')->merge(
            Admission::distinct()->pluck('class_name')
        )->filter()->unique()->sort()->values();

        $teachersList = Staff::where('designation', 'like', '%teacher%')->orderBy('first_name')->get();
        $subjectTypes = SubjectType::where('status', 'active')->orderBy('name')->get();

        return view('pages.admin.subjects.create', compact('classesList', 'teachersList', 'subjectTypes'));
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $studentClass = StudentClass::where('name', $validated['class_name'])->first();
        if ($studentClass) {
            $validated['student_class_id'] = $studentClass->id;
        }

        if (!empty($validated['subject_type_id'])) {
            $sType = SubjectType::find($validated['subject_type_id']);
            if ($sType) {
                $validated['subject_type'] = $sType->code;
            }
        }

        Subject::create($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function show($id): View
    {
        $subject = Subject::with(['studentClass', 'teacher', 'subjectType'])->findOrFail($id);

        return view('pages.admin.subjects.show', compact('subject'));
    }

    public function edit($id): View
    {
        $subject = Subject::with(['studentClass', 'teacher', 'subjectType'])->findOrFail($id);

        $classesList = StudentClass::pluck('name')->merge(
            Admission::distinct()->pluck('class_name')
        )->filter()->unique()->sort()->values();

        $teachersList = Staff::where('designation', 'like', '%teacher%')->orderBy('first_name')->get();
        $subjectTypes = SubjectType::where('status', 'active')->orderBy('name')->get();

        return view('pages.admin.subjects.edit', compact('subject', 'classesList', 'teachersList', 'subjectTypes'));
    }

    public function update(UpdateSubjectRequest $request, $id): RedirectResponse
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validated();

        $studentClass = StudentClass::where('name', $validated['class_name'])->first();
        $validated['student_class_id'] = $studentClass ? $studentClass->id : null;

        if (!empty($validated['subject_type_id'])) {
            $sType = SubjectType::find($validated['subject_type_id']);
            if ($sType) {
                $validated['subject_type'] = $sType->code;
            }
        }

        $subject->update($validated);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
