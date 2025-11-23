<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return abort(403, 'Unauthorized. Please login.');
        }

        $user = $request->user();

        // ✅ NEW LOGIC: hasRole() ফাংশন ব্যবহার করে চেক করা
        if (!$user->hasRole($role)) {
            return abort(403, 'Unauthorized action. You do not have the required role: ' . $role);
        }

        return $next($request);
    }
}