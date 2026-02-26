<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div><h3 class="text-lg font-medium text-gray-900">All Customers</h3><p class="text-sm text-gray-500">Manage customer accounts</p></div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tickets</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($customers as $customer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                                        <div class="text-sm text-gray-500">{{ $customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $customer->tickets_count }} tickets</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('customers.history', $customer) }}" class="text-green-600 hover:text-green-900"><i class="fas fa-history"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center"><i class="fas fa-users text-4xl text-gray-300 mb-3"></i><p class="text-gray-500">No customers found</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($customers->hasPages())<div class="px-6 py-4 bg-gray-50">{{ $customers->withQueryString()->links() }}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
