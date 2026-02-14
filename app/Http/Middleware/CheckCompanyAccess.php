<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;

class CheckCompanyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = $request->route('company');

        if (!$companyId) {
            $companyId = $request->input('company_id');
        }

        if ($companyId) {
            $company = Company::findOrFail($companyId);

            if (!auth()->user()->can('access-company', $company)) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Unauthorized access to company'], 403);
                }
                return redirect()->route('dashboard')->with('error', 'Unauthorized access to company');
            }
        }

        return $next($request);
    }
}
