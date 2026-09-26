<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolInfo\UpdateSchoolInfoRequest;
use App\Models\SchoolInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolInfoController extends Controller
{
    public function index(): View
    {
        $schoolInfo = SchoolInfo::first();

        if (! $schoolInfo) {
            $schoolInfo = SchoolInfo::create([
                'school_name'       => 'The Academy School',
                'school_code'       => 'TAS-001',
                'tagline'           => 'Excellence in Education',
                'email'             => 'info@academyschool.edu.pk',
                'phone'             => '+92 300 1234567',
                'website'           => 'https://academyschool.edu.pk',
                'address'           => '123 Education Road, Gulberg III',
                'city'              => 'Lahore',
                'state'             => 'Punjab',
                'postal_code'       => '54000',
                'established_year'  => '1998',
                'affiliation_board' => 'BISE Lahore',
                'registration_no'   => 'REG-2026-LHR',
                'principal_name'    => 'Dr. Muhammad Usman',
                'currency_symbol'   => 'Rs.',
            ]);
        }

        return view('pages.admin.school-info.index', compact('schoolInfo'));
    }

    public function updateSchoolInfo(UpdateSchoolInfoRequest $request): RedirectResponse
    {
        $schoolInfo = SchoolInfo::firstOrCreate([], [
            'school_name' => 'The Academy School',
        ]);

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            if ($schoolInfo->logo_path && Storage::disk('public')->exists($schoolInfo->logo_path)) {
                Storage::disk('public')->delete($schoolInfo->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('school_assets', 'public');
        }

        if ($request->hasFile('stamp')) {
            if ($schoolInfo->stamp_path && Storage::disk('public')->exists($schoolInfo->stamp_path)) {
                Storage::disk('public')->delete($schoolInfo->stamp_path);
            }
            $validated['stamp_path'] = $request->file('stamp')->store('school_assets', 'public');
        }

        $schoolInfo->update($validated);

        return redirect()
            ->route('school-info.index')
            ->with('success', 'School information updated successfully!');
    }

    public function update(UpdateSchoolInfoRequest $request, $id): RedirectResponse
    {
        return $this->updateSchoolInfo($request);
    }
}
