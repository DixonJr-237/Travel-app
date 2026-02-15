<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add New Bus</h2>
                <p class="text-sm text-gray-600 mt-1">Register a new vehicle to your fleet</p>
            </div>

            @php
                $user = auth()->user();
                $indexRoute = match($user->role) {
                    'super_admin' => 'admin.buses.index',
                    'company_admin' => 'my-company.buses.index',
                    'agency_admin' => 'my-agency.buses.index',
                    default => 'admin.buses.index'
                };
                $storeRoute = match($user->role) {
                    'super_admin' => 'admin.buses.store',
                    'company_admin' => 'my-company.buses.store',
                    'agency_admin' => 'my-agency.buses.store',
                    default => 'admin.buses.store'
                };
            @endphp

            @if(Route::has($indexRoute))
                <a href="{{ route($indexRoute) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

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

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Form Header with Instructions -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Bus Registration Information
                        </h4>
                        <p class="text-xs text-blue-600 mt-1">Fields marked with <span class="text-red-500">*</span> are required</p>
                    </div>

                    <form method="POST" action="{{ Route::has($storeRoute) ? route($storeRoute) : '#' }}" class="space-y-6">
                        @csrf

                        <!-- Basic Information Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                <i class="fas fa-truck text-blue-600 mr-2"></i>
                                Basic Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Registration Number -->
                                <div>
                                    <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Registration Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="registration_number"
                                           id="registration_number"
                                           value="{{ old('registration_number') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('registration_number') border-red-500 @enderror"
                                           placeholder="e.g., CE1234AB">
                                    <p class="mt-1 text-xs text-gray-500">Unique vehicle registration/license plate number</p>
                                    @error('registration_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Bus Number (Optional) -->
                                <div>
                                    <label for="bus_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Bus Number <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <input type="text"
                                           name="bus_number"
                                           id="bus_number"
                                           value="{{ old('bus_number') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('bus_number') border-red-500 @enderror"
                                           placeholder="e.g., BUS-001">
                                    <p class="mt-1 text-xs text-gray-500">Internal fleet identifier</p>
                                    @error('bus_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Agency Selection -->
                                <div>
                                    <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Agency <span class="text-red-500">*</span>
                                    </label>
                                    <select name="agency_id"
                                            id="agency_id"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('agency_id') border-red-500 @enderror">
                                        <option value="">Select Agency</option>
                                        @forelse($agencies ?? [] as $agency)
                                            <option value="{{ $agency->id_agence }}"
                                                {{ old('agency_id') == $agency->id_agence ? 'selected' : '' }}>
                                                {{ $agency->name }}
                                                @if($user->role === 'super_admin' && $agency->company)
                                                    ({{ $agency->company->name }})
                                                @endif
                                            </option>
                                        @empty
                                            <option value="" disabled>No agencies available</option>
                                        @endforelse
                                    </select>
                                    @error('agency_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Model -->
                                <div>
                                    <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                        Model <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="model"
                                           id="model"
                                           value="{{ old('model') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('model') border-red-500 @enderror"
                                           placeholder="e.g., Mercedes-Benz Sprinter">
                                    @error('model')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Year -->
                                <div>
                                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                        Year <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="year"
                                           id="year"
                                           value="{{ old('year') }}"
                                           required
                                           min="1990"
                                           max="{{ date('Y') + 1 }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('year') border-red-500 @enderror"
                                           placeholder="e.g., 2020">
                                    @error('year')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Manufacturer (Optional) -->
                                <div>
                                    <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-2">
                                        Manufacturer <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <input type="text"
                                           name="manufacturer"
                                           id="manufacturer"
                                           value="{{ old('manufacturer') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., Mercedes-Benz">
                                    @error('manufacturer')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Capacity & Configuration Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                <i class="fas fa-chair text-green-600 mr-2"></i>
                                Capacity & Configuration
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Total Seats -->
                                <div>
                                    <label for="total_seats" class="block text-sm font-medium text-gray-700 mb-2">
                                        Total Seats <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="total_seats"
                                           id="total_seats"
                                           value="{{ old('total_seats') }}"
                                           required
                                           min="1"
                                           max="100"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('total_seats') border-red-500 @enderror"
                                           placeholder="e.g., 50">
                                    @error('total_seats')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Available Seats -->
                                <div>
                                    <label for="available_seats" class="block text-sm font-medium text-gray-700 mb-2">
                                        Available Seats <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="available_seats"
                                           id="available_seats"
                                           value="{{ old('available_seats') }}"
                                           required
                                           min="0"
                                           max="100"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('available_seats') border-red-500 @enderror"
                                           placeholder="e.g., 50">
                                    <p class="mt-1 text-xs text-gray-500">Should not exceed total seats</p>
                                    @error('available_seats')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Seat Configuration -->
                                <div>
                                    <label for="seat_configuration" class="block text-sm font-medium text-gray-700 mb-2">
                                        Seat Configuration <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <input type="text"
                                           name="seat_configuration"
                                           id="seat_configuration"
                                           value="{{ old('seat_configuration') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="e.g., 2x2, 3x2">
                                    <p class="mt-1 text-xs text-gray-500">e.g., 2x2, 3x2, or custom layout</p>
                                    @error('seat_configuration')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status & Additional Info Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                <i class="fas fa-cog text-purple-600 mr-2"></i>
                                Status & Additional Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status"
                                            id="status"
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>In Maintenance</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Fuel Type -->
                                <div>
                                    <label for="fuel_type" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fuel Type <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <select name="fuel_type"
                                            id="fuel_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Fuel Type</option>
                                        <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                                        <option value="petrol" {{ old('fuel_type') == 'petrol' ? 'selected' : '' }}>Petrol</option>
                                        <option value="electric" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>Electric</option>
                                        <option value="hybrid" {{ old('fuel_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                        <option value="cng" {{ old('fuel_type') == 'cng' ? 'selected' : '' }}>CNG</option>
                                    </select>
                                </div>

                                <!-- Last Maintenance Date -->
                                <div>
                                    <label for="last_maintenance" class="block text-sm font-medium text-gray-700 mb-2">
                                        Last Maintenance Date <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <input type="date"
                                           name="last_maintenance"
                                           id="last_maintenance"
                                           value="{{ old('last_maintenance') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Next Maintenance Date -->
                                <div>
                                    <label for="next_maintenance" class="block text-sm font-medium text-gray-700 mb-2">
                                        Next Maintenance Date <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <input type="date"
                                           name="next_maintenance"
                                           id="next_maintenance"
                                           value="{{ old('next_maintenance') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Notes -->
                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Additional Notes <span class="text-gray-400 text-xs">(optional)</span>
                                    </label>
                                    <textarea name="notes"
                                              id="notes"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Any additional information about this bus...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            @if(Route::has($indexRoute))
                                <a href="{{ route($indexRoute) }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-times mr-2"></i> Cancel
                                </a>
                            @endif

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Save Bus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                // Validate that available seats don't exceed total seats
                const totalSeats = document.getElementById('total_seats');
                const availableSeats = document.getElementById('available_seats');

                if (totalSeats && availableSeats) {
                    const validateSeats = function() {
                        const total = parseInt(totalSeats.value) || 0;
                        const available = parseInt(availableSeats.value) || 0;

                        if (available > total) {
                            availableSeats.setCustomValidity('Available seats cannot exceed total seats');
                            availableSeats.classList.add('border-red-500');
                        } else {
                            availableSeats.setCustomValidity('');
                            availableSeats.classList.remove('border-red-500');
                        }
                    };

                    totalSeats.addEventListener('input', validateSeats);
                    availableSeats.addEventListener('input', validateSeats);
                }

                // Auto-populate available seats if empty when total seats is set
                if (totalSeats && availableSeats) {
                    totalSeats.addEventListener('blur', function() {
                        if (!availableSeats.value && this.value) {
                            availableSeats.value = this.value;
                        }
                    });
                }

                // Confirm before leaving with unsaved changes
                const form = document.querySelector('form');
                let formChanged = false;

                if (form) {
                    const formInputs = form.querySelectorAll('input, select, textarea');

                    formInputs.forEach(input => {
                        input.addEventListener('change', () => {
                            formChanged = true;
                        });
                        input.addEventListener('input', () => {
                            formChanged = true;
                        });
                    });

                    window.addEventListener('beforeunload', function(e) {
                        if (formChanged) {
                            e.preventDefault();
                            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                        }
                    });

                    form.addEventListener('submit', function() {
                        formChanged = false;
                    });
                }
            })();
        </script>
    @endpush
</x-app-layout>
