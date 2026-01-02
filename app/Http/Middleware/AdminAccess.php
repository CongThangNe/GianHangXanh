<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Allow /admin for admin + staff only.
     * Return 404 for others (customer, etc.).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(404);
        }

        $role = $user->role ?? null;
        if (!in_array($role, ['admin', 'staff'], true)) {
            abort(404);
        }

        return $next($request);
    }
}
