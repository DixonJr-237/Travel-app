<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Ticket;
use App\Models\Tips;
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
            'last_updated' => now()->toDateTimeString()
        ];

        switch ($role) {
            case 'super_admin':
                return array_merge($emptyData, [
                    'total_companies' => 0,
                    'total_agencies' => 0,
                    'total_trips' => 0,
                    'total_customers' => 0,
                    'recent_trips' => collect([]),
                    'revenue_data' => collect([]),
                ]);

            case 'company_admin':
                return array_merge($emptyData, [
                    'total_agencies' => 0,
                    'total_buses' => 0,
                    'total_trips' => 0,
                    'monthly_revenue' => 0,
                    'agency_stats' => collect([]),
                ]);

            case 'agency_admin':
                return array_merge($emptyData, [
                    'total_buses' => 0,
                    'active_trips' => 0,
                    'today_trips' => 0,
                    'monthly_tickets' => 0,
                    'recent_bookings' => collect([]),
                ]);

            case 'customer':
                return array_merge($emptyData, [
                    'upcoming_trips' => collect([]),
                    'past_trips' => collect([]),
                    'total_tickets' => 0,
                ]);

            default:
                return $emptyData;
        }
    }

    private function getDashboardDataForUser($user)
    {
        $dashboardData = [
            'stats' => [],
            'last_updated' => now()->toDateTimeString()
        ];

        try {
            switch ($user->role) {
                case 'super_admin':
                    return array_merge($dashboardData, $this->getSuperAdminData());

                case 'company_admin':
                    return array_merge($dashboardData, $this->getCompanyAdminData($user));

                case 'agency_admin':
                    return array_merge($dashboardData, $this->getAgencyAdminData($user));

                case 'customer':
                    return array_merge($dashboardData, $this->getCustomerData($user));

                default:
                    return $dashboardData;
            }
        } catch (\Exception $e) {
            Log::error('Error in getDashboardDataForUser: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'role' => $user->role,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getEmptyDashboardData($user->role);
        }
    }

   private function getSuperAdminData()
{
    $cacheKey = 'super_admin_dashboard_stats_v3';

    try {
        return Cache::remember($cacheKey, 300, function () {
            // Get all counts in optimized queries
            $counts = DB::selectOne("
                SELECT
                    (SELECT COUNT(*) FROM companies) as total_companies,
                    (SELECT COUNT(*) FROM agencies) as total_agencies,
                    (SELECT COUNT(*) FROM customers) as total_customers,
                    (SELECT COUNT(*) FROM tickets) as total_tickets,
                    (SELECT COUNT(*) FROM tips WHERE status = 'scheduled') as total_trips
            ");

            // Get recent trips with relationships
            $recentTrips = Tips::with([
                'bus.agence:id_agence,name',
                'journey.departureLocation.city:id_city,name',
                'journey.arrivalLocation.city:id_city,name'
            ])
            ->where('status', 'scheduled')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($trip) {
                return (object) [
                    'departureLocation' => (object) [
                        'city' => (object) [
                            'name' => $trip->journey->departureLocation->city->name ?? 'Unknown'
                        ]
                    ],
                    'arrivalLocation' => (object) [
                        'city' => (object) [
                            'name' => $trip->journey->arrivalLocation->city->name ?? 'Unknown'
                        ]
                    ],
                    'departure_date' => $trip->departure_time?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'departure_time' => $trip->departure_time?->format('H:i') ?? '00:00',
                    'bus' => (object) [
                        'registration_number' => $trip->bus->registration_number ?? 'N/A',
                        'agency' => (object) [
                            'name' => $trip->bus->agence->name ?? 'N/A'
                        ]
                    ],
                    'status' => $trip->status ?? 'scheduled',
                ];
            });

            // Get revenue data for chart
            $revenueData = DB::table('tickets')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COALESCE(SUM(price), 0) as revenue')
                )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'date' => Carbon::parse($item->date)->format('M d'),
                        'revenue' => (float) $item->revenue
                    ];
                });

            return [
                'stats' => [
                    'total_companies' => (int) ($counts->total_companies ?? 0),
                    'total_agencies' => (int) ($counts->total_agencies ?? 0),
                    'total_trips' => (int) ($counts->total_trips ?? 0),
                    'total_customers' => (int) ($counts->total_customers ?? 0),
                    'total_tickets' => (int) ($counts->total_tickets ?? 0),
                ],
                'total_companies' => (int) ($counts->total_companies ?? 0),
                'total_agencies' => (int) ($counts->total_agencies ?? 0),
                'total_trips' => (int) ($counts->total_trips ?? 0),
                'total_customers' => (int) ($counts->total_customers ?? 0),
                'total_tickets' => (int) ($counts->total_tickets ?? 0),
                'recent_trips' => $recentTrips,
                'revenue_data' => $revenueData,
            ];
        });
    } catch (\Exception $e) {
        Log::error('Error in getSuperAdminData: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id()
        ]);

        return [
            'stats' => [
                'total_companies' => 0,
                'total_agencies' => 0,
                'total_trips' => 0,
                'total_customers' => 0,
                'total_tickets' => 0,
            ],
            'total_companies' => 0,
            'total_agencies' => 0,
            'total_trips' => 0,
            'total_customers' => 0,
            'total_tickets' => 0,
            'recent_trips' => collect([]),
            'revenue_data' => collect([]),
        ];
    }
}
    private function getCompanyAdminData($user)
    {
        try {
            // Get the company ID from user
            $companyId = $user->company_id;

            if (!$companyId) {
                Log::warning('Company admin has no company_id', ['user_id' => $user->user_id]);
                return [
                    'total_agencies' => 0,
                    'total_buses' => 0,
                    'total_trips' => 0,
                    'monthly_revenue' => 0,
                    'agency_stats' => collect([]),
                ];
            }

            $cacheKey = "company_admin_data_{$companyId}";

            return Cache::remember($cacheKey, 300, function () use ($companyId) {
                // Get all agencies under this company
                $agencies = Agence::where('id_company', $companyId)->get();
                $agencyIds = $agencies->pluck('id_agence')->toArray();

                if (empty($agencyIds)) {
                    return [
                        'total_agencies' => 0,
                        'total_buses' => 0,
                        'total_trips' => 0,
                        'monthly_revenue' => 0,
                        'agency_stats' => collect([]),
                    ];
                }

                // Get all buses from these agencies
                $busIds = DB::table('buses')
                    ->whereIn('agency_id', $agencyIds)
                    ->pluck('bus_id')
                    ->toArray();

                // Get stats in a single optimized query
                $stats = DB::selectOne("
                    SELECT
                        COUNT(DISTINCT a.id_agence) as total_agencies,
                        COUNT(DISTINCT b.bus_id) as total_buses,
                        COUNT(DISTINCT t.trip_id) as total_trips,
                        COALESCE(SUM(tk.price), 0) as monthly_revenue
                    FROM agencies a
                    LEFT JOIN buses b ON b.agency_id = a.id_agence
                    LEFT JOIN tips t ON t.bus_id = b.bus_id AND t.status = 'scheduled'
                    LEFT JOIN tickets tk ON tk.trip_id = t.trip_id
                        AND MONTH(tk.purchase_date) = MONTH(CURRENT_DATE())
                        AND YEAR(tk.purchase_date) = YEAR(CURRENT_DATE())
                    WHERE a.id_company = ?
                ", [$companyId]);

                // Agency performance stats
                $agencyStats = [];
                foreach ($agencies as $agency) {
                    $agencyBusIds = DB::table('buses')
                        ->where('agency_id', $agency->id_agence)
                        ->pluck('bus_id')
                        ->toArray();

                    $tripCount = Tips::whereIn('bus_id', $agencyBusIds)->count();

                    $ticketCount = DB::table('tickets')
                        ->whereIn('trip_id', function($query) use ($agencyBusIds) {
                            $query->select('trip_id')
                                ->from('tips')
                                ->whereIn('bus_id', $agencyBusIds);
                        })->count();

                    $agencyStats[] = [
                        'name' => $agency->name,
                        'trip_count' => $tripCount,
                        'ticket_count' => $ticketCount,
                    ];
                }

                return [
                    'total_agencies' => (int) ($stats->total_agencies ?? count($agencyIds)),
                    'total_buses' => (int) ($stats->total_buses ?? 0),
                    'total_trips' => (int) ($stats->total_trips ?? 0),
                    'monthly_revenue' => (float) ($stats->monthly_revenue ?? 0),
                    'agency_stats' => collect($agencyStats),
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getCompanyAdminData: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'total_agencies' => 0,
                'total_buses' => 0,
                'total_trips' => 0,
                'monthly_revenue' => 0,
                'agency_stats' => collect([]),
            ];
        }
    }

    private function getAgencyAdminData($user)
    {
        try {
            // Get the agency ID from user
            $agencyId = $user->agency_id;

            if (!$agencyId) {
                Log::warning('Agency admin has no agency_id', ['user_id' => $user->user_id]);
                return [
                    'total_buses' => 0,
                    'active_trips' => 0,
                    'today_trips' => 0,
                    'monthly_tickets' => 0,
                    'recent_bookings' => collect([]),
                ];
            }

            $cacheKey = "agency_admin_data_{$agencyId}";

            return Cache::remember($cacheKey, 180, function () use ($agencyId) {
                // Get all buses for this agency
                $buses = DB::table('buses')
                    ->where('agency_id', $agencyId)
                    ->get();

                $busIds = $buses->pluck('bus_id')->toArray();

                if (empty($busIds)) {
                    return [
                        'total_buses' => 0,
                        'active_trips' => 0,
                        'today_trips' => 0,
                        'monthly_tickets' => 0,
                        'recent_bookings' => collect([]),
                    ];
                }

                // Get trip statistics
                $today = Carbon::today();

                $activeTrips = Tips::whereIn('bus_id', $busIds)
                    ->where('status', 'scheduled')
                    ->where('departure_date', '>=', $today)
                    ->count();

                $todayTrips = Tips::whereIn('bus_id', $busIds)
                    ->whereDate('departure_date', $today)
                    ->count();

                // Get monthly tickets
                $monthlyTickets = DB::table('tickets')
                    ->whereIn('trip_id', function($query) use ($busIds) {
                        $query->select('trip_id')
                            ->from('tips')
                            ->whereIn('bus_id', $busIds);
                    })
                    ->whereMonth('purchase_date', Carbon::now()->month)
                    ->whereYear('purchase_date', Carbon::now()->year)
                    ->count();

                // Get recent bookings with proper relationships
                $recentBookings = Ticket::query()
                    ->with([
                        'customer:customer_id,first_name,last_name,phone',
                        'trip:journey_id,trip_id,departure_date,departure_time',
                        'trip.journey.departureLocation.city',
                        'trip.journey.arrivalLocation.city'
                    ])
                    ->whereHas('trip', function($query) use ($busIds) {
                        $query->whereIn('bus_id', $busIds);
                    })
                    ->orderBy('purchase_date', 'desc')
                    ->limit(10)
                    ->get();

                return [
                    'total_buses' => count($busIds),
                    'active_trips' => $activeTrips,
                    'today_trips' => $todayTrips,
                    'monthly_tickets' => $monthlyTickets,
                    'recent_bookings' => $recentBookings,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getAgencyAdminData: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'total_buses' => 0,
                'active_trips' => 0,
                'today_trips' => 0,
                'monthly_tickets' => 0,
                'recent_bookings' => collect([]),
            ];
        }
    }

    private function getCustomerData($user)
    {
        try {
            // Get the customer record associated with the user
            $customer = Customer::where('user_id', $user->user_id)->first();

            if (!$customer) {
                return [
                    'upcoming_trips' => collect([]),
                    'past_trips' => collect([]),
                    'total_tickets' => 0,
                ];
            }

            $cacheKey = "customer_data_{$customer->customer_id}";

            return Cache::remember($cacheKey, 120, function () use ($customer) {
                $today = Carbon::today();

                // Get all tickets for this customer
                $tickets = Ticket::where('customer_id', $customer->customer_id)
                    ->with([
                        'trip.journey.departureLocation.city',
                        'trip.journey.arrivalLocation.city',
                        'trip.bus.agence'
                    ])
                    ->orderBy('purchase_date', 'desc')
                    ->get();

                // Separate upcoming and past trips
                $upcomingTrips = $tickets->filter(function ($ticket) use ($today) {
                    return $ticket->trip &&
                           $ticket->trip->departure_date >= $today->toDateString() &&
                           $ticket->status !== 'cancelled';
                })->values();

                $pastTrips = $tickets->filter(function ($ticket) use ($today) {
                    return !$ticket->trip ||
                           $ticket->trip->departure_date < $today->toDateString() ||
                           $ticket->status === 'cancelled';
                })->values();

                return [
                    'upcoming_trips' => $upcomingTrips,
                    'past_trips' => $pastTrips,
                    'total_tickets' => $tickets->count(),
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getCustomerData: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'upcoming_trips' => collect([]),
                'past_trips' => collect([]),
                'total_tickets' => 0,
            ];
        }
    }

    // ... (keep all your other methods like notifications, search, settings, etc. as they are)

    /**
     * Static method to clear dashboard cache (can be called from other controllers)
     */
    public static function clearDashboardCache($userId, $role, $relatedId = null)
    {
        try {
            $cacheKey = "dashboard_{$role}_{$userId}";
            Cache::forget($cacheKey);

            // Clear role-specific caches
            switch ($role) {
                case 'super_admin':
                    Cache::forget('super_admin_dashboard_stats');
                    break;
                case 'company_admin':
                    if ($relatedId) {
                        Cache::forget("company_admin_data_{$relatedId}");
                    }
                    break;
                case 'agency_admin':
                    if ($relatedId) {
                        Cache::forget("agency_admin_data_{$relatedId}");
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
}
