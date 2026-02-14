<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sell Ticket - Select Trip') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Search for a Trip</h3>
                    <form action="{{ route('tickets.sell') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-label for="from" :value="__('From')" />
                            <x-input id="from" name="from" type="text" class="mt-1 block w-full"
                                     placeholder="Departure city" value="{{ request('from') }}" />
                        </div>
                        <div>
                            <x-label for="to" :value="__('To')" />
                            <x-input id="to" name="to" type="text" class="mt-1 block w-full"
                                     placeholder="Arrival city" value="{{ request('to') }}" />
                        </div>
                        <div>
                            <x-label for="date" :value="__('Date')" />
                            <x-input id="date" name="date" type="date" class="mt-1 block w-full"
                                     value="{{ request('date') }}" />
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <i class="fas fa-search mr-2"></i> {{ __('Search') }}
                            </button>
                            <a href="{{ route('tickets.sell') }}"
                               class="ml-2 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                                {{ __('Clear') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Trips List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Trips</h3>

                    @if($trips->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($trips as $trip)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $trip->journey->departureLocation->city->name ?? 'Unknown' }} →
                                                    {{ $trip->journey->arrivalLocation->city->name ?? 'Unknown' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $trip->bus->agency->name ?? 'Unknown Agency' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $trip->departure_date->format('M d, Y') }}</div>
                                                <div class="text-sm text-gray-500">{{ $trip->departure_time }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $trip->bus->registration_number ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ $trip->bus->model ?? '' }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ number_format($trip->initial_price, 0, '.', ',') }} FCFA
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($trip->available_seats > 10) bg-green-100 text-green-800
                                                    @elseif($trip->available_seats > 0) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ $trip->available_seats }} seats
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($trip->available_seats > 0)
                                                    <a href="{{ route('tickets.create', $trip) }}"
                                                       class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                        <i class="fas fa-ticket-alt mr-1"></i> Select
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">Sold Out</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $trips->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-route text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No trips found matching your criteria</p>
                            <p class="text-sm text-gray-400 mt-1">Try adjusting your search filters</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

