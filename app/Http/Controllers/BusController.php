<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use App\Models\Bus;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class BusController extends Controller
{
    /**
     * Display a listing of buses.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Bus::with(['agency.company', 'agency.coordinates.city']);

        // Filter by agency based on user role
        if ($user->role === 'agency_admin' && $user->agency_id) {
            $query->where('agency_id', $user->agency_id);
        } elseif ($user->role === 'company_admin' && $user->company_id) {
            $query->whereHas('agency', function ($q) use ($user) {
                $q->where('id_company', $user->company_id);
            });
        }

        // Filter by agency if specified
        if ($request->has('agency_id') && $request->agency_id) {
            $query->where('agency_id', $request->agency_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $buses = $query->withCount(['trips'])
            ->orderBy('registration_number')
            ->paginate(10);

        $companies = Company::orderBy('name')->get();
        $agencies = Agence::orderBy('name')->get();

        return view('buses.index', compact('buses', 'agencies', 'companies'));
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
