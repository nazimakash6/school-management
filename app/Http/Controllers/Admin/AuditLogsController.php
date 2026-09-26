<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLogs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogsController extends Controller
{
    public function index(Request $request): View
    {
        $eventType = $request->input('event_type');
        $userId = $request->input('user_id');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = AuditLogs::with('user')->orderBy('id', 'desc');

        if ($eventType && $eventType !== 'all') {
            $query->where('event_type', $eventType);
        }

        if ($userId && $userId !== 'all') {
            $query->where('user_id', $userId);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // Statistics
        $totalLogsToday = AuditLogs::whereDate('created_at', now()->today())->count();
        $authEventsCount = AuditLogs::where('event_type', 'auth')->count();
        $securityEventsCount = AuditLogs::where('event_type', 'security')->count();

        $users = User::orderBy('name')->get();

        return view('pages.admin.audit-logs.index', compact(
            'logs', 'users', 'eventType', 'userId', 'search', 'dateFrom', 'dateTo',
            'totalLogsToday', 'authEventsCount', 'securityEventsCount'
        ));
    }

    public function show($id): View
    {
        $auditLog = AuditLogs::with('user')->findOrFail($id);
        return view('pages.admin.audit-logs.show', compact('auditLog'));
    }

    public function clear(Request $request): RedirectResponse
    {
        $days = $request->input('days', 30);
        $count = AuditLogs::where('created_at', '<', now()->subDays($days))->delete();

        AuditLogs::log('security', 'logs_cleared', "Cleared {$count} audit log entries older than {$days} days.");

        return redirect()->route('audit-logs.index')->with('status', "Successfully cleared {$count} old audit logs.");
    }

    public function export(): StreamedResponse
    {
        $fileName = 'audit_logs_' . date('Y-m-d_H-i') . '.csv';
        $logs = AuditLogs::with('user')->orderBy('id', 'desc')->take(1000)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Timestamp', 'User', 'Event Type', 'Action', 'Description', 'IP Address', 'Route', 'Method']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    optional($log->user)->name ?: 'System',
                    $log->event_type,
                    $log->action,
                    $log->description,
                    $log->ip_address,
                    $log->route,
                    $log->method,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
