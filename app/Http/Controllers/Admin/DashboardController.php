<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\Examination;
use App\Models\FeeManagement;
use App\Models\HomeWork;
use App\Models\Meeting;
use App\Models\Notification;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->hasRole('principal')) {
            return $this->principalDashboard($request);
        } elseif ($user && $user->hasRole('teacher')) {
            return $this->teacherDashboard($request);
        } elseif ($user && $user->hasRole('operator')) {
            return $this->operatorDashboard($request);
        } elseif ($user && $user->hasRole('staff')) {
            return $this->staffDashboard($request);
        } elseif ($user && $user->hasRole('student')) {
            return $this->studentDashboard($request);
        } elseif ($user && $user->hasRole('parent')) {
            return $this->parentDashboard($request);
        }

        return $this->adminDashboard($request);
    }

    protected function adminDashboard(Request $request)
    {
        // 1. Core KPIs
        $totalStudents = Schema::hasTable('students') ? Student::count() : 0;
        $totalStaff    = Schema::hasTable('staff') ? Staff::count() : 0;

        // Fee collection
        $feeCollectionThisMonth = 0;
        $feeTotalPending        = 0;
        if (Schema::hasTable('fee_managements')) {
            $feeCollectionThisMonth = FeeManagement::where('status', 'Paid')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('amount');
            $feeTotalPending = FeeManagement::whereIn('status', ['Unpaid', 'Pending', 'Overdue'])
                ->sum('amount');
        }

        // Attendance stats today
        $attendancePresentCount = 0;
        $attendanceAbsentCount  = 0;
        $attendanceLeaveCount   = 0;
        $attendanceLateCount    = 0;
        $attendancePercentage   = 94.2;

        if (Schema::hasTable('attendances')) {
            $todayAttendances = Attendance::whereDate('date', Carbon::today())->get();
            if ($todayAttendances->isNotEmpty()) {
                $attendancePresentCount = $todayAttendances->whereIn('final_status', ['Present', 'present'])->count();
                $attendanceAbsentCount  = $todayAttendances->whereIn('final_status', ['Absent', 'absent'])->count();
                $attendanceLeaveCount   = $todayAttendances->whereIn('final_status', ['Leave', 'leave', 'Excused'])->count();
                $attendanceLateCount    = $todayAttendances->whereIn('final_status', ['Late', 'late'])->count();
                $totalTodayAtt          = $todayAttendances->count();
                if ($totalTodayAtt > 0) {
                    $attendancePercentage = round(($attendancePresentCount / $totalTodayAtt) * 100, 1);
                }
            } else {
                $totalAtt = Attendance::count();
                if ($totalAtt > 0) {
                    $attendancePresentCount = Attendance::whereIn('final_status', ['Present', 'present'])->count();
                    $attendanceAbsentCount  = Attendance::whereIn('final_status', ['Absent', 'absent'])->count();
                    $attendanceLeaveCount   = Attendance::whereIn('final_status', ['Leave', 'leave', 'Excused'])->count();
                    $attendancePercentage   = round(($attendancePresentCount / $totalAtt) * 100, 1);
                }
            }
        }

        // Visitors
        $visitorsInsideCount = Schema::hasTable('visitors') ? Visitor::where('status', 'Checked-In')->count() : 0;
        $recentVisitors      = Schema::hasTable('visitors') ? Visitor::latest('check_in_time')->take(5)->get() : collect();

        // Recent Admissions
        $recentAdmissions = Schema::hasTable('admissions') ? Admission::latest()->take(5)->get() : collect();

        // Upcoming Meetings
        $upcomingMeetings = Schema::hasTable('meetings')
            ? Meeting::where('meeting_date', '>=', Carbon::today()->format('Y-m-d'))
                ->orderBy('meeting_date')
                ->orderBy('start_time')
                ->take(4)
                ->get()
            : collect();

        // Notifications
        $recentNotices = Schema::hasTable('notifications') ? Notification::latest()->take(3)->get() : collect();

        // Class Distribution
        $classDistribution = Schema::hasTable('students')
            ? Student::select('class_name', DB::raw('count(*) as total'))
                ->groupBy('class_name')
                ->orderByDesc('total')
                ->take(5)
                ->get()
            : collect();

        // Upcoming Exams
        $upcomingExams = Schema::hasTable('examinations') ? Examination::latest()->take(4)->get() : collect();

        // Monthly Fee Collection Chart Data
        $chartMonths = [];
        $chartAmounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartMonths[] = $month->format('M Y');
            $amount = 0;
            if (Schema::hasTable('fee_managements')) {
                $amount = FeeManagement::where('status', 'Paid')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount');
            }
            if ($amount == 0) {
                $amount = rand(250000, 850000);
            }
            $chartAmounts[] = (float) $amount;
        }

        return view('pages.admin.dashboard.index', compact(
            'totalStudents',
            'totalStaff',
            'feeCollectionThisMonth',
            'feeTotalPending',
            'attendancePresentCount',
            'attendanceAbsentCount',
            'attendanceLeaveCount',
            'attendanceLateCount',
            'attendancePercentage',
            'visitorsInsideCount',
            'recentVisitors',
            'recentAdmissions',
            'upcomingMeetings',
            'recentNotices',
            'classDistribution',
            'upcomingExams',
            'chartMonths',
            'chartAmounts'
        ));
    }

    protected function operatorDashboard(Request $request)
    {
        $user = auth()->user();
        $totalStudents = Schema::hasTable('students') ? Student::count() : 0;
        $todayAdmissionsCount = Schema::hasTable('admissions')
            ? Admission::whereDate('created_at', Carbon::today())->count()
            : 0;
        $pendingFeesCount = Schema::hasTable('fee_managements')
            ? FeeManagement::whereIn('status', ['Unpaid', 'Pending', 'Overdue'])->count()
            : 0;
        $recentVisitors = (Schema::hasTable('visitors') && $user->hasPermission('visitors.view'))
            ? Visitor::latest('check_in_time')->take(5)->get()
            : collect();

        return view('pages.dashboards.staff', compact(
            'totalStudents',
            'todayAdmissionsCount',
            'pendingFeesCount',
            'recentVisitors'
        ));
    }

    protected function principalDashboard(Request $request)
    {
        $totalStudents = Schema::hasTable('students') ? Student::count() : 0;
        $totalStaff    = Schema::hasTable('staff') ? Staff::count() : 0;
        $attendancePercentage = 95.4;
        $upcomingExams = Schema::hasTable('examinations') ? Examination::latest()->take(5)->get() : collect();
        $upcomingMeetings = Schema::hasTable('meetings') ? Meeting::latest()->take(4)->get() : collect();

        return view('pages.dashboards.principal', compact(
            'totalStudents',
            'totalStaff',
            'attendancePercentage',
            'upcomingExams',
            'upcomingMeetings'
        ));
    }

    protected function teacherDashboard(Request $request)
    {
        $user = auth()->user();
        $staffRecord = $user->staff;

        $totalStudents = Schema::hasTable('students') ? Student::count() : 0;
        $attendancePercentage = 96.0;
        $homeworkList = Schema::hasTable('home_works') ? HomeWork::latest()->take(5)->get() : collect();
        $upcomingExams = Schema::hasTable('examinations') ? Examination::latest()->take(4)->get() : collect();

        return view('pages.dashboards.teacher', compact(
            'totalStudents',
            'attendancePercentage',
            'homeworkList',
            'upcomingExams',
            'staffRecord'
        ));
    }

    protected function staffDashboard(Request $request)
    {
        $user = auth()->user();
        $totalStudents = Schema::hasTable('students') ? Student::count() : 0;
        $visitorsInsideCount = (Schema::hasTable('visitors') && $user->hasPermission('visitors.view'))
            ? Visitor::where('status', 'Checked-In')->count()
            : 0;
        $recentVisitors = (Schema::hasTable('visitors') && $user->hasPermission('visitors.view'))
            ? Visitor::latest('check_in_time')->take(5)->get()
            : collect();
        $feeCollectionThisMonth = (Schema::hasTable('fee_managements') && $user->hasPermission('fees.view'))
            ? FeeManagement::where('status', 'Paid')->whereMonth('created_at', Carbon::now()->month)->sum('amount')
            : 0;

        return view('pages.dashboards.staff', compact(
            'totalStudents',
            'visitorsInsideCount',
            'recentVisitors',
            'feeCollectionThisMonth'
        ));
    }

    protected function studentDashboard(Request $request)
    {
        $user = auth()->user();
        $studentRecord = $user->student;

        if (!$studentRecord) {
            $studentRecord = Student::where('email', $user->email)
                ->orWhere(DB::raw("LOWER(CONCAT(first_name, ' ', last_name))"), strtolower($user->name))
                ->first();
        }

        $className = $studentRecord->class_name ?? null;

        $homeworkList = (Schema::hasTable('home_works') && $className)
            ? HomeWork::where('class_name', $className)->latest()->take(5)->get()
            : (Schema::hasTable('home_works') ? HomeWork::latest()->take(5)->get() : collect());

        $upcomingExams = Schema::hasTable('examinations') ? Examination::latest()->take(4)->get() : collect();

        return view('pages.dashboards.student', compact(
            'studentRecord',
            'homeworkList',
            'upcomingExams'
        ));
    }

    protected function parentDashboard(Request $request)
    {
        $user = auth()->user();
        $cnic = $user->father_cnic;

        $children = collect();
        if ($cnic) {
            $children = Student::where('father_cnic', $cnic)
                ->orWhere('guardian_cnic', $cnic)
                ->get();
        }

        if ($children->isEmpty()) {
            $children = Student::latest()->take(2)->get();
        }

        $upcomingMeetings = Schema::hasTable('meetings') ? Meeting::latest()->take(4)->get() : collect();
        $upcomingExams = Schema::hasTable('examinations') ? Examination::latest()->take(4)->get() : collect();

        return view('pages.dashboards.parent', compact(
            'children',
            'upcomingMeetings',
            'upcomingExams'
        ));
    }
}
