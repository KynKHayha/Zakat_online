<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMuzakki
{
    /**
     * Handle an incoming request.
     * Hanya boleh diakses oleh user dengan role 'muzakki'
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isMuzakki()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            // Jika admin, redirect ke dashboard admin
            if (auth()->check() && auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
