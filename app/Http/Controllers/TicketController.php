<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class TicketController extends Controller
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
        $query = Ticket::with(['trip.bus.agency', 'customer', 'journey']);

        if ($user->role === 'customer') {
            $query->where('customer_id', $user->customer->customer_id);
        } elseif ($user->role === 'agency_admin') {
            $query->whereIn('trip_id', function ($q) use ($user) {
                $q->select('t.trip_id')
                    ->from('trips as t')
                    ->join('buses as b', 'b.bus_id', '=', 't.bus_id')
                    ->where('b.agency_id', $user->agency_id);
            });
        } elseif ($user->role === 'company_admin') {
            $query->whereIn('trip_id', function ($q) use ($user) {
                $q->select('t.trip_id')
                    ->from('trips as t')
                    ->join('buses as b', 'b.bus_id', '=', 't.bus_id')
                    ->join('agencies as a', 'a.id_agence', '=', 'b.agency_id')
                    ->where('a.id_company', $user->company_id);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }

        $tickets = $query->orderBy('purchase_date', 'desc')->paginate(20);

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['trip.bus.agency', 'trip.journey.departureLocation.city',
                      'trip.journey.arrivalLocation.city', 'customer']);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $ticket->load(['trip.bus', 'customer']);

        return view('tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,used,cancelled,refunded',
            'seat_number' => 'required|integer|min:1',
        ]);

        $ticket->update([
            'status' => $request->status,
            'seat_number' => $request->seat_number,
        ]);

        // Update trip available seats if status changed
        if ($request->status === 'cancelled' && $ticket->status !== 'cancelled') {
            $ticket->trip->increment('available_seats');
        } elseif ($ticket->status === 'cancelled' && $request->status !== 'cancelled') {
            $ticket->trip->decrement('available_seats');
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->status !== 'cancelled') {
            $ticket->trip()->increment('available_seats');
        }

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    public function cancel(Request $request, Ticket $ticket)
    {
        // Check cancellation policy (e.g., within 24 hours of departure)
        $hoursBeforeDeparture = now()->diffInHours($ticket->trip->departure_date, false);

        if ($hoursBeforeDeparture < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel ticket within 2 hours of departure.'
            ], 422);
        }

        DB::transaction(function () use ($ticket) {
            $ticket->update(['status' => 'cancelled']);
            $ticket->trip()->increment('available_seats');
        });

        return response()->json([
            'success' => true,
            'message' => 'Ticket cancelled successfully. Seat has been made available.'
        ]);
    }

    public function print(Ticket $ticket)
    {
        $ticket->load(['trip.bus.agency', 'trip.journey.departureLocation.city',
                      'trip.journey.arrivalLocation.city', 'customer']);

        return view('tickets.print', compact('ticket'));
    }

    public function download(Ticket $ticket)
    {
        // PDF download logic would go here
        return $this->print($ticket);
    }

    /**
     * Show trip selection page for manual ticket sales (agency admins)
     */
    public function sell(Request $request)
    {
        $user = auth()->user();

        $query = Trip::with(['journey.departureLocation.city', 'journey.arrivalLocation.city', 'bus.agency'])
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0);

        // Filter by agency for agency admins
        if ($user->role === 'agency_admin') {
            $query->whereHas('bus', function ($q) use ($user) {
                $q->where('agency_id', $user->agency_id);
            });
        }
        // Filter by company for company admins
        elseif ($user->role === 'company_admin') {
            $query->whereHas('bus.agency', function ($q) use ($user) {
                $q->where('id_company', $user->company_id);
            });
        }

        // Search filters
        if ($request->has('from') && $request->from) {
            $query->whereHas('journey.departureLocation.city', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->from . '%');
            });
        }

        if ($request->has('to') && $request->to) {
            $query->whereHas('journey.arrivalLocation.city', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->to . '%');
            });
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('departure_date', $request->date);
        }

        $trips = $query->orderBy('departure_date')->orderBy('departure_time')->paginate(10);

        return view('tickets.sell', compact('trips'));
    }

    // Customer-specific routes
    public function myTickets()
    {
        $user = auth()->user();

        $tickets = Ticket::with(['trip.bus.agency', 'journey'])
            ->where('customer_id', $user->customer->customer_id)
            ->orderBy('purchase_date', 'desc')
            ->paginate(20);

        return view('tickets.my-tickets', compact('tickets'));
    }

    // Booking flow
    public function searchTrips(Request $request)
    {
        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'date' => 'required|date',
        ]);

        $trips = Trip::with(['journey.departureLocation.city', 'journey.arrivalLocation.city',
                           'bus.agency', 'bus'])
            ->whereHas('journey', function ($q) use ($request) {
                $q->whereHas('departureLocation.city', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->from . '%');
                })->whereHas('arrivalLocation.city', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->to . '%');
                });
            })
            ->whereDate('departure_date', $request->date)
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time')
            ->get();

        return view('tickets.search', compact('trips'));
    }

    public function selectSeats(Request $request, Trip $trip)
    {
        $trip->load(['bus', 'tickets']);

        $bookedSeats = $trip->tickets->where('status', '!=', 'cancelled')
            ->pluck('seat_number')->toArray();

        $seatPrice = $trip->initial_price;

        return view('tickets.select-seats', compact('trip', 'bookedSeats', 'seatPrice'));
    }

    public function confirmBooking(Request $request, Trip $trip)
    {
        $request->validate([
            'seat_numbers' => 'required|array',
            'seat_numbers.*' => 'required|integer|min:1',
        ]);

        $trip->load(['tickets']);

        // Check if seats are available
        $bookedSeats = $trip->tickets->where('status', '!=', 'cancelled')
            ->pluck('seat_number')->toArray();

        foreach ($request->seat_numbers as $seatNumber) {
            if (in_array($seatNumber, $bookedSeats)) {
                return redirect()->back()->with('error', "Seat {$seatNumber} is no longer available.");
            }
        }

        $totalPrice = $trip->initial_price * count($request->seat_numbers);

        return view('tickets.confirm', compact('trip', 'totalPrice'));
    }

    public function processPayment(Request $request, Trip $trip)
    {
        $request->validate([
            'seat_numbers' => 'required|array',
            'seat_numbers.*' => 'required|integer|min:1',
            'customer_id' => 'required|exists:customers,customer_id',
            'payment_method' => 'required|in:cash,card,mobile_money',
        ]);

        DB::transaction(function () use ($request, $trip) {
            $customer = Customer::findOrFail($request->customer_id);

            foreach ($request->seat_numbers as $seatNumber) {
                Ticket::create([
                    'booking_reference' => 'BK-' . strtoupper(Str::random(8)),
                    'trip_id' => $trip->trip_id,
                    'journey_id' => $trip->journey_id,
                    'customer_id' => $customer->customer_id,
                    'price' => $trip->initial_price,
                    'seat_number' => $seatNumber,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'purchase_date' => now(),
                ]);
            }

            $trip->decrement('available_seats', count($request->seat_numbers));
        });

        return redirect()->route('tickets.my')->with('success', 'Booking confirmed! Your tickets are ready.');
    }

    // Public routes
    public function verify($reference)
    {
        $ticket = Ticket::with(['trip.bus.agency', 'customer'])
            ->where('booking_reference', $reference)
            ->first();

        if (!$ticket) {
            return view('tickets.verify', ['error' => 'Ticket not found']);
        }

        return view('tickets.verify', compact('ticket'));
    }

    public function qrCode($reference)
    {
        $ticket = Ticket::where('booking_reference', $reference)->firstOrFail();

        // Generate QR code (this is a simplified version)
        $qrData = json_encode([
            'ref' => $ticket->booking_reference,
            'trip' => $ticket->trip_id,
            'seat' => $ticket->seat_number,
        ]);

        return view('tickets.qr', compact('ticket', 'qrData'));
    }
}

