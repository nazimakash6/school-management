<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentPromotion\StoreStudentPromotion;
use App\Http\Requests\StudentPromotion\UpdateStudentPromotion;
use App\Models\AcademicSession;
use App\Models\Admission;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\StudentPromotion;
use Illuminate\Http\Request;

class StudentPromotionController extends Controller
{
    public function index(Request $request)
    {
        $academicSessionFilter = trim((string) $request->query('academic_session_id'));
        $currentClassFilter = trim((string) $request->query('class_filter'));
        $search = trim((string) $request->query('search'));

        // Fetch all admissions with their latest promotion & session
        $query = Admission::with([
            'promotions' => function ($query) {
                $query->latest('id');
            },
            'academicSession'
        ]);

        if ($academicSessionFilter !== '' && $academicSessionFilter !== 'all') {
            $query->where('academic_session_id', $academicSessionFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        $admissions = $query->get();

        // Calculate current class for each student
        $students = $admissions->map(function ($admission) {
            $latestPromotion = $admission->promotions->first();
            $admission->current_class = $latestPromotion ? $latestPromotion->promoted_to_class : $admission->class_name;
            return $admission;
        });

        // Apply class filter if present
        if ($currentClassFilter !== '' && $currentClassFilter !== 'all') {
            $students = $students->filter(function ($student) use ($currentClassFilter) {
                return strcasecmp(trim((string) $student->current_class), $currentClassFilter) === 0;
            });
        }

        // Get active classes and academic sessions for filter and promotion
        $dbClasses = StudentClass::where('status', 'active')->orderBy('name')->pluck('name');
        $academicSessions = AcademicSession::orderBy('id', 'desc')->get();

        return view('pages.admin.student-promotion.index', compact(
            'students',
            'dbClasses',
            'academicSessions',
            'academicSessionFilter',
            'currentClassFilter',
            'search'
        ));
    }

    public function create()
    {
        return view('pages.admin.student-promotion.create');
    }

    public function store(StoreStudentPromotion $request)
    {
        $validated = $request->validated();

        $studentIds = $validated['student_ids'];
        $promoteToClass = $validated['promote_to_class'];
        $targetSessionId = $validated['target_academic_session_id'] ?? null;
        $promotionType = $validated['promotion_type'];
        $promotionDate = $validated['promotion_date'];
        $notes = $validated['notes'] ?? null;

        // Fetch selected admissions
        $students = Admission::with([
            'promotions' => function ($query) {
                $query->latest('id');
            }
        ])->whereIn('id', $studentIds)->get();

        $promotedCount = 0;
        foreach ($students as $student) {
            $latestPromotion = $student->promotions->first();
            $currentClass = $latestPromotion ? $latestPromotion->promoted_to_class : $student->class_name;

            // Log the promotion history
            StudentPromotion::create([
                'admission_id' => $student->id,
                'promoted_from_class' => $currentClass,
                'promoted_to_class' => $promoteToClass,
                'promotion_type' => $promotionType,
                'promotion_date' => $promotionDate,
                'notes' => $notes,
            ]);

            // Update admission record class & session
            $student->class_name = $promoteToClass;
            if ($targetSessionId) {
                $student->academic_session_id = $targetSessionId;
            }
            $student->save();

            // Direct sync with Student table
            $updateData = ['class_name' => $promoteToClass];
            if ($targetSessionId) {
                $updateData['academic_session_id'] = $targetSessionId;
            }
            Student::where('admission_no', $student->admission_no)->update($updateData);

            $promotedCount++;
        }

        if ($promotedCount > 0) {
            return redirect()->route('student-promotion.index', ['class_filter' => $promoteToClass])
                ->with('success', "{$promotedCount} student(s) successfully promoted to class \"{$promoteToClass}\" and updated in Student List!");
        }

        return redirect()->back()->with('warning', 'No students were selected for promotion.');
    }

    public function show($id)
    {
        return view('pages.admin.student-promotion.show', compact('id'));
    }

    public function edit($id)
    {
        return view('pages.admin.student-promotion.edit', compact('id'));
    }

    public function update(UpdateStudentPromotion $request, $id)
    {
        $validated = $request->validated();
        $promotion = StudentPromotion::findOrFail($id);
        $promotion->update($validated);

        return redirect()->route('admin.student-promotion.index')->with('success', 'Student promotion updated successfully.');
    }
}
