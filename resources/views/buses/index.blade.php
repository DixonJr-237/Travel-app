<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    @if($isSuperAdmin)
                        Fleet Management
                    @elseif($isCompanyAdmin)
                        @php
                            $companyName = 'Company';
                            if (isset($companies)) {
                                if ($companies instanceof \Illuminate\Support\Collection) {
                                    $firstCompany = $companies->first();
                                    $companyName = $firstCompany?->name ?? 'Company';
                                } elseif (is_array($companies) && !empty($companies)) {
                                    $firstCompany = reset($companies);
                                    $companyName = is_object($firstCompany) && property_exists($firstCompany, 'name')
                                        ? $firstCompany->name
                                        : (is_array($firstCompany) && isset($firstCompany['name']) ? $firstCompany['name'] : 'Company');
                                }
                            }
                        @endphp
                        {{ $companyName }} Fleet Management
                    @elseif($isAgencyAdmin)
                        @php
                            $agencyName = 'Agency';
                            $userAgencyId = auth()->user()->agency_id ?? null;

                            if (isset($agencies) && $userAgencyId) {
                                if ($agencies instanceof \Illuminate\Support\Collection) {
                                    $userAgency = $agencies->firstWhere('id_agence', $userAgencyId);
                                    $agencyName = $userAgency?->name ?? 'Agency';
                                } elseif (is_array($agencies)) {
                                    foreach ($agencies as $agency) {
                                        $agencyId = is_object($agency)
                                            ? ($agency->id_agence ?? null)
                                            : ($agency['id_agence'] ?? null);

                                        if ($agencyId == $userAgencyId) {
                                            $agencyName = is_object($agency)
                                                ? ($agency->name ?? 'Agency')
                                                : ($agency['name'] ?? 'Agency');
                                            break;
                                        }
                                    }
                                }
                            }
                        @endphp
                        {{ $agencyName }} Fleet Management
                    @else
                        Buses Management
                    @endif
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    @if($isSuperAdmin)
                        Manage all fleet vehicles across all companies and agencies
                    @elseif($isCompanyAdmin)
                        @php
                            $agencyCount = 0;
                            if (isset($accessibleAgencyIds)) {
                                $agencyCount = is_countable($accessibleAgencyIds) ? count($accessibleAgencyIds) : 0;
                            }
                        @endphp
                        Manage your company's fleet vehicles across {{ $agencyCount }} {{ Str::plural('agency', $agencyCount) }}
                    @elseif($isAgencyAdmin)
                        Manage your agency's fleet vehicles
                    @endif
                </p>
            </div>

            @php
                $user = auth()->user();
                $createRoute = match(true) {
                    $isSuperAdmin => 'admin.buses.create',
                    $isCompanyAdmin => 'my-company.buses.create',
                    $isAgencyAdmin => 'my-agency.buses.create',
                    default => null
                };

                // Check if user can create buses
                $canCreate = match(true) {
                    $isSuperAdmin => true,
                    $isCompanyAdmin => !empty($accessibleAgencyIds),
                    $isAgencyAdmin => !empty($accessibleAgencyIds),
                    default => false
                };
            @endphp

            @if($createRoute && Route::has($createRoute) && $canCreate)
                <a href="{{ route($createRoute) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Bus
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Statistics Dashboard -->
            @if(isset($stats) && is_array($stats))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-lg p-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4-4m-4 4l4 4"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Total Buses</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $stats['total_buses'] ?? 0 }}</p>
                                @if($isCompanyAdmin || $isAgencyAdmin)
                                    <p class="text-xs text-gray-500">in your fleet</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-lg p-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Active</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $stats['active_buses'] ?? 0 }}</p>
                                @if(($stats['total_buses'] ?? 0) > 0)
                                    @php
                                        $activePercentage = round((($stats['active_buses'] ?? 0) / ($stats['total_buses'] ?? 1)) * 100);
                                    @endphp
                                    <p class="text-xs text-gray-500">{{ $activePercentage }}% of fleet</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Maintenance</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $stats['maintenance_buses'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-lg p-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Upcoming Trips</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $stats['upcoming_trips'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-lg p-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-500">Utilization</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $stats['utilization_rate'] ?? 0 }}%</p>
                                <p class="text-xs text-gray-500">fleet active rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Filters Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route(
                        match(true) {
                            $isSuperAdmin => 'admin.buses.index',
                            $isCompanyAdmin => 'my-company.buses.index',
                            $isAgencyAdmin => 'my-agency.buses.index',
                            default => 'admin.buses.index'
                        }
                    ) }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Company Filter - Only for Super Admin -->
                            @if($isSuperAdmin)
                                <div>
                                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Company
                                    </label>
                                    <select name="company_id" id="company_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Companies</option>
                                        <option value="none" {{ request('company_id') == 'none' ? 'selected' : '' }}>Unassigned</option>
                                        @if(isset($companies) && (($companies instanceof \Illuminate\Support\Collection && $companies->isNotEmpty()) || (is_array($companies) && !empty($companies))))
                                            @foreach($companies as $company)
                                                @php
                                                    $companyId = is_object($company) ? ($company->id_company ?? null) : ($company['id_company'] ?? null);
                                                    $companyName = is_object($company) ? ($company->name ?? 'Unknown') : ($company['name'] ?? 'Unknown');
                                                @endphp
                                                @if($companyId)
                                                    <option value="{{ $companyId }}"
                                                        {{ request('company_id') == $companyId ? 'selected' : '' }}>
                                                        {{ $companyName }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif

                            <!-- Agency Filter - Show for all but with role-based options -->
                            <div>
                                <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Agency
                                </label>
                                <select name="agency_id" id="agency_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Agencies</option>
                                    @if(isset($agencies) && (($agencies instanceof \Illuminate\Support\Collection && $agencies->isNotEmpty()) || (is_array($agencies) && !empty($agencies))))
                                        @foreach($agencies as $agency)
                                            @php
                                                $agencyId = is_object($agency) ? ($agency->id_agence ?? null) : ($agency['id_agence'] ?? null);
                                                $agencyName = is_object($agency) ? ($agency->name ?? 'Unknown') : ($agency['name'] ?? 'Unknown');
                                                $agencyCompany = is_object($agency) ? ($agency->company ?? null) : ($agency['company'] ?? null);
                                                $userAgencyId = auth()->user()->agency_id ?? null;
                                            @endphp
                                            @if($agencyId)
                                                <option value="{{ $agencyId }}"
                                                    {{ request('agency_id') == $agencyId ? 'selected' : '' }}
                                                    {{ ($isAgencyAdmin && $agencyId != $userAgencyId) ? 'disabled' : '' }}>
                                                    {{ $agencyName }}
                                                    @if($isSuperAdmin && $agencyCompany)
                                                        @php
                                                            $companyName = is_object($agencyCompany) ? ($agencyCompany->name ?? null) : ($agencyCompany['name'] ?? null);
                                                        @endphp
                                                        @if($companyName)
                                                            ({{ $companyName }})
                                                        @endif
                                                    @endif
                                                    @if($isAgencyAdmin && $agencyId == $userAgencyId)
                                                        (Your Agency)
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Status
                                </label>
                                <select name="status" id="status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    @if(isset($statusOptions) && is_array($statusOptions))
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Capacity Filter -->
                            <div>
                                <label for="min_capacity" class="block text-sm font-medium text-gray-700 mb-1">
                                    Min. Seats
                                </label>
                                <input type="number" name="min_capacity" id="min_capacity"
                                       value="{{ request('min_capacity') }}"
                                       min="1"
                                       placeholder="e.g. 30"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                    Search
                                </label>
                                <input type="text" name="search" id="search"
                                       value="{{ request('search') }}"
                                       placeholder="Search by registration, model, agency..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Active Filters Display -->
                        @if(request()->anyFilled(['search', 'agency_id', 'company_id', 'status', 'min_capacity']))
                            <div class="flex flex-wrap items-center gap-2 pt-2">
                                <span class="text-sm text-gray-600">Active filters:</span>
                                <button type="button" onclick="clearAllFilters()"
                                        class="text-xs text-red-600 hover:text-red-800 underline">
                                    Clear all
                                </button>

                                @if(request('search'))
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-sm">
                                        Search: "{{ request('search') }}"
                                        <button type="button" onclick="removeFilter('search')" class="ml-1 hover:text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </span>
                                @endif

                                @if(request('company_id') && $isSuperAdmin)
                                    @php
                                        $companyName = 'Unassigned';
                                        if (request('company_id') !== 'none' && isset($companies)) {
                                            if ($companies instanceof \Illuminate\Support\Collection) {
                                                $company = $companies->firstWhere('id_company', request('company_id'));
                                                $companyName = $company->name ?? 'Unknown';
                                            } elseif (is_array($companies)) {
                                                foreach ($companies as $company) {
                                                    $cid = is_object($company) ? ($company->id_company ?? null) : ($company['id_company'] ?? null);
                                                    if ($cid == request('company_id')) {
                                                        $companyName = is_object($company) ? ($company->name ?? 'Unknown') : ($company['name'] ?? 'Unknown');
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-sm">
                                        Company: {{ $companyName }}
                                        <button type="button" onclick="removeFilter('company_id')" class="ml-1 hover:text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </span>
                                @endif

                                @if(request('agency_id'))
                                    @php
                                        $agencyName = 'Unknown';
                                        if (isset($agencies)) {
                                            if ($agencies instanceof \Illuminate\Support\Collection) {
                                                $agency = $agencies->firstWhere('id_agence', request('agency_id'));
                                                $agencyName = $agency->name ?? 'Unknown';
                                            } elseif (is_array($agencies)) {
                                                foreach ($agencies as $agency) {
                                                    $aid = is_object($agency) ? ($agency->id_agence ?? null) : ($agency['id_agence'] ?? null);
                                                    if ($aid == request('agency_id')) {
                                                        $agencyName = is_object($agency) ? ($agency->name ?? 'Unknown') : ($agency['name'] ?? 'Unknown');
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-sm">
                                        Agency: {{ $agencyName }}
                                        <button type="button" onclick="removeFilter('agency_id')" class="ml-1 hover:text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </span>
                                @endif

                                @if(request('status') && isset($statusOptions[request('status')]))
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-sm">
                                        Status: {{ $statusOptions[request('status')] }}
                                        <button type="button" onclick="removeFilter('status')" class="ml-1 hover:text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </span>
                                @endif

                                @if(request('min_capacity'))
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-sm">
                                        Min Seats: {{ request('min_capacity') }}
                                        <button type="button" onclick="removeFilter('min_capacity')" class="ml-1 hover:text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route(
                                match(true) {
                                    $isSuperAdmin => 'admin.buses.index',
                                    $isCompanyAdmin => 'my-company.buses.index',
                                    $isAgencyAdmin => 'my-agency.buses.index',
                                    default => 'admin.buses.index'
                                }
                            ) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear Filters
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Buses Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agency</th>
                                @if($isSuperAdmin)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($buses as $bus)
                                @php
                                    $isMyAgency = $isAgencyAdmin && isset($bus->belongs_to_my_agency) && $bus->belongs_to_my_agency;
                                    $isMyCompany = $isCompanyAdmin && isset($bus->belongs_to_my_company) && $bus->belongs_to_my_company;
                                    $rowHighlight = $isMyAgency || $isMyCompany;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $rowHighlight ? 'bg-blue-50/50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4-4m-4 4l4 4"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $bus->registration_number ?? 'N/A' }}
                                                    @if($rowHighlight)
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $isMyAgency ? 'Your Agency' : 'Your Company' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $bus->make ?? '' }} {{ $bus->model ?? '' }}
                                                    @if($bus->year)
                                                        ({{ $bus->year }})
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $bus->agency->name ?? 'N/A' }}</div>
                                        @if(isset($bus->agency) && isset($bus->agency->agency_admin_name))
                                            <div class="text-xs text-gray-500">
                                                Admin: {{ $bus->agency->agency_admin_name }}
                                            </div>
                                        @endif
                                    </td>
                                    @if($isSuperAdmin)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $bus->agency->company->name ?? 'N/A' }}</div>
                                        @if(isset($bus->agency) && isset($bus->agency->company) && isset($bus->agency->company->company_admin_name))
                                            <div class="text-xs text-gray-500">
                                                Admin: {{ $bus->agency->company->company_admin_name }}
                                            </div>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $bus->capacity ?? 0 }}</div>
                                        @php
                                            $capacity = $bus->capacity ?? 0;
                                            $sizeClass = match(true) {
                                                $capacity >= 40 => 'bg-green-100 text-green-800',
                                                $capacity >= 20 => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                            $sizeLabel = match(true) {
                                                $capacity >= 40 => 'Large',
                                                $capacity >= 20 => 'Medium',
                                                default => 'Small'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $sizeClass }}">
                                            {{ $sizeLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800 border-green-200',
                                                'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'inactive' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                'out_of_service' => 'bg-red-100 text-red-800 border-red-200',
                                                'default' => 'bg-gray-100 text-gray-800 border-gray-200'
                                            ];
                                            $status = $bus->status ?? 'unknown';
                                            $statusColor = $statusColors[$status] ?? $statusColors['default'];
                                            $statusLabel = $statusOptions[$status] ?? ucfirst(str_replace('_', ' ', $status));
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }} border">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-500">Trips:</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $bus->trips_count ?? 0 }}</span>
                                            </div>
                                            @if(isset($bus->upcoming_trips_count))
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-500">Upcoming:</span>
                                                <span class="text-sm {{ $bus->upcoming_trips_count > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                                    {{ $bus->upcoming_trips_count }}
                                                </span>
                                            </div>
                                            @endif
                                            @if(isset($bus->utilization_rate))
                                            <div class="mt-2">
                                                <div class="flex items-center justify-between text-xs mb-1">
                                                    <span class="text-gray-500">Utilization</span>
                                                    <span class="{{ $bus->utilization_rate > 70 ? 'text-green-600' : ($bus->utilization_rate > 30 ? 'text-yellow-600' : 'text-gray-500') }}">
                                                        {{ $bus->utilization_rate }}%
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full {{ $bus->utilization_rate > 70 ? 'bg-green-500' : ($bus->utilization_rate > 30 ? 'bg-yellow-500' : 'bg-gray-400') }}"
                                                         style="width: {{ min($bus->utilization_rate, 100) }}%"></div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @php
                                            $showRoute = match(true) {
                                                $isSuperAdmin => 'admin.buses.show',
                                                $isCompanyAdmin => 'my-company.buses.show',
                                                $isAgencyAdmin => 'my-agency.buses.show',
                                                default => null
                                            };
                                            $editRoute = match(true) {
                                                $isSuperAdmin => 'admin.buses.edit',
                                                $isCompanyAdmin => 'my-company.buses.edit',
                                                $isAgencyAdmin => 'my-agency.buses.edit',
                                                default => null
                                            };
                                            $destroyRoute = match(true) {
                                                $isSuperAdmin => 'admin.buses.destroy',
                                                $isCompanyAdmin => 'my-company.buses.destroy',
                                                $isAgencyAdmin => 'my-agency.buses.destroy',
                                                default => null
                                            };

                                            $canEdit = match(true) {
                                                $isSuperAdmin => true,
                                                $isCompanyAdmin => $isMyCompany,
                                                $isAgencyAdmin => $isMyAgency,
                                                default => false
                                            };

                                            $canDelete = $canEdit && ($bus->trips_count ?? 0) === 0;
                                            $busId = $bus->bus_id ?? $bus->id ?? null;
                                        @endphp

                                        @if($busId)
                                        <div class="flex items-center space-x-3">
                                            @if($showRoute && Route::has($showRoute))
                                                <a href="{{ route($showRoute, $busId) }}"
                                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                                                   title="View Details">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            @if($editRoute && Route::has($editRoute) && $canEdit)
                                                <a href="{{ route($editRoute, $busId) }}"
                                                   class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                                   title="Edit Bus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                            @endif

                                            @if($destroyRoute && Route::has($destroyRoute) && $canDelete)
                                                <form action="{{ route($destroyRoute, $busId) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('⚠️ Are you sure you want to delete this bus? This action cannot be undone and will affect all associated data.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                                            title="Delete Bus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @elseif(($bus->trips_count ?? 0) > 0 && $canEdit)
                                                <span class="text-gray-400 cursor-not-allowed"
                                                      title="Cannot delete bus with existing trips">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isSuperAdmin ? 7 : 6 }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4-4m-4 4l4 4"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Buses Found</h3>
                                            <p class="text-gray-500 mb-4 max-w-md text-center">
                                                @if(request()->anyFilled(['search', 'agency_id', 'company_id', 'status', 'min_capacity']))
                                                    No buses match your current filters.
                                                    <button onclick="clearAllFilters()" class="text-blue-600 hover:text-blue-800 underline">
                                                        Clear all filters
                                                    </button>
                                                    to see all buses.
                                                @else
                                                    @if($isAgencyAdmin)
                                                        Your agency doesn't have any buses yet.
                                                    @elseif($isCompanyAdmin)
                                                        Your company doesn't have any buses yet.
                                                    @else
                                                        No buses have been added to the system yet.
                                                    @endif
                                                @endif
                                            </p>

                                            @if($createRoute && Route::has($createRoute) && $canCreate && !request()->anyFilled(['search', 'agency_id', 'company_id', 'status', 'min_capacity']))
                                                <a href="{{ route($createRoute) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150 shadow-sm">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    Add Your First Bus
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($buses) && $buses instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $buses->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $buses->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            @if(isset($buses) && method_exists($buses, 'isNotEmpty') && $buses->isNotEmpty())
                <div class="mt-6 flex justify-end space-x-3">
                    @if($isSuperAdmin)
                        <button onclick="exportData()"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Report
                        </button>
                    @endif

                    @if($isCompanyAdmin || $isAgencyAdmin)
                        <a href="{{ route($isCompanyAdmin ? 'my-company.dashboard' : 'my-agency.dashboard') }}"
                           class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                // Auto-submit filters when select boxes change
                const autoSubmitSelects = document.querySelectorAll('#company_id, #agency_id, #status');

                autoSubmitSelects.forEach(select => {
                    select.addEventListener('change', function() {
                        if (this.value !== '') {
                            this.form.submit();
                        }
                    });
                });

                // Debounced search
                let searchTimeout;
                const searchInput = document.getElementById('search');

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            if (this.value.length >= 2 || this.value.length === 0) {
                                this.form.submit();
                            }
                        }, 500);
                    });
                }

                // Capacity validation
                const minCapacity = document.getElementById('min_capacity');

                if (minCapacity) {
                    minCapacity.addEventListener('change', function() {
                        if (this.value && parseInt(this.value) < 1) {
                            this.value = 1;
                        }
                    });
                }

                // Filter removal functions
                window.removeFilter = function(filterName) {
                    const url = new URL(window.location.href);
                    url.searchParams.delete(filterName);
                    window.location.href = url.toString();
                };

                window.clearAllFilters = function() {
                    const baseUrl = window.location.href.split('?')[0];
                    window.location.href = baseUrl;
                };

                // Export functionality
                window.exportData = function() {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('export', 'csv');
                    window.location.href = currentUrl.toString();
                };

                // Keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    // Ctrl/Cmd + K to focus search
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        searchInput?.focus();
                    }

                    // Esc to clear search
                    if (e.key === 'Escape' && document.activeElement === searchInput) {
                        searchInput.value = '';
                        searchInput.form.submit();
                    }
                });
            })();
        </script>
    @endpush

    @push('styles')
        <style>
            .stats-card {
                transition: all 0.2s ease-in-out;
            }
            .stats-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            tbody tr {
                transition: background-color 0.15s ease-in-out;
            }
            .overflow-x-auto {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f1f5f9;
            }
            .overflow-x-auto::-webkit-scrollbar {
                height: 8px;
            }
            .overflow-x-auto::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }
            .overflow-x-auto::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 4px;
            }
            .overflow-x-auto::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    @endpush
</x-app-layout>
