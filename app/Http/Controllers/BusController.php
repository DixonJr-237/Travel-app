<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Agence;
use App\Models\Bus;
use App\Models\Company;
use App\Models\User;
use App\Models\Tips;
use Illuminate\Http\Request;

class BusController extends Controller
{
    /**
     * Display a listing of buses.
     */
    public function index(Request $request)
{
    try {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $isCompanyAdmin = $user->role === 'company_admin';
        $isAgencyAdmin = $user->role === 'agency_admin';

        // Get accessible data based on role
        $accessibleAgencyIds = [];
        $accessibleCompanyIds = [];

        if ($isSuperAdmin) {
            // Super admin can access everything - no restrictions needed
            $accessibleAgencyIds = []; // Empty means no filter
            $accessibleCompanyIds = [];
        } elseif ($isCompanyAdmin && $user->company_id) {
            // Company admin: get all agencies under their company
            $accessibleAgencyIds = Agence::where('id_company', $user->company_id)
                ->pluck('id_agence')
                ->toArray();
            $accessibleCompanyIds = [$user->company_id];
        } elseif ($isAgencyAdmin && $user->agency_id) {
            // Agency admin: only their own agency
            $accessibleAgencyIds = [$user->agency_id];
            // Get their company ID for company filter dropdown
            $agency = Agence::find($user->agency_id);
            $accessibleCompanyIds = $agency ? [$agency->id_company] : [];
        }

        // Build the base query
        $query = Bus::with([
            'agency' => function($q) {
                $q->select('id_agence', 'name', 'id_company', 'phone', 'email', 'user_id');
            },
            'agency.company' => function($q) {
                $q->select('id_company', 'name', 'user_id');
            },
            'agency.coordinates' => function($q) {
                $q->select('id_coord', 'address', 'latitude', 'longitude', 'id_city');
            },
            'agency.coordinates.city' => function($q) {
                $q->select('id_city', 'name');
            }
        ]);

        // Apply role-based filtering
        if ($isCompanyAdmin && !empty($accessibleAgencyIds)) {
            $query->whereIn('agency_id', $accessibleAgencyIds);
        } elseif ($isAgencyAdmin && !empty($accessibleAgencyIds)) {
            $query->whereIn('agency_id', $accessibleAgencyIds);
        }
        // Super admin has no filters applied

        // Filter by agency if specified (with role validation)
        if ($request->has('agency_id') && $request->agency_id) {
            $agencyId = $request->agency_id;

            // Validate that user has access to this agency
            $canAccess = false;
            if ($isSuperAdmin) {
                $canAccess = true;
            } elseif ($isCompanyAdmin && in_array($agencyId, $accessibleAgencyIds)) {
                $canAccess = true;
            } elseif ($isAgencyAdmin && $agencyId == $user->agency_id) {
                $canAccess = true;
            }

            if ($canAccess) {
                $query->where('agency_id', $agencyId);
            }
        }

        // Filter by company (indirectly through agency)
        if ($request->has('company_id') && $request->company_id) {
            $companyId = $request->company_id;

            // Validate company access
            $canAccess = false;
            if ($isSuperAdmin) {
                $canAccess = true;
            } elseif ($isCompanyAdmin && in_array($companyId, $accessibleCompanyIds)) {
                $canAccess = true;
            } elseif ($isAgencyAdmin && in_array($companyId, $accessibleCompanyIds)) {
                $canAccess = true;
            }

            if ($canAccess) {
                $query->whereHas('agency', function($q) use ($companyId) {
                    $q->where('id_company', $companyId);
                });
            }
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('registration_number', 'LIKE', "%{$search}%")
                  ->orWhere('plate_number', 'LIKE', "%{$search}%")
                  ->orWhere('model', 'LIKE', "%{$search}%")
                  ->orWhere('make', 'LIKE', "%{$search}%")
                  ->orWhereHas('agency', function($subQ) use ($search) {
                      $subQ->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Get buses with counts and pagination
        $buses = $query->withCount(['trips', 'trips as upcoming_trips_count' => function($q) {
                $q->where('departure_time', '>=', now());
            }])
            ->orderBy('registration_number')
            ->paginate(10)
            ->withQueryString();

        // Enrich bus data with computed properties
        foreach ($buses as $bus) {
            // Add agency admin info
            if ($bus->agency && $bus->agency->user_id) {
                $agencyAdmin = User::find($bus->agency->user_id);
                $bus->agency_admin_name = $agencyAdmin ? $agencyAdmin->name : null;
                $bus->agency_admin_email = $agencyAdmin ? $agencyAdmin->email : null;
            }

            // Add flags for role-based UI
            if ($isAgencyAdmin) {
                $bus->belongs_to_my_agency = ($bus->agency_id == $user->agency_id);
            } elseif ($isCompanyAdmin) {
                $bus->belongs_to_my_company = in_array($bus->agency_id, $accessibleAgencyIds);
            }

            // Calculate utilization rate (if you have trips data)
            $bus->utilization_rate = $bus->trips_count > 0
                ? min(100, round(($bus->upcoming_trips_count / $bus->trips_count) * 100, 2))
                : 0;
        }

        // Get filter data with role-based restrictions
        $companies = [];
        $agencies = [];

        if ($isSuperAdmin) {
            // Super admin sees all
            $companies = Company::select('id_company', 'name', 'user_id')
                ->orderBy('name')
                ->get();

            $agencies = Agence::select('id_agence', 'name', 'id_company')
                ->with('company:id_company,name')
                ->orderBy('name')
                ->get();

        } elseif ($isCompanyAdmin && !empty($accessibleCompanyIds)) {
            // Company admin sees only their companies and their agencies
            $companies = Company::whereIn('id_company', $accessibleCompanyIds)
                ->select('id_company', 'name', 'user_id')
                ->orderBy('name')
                ->get();

            $agencies = Agence::whereIn('id_company', $accessibleCompanyIds)
                ->select('id_agence', 'name', 'id_company')
                ->with('company:id_company,name')
                ->orderBy('name')
                ->get();

        } elseif ($isAgencyAdmin && !empty($accessibleAgencyIds)) {
            // Agency admin sees only their company and their agency
            if (!empty($accessibleCompanyIds)) {
                $companies = Company::whereIn('id_company', $accessibleCompanyIds)
                    ->select('id_company', 'name', 'user_id')
                    ->orderBy('name')
                    ->get();
            }

            $agencies = Agence::whereIn('id_agence', $accessibleAgencyIds)
                ->select('id_agence', 'name', 'id_company')
                ->with('company:id_company,name')
                ->orderBy('name')
                ->get();
        }

        // Calculate stats
        $stats = $this->calculateBusStats($isSuperAdmin, $isCompanyAdmin, $isAgencyAdmin, $accessibleAgencyIds, $accessibleCompanyIds);

        // Status options for filter dropdown
        $statusOptions = [
            'active' => 'Active',
            'maintenance' => 'Under Maintenance',
            'inactive' => 'Inactive',
            'out_of_service' => 'Out of Service'
        ];

        return view('buses.index', compact(
            'buses',
            'companies',
            'agencies',
            'statusOptions',
            'stats',
            'isSuperAdmin',
            'isCompanyAdmin',
            'isAgencyAdmin',
            'accessibleAgencyIds',
            'accessibleCompanyIds'
        ));

    } catch (\Exception $e) {
        Log::error('Error in BusController@index', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'url' => $request->fullUrl()
        ]);

        $user = auth()->user();

        return view('buses.index', [
            'buses' => collect([]),
            'companies' => collect([]),
            'agencies' => collect([]),
            'statusOptions' => [
                'active' => 'Active',
                'maintenance' => 'Under Maintenance',
                'inactive' => 'Inactive',
                'out_of_service' => 'Out of Service'
            ],
            'stats' => [
                'total_buses' => 0,
                'active_buses' => 0,
                'inactive_buses' => 0,
                'maintenance_buses' => 0,
                'total_trips' => 0,
                'upcoming_trips' => 0
            ],
            'isSuperAdmin' => $user->role === 'super_admin',
            'isCompanyAdmin' => $user->role === 'company_admin',
            'isAgencyAdmin' => $user->role === 'agency_admin',
            'accessibleAgencyIds' => [],
            'accessibleCompanyIds' => [],
            'error' => 'An error occurred while loading buses. Please try again.'
        ]);
    }
    }

    /**
     * Calculate bus statistics based on user role
     */
    private function calculateBusStats($isSuperAdmin, $isCompanyAdmin, $isAgencyAdmin, $accessibleAgencyIds, $accessibleCompanyIds)
    {
        try {
            $query = Bus::query();

            // Apply role-based filtering to stats
            if ($isCompanyAdmin && !empty($accessibleAgencyIds)) {
                $query->whereIn('agency_id', $accessibleAgencyIds);
            } elseif ($isAgencyAdmin && !empty($accessibleAgencyIds)) {
                $query->whereIn('agency_id', $accessibleAgencyIds);
            }

            $totalBuses = (clone $query)->count();
            $activeBuses = (clone $query)->where('status', 'active')->count();
            $maintenanceBuses = (clone $query)->where('status', 'maintenance')->count();
            $inactiveBuses = (clone $query)->whereIn('status', ['inactive', 'out_of_service'])->count();

            // Get bus IDs for trip statistics
            $busIds = (clone $query)->pluck('bus_id')->toArray();

            $totalTrips = 0;
            $upcomingTrips = 0;

            if (!empty($busIds)) {
                $totalTrips = Tips::whereIn('bus_id', $busIds)->count();
                $upcomingTrips = Tips::whereIn('bus_id', $busIds)
                    ->where('departure_time', '>=', now())
                    ->count();
            }

            return [
                'total_buses' => $totalBuses,
                'active_buses' => $activeBuses,
                'maintenance_buses' => $maintenanceBuses,
                'inactive_buses' => $inactiveBuses,
                'total_trips' => $totalTrips,
                'upcoming_trips' => $upcomingTrips,
                'utilization_rate' => $totalBuses > 0
                    ? round(($activeBuses / $totalBuses) * 100, 2)
                    : 0
            ];

        } catch (\Exception $e) {
            Log::error('Error calculating bus stats', [
                'message' => $e->getMessage()
            ]);

            return [
                'total_buses' => 0,
                'active_buses' => 0,
                'maintenance_buses' => 0,
                'inactive_buses' => 0,
                'total_trips' => 0,
                'upcoming_trips' => 0,
                'utilization_rate' => 0
            ];
        }
    }


    /**
     * Show the form for creating a new bus.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $agencies = Agence::with('company')->orderBy('name')->get();
        } elseif ($user->role === 'company_admin' && $user->company_id) {
            $agencies = Agence::where('id_company', $user->company_id)->orderBy('name')->get();
        } else {
            $agencies = Agence::where('id_agence', $user->agency_id)->get();
        }

        return view('buses.create', compact('agencies'));
    }

    /**
     * Store a newly created bus.
     */
    public function store(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|max:20|unique:buses',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:2026',
            'seats_count' => 'required|integer|min:1|max:100',
            'agency_id' => 'required|exists:agencies,id_agence',
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        Bus::create($request->all());

        return redirect()->route('buses.index')
            ->with('success', 'Bus created successfully.');
    }

    /**
     * Display bus details.
     */
    public function show(Bus $bus)
    {
        $bus->load(['agency.company', 'agency.coordinates.city', 'trips.journey']);

        $upcomingTrips = $bus->trips()
            ->where('departure_date', '>=', now())
            ->where('status', 'scheduled')
            ->count();

        $completedTrips = $bus->trips()
            ->where('status', 'completed')
            ->count();

        $totalBookings = $bus->trips()->withCount(['tickets'])->get()
            ->sum('tickets_count');

        $totalRevenue = $bus->trips()->with(['tickets'])->get()
            ->sum(function ($trip) {
                return $trip->tickets->sum('price');
            });

        $stats = [
            'upcoming_trips' => $upcomingTrips,
            'completed_trips' => $completedTrips,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
        ];

        return view('buses.show', compact('bus', 'stats'));
    }

    /**
     * Show the form for editing a bus.
     */
    public function edit(Bus $bus)
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $agencies = Agence::with('company')->orderBy('name')->get();
        } elseif ($user->role === 'company_admin' && $user->company_id) {
            $agencies = Agence::where('id_company', $user->company_id)->orderBy('name')->get();
        } else {
            $agencies = Agence::where('id_agence', $bus->agency_id)->get();
        }

        return view('buses.edit', compact('bus', 'agencies'));
    }

    /**
     * Update the bus.
     */
    public function update(Request $request, Bus $bus)
    {
        $request->validate([
            'registration_number' => 'required|string|max:20|unique:buses,registration_number,' . $bus->bus_id . ',bus_id',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:2026',
            'seats_count' => 'required|integer|min:1|max:100',
            'agency_id' => 'required|exists:agencies,id_agence',
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        $bus->update($request->all());

        return redirect()->route('buses.show', $bus)
            ->with('success', 'Bus updated successfully.');
    }

    /**
     * Remove the bus.
     */
    public function destroy(Bus $bus)
    {
        if ($bus->trips()->exists()) {
            return redirect()->route('buses.index')
                ->with('error', 'Cannot delete bus with existing trips.');
        }

        $bus->delete();

        return redirect()->route('buses.index')
            ->with('success', 'Bus deleted successfully.');
    }

    /**
     * Update bus status.
     */
    public function updateStatus(Request $request, Bus $bus)
    {
        $request->validate([
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        $bus->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Bus status updated successfully.');
    }

    /**
     * Set bus to maintenance mode.
     */
    public function maintenance(Bus $bus)
    {
        $bus->update(['status' => 'maintenance']);

        return redirect()->back()
            ->with('success', 'Bus set to maintenance mode.');
    }
}
