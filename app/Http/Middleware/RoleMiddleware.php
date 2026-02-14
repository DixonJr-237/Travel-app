<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Debug logging (remove in production)
        Log::info('Role check', [
            'user_id' => $user->id,
            'required_roles' => $roles,
            'user_role' => $user->role ?? 'not set'
        ]);

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            // Handle pipe-separated roles (e.g., 'super_admin|company_admin')
            if (str_contains($role, '|')) {
                $pipeRoles = explode('|', $role);
                foreach ($pipeRoles as $pipeRole) {
                    if ($this->userHasRole($user, trim($pipeRole))) {
                        return $next($request);
                    }
                }
            }
            // Single role
            else {
                if ($this->userHasRole($user, $role)) {
                    return $next($request);
                }
            }
        }

        // No matching role found - log and abort
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role ?? 'not set',
            'required_roles' => $roles,
            'url' => $request->fullUrl()
        ]);

        // Redirect or abort based on request type
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized. Insufficient permissions.'], 403);
        }

        abort(403, 'You do not have permission to access this page.');
    }

    /**
     * Check if user has a specific role
     *
     * @param  \App\Models\User  $user
     * @param  string  $role
     * @return bool
     */
    private function userHasRole($user, string $role): bool
    {
        // Method 1: If using a role column directly on users table
        if (isset($user->role) && $user->role === $role) {
            return true;
        }

        // Method 2: If using Spatie Permission package (uncomment if needed)
        // if (method_exists($user, 'hasRole')) {
        //     return $user->hasRole($role);
        // }

        // Method 3: If using a roles relationship
        // if (method_exists($user, 'roles')) {
        //     return $user->roles()->where('name', $role)->exists();
        // }

        return false;
    }
}
