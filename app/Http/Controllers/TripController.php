<?php

namespace App\Http\Controllers\Web;

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
        $user = auth()->user();
        $query = Tips::with(['journey', 'bus', 'bus.agency']);

        // Filter by agency based on user role
        if ($user->role === 'agency_admin') {
            $query->whereIn('bus_id', function ($q) use ($user) {
                $q->select('bus_id')
                    ->from('buses')
                    ->where('agency_id', $user->agency_id);
            });
        } elseif ($user->role === 'company_admin') {
            $query->whereIn('bus_id', function ($q) use ($user) {
                $q->select('b.bus_id')
                    ->from('buses as b')
                    ->join('agencies as a', 'a.id_agence', '=', 'b.agency_id')
                    ->where('a.id_company', $user->company_id);
            });
        }

        if ($request->has('departure_date')) {
            $query->whereDate('departure_date', $request->departure_date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tips = $query->orderBy('departure_date', 'desc')->paginate(20);

        return view('trips.index', compact('trips'));
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

