<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ auth()->user()->hasRole('super_admin') ? 'Create New Agency' : 'Add Agency to Your Company' }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ auth()->user()->hasRole('super_admin')
                        ? 'Register a new agency in the system'
                        : 'Add a new agency under your company management' }}
                </p>
            </div>

            @php
                $user = auth()->user();
                $backRoute = match($user->role) {
                    'super_admin' => 'admin.agencies.index',
                    'company_admin' => 'my-company.agencies.index',
                    default => 'dashboard'
                };
            @endphp

            @if(isset($backRoute) && Route::has($backRoute))
                <a href="{{ route($backRoute) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Agencies
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

                    {{-- Role-based form action --}}
                    @php
                        $isSuperAdmin = auth()->user()->hasRole('super_admin');
                        $isCompanyAdmin = auth()->user()->hasRole('company_admin');

                        // Determine the correct store route based on role
                        $storeRoute = match(true) {
                            $isSuperAdmin => 'admin.agencies.store',
                            $isCompanyAdmin => 'my-company.agencies.store', // Fixed: was 'my-agency.store'
                            default => null
                        };

                        // Determine if route exists
                        $routeExists = $storeRoute && Route::has($storeRoute);

                        // Get company ID for company admin
                        $companyId = $isCompanyAdmin ? auth()->user()->company_id : null;
                        $company = $isCompanyAdmin ? auth()->user()->company : null;
                    @endphp

                    <!-- Form Header with Instructions -->
                    <div class="mb-6 p-4 {{ $isSuperAdmin ? 'bg-purple-50' : 'bg-blue-50' }} rounded-lg">
                        <h4 class="text-sm font-medium {{ $isSuperAdmin ? 'text-purple-800' : 'text-blue-800' }} flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Agency Registration Information
                        </h4>
                        <p class="text-xs {{ $isSuperAdmin ? 'text-purple-600' : 'text-blue-600' }} mt-1">
                            Fields marked with <span class="text-red-500">*</span> are required.
                            @if($isCompanyAdmin && $company)
                                This agency will be associated with <strong>{{ $company->name }}</strong>.
                            @elseif($isCompanyAdmin && !$company)
                                <span class="text-red-600">Warning: No company associated with your account.</span>
                            @endif
                        </p>
                    </div>

                    @if(!$isCompanyAdmin || ($isCompanyAdmin && $companyId))
                        @if($routeExists)
                            <form method="POST" action="{{ route($storeRoute) }}" class="space-y-6">
                                @csrf

                                <!-- Basic Information Section -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                        <i class="fas fa-building text-blue-600 mr-2"></i>
                                        Basic Information
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Agency Name -->
                                        <div class="md:col-span-2">
                                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                                Agency Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                   name="name"
                                                   id="name"
                                                   value="{{ old('name') }}"
                                                   required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                                   placeholder="e.g., Downtown Travel Agency">
                                            @error('name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                                Email Address <span class="text-red-500">*</span>
                                            </label>
                                            <input type="email"
                                                   name="email"
                                                   id="email"
                                                   value="{{ old('email') }}"
                                                   required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                                   placeholder="agency@example.com">
                                            @error('email')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Phone -->
                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                                Phone Number <span class="text-red-500">*</span>
                                            </label>
                                            <input type="tel"
                                                   name="phone"
                                                   id="phone"
                                                   value="{{ old('phone') }}"
                                                   required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                                   placeholder="+237 123 456 789">
                                            @error('phone')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Address -->
                                        <div class="md:col-span-2">
                                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                                Physical Address <span class="text-red-500">*</span>
                                            </label>
                                            <textarea name="address"
                                                      id="address"
                                                      required
                                                      rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                                      placeholder="Full street address, landmark, etc.">{{ old('address') }}</textarea>
                                            @error('address')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Company (Hidden for company admin, visible for super admin) -->
                                        @if($isSuperAdmin)
                                        <div>
                                            <label for="id_company" class="block text-sm font-medium text-gray-700 mb-2">
                                                Parent Company <span class="text-red-500">*</span>
                                            </label>
                                            <select name="id_company"
                                                    id="id_company"
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_company') border-red-500 @enderror">
                                                <option value="">Select Company</option>
                                                @foreach($companies ?? [] as $company)
                                                    <option value="{{ $company->id_company }}"
                                                        {{ old('id_company') == $company->id_company ? 'selected' : '' }}>
                                                        {{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_company')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        @else
                                            <input type="hidden" name="id_company" value="{{ $companyId }}">
                                        @endif
                                    </div>
                                </div>

                                <!-- Location Section -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                        <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>
                                        Location Details
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Country -->
                                        <div>
                                            <label for="id_country" class="block text-sm font-medium text-gray-700 mb-2">
                                                Country <span class="text-red-500">*</span>
                                            </label>
                                            <select name="id_country"
                                                    id="id_country"
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_country') border-red-500 @enderror">
                                                <option value="">Select Country</option>
                                                @foreach($countries ?? [] as $country)
                                                    <option value="{{ $country->id_country }}"
                                                        {{ old('id_country') == $country->id_country ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_country')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Region -->
                                        <div>
                                            <label for="id_region" class="block text-sm font-medium text-gray-700 mb-2">
                                                Region <span class="text-red-500">*</span>
                                            </label>
                                            <select name="id_region"
                                                    id="id_region"
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_region') border-red-500 @enderror">
                                                <option value="">Select Region</option>
                                                @foreach($regions ?? [] as $region)
                                                    <option value="{{ $region->id_region }}"
                                                        {{ old('id_region') == $region->id_region ? 'selected' : '' }}
                                                        data-country-id="{{ $region->id_country ?? '' }}">
                                                        {{ $region->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_region')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Sub Region -->
                                        <div>
                                            <label for="id_sub_region" class="block text-sm font-medium text-gray-700 mb-2">
                                                Sub Region <span class="text-red-500">*</span>
                                            </label>
                                            <select name="id_sub_region"
                                                    id="id_sub_region"
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_sub_region') border-red-500 @enderror">
                                                <option value="">Select Sub Region</option>
                                                @foreach($subRegions ?? [] as $subRegion)
                                                    <option value="{{ $subRegion->id_sub_region }}"
                                                        {{ old('id_sub_region') == $subRegion->id_sub_region ? 'selected' : '' }}
                                                        data-region-id="{{ $subRegion->id_region }}">
                                                        {{ $subRegion->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_sub_region')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- City -->
                                        <div>
                                            <label for="id_city" class="block text-sm font-medium text-gray-700 mb-2">
                                                City <span class="text-red-500">*</span>
                                            </label>
                                            <select name="id_city"
                                                    id="id_city"
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_city') border-red-500 @enderror">
                                                <option value="">Select City</option>
                                                @foreach($cities ?? [] as $city)
                                                    <option value="{{ $city->id_city }}"
                                                        {{ old('id_city') == $city->id_city ? 'selected' : '' }}
                                                        data-sub-region-id="{{ $city->id_sub_region }}">
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_city')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Coordinates (Optional) -->
                                        <div class="md:col-span-2">
                                            <label for="id_coord" class="block text-sm font-medium text-gray-700 mb-2">
                                                Exact Location <span class="text-gray-400 text-xs">(optional)</span>
                                            </label>
                                            <select name="id_coord"
                                                    id="id_coord"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_coord') border-red-500 @enderror">
                                                <option value="">Select Coordinates</option>
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500">Select a specific location or enter coordinates manually</p>
                                            @error('id_coord')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @if($isSuperAdmin)
                                    <!-- Admin User Creation Section (Super Admin Only) -->
                                    <div class="mb-8">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                            <i class="fas fa-user-tie text-purple-600 mr-2"></i>
                                            Agency Admin Account
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-4">Create a user account for the agency administrator</p>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Admin Name -->
                                            <div>
                                                <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Admin Full Name <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text"
                                                       name="admin_name"
                                                       id="admin_name"
                                                       value="{{ old('admin_name') }}"
                                                       required
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                       placeholder="John Doe">
                                                @error('admin_name')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Admin Email -->
                                            <div>
                                                <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Admin Email <span class="text-red-500">*</span>
                                                </label>
                                                <input type="email"
                                                       name="admin_email"
                                                       id="admin_email"
                                                       value="{{ old('admin_email') }}"
                                                       required
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                       placeholder="admin@agency.com">
                                                @error('admin_email')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Admin Password -->
                                            <div>
                                                <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Password <span class="text-red-500">*</span>
                                                </label>
                                                <input type="password"
                                                       name="admin_password"
                                                       id="admin_password"
                                                       required
                                                       minlength="8"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                       placeholder="Minimum 8 characters">
                                                @error('admin_password')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Admin Password Confirmation -->
                                            <div>
                                                <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Confirm Password <span class="text-red-500">*</span>
                                                </label>
                                                <input type="password"
                                                       name="admin_password_confirmation"
                                                       id="admin_password_confirmation"
                                                       required
                                                       minlength="8"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            </div>

                                            <!-- Admin Phone -->
                                            <div>
                                                <label for="admin_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Admin Phone <span class="text-gray-400 text-xs">(optional)</span>
                                                </label>
                                                <input type="tel"
                                                       name="admin_phone"
                                                       id="admin_phone"
                                                       value="{{ old('admin_phone') }}"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                       placeholder="Admin contact number">
                                                @error('admin_phone')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @elseif($isCompanyAdmin)
                                    {{-- Company Admin creates agency - they will be the admin --}}
                                    <div class="mb-8 p-4 bg-blue-50 rounded-lg">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-sm font-medium text-blue-800">Agency Administrator</h4>
                                                <p class="text-sm text-blue-600 mt-1">
                                                    You will be set as the administrator for this agency using your current account:
                                                    <strong class="block mt-1">{{ auth()->user()->name }} ({{ auth()->user()->email }})</strong>
                                                </p>
                                                <input type="hidden" name="user_id" value="{{ auth()->user()->user_id }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Form Actions -->
                                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                                    @if(isset($backRoute) && Route::has($backRoute))
                                        <a href="{{ route($backRoute) }}"
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <i class="fas fa-times mr-2"></i>
                                            Cancel
                                        </a>
                                    @endif

                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <i class="fas fa-save mr-2"></i>
                                        {{ $isSuperAdmin ? 'Create Agency' : 'Add Agency' }}
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Fallback when route doesn't exist -->
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            The form action could not be determined. Please contact support or try again later.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Company admin without company -->
                        <div class="bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        Your account is not associated with any company. Please contact the super administrator.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                // Dependent dropdowns for location
                const countrySelect = document.getElementById('id_country');
                const regionSelect = document.getElementById('id_region');
                const subRegionSelect = document.getElementById('id_sub_region');
                const citySelect = document.getElementById('id_city');
                const coordSelect = document.getElementById('id_coord');

                if (countrySelect && regionSelect) {
                    // Filter regions by country
                    countrySelect.addEventListener('change', function() {
                        const countryId = this.value;

                        // Reset dependent selects
                        regionSelect.innerHTML = '<option value="">Select Region</option>';
                        subRegionSelect.innerHTML = '<option value="">Select Sub Region</option>';
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        coordSelect.innerHTML = '<option value="">Select Coordinates</option>';

                        if (countryId) {
                            const regions = @json($regions ?? []);
                            regions.forEach(region => {
                                if (region.id_country == countryId) {
                                    const option = document.createElement('option');
                                    option.value = region.id_region;
                                    option.textContent = region.name;
                                    regionSelect.appendChild(option);
                                }
                            });
                        }
                    });
                }

                if (regionSelect && subRegionSelect) {
                    // Filter sub regions by region
                    regionSelect.addEventListener('change', function() {
                        const regionId = this.value;

                        // Reset dependent selects
                        subRegionSelect.innerHTML = '<option value="">Select Sub Region</option>';
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        coordSelect.innerHTML = '<option value="">Select Coordinates</option>';

                        if (regionId) {
                            const subRegions = @json($subRegions ?? []);
                            subRegions.forEach(subRegion => {
                                if (subRegion.id_region == regionId) {
                                    const option = document.createElement('option');
                                    option.value = subRegion.id_sub_region;
                                    option.textContent = subRegion.name;
                                    subRegionSelect.appendChild(option);
                                }
                            });
                        }
                    });
                }

                if (subRegionSelect && citySelect) {
                    // Filter cities by sub region
                    subRegionSelect.addEventListener('change', function() {
                        const subRegionId = this.value;

                        // Reset dependent selects
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        coordSelect.innerHTML = '<option value="">Select Coordinates</option>';

                        if (subRegionId) {
                            const cities = @json($cities ?? []);
                            cities.forEach(city => {
                                if (city.id_sub_region == subRegionId) {
                                    const option = document.createElement('option');
                                    option.value = city.id_city;
                                    option.textContent = city.name;
                                    citySelect.appendChild(option);
                                }
                            });
                        }
                    });
                }

                if (citySelect && coordSelect) {
                    // Fetch coordinates when city is selected
                    citySelect.addEventListener('change', function() {
                        const cityId = this.value;

                        // Reset coordinates
                        coordSelect.innerHTML = '<option value="">Select Coordinates</option>';

                        if (cityId) {
                            fetch(`/api/cities/${cityId}/coordinates`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.data && data.data.length > 0) {
                                        data.data.forEach(coord => {
                                            const option = document.createElement('option');
                                            option.value = coord.id_coord;
                                            option.textContent = `${coord.address || 'Location'} (${coord.latitude}, ${coord.longitude})`;
                                            coordSelect.appendChild(option);
                                        });
                                    }
                                })
                                .catch(error => console.error('Error fetching coordinates:', error));
                        }
                    });
                }

                // Password confirmation validation
                const password = document.getElementById('admin_password');
                const confirmPassword = document.getElementById('admin_password_confirmation');

                if (password && confirmPassword) {
                    function validatePassword() {
                        if (password.value !== confirmPassword.value) {
                            confirmPassword.setCustomValidity('Passwords do not match');
                            confirmPassword.classList.add('border-red-500');
                        } else {
                            confirmPassword.setCustomValidity('');
                            confirmPassword.classList.remove('border-red-500');
                        }
                    }

                    password.addEventListener('change', validatePassword);
                    confirmPassword.addEventListener('keyup', validatePassword);
                }

                // Unsaved changes warning
                const form = document.querySelector('form');
                let formChanged = false;

                if (form) {
                    const formInputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');

                    formInputs.forEach(input => {
                        input.addEventListener('change', () => { formChanged = true; });
                        input.addEventListener('input', () => { formChanged = true; });
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
