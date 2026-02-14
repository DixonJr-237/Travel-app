<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies (Super Admin only).
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get companies based on role
        if ($user->role === 'super_admin') {
            // Super admin sees all companies
            $companies = Company::orderBy('created_at', 'desc')->paginate(10);
        } elseif ($user->role === 'company_admin' && !empty($user->company_id)) {
            // Company admin only sees their own company
            $companies = Company::where('id_company', $user->company_id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Others see empty result
            $companies = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            return view('companies.index', compact('companies'));
        }

        // Manually add counts to each company
        foreach ($companies as $company) {
            // Count agencies for this company
            $company->agencies_count = Agence::where('id_company', $company->id_company)->count();

            // Get all agency IDs for this company
            $agencyIds = Agence::where('id_company', $company->id_company)->pluck('id_agence');

            // Count users that belong to these agencies
            // Since users don't have agency_id, we need to count users who are either:
            // 1. Agency admins (users with user_id in agences table)
            // 2. Regular users (if you have a separate table for user-agency assignments)

            // Count agency admins (users who are admins of agencies in this company)
            $agencyAdminIds = Agence::where('id_company', $company->id_company)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            // If you have a user_agence table for regular users (many-to-many relationship)
            // Uncomment and adjust this based on your actual structure
            /*
            $regularUserIds = DB::table('user_agence')
                ->whereIn('agence_id', $agencyIds)
                ->pluck('user_id');

            // Combine all user IDs and get unique count
            $allUserIds = $agencyAdminIds->merge($regularUserIds)->unique();
            $company->users_count = $allUserIds->count();
            */

            // For now, if you only have agency admins (users in agences table)
            $company->users_count = $agencyAdminIds->count();
        }

        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
{
    $user = auth()->user();

    // Check if user can create a company
    if ($user->hasRole('company_admin') && $user->company_id) {
        return redirect()->route('my-company.dashboard')
            ->with('error', 'You already have a company.');
    }

    return view('companies.create', [
        'isSuperAdmin' => $user->hasRole('super_admin'),
    ]);
}

    /**
     * Store a newly created company.
     */
    public function store(Request $request)
{
    // Determine if this is a super admin creating a company
    $isSuperAdmin = auth()->user()->hasRole('super_admin');

    // Base validation rules for company
    $rules = [
        'name' => 'required|string|max:255|unique:companies,name',
        'email' => 'required|string|email|max:255|unique:companies,email',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:500',
        'registration_number' => 'nullable|string|max:100|unique:companies,registration_number',
        'tax_id' => 'nullable|string|max:100|unique:companies,tax_id',
        'website' => 'nullable|url|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    // Add admin validation rules for super admin
    if ($isSuperAdmin) {
        $rules = array_merge($rules, [
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'admin_password_confirmation' => 'required|string|min:8',
            'admin_phone' => 'nullable|string|max:20',
        ]);
    }

    // Validate the request
    $validated = $request->validate($rules);

    try {
        DB::transaction(function () use ($request, $isSuperAdmin) {

            if ($isSuperAdmin) {
                // SUPER ADMIN: Create company with new admin user

                // Create company admin user
                $adminUser = User::create([
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'password' => Hash::make($request->admin_password),
                    'role' => 'company_admin',
                    'phone' => $request->admin_phone ?? $request->phone,
                    'email_verified_at' => now(), // Auto-verify admin accounts
                    'status' => 'active',
                ]);

                // Create company with user_id reference
                $company = Company::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'registration_number' => $request->registration_number,
                    'tax_id' => $request->tax_id,
                    'website' => $request->website,
                    'description' => $request->description,
                    'user_id' => $adminUser->user_id, // Reference to admin user
                    'status' => 'active',
                ]);

                // Update user with company_id
                $adminUser->update(['company_id' => $company->id_company]);

                // Assign role using Spatie if available
                if (class_exists('Spatie\Permission\Models\Role')) {
                    $adminUser->assignRole('company_admin');
                }

                // Send welcome email to new admin
                try {
                    Mail::to($adminUser->email)->send(new CompanyAdminWelcomeMail($company, $adminUser, $request->admin_password));
                } catch (\Exception $e) {
                    // Log email error but don't break the transaction
                    Log::warning('Failed to send welcome email to company admin: ' . $e->getMessage());
                }

            } else {
                // COMPANY ADMIN: Create their own company (current user becomes admin)

                // Get current authenticated user
                $currentUser = auth()->user();

                // Check if user already has a company
                if ($currentUser->company_id) {
                    throw new \Exception('You already belong to a company and cannot create a new one.');
                }

                // Create company with current user as admin
                $company = Company::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'registration_number' => $request->registration_number,
                    'tax_id' => $request->tax_id,
                    'website' => $request->website,
                    'description' => $request->description,
                    'user_id' => $currentUser->user_id, // Reference to current user
                    'status' => 'pending', // Maybe require super admin approval
                ]);

                // Update current user with company_id and role
                $currentUser->update([
                    'company_id' => $company->id_company,
                    'role' => 'company_admin',
                ]);

                // Assign role using Spatie if available
                if (class_exists('Spatie\Permission\Models\Role')) {
                    $currentUser->assignRole('company_admin');
                }

                // Notify super admins about new company registration
                $this->notifySuperAdmins($company, $currentUser);
            }

            // Log the activity
            Activity::log([
                'action' => 'company_created',
                'company_id' => $company->id_company,
                'user_id' => auth()->id(),
                'details' => $isSuperAdmin ? 'Company created with new admin' : 'Company registered by company admin',
            ]);
        });

        // Redirect based on user role
        if ($isSuperAdmin) {
            return redirect()->route('admin.companies.index')
                ->with('success', 'Company created successfully. Admin user has been created and notified.');
        } else {
            return redirect()->route('my-company.dashboard')
                ->with('success', 'Your company has been created successfully. It will be reviewed by our administrators.');
        }

    } catch (\Exception $e) {
        // Handle transaction failure
        Log::error('Company creation failed: ' . $e->getMessage());

        return back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create company. Please try again. Error: ' . $e->getMessage()]);
    }
}

/**
 * Notify super admins about new company registration
 */
private function notifySuperAdmins($company, $user)
{
    $superAdmins = User::where('role', 'super_admin')->get();

    foreach ($superAdmins as $admin) {
        // Create notification in database
        $admin->notifications()->create([
            'type' => 'new_company_registration',
            'data' => [
                'company_id' => $company->id_company,
                'company_name' => $company->name,
                'user_id' => $user->user_id,
                'user_name' => $user->name,
                'message' => "New company '{$company->name}' has been registered and requires approval.",
            ],
            'url' => route('admin.companies.show', $company->id_company),
        ]);

        // Send email if configured
        try {
            Mail::to($admin->email)->send(new NewCompanyNotificationMail($company, $user));
        } catch (\Exception $e) {
            Log::warning('Failed to send new company notification to super admin: ' . $e->getMessage());
        }
    }
}
    /**
     * Display company details.
     */
    public function show(Company $company)
    {
        $company->load(['user', 'agencies.buses', 'agencies.coordinates.city']);

        // Get stats
        $stats = [
            'total_agencies' => $company->agencies->count(),
            'total_buses' => $company->agencies->sum(function ($agency) {
                return $agency->buses->count();
            }),
            'active_buses' => $company->agencies->sum(function ($agency) {
                return $agency->buses->where('status', 'active')->count();
            }),
            'total_trips' => $company->agencies->sum(function ($agency) {
                return $agency->buses->sum(function ($bus) {
                    return $bus->trips->count();
                });
            }),
            'total_users' => $company->users()->count(), // Add this if you want
        ];

        return view('admin.companies.show', compact('company', 'stats'));
    }

    /**
     * Show the form for editing a company.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the company.
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id_company . ',id_company',
            'email' => 'required|string|email|max:255|unique:companies,email,' . $company->id_company . ',id_company',
            'phone' => 'required|string|max:20',
        ]);

        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the company.
     */
    public function destroy(Company $company)
    {
        // Check if company has agencies
        if ($company->agencies()->exists()) {
            return redirect()->route('companies.index')
                ->with('error', 'Cannot delete company with existing agencies. Delete agencies first.');
        }

        // Delete company admin user
        if ($company->user) {
            $company->user->delete();
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    /**
     * Display company admin's own company.
     */
    public function myCompany()
    {
        $user = auth()->user();

        if (!$user->company_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any company.');
        }

        $company = Company::with(['user', 'agencies.buses', 'agencies.coordinates.city'])
            ->findOrFail($user->company_id);

        // Get stats
        $stats = [
            'total_agencies' => $company->agencies->count(),
            'total_buses' => $company->agencies->sum(function ($agency) {
                return $agency->buses->count();
            }),
            'active_buses' => $company->agencies->sum(function ($agency) {
                return $agency->buses->where('status', 'active')->count();
            }),
            'total_trips' => $company->agencies->sum(function ($agency) {
                return $agency->buses->sum(function ($bus) {
                    return $bus->trips->count();
                });
            }),
        ];

        return view('companies.show', compact('company', 'stats'));
    }

    /**
     * Show edit form for company admin's company.
     */
    public function editMyCompany()
    {
        $user = auth()->user();

        if (!$user->company_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any company.');
        }

        $company = Company::findOrFail($user->company_id);

        return view('companies.edit', compact('company'));
    }

    /**
     * Update company admin's company.
     */
    public function updateMyCompany(Request $request)
    {
        $user = auth()->user();

        if (!$user->company_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any company.');
        }

        $company = Company::findOrFail($user->company_id);

        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id_company . ',id_company',
            'email' => 'required|string|email|max:255|unique:companies,email,' . $company->id_company . ',id_company',
            'phone' => 'required|string|max:20',
        ]);

        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('my-company.dashboard')
            ->with('success', 'Company updated successfully.');
    }

    /**
     * List company's agencies.
     */
    public function myAgencies()
    {
        $user = auth()->user();

        if (!$user->company_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any company.');
        }

        $agencies = Agence::with(['coordinates.city', 'user'])
            ->where('id_company', $user->company_id)
            ->withCount(['buses', 'users'])
            ->paginate(10);

        return view('agencies.index', compact('agencies'));
    }

    /**
     * Show company reports.
     */
    public function myReports()
    {
        $user = auth()->user();

        if (!$user->company_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any company.');
        }

        $company = Company::with(['agencies.buses.trips', 'agencies.tickets'])
            ->findOrFail($user->company_id);

        // Calculate stats
        $totalRevenue = $company->agencies->sum(function ($agency) {
            return $agency->tickets->sum('price');
        });

        $totalTrips = $company->agencies->sum(function ($agency) {
            return $agency->buses->sum(function ($bus) {
                return $bus->trips->count();
            });
        });

        $totalTickets = $company->agencies->sum(function ($agency) {
            return $agency->tickets->count();
        });

        return view('companies.reports', compact('company', 'totalRevenue', 'totalTrips', 'totalTickets'));
    }

    /**
     * List company users.
     */
    public function myUsers()
    {
        $user = auth()->user();

        if (!$user->company_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any company.');
        }

        $users = User::where('company_id', $user->company_id)
            ->orWhereHas('agence', function ($query) use ($user) {
                $query->where('id_company', $user->company_id);
            })
            ->with(['agence'])
            ->paginate(10);

        return view('companies.users', compact('users'));
    }

    /**
     * Show public company info.
     */
    public function publicShow(Company $company)
    {
        $company->load(['agencies.coordinates.city', 'agencies.buses']);

        return view('companies.public', compact('company'));
    }


}
