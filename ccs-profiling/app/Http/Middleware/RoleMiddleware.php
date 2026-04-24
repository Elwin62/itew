<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: route()->middleware('role:Admin,Faculty') — allows Admin and Faculty only.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        if (!in_array($userRole, $roles)) {
            // Redirect to their appropriate dashboard with an error
            return redirect()->route('dashboard')
                ->with('error', "Access denied. Your role ({$userRole}) is not authorized to view that page.");
        }

        return $next($request);
    }
}
