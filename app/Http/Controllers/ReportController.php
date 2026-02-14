<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Bus;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Display reports index.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * System reports (Super Admin).
     */
    public function system()
    {
        $stats = [
            'total_companies' => Company::count(),
            'total_agencies' => Agency::count(),
            'total_buses' => Bus::count(),
            'total_trips' => Trip::count(),
            'total_tickets' => Ticket::count(),
        ];

        return view('reports.system', compact('stats'));
    }

    /**
     * Companies performance report.
     */
    public function companiesPerformance()
    {
        $companies = Company::with(['agencies.buses.trips.tickets'])
            ->get()
            ->map(function ($company) {
                $totalTrips = 0;
                $totalTickets = 0;
                $totalRevenue = 0;

                foreach ($company->agencies as $agency) {
                    foreach ($agency->buses as $bus) {
                        foreach ($bus->trips as $trip) {
                            $totalTrips++;
                            foreach ($trip->tickets as $ticket) {
                                $totalTickets++;
                                $totalRevenue += $ticket->price;
                            }
                        }
                    }
                }

                return [
                    'name' => $company->name,
                    'agencies_count' => $company->agencies->count(),
                    'total_trips' => $totalTrips,
                    'total_tickets' => $totalTickets,
                    'total_revenue' => $totalRevenue,
                ];
            });

        return view('reports.companies-performance', compact('companies'));
    }

    /**
     * Company report (Company Admin).
     */
    public function company()
    {
        $user = auth()->user();
        
        if (!$user->company_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any company.');
        }

        $company = Company::with(['agencies.buses.trips.tickets', 'agencies.tickets'])
            ->findOrFail($user->company_id);

        $stats = [
            'total_agencies' => $company->agencies->count(),
            'total_buses' => $company->agencies->sum(function ($a) {
                return $a->buses->count();
            }),
            'total_trips' => $company->agencies->sum(function ($a) {
                return $a->buses->sum(function ($b) {
                    return $b->trips->count();
                });
            }),
            'total_tickets' => $company->agencies->sum(function ($a) {
                return $a->tickets->count();
            }),
            'total_revenue' => $company->agencies->sum(function ($a) {
                return $a->tickets->sum('price');
            }),
        ];

        return view('reports.company', compact('company', 'stats'));
    }

    /**
     * Agencies performance report.
     */
    public function agenciesPerformance()
    {
        $user = auth()->user();
        
        if (!$user->company_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any company.');
        }

        $agencies = Agency::where('id_company', $user->company_id)
            ->with(['buses.trips.tickets', 'tickets'])
            ->get()
            ->map(function ($agency) {
                $totalTrips = 0;
                $totalTickets = 0;
                $totalRevenue = 0;

                foreach ($agency->buses as $bus) {
                    foreach ($bus->trips as $trip) {
                        $totalTrips++;
                        foreach ($trip->tickets as $ticket) {
                            $totalTickets++;
                            $totalRevenue += $ticket->price;
                        }
                    }
                }

                return [
                    'name' => $agency->name,
                    'buses_count' => $agency->buses->count(),
                    'total_trips' => $totalTrips,
                    'total_tickets' => $totalTickets,
                    'total_revenue' => $totalRevenue,
                ];
            });

        return view('reports.agencies-performance', compact('agencies'));
    }

    /**
     * Agency report (Agency Admin).
     */
    public function agency()
    {
        $user = auth()->user();
        
        if (!$user->agency_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any agency.');
        }

        $agency = Agency::with(['buses.trips.tickets', 'buses.trips', 'activities'])
            ->findOrFail($user->agency_id);

        $stats = [
            'total_buses' => $agency->buses->count(),
            'active_buses' => $agency->buses->where('status', 'active')->count(),
            'total_trips' => $agency->buses->sum(function ($b) {
                return $b->trips->count();
            }),
            'total_tickets' => $agency->buses->sum(function ($b) {
                return $b->trips->sum(function ($t) {
                    return $t->tickets->count();
                });
            }),
            'total_revenue' => $agency->buses->sum(function ($b) {
                return $b->trips->sum(function ($t) {
                    return $t->tickets->sum('price');
                });
            }),
        ];

        return view('reports.agency', compact('agency', 'stats'));
    }

    /**
     * Financial report.
     */
    public function financial(Request $request)
    {
        $user = auth()->user();
        
        $startDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        if ($user->agency_id) {
            $buses = Bus::where('agency_id', $user->agency_id)->pluck('bus_id');
            $trips = Trip::whereIn('bus_id', $buses)
                ->whereBetween('departure_date', [$startDate, $endDate])
                ->with(['tickets', 'bus.agency'])
                ->get();
        } elseif ($user->company_id) {
            $agencies = Agency::where('id_company', $user->company_id)->pluck('id_agence');
            $buses = Bus::whereIn('agency_id', $agencies)->pluck('bus_id');
            $trips = Trip::whereIn('bus_id', $buses)
                ->whereBetween('departure_date', [$startDate, $endDate])
                ->with(['tickets', 'bus.agency'])
                ->get();
        } else {
            $trips = Trip::whereBetween('departure_date', [$startDate, $endDate])
                ->with(['tickets', 'bus.agency'])
                ->get();
        }

        $totalRevenue = $trips->sum(function ($trip) {
            return $trip->tickets->sum('price');
        });

        $totalTickets = $trips->sum(function ($trip) {
            return $trip->tickets->count();
        });

        return view('reports.financial', compact('trips', 'totalRevenue', 'totalTickets', 'startDate', 'endDate'));
    }

    /**
     * Operational report.
     */
    public function operational(Request $request)
    {
        $user = auth()->user();
        
        $startDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        if ($user->agency_id) {
            $buses = Bus::where('agency_id', $user->agency_id)->pluck('bus_id');
            $trips = Trip::whereIn('bus_id', $buses)
                ->whereBetween('departure_date', [$startDate, $endDate])
                ->with(['tickets', 'bus'])
                ->get();
        } elseif ($user->company_id) {
            $agencies = Agency::where('id_company', $user->company_id)->pluck('id_agence');
            $buses = Bus::whereIn('agency_id', $agencies)->pluck('bus_id');
            $trips = Trip::whereIn('bus_id', $buses)
                ->whereBetween('departure_date', [$startDate, $endDate])
                ->with(['tickets', 'bus'])
                ->get();
        } else {
            $trips = Trip::whereBetween('departure_date', [$startDate, $endDate])
                ->with(['tickets', 'bus'])
                ->get();
        }

        $stats = [
            'total_trips' => $trips->count(),
            'completed_trips' => $trips->where('status', 'completed')->count(),
            'cancelled_trips' => $trips->where('status', 'cancelled')->count(),
            'total_bookings' => $trips->sum(function ($trip) {
                return $trip->tickets->count();
            }),
            'cancellation_rate' => $trips->count() > 0 
                ? round(($trips->where('status', 'cancelled')->count() / $trips->count()) * 100, 2)
                : 0,
        ];

        return view('reports.operational', compact('trips', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Customer report.
     */
    public function customer(Request $request)
    {
        $user = auth()->user();
        
        $startDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        if ($user->agency_id) {
            $buses = Bus::where('agency_id', $user->agency_id)->pluck('bus_id');
            $tickets = Ticket::whereIn('trip_id', function ($query) use ($buses) {
                $query->select('trip_id')->from('trips')->whereIn('bus_id', $buses);
            })->whereBetween('purchase_date', [$startDate, $endDate])
                ->with(['customer', 'trip.bus'])
                ->get();
        } elseif ($user->company_id) {
            $agencies = Agency::where('id_company', $user->company_id)->pluck('id_agence');
            $buses = Bus::whereIn('agency_id', $agencies)->pluck('bus_id');
            $tickets = Ticket::whereIn('trip_id', function ($query) use ($buses) {
                $query->select('trip_id')->from('trips')->whereIn('bus_id', $buses);
            })->whereBetween('purchase_date', [$startDate, $endDate])
                ->with(['customer', 'trip.bus'])
                ->get();
        } else {
            $tickets = Ticket::whereBetween('purchase_date', [$startDate, $endDate])
                ->with(['customer', 'trip.bus'])
                ->get();
        }

        $stats = [
            'total_bookings' => $tickets->count(),
            'total_revenue' => $tickets->sum('price'),
            'avg_ticket_price' => $tickets->count() > 0 ? round($tickets->sum('price') / $tickets->count()) : 0,
            'cancelled_tickets' => $tickets->where('status', 'cancelled')->count(),
        ];

        return view('reports.customer', compact('tickets', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Generate report.
     */
    public function generate(Request $request)
    {
        $type = $request->get('type', 'summary');
        $format = $request->get('format', 'html');

        // Generate report based on type
        $data = []; // Report data would go here

        if ($format === 'pdf') {
            // Return PDF download
            return Response::download($path, $filename);
        }

        return view('reports.generate', compact('data', 'type'));
    }

    /**
     * Export report.
     */
    public function export(Request $request, $type)
    {
        // Export logic would go here
        return redirect()->back()->with('success', 'Report exported successfully.');
    }

    /**
     * Download report file.
     */
    public function download($filename)
    {
        $path = storage_path('reports/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Report not found.');
        }

        return Response::download($path);
    }
}
