<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tickets
            </h2>
            @can('create', App\Models\Ticket::class)
                <a href="{{ route('tickets.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Sell Ticket
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
                                <i class="fas fa-ticket-alt text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Tickets</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $tickets->total() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <i class="fas fa-money-bill-wave text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Today's Revenue</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    @php
                                        $todayRevenue = \App\Models\Ticket::whereDate('purchase_date', today())->sum('price');
                                    @endphp
                                    {{ number_format($todayRevenue, 0, '.', ',') }} FCFA
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Customers</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\Customer::count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <i class="fas fa-percentage text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Confirmation Rate</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    @php
                                        $confirmed = \App\Models\Ticket::where('status', 'confirmed')->count();
                                        $total = \App\Models\Ticket::count();
                                        $rate = $total > 0 ? round(($confirmed / $total) * 100) : 0;
                                    @endphp
                                    {{ $rate }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('tickets.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Booking Reference -->
                            <div>
                                <label for="booking_reference" class="block text-sm font-medium text-gray-700 mb-1">Booking Reference</label>
                                <input type="text"
                                       name="booking_reference"
                                       id="booking_reference"
                                       value="{{ request('booking_reference') }}"
                                       placeholder="Enter booking reference"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Customer Email -->
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                                <input type="email"
                                       name="customer_email"
                                       id="customer_email"
                                       value="{{ request('customer_email') }}"
                                       placeholder="customer@example.com"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status"
                                        id="status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Used</option>
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                                <input type="date"
                                       name="date_from"
                                       id="date_from"
                                       value="{{ request('date_from') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Date To -->
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                                <input type="date"
                                       name="date_to"
                                       id="date_to"
                                       value="{{ request('date_to') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Trip Filter -->
                            <div>
                                <label for="trip_id" class="block text-sm font-medium text-gray-700 mb-1">Trip</label>
                                <select name="trip_id"
                                        id="trip_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Trips</option>
                                    @foreach(\App\Models\Trip::where('departure_date', '>=', now())->get() as $trip)
                                        <option value="{{ $trip->trip_id }}"
                                                {{ request('trip_id') == $trip->trip_id ? 'selected' : '' }}>
                                            {{ $trip->departureLocation->city->name ?? 'Unknown' }} →
                                            {{ $trip->arrivalLocation->city->name ?? 'Unknown' }}
                                            ({{ $trip->departure_date->format('M d') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

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
                                        @foreach(\App\Models\Agency::when(request('company_id'), function($q) {
                                            $q->where('id_company', request('company_id'));
                                        })->when(auth()->user()->company_id, function($q) {
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
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} results
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('tickets.index') }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Clear Filters
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <i class="fas fa-search mr-2"></i> Search Tickets
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trip Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price & Seat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->booking_reference }}</div>
                                        <div class="text-sm text-gray-500">{{ $ticket->purchase_date->format('M d, Y H:i') }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $ticket->purchase_date->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $ticket->customer->first_name }} {{ $ticket->customer->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $ticket->customer->email }}</div>
                                                <div class="text-xs text-gray-400">{{ $ticket->customer->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $ticket->trip->departureLocation->city->name ?? 'Unknown' }} →
                                            {{ $ticket->trip->arrivalLocation->city->name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $ticket->trip->departure_date->format('M d, Y') }} at {{ $ticket->trip->departure_time }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            Bus: {{ $ticket->trip->bus->registration_number }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($ticket->price, 0, '.', ',') }} FCFA
                                        </div>
                                        <div class="text-sm text-gray-500">Seat {{ $ticket->seat_number }}</div>
                                        @if($ticket->trip->bus)
                                            <div class="text-xs text-gray-400">
                                                Bus capacity: {{ $ticket->trip->bus->seats_count }} seats
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($ticket->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($ticket->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($ticket->status === 'used') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                        @if($ticket->reservation)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Reservation: {{ ucfirst($ticket->reservation->status) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                               class="text-blue-600 hover:text-blue-900"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tickets.print', $ticket) }}"
                                               target="_blank"
                                               class="text-purple-600 hover:text-purple-900"
                                               title="Print Ticket">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if($ticket->status === 'confirmed' && $ticket->trip->departure_date > now())
                                                <form action="{{ route('tickets.cancel', $ticket) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to cancel this ticket?');">
                                                    @csrf
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900"
                                                            title="Cancel Ticket">
                                                        <i class="fas fa-times"></i>
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
                                            <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-3"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Tickets Found</h3>
                                            <p class="text-gray-500 mb-4">Try adjusting your search filters or sell a new ticket</p>
                                            @can('create', App\Models\Ticket::class)
                                                <a href="{{ route('tickets.create') }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                    <i class="fas fa-plus mr-2"></i> Sell Ticket
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
                @if($tickets->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $tickets->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            <!-- Export Options -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Export Data</h3>
                            <p class="text-sm text-gray-600">Export ticket data for reporting</p>
                        </div>
                        <div class="flex space-x-3">
                            <form action="{{ route('reports.export') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="type" value="pdf">
                                <input type="hidden" name="report_type" value="tickets">
                                <input type="hidden" name="start_date" value="{{ request('date_from') }}">
                                <input type="hidden" name="end_date" value="{{ request('date_to') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest shadow-sm hover:bg-red-50">
                                    <i class="fas fa-file-pdf mr-2"></i> Export as PDF
                                </button>
                            </form>
                            <form action="{{ route('reports.export') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="type" value="excel">
                                <input type="hidden" name="report_type" value="tickets">
                                <input type="hidden" name="start_date" value="{{ request('date_from') }}">
                                <input type="hidden" name="end_date" value="{{ request('date_to') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-green-300 rounded-md font-semibold text-xs text-green-700 uppercase tracking-widest shadow-sm hover:bg-green-50">
                                    <i class="fas fa-file-excel mr-2"></i> Export as Excel
                                </button>
                            </form>
                            <form action="{{ route('reports.export') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="type" value="csv">
                                <input type="hidden" name="report_type" value="tickets">
                                <input type="hidden" name="start_date" value="{{ request('date_from') }}">
                                <input type="hidden" name="end_date" value="{{ request('date_to') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md font-semibold text-xs text-blue-700 uppercase tracking-widest shadow-sm hover:bg-blue-50">
                                    <i class="fas fa-file-csv mr-2"></i> Export as CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Set default date range to last 30 days if not set
            document.addEventListener('DOMContentLoaded', function() {
                const dateFrom = document.getElementById('date_from');
                const dateTo = document.getElementById('date_to');

                if (dateFrom && !dateFrom.value) {
                    const thirtyDaysAgo = new Date();
                    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                    dateFrom.value = thirtyDaysAgo.toISOString().split('T')[0];
                }

                if (dateTo && !dateTo.value) {
                    dateTo.value = new Date().toISOString().split('T')[0];
                }
            });

            // Quick status filter buttons
            function filterByStatus(status) {
                document.getElementById('status').value = status;
                document.querySelector('form').submit();
            }

            function filterByDateRange(days) {
                const dateTo = new Date();
                const dateFrom = new Date();
                dateFrom.setDate(dateFrom.getDate() - days);

                document.getElementById('date_from').value = dateFrom.toISOString().split('T')[0];
                document.getElementById('date_to').value = dateTo.toISOString().split('T')[0];
                document.querySelector('form').submit();
            }
        </script>
    @endpush
</x-app-layout>
