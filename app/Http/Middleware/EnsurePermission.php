<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if (! $user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Forbidden. You do not have permission [{$permission}] to access this action.",
                ], 403);
            }

            abort(403, "You do not have the required permission [{$permission}] to perform this operation.");
        }

        return $next($request);
    }
}
