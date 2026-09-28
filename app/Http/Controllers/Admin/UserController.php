<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));
        $role   = $request->get('role');
        $status = $request->get('status');

        $query = User::with(['roleRelation', 'directPermissions', 'student', 'staff']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('father_cnic', 'like', "%{$search}%");
            });
        }

        if ($role && $role !== 'all') {
            $query->where(function ($q) use ($role) {
                $q->where('role', $role)
                  ->orWhereHas('roleRelation', fn($rq) => $rq->where('name', $role)->orWhere('slug', $role));
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $allRoles = Role::orderBy('name')->get();
        $rolesList = $allRoles->pluck('name')->toArray();
        if (empty($rolesList)) {
            $rolesList = User::roles();
        }

        $availablePermissionsGrouped = Role::availablePermissionsGrouped();
        $allPermissions = Permission::orderBy('module')->orderBy('id')->get();

        $academicSessions = AcademicSession::orderBy('id', 'desc')->get();
        $classes          = StudentClass::orderBy('name')->get();

        $linkedStudentIds = User::whereNotNull('student_id')->pluck('student_id')->toArray();
        $linkedStaffIds   = User::whereNotNull('staff_id')->pluck('staff_id')->toArray();

        // Unlinked Students
        $unlinkedStudents = Student::whereNotIn('id', $linkedStudentIds)
            ->orderBy('first_name')
            ->get([
                'id',
                'first_name',
                'last_name',
                'admission_no',
                'roll_no',
                'class_name',
                'academic_session_id',
                'father_name',
                'father_cnic',
                'guardian_name',
                'guardian_cnic',
                'student_email',
                'student_mobile_no'
            ]);

        // All Students for Parent linking
        $allStudentsForParent = Student::orderBy('first_name')
            ->get([
                'id',
                'first_name',
                'last_name',
                'admission_no',
                'roll_no',
                'class_name',
                'father_name',
                'father_cnic',
                'guardian_name',
                'guardian_cnic',
                'guardian_email'
            ]);

        // Unlinked Staff (non-teachers)
        $unlinkedStaff = Staff::whereNotIn('id', $linkedStaffIds)
            ->where(function ($q) {
                $q->whereNull('department')
                  ->orWhere('department', '!=', 'Academics');
            })
            ->where(function ($q) {
                $q->whereNull('designation')
                  ->orWhere(function ($dq) {
                      $dq->where('designation', 'not like', '%teacher%')
                         ->where('designation', 'not like', '%Teacher%');
                  });
            })
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'staff_id', 'designation', 'department', 'email', 'cnic', 'mobile_no']);

        // Unlinked Teachers
        $unlinkedTeachers = Staff::whereNotIn('id', $linkedStaffIds)
            ->where(function ($q) {
                $q->where('department', 'Academics')
                  ->orWhere('designation', 'like', '%teacher%')
                  ->orWhere('designation', 'like', '%Teacher%');
            })
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'staff_id', 'designation', 'department', 'email', 'cnic', 'mobile_no']);

        $allUnlinkedStaff = Staff::whereNotIn('id', $linkedStaffIds)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'staff_id', 'designation', 'department', 'email', 'cnic', 'mobile_no']);

        $students = $unlinkedStudents;
        $staffMembers = $allUnlinkedStaff;

        // Dashboard Stats
        $stats = [
            'total'    => User::count(),
            'active'   => User::where('status', 'active')->count(),
            'teachers' => User::where('role', User::ROLE_TEACHER)->orWhereHas('roleRelation', fn($q)=>$q->where('name', User::ROLE_TEACHER))->count(),
            'staff'    => User::whereIn('role', [User::ROLE_STAFF, User::ROLE_ACCOUNTANT, User::ROLE_RECEPTIONIST])->count(),
            'students' => User::where('role', User::ROLE_STUDENT)->orWhereHas('roleRelation', fn($q)=>$q->where('name', User::ROLE_STUDENT))->count(),
            'parents'  => User::where('role', User::ROLE_PARENT)->orWhereHas('roleRelation', fn($q)=>$q->where('name', User::ROLE_PARENT))->count(),
        ];

        return view('pages.admin.users.index', compact(
            'users',
            'allRoles',
            'rolesList',
            'stats',
            'search',
            'role',
            'status',
            'availablePermissionsGrouped',
            'allPermissions',
            'academicSessions',
            'classes',
            'unlinkedStudents',
            'allStudentsForParent',
            'unlinkedStaff',
            'unlinkedTeachers',
            'allUnlinkedStaff',
            'students',
            'staffMembers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email',
            'password'    => 'required|string|min:6',
            'role'        => 'required|string',
            'status'      => 'required|in:active,inactive',
            'student_id'  => 'nullable|exists:students,id',
            'staff_id'    => 'nullable|exists:staff,id',
            'father_cnic' => 'nullable|string|max:30',
        ]);

        $roleObj = Role::where('name', $validated['role'])
            ->orWhere('slug', \Illuminate\Support\Str::slug($validated['role']))
            ->first();

        $validated['role_id']  = $roleObj?->id;
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return redirect()->route('users.index')->with('success', "User '{$user->name}' created successfully.");
    }

    public function update(Request $request, User $user)
    {
        // Protect Super Admin from role changes or deactivation
        if ($user->isSuperAdmin() && auth()->id() !== $user->id) {
            return redirect()->back()->with('error', 'Super Admin account cannot be modified by other users.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'        => 'required|string',
            'status'      => 'required|in:active,inactive',
            'password'    => 'nullable|string|min:6',
            'student_id'  => 'nullable|exists:students,id',
            'staff_id'    => 'nullable|exists:staff,id',
            'father_cnic' => 'nullable|string|max:30',
        ]);

        if ($user->isSuperAdmin()) {
            $validated['role']   = User::ROLE_ADMIN;
            $validated['status'] = User::ACTIVE;
        }

        $roleObj = Role::where('name', $validated['role'])
            ->orWhere('slug', \Illuminate\Support\Str::slug($validated['role']))
            ->first();

        $validated['role_id'] = $roleObj?->id;

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', "User '{$user->name}' updated successfully.");
    }

    public function updatePermissions(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin already possesses all permissions.');
        }

        $allowedNames = $request->input('allowed_permissions', []);
        $deniedNames  = $request->input('denied_permissions', []);

        $syncData = [];

        if (!empty($allowedNames)) {
            $allowedPerms = Permission::whereIn('name', $allowedNames)->get();
            foreach ($allowedPerms as $perm) {
                $syncData[$perm->id] = ['is_granted' => true];
            }
        }

        if (!empty($deniedNames)) {
            $deniedPerms = Permission::whereIn('name', $deniedNames)->get();
            foreach ($deniedPerms as $perm) {
                // Denied overrides allow
                $syncData[$perm->id] = ['is_granted' => false];
            }
        }

        $user->directPermissions()->sync($syncData);

        return redirect()->route('users.index')->with('success', "Individual permissions updated for user '{$user->name}'.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin account cannot be deactivated.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return redirect()->back()->with('success', "User status updated to {$newStatus}.");
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin account cannot be deleted.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own logged-in account.');
        }

        $user->directPermissions()->detach();
        $user->delete();

        return redirect()->route('users.index')->with('success', "User '{$user->name}' deleted successfully.");
    }
}
