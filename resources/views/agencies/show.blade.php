<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $agency->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-store text-2xl text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-gray-900">{{ $agency->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $agency->company->name ?? 'No company' }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('agencies.edit', $agency) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <i class="fas fa-bus text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Buses</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_buses'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Active Buses</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_buses'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <i class="fas fa-route text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Trips</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_trips'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <i class="fas fa-calendar-check text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Upcoming Trips</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['upcoming_trips'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Agency Details</h4>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $agency->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $agency->phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Location</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $agency->coordinates->city->name ?? 'N/A' }},
                                    {{ $agency->coordinates->city->subRegion->region->country->name ?? '' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $agency->coordinates->address ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Buses</h4>
                        @if($agency->buses->count() > 0)
                            <div class="space-y-3">
                                @foreach($agency->buses as $bus)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-bus text-blue-600 mr-3"></i>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $bus->registration_number }}</div>
                                                <div class="text-xs text-gray-500">{{ $bus->model }} ({{ $bus->year }})</div>
                                            </div>
                                        </div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($bus->status === 'active') bg-green-100 text-green-800
                                            @elseif($bus->status === 'maintenance') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $bus->status }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No buses assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
