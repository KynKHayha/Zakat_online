<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     * Hanya boleh diakses oleh user dengan role 'admin'
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
            }
            abort(403, 'Akses ditolak. Anda tidak memiliki hak akses admin.');
        }

        return $next($request);
    }
}
