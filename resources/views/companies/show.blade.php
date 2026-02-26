<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $company->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Company Header Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-2xl text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-gray-900">{{ $company->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $company->email }}</p>
                            </div>
                        </div>

                        {{-- Edit button with dynamic route based on user role --}}
                        @can('update', $company)
                            <a href="{{ route( Auth::user()->isSuperAdmin() ? 'admin.companies.edit' : 'companies.edit', $company) }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Agencies Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <i class="fas fa-store text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Agencies</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_agencies'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buses Card -->
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

                <!-- Active Buses Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <i class="fas fa-route text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Active Buses</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_buses'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Trips Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <i class="fas fa-ticket-alt text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Trips</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_trips'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Company Details and Admin Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Details -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Company Details</h4>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $company->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Admin Information -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Company Admin</h4>
                        @if($company->user)
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 h-12 w-12 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $company->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $company->user->email }}</p>
                                </div>
                            </div>
                            <div class="border-t pt-4">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Last Login:</span>
                                    {{ $company->user->last_login_at ? $company->user->last_login_at->diffForHumans() : 'Never' }}
                                </p>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No admin assigned</p>
                            @can('assignAdmin', $company)
                                <button class="mt-4 inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">
                                    <i class="fas fa-user-plus mr-2"></i> Assign Admin
                                </button>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions for Super Admin --}}
            @can('manageAllCompanies', App\Models\Company::class)
                <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h4>
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.agencies.index', ['company_id' => $company->id_company]) }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700">
                                <i class="fas fa-store mr-2"></i> Manage Agencies
                            </a>

                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
