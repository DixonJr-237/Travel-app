<?php

namespace App\Policies;

use App\Models\Agence;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgencyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any agencies.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin', 'agency_admin']);
    }

    /**
     * Determine whether the user can view the agency.
     */
    public function view(User $user, Agence $agency): bool
    {
        // Super admin can view any agency
        if ($user->role === 'super_admin') {
            return true;
        }

        // Company admin can view agencies within their company
        if ($user->role === 'company_admin' && $agency->id_company == $user->company_id) {
            return true;
        }

        // Agency admin can only view their own agency
        if ($user->role === 'agency_admin' && $user->agency_id == $agency->id_agence) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create agencies.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }

    /**
     * Determine whether the user can update the agency.
     */
    public function update(User $user, Agence $agency): bool
    {
        // Super admin can update any agency
        if ($user->role === 'super_admin') {
            return true;
        }

        // Company admin can update agencies within their company
        if ($user->role === 'company_admin' && $agency->id_company == $user->company_id) {
            return true;
        }

        // Agency admin can only update their own agency
        if ($user->role === 'agency_admin' && $user->agency_id == $agency->id_agence) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the agency.
     */
    public function delete(User $user, Agence $agency): bool
    {
        // Super admin can delete any agency
        if ($user->role === 'super_admin') {
            return true;
        }

        // Company admin can delete agencies within their company
        if ($user->role === 'company_admin' && $agency->id_company == $user->company_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can activate the agency.
     */
    public function activate(User $user, Agence $agency): bool
    {
        // Only super admin and company admin can activate agencies
        if (!in_array($user->role, ['super_admin', 'company_admin'])) {
            return false;
        }

        // Company admin can only activate agencies within their company
        if ($user->role === 'company_admin' && $agency->id_company != $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can deactivate the agency.
     */
    public function deactivate(User $user, Agence $agency): bool
    {
        // Only super admin and company admin can deactivate agencies
        if (!in_array($user->role, ['super_admin', 'company_admin'])) {
            return false;
        }

        // Company admin can only deactivate agencies within their company
        if ($user->role === 'company_admin' && $agency->id_company != $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can export agency data.
     */
    public function export(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }

    /**
     * Determine whether the user can perform bulk actions.
     */
    public function bulkAction(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }
}
