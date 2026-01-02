<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Chỉ cho phép role admin.
     * Trả 404 cho staff/customer để đúng yêu cầu.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(404);
        }

        if (($user->role ?? null) !== 'admin') {
            abort(404);
        }

        return $next($request);
    }
}
