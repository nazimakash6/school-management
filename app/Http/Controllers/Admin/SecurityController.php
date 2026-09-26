<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecuritySetting;
use App\Models\IpBan;
use App\Models\AuditLogs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function index(): View
    {
        $settings = SecuritySetting::all()->pluck('value', 'key');
        $bannedIps = IpBan::with('bannedBy')->orderBy('id', 'desc')->get();
        $recentSecurityLogs = AuditLogs::where('event_type', 'security')->orderBy('id', 'desc')->take(10)->get();
        $adminUsers = User::orderBy('name')->get();

        return view('pages.admin.security.index', compact('settings', 'bannedIps', 'recentSecurityLogs', 'adminUsers'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            '2fa_requirement' => 'required|in:disabled,optional,required',
            'session_timeout' => 'required|numeric|min:5|max:1440',
            'password_min_length' => 'required|numeric|min:6|max:32',
            'password_require_number' => 'nullable|boolean',
            'password_require_symbol' => 'nullable|boolean',
            'password_expiry_days' => 'nullable|numeric|min:0',
            'max_failed_attempts' => 'required|numeric|min:1|max:20',
            'lockout_duration_minutes' => 'required|numeric|min:1|max:1440',
        ]);

        foreach ($validated as $key => $val) {
            SecuritySetting::setByKey($key, is_bool($val) ? ($val ? '1' : '0') : (string)$val);
        }

        AuditLogs::log('security', 'security_settings_updated', 'Updated system security policies and password rules.');

        return redirect()->route('security.index')->with('status', 'Security policies updated successfully.');
    }

    public function banIp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        IpBan::create([
            'ip_address' => $validated['ip_address'],
            'reason' => $validated['reason'] ?? 'Manually blocked by admin',
            'banned_by_user_id' => auth()->id(),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        AuditLogs::log('security', 'ip_banned', "Banned IP address: {$validated['ip_address']}");

        return redirect()->route('security.index')->with('status', "IP {$validated['ip_address']} has been blocked.");
    }

    public function unbanIp(IpBan $ipBan): RedirectResponse
    {
        $ip = $ipBan->ip_address;
        $ipBan->delete();

        AuditLogs::log('security', 'ip_unbanned', "Unblocked IP address: {$ip}");

        return redirect()->route('security.index')->with('status', "IP {$ip} has been unblocked.");
    }
}
