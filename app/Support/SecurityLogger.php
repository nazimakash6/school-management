<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;

class SecurityLogger
{
    public static function logAudit(?User $user, string $eventType, string $action, string $description, Request $request, array $context = []): void
    {
        AuditLog::create([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'action' => $action,
            'description' => $description,
            'route' => optional($request->route())->getName(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => $context,
        ]);
    }

    public static function logActivity(?User $user, string $activityType, string $description, Request $request, array $properties = []): void
    {
        ActivityLog::create([
            'user_id' => $user?->id,
            'activity_type' => $activityType,
            'description' => $description,
            'route' => optional($request->route())->getName(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'properties' => $properties,
        ]);
    }

    public static function recordLogin(User $user, Request $request): void
    {
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        LoginHistory::create([
            'user_id' => $user->id,
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'logged_in_at' => now(),
            'status' => 'active',
        ]);

        self::logAudit($user, 'auth', 'login', 'User logged in successfully.', $request, [
            'session_id' => $request->session()->getId(),
        ]);

        self::logActivity($user, 'login', 'Logged into the application.', $request, [
            'session_id' => $request->session()->getId(),
        ]);
    }

    public static function recordLogout(?User $user, Request $request): void
    {
        if (! $user) {
            return;
        }

        LoginHistory::query()
            ->where('user_id', $user->id)
            ->where('session_id', $request->session()->getId())
            ->whereNull('logged_out_at')
            ->latest('id')
            ->first()?->update([
                'logged_out_at' => now(),
                'status' => 'logged_out',
            ]);

        self::logAudit($user, 'auth', 'logout', 'User logged out.', $request, [
            'session_id' => $request->session()->getId(),
        ]);

        self::logActivity($user, 'logout', 'Logged out of the application.', $request, [
            'session_id' => $request->session()->getId(),
        ]);
    }
}