<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Agency;

class CheckAgencyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $agencyId = $request->route('agency');

        if (!$agencyId) {
            $agencyId = $request->input('agency_id');
        }

        if ($agencyId) {
            $agency = Agency::findOrFail($agencyId);

            if (!auth()->user()->can('access-agency', $agency)) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthorized access to agency'], 403);
                }
                return redirect()->route('dashboard')->with('error', 'Unauthorized access to agency');
            }
        }

        return $next($request);
    }
}
