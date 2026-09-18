<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->user();

        // 🔴 Safety
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        return $next($request);
        // 🟢 ADMIN → SAB SKIP
        if ($user->hasRole('admin')) {
            // \Log::info('Admin user, skipping permission check');
            // console.log('Admin user, skipping permission check');
            return $next($request);
        }

        // 🟡 USER → PERMISSION CHECK
        if (!$user->can($permission)) {
            return response()->json([
                'message' => 'Permission denied',
                'required_permission' => $permission
            ], 403);
        }

        return $next($request);
    }
}
