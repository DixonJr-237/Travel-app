<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center"><i class="fas fa-user text-2xl text-blue-600"></i></div>
                        <div class="ml-4"><h3 class="text-xl font-bold text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</h3><p class="text-sm text-gray-500">{{ $customer->email }}</p></div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><div class="flex items-center"><div class="flex-shrink-0 bg-blue-500 rounded-md p-3"><i class="fas fa-ticket-alt text-white"></i></div><div class="ml-4"><p class="text-sm font-medium text-gray-500">Total Trips</p><p class="text-2xl font-semibold text-gray-900">{{ $stats['total_trips'] }}</p></div></div></div></div>
                <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><div class="flex items-center"><div class="flex-shrink-0 bg-green-500 rounded-md p-3"><i class="fas fa-money-bill-wave text-white"></i></div><div class="ml-4"><p class="text-sm font-medium text-gray-500">Total Spent</p><p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_spent'], 0, '.', ',') }} FCFA</p></div></div></div></div>
                <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><div class="flex items-center"><div class="flex-shrink-0 bg-purple-500 rounded-md p-3"><i class="fas fa-calendar-check text-white"></i></div><div class="ml-4"><p class="text-sm font-medium text-gray-500">Upcoming</p><p class="text-2xl font-semibold text-gray-900">{{ $stats['upcoming_trips'] }}</p></div></div></div></div>
            </div>
        </div>
    </div>
</x-app-layout>
