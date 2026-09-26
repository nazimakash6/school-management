<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPlanning;
use App\Models\AcademicSession;
use App\Models\Staff;
use App\Models\StudentClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AcademicPlanningController extends Controller
{
    public function __construct()
    {
        view()->share('errors', session('errors', new \Illuminate\Support\ViewErrorBag));
    }
    public function index(Request $request): View
    {
        $search      = trim((string) $request->string('search'));
        $planType    = trim((string) $request->string('plan_type', 'all'));
        $className   = trim((string) $request->string('class_name', 'all'));
        $subjectName = trim((string) $request->string('subject_name', 'all'));
        $status      = trim((string) $request->string('status', 'all'));

        $query = AcademicPlanning::query()->with('teacher')->latest('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('objectives', 'like', "%{$search}%")
                  ->orWhere('topics_covered', 'like', "%{$search}%")
                  ->orWhere('class_name', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%");
            });
        }

        if ($planType !== 'all') {
            $query->where('plan_type', $planType);
        }

        if ($className !== 'all') {
            $query->where('class_name', $className);
        }

        if ($subjectName !== 'all') {
            $query->where('subject_name', $subjectName);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $plans = $query->paginate(12)->withQueryString();

        // Metrics
        $totalPlans   = AcademicPlanning::count();
        $activePlans  = AcademicPlanning::where('status', 'active')->count();
        $annualPlans  = AcademicPlanning::where('plan_type', 'annual')->count();
        $monthlyPlans = AcademicPlanning::where('plan_type', 'monthly')->count();
        $trashCount   = AcademicPlanning::onlyTrashed()->count();

        // Dropdown data
        $classes  = StudentClass::query()->orderBy('name')->pluck('name')->filter()->unique()->values();
        $subjects = Subject::query()->orderBy('subject_name')->pluck('subject_name')->filter()->unique()->values();
        $teachers = Staff::query()->where('status', 'active')->orderBy('first_name')->get();

        return view('pages.admin.academic-planning.index', compact(
            'plans',
            'search',
            'planType',
            'className',
            'subjectName',
            'status',
            'totalPlans',
            'activePlans',
            'annualPlans',
            'monthlyPlans',
            'trashCount',
            'classes',
            'subjects',
            'teachers'
        ));
    }

    public function create(): View
    {
        $classes  = StudentClass::query()->orderBy('name')->pluck('name')->filter()->unique()->values();
        $subjects = Subject::query()->orderBy('subject_name')->pluck('subject_name')->filter()->unique()->values();
        $teachers = Staff::query()->where('status', 'active')->orderBy('first_name')->get();
        $sessions = AcademicSession::query()->orderBy('session_name', 'desc')->pluck('session_name')->filter()->unique()->values();

        return view('pages.admin.academic-planning.create', compact('classes', 'subjects', 'teachers', 'sessions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'plan_type'            => 'required|string|in:annual,monthly,weekly,daily,unit',
            'class_name'           => 'nullable|string|max:100',
            'subject_name'         => 'nullable|string|max:100',
            'academic_session'     => 'nullable|string|max:100',
            'teacher_id'           => 'nullable|integer|exists:staff,id',
            'start_date'           => 'nullable|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'objectives'           => 'nullable|string',
            'topics_covered'       => 'nullable|string',
            'teaching_methodology' => 'nullable|string',
            'assessment_plan'      => 'nullable|string',
            'status'               => 'required|string|in:draft,active,completed,archived',
            'attachment_file'      => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png,zip|max:10240',
        ]);

        if ($request->hasFile('attachment_file')) {
            $path = $request->file('attachment_file')->store('academic_plans', 'public');
            $validated['attachment'] = $path;
        }

        AcademicPlanning::create($validated);

        return redirect()->route('academic-planning.index')
            ->with('success', 'Academic plan created successfully.');
    }

    public function show($id): View
    {
        $plan = AcademicPlanning::with('teacher')->findOrFail($id);
        return view('pages.admin.academic-planning.show', compact('plan'));
    }

    public function edit($id): View
    {
        $plan     = AcademicPlanning::findOrFail($id);
        $classes  = StudentClass::query()->orderBy('name')->pluck('name')->filter()->unique()->values();
        $subjects = Subject::query()->orderBy('subject_name')->pluck('subject_name')->filter()->unique()->values();
        $teachers = Staff::query()->where('status', 'active')->orderBy('first_name')->get();
        $sessions = AcademicSession::query()->orderBy('session_name', 'desc')->pluck('session_name')->filter()->unique()->values();

        return view('pages.admin.academic-planning.edit', compact('plan', 'classes', 'subjects', 'teachers', 'sessions'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $plan = AcademicPlanning::findOrFail($id);

        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'plan_type'            => 'required|string|in:annual,monthly,weekly,daily,unit',
            'class_name'           => 'nullable|string|max:100',
            'subject_name'         => 'nullable|string|max:100',
            'academic_session'     => 'nullable|string|max:100',
            'teacher_id'           => 'nullable|integer|exists:staff,id',
            'start_date'           => 'nullable|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'objectives'           => 'nullable|string',
            'topics_covered'       => 'nullable|string',
            'teaching_methodology' => 'nullable|string',
            'assessment_plan'      => 'nullable|string',
            'status'               => 'required|string|in:draft,active,completed,archived',
            'attachment_file'      => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png,zip|max:10240',
        ]);

        if ($request->hasFile('attachment_file')) {
            if ($plan->attachment && Storage::disk('public')->exists($plan->attachment)) {
                Storage::disk('public')->delete($plan->attachment);
            }
            $path = $request->file('attachment_file')->store('academic_plans', 'public');
            $validated['attachment'] = $path;
        }

        $plan->update($validated);

        return redirect()->route('academic-planning.index')
            ->with('success', 'Academic plan updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $plan = AcademicPlanning::findOrFail($id);
        $plan->delete();

        return redirect()->route('academic-planning.index')
            ->with('success', 'Academic plan moved to trash.');
    }

    public function trash(): View
    {
        $plans = AcademicPlanning::onlyTrashed()->with('teacher')->latest('deleted_at')->paginate(12);
        return view('pages.admin.academic-planning.trash', compact('plans'));
    }

    public function restore($id): RedirectResponse
    {
        $plan = AcademicPlanning::onlyTrashed()->findOrFail($id);
        $plan->restore();

        return redirect()->route('academic-planning.trash')
            ->with('success', 'Academic plan restored successfully.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $plan = AcademicPlanning::onlyTrashed()->findOrFail($id);

        if ($plan->attachment && Storage::disk('public')->exists($plan->attachment)) {
            Storage::disk('public')->delete($plan->attachment);
        }

        $plan->forceDelete();

        return redirect()->route('academic-planning.trash')
            ->with('success', 'Academic plan permanently deleted.');
    }

    public function bulkTrash(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Please select at least one academic plan to move to trash.');
        }

        AcademicPlanning::whereIn('id', $ids)->delete();

        return redirect()->route('academic-planning.index')
            ->with('success', count($ids) . ' academic plan(s) moved to trash successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one record.');
        }

        if ($action === 'restore') {
            AcademicPlanning::onlyTrashed()->whereIn('id', $ids)->restore();
            return redirect()->route('academic-planning.trash')
                ->with('success', count($ids) . ' academic plan(s) restored successfully.');
        }

        if ($action === 'force_delete' || $action === 'delete') {
            $plans = AcademicPlanning::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($plans as $plan) {
                if ($plan->attachment && Storage::disk('public')->exists($plan->attachment)) {
                    Storage::disk('public')->delete($plan->attachment);
                }
                $plan->forceDelete();
            }
            return redirect()->route('academic-planning.trash')
                ->with('success', count($plans) . ' academic plan(s) permanently deleted.');
        }

        return back()->with('error', 'Invalid action selected.');
    }
}
