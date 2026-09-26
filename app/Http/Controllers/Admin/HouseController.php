<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\House\StoreHouseRequest;
use App\Http\Requests\House\UpdateHouseRequest;
use App\Models\House;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HouseController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 25);
        $search  = trim((string) $request->string('search'));
        $status  = trim((string) $request->string('status', 'all'));

        if (!in_array($perPage, [25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = House::query()
            ->with('master')
            ->withCount('students')
            ->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        $houses = $query->paginate($perPage)->withQueryString();

        $totalHouses = House::count();
        $activeHouses = House::where('status', 'active')->count();

        return view('pages.admin.houses.index', compact(
            'houses',
            'perPage',
            'search',
            'status',
            'totalHouses',
            'activeHouses'
        ));
    }

    public function create(): View
    {
        $staffMembers = Staff::orderBy('first_name')->get();
        $availableStudents = \App\Models\Student::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'admission_no', 'class_name', 'section_name', 'house_name']);

        return view('pages.admin.houses.create', compact('staffMembers', 'availableStudents'));
    }

    public function store(StoreHouseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $studentIds = $data['student_ids'] ?? [];
        $house = House::create($data);

        if (!empty($studentIds)) {
            \App\Models\Student::whereIn('id', $studentIds)->update(['house_name' => $house->name]);
        }

        return redirect()
            ->route('houses.index')
            ->with('status', "House '{$house->name}' created successfully.");
    }

    public function show(House $house): View
    {
        $house->load('master')->loadCount('students');
        $students = $house->students()->paginate(15);

        return view('pages.admin.houses.show', compact('house', 'students'));
    }

    public function edit(House $house): View
    {
        $staffMembers = Staff::orderBy('first_name')->get();
        $availableStudents = \App\Models\Student::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'admission_no', 'class_name', 'section_name', 'house_name']);

        return view('pages.admin.houses.edit', compact('house', 'staffMembers', 'availableStudents'));
    }

    public function update(UpdateHouseRequest $request, House $house): RedirectResponse
    {
        $oldHouseName = $house->name;
        $data = $request->validated();
        $studentIds = $data['student_ids'] ?? [];
        $house->update($data);

        // Reset house_name for students previously assigned to this house
        \App\Models\Student::where('house_name', $oldHouseName)->update(['house_name' => null]);

        // Assign selected students to updated house
        if (!empty($studentIds)) {
            \App\Models\Student::whereIn('id', $studentIds)->update(['house_name' => $house->name]);
        }

        return redirect()
            ->route('houses.index')
            ->with('status', "House '{$house->name}' updated successfully.");
    }

    public function destroy(House $house): RedirectResponse
    {
        $houseName = $house->name;
        $house->delete();

        return redirect()
            ->route('houses.index')
            ->with('status', "House '{$houseName}' deleted successfully.");
    }
}
