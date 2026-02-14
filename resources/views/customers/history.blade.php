<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Travel History - {{ $customer->first_name }} {{ $customer->last_name }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><p class="text-sm font-medium text-gray-500">Total Spent</p><p class="text-2xl font-semibold text-gray-900">{{ number_format($totalSpent, 0, '.', ',') }} FCFA</p></div></div>
                <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><p class="text-sm font-medium text-gray-500">Total Trips</p><p class="text-2xl font-semibold text-gray-900">{{ $totalTrips }}</p></div></div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-6"><h4 class="text-lg font-medium text-gray-900 mb-4">Upcoming Trips</h4>
                @forelse($upcomingTickets as $ticket)
                    <div class="flex items-center justify-between p-4 border-b"><div><p class="font-medium">{{ $ticket->trip->departure_date->format('M d, Y') }}</p><p class="text-sm text-gray-500">{{ $ticket->journey->name ?? 'N/A' }}</p></div><span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">{{ $ticket->status }}</span></div>
                @empty<p class="text-gray-500">No upcoming trips</p>@endforelse
            </div></div>
            <div class="bg-white overflow-hidden shadow rounded-lg mt-6"><div class="p-6"><h4 class="text-lg font-medium text-gray-900 mb-4">Past Trips</h4>
                @forelse($pastTickets as $ticket)
                    <div class="flex items-center justify-between p-4 border-b"><div><p class="font-medium">{{ $ticket->trip->departure_date->format('M d, Y') }}</p><p class="text-sm text-gray-500">{{ $ticket->journey->name ?? 'N/A' }}</p></div><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ $ticket->status }}</span></div>
                @empty<p class="text-gray-500">No past trips</p>@endforelse
            </div></div>
        </div>
    </div>
</x-app-layout>
