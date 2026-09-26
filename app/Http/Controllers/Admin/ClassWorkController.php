<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassWork;
use App\Models\SchoolInfo;
use App\Models\StudentClass;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClassWorkController extends Controller
{
    public function index(Request $request)
    {
        $classes = StudentClass::orderBy('name')->get();

        $query = ClassWork::with(['studentClass', 'creator'])
            ->latest('assigned_date')
            ->latest('id');

        if ($request->filled('class_id')) {
            $query->where('student_class_id', $request->class_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('assigned_date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('subject_tasks', 'like', "%{$search}%")
                  ->orWhereHas('studentClass', function ($classQuery) use ($search) {
                      $classQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subject', function ($subjQuery) use ($search) {
                      $subjQuery->where('subject_name', 'like', "%{$search}%");
                  });
            });
        }

        $classworks = $query->paginate(15)->appends($request->query());

        // Stats metrics
        $totalCount = ClassWork::count();
        $activeCount = ClassWork::where('status', 'active')->count();
        $classesWithClassworkToday = ClassWork::whereDate('assigned_date', Carbon::today())
            ->distinct('student_class_id')
            ->count('student_class_id');
        $dueTodayCount = ClassWork::whereDate('due_date', Carbon::today())->count();

        return view('pages.admin.classwork.index', compact(
            'classworks',
            'classes',
            'totalCount',
            'activeCount',
            'classesWithClassworkToday',
            'dueTodayCount'
        ));
    }

    public function create(Request $request)
    {
        $classes = StudentClass::orderBy('name')->get();
        $selectedClassId = $request->get('class_id');

        return view('pages.admin.classwork.create', compact('classes', 'selectedClassId'));
    }

    public function getSubjects($classId)
    {
        $studentClass = StudentClass::find($classId);

        if (!$studentClass) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found',
                'subjects' => []
            ], 404);
        }

        $subjects = Subject::where(function ($q) use ($classId, $studentClass) {
            $q->where('student_class_id', $classId)
              ->orWhere('class_name', $studentClass->name);
        })
        ->where(function ($q) {
            $q->where('status', 'active')
              ->orWhereNull('status');
        })
        ->orderBy('subject_name')
        ->get(['id', 'subject_name', 'subject_code', 'subject_type']);

        return response()->json([
            'success' => true,
            'class' => [
                'id' => $studentClass->id,
                'name' => $studentClass->name,
            ],
            'subjects' => $subjects
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_class_id' => 'required|exists:student_classes,id',
            'assigned_date'    => 'required|date',
            'classworks'       => 'required|array|min:1',
            'classworks.*.subject_id'  => 'required|exists:subjects,id',
            'classworks.*.description' => 'required|string',
            'classworks.*.title'       => 'nullable|string|max:255',
            'classworks.*.due_date'    => 'nullable|date',
            'classworks.*.status'      => 'nullable|string',
            'classworks.*.attachment'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'student_class_id.required' => 'Please select a class.',
            'classworks.required'       => 'Please add classwork for at least one subject.',
            'classworks.*.subject_id.required' => 'Each classwork task must have a subject assigned.',
            'classworks.*.description.required' => 'Please enter the classwork description for all added subjects.',
        ]);

        $tasks = [];
        $latestDueDate = null;

        foreach ($request->classworks as $index => $item) {
            $attachmentPath = null;
            if ($request->hasFile("classworks.{$index}.attachment")) {
                $attachmentPath = $request->file("classworks.{$index}.attachment")->store('classwork_attachments', 'public');
            }

            $subject = Subject::find($item['subject_id']);

            $tasks[] = [
                'subject_id'   => $item['subject_id'],
                'subject_name' => $subject ? $subject->subject_name : 'Subject',
                'subject_code' => $subject ? $subject->subject_code : null,
                'title'        => $item['title'] ?? null,
                'description'  => $item['description'],
                'due_date'     => $item['due_date'] ?? null,
                'attachment'   => $attachmentPath,
                'status'       => $item['status'] ?? 'active',
            ];

            if (!$latestDueDate && !empty($item['due_date'])) {
                $latestDueDate = $item['due_date'];
            }
        }

        $classObj = StudentClass::find($request->student_class_id);
        $classworkTitle = "{$classObj->name} Classwork - " . Carbon::parse($request->assigned_date)->format('M d, Y');

        // Ensure EXACTLY ONE ClassWork record exists per Class & Assigned Date
        $existingClasswork = ClassWork::where('student_class_id', $request->student_class_id)
            ->whereDate('assigned_date', $request->assigned_date)
            ->first();

        if ($existingClasswork) {
            // Merge existing tasks with newly submitted tasks
            $existingTasks = $existingClasswork->tasks;
            $mergedTasksMap = [];
            foreach ($existingTasks as $t) {
                $subKey = isset($t['subject_id']) ? (string)$t['subject_id'] : ('task_' . uniqid());
                $mergedTasksMap[$subKey] = $t;
            }
            foreach ($tasks as $t) {
                $subKey = isset($t['subject_id']) ? (string)$t['subject_id'] : ('task_' . uniqid());
                if (empty($t['attachment']) && !empty($mergedTasksMap[$subKey]['attachment'])) {
                    $t['attachment'] = $mergedTasksMap[$subKey]['attachment'];
                }
                $mergedTasksMap[$subKey] = $t;
            }
            $finalTasks = array_values($mergedTasksMap);

            $existingClasswork->update([
                'title'         => $classworkTitle,
                'description'   => "Daily classwork assigned for {$classObj->name} (" . count($finalTasks) . " subjects)",
                'subject_tasks' => $finalTasks,
                'due_date'      => $latestDueDate ?: $existingClasswork->due_date,
                'status'        => 'active',
            ]);

            $classwork = $existingClasswork;
            $msg = "Classwork Entry #{$classwork->id} updated successfully for {$classObj->name} on " . Carbon::parse($request->assigned_date)->format('M d, Y') . " (" . count($finalTasks) . " subject task(s))!";
        } else {
            $classwork = ClassWork::create([
                'student_class_id' => $request->student_class_id,
                'title'            => $classworkTitle,
                'description'      => "Daily classwork assigned for {$classObj->name} (" . count($tasks) . " subjects)",
                'subject_tasks'    => $tasks,
                'assigned_date'    => $request->assigned_date,
                'due_date'         => $latestDueDate,
                'status'           => 'active',
                'created_by'       => Auth::id(),
            ]);
            $msg = "Classwork Entry #{$classwork->id} created successfully for {$classObj->name} with " . count($tasks) . " subject task(s)!";
        }

        return redirect()->route('classwork.show', $classwork->id)
            ->with('success', $msg);
    }

    public function show($id)
    {
        $classwork = ClassWork::with(['studentClass', 'creator'])->findOrFail($id);
        return view('pages.admin.classwork.show', compact('classwork'));
    }

    public function print($id)
    {
        $classwork = ClassWork::with(['studentClass', 'creator'])->findOrFail($id);
        $schoolInfo = SchoolInfo::first();

        return view('pages.admin.classwork.print', compact('classwork', 'schoolInfo'));
    }

    public function edit($id)
    {
        $classwork = ClassWork::with(['studentClass'])->findOrFail($id);

        // Consolidate any duplicate records for the same class and date into this single classwork entry
        $duplicateRecords = ClassWork::where('student_class_id', $classwork->student_class_id)
            ->whereDate('assigned_date', $classwork->assigned_date)
            ->where('id', '!=', $classwork->id)
            ->get();

        if ($duplicateRecords->count() > 0) {
            $allTasksMap = [];
            foreach ($classwork->tasks as $t) {
                $subKey = isset($t['subject_id']) ? (string)$t['subject_id'] : (isset($t['subject_name']) ? strtolower($t['subject_name']) : ('task_' . uniqid()));
                $allTasksMap[$subKey] = $t;
            }
            foreach ($duplicateRecords as $dup) {
                foreach ($dup->tasks as $t) {
                    $subKey = isset($t['subject_id']) ? (string)$t['subject_id'] : (isset($t['subject_name']) ? strtolower($t['subject_name']) : ('task_' . uniqid()));
                    $allTasksMap[$subKey] = $t;
                }
                $dup->delete();
            }
            $classwork->update([
                'subject_tasks' => array_values($allTasksMap),
                'subject_id'    => null,
            ]);
            $classwork->refresh();
        }

        $classes = StudentClass::orderBy('name')->get();

        $subjects = Subject::where(function ($q) use ($classwork) {
            $q->where('student_class_id', $classwork->student_class_id)
              ->orWhere('class_name', $classwork->studentClass?->name);
        })->get();

        return view('pages.admin.classwork.edit', compact('classwork', 'classes', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $classwork = ClassWork::findOrFail($id);

        $request->validate([
            'student_class_id' => 'required|exists:student_classes,id',
            'assigned_date'    => 'required|date',
            'classworks'       => 'required|array|min:1',
            'classworks.*.subject_id'  => 'required|exists:subjects,id',
            'classworks.*.description' => 'required|string',
            'classworks.*.title'       => 'nullable|string|max:255',
            'classworks.*.due_date'    => 'nullable|date',
            'classworks.*.status'      => 'nullable|string',
            'classworks.*.attachment'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'student_class_id.required' => 'Please select a class.',
            'classworks.required'       => 'Please add classwork for at least one subject.',
            'classworks.*.subject_id.required' => 'Each classwork task must have a subject assigned.',
            'classworks.*.description.required' => 'Please enter the classwork description for all added subjects.',
        ]);

        $newTasks = [];
        $latestDueDate = null;

        foreach ($request->classworks as $index => $item) {
            $attachmentPath = $item['existing_attachment'] ?? null;

            if ($request->hasFile("classworks.{$index}.attachment")) {
                if ($attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                $attachmentPath = $request->file("classworks.{$index}.attachment")->store('classwork_attachments', 'public');
            }

            $subject = Subject::find($item['subject_id']);

            $newTasks[] = [
                'subject_id'   => $item['subject_id'],
                'subject_name' => $subject ? $subject->subject_name : ($item['subject_name'] ?? 'Subject'),
                'subject_code' => $subject ? $subject->subject_code : ($item['subject_code'] ?? null),
                'title'        => $item['title'] ?? null,
                'description'  => $item['description'],
                'due_date'     => $item['due_date'] ?? null,
                'attachment'   => $attachmentPath,
                'status'       => $item['status'] ?? 'active',
            ];

            if (!$latestDueDate && !empty($item['due_date'])) {
                $latestDueDate = $item['due_date'];
            }
        }

        // Merge any other records for same target class and assigned date into this classwork entry
        $duplicateRecords = ClassWork::where('student_class_id', $request->student_class_id)
            ->whereDate('assigned_date', $request->assigned_date)
            ->where('id', '!=', $classwork->id)
            ->get();

        if ($duplicateRecords->count() > 0) {
            foreach ($duplicateRecords as $dup) {
                foreach ($dup->tasks as $t) {
                    $existingSubjIds = array_column($newTasks, 'subject_id');
                    if (isset($t['subject_id']) && !in_array($t['subject_id'], $existingSubjIds)) {
                        $newTasks[] = $t;
                    }
                }
                $dup->delete();
            }
        }

        $classObj = StudentClass::find($request->student_class_id);
        $classworkTitle = "{$classObj->name} Classwork - " . Carbon::parse($request->assigned_date)->format('M d, Y');

        $classwork->update([
            'student_class_id' => $request->student_class_id,
            'title'            => $classworkTitle,
            'description'      => "Daily classwork assigned for {$classObj->name} (" . count($newTasks) . " subjects)",
            'subject_tasks'    => $newTasks,
            'assigned_date'    => $request->assigned_date,
            'due_date'         => $latestDueDate ?: $classwork->due_date,
            'status'           => $request->status ?? 'active',
        ]);

        return redirect()->route('classwork.show', $classwork->id)
            ->with('success', "Classwork Entry #{$classwork->id} updated successfully for {$classObj->name} (" . count($newTasks) . " subject task(s))!");
    }

    public function destroy($id)
    {
        $classwork = ClassWork::findOrFail($id);

        if (is_array($classwork->subject_tasks)) {
            foreach ($classwork->subject_tasks as $task) {
                if (!empty($task['attachment'])) {
                    Storage::disk('public')->delete($task['attachment']);
                }
            }
        }

        if ($classwork->attachment) {
            Storage::disk('public')->delete($classwork->attachment);
        }

        $classwork->delete();

        return redirect()->route('classwork.index')
            ->with('success', 'Classwork entry deleted successfully!');
    }
}
