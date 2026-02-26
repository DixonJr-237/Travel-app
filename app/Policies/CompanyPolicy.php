<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Company;

class CompanyPolicy
{
    /**
     * Determine if the user can view the company
     */
    public function view(User $user, Company $company): bool
    {
        // Super admin can view any company
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Company admin can only view their own company
        return $user->isCompanyAdmin() && $user->managedCompany?->id_company === $company->id_company;
    }

    /**
     * Determine if the user can update the company
     */
    public function update(User $user, Company $company): bool
    {
        // Super admin can update any company
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Company admin can only update their own company
        return $user->isCompanyAdmin() && $user->managedCompany?->id_company === $company->id_company;
    }

    /**
     * Determine if user can manage all companies (super admin only)
     */
    public function manageAllCompanies(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
