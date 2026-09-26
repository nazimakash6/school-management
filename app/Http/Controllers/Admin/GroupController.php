<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\StoreGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 10);
        $search  = trim((string) $request->string('search'));
        $status  = trim((string) $request->string('status', 'all'));

        $query = Group::latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('subjects', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $groups = $query->paginate($perPage)->withQueryString();

        // Student Counts per Group
        $groupStudentCounts = \App\Models\Student::query()
            ->selectRaw("COALESCE(NULLIF(group_name, ''), NULLIF(`group`, '')) as g_name, COUNT(*) as total")
            ->where(function($q) {
                $q->whereNotNull('group_name')->where('group_name', '!=', '')
                  ->orWhere(function($q2) {
                      $q2->whereNotNull('group')->where('group', '!=', '');
                  });
            })
            ->groupBy('g_name')
            ->pluck('total', 'g_name')
            ->toArray();

        $totalStudentsInGroups = array_sum($groupStudentCounts);

        // All groups with student counts for summary cards box
        $allGroupsWithCounts = Group::orderBy('name')->get()->map(function ($grp) use ($groupStudentCounts) {
            $grp->student_count = $groupStudentCounts[$grp->name] ?? 0;
            return $grp;
        });

        // Metrics
        $totalGroups  = Group::count();
        $activeGroups = Group::where('status', 'active')->count();
        $inactiveGroups = Group::where('status', 'inactive')->count();
        
        // Total mapped subjects count across groups
        $allGroupSubjects = Group::pluck('subjects')->flatten()->filter()->unique();
        $totalMappedSubjects = $allGroupSubjects->count();

        return view('pages.admin.groups.index', compact(
            'groups',
            'perPage',
            'search',
            'status',
            'totalGroups',
            'activeGroups',
            'inactiveGroups',
            'totalMappedSubjects',
            'groupStudentCounts',
            'totalStudentsInGroups',
            'allGroupsWithCounts'
        ));
    }

    public function create(): View
    {
        $availableSubjects = Subject::distinct()
            ->pluck('subject_name')
            ->filter()
            ->sort()
            ->values();

        return view('pages.admin.groups.create', compact('availableSubjects'));
    }

    public function store(StoreGroupRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Process subjects input array / comma separated tags
        $subjectsList = [];
        if (!empty($validated['subject'])) {
            $subjectsList = array_values(array_filter(array_map('trim', $validated['subject'])));
        }

        Group::create([
            'name'        => $validated['name'],
            'subjects'    => $subjectsList,
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('groups.index')
            ->with('success', 'Group created successfully.');
    }

    public function show($id): View
    {
        $group = Group::findOrFail($id);
        $enrolledStudents = \App\Models\Student::where('group_name', $group->name)
            ->orWhere('group', $group->name)
            ->get();
        $studentCount = $enrolledStudents->count();

        return view('pages.admin.groups.show', compact('group', 'enrolledStudents', 'studentCount'));
    }

    public function edit($id): View
    {
        $group = Group::findOrFail($id);

        $availableSubjects = Subject::distinct()
            ->pluck('subject_name')
            ->filter()
            ->sort()
            ->values();

        return view('pages.admin.groups.edit', compact('group', 'availableSubjects'));
    }

    public function update(UpdateGroupRequest $request, $id): RedirectResponse
    {
        $group = Group::findOrFail($id);

        $validated = $request->validated();

        $subjectsList = [];
        if (!empty($validated['subject'])) {
            $subjectsList = array_values(array_filter(array_map('trim', $validated['subject'])));
        }

        $group->update([
            'name'        => $validated['name'],
            'subjects'    => $subjectsList,
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('groups.index')
            ->with('success', 'Group updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()
            ->route('groups.index')
            ->with('success', 'Group deleted successfully.');
    }
}
