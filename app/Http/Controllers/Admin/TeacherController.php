<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search  = trim((string) $request->string('search'));
        $status  = trim((string) $request->string('status', 'all'));
        $shift   = trim((string) $request->string('shift', 'all'));

        if (! in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        if (! in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        if (! in_array($shift, ['all', 'morning', 'evening'], true)) {
            $shift = 'all';
        }

        $query = $this->applyTeacherFilter(Staff::query())->latest();
        $this->applyFilters($query, $search, $status, $shift);

        $teachers     = $query->paginate($perPage)->withQueryString();
        $totalCount   = $this->applyTeacherFilter(Staff::query())->count();
        $activeCount  = $this->applyTeacherFilter(Staff::query())->where('status', 'active')->count();
        $inactiveCount = $this->applyTeacherFilter(Staff::query())->where('status', 'inactive')->count();
        $newThisMonth = $this->applyTeacherFilter(Staff::query())
            ->whereMonth('joining_date', now()->month)
            ->whereYear('joining_date', now()->year)
            ->count();
        $trashCount   = $this->applyTeacherFilter(Staff::onlyTrashed())->count();

        return view('pages.admin.teacher.index', compact(
            'teachers',
            'perPage',
            'search',
            'status',
            'shift',
            'totalCount',
            'activeCount',
            'inactiveCount',
            'newThisMonth',
            'trashCount',
        ));
    }

    public function trash(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search  = trim((string) $request->string('search'));
        $status  = trim((string) $request->string('status', 'all'));
        $shift   = trim((string) $request->string('shift', 'all'));

        if (! in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        if (! in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        if (! in_array($shift, ['all', 'morning', 'evening'], true)) {
            $shift = 'all';
        }

        $query = $this->applyTeacherFilter(Staff::onlyTrashed())->latest('deleted_at');
        $this->applyFilters($query, $search, $status, $shift);

        $teachers          = $query->paginate($perPage)->withQueryString();
        $trashCount        = $this->applyTeacherFilter(Staff::onlyTrashed())->count();
        $activeTeachers    = $this->applyTeacherFilter(Staff::query())->count();
        $trashedLast30Days = $this->applyTeacherFilter(Staff::onlyTrashed())
            ->whereDate('deleted_at', '>=', now()->subDays(30)->toDateString())
            ->count();

        return view('pages.admin.teacher.trash', compact(
            'teachers',
            'perPage',
            'search',
            'status',
            'shift',
            'trashCount',
            'activeTeachers',
            'trashedLast30Days',
        ));
    }

    public function restore(int $id): RedirectResponse
    {
        $teacher = $this->applyTeacherFilter(Staff::onlyTrashed())->findOrFail($id);
        $teacher->restore();

        return redirect()
            ->route('teacher.trash')
            ->with('success', 'Teacher restored successfully.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $teacher = $this->applyTeacherFilter(Staff::onlyTrashed())->findOrFail($id);

        if ($teacher->cv) {
            Storage::disk('public')->delete($teacher->cv);
        }

        if ($teacher->profile_picture) {
            Storage::disk('public')->delete($teacher->profile_picture);
        }

        $teacher->forceDelete();

        return redirect()
            ->route('teacher.trash')
            ->with('success', 'Teacher permanently deleted successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $teacher = $this->applyTeacherFilter(Staff::query())->findOrFail($id);
        $teacher->delete();

        return redirect()
            ->route('teacher.index')
            ->with('success', 'Teacher moved to trash successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'selected_ids'   => ['required', 'array', 'min:1'],
            'selected_ids.*' => ['integer', 'min:1'],
            'bulk_action'    => ['required', 'in:trash,restore,force_delete'],
        ]);

        $ids    = Arr::map($validated['selected_ids'], fn ($id) => (int) $id);
        $action = $validated['bulk_action'];

        if ($action === 'trash') {
            $this->applyTeacherFilter(Staff::query())->whereKey($ids)->delete();

            return redirect()
                ->route('teacher.index')
                ->with('success', 'Selected teachers moved to trash successfully.');
        }

        if ($action === 'restore') {
            $this->applyTeacherFilter(Staff::onlyTrashed())->whereIn('id', $ids)->restore();

            return redirect()
                ->route('teacher.trash')
                ->with('success', 'Selected teachers restored successfully.');
        }

        // force_delete
        $teachers = $this->applyTeacherFilter(Staff::onlyTrashed())->whereIn('id', $ids)->get();

        foreach ($teachers as $teacher) {
            if ($teacher->cv) {
                Storage::disk('public')->delete($teacher->cv);
            }

            if ($teacher->profile_picture) {
                Storage::disk('public')->delete($teacher->profile_picture);
            }
        }

        $this->applyTeacherFilter(Staff::onlyTrashed())->whereIn('id', $ids)->forceDelete();

        return redirect()
            ->route('teacher.trash')
            ->with('success', 'Selected teachers permanently deleted successfully.');
    }

    private function applyTeacherFilter(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('department', 'teaching')
              ->orWhereIn('designation', ['teacher', 'head_teacher']);
        });
    }

    private function applyFilters(Builder $query, string $search, string $status, string $shift): void
    {
        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('staff_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($shift !== 'all') {
            $query->where('shift', $shift);
        }
    }
}
