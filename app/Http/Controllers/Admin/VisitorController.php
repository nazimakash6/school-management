<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VisitorController extends Controller
{
    public const PURPOSES = [
        'Student Early Pick-up',
        'Fee Payment & Inquiry',
        'Meeting Staff/Teacher',
        'Vendor / Delivery',
        'Admission Query',
        'Parent Consultation',
        'Official Meeting',
        'General Inquiry',
    ];

    public const MEET_TYPES = [
        'Student',
        'Staff',
        'General',
    ];

    public const GATES = [
        'Main Gate 1',
        'Gate 2 (East)',
        'Gate 3 (Junior Block)',
        'Admin Block Gate',
    ];

    public function index(Request $request)
    {
        $purposes  = self::PURPOSES;
        $meetTypes = self::MEET_TYPES;

        $query = Visitor::with(['student', 'staff', 'creator'])
            ->latest('check_in_time')
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('meet_type')) {
            $query->where('meet_type', $request->meet_type);
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('date')) {
            $query->whereDate('visit_date', $request->date);
        } else {
            // Default filter to today if requested or show all
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('visitor_name', 'like', "%{$search}%")
                  ->orWhere('pass_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('cnic_id', 'like', "%{$search}%")
                  ->orWhere('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('person_to_meet', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($s) use ($search) {
                      $s->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('roll_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('staff', function ($st) use ($search) {
                      $st->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $visitors = $query->paginate(15)->appends($request->query());

        // Overview Statistics
        $currentlyInsideCount = Visitor::where('status', 'Checked-In')->count();
        $totalToday           = Visitor::whereDate('visit_date', Carbon::today())->count();
        $studentVisitsToday   = Visitor::whereDate('visit_date', Carbon::today())->where('meet_type', 'Student')->count();
        $staffVisitsToday     = Visitor::whereDate('visit_date', Carbon::today())->where('meet_type', 'Staff')->count();

        return view('pages.admin.visitors.index', compact(
            'visitors', 'purposes', 'meetTypes',
            'currentlyInsideCount', 'totalToday', 'studentVisitsToday', 'staffVisitsToday'
        ));
    }

    public function create()
    {
        $purposes  = self::PURPOSES;
        $meetTypes = self::MEET_TYPES;
        $gates     = self::GATES;
        $classes   = StudentClass::orderBy('name')->get();
        $staffList = Staff::orderBy('first_name')->get();

        return view('pages.admin.visitors.create', compact(
            'purposes', 'meetTypes', 'gates', 'classes', 'staffList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'visitor_name'   => 'required|string|max:255',
            'phone'          => 'required|string|max:50',
            'cnic_id'        => 'nullable|string|max:50',
            'num_persons'    => 'required|integer|min:1',
            'purpose'        => 'required|string',
            'meet_type'      => 'required|in:Student,Staff,General',
            'student_id'     => 'nullable|exists:students,id',
            'staff_id'       => 'nullable|exists:staff,id',
            'person_to_meet' => 'nullable|string|max:255',
            'vehicle_no'     => 'nullable|string|max:100',
            'gate_no'        => 'required|string',
            'remarks'        => 'nullable|string',
            'id_proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('id_proof_image')) {
            $imagePath = $request->file('id_proof_image')->store('visitor_proofs', 'public');
        }

        $passCode = 'VSTR-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));

        $visitor = Visitor::create([
            'pass_code'      => $passCode,
            'visitor_name'   => $request->visitor_name,
            'phone'          => $request->phone,
            'cnic_id'        => $request->cnic_id,
            'num_persons'    => $request->num_persons,
            'purpose'        => $request->purpose,
            'meet_type'      => $request->meet_type,
            'student_id'     => $request->meet_type === 'Student' ? $request->student_id : null,
            'staff_id'       => $request->meet_type === 'Staff' ? $request->staff_id : null,
            'person_to_meet' => $request->person_to_meet,
            'visit_date'     => date('Y-m-d'),
            'check_in_time'  => Carbon::now(),
            'vehicle_no'     => $request->vehicle_no,
            'gate_no'        => $request->gate_no,
            'status'         => 'Checked-In',
            'id_proof_image' => $imagePath,
            'remarks'        => $request->remarks,
            'created_by'     => Auth::id(),
        ]);

        return redirect()->route('visitors.show', $visitor->id)
            ->with('success', "Visitor pass {$passCode} issued for {$visitor->visitor_name}. Currently Checked-In!");
    }

    public function show($id)
    {
        $visitor = Visitor::with(['student', 'staff', 'creator'])->findOrFail($id);
        return view('pages.admin.visitors.show', compact('visitor'));
    }

    public function edit($id)
    {
        $visitor   = Visitor::with(['student', 'staff'])->findOrFail($id);
        $purposes  = self::PURPOSES;
        $meetTypes = self::MEET_TYPES;
        $gates     = self::GATES;
        $classes   = StudentClass::orderBy('name')->get();
        $staffList = Staff::orderBy('first_name')->get();

        return view('pages.admin.visitors.edit', compact(
            'visitor', 'purposes', 'meetTypes', 'gates', 'classes', 'staffList'
        ));
    }

    public function update(Request $request, $id)
    {
        $visitor = Visitor::findOrFail($id);

        $request->validate([
            'visitor_name'   => 'required|string|max:255',
            'phone'          => 'required|string|max:50',
            'cnic_id'        => 'nullable|string|max:50',
            'num_persons'    => 'required|integer|min:1',
            'purpose'        => 'required|string',
            'meet_type'      => 'required|in:Student,Staff,General',
            'student_id'     => 'nullable|exists:students,id',
            'staff_id'       => 'nullable|exists:staff,id',
            'person_to_meet' => 'nullable|string|max:255',
            'vehicle_no'     => 'nullable|string|max:100',
            'gate_no'        => 'required|string',
            'status'         => 'required|in:Checked-In,Checked-Out,Blocked',
            'remarks'        => 'nullable|string',
            'id_proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'visitor_name'   => $request->visitor_name,
            'phone'          => $request->phone,
            'cnic_id'        => $request->cnic_id,
            'num_persons'    => $request->num_persons,
            'purpose'        => $request->purpose,
            'meet_type'      => $request->meet_type,
            'student_id'     => $request->meet_type === 'Student' ? $request->student_id : null,
            'staff_id'       => $request->meet_type === 'Staff' ? $request->staff_id : null,
            'person_to_meet' => $request->person_to_meet,
            'vehicle_no'     => $request->vehicle_no,
            'gate_no'        => $request->gate_no,
            'status'         => $request->status,
            'remarks'        => $request->remarks,
        ];

        if ($request->status === 'Checked-Out' && !$visitor->check_out_time) {
            $data['check_out_time'] = Carbon::now();
        }

        if ($request->hasFile('id_proof_image')) {
            if ($visitor->id_proof_image) {
                Storage::disk('public')->delete($visitor->id_proof_image);
            }
            $data['id_proof_image'] = $request->file('id_proof_image')->store('visitor_proofs', 'public');
        }

        $visitor->update($data);

        return redirect()->route('visitors.index')
            ->with('success', "Visitor record {$visitor->pass_code} updated successfully!");
    }

    public function destroy($id)
    {
        $visitor = Visitor::findOrFail($id);
        if ($visitor->id_proof_image) {
            Storage::disk('public')->delete($visitor->id_proof_image);
        }
        $visitor->delete();

        return redirect()->route('visitors.index')
            ->with('success', 'Visitor log entry deleted successfully!');
    }

    /**
     * Action: Instant 1-click Check Out
     */
    public function checkOut($id)
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->update([
            'status'         => 'Checked-Out',
            'check_out_time' => Carbon::now(),
        ]);

        return redirect()->route('visitors.index')
            ->with('success', "Visitor {$visitor->visitor_name} ({$visitor->pass_code}) checked out at " . date('g:i A'));
    }

    /**
     * AJAX: Returns students for class dropdown
     */
    public function getStudentsByClass($className)
    {
        $students = Student::where('class_name', $className)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'roll_no', 'admission_no', 'class_name']);

        return response()->json([
            'success'  => true,
            'students' => $students->map(fn($s) => [
                'id'           => $s->id,
                'first_name'   => $s->first_name,
                'last_name'    => $s->last_name,
                'roll_no'      => $s->roll_no,
                'admission_no' => $s->admission_no,
                'class_name'   => $s->class_name,
            ]),
        ]);
    }
}
