<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Trips
            </h2>
            @can('create', App\Models\Trip::class)
                <a href="{{ route('trips.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Schedule Trip
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <i class="fas fa-route text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Trips</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $trips->total() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <i class="fas fa-calendar-day text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Today's Trips</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $todayTripsCount ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <i class="fas fa-bus text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Active Buses</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\Bus::where('status', 'active')->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <i class="fas fa-ticket-alt text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Available Seats</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\Trip::where('departure_date', '>=', now())->sum('available_seats') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('trips.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Departure Date -->
                            <div>
                                <label for="departure_date" class="block text-sm font-medium text-gray-700 mb-1">Departure Date</label>
                                <input type="date"
                                       name="departure_date"
                                       id="departure_date"
                                       value="{{ request('departure_date') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Departure Location -->
                            <div>
                                <label for="departure_location" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                                <select name="departure_location"
                                        id="departure_location"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Departures</option>
                                    @foreach($coordinates as $coord)
                                        <option value="{{ $coord->id_coord }}"
                                                {{ request('departure_location') == $coord->id_coord ? 'selected' : '' }}>
                                            {{ $coord->city->name ?? 'Unknown' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Arrival Location -->
                            <div>
                                <label for="arrival_location" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                                <select name="arrival_location"
                                        id="arrival_location"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Arrivals</option>
                                    @foreach($coordinates as $coord)
                                        <option value="{{ $coord->id_coord }}"
                                                {{ request('arrival_location') == $coord->id_coord ? 'selected' : '' }}>
                                            {{ $coord->city->name ?? 'Unknown' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status"
                                        id="status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <!-- Additional Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Company Filter (for super admin) -->
                            @if(auth()->user()->role === 'super_admin')
                                <div>
                                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                    <select name="company_id"
                                            id="company_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Companies</option>
                                        @foreach(\App\Models\Company::all() as $company)
                                            <option value="{{ $company->id_company }}"
                                                    {{ request('company_id') == $company->id_company ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Agency Filter -->
                            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'company_admin')
                                <div>
                                    <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-1">Agency</label>
                                    <select name="agency_id"
                                            id="agency_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Agencies</option>
                                        @foreach(\App\Models\Agency::when(auth()->user()->company_id, function($q) {
                                            $q->where('id_company', auth()->user()->company_id);
                                        })->get() as $agency)
                                            <option value="{{ $agency->id_agence }}"
                                                    {{ request('agency_id') == $agency->id_agence ? 'selected' : '' }}>
                                                {{ $agency->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Bus Filter -->
                            <div>
                                <label for="bus_id" class="block text-sm font-medium text-gray-700 mb-1">Bus</label>
                                <select name="bus_id"
                                        id="bus_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Buses</option>
                                    @foreach(\App\Models\Bus::when(request('agency_id'), function($q) {
                                        $q->where('agency_id', request('agency_id'));
                                    })->get() as $bus)
                                        <option value="{{ $bus->bus_id }}"
                                                {{ request('bus_id') == $bus->bus_id ? 'selected' : '' }}>
                                            {{ $bus->registration_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('trips.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear Filters
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-search mr-2"></i> Search Trips
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Trips Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus & Agency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats & Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($trips as $trip)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-route text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $trip->departureLocation->city->name ?? 'Unknown' }} →
                                                    {{ $trip->arrivalLocation->city->name ?? 'Unknown' }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $trip->journey->name ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $trip->departure_date->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $trip->departure_time }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $trip->departure_date->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $trip->bus->registration_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $trip->bus->agency->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-400">{{ $trip->bus->model ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $trip->available_seats }}</div>
                                                <div class="text-xs text-gray-500">available</div>
                                            </div>
                                            <div class="text-gray-300">|</div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($trip->initial_price, 0, '.', ',') }} FCFA
                                                </div>
                                                <div class="text-xs text-gray-500">per seat</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($trip->status === 'scheduled') bg-green-100 text-green-800
                                            @elseif($trip->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($trip->status === 'completed') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($trip->status) }}
                                        </span>
                                        @if($trip->departure_date < now() && $trip->status === 'scheduled')
                                            <div class="text-xs text-red-600 mt-1">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Departure time passed
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('trips.show', $trip) }}"
                                               class="text-blue-600 hover:text-blue-900"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('update', $trip)
                                                <a href="{{ route('trips.edit', $trip) }}"
                                                   class="text-green-600 hover:text-green-900"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @if($trip->status === 'scheduled')
                                                <a href="{{ route('tickets.create', ['trip_id' => $trip->trip_id]) }}"
                                                   class="text-yellow-600 hover:text-yellow-900"
                                                   title="Sell Ticket">
                                                    <i class="fas fa-ticket-alt"></i>
                                                </a>
                                            @endif
                                            @can('delete', $trip)
                                                <form action="{{ route('trips.destroy', $trip) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this trip?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-route text-4xl text-gray-300 mb-3"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Trips Found</h3>
                                            <p class="text-gray-500 mb-4">Try adjusting your search filters or create a new trip</p>
                                            @can('create', App\Models\Trip::class)
                                                <a href="{{ route('trips.create') }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                    <i class="fas fa-plus mr-2"></i> Schedule Trip
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($trips->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $trips->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            <!-- Upcoming Trips Summary -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Today's Trips -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Trips</h3>
                        @php
                            $todayTrips = \App\Models\Trip::whereDate('departure_date', today())
                                ->where('status', 'scheduled')
                                ->count();
                        @endphp
                        <div class="text-center py-6">
                            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $todayTrips }}</div>
                            <p class="text-gray-600">trips scheduled for today</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('trips.index', ['departure_date' => today()->format('Y-m-d')]) }}"
                               class="block text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                View Today's Trips
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tomorrow's Trips -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tomorrow's Trips</h3>
                        @php
                            $tomorrowTrips = \App\Models\Trip::whereDate('departure_date', today()->addDay())
                                ->where('status', 'scheduled')
                                ->count();
                        @endphp
                        <div class="text-center py-6">
                            <div class="text-3xl font-bold text-green-600 mb-2">{{ $tomorrowTrips }}</div>
                            <p class="text-gray-600">trips scheduled for tomorrow</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('trips.index', ['departure_date' => today()->addDay()->format('Y-m-d')]) }}"
                               class="block text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                View Tomorrow's Trips
                            </a>
                        </div>
                    </div>
                </div>

                <!-- This Week's Trips -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">This Week's Trips</h3>
                        @php
                            $weekStart = today()->startOfWeek();
                            $weekEnd = today()->endOfWeek();
                            $weekTrips = \App\Models\Trip::whereBetween('departure_date', [$weekStart, $weekEnd])
                                ->where('status', 'scheduled')
                                ->count();
                        @endphp
                        <div class="text-center py-6">
                            <div class="text-3xl font-bold text-purple-600 mb-2">{{ $weekTrips }}</div>
                            <p class="text-gray-600">trips scheduled this week</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('trips.index', ['departure_date_from' => $weekStart->format('Y-m-d'), 'departure_date_to' => $weekEnd->format('Y-m-d')]) }}"
                               class="block text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                View This Week's Trips
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-update bus options based on agency selection
            const agencySelect = document.getElementById('agency_id');
            const busSelect = document.getElementById('bus_id');

            if (agencySelect && busSelect) {
                agencySelect.addEventListener('change', function() {
                    const agencyId = this.value;

                    // Clear existing options except the first one
                    while (busSelect.options.length > 1) {
                        busSelect.remove(1);
                    }

                    if (agencyId) {
                        // Fetch buses for selected agency
                        fetch(`/api/agencies/${agencyId}/buses`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.data.length > 0) {
                                    data.data.forEach(bus => {
                                        const option = document.createElement('option');
                                        option.value = bus.bus_id;
                                        option.textContent = bus.registration_number;
                                        busSelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching buses:', error);
                            });
                    }
                });
            }

            // Set default date to today
            document.addEventListener('DOMContentLoaded', function() {
                const dateInput = document.getElementById('departure_date');
                if (dateInput && !dateInput.value) {
                    dateInput.value = new Date().toISOString().split('T')[0];
                }
            });
        </script>
    @endpush
</x-app-layout>
