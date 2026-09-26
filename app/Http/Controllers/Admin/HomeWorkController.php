<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeWork;
use App\Models\SchoolInfo;
use App\Models\StudentClass;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeWorkController extends Controller
{
    public function index(Request $request)
    {
        $classes = StudentClass::orderBy('name')->get();

        $query = HomeWork::with(['studentClass', 'creator'])
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
                  });
            });
        }

        $homeworks = $query->paginate(15)->appends($request->query());

        // Stats metrics
        $totalCount = HomeWork::count();
        $activeCount = HomeWork::where('status', 'active')->count();
        $classesWithHomeworkToday = HomeWork::whereDate('assigned_date', Carbon::today())
            ->distinct('student_class_id')
            ->count('student_class_id');
        $dueTodayCount = HomeWork::whereDate('due_date', Carbon::today())->count();

        return view('pages.admin.homework.index', compact(
            'homeworks',
            'classes',
            'totalCount',
            'activeCount',
            'classesWithHomeworkToday',
            'dueTodayCount'
        ));
    }

    public function create(Request $request)
    {
        $classes = StudentClass::orderBy('name')->get();
        $selectedClassId = $request->get('class_id');

        return view('pages.admin.homework.create', compact('classes', 'selectedClassId'));
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
        ->where('status', 'active')
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
            'homeworks'        => 'required|array|min:1',
            'homeworks.*.subject_id'  => 'required|exists:subjects,id',
            'homeworks.*.description' => 'required|string',
            'homeworks.*.title'       => 'nullable|string|max:255',
            'homeworks.*.due_date'    => 'nullable|date',
            'homeworks.*.status'      => 'nullable|string',
            'homeworks.*.attachment'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'student_class_id.required' => 'Please select a class.',
            'homeworks.required'        => 'Please add homework for at least one subject.',
            'homeworks.*.subject_id.required' => 'Each homework task must have a subject assigned.',
            'homeworks.*.description.required' => 'Please enter the homework description for all added subjects.',
        ]);

        $tasks = [];
        $latestDueDate = null;

        foreach ($request->homeworks as $index => $item) {
            $attachmentPath = null;
            if ($request->hasFile("homeworks.{$index}.attachment")) {
                $attachmentPath = $request->file("homeworks.{$index}.attachment")->store('homework_attachments', 'public');
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
        $homeworkTitle = "{$classObj->name} Homework - " . Carbon::parse($request->assigned_date)->format('M d, Y');

        // Ensure EXACTLY ONE Homework record exists per Class & Assigned Date
        $existingHomework = HomeWork::where('student_class_id', $request->student_class_id)
            ->whereDate('assigned_date', $request->assigned_date)
            ->first();

        if ($existingHomework) {
            // Merge existing tasks with newly submitted tasks
            $existingTasks = $existingHomework->tasks;
            $mergedTasksMap = [];
            foreach ($existingTasks as $t) {
                $subKey = isset($t['subject_id']) ? (string)$t['subject_id'] : ('task_' . uniqid());
                $mergedTasksMap[$subKey] = $t;
            }
            foreach ($tasks as $t) {
                $subKey = isset($t['subject_id']) ? (string)$t['subject_id'] : ('task_' . uniqid());
                $mergedTasksMap[$subKey] = $t;
            }
            $finalTasks = array_values($mergedTasksMap);

            $existingHomework->update([
                'title'         => $homeworkTitle,
                'description'   => "Daily homework assigned for {$classObj->name} (" . count($finalTasks) . " subjects)",
                'subject_tasks' => $finalTasks,
                'due_date'      => $latestDueDate ?: $existingHomework->due_date,
                'status'        => 'active',
            ]);

            $homework = $existingHomework;
            $msg = "Homework Entry #{$homework->id} updated successfully for {$classObj->name} on " . Carbon::parse($request->assigned_date)->format('M d, Y') . " (" . count($finalTasks) . " subject task(s))!";
        } else {
            $homework = HomeWork::create([
                'student_class_id' => $request->student_class_id,
                'title'            => $homeworkTitle,
                'description'      => "Daily homework assigned for {$classObj->name} (" . count($tasks) . " subjects)",
                'subject_tasks'    => $tasks,
                'assigned_date'    => $request->assigned_date,
                'due_date'         => $latestDueDate,
                'status'           => 'active',
                'created_by'       => Auth::id(),
            ]);
            $msg = "Homework Entry #{$homework->id} created successfully for {$classObj->name} with " . count($tasks) . " subject task(s)!";
        }

        return redirect()->route('homework.show', $homework->id)
            ->with('success', $msg);
    }

    public function show($id)
    {
        $homework = HomeWork::with(['studentClass', 'creator'])->findOrFail($id);
        return view('pages.admin.homework.show', compact('homework'));
    }

    public function print($id)
    {
        $homework = HomeWork::with(['studentClass', 'creator'])->findOrFail($id);
        $schoolInfo = SchoolInfo::first();

        return view('pages.admin.homework.print', compact('homework', 'schoolInfo'));
    }

    public function edit($id)
    {
        $homework = HomeWork::with(['studentClass'])->findOrFail($id);

        // Consolidate any duplicate records for the same class and date into this single homework entry
        $duplicateRecords = HomeWork::where('student_class_id', $homework->student_class_id)
            ->whereDate('assigned_date', $homework->assigned_date)
            ->where('id', '!=', $homework->id)
            ->get();

        if ($duplicateRecords->count() > 0) {
            $allTasksMap = [];
            foreach ($homework->tasks as $t) {
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
            $homework->update([
                'subject_tasks' => array_values($allTasksMap),
                'subject_id'    => null,
            ]);
            $homework->refresh();
        }

        $classes = StudentClass::orderBy('name')->get();

        $subjects = Subject::where(function ($q) use ($homework) {
            $q->where('student_class_id', $homework->student_class_id)
              ->orWhere('class_name', $homework->studentClass?->name);
        })->get();

        return view('pages.admin.homework.edit', compact('homework', 'classes', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $homework = HomeWork::findOrFail($id);

        $request->validate([
            'student_class_id' => 'required|exists:student_classes,id',
            'assigned_date'    => 'required|date',
            'homeworks'        => 'required|array|min:1',
            'homeworks.*.subject_id'  => 'required|exists:subjects,id',
            'homeworks.*.description' => 'required|string',
            'homeworks.*.title'       => 'nullable|string|max:255',
            'homeworks.*.due_date'    => 'nullable|date',
            'homeworks.*.status'      => 'nullable|string',
            'homeworks.*.attachment'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'student_class_id.required' => 'Please select a class.',
            'homeworks.required'        => 'Please add homework for at least one subject.',
            'homeworks.*.subject_id.required' => 'Each homework task must have a subject assigned.',
            'homeworks.*.description.required' => 'Please enter the homework description for all added subjects.',
        ]);

        $newTasks = [];
        $latestDueDate = null;

        foreach ($request->homeworks as $index => $item) {
            $attachmentPath = $item['existing_attachment'] ?? null;

            if ($request->hasFile("homeworks.{$index}.attachment")) {
                if ($attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                $attachmentPath = $request->file("homeworks.{$index}.attachment")->store('homework_attachments', 'public');
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

        // Merge any other records for same target class and assigned date into this homework entry
        $duplicateRecords = HomeWork::where('student_class_id', $request->student_class_id)
            ->whereDate('assigned_date', $request->assigned_date)
            ->where('id', '!=', $homework->id)
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
        $homeworkTitle = "{$classObj->name} Homework - " . Carbon::parse($request->assigned_date)->format('M d, Y');

        $homework->update([
            'student_class_id' => $request->student_class_id,
            'title'            => $homeworkTitle,
            'description'      => "Daily homework assigned for {$classObj->name} (" . count($newTasks) . " subjects)",
            'subject_tasks'    => $newTasks,
            'assigned_date'    => $request->assigned_date,
            'due_date'         => $latestDueDate ?: $homework->due_date,
            'status'           => $request->status ?? 'active',
        ]);

        return redirect()->route('homework.show', $homework->id)
            ->with('success', "Homework Entry #{$homework->id} updated successfully for {$classObj->name} (" . count($newTasks) . " subject task(s))!");
    }

    public function destroy($id)
    {
        $homework = HomeWork::findOrFail($id);

        if (is_array($homework->subject_tasks)) {
            foreach ($homework->subject_tasks as $task) {
                if (!empty($task['attachment'])) {
                    Storage::disk('public')->delete($task['attachment']);
                }
            }
        }

        if ($homework->attachment) {
            Storage::disk('public')->delete($homework->attachment);
        }

        $homework->delete();

        return redirect()->route('homework.index')
            ->with('success', 'Homework entry deleted successfully!');
    }
}
