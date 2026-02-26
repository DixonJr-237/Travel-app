<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Bus;
use App\Models\Coordinate;
use App\Models\Customer;
use App\Models\Journey;
use App\Models\Tips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log; // Add this line
use Carbon\Carbon; // Add this if you're using Carbon

class TripController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
{
    try {
        $user = auth()->user();

        // Start with the Tips model (not Trip)
        $query = Tips::with([
            'journey.departureLocation.city',
            'journey.arrivalLocation.city',
            'bus.agency.company'
        ]);

        // Filter by agency based on user role
        if ($user->role === 'agency_admin') {
            $agencyId = $user->agency_id ?? null;

            if (!$agencyId) {
                Log::warning('Agency admin has no agency_id', ['user_id' => $user->user_id]);
                $tips = collect([]); // Empty collection
                return view('trips.index', compact('tips'))->with('error', 'No agency associated with your account');
            }

            $query->whereHas('bus', function ($q) use ($agencyId) {
                $q->where('agency_id', $agencyId);
            });

        } elseif ($user->role === 'company_admin') {
            $companyId = $user->company_id ?? null;

            if (!$companyId) {
                Log::warning('Company admin has no company_id', ['user_id' => $user->user_id]);
                $tips = collect([]);
                return view('trips.index', compact('tips'))->with('error', 'No company associated with your account');
            }

            $query->whereHas('bus.agency', function ($q) use ($companyId) {
                $q->where('id_company', $companyId);
            });
        }

        // Apply filters with validation
        if ($request->has('departure_date') && !empty($request->departure_date)) {
            try {
                $date = Carbon::parse($request->departure_date)->format('Y-m-d');
                $query->whereDate('departure_date', $date);
            } catch (\Exception $e) {
                Log::warning('Invalid date format', ['date' => $request->departure_date]);
                // Continue without date filter
            }
        }

        if ($request->has('status') && !empty($request->status)) {
            $validStatuses = ['scheduled', 'in_progress', 'completed', 'cancelled', 'active'];
            if (in_array($request->status, $validStatuses)) {
                $query->where('status', $request->status);
            }
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('bus', function ($busQuery) use ($search) {
                    $busQuery->where('registration_number', 'LIKE', "%{$search}%")
                        ->orWhere('bus_number', 'LIKE', "%{$search}%");
                })->orWhereHas('journey.departureLocation.city', function ($cityQuery) use ($search) {
                    $cityQuery->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('journey.arrivalLocation.city', function ($cityQuery) use ($search) {
                    $cityQuery->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            try {
                $query->whereDate('departure_date', '>=', Carbon::parse($request->date_from));
            } catch (\Exception $e) {
                // Ignore invalid date
            }
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            try {
                $query->whereDate('departure_date', '<=', Carbon::parse($request->date_to));
            } catch (\Exception $e) {
                // Ignore invalid date
            }
        }

        // Get paginated results
        $tips = $query->orderBy('departure_date', 'desc')
            ->orderBy('departure_time', 'desc')
            ->paginate(20)
            ->withQueryString(); // Preserve query parameters in pagination links

        // Add additional data for the view
        $statusCounts = [
            'total' => Tips::count(),
            'scheduled' => Tips::where('status', 'scheduled')->count(),
            'in_progress' => Tips::where('status', 'in_progress')->count(),
            'completed' => Tips::where('status', 'completed')->count(),
            'cancelled' => Tips::where('status', 'cancelled')->count(),
        ];

        return view('trips.index', [
            'tips' => $tips,
            'filters' => $request->only(['departure_date', 'status', 'search', 'date_from', 'date_to']),
            'statusCounts' => $statusCounts,
            'userRole' => $user->role,
        ]);

    } catch (\Exception $e) {
        // Log the error with details
        Log::error('Error in TripController@index', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id() ?? null,
            'url' => $request->fullUrl()
        ]);

        // Return error view or empty results
        return view('trips.index', [
            'tips' => collect([]),
            'error' => 'An error occurred while loading trips. Please try again.'
        ])->withErrors(['error' => 'Unable to load trips. Please try again later.']);
    }
}
    public function create()
    {
        $user = auth()->user();
        $buses = collect();
        $journeys = collect();

        if ($user->role === 'super_admin') {
            $buses = Bus::with('agency')->get();
            $journeys = Journey::with(['departureLocation', 'arrivalLocation'])->get();
        } elseif ($user->role === 'company_admin') {
            $buses = Bus::whereIn('agency_id', function ($q) use ($user) {
                $q->select('id_agence')
                    ->from('agencies')
                    ->where('id_company', $user->company_id);
            })->with('agency')->get();
            $journeys = Journey::all();
        } elseif ($user->role === 'agency_admin') {
            $buses = Bus::where('agency_id', $user->agency_id)->with('agency')->get();
            $journeys = Journey::all();
        }

        return view('trips.create', compact('buses', 'journeys'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Get bus and determine journey coordinates
        $bus = Bus::findOrFail($request->bus_id);
        $journey = Journey::findOrFail($request->journey_id);

        $tips = Tips::create([
            'departure_date' => $request->departure_date,
            'departure_time' => $request->departure_time,
            'initial_price' => $request->initial_price,
            'available_seats' => $request->available_seats ?? $bus->seats_count,
            'bus_id' => $request->bus_id,
            'journey_id' => $request->journey_id,
            'departure_location_coord_id' => $journey->departure_location_coord_id,
            'arrival_location_coord_id' => $journey->arrival_location_coord_id,
            'status' => $request->status ?? 'scheduled',
        ]);

        return redirect()->route('trips.show', $tips)
            ->with('success', 'Trip created successfully.');
    }

    public function show(Tips $trip)
    {
        $trip->load(['journey.departureLocation', 'journey.arrivalLocation', 'bus.agency', 'tickets.customer']);

        $stats = [
            'total_seats' => $trip->available_seats + $trip->tickets->count(),
            'booked_seats' => $trip->tickets->count(),
            'available_seats' => $trip->available_seats,
            'total_revenue' => $trip->tickets->sum('price'),
        ];

        return view('trips.show', compact('trip', 'stats'));
    }



    public function edit(Tips $trip)
    {
        $user = auth()->user();
        $buses = collect();
        $journeys = collect();

        if ($user->role === 'super_admin') {
            $buses = Bus::with('agency')->get();
            $journeys = Journey::with(['departureLocation', 'arrivalLocation'])->get();
        } elseif ($user->role === 'company_admin') {
            $buses = Bus::whereIn('agency_id', function ($q) use ($user) {
                $q->select('id_agence')
                    ->from('agencies')
                    ->where('id_company', $user->company_id);
            })->with('agency')->get();
            $journeys = Journey::all();
        } elseif ($user->role === 'agency_admin') {
            $buses = Bus::where('agency_id', $user->agency_id)->with('agency')->get();
            $journeys = Journey::all();
        }

        return view('trips.edit', compact('trip', 'buses', 'journeys'));
    }

    public function update(Request $request, Tips $trip)
    {
        $journey = Journey::findOrFail($request->journey_id);

        $trip->update([
            'departure_date' => $request->departure_date,
            'departure_time' => $request->departure_time,
            'initial_price' => $request->initial_price,
            'available_seats' => $request->available_seats,
            'bus_id' => $request->bus_id,
            'journey_id' => $request->journey_id,
            'departure_location_coord_id' => $journey->departure_location_coord_id,
            'arrival_location_coord_id' => $journey->arrival_location_coord_id,
            'status' => $request->status,
        ]);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Trip updated successfully.');
    }

    public function destroy(Tips $trip)
    {
        if ($trip->tickets()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete trip with existing bookings.');
        }

        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully.');
    }

    public function updateStatus(Request $request, Tips $trip)
    {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $trip->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Trip status updated successfully.');
    }

    public function seats(Tips $trip)
    {
        $trip->load(['bus', 'tickets']);

        $bookedSeats = $trip->tickets->pluck('seat_number')->toArray();

        return view('trips.seats', compact('trip', 'bookedSeats'));
    }

    public function updateSeats(Request $request, Tips $trip)
    {
        $request->validate([
            'available_seats' => 'required|integer|min:0',
        ]);

        $trip->update(['available_seats' => $request->available_seats]);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Seats updated successfully.');
    }

    public function passengers(Tips $trip)
    {
        $trip->load(['tickets.customer']);

        return view('trips.passengers', compact('trip'));
    }

    public function createSchedule()
    {
        $user = auth()->user();
        $buses = collect();
        $journeys = collect();

        if ($user->role === 'agency_admin') {
            $buses = Bus::where('agency_id', $user->agency_id)->get();
            $journeys = Journey::all();
        }

        return view('trips.schedule', compact('buses', 'journeys'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,bus_id',
            'journey_id' => 'required|exists:journeys,journey_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'departure_time' => 'required',
            'initial_price' => 'required|numeric',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $user = auth()->user();
        $journey = Journey::findOrFail($request->journey_id);
        $bus = Bus::findOrFail($request->bus_id);

        // Generate trips for the date range
        $currentDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $tripsCreated = 0;

        while ($currentDate->lte($endDate)) {
            // Check if trip already exists for this date and bus
            $exists = Tips::where('bus_id', $request->bus_id)
                ->where('departure_date', $currentDate->toDateString())
                ->exists();

            if (!$exists) {
                Tips::create([
                    'departure_date' => $currentDate->toDateString(),
                    'departure_time' => $request->departure_time,
                    'initial_price' => $request->initial_price,
                    'available_seats' => $bus->seats_count,
                    'bus_id' => $request->bus_id,
                    'journey_id' => $request->journey_id,
                    'departure_location_coord_id' => $journey->departure_location_coord_id,
                    'arrival_location_coord_id' => $journey->arrival_location_coord_id,
                    'status' => $request->status,
                ]);
                $tripsCreated++;
            }

            $currentDate->addDay();
        }

        return redirect()->route('trips.index')
            ->with('success', "Schedule created successfully. {$tripsCreated} trips generated.");
    }

    // Public search methods
    public function publicSearch(Request $request)
    {
        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'date' => 'required|date',
        ]);

        $fromCoord = Coordinate::whereHas('city', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->from . '%');
        })->first();

        $toCoord = Coordinate::whereHas('city', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->to . '%');
        })->first();

        if (!$fromCoord || !$toCoord) {
            return redirect()->back()->with('error', 'Location not found.');
        }

        $trips = Tips::with(['journey.departureLocation.city', 'journey.arrivalLocation.city', 'bus.agency'])
            ->where('departure_location_coord_id', $fromCoord->id_coord)
            ->where('arrival_location_coord_id', $toCoord->id_coord)
            ->whereDate('departure_date', $request->date)
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time')
            ->paginate(20);

        return view('trips.search', compact('trips'));
    }

    public function publicSchedule(Request $request)
    {
        $fromCoordId = $request->from;
        $toCoordId = $request->to;
        $date = $request->date ?? now()->toDateString();

        $trips = collect();

        if ($fromCoordId && $toCoordId) {
            $trips = Tips::with(['journey.departureLocation.city', 'journey.arrivalLocation.city', 'bus.agency'])
                ->where('departure_location_coord_id', $fromCoordId)
                ->where('arrival_location_coord_id', $toCoordId)
                ->whereDate('departure_date', $date)
                ->where('status', 'scheduled')
                ->where('available_seats', '>', 0)
                ->orderBy('departure_time')
                ->get();
        }

        $coordinates = Coordinate::with('city')->get();

        return view('trips.schedule-public', compact('trips', 'coordinates'));
    }
}

