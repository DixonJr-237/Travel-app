<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @php
                $user = auth()->user();
                $isSuperAdmin = $user->hasRole('super_admin');
                $isCompanyAdmin = $user->hasRole('company_admin');
                $isAgencyAdmin = $user->hasRole('agency_admin');
            @endphp

            @if($isSuperAdmin)
                Agencies Management
            @elseif($isCompanyAdmin)
                Company Agencies
            @elseif($isAgencyAdmin)
                My Agency
            @else
                Agencies
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header with Actions -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        @if($isSuperAdmin)
                            All Agencies Across All Companies
                        @elseif($isCompanyAdmin)
                            Your Company's Agencies
                        @elseif($isAgencyAdmin)
                            Agency Details
                        @endif
                    </h3>
                    <p class="text-sm text-gray-500">
                        @if($isSuperAdmin)
                            Manage all agency locations across companies ({{ $stats['total_companies'] ?? 0 }} companies)
                        @elseif($isCompanyAdmin)
                            Manage your company's agency locations ({{ $stats['my_companies'] ?? 0 }} companies)
                        @elseif($isAgencyAdmin)
                            View and manage your agency information
                        @endif
                    </p>
                </div>

                {{-- Add Agency Button --}}
                @if($isSuperAdmin && Route::has('admin.agencies.create'))
                    <a href="{{ route('admin.agencies.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Agency
                    </a>
                @elseif($isCompanyAdmin && !($hasAgency ?? false) && Route::has('my-agency.create'))
                    <a href="{{ route('my-agency.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Agency
                    </a>
                @endif
            </div>

            @if($isSuperAdmin || $isCompanyAdmin)
                {{-- Filters Section --}}
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ request()->url() }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @if($isSuperAdmin)
                        <div>
                            <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                            <select name="company_id" id="company_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Companies</option>
                                @if(isset($stats['agencies_without_company']) && $stats['agencies_without_company'] > 0)
                                    <option value="none" {{ request('company_id') == 'none' ? 'selected' : '' }}>
                                        -- Without Company ({{ $stats['agencies_without_company'] }}) --
                                    </option>
                                @endif
                                @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id_company }}" {{ request('company_id') == $company->id_company ? 'selected' : '' }}>
                                        {{ $company->name }}
                                        @if(isset($company->admin))
                                            (Admin: {{ $company->admin->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div>
                            <label for="city_id" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <select name="city_id" id="city_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Cities</option>
                                @foreach($cities ?? [] as $city)
                                    <option value="{{ $city->id_city }}" {{ request('city_id') == $city->id_city ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Status</option>
                                @foreach($statusOptions ?? [] as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Name, email, phone..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-4 flex justify-end space-x-2">
                            <a href="{{ request()->url() }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear Filters
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Agencies</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_agencies'] ?? 0 }}</p>
                                    @if($isSuperAdmin && isset($stats['agencies_without_company']) && $stats['agencies_without_company'] > 0)
                                        <p class="text-xs text-gray-500">{{ $stats['agencies_without_company'] }} without company</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Active Agencies</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_agencies'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Pending Agencies</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_agencies'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Buses</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_buses'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Agencies Table --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agency</th>
                                    @if($isSuperAdmin)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Admin</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agency Admin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buses</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($agencies ?? [] as $agency)
                                    @php
                                        $isMyCompanyAgency = $isCompanyAdmin && isset($userCompanyIds) && in_array($agency->id_company, $userCompanyIds);
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $isMyCompanyAgency ? 'bg-blue-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-sm">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $agency->name }}</div>
                                                    <div class="text-xs text-gray-500">ID: {{ $agency->id_agence }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        @if($isSuperAdmin)
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($agency->company_data)
                                                    <div class="text-sm text-gray-900">{{ $agency->company_data->name }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        Status: {{ ucfirst($agency->company_data->status ?? 'active') }}
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500 italic">No company</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(isset($agency->company_data) && isset($agency->company_data->admin))
                                                    <div class="text-sm text-gray-900">{{ $agency->company_data->admin->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $agency->company_data->admin->email }}</div>
                                                @else
                                                    <span class="text-sm text-gray-500">N/A</span>
                                                @endif
                                            </td>
                                        @endif

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if(isset($agency->coordinates_data) && isset($agency->coordinates_data->city))
                                                    {{ $agency->coordinates_data->city->name }}
                                                @else
                                                    {{ $agency->city ?? 'N/A' }}
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 truncate max-w-xs">
                                                {{ $agency->coordinates_data->address ?? $agency->address ?? 'No address' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(isset($agency->user_data))
                                                <div class="text-sm text-gray-900">{{ $agency->user_data->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $agency->user_data->email }}</div>
                                            @else
                                                <span class="text-sm text-gray-500">No admin assigned</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $agency->phone }}</div>
                                            <div class="text-xs text-gray-500">{{ $agency->email }}</div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $agency->buses_count ?? 0 }} buses
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $status = $agency->status ?? 'active';
                                                $statusColors = [
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'suspended' => 'bg-red-100 text-red-800',
                                                    'inactive' => 'bg-gray-100 text-gray-800'
                                                ];
                                                $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                {{-- View button --}}
                                                @if($isSuperAdmin && Route::has('admin.agencies.show'))
                                                    <a href="{{ route('admin.agencies.show', $agency->id_agence) }}"
                                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                                                       title="View Details">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                {{-- Edit button --}}
                                                @if($isSuperAdmin && Route::has('admin.agencies.edit'))
                                                    <a href="{{ route('admin.agencies.edit', $agency->id_agence) }}"
                                                       class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                                       title="Edit Agency">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                @elseif($isCompanyAdmin && $isMyCompanyAgency && Route::has('my-agency.edit'))
                                                    <a href="{{ route('my-agency.edit') }}"
                                                       class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                                       title="Edit Agency">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                {{-- Status toggle buttons for super admin --}}
                                                @if($isSuperAdmin)
                                                    @if($status !== 'active' && Route::has('admin.agencies.activate'))
                                                        <form action="{{ route('admin.agencies.activate', $agency->id_agence) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                                                    title="Activate"
                                                                    onclick="return confirm('Are you sure you want to activate this agency?')">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($status !== 'suspended' && Route::has('admin.agencies.suspend'))
                                                        <form action="{{ route('admin.agencies.suspend', $agency->id_agence) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="text-yellow-600 hover:text-yellow-900 transition-colors duration-150"
                                                                    title="Suspend"
                                                                    onclick="return confirm('Are you sure you want to suspend this agency?')">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($status !== 'inactive' && Route::has('admin.agencies.deactivate'))
                                                        <form action="{{ route('admin.agencies.deactivate', $agency->id_agence) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                                                    title="Deactivate"
                                                                    onclick="return confirm('Are you sure you want to deactivate this agency?')">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isSuperAdmin ? '9' : '7' }}" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Agencies Found</h3>
                                                <p class="text-gray-500 mb-4">
                                                    @if($isSuperAdmin)
                                                        No agencies have been created yet.
                                                        @if(isset($stats['agencies_without_company']) && $stats['agencies_without_company'] > 0)
                                                            <br><span class="text-sm">{{ $stats['agencies_without_company'] }} agencies without company assignment.</span>
                                                        @endif
                                                    @elseif($isCompanyAdmin)
                                                        Your company hasn't created any agencies yet.
                                                    @endif
                                                </p>

                                                @if($isSuperAdmin && Route::has('admin.agencies.create'))
                                                    <a href="{{ route('admin.agencies.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                        Create First Agency
                                                    </a>
                                                @elseif($isCompanyAdmin && !($hasAgency ?? false) && Route::has('my-agency.create'))
                                                    <a href="{{ route('my-agency.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                        Create Agency
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($agencies) && method_exists($agencies, 'hasPages') && $agencies->hasPages())
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            {{ $agencies->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

            @elseif($isAgencyAdmin && isset($agency))
                {{-- Agency Admin View - Show single agency details --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Agency Information -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Agency Information
                                </h4>

                                <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Agency Name</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $agency->name }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Agency ID</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $agency->id_agence }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $agency->email }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $agency->phone }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $agency->address ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">City</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                {{ $agency->coordinates_data->city->name ?? $agency->city ?? 'N/A' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Company</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $agency->company_data->name ?? 'N/A' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                                            <dd class="mt-1">
                                                @php
                                                    $status = $agency->status ?? 'active';
                                                    $statusColors = [
                                                        'active' => 'bg-green-100 text-green-800',
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'suspended' => 'bg-red-100 text-red-800',
                                                        'inactive' => 'bg-gray-100 text-gray-800'
                                                    ];
                                                    $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </dd>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex space-x-3">
                                        @if(Route::has('my-agency.edit'))
                                            <a href="{{ route('my-agency.edit') }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit Agency
                                            </a>
                                        @endif

                                        @if(Route::has('my-agency.activities'))
                                            <a href="{{ route('my-agency.activities') }}"
                                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                View Activities
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Quick Actions
                                </h4>

                                <div class="grid grid-cols-2 gap-4">
                                    @if(Route::has('my-agency.buses'))
                                        <a href="{{ route('my-agency.buses') }}"
                                           class="block p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-150 border border-blue-200">
                                            <div class="flex flex-col items-center text-center">
                                                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <h5 class="font-medium text-gray-900">Buses</h5>
                                                <p class="text-xs text-gray-600 mt-1">Manage your fleet</p>
                                                <span class="mt-2 inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                                                    {{ $agency->buses_count ?? 0 }} buses
                                                </span>
                                            </div>
                                        </a>
                                    @endif

                                    @if(Route::has('my-agency.trips'))
                                        <a href="{{ route('my-agency.trips') }}"
                                           class="block p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-150 border border-green-200">
                                            <div class="flex flex-col items-center text-center">
                                                <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                </svg>
                                                <h5 class="font-medium text-gray-900">Trips</h5>
                                                <p class="text-xs text-gray-600 mt-1">Manage trips</p>
                                                <span class="mt-2 inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded-full">
                                                    {{ $agency->trips_count ?? 0 }} trips
                                                </span>
                                            </div>
                                        </a>
                                    @endif

                                    @if(Route::has('my-agency.reports'))
                                        <a href="{{ route('my-agency.reports') }}"
                                           class="block p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all duration-150 border border-purple-200">
                                            <div class="flex flex-col items-center text-center">
                                                <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                                </svg>
                                                <h5 class="font-medium text-gray-900">Reports</h5>
                                                <p class="text-xs text-gray-600 mt-1">View analytics</p>
                                            </div>
                                        </a>
                                    @endif

                                    @if(Route::has('tickets.sell'))
                                        <a href="{{ route('tickets.sell') }}"
                                           class="block p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg hover:from-yellow-100 hover:to-yellow-200 transition-all duration-150 border border-yellow-200">
                                            <div class="flex flex-col items-center text-center">
                                                <svg class="w-8 h-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                                </svg>
                                                <h5 class="font-medium text-gray-900">Sell Tickets</h5>
                                                <p class="text-xs text-gray-600 mt-1">Issue tickets</p>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Fallback for unauthorized access --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Access Denied</h3>
                        <p class="text-gray-500 mb-4">You don't have permission to view this page.</p>
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
