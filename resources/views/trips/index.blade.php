<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trips Management</h2>
                <p class="text-sm text-gray-600 mt-1">Manage your scheduled trips and routes</p>
            </div>

            @php
                $user = auth()->user();
                $createRoute = match($user->role) {
                    'super_admin' => 'admin.trips.create',
                    'company_admin' => 'my-company.trips.create',
                    'agency_admin' => 'my-agency.trips.create',
                    default => null
                };
            @endphp

            @if($createRoute && Route::has($createRoute))
                <a href="{{ route($createRoute) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Schedule New Trip
                </a>
            @endif
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
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $statusCounts['total'] ?? ($tips instanceof \Illuminate\Pagination\LengthAwarePaginator ? $tips->total() : $tips->count()) ?? 0 }}
                                </p>
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
                                    {{ $todayTripsCount ?? \App\Models\Tips::whereDate('departure_date', today())->count() }}
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
                                    {{ $activeBusesCount ?? \App\Models\Bus::where('status', 'active')->count() }}
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
                                    {{ $availableSeatsCount ?? \App\Models\Tips::where('departure_date', '>=', now())->sum('available_seats') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Message Display -->
            @if(isset($error) && $error)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filters and Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    @php
                        $indexRoute = match($user->role) {
                            'super_admin' => 'admin.trips.index',
                            'company_admin' => 'my-company.trips.index',
                            'agency_admin' => 'my-agency.trips.index',
                            default => 'admin.trips.index'
                        };
                    @endphp

                    <form method="GET" action="{{ Route::has($indexRoute) ? route($indexRoute) : '#' }}" class="space-y-4">
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

                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text"
                                       name="search"
                                       id="search"
                                       value="{{ request('search') }}"
                                       placeholder="Route, bus number..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                </select>
                            </div>

                            <!-- Date From -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                                <input type="date"
                                       name="date_from"
                                       id="date_from"
                                       value="{{ request('date_from') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Date To -->
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                                <input type="date"
                                       name="date_to"
                                       id="date_to"
                                       value="{{ request('date_to') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Agency Filter (for super admin and company admin) -->
                            @if(in_array($user->role, ['super_admin', 'company_admin']))
                                <div>
                                    <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-1">Agency</label>
                                    <select name="agency_id"
                                            id="agency_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Agencies</option>
                                        @foreach($agencies ?? [] as $agency)
                                            <option value="{{ $agency->id_agence }}"
                                                    {{ request('agency_id') == $agency->id_agence ? 'selected' : '' }}>
                                                {{ $agency->name }}
                                                @if($user->role === 'super_admin' && $agency->company)
                                                    ({{ $agency->company->name }})
                                                @endif
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
                                    @foreach($buses ?? [] as $bus)
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
                            <a href="{{ Route::has($indexRoute) ? route($indexRoute) : '#' }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-times mr-2"></i> Clear Filters
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-search mr-2"></i> Search Trips
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Status Summary Cards -->
            @if(isset($statusCounts))
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <span class="text-xs text-gray-600">Total</span>
                    <span class="block text-lg font-bold text-blue-600">{{ $statusCounts['total'] }}</span>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-center">
                    <span class="text-xs text-gray-600">Scheduled</span>
                    <span class="block text-lg font-bold text-green-600">{{ $statusCounts['scheduled'] }}</span>
                </div>
                <div class="bg-yellow-50 rounded-lg p-3 text-center">
                    <span class="text-xs text-gray-600">In Progress</span>
                    <span class="block text-lg font-bold text-yellow-600">{{ $statusCounts['in_progress'] }}</span>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <span class="text-xs text-gray-600">Completed</span>
                    <span class="block text-lg font-bold text-gray-600">{{ $statusCounts['completed'] }}</span>
                </div>
                <div class="bg-red-50 rounded-lg p-3 text-center">
                    <span class="text-xs text-gray-600">Cancelled</span>
                    <span class="block text-lg font-bold text-red-600">{{ $statusCounts['cancelled'] }}</span>
                </div>
            </div>
            @endif

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
                            @forelse($tips ?? [] as $trip)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-route text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ optional($trip->journey->departureLocation->city)->name ?? 'Unknown' }} →
                                                    {{ optional($trip->journey->arrivalLocation->city)->name ?? 'Unknown' }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $trip->journey->name ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $trip->departure_date ? \Carbon\Carbon::parse($trip->departure_date)->format('M d, Y') : 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $trip->departure_time ?? 'N/A' }}</div>
                                        @if($trip->departure_date)
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($trip->departure_date)->diffForHumans() }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $trip->bus->registration_number ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $trip->bus->agency->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-400">{{ $trip->bus->model ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $trip->available_seats ?? 0 }}</div>
                                                <div class="text-xs text-gray-500">available</div>
                                            </div>
                                            <div class="text-gray-300">|</div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($trip->initial_price ?? 0, 0, '.', ',') }} FCFA
                                                </div>
                                                <div class="text-xs text-gray-500">per seat</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'scheduled' => 'bg-green-100 text-green-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'active' => 'bg-purple-100 text-purple-800',
                                                'completed' => 'bg-gray-100 text-gray-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'default' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $statusColor = $statusColors[$trip->status] ?? $statusColors['default'];
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $trip->status ?? 'unknown')) }}
                                        </span>
                                        @if($trip->departure_date && \Carbon\Carbon::parse($trip->departure_date) < now() && $trip->status === 'scheduled')
                                            <div class="text-xs text-red-600 mt-1">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Departure time passed
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @php
                                            $showRoute = match($user->role) {
                                                'super_admin' => 'admin.trips.show',
                                                'company_admin' => 'my-company.trips.show',
                                                'agency_admin' => 'my-agency.trips.show',
                                                default => null
                                            };
                                            $editRoute = match($user->role) {
                                                'super_admin' => 'admin.trips.edit',
                                                'company_admin' => 'my-company.trips.edit',
                                                'agency_admin' => 'my-agency.trips.edit',
                                                default => null
                                            };
                                            $destroyRoute = match($user->role) {
                                                'super_admin' => 'admin.trips.destroy',
                                                'company_admin' => 'my-company.trips.destroy',
                                                'agency_admin' => 'my-agency.trips.destroy',
                                                default => null
                                            };
                                        @endphp

                                        <div class="flex space-x-3">
                                            @if($showRoute && Route::has($showRoute))
                                                <a href="{{ route($showRoute, $trip->id_tips ?? $trip->id) }}"
                                                   class="text-blue-600 hover:text-blue-900"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if($editRoute && Route::has($editRoute))
                                                <a href="{{ route($editRoute, $trip->id_tips ?? $trip->id) }}"
                                                   class="text-green-600 hover:text-green-900"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            @if($destroyRoute && Route::has($destroyRoute))
                                                <form action="{{ route($destroyRoute, $trip->id_tips ?? $trip->id) }}"
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
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-route text-4xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Trips Found</h3>
                                            <p class="text-gray-500 mb-4 max-w-md">
                                                @if(request()->anyFilled(['departure_date', 'search', 'status', 'date_from', 'date_to', 'agency_id', 'bus_id']))
                                                    No trips match your current filters. Try adjusting your search criteria.
                                                @else
                                                    Get started by scheduling your first trip.
                                                @endif
                                            </p>

                                            @if($createRoute && Route::has($createRoute) && !request()->anyFilled(['departure_date', 'search', 'status', 'date_from', 'date_to', 'agency_id', 'bus_id']))
                                                <a href="{{ route($createRoute) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                    <i class="fas fa-plus mr-2"></i> Schedule Your First Trip
                                                </a>
                                            @elseif(request()->anyFilled(['departure_date', 'search', 'status', 'date_from', 'date_to', 'agency_id', 'bus_id']))
                                                <a href="{{ Route::has($indexRoute) ? route($indexRoute) : '#' }}"
                                                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                    <i class="fas fa-times mr-2"></i> Clear All Filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination - FIXED -->
                @if(isset($tips))
                    @if($tips instanceof \Illuminate\Pagination\LengthAwarePaginator && $tips->hasPages())
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ $tips->firstItem() ?? 0 }}</span>
                                    to <span class="font-medium">{{ $tips->lastItem() ?? 0 }}</span>
                                    of <span class="font-medium">{{ $tips->total() }}</span> results
                                </div>
                                <div class="flex-1 flex justify-end">
                                    {{ $tips->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    @elseif($tips instanceof \Illuminate\Support\Collection && $tips->count() > 0)
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="text-sm text-gray-600 text-center">
                                Showing all {{ $tips->count() }} trips
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Quick Stats Summary -->
            @if(isset($tips) && $tips->count() > 0)
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-2">
                                    <i class="fas fa-calendar-check text-white"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Today's Trips</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $tips->where('departure_date', today()->format('Y-m-d'))->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-2">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Upcoming</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $tips->where('departure_date', '>', now())->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-md p-2">
                                    <i class="fas fa-chair text-white"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Total Seats</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $tips->sum('available_seats') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-2">
                                    <i class="fas fa-tag text-white"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Avg. Price</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ number_format($tips->avg('initial_price') ?? 0, 0, '.', ',') }} FCFA
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
