<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Allow if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
