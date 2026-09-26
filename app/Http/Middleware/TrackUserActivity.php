<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && $request->isMethodSafe() && ! $request->expectsJson()) {
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'activity_type' => 'page_view',
                'description' => 'Visited ' . $request->path(),
                'route' => optional($request->route())->getName(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'properties' => [
                    'url' => $request->fullUrl(),
                ],
            ]);
        }

        return $response;
    }
}