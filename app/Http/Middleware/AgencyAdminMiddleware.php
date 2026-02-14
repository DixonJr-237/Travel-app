<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgencyAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'agency_admin') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Agency admin access required'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Agency admin access required');
        }

        if (! $user->agency_id) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No agency assigned'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'No agency assigned');
        }

        return $next($request);
    }
}
