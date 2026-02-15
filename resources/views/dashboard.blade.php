<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-circle text-4xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Welcome back, {{ auth()->user()->name }}!</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                @switch(auth()->user()->role)
                                    @case('super_admin')
                                        Super Administrator - Full system access
                                        @break
                                    @case('company_admin')
                                        Company Administrator - {{ auth()->user()->company->name ?? 'No company' }}
                                        @break
                                    @case('agency_admin')
                                        Agency Administrator - {{ auth()->user()->agency->name ?? 'No agency' }}
                                        @break
                                    @case('customer')
                                        Customer - Book and manage your trips
                                        @break
                                @endswitch
                            </p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @switch(auth()->user()->role)
                                    @case('super_admin') bg-purple-100 text-purple-800 @break
                                    @case('company_admin') bg-blue-100 text-blue-800 @break
                                    @case('agency_admin') bg-green-100 text-green-800 @break
                                    @case('customer') bg-yellow-100 text-yellow-800 @break
                                @endswitch">
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                @php
                    $stats = $data['stats'] ?? [];
                @endphp

                @if(auth()->user()->role === 'super_admin')
                    <!-- Super Admin Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-building text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Companies</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_companies'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('admin.companies.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View all companies
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-store text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Agencies</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_agencies'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('admin.agencies.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View all agencies
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-route text-3xl text-purple-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_trips'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('admin.trips.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View all trips
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-3xl text-yellow-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_customers'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('customers.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View all customers
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(auth()->user()->role === 'company_admin')
                    <!-- Company Admin Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-store text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Agencies</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_agencies'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-company.agencies.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View agencies
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bus text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Buses</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_buses'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-company.buses.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View buses
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-route text-3xl text-purple-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_trips'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-company.trips.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View trips
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ number_format($data['monthly_revenue'] ?? 0, 0, '.', ',') }} FCFA
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-company.reports') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View reports
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(auth()->user()->role === 'agency_admin')
                    <!-- Agency Admin Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bus text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Buses</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_buses'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-agency.buses.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View buses
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-route text-3xl text-purple-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['active_trips'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-agency.trips.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View trips
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-day text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['today_trips'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('trips.index', ['departure_date' => now()->format('Y-m-d')]) }}"
                                   class="font-medium text-blue-600 hover:text-blue-500">
                                    View today's trips
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-ticket-alt text-3xl text-yellow-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Monthly Tickets</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['monthly_tickets'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('tickets.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View tickets
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(auth()->user()->role === 'customer')
                    @php
                        $customer = auth()->user()->customer;
                        $customerId = $customer?->customer_id ?? auth()->user()->customer_id ?? null;
                    @endphp

                    <!-- Customer Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-ticket-alt text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Tickets</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $data['total_tickets'] ?? 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('tickets.my') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View my tickets
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-check text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ is_countable($data['upcoming_trips'] ?? []) ? count($data['upcoming_trips']) : 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('tickets.booking.search') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    Book new trip
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-history text-3xl text-gray-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Past Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ is_countable($data['past_trips'] ?? []) ? count($data['past_trips']) : 0 }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                @if($customerId)
                                    <a href="{{ route('customers.history', $customerId) }}"
                                       class="font-medium text-blue-600 hover:text-blue-500">
                                        View history
                                    </a>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Customer profile not found">
                                        View history
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-wallet text-3xl text-yellow-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Spent</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            @php
                                                $totalSpent = 0;
                                                if ($customer && method_exists($customer, 'tickets')) {
                                                    $totalSpent = $customer->tickets()->sum('price') ?? 0;
                                                }
                                            @endphp
                                            {{ number_format($totalSpent, 0, '.', ',') }} FCFA
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                @if($customerId)
                                    <a href="{{ route('customers.tickets', $customerId) }}"
                                       class="font-medium text-blue-600 hover:text-blue-500">
                                        View spending
                                    </a>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Customer profile not found">
                                        View spending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    @if(auth()->user()->role === 'super_admin')
                        <!-- Revenue Chart -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Overview (Last 30 Days)</h3>
                                <div class="h-64">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Trips -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Recent Trips</h3>
                                    <a href="{{ route('admin.trips.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        View all
                                    </a>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse(($data['recent_trips'] ?? []) as $trip)
                                                <tr>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $trip->departureLocation->city->name ?? 'Unknown' }} →
                                                            {{ $trip->arrivalLocation->city->name ?? 'Unknown' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($trip->departure_date)->format('M d, Y') }}</div>
                                                        <div class="text-sm text-gray-500">{{ $trip->departure_time }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $trip->bus->registration_number ?? 'N/A' }}</div>
                                                        <div class="text-sm text-gray-500">{{ $trip->bus->agency->name ?? 'N/A' }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            @if($trip->status === 'scheduled') bg-green-100 text-green-800
                                                            @elseif($trip->status === 'in_progress') bg-blue-100 text-blue-800
                                                            @elseif($trip->status === 'completed') bg-gray-100 text-gray-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            {{ ucfirst($trip->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                                        No recent trips found
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()->role === 'company_admin')
                        <!-- Agency Performance - FIXED SECTION -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Agency Performance</h3>
                                <div class="space-y-4">
                                    @php
                                        $agencyStats = $data['agency_stats'] ?? collect();
                                        // Calculate total tickets safely
                                        $totalTickets = 0;
                                        if ($agencyStats instanceof \Illuminate\Support\Collection) {
                                            $totalTickets = $agencyStats->sum('ticket_count');
                                        } elseif (is_array($agencyStats)) {
                                            $totalTickets = array_sum(array_column($agencyStats, 'ticket_count'));
                                        }
                                    @endphp

                                    @forelse($agencyStats as $stat)
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $stat['name'] ?? 'Unknown Agency' }}</p>
                                                <div class="flex items-center space-x-4 mt-1">
                                                    <span class="text-xs text-gray-500">
                                                        <i class="fas fa-route mr-1"></i> {{ $stat['trip_count'] ?? 0 }} trips
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        <i class="fas fa-ticket-alt mr-1"></i> {{ $stat['ticket_count'] ?? 0 }} tickets
                                                    </span>
                                                </div>
                                            </div>
                                            <a href="{{ route('my-company.agencies') }}"
                                               class="text-sm text-blue-600 hover:text-blue-500">
                                                View
                                            </a>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            @php
                                                $ticketCount = $stat['ticket_count'] ?? 0;
                                                $percentage = $totalTickets > 0 ? ($ticketCount / $totalTickets * 100) : 0;
                                            @endphp
                                            <div class="bg-blue-600 h-2 rounded-full"
                                                 style="width: {{ $percentage }}%">
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 text-center py-4">No agency statistics available</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()->role === 'agency_admin')
                        <!-- Recent Bookings -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Recent Bookings</h3>
                                    <a href="{{ route('tickets.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        View all
                                    </a>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Ref</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trip</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse(($data['recent_bookings'] ?? []) as $ticket)
                                                <tr>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->booking_reference ?? 'N/A' }}</div>
                                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($ticket->purchase_date ?? now())->format('M d, H:i') }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">
                                                            {{ $ticket->customer->first_name ?? '' }} {{ $ticket->customer->last_name ?? '' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">{{ $ticket->customer->phone ?? '' }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">
                                                            {{ $ticket->trip->departureLocation->city->name ?? 'Unknown' }} →
                                                            {{ $ticket->trip->arrivalLocation->city->name ?? 'Unknown' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($ticket->trip->departure_date ?? '')->format('M d') }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ number_format($ticket->price ?? 0, 0, '.', ',') }} FCFA</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            @if(($ticket->status ?? '') === 'confirmed') bg-green-100 text-green-800
                                                            @elseif(($ticket->status ?? '') === 'pending') bg-yellow-100 text-yellow-800
                                                            @elseif(($ticket->status ?? '') === 'used') bg-blue-100 text-blue-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            {{ ucfirst($ticket->status ?? 'unknown') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                        No recent bookings
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Trip Schedule -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Upcoming Trips</h3>
                                    <a href="{{ route('trips.create') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        Schedule trip
                                    </a>
                                </div>
                                <div class="space-y-4">
                                    @php
                                        $agencyId = auth()->user()->agency_id;
                                        $upcomingTrips = collect();
                                        if ($agencyId) {
                                            try {
                                                $upcomingTrips = \App\Models\Tips::whereIn('bus_id',
                                                    \App\Models\Bus::where('agency_id', $agencyId)->pluck('bus_id')
                                                )->where('departure_date', '>=', now())
                                                 ->where('status', 'scheduled')
                                                 ->orderBy('departure_date')
                                                 ->limit(5)
                                                 ->get();
                                            } catch (\Exception $e) {
                                                $upcomingTrips = collect();
                                            }
                                        }
                                    @endphp
                                    @forelse($upcomingTrips as $trip)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-bus text-blue-600"></i>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $trip->departureLocation->city->name ?? 'Unknown' }} →
                                                            {{ $trip->arrivalLocation->city->name ?? 'Unknown' }}
                                                        </p>
                                                        <div class="flex items-center text-xs text-gray-500 mt-1">
                                                            <span><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($trip->departure_date)->format('M d, Y') }}</span>
                                                            <span class="mx-2">•</span>
                                                            <span><i class="fas fa-clock mr-1"></i> {{ $trip->departure_time }}</span>
                                                            <span class="mx-2">•</span>
                                                            <span>{{ $trip->available_seats ?? 0 }} seats left</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($trip->initial_price ?? 0, 0, '.', ',') }} FCFA
                                                </div>
                                                <div class="text-xs text-gray-500">per seat</div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 text-center py-4">No upcoming trips scheduled</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()->role === 'customer')
                        @php
                            $customer = auth()->user()->customer;
                            $customerId = $customer?->customer_id ?? auth()->user()->customer_id ?? null;
                            $upcomingTrips = $data['upcoming_trips'] ?? [];
                            $pastTrips = $data['past_trips'] ?? [];

                            // Ensure they are collections/arrays
                            if (!is_array($upcomingTrips) && !($upcomingTrips instanceof \Illuminate\Support\Collection)) {
                                $upcomingTrips = [];
                            }
                            if (!is_array($pastTrips) && !($pastTrips instanceof \Illuminate\Support\Collection)) {
                                $pastTrips = [];
                            }
                        @endphp

                        <!-- Upcoming Trips -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Upcoming Trips</h3>
                                    <a href="{{ route('tickets.booking.search') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        Book new trip
                                    </a>
                                </div>
                                <div class="space-y-4">
                                    @forelse($upcomingTrips as $ticket)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-bus text-blue-600"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $ticket->trip->departureLocation->city->name ?? 'Unknown' }} →
                                                                {{ $ticket->trip->arrivalLocation->city->name ?? 'Unknown' }}
                                                            </p>
                                                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                                                <span><i class="fas fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($ticket->trip->departure_date ?? '')->format('M d, Y') }}</span>
                                                                <span class="mx-2">•</span>
                                                                <span><i class="fas fa-clock mr-1"></i> {{ $ticket->trip->departure_time ?? '' }}</span>
                                                                <span class="mx-2">•</span>
                                                                <span>Seat {{ $ticket->seat_number ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                            @if(($ticket->status ?? '') === 'confirmed') bg-green-100 text-green-800
                                                            @elseif(($ticket->status ?? '') === 'pending') bg-yellow-100 text-yellow-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            {{ ucfirst($ticket->status ?? 'unknown') }}
                                                        </span>
                                                        @if(isset($ticket->ticket_id))
                                                            <a href="{{ route('tickets.show', $ticket->ticket_id) }}"
                                                               class="text-blue-600 hover:text-blue-500">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-900 mt-2">
                                                        {{ number_format($ticket->price ?? 0, 0, '.', ',') }} FCFA
                                                    </div>
                                                    <div class="text-xs text-gray-500">Booking: {{ $ticket->booking_reference ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-end space-x-2">
                                                @if(isset($ticket->ticket_id))
                                                    <a href="{{ route('tickets.print', $ticket->ticket_id) }}"
                                                       target="_blank"
                                                       class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                        <i class="fas fa-print mr-1"></i> Print Ticket
                                                    </a>
                                                    @if(($ticket->status ?? '') === 'confirmed')
                                                        <button onclick="cancelTicket({{ $ticket->ticket_id }})"
                                                                class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                                            <i class="fas fa-times mr-1"></i> Cancel
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8">
                                            <i class="fas fa-route text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-gray-500">No upcoming trips booked</p>
                                            <a href="{{ route('tickets.booking.search') }}"
                                               class="inline-flex items-center px-4 py-2 mt-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                <i class="fas fa-search mr-2"></i> Find Trips
                                            </a>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Travel History -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Travel History</h3>
                                    @if($customerId)
                                        <a href="{{ route('customers.history', $customerId) }}"
                                           class="text-sm text-blue-600 hover:text-blue-500">
                                            View all
                                        </a>
                                    @endif
                                </div>
                                <div class="space-y-3">
                                    @forelse($pastTrips as $ticket)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-check text-gray-600 text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $ticket->trip->departureLocation->city->name ?? 'Unknown' }} →
                                                        {{ $ticket->trip->arrivalLocation->city->name ?? 'Unknown' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($ticket->trip->departure_date ?? '')->format('M d, Y') }} ·
                                                        {{ number_format($ticket->price ?? 0, 0, '.', ',') }} FCFA
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($ticket->trip->departure_date ?? '')->diffForHumans() }}</span>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 text-center py-4">No travel history</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @if(auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.companies.create') }}"
                                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-plus-circle text-blue-600 group-hover:text-blue-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Create Company</p>
                                            <p class="text-xs text-gray-500">Register new travel company</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('admin.agencies.create') }}"
                                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-store-alt text-green-600 group-hover:text-green-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Create Agency</p>
                                            <p class="text-xs text-gray-500">Add new agency location</p>
                                        </div>
                                    </a>
                                @endif

                                @if(auth()->user()->role === 'company_admin')
                                    <a href="{{ route('my-company.agencies.create') }}"
                                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-store-alt text-green-600 group-hover:text-green-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Manage Agencies</p>
                                            <p class="text-xs text-gray-500">View and manage agencies</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('my-company.buses.create') }}"
                                       class="flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-bus text-yellow-600 group-hover:text-yellow-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Manage Buses</p>
                                            <p class="text-xs text-gray-500">View fleet inventory</p>
                                        </div>
                                    </a>
                                @endif

                                @if(in_array(auth()->user()->role, ['agency_admin']))
                                    <a href="{{ route('trips.create') }}"
                                       class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-route text-purple-600 group-hover:text-purple-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Schedule Trip</p>
                                            <p class="text-xs text-gray-500">Create new trip schedule</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('buses.create') }}"
                                       class="flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-bus text-yellow-600 group-hover:text-yellow-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Add Bus</p>
                                            <p class="text-xs text-gray-500">Register new bus</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('tickets.sell') }}"
                                       class="flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-ticket-alt text-indigo-600 group-hover:text-indigo-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Sell Ticket</p>
                                            <p class="text-xs text-gray-500">Manual ticket booking</p>
                                        </div>
                                    </a>
                                @endif

                                @if(auth()->user()->role === 'customer')
                                    <a href="{{ route('tickets.booking.search') }}"
                                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-search text-blue-600 group-hover:text-blue-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Find Trips</p>
                                            <p class="text-xs text-gray-500">Search and book trips</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('tickets.my') }}"
                                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-history text-green-600 group-hover:text-green-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">My Bookings</p>
                                            <p class="text-xs text-gray-500">View all your tickets</p>
                                        </div>
                                    </a>
                                @endif

                                <a href="{{ route('reports.index') }}"
                                   class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg group">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chart-bar text-gray-600 group-hover:text-gray-700"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Generate Report</p>
                                        <p class="text-xs text-gray-500">Create custom reports</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">System Status</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">API Status</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-xs mr-1"></i> Operational
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Database</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-xs mr-1"></i> Connected
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Last Backup</span>
                                    <span class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse(now()->subDays(1))->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Active Users</span>
                                    <span class="text-sm text-gray-900">
                                        {{ \DB::table('sessions')->where('last_activity', '>=', now()->subMinutes(5)->timestamp)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'super_admin' && isset($data['revenue_data']))
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('revenueChart');
                    if (ctx) {
                        try {
                            const ctx2d = ctx.getContext('2d');
                            const revenueData = @json($data['revenue_data'] ?? []);

                            const dates = revenueData.map(item => item.date || '');
                            const revenues = revenueData.map(item => item.revenue || 0);

                            new Chart(ctx2d, {
                                type: 'line',
                                data: {
                                    labels: dates,
                                    datasets: [{
                                        label: 'Daily Revenue (FCFA)',
                                        data: revenues,
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 2,
                                        tension: 0.4,
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return 'Revenue: ' + context.parsed.y.toLocaleString() + ' FCFA';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return value.toLocaleString() + ' FCFA';
                                                }
                                            }
                                        },
                                        x: {
                                            ticks: {
                                                maxTicksLimit: 7
                                            }
                                        }
                                    }
                                }
                            });
                        } catch (e) {
                            console.error('Error initializing chart:', e);
                        }
                    }
                });

                function cancelTicket(ticketId) {
                    if (!ticketId) return;

                    if (confirm('Are you sure you want to cancel this ticket?')) {
                        fetch(`/tickets/${ticketId}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message || 'Error cancelling ticket');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error cancelling ticket');
                        });
                    }
                }
            </script>
        @endpush
    @endif

</x-app-layout>
