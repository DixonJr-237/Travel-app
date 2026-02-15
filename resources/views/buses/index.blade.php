<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buses Management</h2>
                <p class="text-sm text-gray-600 mt-1">Manage your fleet vehicles and track their status</p>
            </div>

            @php
                $user = auth()->user();
                $createRoute = match($user->role) {
                    'super_admin' => 'admin.buses.create',
                    'company_admin' => 'my-company.buses.create',
                    'agency_admin' => 'my-agency.buses.create',
                    default => null
                };
            @endphp

            @if($createRoute && Route::has($createRoute))
                <a href="{{ route($createRoute) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Add New Bus
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filters Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route(
                        match($user->role) {
                            'super_admin' => 'admin.buses.index',
                            'company_admin' => 'my-company.buses.index',
                            'agency_admin' => 'my-agency.buses.index',
                            default => 'admin.buses.index'
                        }
                    ) }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Agency Filter (for super admin and company admin) -->
                            @if(in_array($user->role, ['super_admin', 'company_admin']))
                                <div>
                                    <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Filter by Agency
                                    </label>
                                    <select name="agency_id" id="agency_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Agencies</option>
                                        @forelse($agencies ?? [] as $agency)
                                            <option value="{{ $agency->id_agence }}"
                                                {{ request('agency_id') == $agency->id_agence ? 'selected' : '' }}>
                                                {{ $agency->name }}
                                                @if($user->role === 'super_admin' && $agency->company)
                                                    ({{ $agency->company->name }})
                                                @endif
                                            </option>
                                        @empty
                                            <option value="" disabled>No agencies available</option>
                                        @endforelse
                                    </select>
                                </div>
                            @endif

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Filter by Status
                                </label>
                                <select name="status" id="status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>In Maintenance</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                    Search Buses
                                </label>
                                <input type="text" name="search" id="search"
                                       value="{{ request('search') }}"
                                       placeholder="Search by registration number, model, or year..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route(
                                match($user->role) {
                                    'super_admin' => 'admin.buses.index',
                                    'company_admin' => 'my-company.buses.index',
                                    'agency_admin' => 'my-agency.buses.index',
                                    default => 'admin.buses.index'
                                }
                            ) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-times mr-2"></i> Clear Filters
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i> Apply Filters
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Trips</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($buses ?? [] as $bus)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-bus text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $bus->registration_number ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    @if($bus->model || $bus->year)
                                                        {{ $bus->model ?? 'Unknown model' }}
                                                        ({{ $bus->year ?? 'Year N/A' }})
                                                    @else
                                                        <span class="text-gray-400">No additional details</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $bus->agency->name ?? 'N/A' }}</div>
                                        @if($user->role === 'super_admin' && $bus->agency->company)
                                            <div class="text-xs text-gray-500">{{ $bus->agency->company->name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $bus->seats_count ?? 0 }} seats</div>
                                        @if(($bus->available_seats ?? 0) > 0)
                                            <div class="text-xs text-green-600">{{ $bus->available_seats }} available</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'maintenance' => 'bg-yellow-100 text-yellow-800',
                                                'inactive' => 'bg-gray-100 text-gray-800',
                                                'default' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $statusColor = $statusColors[$bus->status] ?? $statusColors['default'];
                                        @endphp
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst($bus->status ?? 'unknown') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="font-medium">{{ $bus->trips_count ?? 0 }}</span> trips
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @php
                                            $showRoute = match($user->role) {
                                                'super_admin' => 'admin.buses.show',
                                                'company_admin' => 'my-company.buses.show',
                                                'agency_admin' => 'my-agency.buses.show',
                                                default => null
                                            };
                                            $editRoute = match($user->role) {
                                                'super_admin' => 'admin.buses.edit',
                                                'company_admin' => 'my-company.buses.edit',
                                                'agency_admin' => 'my-agency.buses.edit',
                                                default => null
                                            };
                                            $destroyRoute = match($user->role) {
                                                'super_admin' => 'admin.buses.destroy',
                                                'company_admin' => 'my-company.buses.destroy',
                                                'agency_admin' => 'my-agency.buses.destroy',
                                                default => null
                                            };
                                        @endphp

                                        <div class="flex items-center space-x-3">
                                            @if($showRoute && Route::has($showRoute))
                                                <a href="{{ route($showRoute, $bus->bus_id ?? $bus->id) }}"
                                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if($editRoute && Route::has($editRoute))
                                                <a href="{{ route($editRoute, $bus->bus_id ?? $bus->id) }}"
                                                   class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                                   title="Edit Bus">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            @if($destroyRoute && Route::has($destroyRoute) && ($bus->trips_count ?? 0) === 0)
                                                <form action="{{ route($destroyRoute, $bus->bus_id ?? $bus->id) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('⚠️ Are you sure you want to delete this bus? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                                            title="Delete Bus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @elseif(($bus->trips_count ?? 0) > 0)
                                                <span class="text-gray-400 cursor-not-allowed"
                                                      title="Cannot delete bus with existing trips">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-bus text-4xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Buses Found</h3>
                                            <p class="text-gray-500 mb-4 max-w-md">
                                                @if(request()->anyFilled(['search', 'agency_id', 'status']))
                                                    No buses match your current filters. Try adjusting your search criteria.
                                                @else
                                                    Get started by adding your first bus to the fleet.
                                                @endif
                                            </p>

                                            @if($createRoute && Route::has($createRoute) && !request()->anyFilled(['search', 'agency_id', 'status']))
                                                <a href="{{ route($createRoute) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                                                    <i class="fas fa-plus mr-2"></i> Add Your First Bus
                                                </a>
                                            @elseif(request()->anyFilled(['search', 'agency_id', 'status']))
                                                <a href="{{ route(
                                                    match($user->role) {
                                                        'super_admin' => 'admin.buses.index',
                                                        'company_admin' => 'my-company.buses.index',
                                                        'agency_admin' => 'my-agency.buses.index',
                                                        default => 'admin.buses.index'
                                                    }
                                                ) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                                                    <i class="fas fa-times mr-2"></i> Clear All Filters
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
                @if(isset($buses) && $buses instanceof \Illuminate\Pagination\LengthAwarePaginator && $buses->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $buses->firstItem() ?? 0 }}</span>
                                to <span class="font-medium">{{ $buses->lastItem() ?? 0 }}</span>
                                of <span class="font-medium">{{ $buses->total() }}</span> results
                            </div>
                            <div class="flex-1 flex justify-end">
                                {{ $buses->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Stats Summary (Optional) -->
            @if(isset($buses) && $buses->total() > 0)
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-2">
                                    <i class="fas fa-bus text-white"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Total Buses</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $buses->total() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-2">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">Active Buses</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $buses->where('status', 'active')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-2">
                                    <i class="fas fa-tools text-white"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-500">In Maintenance</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $buses->where('status', 'maintenance')->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                // Auto-submit filters when select boxes change (with debounce)
                const filterInputs = document.querySelectorAll('#agency_id, #status');

                filterInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        this.form.submit();
                    });
                });

                // Debounced search input to prevent too many requests
                const searchInput = document.getElementById('search');
                if (searchInput) {
                    let timeout = null;
                    searchInput.addEventListener('keyup', function(e) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => {
                            if (this.value.length >= 2 || this.value.length === 0) {
                                this.form.submit();
                            }
                        }, 500);
                    });
                }

                // Add tooltips for better UX
                const deleteButtons = document.querySelectorAll('button[title="Delete Bus"], span[title="Cannot delete bus with existing trips"]');
                deleteButtons.forEach(button => {
                    button.addEventListener('mouseenter', function(e) {
                        const title = this.getAttribute('title');
                        if (title) {
                            // You can implement a custom tooltip here if needed
                        }
                    });
                });

                // Confirm delete for forms that don't have the confirm dialog
                document.querySelectorAll('form[onsubmit]').forEach(form => {
                    const originalSubmit = form.onsubmit;
                    form.onsubmit = function(e) {
                        if (typeof originalSubmit === 'function') {
                            return originalSubmit.call(this, e);
                        }
                        return confirm('Are you sure you want to perform this action?');
                    };
                });
            })();
        </script>
    @endpush

    <style>
        /* Optional: Add smooth transitions */
        .transition-colors {
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
        }

        /* Improve table row hover effect */
        tbody tr:hover td {
            background-color: rgba(59, 130, 246, 0.05);
        }

        /* Better focus styles for accessibility */
        a:focus-visible, button:focus-visible {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
    </style>
</x-app-layout>
