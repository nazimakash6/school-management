<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentClass\StoreStudentClassRequest;
use App\Http\Requests\StudentClass\UpdateStudentClassRequest;
use App\Models\Group;
use App\Models\Staff;
use App\Models\StudentClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentClassController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search = trim((string) $request->string('search'));
        $level = trim((string) $request->string('level'));

        if (!in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = StudentClass::query()
            ->withCount('students')
            ->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('level', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($level !== '') {
            $query->where('level', $level);
        }

        $studentClasses = $query
            ->paginate($perPage)
            ->withQueryString();

        $levels = StudentClass::query()
            ->select('level')
            ->distinct()
            ->orderBy('level')
            ->pluck('level');

        $trashCount = StudentClass::onlyTrashed()->count();

        return view('pages.admin.classes.index', compact('studentClasses', 'perPage', 'search', 'level', 'levels', 'trashCount'));
    }

    public function create(): View
    {
        $teachers = Staff::where('designation', 'teacher')->orderBy('first_name')->get();
        $groups   = Group::orderBy('name')->get();
        $subjects = Subject::orderBy('subject_name')->get();

        return view('pages.admin.classes.create', compact('teachers', 'groups', 'subjects'));
    }

    public function store(StoreStudentClassRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $studentClass = StudentClass::create($validated);

        return redirect()
            ->route('classes.show', $studentClass)
            ->with('status', 'Class created successfully.');
    }

    public function show(StudentClass $studentClass): View
    {
        $studentClass->loadCount('students')->load('teacher');
        $subjects = Subject::where('class_name', $studentClass->name)->get();

        return view('pages.admin.classes.show', compact('studentClass', 'subjects'));
    }

    public function edit(StudentClass $studentClass): View
    {
        $teachers = Staff::where('designation', 'teacher')->orderBy('first_name')->get();
        $groups   = Group::orderBy('name')->get();
        $subjects = Subject::orderBy('subject_name')->get();
        $classSubjects = Subject::where('class_name', $studentClass->name)->get();

        return view('pages.admin.classes.edit', compact('studentClass', 'teachers', 'groups', 'subjects', 'classSubjects'));
    }

    public function update(UpdateStudentClassRequest $request, StudentClass $studentClass): RedirectResponse
    {
        $validated = $request->validated();

        $studentClass->update($validated);

        return redirect()
            ->route('classes.show', $studentClass)
            ->with('status', 'Class updated successfully.');
    }

    public function destroy(StudentClass $studentClass): RedirectResponse
    {
        $studentClass->delete();

        return redirect()
            ->route('classes.index')
            ->with('status', 'Class moved to trash successfully.');
    }

    public function trash(): View
    {
        $trashedClasses = StudentClass::onlyTrashed()
            ->withCount('students')
            ->latest('deleted_at')
            ->paginate(25);

        return view('pages.admin.classes.trash', compact('trashedClasses'));
    }

    public function restore($id): RedirectResponse
    {
        $studentClass = StudentClass::onlyTrashed()->findOrFail($id);
        $studentClass->restore();

        return redirect()
            ->route('classes.trash')
            ->with('status', 'Class restored successfully.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $studentClass = StudentClass::onlyTrashed()->findOrFail($id);
        $studentClass->forceDelete();

        return redirect()
            ->route('classes.trash')
            ->with('status', 'Class deleted permanently.');
    }

    public function bulkTrash(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Please select at least one class to move to trash.');
        }

        StudentClass::whereIn('id', $ids)->delete();

        return redirect()
            ->route('classes.index')
            ->with('status', count($ids) . ' class(es) moved to trash successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one record.');
        }

        if ($action === 'restore') {
            StudentClass::onlyTrashed()->whereIn('id', $ids)->restore();
            return redirect()
                ->route('classes.trash')
                ->with('status', count($ids) . ' class(es) restored successfully.');
        }

        if ($action === 'force_delete' || $action === 'delete') {
            StudentClass::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            return redirect()
                ->route('classes.trash')
                ->with('status', count($ids) . ' class(es) deleted permanently.');
        }

        return back()->with('error', 'Invalid action selected.');
    }

    private function validatedData(Request $request, ?int $studentClassId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:student_classes,name,' . $studentClassId],
            'level' => ['required', 'string', 'max:255'],
            'staff_table_id' => ['nullable', 'exists:staff,id'],
            'section_names' => ['required', 'array', 'min:1'],
            'section_names.*' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
        ]);

        $sectionNames = collect(Arr::get($validated, 'section_names', []))
            ->map(fn($sectionName) => trim((string) $sectionName))
            ->filter(fn($sectionName) => $sectionName !== '')
            ->unique()
            ->values();

        if ($sectionNames->isEmpty()) {
            throw ValidationException::withMessages([
                'section_names' => ['At least one section name is required.'],
            ]);
        }

        $validated['section_names'] = $sectionNames->all();
        $validated['section_count'] = $sectionNames->count();

        return $validated;
    }
}
