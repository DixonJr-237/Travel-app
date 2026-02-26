<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-circle text-4xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Welcome back, {{ auth()->user()->name ?? 'User' }}!</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                @switch(auth()->user()->role ?? '')
                                    @case('super_admin')
                                        Super Administrator - Full system access
                                        @break
                                    @case('company_admin')
                                        Company Administrator - {{ auth()->user()->company->name ?? 'No company' }}
                                        @break
                                    @case('agency_admin')
                                        Agency Administrator - {{ auth()->user()->agency->name ?? 'No agency' }}
                                        @break
                                    @case('customer')
                                        Customer - Book and manage your trips
                                        @break
                                @endswitch
                            </p>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @switch(auth()->user()->role ?? '')
                                    @case('super_admin') bg-purple-100 text-purple-800 @break
                                    @case('company_admin') bg-blue-100 text-blue-800 @break
                                    @case('agency_admin') bg-green-100 text-green-800 @break
                                    @case('customer') bg-yellow-100 text-yellow-800 @break
                                @endswitch">
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'guest')) }}
                            </span>
                        </div>
                    </div>
                    @if(!($data['is_empty'] ?? true) && isset($data['last_updated']))
                        <div class="mt-2 text-xs text-gray-500 flex justify-end">
                            <i class="fas fa-sync-alt mr-1"></i> Last updated: {{ $data['last_updated'] }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Loading State -->
            @if($data['is_empty'] ?? false)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Loading dashboard data... Please refresh in a moment.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                @if(auth()->user()->role === 'super_admin')
                    <!-- Super Admin Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-building text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Companies</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['total_companies'] ?? 0) }}</dd>
                                    </dl>
                                    <p class="text-xs text-green-600 mt-1">
                                        <i class="fas fa-arrow-up"></i> +{{ number_format($data['stats']['new_companies'] ?? 0) }} this month
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('admin.companies.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View all companies
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Super Admin Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-store text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Agencies</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['total_agencies'] ?? 0) }}</dd>
                                    </dl>
                                    <p class="text-xs text-green-600 mt-1">
                                        <i class="fas fa-arrow-up"></i> +{{ number_format($data['stats']['new_agencies'] ?? 0) }} this month
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('admin.agencies.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View all agencies
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-route text-3xl text-purple-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['total_trips'] ?? 0) }}</dd>
                                    </dl>
                                    <p class="text-xs text-green-600 mt-1">
                                        <i class="fas fa-arrow-up"></i> +{{ number_format($data['stats']['new_trips'] ?? 0) }} this month
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('admin.trips.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View all trips
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-3xl text-yellow-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['total_customers'] ?? 0) }}</dd>
                                    </dl>
                                    <p class="text-xs text-green-600 mt-1">
                                        <i class="fas fa-arrow-up"></i> +{{ number_format($data['stats']['new_customers'] ?? 0) }} this month
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('customers.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View all customers
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(auth()->user()->role === 'company_admin' && auth()->user()->company)
                    <!-- Company Admin Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-store text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Agencies</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['total_agencies'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-company.agencies.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View agencies
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bus text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Buses</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['total_buses'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-company.buses.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View buses
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-route text-3xl text-purple-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['upcoming_trips'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-company.trips.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View trips
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ number_format($data['monthly_revenue'] ?? 0, 0, '.', ',') }} FCFA
                                        </dd>
                                    </dl>
                                    @if(($data['revenue_growth'] ?? 0) != 0)
                                        <p class="text-xs {{ ($data['revenue_growth'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                            <i class="fas {{ ($data['revenue_growth'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                            {{ number_format(abs($data['revenue_growth'] ?? 0), 1) }}% vs last month
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-company.reports') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View reports
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(auth()->user()->role === 'agency_admin')
                    <!-- Agency Admin Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bus text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Buses</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['total_buses'] ?? 0) }}</dd>
                                    </dl>
                                    <p class="text-xs text-green-600 mt-1">
                                        {{ number_format($data['stats']['active_buses'] ?? 0) }} active
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-agency.buses.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View buses
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-route text-3xl text-purple-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['upcoming_trips'] ?? 0) }}</dd>
                                    </dl>
                                    <p class="text-xs text-blue-600 mt-1">
                                        {{ number_format($data['stats']['today_trips'] ?? 0) }} today
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('my-agency.trips.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View trips
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-ticket-alt text-3xl text-yellow-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Monthly Tickets</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['monthly_tickets'] ?? 0) }}</dd>
                                    </dl>
                                    <p class="text-xs text-green-600 mt-1">
                                        {{ number_format($data['weekly_tickets'] ?? 0) }} this week
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('tickets.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View tickets
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chart-line text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ number_format($data['stats']['monthly_revenue'] ?? 0, 0, '.', ',') }} FCFA
                                        </dd>
                                    </dl>
                                    @if(($data['revenue_growth'] ?? 0) != 0)
                                        <p class="text-xs {{ ($data['revenue_growth'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                            <i class="fas {{ ($data['revenue_growth'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                            {{ number_format(abs($data['revenue_growth'] ?? 0), 1) }}% vs last month
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('reports.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View reports
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(auth()->user()->role === 'customer')
                    <!-- Customer Stats -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-ticket-alt text-3xl text-blue-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Tickets</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['total_tickets'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('tickets.my') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    View my tickets
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-calendar-check text-3xl text-green-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['upcoming_trips'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <a href="{{ route('tickets.booking.search') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                    Book new trip
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-history text-3xl text-gray-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Past Trips</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['stats']['past_trips'] ?? 0) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                @if(isset($data['customer_since']))
                                    <a href="{{ route('customers.history', auth()->user()->customer->customer_id ?? '') }}"
                                       class="font-medium text-blue-600 hover:text-blue-500">
                                        View history
                                    </a>
                                @else
                                    <span class="text-gray-400">View history</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-wallet text-3xl text-yellow-600"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Spent</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ number_format($data['stats']['total_spent'] ?? 0, 0, '.', ',') }} FCFA
                                        </dd>
                                    </dl>
                                    @if(($data['cancelled_tickets'] ?? 0) > 0)
                                        <p class="text-xs text-red-600 mt-1">
                                            {{ number_format($data['cancelled_tickets'] ?? 0) }} cancelled
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                @if(isset($data['customer_since']))
                                    <span class="text-gray-600 text-xs">
                                        Customer since {{ \Carbon\Carbon::parse($data['customer_since'])->format('M Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    @if(auth()->user()->role === 'super_admin')
                        <!-- Revenue Overview Card -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <!-- Header with Key Metrics -->
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Revenue Overview</h3>
                                        <p class="text-sm text-gray-500">Last 30 days performance</p>
                                    </div>
                                    <div class="mt-2 sm:mt-0 grid grid-cols-2 gap-4">
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Total Revenue (30d)</p>
                                            <p class="text-xl font-bold text-gray-900">
                                                {{ number_format(collect($data['revenue_data'] ?? [])->sum('revenue'), 0, '.', ',') }} FCFA
                                            </p>
                                        </div>
                                        <div class="text-right border-l pl-4">
                                            <p class="text-sm text-gray-500">Growth</p>
                                            <p class="text-xl font-bold {{ ($data['stats']['revenue_growth'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ ($data['stats']['revenue_growth'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($data['stats']['revenue_growth'] ?? 0, 1) }}%
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Key Metrics Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Current Month</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['stats']['current_revenue'] ?? 0, 0, '.', ',') }} FCFA</p>
                                        <p class="text-xs text-green-600 mt-1">{{ number_format($data['stats']['new_tickets'] ?? 0) }} tickets</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Total Revenue</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['total_revenue'] ?? 0, 0, '.', ',') }} FCFA</p>
                                        <p class="text-xs text-gray-500 mt-1">All time</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Total Tickets</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['stats']['total_tickets'] ?? 0) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">+{{ number_format($data['stats']['new_tickets'] ?? 0) }} this month</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Avg. Ticket Price</p>
                                        @php
                                            $avgPrice = ($data['stats']['total_tickets'] ?? 0) > 0
                                                ? ($data['stats']['total_revenue'] ?? 0) / ($data['stats']['total_tickets'] ?? 1)
                                                : 0;
                                        @endphp
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($avgPrice, 0, '.', ',') }} FCFA</p>
                                    </div>
                                </div>

                                <!-- Revenue Chart -->
                                @if(!empty($data['revenue_data']) && count($data['revenue_data']) > 0)
                                    <div class="h-80 relative">
                                        <canvas id="revenueChart"></canvas>
                                    </div>

                                    <!-- Daily Stats Table -->
                                    <div class="mt-6 border-t border-gray-200 pt-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-3">Daily Breakdown (Last 7 Days)</h4>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tickets</th>
                                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Avg. Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach(collect($data['revenue_data'])->take(7) as $day)
                                                        <tr>
                                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $day['date'] ?? 'N/A' }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ number_format($day['revenue'] ?? 0, 0, '.', ',') }} FCFA</td>
                                                            <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ $day['ticket_count'] ?? 0 }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-900 text-right">
                                                                @php
                                                                    $avgDayPrice = ($day['ticket_count'] ?? 0) > 0 ? ($day['revenue'] ?? 0) / ($day['ticket_count'] ?? 1) : 0;
                                                                @endphp
                                                                {{ number_format($avgDayPrice, 0, '.', ',') }} FCFA
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <!-- Empty State -->
                                    <div class="h-80 flex flex-col items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <p class="text-lg">No revenue data available</p>
                                        <p class="text-sm">Revenue chart will appear once tickets are sold</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Enhanced Recent Trips with Customer Spending -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Recent Trips & Customer Spending</h3>
                                        <p class="text-sm text-gray-500">Latest trips with detailed customer revenue</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="filterTrips('all')" class="filter-btn active px-3 py-1 text-sm font-medium rounded-md bg-blue-50 text-blue-700" data-filter="all">All</button>
                                        <button onclick="filterTrips('today')" class="filter-btn px-3 py-1 text-sm font-medium rounded-md text-gray-500 hover:bg-gray-50" data-filter="today">Today</button>
                                        <button onclick="filterTrips('week')" class="filter-btn px-3 py-1 text-sm font-medium rounded-md text-gray-500 hover:bg-gray-50" data-filter="week">This Week</button>
                                    </div>
                                </div>

                                <!-- Trip Summary Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-blue-600 font-medium">Total Trip Revenue</p>
                                                <p class="text-xl font-bold text-gray-900">
                                                    {{ number_format(collect($data['recent_trips'] ?? [])->sum('total_revenue'), 0, '.', ',') }} FCFA
                                                </p>
                                            </div>
                                            <i class="fas fa-money-bill-wave text-2xl text-blue-400"></i>
                                        </div>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-green-600 font-medium">Total Passengers</p>
                                                <p class="text-xl font-bold text-gray-900">
                                                    {{ number_format(collect($data['recent_trips'] ?? [])->sum('total_passengers')) }}
                                                </p>
                                            </div>
                                            <i class="fas fa-users text-2xl text-green-400"></i>
                                        </div>
                                    </div>
                                    <div class="bg-purple-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xs text-purple-600 font-medium">Avg. per Trip</p>
                                                @php
                                                    $tripCount = count($data['recent_trips'] ?? []);
                                                    $avgTripRevenue = $tripCount > 0
                                                        ? collect($data['recent_trips'] ?? [])->sum('total_revenue') / $tripCount
                                                        : 0;
                                                @endphp
                                                <p class="text-xl font-bold text-gray-900">{{ number_format($avgTripRevenue, 0, '.', ',') }} FCFA</p>
                                            </div>
                                            <i class="fas fa-chart-line text-2xl text-purple-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Trips List with Customer Details -->
                                <div class="space-y-4" id="tripsList">
                                    @forelse(($data['recent_trips'] ?? []) as $index => $trip)
                                        <div class="trip-item border border-gray-200 rounded-lg hover:shadow-md transition-shadow duration-200"
                                             data-departure="{{ $trip['departure_date'] ?? '' }}">
                                            <div class="p-4">
                                                <!-- Trip Header -->
                                                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex-shrink-0">
                                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex flex-col items-center justify-center">
                                                                <i class="fas fa-clock text-blue-600 text-sm"></i>
                                                                <span class="text-xs font-medium text-blue-600">{{ $trip['departure_time'] ?? '--:--' }}</span>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-sm font-medium text-gray-900">{{ $trip['departure_location'] ?? 'Unknown' }}</span>
                                                                <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                                                                <span class="text-sm font-medium text-gray-900">{{ $trip['arrival_location'] ?? 'Unknown' }}</span>
                                                            </div>
                                                            <div class="flex items-center space-x-3 mt-1">
                                                                <span class="text-xs text-gray-500">
                                                                    <i class="far fa-calendar mr-1"></i> {{ $trip['departure_date'] ?? '' }}
                                                                </span>
                                                                <span class="text-xs text-gray-500">
                                                                    <i class="fas fa-bus mr-1"></i> {{ $trip['bus_registration'] ?? 'N/A' }}
                                                                </span>
                                                                <span class="text-xs text-gray-500">
                                                                    <i class="fas fa-store mr-1"></i> {{ $trip['agency_name'] ?? 'N/A' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Trip Revenue & Status -->
                                                    <div class="mt-3 md:mt-0 flex items-center space-x-4">
                                                        <div class="text-right">
                                                            <p class="text-xs text-gray-500">Trip Revenue</p>
                                                            <p class="text-sm font-bold text-green-600">{{ number_format($trip['total_revenue'] ?? 0, 0, '.', ',') }} FCFA</p>
                                                        </div>

                                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                            @if(($trip['status'] ?? '') === 'scheduled') bg-green-100 text-green-800
                                                            @elseif(($trip['status'] ?? '') === 'in_progress') bg-blue-100 text-blue-800
                                                            @elseif(($trip['status'] ?? '') === 'completed') bg-gray-100 text-gray-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            {{ ucfirst($trip['status'] ?? 'unknown') }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Customer Spending Details -->
                                                @if(!empty($trip['customers']) && count($trip['customers']) > 0)
                                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                                        <div class="flex items-center justify-between mb-3">
                                                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                                <i class="fas fa-users mr-1"></i> Customer Bookings ({{ count($trip['customers']) }} passengers)
                                                            </h4>
                                                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                                                Total: {{ number_format($trip['total_revenue'] ?? 0, 0, '.', ',') }} FCFA
                                                            </span>
                                                        </div>

                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            @foreach($trip['customers'] as $customer)
                                                                <div class="bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors">
                                                                    <div class="flex items-start justify-between">
                                                                        <div class="flex items-center">
                                                                            <div class="flex-shrink-0">
                                                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ml-2">
                                                                                <p class="text-sm font-medium text-gray-900">{{ $customer['name'] ?? 'Unknown' }}</p>
                                                                                <p class="text-xs text-gray-500">
                                                                                    {{ $customer['tickets'] ?? 0 }} ticket(s) ·
                                                                                    <span class="font-medium text-green-600">{{ number_format($customer['spent'] ?? 0, 0, '.', ',') }} FCFA</span>
                                                                                </p>
                                                                                @if(!empty($customer['phone']))
                                                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $customer['phone'] }}</p>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        @if(($customer['tickets'] ?? 0) > 1)
                                                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                                                                Regular
                                                                            </span>
                                                                        @endif
                                                                    </div>

                                                                    @if(!empty($customer['seat_numbers']))
                                                                        <div class="mt-2 flex flex-wrap gap-1">
                                                                            @foreach($customer['seat_numbers'] as $seat)
                                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                                                                    Seat {{ $seat }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif

                                                                    @if(!empty($customer['booking_dates']))
                                                                        <p class="text-xs text-gray-400 mt-2">
                                                                            Booked: {{ \Carbon\Carbon::parse($customer['booking_dates'][0] ?? now())->format('M d, H:i') }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Trip Summary Stats -->
                                                        <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs">
                                                            <div class="bg-gray-50 rounded p-2">
                                                                <p class="text-gray-500">Total Passengers</p>
                                                                <p class="font-bold text-gray-900">{{ $trip['total_passengers'] ?? 0 }}</p>
                                                            </div>
                                                            <div class="bg-gray-50 rounded p-2">
                                                                <p class="text-gray-500">Occupancy</p>
                                                                <p class="font-bold {{ ($trip['occupancy_rate'] ?? 0) >= 80 ? 'text-green-600' : 'text-yellow-600' }}">
                                                                    {{ $trip['occupancy_rate'] ?? 0 }}%
                                                                </p>
                                                            </div>
                                                            <div class="bg-gray-50 rounded p-2">
                                                                <p class="text-gray-500">Avg. Spend</p>
                                                                <p class="font-bold text-gray-900">
                                                                    @php
                                                                        $avgSpend = ($trip['total_passengers'] ?? 0) > 0
                                                                            ? ($trip['total_revenue'] ?? 0) / ($trip['total_passengers'] ?? 1)
                                                                            : 0;
                                                                    @endphp
                                                                    {{ number_format($avgSpend, 0, '.', ',') }} FCFA
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- No customers yet -->
                                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                                        <div class="text-center py-3 bg-gray-50 rounded-lg">
                                                            <i class="fas fa-user-clock text-gray-400 text-2xl mb-2"></i>
                                                            <p class="text-sm text-gray-500">No bookings yet for this trip</p>
                                                            <p class="text-xs text-gray-400">Bookings will appear here once customers purchase tickets</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Quick Actions -->
                                                <div class="mt-4 flex justify-end space-x-2">
                                                    <button onclick="viewTripDetails({{ $trip['id'] ?? 0 }})"
                                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                        <i class="fas fa-eye mr-1"></i> View Details
                                                    </button>
                                                    <button onclick="exportTripData({{ $trip['id'] ?? 0 }})"
                                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                        <i class="fas fa-download mr-1"></i> Export
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <!-- Empty State -->
                                        <div class="text-center py-12">
                                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                                <i class="fas fa-route text-3xl text-gray-400"></i>
                                            </div>
                                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Recent Trips</h4>
                                            <p class="text-sm text-gray-500 mb-6">There are no trips scheduled in the system yet.</p>
                                            <a href="{{ route('my-agency.trips.create') }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                <i class="fas fa-plus mr-2"></i> Schedule Your First Trip
                                            </a>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Load More Button -->
                                @if(count($data['recent_trips'] ?? []) >= 5)
                                    <div class="mt-6 text-center">
                                        <button onclick="loadMoreTrips()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-sync-alt mr-2"></i> Load More Trips
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Top Agencies -->
                        @if(!empty($data['top_agencies']) && count($data['top_agencies']) > 0)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Top Performing Agencies (30 days)</h3>
                                <div class="space-y-4">
                                    @foreach($data['top_agencies'] as $agency)
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-medium text-gray-700">{{ $agency->name ?? 'Unknown' }}</span>
                                                <span class="text-sm text-gray-600">{{ number_format($agency->revenue ?? 0, 0, '.', ',') }} FCFA</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                @php
                                                    $maxRevenue = $data['top_agencies']->max('revenue') ?? 1;
                                                    $width = $maxRevenue > 0 ? (($agency->revenue ?? 0) / $maxRevenue * 100) : 0;
                                                @endphp
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $width }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">{{ number_format($agency->ticket_count ?? 0) }} tickets sold</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                    @elseif(auth()->user()->role === 'company_admin')
                        <!-- Revenue Overview for Company Admin -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <!-- Header -->
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Revenue Overview</h3>
                                        <p class="text-sm text-gray-500">Company performance overview</p>
                                    </div>
                                    <div class="mt-2 sm:mt-0 text-right">
                                        <p class="text-sm text-gray-500">Monthly Revenue</p>
                                        <p class="text-2xl font-bold text-gray-900">
                                            {{ number_format($data['monthly_revenue'] ?? 0, 0, '.', ',') }} FCFA
                                        </p>
                                        @if(($data['revenue_growth'] ?? 0) != 0)
                                            <p class="text-xs {{ ($data['revenue_growth'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ ($data['revenue_growth'] ?? 0) >= 0 ? '↑' : '↓' }}
                                                {{ number_format(abs($data['revenue_growth'] ?? 0), 1) }}% vs last month
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Key Metrics -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Total Revenue</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['total_revenue'] ?? 0, 0, '.', ',') }} FCFA</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Monthly Tickets</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['stats']['monthly_tickets'] ?? 0) }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Total Agencies</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['stats']['total_agencies'] ?? 0) }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Total Buses</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['stats']['total_buses'] ?? 0) }}</p>
                                    </div>
                                </div>

                                <!-- Revenue Trend Chart -->
                                @if(!empty($data['revenue_trend']) && count($data['revenue_trend']) > 0)
                                    <div class="h-64">
                                        <canvas id="revenueChart"></canvas>
                                    </div>
                                @else
                                    <div class="h-64 flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
                                        <p>No revenue data available for the selected period</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Agency Performance -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Agency Performance</h3>
                                    <a href="{{ route('my-company.agencies.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        Manage agencies
                                    </a>
                                </div>
                                <div class="space-y-6">
                                    @forelse(($data['agency_stats'] ?? []) as $stat)
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $stat['name'] ?? 'Unknown Agency' }}</p>
                                                    <div class="flex items-center space-x-4 mt-1">
                                                        <span class="text-xs text-gray-500">
                                                            <i class="fas fa-bus mr-1"></i> {{ $stat['bus_count'] ?? 0 }} buses
                                                        </span>
                                                        <span class="text-xs text-gray-500">
                                                            <i class="fas fa-route mr-1"></i> {{ $stat['trip_count'] ?? 0 }} trips
                                                        </span>
                                                        <span class="text-xs text-gray-500">
                                                            <i class="fas fa-ticket-alt mr-1"></i> {{ $stat['ticket_count'] ?? 0 }} tickets
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-semibold text-gray-900">
                                                        {{ number_format($data['monthly_revenue'] ?? 0, 0, '.', ',') }} FCFA
                                                    </p>
                                                    <p class="text-xs text-gray-500">this month</p>
                                                </div>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                @php
                                                    $utilization = $stat['utilization_rate'] ?? 0;
                                                @endphp
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $utilization }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Utilization: {{ $utilization }}%</p>
                                            @if(($stat['upcoming_trips'] ?? 0) > 0)
                                                <p class="text-xs text-green-600 mt-1">
                                                    <i class="fas fa-calendar"></i> {{ $stat['upcoming_trips'] }} upcoming trips
                                                </p>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 text-center py-4">No agency statistics available</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Popular Routes -->
                        @if(!empty($data['popular_routes']) && count($data['popular_routes']) > 0)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Popular Routes (30 days)</h3>
                                <div class="space-y-3">
                                    @foreach($data['popular_routes'] as $route)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $route['route'] ?? 'Unknown' }}</p>
                                                <div class="flex items-center space-x-3 mt-1">
                                                    <span class="text-xs text-gray-500">
                                                        <i class="fas fa-ticket-alt mr-1"></i> {{ number_format($route['ticket_count'] ?? 0) }} tickets
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        <i class="fas fa-bus mr-1"></i> {{ number_format($route['trip_count'] ?? 0) }} trips
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ number_format($route['revenue'] ?? 0, 0, '.', ',') }} FCFA
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                    @elseif(auth()->user()->role === 'agency_admin')
                        <!-- Revenue Overview for Agency Admin -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <!-- Header -->
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Revenue Overview</h3>
                                        <p class="text-sm text-gray-500">Agency performance metrics</p>
                                    </div>
                                    <div class="mt-2 sm:mt-0 grid grid-cols-2 gap-4">
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Monthly Revenue</p>
                                            <p class="text-xl font-bold text-gray-900">
                                                {{ number_format($data['stats']['monthly_revenue'] ?? 0, 0, '.', ',') }} FCFA
                                            </p>
                                        </div>
                                        <div class="text-right border-l pl-4">
                                            <p class="text-sm text-gray-500">Monthly Tickets</p>
                                            <p class="text-xl font-bold text-gray-900">
                                                {{ number_format($data['stats']['monthly_tickets'] ?? 0) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Key Metrics Cards -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Weekly Revenue</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['weekly_revenue'] ?? 0, 0, '.', ',') }} FCFA</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Weekly Tickets</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['weekly_tickets'] ?? 0) }}</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Avg. Occupancy</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['stats']['avg_occupancy'] ?? 0) }}%</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-xs text-gray-500">Unique Customers</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($data['unique_customers'] ?? 0) }}</p>
                                    </div>
                                </div>

                                <!-- Hourly Distribution (if available) -->
                                @if(!empty($data['hourly_distribution']) && count($data['hourly_distribution']) > 0)
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-3">Today's Bookings by Hour</h4>
                                        <div class="h-40">
                                            <canvas id="hourlyChart"></canvas>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Today's Trips - FIXED SECTION -->
                        @if(!empty($data['today_trips_details']) && count($data['today_trips_details']) > 0)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Trips</h3>
                                <div class="space-y-4">
                                    @foreach($data['today_trips_details'] as $trip)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-medium text-gray-900">
                                                        {{ $trip['departure_location'] ?? 'Unknown' }} → {{ $trip['arrival_location'] ?? 'Unknown' }}
                                                    </p>
                                                    <div class="flex items-center mt-2 space-x-4">
                                                        <span class="text-sm text-gray-600">
                                                            <i class="fas fa-clock mr-1"></i> {{ $trip['departure_time'] ?? '--:--' }}
                                                        </span>
                                                        <span class="text-sm text-gray-600">
                                                            <i class="fas fa-bus mr-1"></i> {{ $trip['bus_registration'] ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                        @if(($trip['occupancy_rate'] ?? 0) >= 80) bg-green-100 text-green-800
                                                        @elseif(($trip['occupancy_rate'] ?? 0) >= 50) bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800 @endif">
                                                        {{ $trip['occupancy_rate'] ?? 0 }}% full
                                                    </span>
                                                    <p class="text-sm text-gray-600 mt-2">
                                                        <!-- FIXED: Use proper null coalescing with fallback values -->
                                                        {{ $trip['booked_seats'] ?? $trip['ticket_count'] ?? 0 }}/{{ $trip['total_seats'] ?? $trip['capacity'] ?? 0 }} seats
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Recent Bookings -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Recent Bookings</h3>
                                    <a href="{{ route('tickets.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        View all
                                    </a>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse(($data['recent_bookings'] ?? []) as $booking)
                                                <tr>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $booking['customer_name'] ?? 'N/A' }}</div>
                                                        <div class="text-xs text-gray-500">{{ $booking['customer_phone'] ?? '' }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $booking['route'] ?? 'Unknown' }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $booking['departure_date'] ?? '' }}</div>
                                                        <div class="text-xs text-gray-500">{{ $booking['departure_time'] ?? '' }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ number_format($booking['price'] ?? 0, 0, '.', ',') }} FCFA</div>
                                                        <div class="text-xs text-gray-500">{{ $booking['purchase_date'] ?? '' }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            @if(($booking['status'] ?? '') === 'confirmed') bg-green-100 text-green-800
                                                            @elseif(($booking['status'] ?? '') === 'pending') bg-yellow-100 text-yellow-800
                                                            @elseif(($booking['status'] ?? '') === 'used') bg-blue-100 text-blue-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            {{ ucfirst($booking['status'] ?? 'unknown') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                        No recent bookings
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Schedule -->
                        @if(!empty($data['weekly_trips']) && count($data['weekly_trips']) > 0)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">This Week's Schedule</h3>
                                <div class="space-y-3">
                                    @foreach($data['weekly_trips'] as $trip)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0 w-16 text-center">
                                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($trip['date'] ?? now())->format('D') }}</p>
                                                    <p class="text-sm font-medium">{{ \Carbon\Carbon::parse($trip['date'] ?? now())->format('d') }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $trip['departure_location'] ?? 'Unknown' }} → {{ $trip['arrival_location'] ?? 'Unknown' }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $trip['departure_time'] ?? '--:--' }} · {{ $trip['bus_registration'] ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium {{ ($trip['available_seats'] ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $trip['available_seats'] ?? 0 }} seats left
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $trip['occupancy_rate'] ?? 0 }}% occupied</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                    @elseif(auth()->user()->role === 'customer')
                        <!-- Customer Spending Overview -->
                        @if(!empty($data['monthly_spending']) && count($data['monthly_spending']) > 0)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Your Spending Overview</h3>
                                        <p class="text-sm text-gray-500">Last 6 months</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Total Spent</p>
                                        <p class="text-xl font-bold text-gray-900">
                                            {{ number_format($data['stats']['total_spent'] ?? 0, 0, '.', ',') }} FCFA
                                        </p>
                                    </div>
                                </div>

                                <!-- Spending Chart -->
                                <div class="h-48">
                                    <canvas id="spendingChart"></canvas>
                                </div>

                                <!-- Quick Stats -->
                                <div class="grid grid-cols-3 gap-4 mt-4">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Avg. Monthly</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            @php
                                                $avgMonthly = count($data['monthly_spending'] ?? []) > 0
                                                    ? collect($data['monthly_spending'])->avg('total')
                                                    : 0;
                                            @endphp
                                            {{ number_format($avgMonthly, 0, '.', ',') }} FCFA
                                        </p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Tickets</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ number_format($data['stats']['total_tickets'] ?? 0) }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">Avg. per Ticket</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            @php
                                                $avgTicket = ($data['stats']['total_tickets'] ?? 0) > 0
                                                    ? ($data['stats']['total_spent'] ?? 0) / ($data['stats']['total_tickets'] ?? 1)
                                                    : 0;
                                            @endphp
                                            {{ number_format($avgTicket, 0, '.', ',') }} FCFA
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Upcoming Trips -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Upcoming Trips</h3>
                                    <a href="{{ route('tickets.booking.search') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        Book new trip
                                    </a>
                                </div>
                                <div class="space-y-4">
                                    @forelse(($data['upcoming_trips'] ?? []) as $ticket)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-bus text-blue-600"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-gray-900">{{ $ticket['route'] ?? 'Unknown' }}</p>
                                                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                                                <span><i class="fas fa-calendar mr-1"></i> {{ $ticket['departure_date'] ?? '' }}</span>
                                                                <span class="mx-2">•</span>
                                                                <span><i class="fas fa-clock mr-1"></i> {{ $ticket['departure_time'] ?? '' }}</span>
                                                                @if(!empty($ticket['seat_numbers']))
                                                                    <span class="mx-2">•</span>
                                                                    <span>Seat {{ implode(', ', $ticket['seat_numbers']) }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                        @if(($ticket['status'] ?? '') === 'confirmed') bg-green-100 text-green-800
                                                        @elseif(($ticket['status'] ?? '') === 'pending') bg-yellow-100 text-yellow-800 @endif">
                                                        {{ ucfirst($ticket['status'] ?? 'unknown') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex items-center justify-between">
                                                <div>
                                                    <p class="text-xs text-gray-500">Reference: {{ $ticket['reference'] ?? 'N/A' }}</p>
                                                    @if(isset($ticket['cancellation_deadline']))
                                                        <p class="text-xs text-red-500 mt-1">
                                                            <i class="fas fa-clock"></i> Cancel before {{ $ticket['cancellation_deadline'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('tickets.show', $ticket['id'] ?? 0) }}"
                                                       class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                        <i class="fas fa-eye mr-1"></i> View
                                                    </a>
                                                    @if($ticket['can_cancel'] ?? false)
                                                        <button onclick="cancelTicket({{ $ticket['id'] ?? 0 }})"
                                                                class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                                            <i class="fas fa-times mr-1"></i> Cancel
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8">
                                            <i class="fas fa-route text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-gray-500">No upcoming trips booked</p>
                                            <a href="{{ route('tickets.booking.search') }}"
                                               class="inline-flex items-center px-4 py-2 mt-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                <i class="fas fa-search mr-2"></i> Find Trips
                                            </a>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Travel History -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Travel History</h3>
                                    @if(isset($data['customer_since']))
                                        <a href="{{ route('customers.history', auth()->user()->customer->customer_id ?? '') }}"
                                           class="text-sm text-blue-600 hover:text-blue-500">
                                            View all
                                        </a>
                                    @endif
                                </div>
                                <div class="space-y-3">
                                    @forelse(($data['past_trips'] ?? []) as $ticket)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-check text-gray-600 text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">{{ $ticket['route'] ?? 'Unknown' }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $ticket['departure_date'] ?? '' }} ·
                                                        {{ number_format($ticket['price'] ?? 0, 0, '.', ',') }} FCFA
                                                        @if(($ticket['status'] ?? '') === 'cancelled')
                                                            <span class="text-red-500">(Cancelled)</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                {{ isset($ticket['departure_date']) ? \Carbon\Carbon::parse($ticket['departure_date'])->diffForHumans() : '' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 text-center py-4">No travel history</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Favorite Route -->
                        @if(isset($data['favorite_route']))
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Favorite Route</h3>
                                <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-star text-yellow-400 text-2xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-lg font-semibold text-gray-900">{{ $data['favorite_route'] }}</p>
                                        <p class="text-sm text-gray-600">Your most traveled route</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @if(auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.companies.create') }}"
                                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-plus-circle text-blue-600 group-hover:text-blue-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Create Company</p>
                                            <p class="text-xs text-gray-500">Register new travel company</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('admin.agencies.create') }}"
                                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-store-alt text-green-600 group-hover:text-green-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Create Agency</p>
                                            <p class="text-xs text-gray-500">Add new agency location</p>
                                        </div>
                                    </a>
                                @endif

                                @if(auth()->user()->role === 'company_admin')
                                    <!-- View Company Details -->
                                    <a href="{{ route('my-company.companies.show', auth()->user()->company->id_company ?? '') }}"
                                    class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg group mb-2">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-building text-blue-600 group-hover:text-blue-700"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">Company Details</p>
                                            <p class="text-xs text-gray-500">View and manage {{ auth()->user()->company->name ?? 'company' }}</p>
                                        </div>
                                        <div class="ml-2">
                                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                        </div>
                                    </a>

                                    <!-- Edit Company -->
                                    <a href="{{ route('my-company.companies.edit', auth()->user()->company->id_company ?? '') }}"
                                    class="flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg group mb-2">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-edit text-indigo-600 group-hover:text-indigo-700"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">Edit Company</p>
                                            <p class="text-xs text-gray-500">Update company information</p>
                                        </div>
                                        <div class="ml-2">
                                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                        </div>
                                    </a>

                                    <a href="{{ route('my-company.agencies.create') }}"
                                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-store-alt text-green-600 group-hover:text-green-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Create Agency</p>
                                            <p class="text-xs text-gray-500">Add new agency</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('my-company.buses.create') }}"
                                       class="flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-bus text-yellow-600 group-hover:text-yellow-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Add Bus</p>
                                            <p class="text-xs text-gray-500">Register new bus</p>
                                        </div>
                                    </a>
                                @endif

                                @if(in_array(auth()->user()->role, ['agency_admin']))

                                    @php
                                        // Get agency ID safely from the user object
                                        $agencyId = auth()->user()->agence->id_agence ?? null;

                                        // If not in agency_id property, try to get from relationship
                                        if (!$agencyId && auth()->user()->agency) {
                                            $agencyId = auth()->user()->company()->agency->id_agence ?? null;
                                        }

                                        $agencyName = auth()->user()->agency->name ?? 'Agency';
                                    @endphp

                                     <!-- View Agency Details -->
                                    @if($agencyId)
                                        <a href="{{ route('my-agency.agencies.show', $agencyId) }}"
                                        class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg group mb-2">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-building text-blue-600 group-hover:text-blue-700"></i>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-gray-900">Agency Details</p>
                                                <p class="text-xs text-gray-500">View and manage {{ $agencyName }}</p>
                                            </div>
                                            <div class="ml-2">
                                                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                            </div>
                                        </a>

                                        <!-- Edit Agency -->
                                        <a href="{{ route('my-agency.agencies.edit', $agencyId) }}"
                                        class="flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg group mb-2">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-edit text-indigo-600 group-hover:text-indigo-700"></i>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-gray-900">Edit Agency</p>
                                                <p class="text-xs text-gray-500">Update agency information</p>
                                            </div>
                                            <div class="ml-2">
                                                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                            </div>
                                        </a>
                                    @else
                                        <div class="flex items-center p-3 bg-gray-100 rounded-lg mb-2 opacity-50 cursor-not-allowed">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-building text-gray-400"></i>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-gray-500">Agency Details</p>
                                                <p class="text-xs text-gray-400">No agency assigned</p>
                                            </div>
                                        </div>
                                    @endif

                                    <a href="{{ route('my-agency.trips.create') }}"
                                       class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-route text-purple-600 group-hover:text-purple-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Schedule Trip</p>
                                            <p class="text-xs text-gray-500">Create new trip</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('my-agency.buses.create') }}"
                                       class="flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-bus text-yellow-600 group-hover:text-yellow-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Add Bus</p>
                                            <p class="text-xs text-gray-500">Register new bus</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('tickets.sell') }}"
                                       class="flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-ticket-alt text-indigo-600 group-hover:text-indigo-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Sell Ticket</p>
                                            <p class="text-xs text-gray-500">Manual booking</p>
                                        </div>
                                    </a>
                                @endif

                                @if(auth()->user()->role === 'customer')
                                    <a href="{{ route('tickets.booking.search') }}"
                                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-search text-blue-600 group-hover:text-blue-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">Find Trips</p>
                                            <p class="text-xs text-gray-500">Search and book trips</p>
                                        </div>
                                    </a>

                                    <a href="{{ route('tickets.my') }}"
                                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg group">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-history text-green-600 group-hover:text-green-700"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">My Bookings</p>
                                            <p class="text-xs text-gray-500">View all tickets</p>
                                        </div>
                                    </a>
                                @endif

                                <a href="{{ route('reports.index') }}"
                                   class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg group">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chart-bar text-gray-600 group-hover:text-gray-700"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Generate Report</p>
                                        <p class="text-xs text-gray-500">Create custom reports</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics Summary -->
                    @if(auth()->user()->role === 'agency_admin' && isset($data['stats']))
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Key Metrics</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-blue-600">{{ number_format($data['stats']['avg_occupancy'] ?? 0) }}%</p>
                                    <p class="text-xs text-gray-500">Avg. Occupancy</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($data['unique_customers'] ?? 0) }}</p>
                                    <p class="text-xs text-gray-500">Unique Customers</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-purple-600">{{ number_format($data['new_customers'] ?? 0) }}</p>
                                    <p class="text-xs text-gray-500">New Customers</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($data['weekly_tickets'] ?? 0) }}</p>
                                    <p class="text-xs text-gray-500">This Week</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Customer Summary -->
                    @if(auth()->user()->role === 'customer' && isset($data['stats']))
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Your Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Spent:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($data['stats']['total_spent'] ?? 0, 0, '.', ',') }} FCFA</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Tickets Purchased:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($data['stats']['total_tickets'] ?? 0) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Cancelled:</span>
                                    <span class="text-sm font-semibold text-red-600">{{ number_format($data['cancelled_tickets'] ?? 0) }}</span>
                                </div>
                                @if(isset($data['last_purchase_date']))
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Last Purchase:</span>
                                    <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($data['last_purchase_date'])->format('M d, Y') }}</span>
                                </div>
                                @endif
                                @if(isset($data['favorite_route']))
                                <div class="pt-2 mt-2 border-t border-gray-200">
                                    <p class="text-xs text-gray-500 mb-1">Favorite Route:</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $data['favorite_route'] }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- System Status -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">System Status</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">API Status</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-xs mr-1"></i> Operational
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Database</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-xs mr-1"></i> Connected
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Last Backup</span>
                                    <span class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse(now()->subDays(1))->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Active Users</span>
                                    <span class="text-sm text-gray-900">
                                        {{ \DB::table('sessions')->where('last_activity', '>=', now()->subMinutes(5)->timestamp)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Support Info -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Need Help?</h3>
                            <div class="space-y-3">
                                <a href="#" class="flex items-center p-2 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-question-circle text-blue-600 w-6"></i>
                                    <span class="text-sm text-gray-700">Documentation</span>
                                </a>
                                <a href="#" class="flex items-center p-2 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-envelope text-blue-600 w-6"></i>
                                    <span class="text-sm text-gray-700">Contact Support</span>
                                </a>
                                <a href="#" class="flex items-center p-2 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-comment text-blue-600 w-6"></i>
                                    <span class="text-sm text-gray-700">Live Chat</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Insights Modal -->
    <div id="customerInsightsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-chart-pie text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Customer Spending Insights
                            </h3>
                            <div class="mt-4" id="modalContent">
                                <!-- Dynamic content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Revenue Chart
                const revenueCtx = document.getElementById('revenueChart');
                if (revenueCtx) {
                    try {
                        @if(auth()->user()->role === 'super_admin')
                            const revenueData = @json($data['revenue_data'] ?? []);
                        @elseif(auth()->user()->role === 'company_admin')
                            const revenueData = @json($data['revenue_trend'] ?? []);
                        @else
                            const revenueData = [];
                        @endif

                        if (revenueData && revenueData.length > 0) {
                            const ctx2d = revenueCtx.getContext('2d');
                            const dates = revenueData.map(item => item.date || '');
                            const revenues = revenueData.map(item => item.revenue || 0);

                            // Calculate gradient
                            const gradient = ctx2d.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

                            new Chart(ctx2d, {
                                type: 'line',
                                data: {
                                    labels: dates,
                                    datasets: [{
                                        label: 'Revenue (FCFA)',
                                        data: revenues,
                                        backgroundColor: gradient,
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 2,
                                        tension: 0.4,
                                        fill: true,
                                        pointBackgroundColor: 'rgb(59, 130, 246)',
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2,
                                        pointRadius: 3,
                                        pointHoverRadius: 5
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                            titleColor: '#F9FAFB',
                                            bodyColor: '#F3F4F6',
                                            callbacks: {
                                                label: function(context) {
                                                    return 'Revenue: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            grid: {
                                                color: 'rgba(156, 163, 175, 0.1)',
                                            },
                                            ticks: {
                                                callback: function(value) {
                                                    return new Intl.NumberFormat('fr-FR', {
                                                        notation: 'compact',
                                                        compactDisplay: 'short'
                                                    }).format(value) + ' FCFA';
                                                }
                                            }
                                        },
                                        x: {
                                            grid: {
                                                display: false
                                            },
                                            ticks: {
                                                maxTicksLimit: 7
                                            }
                                        }
                                    }
                                }
                            });
                        } else if (revenueCtx.parentElement) {
                            revenueCtx.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">No revenue data available</p>';
                        }
                    } catch (e) {
                        console.error('Error initializing revenue chart:', e);
                    }
                }

                // Spending Chart (for customers)
                const spendingCtx = document.getElementById('spendingChart');
                if (spendingCtx) {
                    try {
                        const spendingData = @json($data['monthly_spending'] ?? []);

                        if (spendingData && spendingData.length > 0) {
                            const ctx2d = spendingCtx.getContext('2d');
                            const months = spendingData.map(item => item.month || '');
                            const totals = spendingData.map(item => item.total || 0);

                            new Chart(ctx2d, {
                                type: 'bar',
                                data: {
                                    labels: months,
                                    datasets: [{
                                        label: 'Spending (FCFA)',
                                        data: totals,
                                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                        borderColor: 'rgb(245, 158, 11)',
                                        borderWidth: 2,
                                        borderRadius: 4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return 'Spent: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return new Intl.NumberFormat('fr-FR', {
                                                        notation: 'compact',
                                                        compactDisplay: 'short'
                                                    }).format(value) + ' FCFA';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    } catch (e) {
                        console.error('Error initializing spending chart:', e);
                    }
                }

                // Hourly Distribution Chart (for agency admin)
                const hourlyCtx = document.getElementById('hourlyChart');
                if (hourlyCtx) {
                    try {
                        const hourlyData = @json($data['hourly_distribution'] ?? []);

                        if (hourlyData && Object.keys(hourlyData).length > 0) {
                            const ctx2d = hourlyCtx.getContext('2d');
                            const hours = Object.keys(hourlyData);
                            const counts = Object.values(hourlyData);

                            new Chart(ctx2d, {
                                type: 'bar',
                                data: {
                                    labels: hours,
                                    datasets: [{
                                        label: 'Bookings',
                                        data: counts,
                                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                        borderColor: 'rgb(16, 185, 129)',
                                        borderWidth: 2,
                                        borderRadius: 4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    } catch (e) {
                        console.error('Error initializing hourly chart:', e);
                    }
                }
            });

            // Filter trips by date
            function filterTrips(filter) {
                // Update active button state
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active', 'bg-blue-50', 'text-blue-700');
                    btn.classList.add('text-gray-500', 'hover:bg-gray-50');
                });
                event.target.classList.add('active', 'bg-blue-50', 'text-blue-700');
                event.target.classList.remove('text-gray-500', 'hover:bg-gray-50');

                const trips = document.querySelectorAll('.trip-item');
                const today = new Date().toISOString().split('T')[0];
                const weekAgo = new Date();
                weekAgo.setDate(weekAgo.getDate() - 7);

                trips.forEach(trip => {
                    const departureDate = trip.dataset.departure;
                    if (!departureDate) return;

                    switch(filter) {
                        case 'all':
                            trip.style.display = 'block';
                            break;
                        case 'today':
                            trip.style.display = departureDate === today ? 'block' : 'none';
                            break;
                        case 'week':
                            const tripDate = new Date(departureDate);
                            trip.style.display = tripDate >= weekAgo ? 'block' : 'none';
                            break;
                    }
                });
            }

            // View trip details
            function viewTripDetails(tripId) {
                if (!tripId) return;
                window.location.href = `/trips/${tripId}`;
            }

            // Export trip data
            function exportTripData(tripId) {
                if (!tripId) return;
                window.location.href = `/trips/${tripId}/export`;
            }

            // Load more trips (pagination)
            function loadMoreTrips() {
                const button = event.target.closest('button');
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Loading...';

                // Simulate AJAX call - replace with actual endpoint
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Load More Trips';
                    // Add logic to append more trips
                }, 1500);
            }

            // Show customer insights
            function showCustomerInsights(tripId, customerId) {
                const modal = document.getElementById('customerInsightsModal');
                const modalContent = document.getElementById('modalContent');

                // Fetch customer insights data
                fetch(`/api/trips/${tripId}/customers/${customerId}/insights`)
                    .then(response => response.json())
                    .then(data => {
                        let html = `
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-500">Total Spent</p>
                                        <p class="text-lg font-bold text-gray-900">${new Intl.NumberFormat('fr-FR').format(data.totalSpent)} FCFA</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-500">Trips Taken</p>
                                        <p class="text-lg font-bold text-gray-900">${data.tripsCount}</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-2">Favorite Route</p>
                                    <p class="text-sm font-medium text-gray-900">${data.favoriteRoute}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-2">Member Since</p>
                                    <p class="text-sm text-gray-900">${data.memberSince}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-2">Recent Bookings</p>
                                    <div class="space-y-2">
                                        ${data.recentBookings.map(booking => `
                                            <div class="text-xs">
                                                <span class="text-gray-600">${booking.date}:</span>
                                                <span class="font-medium">${booking.route}</span>
                                                <span class="text-green-600">${new Intl.NumberFormat('fr-FR').format(booking.amount)} FCFA</span>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        `;
                        modalContent.innerHTML = html;
                        modal.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching customer insights:', error);
                        alert('Unable to load customer insights. Please try again.');
                    });
            }

            // Close modal
            function closeModal() {
                document.getElementById('customerInsightsModal').classList.add('hidden');
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('customerInsightsModal');
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            }

            // Auto-refresh for real-time data (for agency/company admins)
            @if(in_array(auth()->user()->role, ['agency_admin', 'company_admin', 'super_admin']))
                // Check for new bookings every 2 minutes
                setInterval(function() {
                    fetch('/api/recent-bookings/check')
                        .then(response => response.json())
                        .then(data => {
                            if (data.hasNew) {
                                // Show notification
                                const notification = document.createElement('div');
                                notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-bounce';
                                notification.innerHTML = '<i class="fas fa-bell mr-2"></i> New bookings available!';
                                document.body.appendChild(notification);
                                setTimeout(() => notification.remove(), 5000);
                            }
                        })
                        .catch(error => console.error('Error checking for updates:', error));
                }, 120000); // Check every 2 minutes
            @endif
        </script>
    @endpush
</x-app-layout>
