<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicSession\StoreAcademicSessionRequest;
use App\Http\Requests\AcademicSession\UpdateAcademicSessionRequest;
use App\Models\AcademicSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicSessionController extends Controller
{
    public function index(): View
    {
        $academicSessions = AcademicSession::query()
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->paginate(request('per_page', 25))
            ->withQueryString();

        return view('pages.admin.academic-sessions.index', compact('academicSessions'));
    }

    public function create(): View
    {
        return view('pages.admin.academic-sessions.create');
    }

    public function store(StoreAcademicSessionRequest $request): RedirectResponse
    {
        $data = AcademicSession::query()->create($request->validated());

        return redirect()->route('academic-sessions.index')->with('success', 'Academic session created successfully.');
    }

    public function show($id): View
    {
        $academicSession = AcademicSession::findOrFail($id);
        return view('pages.admin.academic-sessions.show', compact('academicSession'));
    }

    public function edit($id): View
    {
        $academicSession = AcademicSession::findOrFail($id);

        return view('pages.admin.academic-sessions.edit', compact('academicSession'));
    }

    public function update(UpdateAcademicSessionRequest $request, AcademicSession $academicSession): RedirectResponse
    {
        $academicSession->update($request->validated());

        return redirect()->route('academic-sessions.index')->with('success', 'Academic session updated successfully.');
    }

    public function trashPage(): View
    {
        $academicSessions = AcademicSession::onlyTrashed()->paginate(request('per_page', 25))->withQueryString();

        return view('pages.admin.academic-sessions.trash', compact('academicSessions'));
    }

    public function destroy(AcademicSession $academicSession): RedirectResponse
    {
        $academicSession->delete();
        $academicSession->status = 'Inactive';
        $academicSession->save();

        return redirect()->route('academic-sessions.index')->with('success', 'Academic session move to trash successfully.');
    }

    public function restore($id): RedirectResponse
    {
        $academicSession = AcademicSession::onlyTrashed()->findOrFail($id);
        $academicSession->restore();
        $academicSession->status = 'Active';
        $academicSession->save();

        return redirect()->route('academic-sessions.trash')->with('success', 'Academic session restored successfully.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $academicSession = AcademicSession::onlyTrashed()->findOrFail($id);
        $academicSession->forceDelete();

        return redirect()->route('academic-sessions.trash')->with('success', 'Academic session deleted permanently.');
    }

    public function bulkTrash(Request $request): RedirectResponse
    {
        if (!$request->filled('academic_sessions')) {
            return back()->with('error', 'Please select at least one record.');
        }

        AcademicSession::whereIn('id', $request->academic_sessions)->delete();

        return back()->with('success', 'Selected academic sessions moved to trash successfully.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $ids = $request->academic_sessions;

        if (empty($ids)) {

            return back()->with('error', 'Please select at least one record.');
        }

        if ($request->action == 'restore') {

            AcademicSession::onlyTrashed()
                ->whereIn('id', $ids)
                ->restore();

            return back()->with('success', 'Selected academic sessions restored successfully.');
        }

        if ($request->action == 'delete') {

            AcademicSession::onlyTrashed()
                ->whereIn('id', $ids)
                ->forceDelete();

            return back()->with('success', 'Selected academic sessions deleted permanently.');
        }

        return back()->with('error', 'Invalid action.');
    }
}
