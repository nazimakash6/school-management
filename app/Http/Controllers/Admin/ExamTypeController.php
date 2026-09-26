<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExamTypeController extends Controller
{
    public function index(): View
    {
        $examTypes = ExamType::withCount('examinations')->orderBy('id', 'asc')->get();
        return view('pages.admin.examination.types', compact('examTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:exam_types,name',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        ExamType::create($validated);

        return redirect()->route('exam-types.index')->with('status', 'Exam Type created successfully.');
    }

    public function update(Request $request, ExamType $examType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:exam_types,name,' . $examType->id,
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $examType->update($validated);

        return redirect()->route('exam-types.index')->with('status', 'Exam Type updated successfully.');
    }

    public function destroy(ExamType $examType): RedirectResponse
    {
        if ($examType->examinations()->count() > 0) {
            return redirect()->route('exam-types.index')->with('error', 'Cannot delete Exam Type because it has associated examinations.');
        }

        $examType->delete();

        return redirect()->route('exam-types.index')->with('status', 'Exam Type deleted successfully.');
    }
}
