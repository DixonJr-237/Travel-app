<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Customer::with(['user']);

        // Filter by company for company admins
        if ($user->role === 'company_admin' && $user->company_id) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            })->orWhereHas('tickets.trip.bus.agency', function ($q) use ($user) {
                $q->where('id_company', $user->company_id);
            });
        }

        // Filter by agency for agency admins
        if ($user->role === 'agency_admin' && $user->agency_id) {
            $query->whereHas('tickets.trip.bus', function ($q) use ($user) {
                $q->where('agency_id', $user->agency_id);
            });
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount(['tickets'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    /**
     * Display customer details.
     */
    public function show(Customer $customer)
    {
        $customer->load(['user', 'tickets.trip.bus.agency', 'tickets.journey']);

        // Get stats
        $totalTrips = $customer->tickets->count();
        $totalSpent = $customer->tickets->sum('price');
        $upcomingTrips = $customer->tickets->filter(function ($ticket) {
            return $ticket->trip->departure_date >= now() && $ticket->status !== 'cancelled';
        })->count();

        $stats = [
            'total_trips' => $totalTrips,
            'total_spent' => $totalSpent,
            'upcoming_trips' => $upcomingTrips,
        ];

        return view('customers.show', compact('customer', 'stats'));
    }

    /**
     * Show the form for editing a customer.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->customer_id . ',customer_id',
        ]);

        $customer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        // Update user if exists
        if ($customer->user) {
            $customer->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'phone' => $request->phone,
            ]);
        }

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the customer.
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has tickets
        if ($customer->tickets()->exists()) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer with existing tickets.');
        }

        // Delete user if exists
        if ($customer->user) {
            $customer->user->delete();
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Display customer's tickets.
     */
    public function tickets(Request $request, Customer $customer)
    {
        $query = Ticket::with(['trip.bus.agency', 'journey'])
            ->where('customer_id', $customer->customer_id);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $tickets = $query->orderBy('purchase_date', 'desc')
            ->paginate(10);

        return view('customers.tickets', compact('customer', 'tickets'));
    }

    /**
     * Display customer's travel history.
     */
    public function history(Request $request, Customer $customer)
    {
        $query = Ticket::with(['trip.bus.agency', 'journey'])
            ->where('customer_id', $customer->customer_id);

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('trip->departure_date', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('trip->departure_date', '<=', $request->to_date);
        }

        // Separate upcoming and past trips
        $upcomingTickets = $query->clone()
            ->whereHas('trip', function ($q) {
                $q->where('departure_date', '>=', now());
            })
            ->get();

        $pastTickets = $query->clone()
            ->whereHas('trip', function ($q) {
                $q->where('departure_date', '<', now());
            })
            ->get();

        // Calculate stats
        $totalSpent = $query->clone()->sum('price');
        $totalTrips = $query->clone()->count();

        return view('customers.history', compact('customer', 'upcomingTickets', 'pastTickets', 'totalSpent', 'totalTrips'));
    }

    /**
     * Update customer status.
     */
    public function updateStatus(Request $request, Customer $customer)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        if ($customer->user) {
            $customer->user->update(['is_active' => ($request->status === 'active')]);
        }

        return redirect()->back()
            ->with('success', 'Customer status updated successfully.');
    }
}
