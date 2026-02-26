<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkAgencyActionRequest;
use App\Models\Agence;
use App\Models\Bus;
use App\Models\City;
use App\Models\Company;
use App\Models\Coordinate;
use App\Models\Country;
use App\Models\Region;
use App\Models\SubRegion;
use App\Models\User;
use App\Models\Tips; // Add this for trips
use App\Services\AgencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use App\Mail\AgencyAdminWelcomeMail;
use App\Mail\NewAgencyNotificationMail;

class AgencyController extends Controller
{
    protected $agencyService;

    public function __construct(AgencyService $agencyService)
    {
        $this->agencyService = $agencyService;
    }

    /**
     * Check if a view exists.
     */
    private function viewExists($view)
    {
        return View::exists($view);
    }

    /**
     * Get the appropriate view based on role with fallback.
     */
    private function getViewForRole($baseView, $isSuperAdmin)
    {
        if ($isSuperAdmin) {
            $adminView = "admin.agencies.{$baseView}";
            // Return admin view if it exists, otherwise fall back to regular view
            return $this->viewExists($adminView) ? $adminView : "agencies.{$baseView}";
        }

        return "agencies.{$baseView}";
    }

    /**
     * Display a listing of agencies.
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('super_admin');
            $isCompanyAdmin = $user->hasRole('company_admin');

            // Get the company IDs for the current user if they are a company admin
            $userCompanyIds = [];
            if ($isCompanyAdmin) {
                $userCompanyIds = Company::where('user_id', $user->user_id)
                    ->orWhere('id_company', $user->company_id)
                    ->pluck('id_company')
                    ->toArray();
            }

            // Build the base query
            $query = Agence::query();

            // Apply role-based filtering
            if ($isCompanyAdmin) {
                // Company admin: only see agencies from their companies
                $query->whereIn('id_company', $userCompanyIds);
            }

            // Apply request filters
            if ($request->has('company_id') && $request->company_id) {
                if ($isSuperAdmin) {
                    if ($request->company_id === 'none') {
                        $query->whereNull('id_company');
                    } else {
                        $query->where('id_company', $request->company_id);
                    }
                } elseif ($isCompanyAdmin && in_array($request->company_id, $userCompanyIds)) {
                    $query->where('id_company', $request->company_id);
                }
            }

            if ($request->has('city_id') && $request->city_id) {
                $query->where('id_city', $request->city_id);
            }

            // REMOVED: Status filter since agencies table doesn't have status column
            // if ($request->has('status') && $request->status) {
            //     $query->where('status', $request->status);
            // }

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
                });
            }

            // Eager load relationships with proper foreign keys - REMOVED status from company query
            $query->with([
                'company' => function($q) {
                    $q->select('id_company', 'name', 'email', 'phone', 'user_id'); // Removed 'status'
                },
                'coordinates' => function($q) {
                    $q->select('id_coord', 'address', 'latitude', 'longitude', 'id_city');
                },
                'coordinates.city' => function($q) {
                    $q->select('id_city', 'name', 'id_sub_region');
                },
                'user' => function($q) {
                    $q->select('user_id', 'name', 'email', 'phone', 'role');
                }
            ]);

            // Get agencies with bus count
            $agencies = $query->withCount('buses')
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();

            // Add computed properties
            foreach ($agencies as $agency) {
                $agency->agency_admin_count = $agency->user ? 1 : 0;

                // Get company admin info - FIXED: Find the user directly instead of relying on relationship
                $companyAdmin = null;
                if ($agency->company && $agency->company->user_id) {
                    $companyAdmin = User::find($agency->company->user_id);
                }
                $agency->company_admin_name = $companyAdmin ? $companyAdmin->name : null;
                $agency->company_admin_email = $companyAdmin ? $companyAdmin->email : null;

                if ($isCompanyAdmin) {
                    $agency->belongs_to_my_company = in_array($agency->id_company, $userCompanyIds);
                }
            }

            // Get filter data (role-based) - REMOVED status from company query
            if ($isSuperAdmin) {
                $companies = Company::select('id_company', 'name', 'user_id') // Removed 'status'
                    ->orderBy('name')
                    ->get();
            } else {
                $companies = Company::whereIn('id_company', $userCompanyIds)
                    ->select('id_company', 'name', 'user_id') // Removed 'status'
                    ->orderBy('name')
                    ->get();
            }

            // Get cities for filter
            $citiesQuery = City::select('id_city', 'name');

            if ($isCompanyAdmin && !empty($userCompanyIds)) {
                $cityIds = DB::table('agencies')
                    ->whereIn('id_company', $userCompanyIds)
                    ->whereNotNull('id_city')
                    ->distinct()
                    ->pluck('id_city');

                $citiesQuery->whereIn('id_city', $cityIds);
            }

            $cities = $citiesQuery->orderBy('name')->get();

            // REMOVED: Status options since agencies table doesn't have status column
            // $statusOptions = [
            //     'active' => 'Active',
            //     'pending' => 'Pending',
            //     'suspended' => 'Suspended',
            //     'inactive' => 'Inactive'
            // ];
            $statusOptions = []; // Empty array since we don't have status

            // Calculate stats - FIXED: Removed status-based queries
            $stats = $this->calculateStats($isSuperAdmin, $isCompanyAdmin, $userCompanyIds);

            // Determine view
            $view = $this->getViewForRole('index', $isSuperAdmin);

            return view($view, compact(
                'agencies',
                'companies',
                'cities',
                'statusOptions',
                'stats',
                'isSuperAdmin',
                'isCompanyAdmin',
                'userCompanyIds'
            ));

        } catch (\Exception $e) {
            Log::error('Error in AgencyController@index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id() ?? null,
                'url' => $request->fullUrl()
            ]);

            $user = auth()->user();

            return view('agencies.index', [
                'agencies' => collect([]),
                'companies' => collect([]),
                'cities' => collect([]),
                'statusOptions' => [],
                'stats' => [
                    'total_agencies' => 0,
                    'active_agencies' => 0,
                    'pending_agencies' => 0,
                    'total_buses' => 0,
                ],
                'isSuperAdmin' => $user->role === 'super_admin',
                'isCompanyAdmin' => $user->role === 'company_admin',
                'userCompanyIds' => [],
                'error' => 'An error occurred while loading agencies. Please try again.'
            ]);
        }
    }

    /**
     * Calculate statistics based on user role.
     */
    private function calculateStats($isSuperAdmin, $isCompanyAdmin, $userCompanyIds)
    {
        try {
            $stats = [];

            if ($isSuperAdmin) {
                // Super admin sees global stats - REMOVED status-based queries
                $stats = [
                    'total_agencies' => Agence::count(),
                    // 'active_agencies' => Agence::where('status', 'active')->count(), // REMOVED
                    // 'pending_agencies' => Agence::where('status', 'pending')->count(), // REMOVED
                    'total_buses' => Bus::count(),
                    'total_companies' => Company::count(),
                    'agencies_without_company' => Agence::whereNull('id_company')->count(),
                ];
            } elseif ($isCompanyAdmin && !empty($userCompanyIds)) {
                // Company admin sees stats for their companies only - REMOVED status-based queries
                $stats = [
                    'total_agencies' => Agence::whereIn('id_company', $userCompanyIds)->count(),
                    // 'active_agencies' => Agence::whereIn('id_company', $userCompanyIds)->where('status', 'active')->count(), // REMOVED
                    // 'pending_agencies' => Agence::whereIn('id_company', $userCompanyIds)->where('status', 'pending')->count(), // REMOVED
                    'total_buses' => Bus::whereIn('company_id', $userCompanyIds)->count(),
                    'my_companies' => count($userCompanyIds),
                ];
            } else {
                // Default empty stats
                $stats = [
                    'total_agencies' => 0,
                    // 'active_agencies' => 0, // REMOVED
                    // 'pending_agencies' => 0, // REMOVED
                    'total_buses' => 0,
                ];
            }

            return $stats;

        } catch (\Exception $e) {
            Log::warning('Error calculating stats: ' . $e->getMessage());
            return [
                'total_agencies' => 0,
                'total_buses' => 0,
                'total_companies' => 0,
                'agencies_without_company' => 0,
                'my_companies' => 0,
            ];
        }
    }

    /**
     * Show the form for creating a new agency.
     */
    public function create(Request $request)
    {
        try {
            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('super_admin');
            $isCompanyAdmin = $user->hasRole('company_admin');

            // Get available companies (role-based)
            $companies = $isSuperAdmin
                ? Company::select('id_company', 'name')->orderBy('name')->get()
                : Company::where('id_company', $user->company_id)->select('id_company', 'name')->orderBy('name')->get();

            // Check if company admin already has an agency
            if ($isCompanyAdmin && $user->agency_id) {
                return redirect()->route('my-agency.dashboard')
                    ->with('error', 'You already have an agency. You cannot create another one.');
            }

            // Get location data
            $countries = Country::select('id_country', 'name')->orderBy('name')->get();
            $regions = Region::select('id_region', 'name')->orderBy('name')->get();
            $subRegions = SubRegion::select('id_sub_region', 'name')->orderBy('name')->get();
            $cities = City::with('subRegion.region.country')->select('id_city', 'name', 'id_sub_region')->orderBy('name')->get();

            // Get coordinates for selected city
            $coordinates = collect();
            if ($request->has('city_id') && $request->city_id) {
                $coordinates = Coordinate::where('id_city', $request->city_id)
                    ->select('id_coord', 'address', 'latitude', 'longitude')
                    ->get();
            }

            // Determine which view to return with fallback
            $view = $this->getViewForRole('create', $isSuperAdmin);

            return view($view, compact(
                'companies',
                'countries',
                'regions',
                'subRegions',
                'cities',
                'coordinates',
                'isSuperAdmin'
            ));

        } catch (\Exception $e) {
            Log::error('Error in AgencyController@create: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Unable to load create agency form. Please try again.');
        }
    }

    /**
     * Store a newly created agency.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin');
        $isCompanyAdmin = $user->hasRole('company_admin');

        // Validation rules - REMOVED status validation
        $rules = [
            'name' => 'required|string|max:255|unique:agencies,name',
            'email' => 'required|string|email|max:255|unique:agencies,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'id_country' => 'required|exists:countries,id_country',
            'id_region' => 'required|exists:regions,id_region',
            'id_sub_region' => 'required|exists:sub_regions,id_sub_region',
            'id_city' => 'required|exists:cities,id_city',
            'id_coord' => 'nullable|exists:coordinates,id_coord',
        ];

        // Company selection (super admin only)
        if ($isSuperAdmin) {
            $rules['id_company'] = 'required|exists:companies,id_company';
        }

        // Admin user creation (super admin only)
        if ($isSuperAdmin) {
            $rules = array_merge($rules, [
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|string|email|max:255|unique:users,email',
                'admin_password' => 'required|string|min:8|confirmed',
                'admin_password_confirmation' => 'required|string|min:8',
                'admin_phone' => 'nullable|string|max:20',
            ]);
        }

        $validated = $request->validate($rules);

        try {
            DB::transaction(function () use ($validated, $user, $isSuperAdmin, $isCompanyAdmin) {

                if ($isSuperAdmin) {
                    // SUPER ADMIN: Create agency with new admin user

                    // Create agency admin user
                    $adminUser = User::create([
                        'name' => $validated['admin_name'],
                        'email' => $validated['admin_email'],
                        'password' => Hash::make($validated['admin_password']),
                        'role' => 'agency_admin',
                        'phone' => $validated['admin_phone'] ?? $validated['phone'],
                        'email_verified_at' => now(),
                        // 'status' => 'active', // REMOVED - users table may not have status
                    ]);

                    // Create agency - REMOVED status field
                    $agency = Agence::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'address' => $validated['address'],
                        'id_company' => $validated['id_company'],
                        'id_city' => $validated['id_city'],
                        'id_coord' => $validated['id_coord'] ?? null,
                        'user_id' => $adminUser->user_id,
                        // 'status' => 'active', // REMOVED
                    ]);

                    // Update user with agency_id
                    $adminUser->update(['agency_id' => $agency->id_agence]);

                    // Assign role using Spatie if available
                    if (class_exists('Spatie\Permission\Models\Role')) {
                        $adminUser->assignRole('agency_admin');
                    }

                    // Send welcome email
                    try {
                        Mail::to($adminUser->email)->send(new AgencyAdminWelcomeMail($agency, $adminUser, $validated['admin_password']));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send welcome email to agency admin: ' . $e->getMessage());
                    }

                } elseif ($isCompanyAdmin) {
                    // COMPANY ADMIN: Create agency under their company

                    // Check if user already has an agency
                    if ($user->agency_id) {
                        throw new \Exception('You already have an agency.');
                    }

                    // Create agency with current user as admin - REMOVED status field
                    $agency = Agence::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'address' => $validated['address'],
                        'id_company' => $user->company_id,
                        'id_city' => $validated['id_city'],
                        'id_coord' => $validated['id_coord'] ?? null,
                        'user_id' => $user->user_id,
                        // 'status' => 'pending', // REMOVED - Requires super admin approval
                    ]);

                    // Update user with agency_id and role
                    $user->update([
                        'agency_id' => $agency->id_agence,
                        'role' => 'agency_admin',
                    ]);

                    // Assign role using Spatie if available
                    if (class_exists('Spatie\Permission\Models\Role')) {
                        $user->assignRole('agency_admin');
                    }

                    // Notify super admins
                    $this->notifySuperAdmins($agency, $user);
                }
            });

            // Redirect based on user role
            if ($isSuperAdmin) {
                return redirect()->route('admin.agencies.index')
                    ->with('success', 'Agency created successfully. Admin user has been created and notified.');
            } else {
                return redirect()->route('my-agency.dashboard')
                    ->with('success', 'Your agency has been created successfully. It will be reviewed by administrators.');
            }

        } catch (\Exception $e) {
            Log::error('Agency creation failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create agency. Please try again. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Display agency details.
     */
    public function show(Agence $agency)
    {
        try {
            $this->authorize('view', $agency);

            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('super_admin');

            $agency->load([
                'company',
                'coordinates.city.subRegion.region.country',
                'user',
                'buses'
            ]);

            // Get stats using service
            $stats = $this->agencyService->getAgencyStats($agency);

            // Determine which view to return with fallback
            $view = $this->getViewForRole('show', $isSuperAdmin);

            return view('agencies.show', compact('agency', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error showing agency: ' . $e->getMessage());
            return redirect()->route('admin.agencies.index')
                ->with('error', 'Agency not found or unable to load details.');
        }
    }

    /**
     * Show the form for editing an agency.
     */
    public function edit(Agence $agency)
    {
        try {
            $this->authorize('update', $agency);

            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('super_admin');

            $agency->load(['coordinates.city']);

            // Get companies (role-based)
            $companies = $isSuperAdmin
                ? Company::select('id_company', 'name')->orderBy('name')->get()
                : Company::where('id_company', $user->company_id)->select('id_company', 'name')->orderBy('name')->get();

            $cities = City::with('subRegion.region.country')->select('id_city', 'name', 'id_sub_region')->orderBy('name')->get();
            $coordinates = Coordinate::where('id_city', $agency->id_city)
                ->select('id_coord', 'address', 'latitude', 'longitude')
                ->get();

            // Determine which view to return with fallback
            $view = $this->getViewForRole('edit', $isSuperAdmin);

            return view($view, compact('agency', 'companies', 'cities', 'coordinates', 'isSuperAdmin'));

        } catch (\Exception $e) {
            Log::error('Error editing agency: ' . $e->getMessage());
            return redirect()->route('admin.agencies.index')
                ->with('error', 'Unable to load edit form.');
        }
    }

    /**
     * Update the agency.
     */
    public function update(Request $request, Agence $agency)
    {
        try {
            $this->authorize('update', $agency);

            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('super_admin');

            $rules = [
                'name' => 'required|string|max:255|unique:agencies,name,' . $agency->id_agence . ',id_agence',
                'email' => 'required|string|email|max:255|unique:agencies,email,' . $agency->id_agence . ',id_agence',
                'phone' => 'required|string|max:20',
                'id_city' => 'required|exists:cities,id_city',
                'id_coord' => 'nullable|exists:coordinates,id_coord',
            ];

            // Only super admin can change company assignment
            if ($isSuperAdmin) {
                $rules['id_company'] = 'required|exists:companies,id_company';
            }

            $validated = $request->validate($rules);

            $this->agencyService->updateAgency($agency, $validated);

            // Redirect based on role
            $redirectRoute = $isSuperAdmin
                ? route('admin.agencies.show', $agency)
                : route('my-agency.dashboard');

            return redirect($redirectRoute)
                ->with('success', 'Agency updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating agency: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update agency: ' . $e->getMessage());
        }
    }

    /**
     * Remove the agency.
     */
    public function destroy(Agence $agency)
    {
        try {
            $this->authorize('delete', $agency);

            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('super_admin');

            // Check if agency has buses
            if ($agency->buses()->count() > 0) {
                return back()->with('error', 'Cannot delete agency with associated buses.');
            }

            // Check if agency has trips
            $busIds = $agency->buses()->pluck('bus_id');
            if (Tips::whereIn('id_bus', $busIds)->exists()) {
                return back()->with('error', 'Cannot delete agency with associated trips.');
            }

            $agency->delete();

            $redirectRoute = $isSuperAdmin
                ? route('admin.agencies.index')
                : route('my-agency.dashboard');

            return redirect($redirectRoute)
                ->with('success', 'Agency deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting agency: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete agency: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions on agencies.
     */
    public function bulkAction(BulkAgencyActionRequest $request)
    {
        try {
            $this->authorize('bulkAction', Agence::class);

            $validated = $request->validated();

            $agencyIds = $validated['agency_ids'];
            $action = $validated['action'];
            $reason = $validated['reason'] ?? '';

            $count = 0;
            $message = '';

            switch ($action) {
                // REMOVED status-based actions
                // case 'activate':
                //     $count = $this->agencyService->bulkActivate($agencyIds);
                //     $message = "{$count} agency(ies) activated successfully.";
                //     break;
                // case 'deactivate':
                //     $count = $this->agencyService->bulkDeactivate($agencyIds);
                //     $message = "{$count} agency(ies) deactivated successfully.";
                //     break;
                case 'delete':
                    $result = $this->agencyService->bulkDelete($agencyIds);
                    $message = "{$result['deleted']} agency(ies) deleted.";
                    if (!empty($result['errors'])) {
                        $message .= ' Some errors occurred: ' . implode('; ', $result['errors']);
                    }
                    break;
                default:
                    return back()->with('error', 'Invalid action.');
            }

            return redirect()->route('admin.agencies.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error in bulk action: ' . $e->getMessage());
            return back()->with('error', 'Failed to perform bulk action.');
        }
    }

    /**
     * Export agencies.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('export', Agence::class);

            $filters = [];

            if ($request->has('company_id') && $request->company_id) {
                $filters['company_id'] = $request->company_id;
            }

            // REMOVED status filter
            // if ($request->has('status') && $request->status) {
            //     $filters['status'] = $request->status;
            // }

            $agencies = $this->agencyService->exportAgencies($filters);

            return response()->json($agencies);

        } catch (\Exception $e) {
            Log::error('Error exporting agencies: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export agencies'], 500);
        }
    }

    /**
     * Search agencies (AJAX).
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'q' => 'required|string|min:2',
            ]);

            $agencies = $this->agencyService->searchAgencies($request->q, 10);

            return response()->json([
                'success' => true,
                'data' => $agencies,
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching agencies: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed'
            ], 500);
        }
    }

    /**
     * Display agency admin's own agency.
     */
    public function myAgency()
    {
        try {
            $user = auth()->user();

            if (!$user->agency_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not assigned to any agency.');
            }

            $agency = Agence::with([
                'company',
                'coordinates.city.subRegion.region.country',
                'user',
                'buses'
            ])->findOrFail($user->agency_id);

            // Get stats using service
            $stats = $this->agencyService->getAgencyStats($agency);

            return view('agencies.show', compact('agency', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error loading my agency: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Unable to load your agency details.');
        }
    }

    /**
     * Show edit form for agency admin's agency.
     */
    public function editMyAgency()
    {
        try {
            $user = auth()->user();

            if (!$user->agency_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not assigned to any agency.');
            }

            $agency = Agence::with(['coordinates.city'])->findOrFail($user->agency_id);

            $cities = City::with('subRegion.region.country')
                ->select('id_city', 'name', 'id_sub_region')
                ->orderBy('name')
                ->get();

            $coordinates = Coordinate::where('id_city', $agency->id_city)
                ->select('id_coord', 'address', 'latitude', 'longitude')
                ->get();

            return view('agencies.edit', compact('agency', 'cities', 'coordinates'));

        } catch (\Exception $e) {
            Log::error('Error editing my agency: ' . $e->getMessage());
            return redirect()->route('my-agency.dashboard')
                ->with('error', 'Unable to load edit form.');
        }
    }

    /**
     * Update agency admin's agency.
     */
    public function updateMyAgency(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user->agency_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not assigned to any agency.');
            }

            $agency = Agence::findOrFail($user->agency_id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:agencies,name,' . $agency->id_agence . ',id_agence',
                'email' => 'required|string|email|max:255|unique:agencies,email,' . $agency->id_agence . ',id_agence',
                'phone' => 'required|string|max:20',
                'id_city' => 'required|exists:cities,id_city',
                'id_coord' => 'nullable|exists:coordinates,id_coord',
            ]);

            $this->agencyService->updateAgency($agency, $validated);

            return redirect()->route('my-agency.dashboard')
                ->with('success', 'Agency updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating my agency: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update agency: ' . $e->getMessage());
        }
    }

    /**
     * List agency buses.
     */
    public function myBuses()
    {
        try {
            $user = auth()->user();

            if (!$user->agency_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not assigned to any agency.');
            }

            $buses = Bus::where('agency_id', $user->agency_id)
                ->with(['trips'])
                ->paginate(10);

            return view('buses.index', compact('buses'));

        } catch (\Exception $e) {
            Log::error('Error loading my buses: ' . $e->getMessage());
            return redirect()->route('my-agency.dashboard')
                ->with('error', 'Unable to load buses.');
        }
    }

    /**
     * List agency trips.
     */
    public function myTrips()
    {
        try {
            $user = auth()->user();

            if (!$user->agency_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not assigned to any agency.');
            }

            $buses = Bus::where('agency_id', $user->agency_id)->pluck('bus_id');

            $trips = Tips::whereIn('id_bus', $buses)
                ->with(['journey', 'bus'])
                ->orderBy('departure_date', 'desc')
                ->paginate(10);

            return view('trips.index', compact('trips'));

        } catch (\Exception $e) {
            Log::error('Error loading my trips: ' . $e->getMessage());
            return redirect()->route('my-agency.dashboard')
                ->with('error', 'Unable to load trips.');
        }
    }

    /**
     * Show agency reports.
     */
    public function myReports()
    {
        try {
            $user = auth()->user();

            if (!$user->agency_id) {
                return redirect()->route('dashboard')
                    ->with('error', 'You are not assigned to any agency.');
            }

            $agency = Agence::with(['buses.trips.tickets', 'buses.trips', 'activities'])
                ->findOrFail($user->agency_id);

            $report = $this->agencyService->getAgencyReport($agency);

            return view('agencies.reports', [
                'agency' => $report['agency'],
                'totalRevenue' => $report['total_revenue'],
                'totalTrips' => $report['total_trips'],
                'totalTickets' => $report['total_tickets'],
                'stats' => $report['stats'],
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading reports: ' . $e->getMessage());
            return redirect()->route('my-agency.dashboard')
                ->with('error', 'Unable to load reports.');
        }
    }

    /**
     * Show public agency info.
     */
    public function publicShow(Agence $agency)
    {
        try {
            $agency->load(['company', 'coordinates.city.subRegion.region.country', 'buses']);

            return view('agencies.public', compact('agency'));

        } catch (\Exception $e) {
            Log::error('Error showing public agency: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Agency not found.');
        }
    }

    /**
     * Get coordinates for a city (AJAX).
     */
    public function getCoordinates(Request $request)
    {
        try {
            $request->validate([
                'city_id' => 'required|exists:cities,id_city',
            ]);

            $coordinates = $this->agencyService->getCoordinatesForCity($request->city_id);

            return response()->json([
                'success' => true,
                'data' => $coordinates,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting coordinates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get coordinates'
            ], 500);
        }
    }

    /**
     * Get agency activities.
     */
    public function activities(Agence $agency)
    {
        try {
            $this->authorize('view', $agency);

            $activities = $this->agencyService->getRecentActivities($agency);

            return response()->json([
                'success' => true,
                'data' => $activities,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting activities: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get activities'
            ], 500);
        }
    }

    /**
     * Notify super admins about new agency registration.
     */
    private function notifySuperAdmins($agency, $user)
    {
        try {
            $superAdmins = User::where('role', 'super_admin')->get();

            foreach ($superAdmins as $admin) {
                // Create notification in database
                $admin->notifications()->create([
                    'type' => 'new_agency_registration',
                    'data' => [
                        'agency_id' => $agency->id_agence,
                        'agency_name' => $agency->name,
                        'user_id' => $user->user_id,
                        'user_name' => $user->name,
                        'message' => "New agency '{$agency->name}' has been registered and requires approval.",
                    ],
                    'url' => route('admin.agencies.show', $agency->id_agence),
                ]);

                // Send email if configured
                try {
                    Mail::to($admin->email)->send(new NewAgencyNotificationMail($agency, $user));
                } catch (\Exception $e) {
                    Log::warning('Failed to send new agency notification to super admin: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Error notifying super admins: ' . $e->getMessage());
        }
    }
}
