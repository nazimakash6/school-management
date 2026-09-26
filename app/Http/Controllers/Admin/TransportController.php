<?php

namespace App\Http\Controllers\Admin;

use App\Models\AcademicSession;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Transport;
use App\Models\TransportStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'routes');

        // Route Catalog Query
        $routesQuery = Transport::with(['driver', 'activeAllocations.student'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $routesQuery->where(function ($q) use ($search) {
                $q->where('route_title', 'like', "%{$search}%")
                  ->orWhere('route_code', 'like', "%{$search}%")
                  ->orWhere('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('vehicle_type')) {
            $routesQuery->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->filled('status')) {
            $routesQuery->where('status', $request->status);
        }

        $routes = $routesQuery->paginate(10, ['*'], 'routes_page')->withQueryString();

        // Passenger Roster Query
        $rosterQuery = TransportStudent::with(['route', 'student.studentClass'])->latest();
        if ($request->filled('roster_search')) {
            $rsearch = $request->roster_search;
            $rosterQuery->where(function ($q) use ($rsearch) {
                $q->where('stop_name', 'like', "%{$rsearch}%")
                  ->orWhereHas('route', function ($rq) use ($rsearch) {
                      $rq->where('route_title', 'like', "%{$rsearch}%")->orWhere('route_code', 'like', "%{$rsearch}%");
                  })
                  ->orWhereHas('student', function ($sq) use ($rsearch) {
                      $sq->where('first_name', 'like', "%{$rsearch}%")
                        ->orWhere('last_name', 'like', "%{$rsearch}%")
                        ->orWhere('admission_number', 'like', "%{$rsearch}%");
                  });
            });
        }

        $roster = $rosterQuery->paginate(10, ['*'], 'roster_page')->withQueryString();

        // Summary Statistics
        $totalRoutes = Transport::count();
        $totalPassengers = TransportStudent::where('status', 'Active')->count();
        $totalCapacity = Transport::sum('vehicle_capacity');
        $totalRevenue = TransportStudent::where('status', 'Active')->sum('monthly_fare');

        $stats = [
            'total_routes'     => $totalRoutes,
            'total_passengers' => $totalPassengers,
            'total_capacity'   => $totalCapacity,
            'total_revenue'    => $totalRevenue,
        ];

        // Fetch Drivers from Staff Table (Staff whose designation contains 'Driver')
        $drivers = Staff::where('status', 'active')
            ->where('designation', 'like', '%Driver%')
            ->orderBy('first_name')
            ->get();

        $availableRoutes = Transport::where('status', 'Active')->get();

        // Fetch Sessions & Classes for Assign Student Modal
        $academicSessions = AcademicSession::where('status', 'Active')->orderBy('start_date', 'desc')->get();
        if ($academicSessions->isEmpty()) {
            $academicSessions = AcademicSession::orderBy('id', 'desc')->get();
        }
        $activeSession = AcademicSession::where('status', 'Active')->first() ?? $academicSessions->first();

        $classNames = Student::where('status', 'active')
            ->when($activeSession, fn($q) => $q->where('academic_session_id', $activeSession->id))
            ->whereNotNull('class_name')
            ->distinct()
            ->pluck('class_name');

        if ($classNames->isEmpty()) {
            $classes = StudentClass::where('status', 'active')->orderBy('name')->get();
        } else {
            $classes = StudentClass::whereIn('name', $classNames)->where('status', 'active')->orderBy('name')->get();
        }

        $firstClass = $classes->first();

        $students = Student::where('status', 'active')
            ->when($activeSession, fn($q) => $q->where('academic_session_id', $activeSession->id))
            ->when($firstClass, fn($q) => $q->where('class_name', $firstClass->name))
            ->orderBy('first_name')
            ->get();

        return view('pages.admin.transport.index', compact(
            'routes', 'roster', 'stats', 'drivers', 'availableRoutes', 'students',
            'academicSessions', 'activeSession', 'classes', 'activeTab'
        ));
    }

    public function create()
    {
        $drivers = Staff::where('status', 'active')
            ->where('designation', 'like', '%Driver%')
            ->orderBy('first_name')
            ->get();
        $vehicleTypes = ['Bus', 'Coaster', 'Van', 'Minibus', 'Auto Ricksha', 'Chandi Gari'];
        $vehicleOwnerships = ['School Owned', 'Driver Owned', 'Contract / Leased'];
        return view('pages.admin.transport.create', compact('drivers', 'vehicleTypes', 'vehicleOwnerships'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_code'        => 'nullable|string|max:100|unique:transports,route_code',
            'route_title'       => 'required|string|max:255',
            'vehicle_number'    => 'required|string|max:100',
            'vehicle_model'     => 'nullable|string|max:255',
            'vehicle_type'      => 'required|string|max:50',
            'vehicle_ownership' => 'required|string|max:50',
            'vehicle_capacity'  => 'required|integer|min:1',
            'driver_id'         => 'nullable|exists:staff,id',
            'driver_name'       => 'nullable|string|max:255',
            'driver_contact'    => 'nullable|string|max:100',
            'driver_license'    => 'nullable|string|max:100',
            'fare_amount'       => 'required|numeric|min:0',
            'pickup_stops'      => 'nullable|string',
            'status'            => 'required|string|max:50',
            'note'              => 'nullable|string',
        ]);

        if (!empty($validated['driver_id'])) {
            $staff = Staff::find($validated['driver_id']);
            if ($staff) {
                $validated['driver_name'] = $staff->full_name;
                $validated['driver_contact'] = $staff->mobile_no;
                $validated['driver_license'] = $staff->driving_license_number;
            }
        }

        $validated['created_by'] = Auth::id();

        Transport::create($validated);

        return redirect()->route('transport.index')
            ->with('success', 'Transport Route & Vehicle created successfully!');
    }

    public function show($id)
    {
        $route = Transport::with(['driver', 'allocations.student.studentClass', 'creator'])->findOrFail($id);
        $students = Student::orderBy('first_name')->get();
        return view('pages.admin.transport.show', compact('route', 'students'));
    }

    public function edit($id)
    {
        $route = Transport::findOrFail($id);
        $drivers = Staff::where('status', 'active')
            ->where(function($q) use ($route) {
                $q->where('designation', 'like', '%Driver%');
                if ($route->driver_id) {
                    $q->orWhere('id', $route->driver_id);
                }
            })
            ->orderBy('first_name')
            ->get();
        $vehicleTypes = ['Bus', 'Coaster', 'Van', 'Minibus', 'Auto Ricksha', 'Chandi Gari'];
        $vehicleOwnerships = ['School Owned', 'Driver Owned', 'Contract / Leased'];
        return view('pages.admin.transport.edit', compact('route', 'drivers', 'vehicleTypes', 'vehicleOwnerships'));
    }

    public function update(Request $request, $id)
    {
        $route = Transport::findOrFail($id);

        $validated = $request->validate([
            'route_code'        => 'nullable|string|max:100|unique:transports,route_code,' . $id,
            'route_title'       => 'required|string|max:255',
            'vehicle_number'    => 'required|string|max:100',
            'vehicle_model'     => 'nullable|string|max:255',
            'vehicle_type'      => 'required|string|max:50',
            'vehicle_ownership' => 'required|string|max:50',
            'vehicle_capacity'  => 'required|integer|min:1',
            'driver_id'         => 'nullable|exists:staff,id',
            'driver_name'       => 'nullable|string|max:255',
            'driver_contact'    => 'nullable|string|max:100',
            'driver_license'    => 'nullable|string|max:100',
            'fare_amount'       => 'required|numeric|min:0',
            'pickup_stops'      => 'nullable|string',
            'status'            => 'required|string|max:50',
            'note'              => 'nullable|string',
        ]);

        if (!empty($validated['driver_id'])) {
            $staff = Staff::find($validated['driver_id']);
            if ($staff) {
                $validated['driver_name'] = $staff->full_name;
                $validated['driver_contact'] = $staff->mobile_no;
                $validated['driver_license'] = $staff->driving_license_number;
            }
        }

        $route->update($validated);

        return redirect()->route('transport.show', $route->id)
            ->with('success', 'Transport route details updated successfully!');
    }

    public function destroy($id)
    {
        $route = Transport::findOrFail($id);
        $route->delete();

        return redirect()->route('transport.index')
            ->with('success', 'Transport Route removed successfully!');
    }

    public function assignStudent(Request $request)
    {
        $validated = $request->validate([
            'transport_id' => 'required|exists:transports,id',
            'student_id'   => 'required|exists:students,id',
            'stop_name'    => 'nullable|string|max:255',
            'pickup_time'  => 'nullable|string|max:100',
            'drop_time'    => 'nullable|string|max:100',
            'monthly_fare' => 'nullable|numeric|min:0',
        ]);

        $route = Transport::findOrFail($validated['transport_id']);

        if ($route->available_capacity <= 0) {
            return redirect()->back()->with('error', "Route '{$route->route_title}' is currently at maximum capacity ({$route->vehicle_capacity} seats)!");
        }

        if (empty($validated['monthly_fare'])) {
            $validated['monthly_fare'] = $route->fare_amount;
        }

        $validated['joining_date'] = Carbon::today()->format('Y-m-d');
        $validated['status'] = 'Active';

        TransportStudent::updateOrCreate(
            [
                'transport_id' => $validated['transport_id'],
                'student_id'   => $validated['student_id'],
            ],
            $validated
        );

        return redirect()->back()->with('success', "Student successfully allocated to transport route '{$route->route_title}'!");
    }

    public function removeStudent(Request $request, $allocationId)
    {
        $allocation = TransportStudent::findOrFail($allocationId);
        $allocation->delete();

        return redirect()->back()->with('success', 'Student transport allocation removed successfully!');
    }

    public function addDriverStaff(Request $request)
    {
        $validated = $request->validate([
            'first_name'             => 'required|string|max:100',
            'last_name'              => 'nullable|string|max:100',
            'mobile_no'              => 'required|string|max:50',
            'email'                  => 'required|email|unique:staff,email',
            'driving_license_number' => 'required|string|max:100',
            'driving_license_expiry' => 'nullable|date',
            'cnic'                   => 'nullable|string|max:50',
            'salary'                 => 'nullable|numeric|min:0',
        ]);

        $validated['staff_id']                 = 'DRV-' . rand(100, 999);
        $validated['department']               = 'Transport';
        $validated['designation']              = 'Driver';
        $validated['gender']                   = 'Male';
        $validated['dob']                      = '1990-01-01';
        $validated['cnic']                     = $validated['cnic'] ?: '35202-0000000-1';
        $validated['marital_status']           = 'Single';
        $validated['current_address']          = 'School Staff Quarter';
        $validated['permanent_address']        = 'School Staff Quarter';
        $validated['emergency_contact_name']   = 'Admin';
        $validated['emergency_contact_number'] = $validated['mobile_no'];
        $validated['emergency_contact_relation'] = 'Self';
        $validated['qualification']            = 'HTV / LTV Driving License';
        $validated['employment_type']          = 'full_time';
        $validated['joining_date']             = Carbon::today()->format('Y-m-d');
        $validated['salary']                   = $validated['salary'] ?: 40000.00;
        $validated['status']                   = 'active';

        $driverStaff = Staff::create($validated);

        return redirect()->back()->with('success', "New Driver '{$driverStaff->full_name}' added to Staff directory successfully!");
    }

    // AJAX: Get classes for selected academic session
    public function getClassesBySession($sessionId)
    {
        $query = Student::where('status', 'active');
        if ($sessionId && $sessionId !== 'all') {
            $query->where('academic_session_id', $sessionId);
        }

        $classNames = $query->whereNotNull('class_name')->distinct()->pluck('class_name');

        if ($classNames->isEmpty()) {
            $classes = StudentClass::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        } else {
            $classes = StudentClass::whereIn('name', $classNames)->where('status', 'active')->orderBy('name')->get(['id', 'name']);
        }

        return response()->json([
            'success' => true,
            'classes' => $classes
        ]);
    }

    // AJAX: Get students for selected class name and optional session
    public function getStudentsByClass(Request $request, $className)
    {
        $query = Student::where('status', 'active');

        if ($className !== 'all') {
            $query->where('class_name', $className);
        }

        if ($request->filled('session_id') && $request->session_id !== 'all') {
            $query->where('academic_session_id', $request->session_id);
        }

        $students = $query->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'admission_number', 'roll_no']);

        return response()->json([
            'success'  => true,
            'students' => $students
        ]);
    }
}
