<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceSettingsRequest;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentClass;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $userType  = $request->string('user_type', 'student')->toString();
        $date      = $request->string('date', now()->format('Y-m-d'))->toString();
        $className = $request->string('class_name')->toString();
        $sectionName = $request->string('section_name')->toString();
        $search    = trim($request->string('search')->toString());

        if (!in_array($userType, ['student', 'staff'], true)) {
            $userType = 'student';
        }

        $settings = AttendanceSetting::getSettings();
        $now = now();
        $selectedDate = Carbon::parse($date);
        $isToday = $selectedDate->isToday();
        $currentTimeStr = $now->format('H:i:s');

        // Timing Flags (If past date, timings are considered passed)
        $isSchoolOpen = !$isToday || ($currentTimeStr >= $settings->school_open_time);
        $isSchoolStarted = !$isToday || ($currentTimeStr >= $settings->school_start_time);
        $isBreakTime = !$isToday || ($currentTimeStr >= $settings->school_break_time);
        $isSchoolEnded = !$isToday || ($currentTimeStr >= $settings->school_end_time);

        // Fetch User List
        $usersList = collect();
        if ($userType === 'student') {
            $query = Student::query()->orderBy('class_name')->orderBy('first_name');
            if ($className !== '') {
                $query->where('class_name', $className);
            }
            if ($sectionName !== '') {
                $query->where('section_name', $sectionName);
            }
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('admission_no', 'like', "%{$search}%");
                });
            }
            $usersList = $query->get();
        } else {
            $query = Staff::query()->orderBy('first_name');
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('employee_id', 'like', "%{$search}%");
                });
            }
            $usersList = $query->get();
        }

        // Fetch Existing Attendance Records for date & type
        $existingRecords = Attendance::where('user_type', $userType)
            ->where('date', $date)
            ->get()
            ->keyBy('user_id');

        // Check if attendance has already been marked for listed records
        $isAttendanceMarked = $existingRecords->filter(fn($r) => !is_null($r->session1_status) || !is_null($r->final_status))->count() > 0;

        // Available classes for student filtering
        $classList = StudentClass::orderBy('name')->pluck('name')->unique();

        // Check if authenticated user has Admin privilege
        $isAdmin = auth()->check() ? auth()->user()->hasRole('admin') : false;

        // Fetch Principal & Admin contact details for staff attendance notifications
        $schoolInfo     = \App\Models\SchoolInfo::first();

        $principalStaff = Staff::where('designation', 'like', '%principal%')->first();
        $principalName  = $principalStaff ? $principalStaff->full_name : ($schoolInfo?->principal_name ?: 'Principal');
        $principalPhone = $principalStaff ? $principalStaff->mobile_no : ($schoolInfo?->phone ?: '');
        $principalEmail = $principalStaff ? $principalStaff->email : ($schoolInfo?->email ?: '');

        $adminStaff     = Staff::where('designation', 'like', '%admin%')->first();
        $adminName      = $adminStaff ? $adminStaff->full_name : 'Admin';
        $adminPhone     = $adminStaff ? $adminStaff->mobile_no : ($schoolInfo?->phone ?: '');
        $adminEmail     = $adminStaff ? $adminStaff->email : ($schoolInfo?->email ?: '');

        return view('pages.admin.attendance.index', compact(
            'userType',
            'date',
            'className',
            'sectionName',
            'search',
            'settings',
            'isToday',
            'isSchoolOpen',
            'isSchoolStarted',
            'isBreakTime',
            'isSchoolEnded',
            'isAdmin',
            'isAttendanceMarked',
            'usersList',
            'existingRecords',
            'classList',
            'principalName',
            'principalPhone',
            'principalEmail',
            'adminName',
            'adminPhone',
            'adminEmail'
        ));
    }

    public function markAttendance(StoreAttendanceRequest $request): RedirectResponse
    {
        $validated   = $request->validated();
        $userType    = $validated['user_type'];
        $date        = $validated['date'];
        $currentTime = now()->format('H:i:s');
        $isAdmin     = auth()->check() ? auth()->user()->hasRole('admin') : false;

        $settings     = AttendanceSetting::getSettings();
        $selectedDate = Carbon::parse($date);
        $isToday      = $selectedDate->isToday();
        $isSchoolOpen = !$isToday || (now()->format('H:i:s') >= $settings->school_open_time);

        // Security check for non-admin users
        if (!$isAdmin) {
            // 1. Check if school is open today
            if ($isToday && !$isSchoolOpen) {
                $openTimeFormatted = Carbon::parse($settings->school_open_time)->format('h:i A');
                return redirect()->back()->with('error', "School has not opened yet today. Attendance opens at {$openTimeFormatted}.");
            }

            // 2. Check if attendance has already been marked for any of these users on this date
            $userIds = array_column($validated['attendance'], 'user_id');
            $alreadyMarkedCount = Attendance::where('user_type', $userType)
                ->where('date', $date)
                ->whereIn('user_id', $userIds)
                ->where(function ($q) {
                    $q->whereNotNull('session1_status')->orWhereNotNull('final_status');
                })
                ->count();

            if ($alreadyMarkedCount > 0) {
                return redirect()->back()->with('error', 'Attendance for today has already been marked and is locked until tomorrow. Only Admins can modify marked attendance.');
            }
        }

        foreach ($validated['attendance'] as $item) {
            $userId = $item['user_id'];
            $status = $item['status'] ?? ($item['s1_status'] ?? null);

            if (!$status) {
                continue;
            }

            $remarks = $item['remarks'] ?? null;

            $record = Attendance::firstOrNew([
                'user_type' => $userType,
                'user_id'   => $userId,
                'date'      => $date,
            ]);

            $record->session1_status  = $status;
            $record->session1_time    = $currentTime;
            $record->session1_remarks = $remarks;

            $record->session2_status  = $status;
            $record->session2_time    = $currentTime;
            $record->session2_remarks = $remarks;

            $record->final_status     = $status;

            $record->save();
        }

        return redirect()->back()->with('status', "Daily attendance saved successfully for " . ucfirst($userType) . "s.");
    }

    public function updateSettings(UpdateAttendanceSettingsRequest $request): RedirectResponse
    {
        $settings = AttendanceSetting::getSettings();
        $settings->update($request->validated());

        return redirect()->back()->with('status', 'School schedule timings updated successfully.');
    }
}
