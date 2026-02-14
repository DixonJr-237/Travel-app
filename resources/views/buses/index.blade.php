<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buses</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">All Buses</h3>
                    <p class="text-sm text-gray-500">Manage fleet vehicles</p>
                </div>
                <a href="{{ route('buses.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Add Bus
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seats</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trips</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($buses as $bus)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-bus text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $bus->registration_number }}</div>
                                                <div class="text-sm text-gray-500">{{ $bus->model }} ({{ $bus->year }})</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $bus->agency->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bus->seats_count }} seats</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($bus->status === 'active') bg-green-100 text-green-800
                                            @elseif($bus->status === 'maintenance') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $bus->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bus->trips_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('buses.show', $bus) }}" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('buses.edit', $bus) }}" class="text-green-600 hover:text-green-900"><i class="fas fa-edit"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-12 text-center"><i class="fas fa-bus text-4xl text-gray-300 mb-3"></i><p class="text-gray-500">No buses found</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($buses->hasPages())
                    <div class="px-6 py-4 bg-gray-50">{{ $buses->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
