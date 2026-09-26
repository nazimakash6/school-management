<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomSetting;
use App\Models\AuditLogs;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomSettingController extends Controller
{
    public function index(): View
    {
        $allSettings = CustomSetting::all()->keyBy('key');
        return view('pages.admin.custom-settings.index', compact('allSettings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settingsData = $request->input('settings', []);

        foreach ($settingsData as $key => $val) {
            CustomSetting::setByKey($key, $val);
        }

        // Handle Logo Image Upload
        if ($request->hasFile('school_logo')) {
            $request->validate([
                'school_logo' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);
            $logoPath = $request->file('school_logo')->store('logos', 'public');
            CustomSetting::setByKey('school_logo', $logoPath, 'general', 'image', 'Application & School Logo Image Path');
        }

        AuditLogs::log('settings', 'settings_updated', 'Updated custom system settings and branding options.', [
            'keys' => array_keys($settingsData),
            'has_logo' => $request->hasFile('school_logo'),
        ]);

        return redirect()->route('custom-settings.index')->with('status', 'System settings & branding updated successfully.');
    }
}
