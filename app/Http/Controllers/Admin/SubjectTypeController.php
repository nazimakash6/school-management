<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectType\StoreSubjectTypeRequest;
use App\Http\Requests\SubjectType\UpdateSubjectTypeRequest;
use App\Models\SubjectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectTypeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $query = SubjectType::query()->withCount('subjects')->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $subjectTypes = $query->paginate(25)->withQueryString();

        return view('pages.admin.subject_types.index', compact('subjectTypes', 'search'));
    }

    public function create(): View
    {
        return view('pages.admin.subject_types.create');
    }

    public function store(StoreSubjectTypeRequest $request): RedirectResponse
    {
        SubjectType::create($request->validated());

        return redirect()
            ->route('subject-types.index')
            ->with('success', 'Subject Type created successfully.');
    }

    public function edit(SubjectType $subjectType): View
    {
        return view('pages.admin.subject_types.edit', compact('subjectType'));
    }

    public function update(UpdateSubjectTypeRequest $request, SubjectType $subjectType): RedirectResponse
    {
        $subjectType->update($request->validated());

        return redirect()
            ->route('subject-types.index')
            ->with('success', 'Subject Type updated successfully.');
    }

    public function destroy(SubjectType $subjectType): RedirectResponse
    {
        if ($subjectType->subjects()->count() > 0) {
            return back()->with('error', 'Cannot delete subject type because it is linked to one or more subjects.');
        }

        $subjectType->delete();

        return redirect()
            ->route('subject-types.index')
            ->with('success', 'Subject Type deleted successfully.');
    }
}
