<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Ticket;
use App\Models\Tips;
use App\Models\Bus;
use App\Models\Journey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Cache durations in seconds
    const CACHE_DURATIONS = [
        'super_admin' => 600,
        'company_admin' => 600,
        'agency_admin' => 300,
        'customer' => 300,
        'reports' => 600,
    ];

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Validate user role exists in cache durations
        if (!isset(self::CACHE_DURATIONS[$user->role])) {
            abort(403, 'Invalid user role');
        }

        // Generate cache key based on user role and ID
        $cacheKey = "dashboard_{$user->role}_{$user->user_id}";

        try {
            // Cache the entire dashboard data
            $data = Cache::remember($cacheKey, self::CACHE_DURATIONS[$user->role], function () use ($user) {
                return $this->getDashboardDataForUser($user);
            });
        } catch (\Exception $e) {
            Log::error('Dashboard cache error: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'role' => $user->role,
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback without cache
            try {
                $data = $this->getDashboardDataForUser($user);
            } catch (\Exception $fallbackException) {
                Log::error('Dashboard fallback error: ' . $fallbackException->getMessage());
                $data = $this->getEmptyDashboardData($user->role);
            }
        }

        return view('dashboard', compact('data'));
    }

    /**
     * Get empty dashboard data structure for a specific role
     */
    private function getEmptyDashboardData($role)
    {
        $emptyData = [
            'stats' => [],
            'last_updated' => now()->toDateTimeString(),
            'is_empty' => true,
            'global_total_revenue' => 0,
            'global_total_trips' => 0,
            'global_total_customers' => 0,
            'global_total_tickets' => 0,
        ];

        switch ($role) {
            case 'super_admin':
                return array_merge($emptyData, [
                    'total_companies' => 0,
                    'total_agencies' => 0,
                    'total_trips' => 0,
                    'total_customers' => 0,
                    'total_tickets' => 0,
                    'total_revenue' => 0,
                    'recent_trips' => collect([]),
                    'revenue_data' => collect([]),
                    'stats' => [
                        'total_companies' => 0,
                        'total_agencies' => 0,
                        'total_trips' => 0,
                        'total_customers' => 0,
                        'total_tickets' => 0,
                        'total_revenue' => 0,
                        'new_companies' => 0,
                        'new_agencies' => 0,
                        'new_customers' => 0,
                        'new_tickets' => 0,
                        'new_trips' => 0,
                        'current_revenue' => 0,
                        'revenue_growth' => 0,
                    ],
                ]);

            case 'company_admin':
                return array_merge($emptyData, [
                    'total_agencies' => 0,
                    'total_buses' => 0,
                    'total_trips' => 0,
                    'monthly_revenue' => 0,
                    'agency_stats' => collect([]),
                    'revenue_trend' => collect([]),
                    'popular_routes' => collect([]),
                    'stats' => [
                        'total_agencies' => 0,
                        'total_buses' => 0,
                        'total_trips' => 0,
                        'upcoming_trips' => 0,
                        'monthly_revenue' => 0,
                        'monthly_tickets' => 0,
                    ],
                ]);

            case 'agency_admin':
                return array_merge($emptyData, [
                    'total_buses' => 0,
                    'active_buses' => 0,
                    'total_trips' => 0,
                    'upcoming_trips' => 0,
                    'today_trips' => 0,
                    'past_trips' => 0,
                    'avg_occupancy' => 0,
                    'total_tickets' => 0,
                    'monthly_tickets' => 0,
                    'weekly_tickets' => 0,
                    'total_revenue' => 0,
                    'monthly_revenue' => 0,
                    'weekly_revenue' => 0,
                    'unique_customers' => 0,
                    'new_customers' => 0,
                    'revenue_growth' => 0,
                    'today_trips_details' => collect([]),
                    'recent_bookings' => collect([]),
                    'weekly_trips' => collect([]),
                    'hourly_distribution' => collect([]),
                    'stats' => [
                        'total_buses' => 0,
                        'active_buses' => 0,
                        'upcoming_trips' => 0,
                        'today_trips' => 0,
                        'monthly_tickets' => 0,
                        'monthly_revenue' => 0,
                        'avg_occupancy' => 0,
                        'unique_customers' => 0,
                    ],
                ]);

            case 'customer':
                return array_merge($emptyData, [
                    'upcoming_trips' => collect([]),
                    'past_trips' => collect([]),
                    'total_tickets' => 0,
                    'total_spent' => 0,
                    'cancelled_tickets' => 0,
                    'upcoming_count' => 0,
                    'upcoming_total' => 0,
                    'past_count' => 0,
                    'past_total' => 0,
                    'last_purchase_date' => null,
                    'favorite_route' => null,
                    'monthly_spending' => collect([]),
                    'customer_since' => null,
                    'stats' => [
                        'upcoming_trips' => 0,
                        'past_trips' => 0,
                        'total_tickets' => 0,
                        'total_spent' => 0,
                        'cancelled_tickets' => 0,
                    ],
                ]);

            default:
                return $emptyData;
        }
    }

    private function getDashboardDataForUser($user)
    {
        try {
            // Validate user data first
            $this->validateUserData($user);

            // Get global stats for all users (only if authorized)
            $globalStats = $this->getGlobalStats($user);

            switch ($user->role) {
                case 'super_admin':
                    $data = $this->getSuperAdminData($user);
                    break;

                case 'company_admin':
                    // Get company ID from agencies table using user_id
                    $companyId = $this->getCompanyIdForUser($user);
                    if (!$companyId) {
                        throw new \Exception('User is not associated with any company');
                    }
                    // Temporarily set company_id on user for verification
                    $user->company_id = $companyId;

                    if (!$this->verifyCompanyAdminAccess($user)) {
                        throw new \Exception('Company admin access verification failed');
                    }
                    $data = $this->getCompanyAdminData($user);
                    break;

                case 'agency_admin':
                    // Get agency ID from agencies table using user_id
                    $agencyId = $this->getAgencyIdForUser($user);
                    if (!$agencyId) {
                        throw new \Exception('User is not associated with any agency');
                    }
                    // Temporarily set agency_id on user for verification
                    $user->agency_id = $agencyId;

                    if (!$this->verifyAgencyAdminAccess($user)) {
                        throw new \Exception('Agency admin access verification failed');
                    }
                    $data = $this->getAgencyAdminData($user);
                    break;

                case 'customer':
                    if (!$this->verifyCustomerAccess($user)) {
                        throw new \Exception('Customer access verification failed');
                    }
                    $data = $this->getCustomerData($user);
                    break;

                default:
                    return $this->getEmptyDashboardData($user->role);
            }

            // Merge global stats with role-specific data
            $data = array_merge($data, $globalStats);

            // Ensure consistent structure
            return $this->normalizeDashboardData($data, $user->role);

        } catch (\Exception $e) {
            Log::error('Error in getDashboardDataForUser: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'role' => $user->role,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getEmptyDashboardData($user->role);
        }
    }

    /**
     * Get company ID for user from agencies table
     */
    private function getCompanyIdForUser($user)
    {
        try {
            // First, try to get from agencies table (since you mentioned agencies have user_id)
            $agency = Agence::where('user_id', $user->user_id)->first();

            if ($agency && !empty($agency->id_company)) {
                return $agency->id_company;
            }

            // Alternative: Check if there's a direct company_users table
            $companyUser = DB::table('company_users')
                ->where('user_id', $user->user_id)
                ->first();

            if ($companyUser) {
                return $companyUser->company_id;
            }

            // Last resort: Check if user has direct company_id
            if (!empty($user->company_id)) {
                return $user->company_id;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting company ID for user: ' . $e->getMessage(), [
                'user_id' => $user->user_id
            ]);
            return null;
        }
    }

    /**
     * Get agency ID for user from agencies table
     */
    private function getAgencyIdForUser($user)
    {
        try {
            // Get agency from agencies table using user_id
            $agency = Agence::where('user_id', $user->user_id)->first();

            if ($agency) {
                return $agency->id_agence;
            }

            // Check if user has direct agency_id
            if (!empty($user->agency_id)) {
                return $user->agency_id;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting agency ID for user: ' . $e->getMessage(), [
                'user_id' => $user->user_id
            ]);
            return null;
        }
    }

    /**
     * Validate user data
     */
    private function validateUserData($user)
    {
        if (!$user || !$user->user_id) {
            throw new \Exception('Invalid user data');
        }

        if (!isset(self::CACHE_DURATIONS[$user->role])) {
            throw new \Exception('Invalid user role');
        }

        return true;
    }

    /**
     * Verify company admin has access to their company
     */
    private function verifyCompanyAdminAccess($user)
    {
        // Check if user has company_id (should be set by now)
        if (empty($user->company_id)) {
            Log::warning('Company admin missing company_id', ['user_id' => $user->user_id]);
            return false;
        }

        // Verify the company exists
        $company = Company::find($user->company_id);
        if (!$company) {
            Log::warning('Company admin has invalid company_id', [
                'user_id' => $user->user_id,
                'company_id' => $user->company_id
            ]);
            return false;
        }

        // Verify user is associated with this company through agencies table
        $agencyExists = Agence::where('id_company', $user->company_id)
            ->where('user_id', $user->user_id)
            ->exists();

        if (!$agencyExists) {
            Log::warning('Company admin not properly linked to company through agencies', [
                'user_id' => $user->user_id,
                'company_id' => $user->company_id
            ]);
            return false;
        }

        return true;
    }

    /**
     * Verify agency admin has access to their agency
     */
    private function verifyAgencyAdminAccess($user)
    {
        // Check if user has agency_id (should be set by now)
        if (empty($user->agency_id)) {
            Log::warning('Agency admin missing agency_id', ['user_id' => $user->user_id]);
            return false;
        }

        // Verify the agency exists and belongs to correct company
        $agency = Agence::with('company')
            ->where('id_agence', $user->agency_id)
            ->where('user_id', $user->user_id) // Ensure user owns this agency
            ->first();

        if (!$agency) {
            Log::warning('Agency admin has invalid agency_id or does not own it', [
                'user_id' => $user->user_id,
                'agency_id' => $user->agency_id
            ]);
            return false;
        }

        return true;
    }

    /**
     * Verify customer access
     */
    private function verifyCustomerAccess($user)
    {
        // Check if customer exists for this user
        $customer = Customer::where('user_id', $user->user_id)->first();

        if (!$customer) {
            Log::warning('Customer user has no customer record', ['user_id' => $user->user_id]);
            return false;
        }

        // Optional: Additional verification like email verification, account status, etc.
        if ($customer->status !== 'active') {
            Log::warning('Customer account not active', [
                'user_id' => $user->user_id,
                'customer_id' => $customer->customer_id,
                'status' => $customer->status
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get global statistics - only authorized data
     */
    private function getGlobalStats($user)
    {
        try {
            // Basic global stats that are safe for all users
            $stats = [
                'global_total_revenue' => 0,
                'global_total_trips' => 0,
                'global_total_customers' => 0,
                'global_total_tickets' => 0,
            ];

            // Super admin gets all stats
            if ($user->role === 'super_admin') {
                $stats = [
                    'global_total_revenue' => Ticket::sum('price') ?? 0,
                    'global_total_trips' => Tips::where('status', 'scheduled')->count(),
                    'global_total_customers' => Customer::count(),
                    'global_total_tickets' => Ticket::count(),
                ];
            }
            // Company admin sees stats for their company only
            elseif ($user->role === 'company_admin' && !empty($user->company_id)) {
                $stats = $this->getCompanyGlobalStats($user->company_id);
            }
            // Agency admin sees stats for their agency only
            elseif ($user->role === 'agency_admin' && !empty($user->agency_id)) {
                $stats = $this->getAgencyGlobalStats($user->agency_id);
            }
            // Customer sees limited stats
            elseif ($user->role === 'customer') {
                $stats = $this->getCustomerGlobalStats($user);
            }

            return $stats;

        } catch (\Exception $e) {
            Log::error('Error getting global stats: ' . $e->getMessage());
            return [
                'global_total_revenue' => 0,
                'global_total_trips' => 0,
                'global_total_customers' => 0,
                'global_total_tickets' => 0,
            ];
        }
    }

    /**
     * Get global stats for a company
     */
    private function getCompanyGlobalStats($companyId)
    {
        try {
            $agencyIds = Agence::where('id_company', $companyId)->pluck('id_agence')->toArray();

            if (empty($agencyIds)) {
                return [
                    'global_total_revenue' => 0,
                    'global_total_trips' => 0,
                    'global_total_customers' => 0,
                    'global_total_tickets' => 0,
                ];
            }

            $busIds = Bus::whereIn('agency_id', $agencyIds)->pluck('bus_id')->toArray();

            if (empty($busIds)) {
                return [
                    'global_total_revenue' => 0,
                    'global_total_trips' => 0,
                    'global_total_customers' => 0,
                    'global_total_tickets' => 0,
                ];
            }

            $tripIds = Tips::whereIn('bus_id', $busIds)->pluck('trip_id')->toArray();

            return [
                'global_total_revenue' => Ticket::whereIn('trip_id', $tripIds)->sum('price') ?? 0,
                'global_total_trips' => Tips::whereIn('bus_id', $busIds)
                    ->where('status', 'scheduled')
                    ->count(),
                'global_total_customers' => Ticket::whereIn('trip_id', $tripIds)
                    ->distinct('customer_id')
                    ->count('customer_id'),
                'global_total_tickets' => Ticket::whereIn('trip_id', $tripIds)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting company global stats: ' . $e->getMessage());
            return [
                'global_total_revenue' => 0,
                'global_total_trips' => 0,
                'global_total_customers' => 0,
                'global_total_tickets' => 0,
            ];
        }
    }

    /**
     * Get global stats for an agency
     */
    private function getAgencyGlobalStats($agencyId)
    {
        try {
            $busIds = Bus::where('agency_id', $agencyId)->pluck('bus_id')->toArray();

            if (empty($busIds)) {
                return [
                    'global_total_revenue' => 0,
                    'global_total_trips' => 0,
                    'global_total_customers' => 0,
                    'global_total_tickets' => 0,
                ];
            }

            $tripIds = Tips::whereIn('bus_id', $busIds)->pluck('trip_id')->toArray();

            return [
                'global_total_revenue' => Ticket::whereIn('trip_id', $tripIds)->sum('price') ?? 0,
                'global_total_trips' => Tips::whereIn('bus_id', $busIds)
                    ->where('status', 'scheduled')
                    ->count(),
                'global_total_customers' => Ticket::whereIn('trip_id', $tripIds)
                    ->distinct('customer_id')
                    ->count('customer_id'),
                'global_total_tickets' => Ticket::whereIn('trip_id', $tripIds)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting agency global stats: ' . $e->getMessage());
            return [
                'global_total_revenue' => 0,
                'global_total_trips' => 0,
                'global_total_customers' => 0,
                'global_total_tickets' => 0,
            ];
        }
    }

    /**
     * Get global stats for a customer
     */
    private function getCustomerGlobalStats($user)
    {
        try {
            $customer = Customer::where('user_id', $user->user_id)->first();

            if (!$customer) {
                return [
                    'global_total_revenue' => 0,
                    'global_total_trips' => 0,
                    'global_total_customers' => 0,
                    'global_total_tickets' => 0,
                ];
            }

            return [
                'global_total_revenue' => 0, // Customers don't see revenue
                'global_total_trips' => Tips::where('status', 'scheduled')->count(),
                'global_total_customers' => Customer::count(), // They see total customers
                'global_total_tickets' => Ticket::where('customer_id', $customer->customer_id)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting customer global stats: ' . $e->getMessage());
            return [
                'global_total_revenue' => 0,
                'global_total_trips' => 0,
                'global_total_customers' => 0,
                'global_total_tickets' => 0,
            ];
        }
    }

    // [Keep all your existing methods - getEnhancedRecentTrips, maskName, maskPhone, etc.]

    /**
     * Get company admin data - FIXED to use company_id from user
     */
    private function getCompanyAdminData($user)
    {
        try {
            // Verify company access again (double-check)
            if (!$this->verifyCompanyAdminAccess($user)) {
                throw new \Exception('Company admin access verification failed');
            }

            $companyId = $user->company_id; // Now this should be set
            $cacheKey = "company_admin_data_{$companyId}";

            return Cache::remember($cacheKey, 300, function () use ($companyId, $user) {
                $now = Carbon::now();
                $startOfMonth = $now->copy()->startOfMonth()->toDateTimeString();
                $startOfLastMonth = $now->copy()->subMonth()->startOfMonth()->toDateTimeString();
                $endOfLastMonth = $now->copy()->subMonth()->endOfMonth()->toDateTimeString();

                // Get all agencies under this company
                $agencies = Agence::where('id_company', $companyId)->get();

                if ($agencies->isEmpty()) {
                    return $this->getEmptyDashboardData('company_admin');
                }

                $agencyIds = $agencies->pluck('id_agence')->toArray();

                // Get basic counts
                $totalAgencies = count($agencyIds);
                $totalBuses = Bus::whereIn('agency_id', $agencyIds)->count();

                // Get trips count
                $busIds = Bus::whereIn('agency_id', $agencyIds)->pluck('bus_id')->toArray();
                $totalTrips = Tips::whereIn('bus_id', $busIds)->count();

                // Get revenue stats
                $tripIds = Tips::whereIn('bus_id', $busIds)->pluck('trip_id')->toArray();

                $monthlyRevenue = Ticket::whereIn('trip_id', $tripIds)
                    ->where('created_at', '>=', $startOfMonth)
                    ->sum('price') ?? 0;

                $lastMonthRevenue = Ticket::whereIn('trip_id', $tripIds)
                    ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                    ->sum('price') ?? 0;

                // Agency performance stats
                $agencyStats = DB::table('agencies')
                    ->select(
                        'agencies.id_agence',
                        'agencies.name',
                        DB::raw('COUNT(DISTINCT buses.bus_id) as bus_count'),
                        DB::raw('COUNT(DISTINCT tips.trip_id) as trip_count'),
                        DB::raw('COUNT(DISTINCT CASE WHEN tips.departure_time >= NOW() THEN tips.trip_id END) as upcoming_trips'),
                        DB::raw('COALESCE(SUM(tickets.price), 0) as revenue'),
                        DB::raw('COUNT(DISTINCT tickets.ticket_id) as ticket_count'),
                        DB::raw('COALESCE(SUM(CASE WHEN tickets.created_at >= "' . $startOfMonth . '" THEN tickets.price END), 0) as monthly_revenue')
                    )
                    ->leftJoin('buses', 'buses.agency_id', '=', 'agencies.id_agence')
                    ->leftJoin('tips', 'tips.bus_id', '=', 'buses.bus_id')
                    ->leftJoin('tickets', 'tickets.trip_id', '=', 'tips.trip_id')
                    ->where('agencies.id_company', $companyId)
                    ->groupBy('agencies.id_agence', 'agencies.name')
                    ->get()
                    ->map(function ($agency) {
                        return [
                            'id' => $agency->id_agence,
                            'name' => $agency->name,
                            'bus_count' => (int) $agency->bus_count,
                            'trip_count' => (int) $agency->trip_count,
                            'upcoming_trips' => (int) $agency->upcoming_trips,
                            'revenue' => (float) $agency->revenue,
                            'ticket_count' => (int) $agency->ticket_count,
                            'monthly_revenue' => (float) $agency->monthly_revenue,
                            'utilization_rate' => $agency->bus_count > 0
                                ? round(($agency->trip_count / $agency->bus_count) * 100, 2)
                                : 0
                        ];
                    });

                // Revenue trend
                $revenueTrend = DB::table('tickets')
                    ->select(
                        DB::raw('DATE(tickets.created_at) as date'),
                        DB::raw('COALESCE(SUM(tickets.price), 0) as revenue')
                    )
                    ->whereIn('tickets.trip_id', $tripIds)
                    ->where('tickets.created_at', '>=', now()->subDays(30)->toDateTimeString())
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(fn($item) => [
                        'date' => Carbon::parse($item->date)->format('M d'),
                        'revenue' => (float) $item->revenue
                    ]);

                // Popular routes
                $popularRoutes = DB::table('tips')
                    ->select(
                        'journeys.departure_location_id',
                        'journeys.arrival_location_id',
                        DB::raw('COUNT(DISTINCT tickets.ticket_id) as ticket_count'),
                        DB::raw('COUNT(DISTINCT tips.trip_id) as trip_count'),
                        DB::raw('COALESCE(SUM(tickets.price), 0) as revenue')
                    )
                    ->join('journeys', 'journeys.journey_id', '=', 'tips.journey_id')
                    ->join('tickets', 'tickets.trip_id', '=', 'tips.trip_id')
                    ->whereIn('tips.trip_id', $tripIds)
                    ->where('tickets.created_at', '>=', now()->subDays(30)->toDateTimeString())
                    ->groupBy('journeys.departure_location_id', 'journeys.arrival_location_id')
                    ->orderByDesc('ticket_count')
                    ->limit(5)
                    ->get()
                    ->map(function ($route) {
                        $departure = DB::table('locations')
                            ->where('location_id', $route->departure_location_id)
                            ->value('name');
                        $arrival = DB::table('locations')
                            ->where('location_id', $route->arrival_location_id)
                            ->value('name');

                        return [
                            'route' => ($departure ?? 'Unknown') . ' → ' . ($arrival ?? 'Unknown'),
                            'ticket_count' => (int) $route->ticket_count,
                            'trip_count' => (int) $route->trip_count,
                            'revenue' => (float) $route->revenue
                        ];
                    });

                // Get enhanced recent trips
                $recentTrips = $this->getEnhancedRecentTrips($user, $agencyIds, 10);

                $stats = [
                    'total_agencies' => (int) $totalAgencies,
                    'total_buses' => (int) $totalBuses,
                    'total_trips' => (int) $totalTrips,
                    'monthly_revenue' => (float) $monthlyRevenue,
                    'revenue_growth' => $this->calculateGrowthRate(
                        $lastMonthRevenue,
                        $monthlyRevenue
                    ),
                ];

                return [
                    'stats' => $stats,
                    'total_agencies' => (int) $totalAgencies,
                    'total_buses' => (int) $totalBuses,
                    'total_trips' => (int) $totalTrips,
                    'upcoming_trips' => $agencyStats->sum('upcoming_trips'),
                    'total_revenue' => (float) $agencyStats->sum('revenue'),
                    'monthly_revenue' => (float) $monthlyRevenue,
                    'monthly_tickets' => (int) $agencyStats->sum('ticket_count'),
                    'revenue_growth' => $this->calculateGrowthRate(
                        $lastMonthRevenue,
                        $monthlyRevenue
                    ),
                    'agency_stats' => $agencyStats,
                    'revenue_trend' => $revenueTrend,
                    'popular_routes' => $popularRoutes,
                    'recent_trips' => $recentTrips,
                    'last_updated' => now()->toDateTimeString(),
                    'is_empty' => false,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getCompanyAdminData: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getEmptyDashboardData('company_admin');
        }
    }

    /**
     * Get agency admin data - FIXED to use agency_id from user
     */
   private function getAgencyAdminData($user)
    {
        try {
            // Verify agency access
            if (!$this->verifyAgencyAdminAccess($user)) {
                throw new \Exception('Agency admin access verification failed');
            }

            $agencyId = $user->agency_id; // Now this should be set
            $cacheKey = "agency_admin_data_v2_{$agencyId}";

            return Cache::remember($cacheKey, 180, function () use ($agencyId, $user) {
                $now = Carbon::now();
                $today = Carbon::today()->toDateString();
                $startOfMonth = $now->copy()->startOfMonth()->toDateTimeString();
                $startOfLastMonth = $now->copy()->subMonth()->startOfMonth()->toDateTimeString();
                $endOfLastMonth = $now->copy()->subMonth()->endOfMonth()->toDateTimeString();
                $startOfWeek = $now->copy()->startOfWeek()->toDateTimeString();
                $endOfWeek = $now->copy()->endOfWeek()->toDateTimeString();

                // Get all buses for this agency
                $buses = Bus::where('agency_id', $agencyId)->get();

                if ($buses->isEmpty()) {
                    return $this->getEmptyDashboardData('agency_admin');
                }

                $busIds = $buses->pluck('bus_id')->toArray();

                // Get all trip IDs
                $tripIds = Tips::whereIn('bus_id', $busIds)->pluck('trip_id')->toArray();

                // Bus statistics
                $totalBuses = count($busIds);
                $activeBuses = Bus::whereIn('bus_id', $busIds)
                    ->where('status', 'active')
                    ->count();

                // Trip statistics
                $totalTrips = Tips::whereIn('bus_id', $busIds)->count();
                $upcomingTrips = Tips::whereIn('bus_id', $busIds)
                    ->where('departure_time', '>', $now)
                    ->where('status', 'scheduled')
                    ->count();
                $todayTrips = Tips::whereIn('bus_id', $busIds)
                    ->whereDate('departure_time', $today)
                    ->count();
                $pastTrips = Tips::whereIn('bus_id', $busIds)
                    ->where('departure_time', '<', $now)
                    ->count();

                // Revenue statistics
                $totalRevenue = Ticket::whereIn('trip_id', $tripIds)->sum('price') ?? 0;
                $monthlyRevenue = Ticket::whereIn('trip_id', $tripIds)
                    ->where('created_at', '>=', $startOfMonth)
                    ->sum('price') ?? 0;
                $weeklyRevenue = Ticket::whereIn('trip_id', $tripIds)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->sum('price') ?? 0;
                $lastMonthRevenue = Ticket::whereIn('trip_id', $tripIds)
                    ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                    ->sum('price') ?? 0;

                // Ticket statistics
                $totalTickets = Ticket::whereIn('trip_id', $tripIds)->count();
                $monthlyTickets = Ticket::whereIn('trip_id', $tripIds)
                    ->where('created_at', '>=', $startOfMonth)
                    ->count();
                $weeklyTickets = Ticket::whereIn('trip_id', $tripIds)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->count();

                // Customer statistics
                $uniqueCustomers = Ticket::whereIn('trip_id', $tripIds)
                    ->distinct('customer_id')
                    ->count('customer_id');
                $newCustomers = Ticket::whereIn('trip_id', $tripIds)
                    ->whereHas('customer', function($q) use ($startOfMonth) {
                        $q->where('created_at', '>=', $startOfMonth);
                    })
                    ->distinct('customer_id')
                    ->count('customer_id');

                // Average occupancy
                $totalCapacity = $buses->sum('capacity');
                $totalPassengers = $totalTickets;
                $avgOccupancy = $totalCapacity > 0 ? round(($totalPassengers / $totalCapacity) * 100, 2) : 0;

                // Today's trips details - FIXED VERSION with all required fields
                $todayTripsDetails = Tips::with([
                    'journey.departureLocation.city',
                    'journey.arrivalLocation.city',
                    'bus'
                ])
                ->whereIn('bus_id', $busIds)
                ->whereDate('departure_time', $today)
                ->orderBy('departure_time')
                ->get()
                ->map(function ($trip) {
                    $ticketCount = Ticket::where('trip_id', $trip->trip_id)->count();
                    $capacity = $trip->bus->capacity ?? 0;

                    // Get location names
                    $departureLocation = $trip->journey?->departureLocation?->city?->name ?? 'Unknown';
                    $arrivalLocation = $trip->journey?->arrivalLocation?->city?->name ?? 'Unknown';

                    return [
                        'id' => $trip->trip_id,
                        // Add both formats to support different view requirements
                        'departure_location' => $departureLocation,
                        'arrival_location' => $arrivalLocation,
                        'route' => $departureLocation . ' → ' . $arrivalLocation,
                        'departure_time' => Carbon::parse($trip->departure_time)->format('H:i'),
                        'bus_registration' => $trip->bus->registration_number ?? 'N/A',
                        'ticket_count' => $ticketCount,
                        'capacity' => $capacity,
                        'occupancy_rate' => $capacity > 0 ? round(($ticketCount / $capacity) * 100, 2) : 0,
                        'status' => $trip->status
                    ];
                });

                // Weekly trips for chart
                $weeklyTrips = DB::table('tips')
                    ->select(
                        DB::raw('DATE(departure_time) as date'),
                        DB::raw('COUNT(*) as trip_count')
                    )
                    ->whereIn('bus_id', $busIds)
                    ->where('departure_time', '>=', $startOfWeek)
                    ->where('departure_time', '<=', $endOfWeek)
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(fn($item) => [
                        'date' => Carbon::parse($item->date)->format('D'),
                        'trip_count' => (int) $item->trip_count
                    ]);

                // Hourly distribution
                $hourlyDistribution = DB::table('tips')
                    ->select(
                        DB::raw('HOUR(departure_time) as hour'),
                        DB::raw('COUNT(*) as trip_count')
                    )
                    ->whereIn('bus_id', $busIds)
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get()
                    ->map(fn($item) => [
                        'hour' => sprintf('%02d:00', $item->hour),
                        'trip_count' => (int) $item->trip_count
                    ]);

                // Get enhanced recent trips
                $recentTrips = $this->getEnhancedRecentTrips($user, [$agencyId], 10);

                // Get recent bookings with masked data
                $recentBookings = Ticket::with([
                    'customer:customer_id,first_name,last_name,phone,email',
                    'trip.journey.departureLocation.city',
                    'trip.journey.arrivalLocation.city',
                ])
                ->whereIn('trip_id', $tripIds)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($booking) use ($user) {
                    $departureLocation = $booking->trip?->journey?->departureLocation?->city?->name ?? 'Unknown';
                    $arrivalLocation = $booking->trip?->journey?->arrivalLocation?->city?->name ?? 'Unknown';

                    return [
                        'id' => $booking->ticket_id,
                        'reference' => $booking->reference_number ?? 'TKT-' . str_pad($booking->ticket_id, 6, '0', STR_PAD_LEFT),
                        'customer_name' => $this->maskName(
                            ($booking->customer?->first_name ?? '') . ' ' . ($booking->customer?->last_name ?? ''),
                            $user->role
                        ),
                        'customer_phone' => $this->maskPhone($booking->customer?->phone, $user->role),
                        'route' => $departureLocation . ' → ' . $arrivalLocation,
                        'departure_date' => $booking->trip?->departure_time ? Carbon::parse($booking->trip->departure_time)->format('Y-m-d') : null,
                        'departure_time' => $booking->trip?->departure_time ? Carbon::parse($booking->trip->departure_time)->format('H:i') : null,
                        'price' => (float) $booking->price,
                        'status' => $booking->status,
                        'purchase_date' => $booking->created_at ? Carbon::parse($booking->created_at)->format('Y-m-d H:i') : null
                    ];
                });

                $stats = [
                    'total_buses' => (int) $totalBuses,
                    'active_buses' => (int) $activeBuses,
                    'total_trips' => (int) $totalTrips,
                    'upcoming_trips' => (int) $upcomingTrips,
                    'today_trips' => (int) $todayTrips,
                    'past_trips' => (int) $pastTrips,
                    'total_tickets' => (int) $totalTickets,
                    'monthly_tickets' => (int) $monthlyTickets,
                    'weekly_tickets' => (int) $weeklyTickets,
                    'total_revenue' => (float) $totalRevenue,
                    'monthly_revenue' => (float) $monthlyRevenue,
                    'weekly_revenue' => (float) $weeklyRevenue,
                    'unique_customers' => (int) $uniqueCustomers,
                    'new_customers' => (int) $newCustomers,
                    'avg_occupancy' => (float) $avgOccupancy,
                    'revenue_growth' => $this->calculateGrowthRate($lastMonthRevenue, $monthlyRevenue),
                ];

                return [
                    'stats' => $stats,
                    'total_buses' => (int) $totalBuses,
                    'active_buses' => (int) $activeBuses,
                    'total_trips' => (int) $totalTrips,
                    'upcoming_trips' => (int) $upcomingTrips,
                    'today_trips' => (int) $todayTrips,
                    'past_trips' => (int) $pastTrips,
                    'avg_occupancy' => (float) $avgOccupancy,
                    'total_tickets' => (int) $totalTickets,
                    'monthly_tickets' => (int) $monthlyTickets,
                    'weekly_tickets' => (int) $weeklyTickets,
                    'total_revenue' => (float) $totalRevenue,
                    'monthly_revenue' => (float) $monthlyRevenue,
                    'weekly_revenue' => (float) $weeklyRevenue,
                    'unique_customers' => (int) $uniqueCustomers,
                    'new_customers' => (int) $newCustomers,
                    'revenue_growth' => $this->calculateGrowthRate($lastMonthRevenue, $monthlyRevenue),
                    'today_trips_details' => $todayTripsDetails,
                    'recent_bookings' => $recentBookings,
                    'weekly_trips' => $weeklyTrips,
                    'hourly_distribution' => $hourlyDistribution,
                    'recent_trips' => $recentTrips,
                    'last_updated' => now()->toDateTimeString(),
                    'is_empty' => false,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getAgencyAdminData: ' . $e->getMessage(), [
                'user_id' => $user->user_id ?? null,
                'agency_id' => $user->agency_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getEmptyDashboardData('agency_admin');
        }
    }
    private function getCustomerData($user)
    {
        try {
            // Verify customer access
            if (!$this->verifyCustomerAccess($user)) {
                throw new \Exception('Customer access verification failed');
            }

            $customer = Customer::where('user_id', $user->user_id)->first();
            $cacheKey = "customer_data_{$customer->customer_id}";

            return Cache::remember($cacheKey, 120, function () use ($customer, $user) {
                $now = Carbon::now();

                // Get customer stats
                $totalTickets = Ticket::where('customer_id', $customer->customer_id)->count();
                $totalSpent = Ticket::where('customer_id', $customer->customer_id)->sum('price') ?? 0;
                $cancelledTickets = Ticket::where('customer_id', $customer->customer_id)
                    ->where('status', 'cancelled')
                    ->count();

                // Upcoming trips
                $upcomingCount = Ticket::where('customer_id', $customer->customer_id)
                    ->whereHas('trip', function($q) use ($now) {
                        $q->where('departure_time', '>=', $now);
                    })
                    ->where('status', '!=', 'cancelled')
                    ->count();

                // Get trips
                $tickets = Ticket::where('customer_id', $customer->customer_id)
                    ->with([
                        'trip.journey.departureLocation.city',
                        'trip.journey.arrivalLocation.city',
                        'trip.bus.agence'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Separate upcoming and past trips
                $upcomingTrips = $tickets->filter(function ($ticket) use ($now) {
                    return $ticket->trip &&
                           Carbon::parse($ticket->trip->departure_time) >= $now &&
                           $ticket->status !== 'cancelled';
                })->map(function ($ticket) {
                    return [
                        'id' => $ticket->ticket_id,
                        'reference' => $ticket->reference_number ?? 'TKT-' . str_pad($ticket->ticket_id, 6, '0', STR_PAD_LEFT),
                        'route' => ($ticket->trip?->journey?->departureLocation?->city?->name ?? 'Unknown') .
                                  ' → ' .
                                  ($ticket->trip?->journey?->arrivalLocation?->city?->name ?? 'Unknown'),
                        'departure_date' => $ticket->trip?->departure_time ? Carbon::parse($ticket->trip->departure_time)->format('Y-m-d') : null,
                        'departure_time' => $ticket->trip?->departure_time ? Carbon::parse($ticket->trip->departure_time)->format('H:i') : null,
                        'price' => (float) $ticket->price,
                        'status' => $ticket->status,
                    ];
                })->values();

                $pastTrips = $tickets->filter(function ($ticket) use ($now) {
                    return !$ticket->trip ||
                           Carbon::parse($ticket->trip->departure_time) < $now ||
                           $ticket->status === 'cancelled';
                })->map(function ($ticket) {
                    return [
                        'id' => $ticket->ticket_id,
                        'reference' => $ticket->reference_number ?? 'TKT-' . str_pad($ticket->ticket_id, 6, '0', STR_PAD_LEFT),
                        'route' => ($ticket->trip?->journey?->departureLocation?->city?->name ?? 'Unknown') .
                                  ' → ' .
                                  ($ticket->trip?->journey?->arrivalLocation?->city?->name ?? 'Unknown'),
                        'departure_date' => $ticket->trip?->departure_time ? Carbon::parse($ticket->trip->departure_time)->format('Y-m-d') : null,
                        'price' => (float) $ticket->price,
                        'status' => $ticket->status,
                    ];
                })->values();

                return [
                    'upcoming_trips' => $upcomingTrips,
                    'past_trips' => $pastTrips,
                    'total_tickets' => (int) $totalTickets,
                    'total_spent' => (float) $totalSpent,
                    'cancelled_tickets' => (int) $cancelledTickets,
                    'upcoming_count' => (int) $upcomingCount,
                    'stats' => [
                        'upcoming_trips' => (int) $upcomingCount,
                        'past_trips' => $pastTrips->count(),
                        'total_tickets' => (int) $totalTickets,
                        'total_spent' => (float) $totalSpent,
                        'cancelled_tickets' => (int) $cancelledTickets,
                    ],
                    'last_updated' => now()->toDateTimeString(),
                    'is_empty' => false,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getCustomerData: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getEmptyDashboardData('customer');
        }
    }

    private function getSuperAdminData($user)
    {
        // Verify user is super admin
        if ($user->role !== 'super_admin') {
            throw new \Exception('Unauthorized access to super admin data');
        }

        $cacheKey = 'super_admin_dashboard_stats_v7';

        try {
            return Cache::remember($cacheKey, 300, function () use ($user) {
                $now = Carbon::now();
                $startOfMonth = $now->copy()->startOfMonth()->toDateTimeString();
                $startOfLastMonth = $now->copy()->subMonth()->startOfMonth()->toDateTimeString();
                $endOfLastMonth = $now->copy()->subMonth()->endOfMonth()->toDateTimeString();

                // Get total counts
                $totalCompanies = Company::count();
                $totalAgencies = Agence::count();
                $totalCustomers = Customer::count();
                $totalTickets = Ticket::count();
                $totalTrips = Tips::where('status', 'scheduled')->count();
                $totalRevenue = Ticket::sum('price') ?? 0;

                // Get current month stats
                $currentMonthCompanies = Company::where('created_at', '>=', $startOfMonth)->count();
                $currentMonthAgencies = Agence::where('created_at', '>=', $startOfMonth)->count();
                $currentMonthCustomers = Customer::where('created_at', '>=', $startOfMonth)->count();
                $currentMonthTickets = Ticket::where('created_at', '>=', $startOfMonth)->count();
                $currentMonthTrips = Tips::where('created_at', '>=', $startOfMonth)
                    ->where('status', 'scheduled')
                    ->count();
                $currentMonthRevenue = Ticket::where('created_at', '>=', $startOfMonth)
                    ->sum('price') ?? 0;

                // Get last month stats
                $lastMonthRevenue = Ticket::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                    ->sum('price') ?? 0;

                // Calculate growth percentage
                $revenueGrowthPercentage = $this->calculateGrowthRate($lastMonthRevenue, $currentMonthRevenue);

                // Get enhanced recent trips with customer spending
                $recentTrips = $this->getEnhancedRecentTrips($user, null, 10);

                // Get revenue data with trends
                $revenueData = DB::table('tickets')
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('COALESCE(SUM(price), 0) as revenue'),
                        DB::raw('COUNT(*) as ticket_count')
                    )
                    ->where('created_at', '>=', now()->subDays(30)->toDateTimeString())
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(fn($item) => [
                        'date' => Carbon::parse($item->date)->format('M d'),
                        'revenue' => (float) $item->revenue,
                        'ticket_count' => (int) $item->ticket_count
                    ]);

                // Get top performing agencies
                $topAgencies = DB::table('agencies')
                    ->select(
                        'agencies.id_agence',
                        'agencies.name',
                        DB::raw('COUNT(DISTINCT tickets.ticket_id) as ticket_count'),
                        DB::raw('COALESCE(SUM(tickets.price), 0) as revenue')
                    )
                    ->join('buses', 'buses.agency_id', '=', 'agencies.id_agence')
                    ->join('tips', 'tips.bus_id', '=', 'buses.bus_id')
                    ->join('tickets', 'tickets.trip_id', '=', 'tips.trip_id')
                    ->where('tickets.created_at', '>=', now()->subDays(30)->toDateTimeString())
                    ->groupBy('agencies.id_agence', 'agencies.name')
                    ->orderByDesc('revenue')
                    ->limit(5)
                    ->get();

                $stats = [
                    'total_companies' => (int) $totalCompanies,
                    'total_agencies' => (int) $totalAgencies,
                    'total_trips' => (int) $totalTrips,
                    'total_customers' => (int) $totalCustomers,
                    'total_tickets' => (int) $totalTickets,
                    'total_revenue' => (float) $totalRevenue,
                    'new_companies' => (int) $currentMonthCompanies,
                    'new_agencies' => (int) $currentMonthAgencies,
                    'new_customers' => (int) $currentMonthCustomers,
                    'new_tickets' => (int) $currentMonthTickets,
                    'new_trips' => (int) $currentMonthTrips,
                    'current_revenue' => (float) $currentMonthRevenue,
                    'revenue_growth' => (float) $revenueGrowthPercentage,
                ];

                return [
                    'stats' => $stats,
                    'total_companies' => (int) $totalCompanies,
                    'total_agencies' => (int) $totalAgencies,
                    'total_trips' => (int) $totalTrips,
                    'total_customers' => (int) $totalCustomers,
                    'total_tickets' => (int) $totalTickets,
                    'total_revenue' => (float) $totalRevenue,
                    'recent_trips' => $recentTrips,
                    'revenue_data' => $revenueData,
                    'top_agencies' => $topAgencies,
                    'last_updated' => now()->toDateTimeString(),
                    'is_empty' => false,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getSuperAdminData: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->user_id
            ]);

            return $this->getEmptyDashboardData('super_admin');
        }
    }

    /**
     * Calculate growth rate percentage
     */
    private function calculateGrowthRate($previous, $current)
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 2);
        }
        return $current > 0 ? 100 : 0;
    }

    /**
     * Ensure consistent data structure across all roles
     */
    private function normalizeDashboardData($data, $role)
    {
        $emptyData = $this->getEmptyDashboardData($role);
        return array_merge($emptyData, $data, ['is_empty' => false]);
    }

    /**
     * Static method to clear dashboard cache
     */
    public static function clearDashboardCache($userId, $role, $relatedId = null)
    {
        try {
            $cacheKey = "dashboard_{$role}_{$userId}";
            Cache::forget($cacheKey);

            switch ($role) {
                case 'super_admin':
                    Cache::forget('super_admin_dashboard_stats_v7');
                    break;
                case 'company_admin':
                    if ($relatedId) {
                        Cache::forget("company_admin_data_{$relatedId}");
                    }
                    break;
                case 'agency_admin':
                    if ($relatedId) {
                        Cache::forget("agency_admin_data_v2_{$relatedId}");
                    }
                    break;
                case 'customer':
                    if ($relatedId) {
                        Cache::forget("customer_data_{$relatedId}");
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error in static clearDashboardCache: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to check for new bookings
     */
    public function checkNewBookings()
    {
        try {
            $user = auth()->user();
            $lastCheck = request()->get('last_check', now()->subMinutes(5));

            $hasNew = false;

            switch ($user->role) {
                case 'super_admin':
                    $hasNew = Ticket::where('created_at', '>', $lastCheck)->exists();
                    break;
                case 'company_admin':
                    $companyId = $this->getCompanyIdForUser($user);
                    if ($companyId) {
                        $agencyIds = Agence::where('id_company', $companyId)->pluck('id_agence')->toArray();
                        $busIds = Bus::whereIn('agency_id', $agencyIds)->pluck('bus_id')->toArray();
                        $tripIds = Tips::whereIn('bus_id', $busIds)->pluck('trip_id')->toArray();
                        $hasNew = Ticket::whereIn('trip_id', $tripIds)
                            ->where('created_at', '>', $lastCheck)
                            ->exists();
                    }
                    break;
                case 'agency_admin':
                    $agencyId = $this->getAgencyIdForUser($user);
                    if ($agencyId) {
                        $busIds = Bus::where('agency_id', $agencyId)->pluck('bus_id')->toArray();
                        $tripIds = Tips::whereIn('bus_id', $busIds)->pluck('trip_id')->toArray();
                        $hasNew = Ticket::whereIn('trip_id', $tripIds)
                            ->where('created_at', '>', $lastCheck)
                            ->exists();
                    }
                    break;
            }

            return response()->json(['hasNew' => $hasNew]);
        } catch (\Exception $e) {
            Log::error('Error checking new bookings: ' . $e->getMessage());
            return response()->json(['hasNew' => false]);
        }
    }

    /**
     * API endpoint to get customer insights
     */
    public function getCustomerInsightsApi($tripId, $customerId)
    {
        try {
            $user = auth()->user();

            // Check authorization
            if (!$this->canAccessCustomerData($user, $customerId)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $insights = $this->getCustomerInsights($user, $customerId, $tripId);

            if ($insights) {
                return response()->json($insights);
            }

            return response()->json(['error' => 'Customer not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error in getCustomerInsightsApi: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch customer insights'], 500);
        }
    }

    /**
     * Check if user can access a specific trip
     */
    private function canAccessTrip($user, $trip)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'company_admin') {
            $companyId = $this->getCompanyIdForUser($user);
            if ($companyId) {
                $agencyIds = Agence::where('id_company', $companyId)->pluck('id_agence')->toArray();
                return in_array($trip->bus?->agency_id, $agencyIds);
            }
        }

        if ($user->role === 'agency_admin') {
            $agencyId = $this->getAgencyIdForUser($user);
            return $agencyId && $trip->bus?->agency_id == $agencyId;
        }

        if ($user->role === 'customer') {
            $customer = Customer::where('user_id', $user->user_id)->first();
            return $customer && $trip->tickets()->where('customer_id', $customer->customer_id)->exists();
        }

        return false;
    }

    /**
     * Export trip data with proper authorization
     */
    public function exportTripData($tripId)
    {
        try {
            $user = auth()->user();

            // Find the trip
            $trip = Tips::with([
                'bus.agence',
                'journey.departureLocation.city',
                'journey.arrivalLocation.city',
                'tickets.customer'
            ])->findOrFail($tripId);

            // Check if user has access to this trip
            if (!$this->canAccessTrip($user, $trip)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Generate CSV
            $filename = 'trip_' . $tripId . '_' . now()->format('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($trip, $user) {
                $file = fopen('php://output', 'w');

                // Headers
                fputcsv($file, [
                    'Ticket ID',
                    'Customer Name',
                    'Customer Phone',
                    'Customer Email',
                    'Route',
                    'Departure Date',
                    'Departure Time',
                    'Seat Number',
                    'Price (FCFA)',
                    'Status',
                    'Purchase Date'
                ]);

                // Data (with masking if needed)
                foreach ($trip->tickets as $ticket) {
                    fputcsv($file, [
                        $ticket->ticket_id,
                        $this->maskName(
                            ($ticket->customer?->first_name ?? '') . ' ' . ($ticket->customer?->last_name ?? ''),
                            $user->role
                        ),
                        $this->maskPhone($ticket->customer?->phone ?? 'N/A', $user->role),
                        $this->maskEmail($ticket->customer?->email ?? 'N/A', $user->role),
                        ($trip->journey?->departureLocation?->city?->name ?? 'Unknown') . ' → ' .
                        ($trip->journey?->arrivalLocation?->city?->name ?? 'Unknown'),
                        $trip->departure_time ? Carbon::parse($trip->departure_time)->format('Y-m-d') : 'N/A',
                        $trip->departure_time ? Carbon::parse($trip->departure_time)->format('H:i') : 'N/A',
                        $ticket->seat_number ?? 'N/A',
                        number_format($ticket->price, 0, '.', ''),
                        $ticket->status,
                        $ticket->created_at ? Carbon::parse($ticket->created_at)->format('Y-m-d H:i') : 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting trip data: ' . $e->getMessage());
            return back()->with('error', 'Unable to export trip data');
        }
    }

    /**
     * Get enhanced recent trips with customer spending data - WITH PROPER AUTHORIZATION
     */
   /**
 * Get enhanced recent trips with customer spending data - WITH PROPER AUTHORIZATION
 */
private function getEnhancedRecentTrips($user, $agencyIds = null, $limit = 10)
{
    try {
        $query = Tips::with([
            'bus.agence:id_agence,name',
            'journey.departureLocation', // Remove column restriction to avoid errors
            'journey.arrivalLocation',   // Remove column restriction to avoid errors
            'tickets.customer' => function($query) {
                $query->select('customer_id', 'first_name', 'last_name', 'phone', 'email');
            }
        ])
        ->where('status', 'scheduled')
        ->orderBy('created_at', 'desc');

        // Apply authorization based on user role
        switch ($user->role) {
            case 'super_admin':
                // No filter - sees all
                break;

            case 'company_admin':
                $companyId = $this->getCompanyIdForUser($user);
                if ($companyId) {
                    $companyAgencyIds = Agence::where('id_company', $companyId)
                        ->pluck('id_agence')
                        ->toArray();
                    if (!empty($companyAgencyIds)) {
                        $query->whereIn('bus_id', function($q) use ($companyAgencyIds) {
                            $q->select('bus_id')
                              ->from('buses')
                              ->whereIn('agency_id', $companyAgencyIds);
                        });
                    }
                }
                break;

            case 'agency_admin':
                $agencyId = $this->getAgencyIdForUser($user);
                if ($agencyId) {
                    $query->whereIn('bus_id', function($q) use ($agencyId) {
                        $q->select('bus_id')
                          ->from('buses')
                          ->where('agency_id', $agencyId);
                    });
                }
                break;

            case 'customer':
                // Customers don't need enhanced recent trips
                return collect([]);

            default:
                return collect([]);
        }

        // Additional filter if specific agency IDs are provided
        if ($agencyIds && !empty($agencyIds) && ($user->role === 'super_admin' || $user->role === 'company_admin')) {
            $query->whereIn('bus_id', function($q) use ($agencyIds) {
                $q->select('bus_id')->from('buses')->whereIn('agency_id', $agencyIds);
            });
        }

        return $query->limit($limit)
            ->get()
            ->map(function ($trip) use ($user) {
                // Filter customer data based on user role
                $tickets = $trip->tickets ?? collect();

                // Get location names safely
                $departureLocation = $trip->journey?->departureLocation?->name ??
                                    $trip->journey?->departureLocation?->city?->name ??
                                    'Unknown';
                $arrivalLocation = $trip->journey?->arrivalLocation?->name ??
                                  $trip->journey?->arrivalLocation?->city?->name ??
                                  'Unknown';

                // Only show full customer details to appropriate roles
                $customers = collect([]);
                if (in_array($user->role, ['super_admin', 'company_admin', 'agency_admin'])) {
                    $customers = $tickets->groupBy('customer_id')->map(function ($customerTickets, $customerId) use ($user) {
                        $firstTicket = $customerTickets->first();
                        $customer = $firstTicket?->customer;

                        return [
                            'id' => $customerId,
                            'name' => $customer ? $this->maskName(trim($customer->first_name . ' ' . $customer->last_name), $user->role) : 'Unknown',
                            'tickets' => $customerTickets->count(),
                            'spent' => (float) $customerTickets->sum('price'),
                            'seat_numbers' => $this->maskSeatNumbers($customerTickets->pluck('seat_number')->filter()->toArray(), $user->role),
                            'phone' => $this->maskPhone($customer?->phone, $user->role),
                            'email' => $this->maskEmail($customer?->email, $user->role),
                        ];
                    })->values();
                }

                return [
                    'id' => $trip->trip_id,
                    'departure_location' => $departureLocation,
                    'arrival_location' => $arrivalLocation,
                    'route' => $departureLocation . ' → ' . $arrivalLocation,
                    'departure_date' => $trip->departure_time ? Carbon::parse($trip->departure_time)->format('Y-m-d') : null,
                    'departure_time' => $trip->departure_time ? Carbon::parse($trip->departure_time)->format('H:i') : null,
                    'bus_registration' => $trip->bus?->registration_number ?? 'N/A',
                    'agency_name' => $trip->bus?->agence?->name ?? 'N/A',
                    'agency_id' => $trip->bus?->agence?->id_agence,
                    'status' => $trip->status,
                    'total_revenue' => (float) $tickets->sum('price'),
                    'total_passengers' => (int) $tickets->count(),
                    'occupancy_rate' => (float) $this->calculateOccupancyRate($trip, $tickets->count()),
                    'bus_capacity' => (int) ($trip->bus?->capacity ?? 0),
                    'customers' => $customers,
                    'ticket_count' => (int) $tickets->count(),
                    'created_at' => $trip->created_at ? Carbon::parse($trip->created_at)->format('Y-m-d H:i:s') : null
                ];
            });
    } catch (\Exception $e) {
        Log::error('Error getting enhanced recent trips: ' . $e->getMessage(), [
            'user_id' => $user->user_id ?? null,
            'trace' => $e->getTraceAsString()
        ]);
        return collect([]);
    }
}

/**
 * Calculate occupancy rate safely
 */
private function calculateOccupancyRate($trip, $passengerCount)
{
    $capacity = $trip->bus?->capacity ?? 0;
    if ($capacity > 0) {
        return round(($passengerCount / $capacity) * 100, 2);
    }
    return 0;
}

    /**
     * Mask sensitive data based on user role
     */
    private function maskName($name, $role)
    {
        if ($role === 'super_admin' || $role === 'company_admin') {
            return $name;
        }
        // Mask for other roles
        $parts = explode(' ', $name);
        if (count($parts) > 1) {
            return $parts[0] . ' ' . substr($parts[1], 0, 1) . '.';
        }
        return substr($name, 0, 3) . '***';
    }

    private function maskPhone($phone, $role)
    {
        if (!$phone) return null;
        if ($role === 'super_admin' || $role === 'company_admin') {
            return $phone;
        }
        return substr($phone, 0, 4) . '****' . substr($phone, -2);
    }

    private function maskEmail($email, $role)
    {
        if (!$email) return null;
        if ($role === 'super_admin' || $role === 'company_admin') {
            return $email;
        }
        $parts = explode('@', $email);
        return substr($parts[0], 0, 2) . '***@' . $parts[1];
    }

    private function maskSeatNumbers($seats, $role)
    {
        if ($role === 'super_admin' || $role === 'company_admin') {
            return $seats;
        }
        return ['***'];
    }

    /**
     * Get customer insights for a specific customer - WITH AUTHORIZATION
     */
    private function getCustomerInsights($user, $customerId, $tripId = null)
    {
        try {
            // Verify user has access to this customer's data
            if (!$this->canAccessCustomerData($user, $customerId)) {
                Log::warning('Unauthorized attempt to access customer data', [
                    'user_id' => $user->user_id,
                    'role' => $user->role,
                    'target_customer_id' => $customerId
                ]);
                return null;
            }

            $customer = Customer::find($customerId);
            if (!$customer) {
                return null;
            }

            // Get all tickets for this customer
            $tickets = Ticket::where('customer_id', $customerId)
                ->with(['trip.journey.departureLocation.city', 'trip.journey.arrivalLocation.city'])
                ->get();

            // Calculate total spent
            $totalSpent = $tickets->sum('price');
            $tripsCount = $tickets->count();

            // Find favorite route
            $routeCounts = [];
            foreach ($tickets as $ticket) {
                if ($ticket->trip && $ticket->trip->journey) {
                    $departure = $ticket->trip->journey->departureLocation?->city?->name ?? 'Unknown';
                    $arrival = $ticket->trip->journey->arrivalLocation?->city?->name ?? 'Unknown';
                    $route = $departure . ' → ' . $arrival;
                    $routeCounts[$route] = ($routeCounts[$route] ?? 0) + 1;
                }
            }
            arsort($routeCounts);
            $favoriteRoute = !empty($routeCounts) ? array_key_first($routeCounts) : 'No trips yet';

            // Get recent bookings (masked for non-admin)
            $recentBookings = $tickets->sortByDesc('created_at')->take(5)->map(function($ticket) use ($user) {
                $departure = $ticket->trip?->journey?->departureLocation?->city?->name ?? 'Unknown';
                $arrival = $ticket->trip?->journey?->arrivalLocation?->city?->name ?? 'Unknown';
                return [
                    'date' => $ticket->created_at ? Carbon::parse($ticket->created_at)->format('M d, Y') : 'Unknown',
                    'route' => $departure . ' → ' . $arrival,
                    'amount' => (float) $ticket->price
                ];
            })->values()->toArray();

            // Return data based on user role
            $insights = [
                'totalSpent' => $totalSpent,
                'tripsCount' => $tripsCount,
                'favoriteRoute' => $favoriteRoute,
                'memberSince' => $customer->created_at ? Carbon::parse($customer->created_at)->format('M Y') : 'Unknown',
                'recentBookings' => $recentBookings,
            ];

            // Only admins get full contact details
            if (in_array($user->role, ['super_admin', 'company_admin', 'agency_admin'])) {
                $insights = array_merge($insights, [
                    'customerName' => trim($customer->first_name . ' ' . $customer->last_name),
                    'customerEmail' => $customer->email,
                    'customerPhone' => $customer->phone,
                ]);
            }

            return $insights;

        } catch (\Exception $e) {
            Log::error('Error getting customer insights: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if user can access customer data
     */
    private function canAccessCustomerData($user, $customerId)
    {
        // Super admin can access all
        if ($user->role === 'super_admin') {
            return true;
        }

        // Company admin can access customers from their companies
        if ($user->role === 'company_admin') {
            $companyId = $this->getCompanyIdForUser($user);
            if ($companyId) {
                $agencyIds = Agence::where('id_company', $companyId)->pluck('id_agence')->toArray();
                $busIds = Bus::whereIn('agency_id', $agencyIds)->pluck('bus_id')->toArray();
                $tripIds = Tips::whereIn('bus_id', $busIds)->pluck('trip_id')->toArray();

                return Ticket::where('customer_id', $customerId)
                    ->whereIn('trip_id', $tripIds)
                    ->exists();
            }
        }

        // Agency admin can access customers from their agency
        if ($user->role === 'agency_admin') {
            $agencyId = $this->getAgencyIdForUser($user);
            if ($agencyId) {
                $busIds = Bus::where('agency_id', $agencyId)->pluck('bus_id')->toArray();
                $tripIds = Tips::whereIn('bus_id', $busIds)->pluck('trip_id')->toArray();

                return Ticket::where('customer_id', $customerId)
                    ->whereIn('trip_id', $tripIds)
                    ->exists();
            }
        }

        // Customers can only access their own data
        if ($user->role === 'customer') {
            $customer = Customer::where('user_id', $user->user_id)->first();
            return $customer && $customer->customer_id == $customerId;
        }

        return false;
    }
}
