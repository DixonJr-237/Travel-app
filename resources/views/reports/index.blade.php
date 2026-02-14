<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('reports.system') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center"><div class="flex-shrink-0 bg-blue-500 rounded-md p-3"><i class="fas fa-server text-white"></i></div>
                        <div class="ml-4"><h3 class="text-lg font-medium text-gray-900">System Report</h3><p class="text-sm text-gray-500">Overview of entire system</p></div></div>
                    </div>
                </a>
                <a href="{{ route('reports.companies-performance') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center"><div class="flex-shrink-0 bg-green-500 rounded-md p-3"><i class="fas fa-building text-white"></i></div>
                        <div class="ml-4"><h3 class="text-lg font-medium text-gray-900">Companies Performance</h3><p class="text-sm text-gray-500">All companies metrics</p></div></div>
                    </div>
                </a>
                @endif
                @if(in_array(auth()->user()->role, ['super_admin', 'company_admin']))
                <a href="{{ route('reports.company') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center"><div class="flex-shrink-0 bg-purple-500 rounded-md p-3"><i class="fas fa-chart-pie text-white"></i></div>
                        <div class="ml-4"><h3 class="text-lg font-medium text-gray-900">Company Report</h3><p class="text-sm text-gray-500">Your company statistics</p></div></div>
                    </div>
                </a>
                <a href="{{ route('reports.agencies-performance') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center"><div class="flex-shrink-0 bg-yellow-500 rounded-md p-3"><i class="fas fa-store text-white"></i></div>
                        <div class="ml-4"><h3 class="text-lg font-medium text-gray-900">Agencies Performance</h3><p class="text-sm text-gray-500">All agencies metrics</p></div></div>
                    </div>
                </a>
                @endif
                @if(in_array(auth()->user()->role, ['super_admin', 'company_admin', 'agency_admin']))
                <a href="{{ route('reports.agency') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center"><div class="flex-shrink-0 bg-red-500 rounded-md p-3"><i class="fas fa-bus text-white"></i></div>
                        <div class="ml-4"><h3 class="text-lg font-medium text-gray-900">Agency Report</h3><p class="text-sm text-gray-500">Your agency statistics</p></div></div>
                    </div>
                </a>
                <a href="{{ route('reports.financial') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center"><div class="flex-shrink-0 bg-green-600 rounded-md p-3"><i class="fas fa-money-bill-wave text-white"></i></div>
                        <div class="ml-4"><h3 class="text-lg font-medium text-gray-900">Financial Report</h3><p class="text-sm text-gray-500">Revenue and earnings</p></div></div>
                    </div>
                </a>
                <a href="{{ route('reports.operational') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center"><div class="flex-shrink-0 bg-indigo-500 rounded-md p-3"><i class="fas fa-cogs text-white"></i></div>
                        <div class="ml-4"><h3 class="text-lg font-medium text-gray-900">Operational Report</h3><p class="text-sm text-gray-500">Trip operations</p></div></div>
                    </div>
                </a>
                <a href="{{ route('reports.customer') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center"><div class="flex-shrink-0 bg-blue-600 rounded-md p-3"><i class="fas fa-users text-white"></i></div>
                        <div class="ml-4"><h3 class="text-lg font-medium text-gray-900">Customer Report</h3><p class="text-sm text-gray-500">Customer analytics</p></div></div>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
