<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ auth()->user()->hasRole('super_admin') ? 'Create Agency (Administrator)' : 'Create Agency' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Role-based header --}}
                    @php
                        $isSuperAdmin = auth()->user()->hasRole('super_admin');
                        $isCompanyAdmin = auth()->user()->hasRole('company_admin');
                        $formAction = $isSuperAdmin
                            ? route('admin.agencies.store')
                            : route('my-agency.store');
                    @endphp

                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Create New Agency
                        </h3>
                        @if($isSuperAdmin)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                Super Admin
                            </span>
                        @elseif($isCompanyAdmin)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Company Admin
                            </span>
                        @endif
                    </div>

                    {{-- Form with enctype for potential file uploads --}}
                    <form method="POST" action="{{ $formAction }}" class="space-y-8">
                        @csrf

                        {{-- Company Selection Section --}}
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h4 class="text-md font-medium text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Company Assignment
                            </h4>

                            @if($isSuperAdmin)
                            <div class="mb-4">
                                <label for="id_company" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Company <span class="text-red-500">*</span>
                                </label>
                                <select name="id_company" id="id_company" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_company') border-red-500 @enderror">
                                    <option value="">-- Choose a company --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id_company }}" {{ old('id_company') == $company->id_company ? 'selected' : '' }}
                                                data-status="{{ $company->status }}">
                                            {{ $company->name }} @if($company->status !== 'active')({{ $company->status }})@endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_company')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Select the company this agency will belong to</p>
                            </div>
                            @else
                                <input type="hidden" name="id_company" value="{{ auth()->user()->company_id }}">
                                <div class="bg-blue-50 p-4 rounded-md">
                                    <p class="text-sm text-blue-700 flex items-start">
                                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>
                                            <strong class="font-medium">Company:</strong> {{ auth()->user()->company->name ?? 'Your company' }}<br>
                                            <span class="text-xs">This agency will be automatically assigned to your company.</span>
                                        </span>
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Location Details with AJAX loading --}}
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h4 class="text-md font-medium text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Location Details
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="location-container">
                                {{-- Country --}}
                                <div>
                                    <label for="id_country" class="block text-sm font-medium text-gray-700 mb-2">
                                        Country <span class="text-red-500">*</span>
                                    </label>
                                    <select name="id_country" id="id_country" required
                                            class="location-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_country') border-red-500 @enderror">
                                        <option value="">-- Select Country --</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id_country }}" {{ old('id_country') == $country->id_country ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Region (loaded via AJAX) --}}
                                <div>
                                    <label for="id_region" class="block text-sm font-medium text-gray-700 mb-2">
                                        Region <span class="text-red-500">*</span>
                                    </label>
                                    <select name="id_region" id="id_region" required disabled
                                            class="location-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_region') border-red-500 @enderror">
                                        <option value="">-- Select Country First --</option>
                                    </select>
                                    @error('id_region')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Sub Region (loaded via AJAX) --}}
                                <div>
                                    <label for="id_sub_region" class="block text-sm font-medium text-gray-700 mb-2">
                                        Sub Region <span class="text-red-500">*</span>
                                    </label>
                                    <select name="id_sub_region" id="id_sub_region" required disabled
                                            class="location-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_sub_region') border-red-500 @enderror">
                                        <option value="">-- Select Region First --</option>
                                    </select>
                                    @error('id_sub_region')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- City (loaded via AJAX) --}}
                                <div>
                                    <label for="id_city" class="block text-sm font-medium text-gray-700 mb-2">
                                        City <span class="text-red-500">*</span>
                                    </label>
                                    <select name="id_city" id="id_city" required disabled
                                            class="location-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_city') border-red-500 @enderror">
                                        <option value="">-- Select Sub Region First --</option>
                                    </select>
                                    @error('id_city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Address/Coordinates (loaded via AJAX) --}}
                                <div class="md:col-span-2">
                                    <label for="id_coord" class="block text-sm font-medium text-gray-700 mb-2">
                                        Address/Coordinates <span class="text-red-500">*</span>
                                    </label>
                                    <select name="id_coord" id="id_coord" required disabled
                                            class="location-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_coord') border-red-500 @enderror">
                                        <option value="">-- Select City First --</option>
                                    </select>
                                    @error('id_coord')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <div id="coordinates-loading" class="hidden mt-2 text-sm text-gray-500">
                                        <svg class="animate-spin h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Loading coordinates...
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Agency Details --}}
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h4 class="text-md font-medium text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Agency Information
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Agency Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                           placeholder="e.g., Yaoundé Central Agency"
                                           autofocus>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
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

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel"
                                           name="phone"
                                           id="phone"
                                           value="{{ old('phone') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                           placeholder="+237 XXX XXX XXX">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                        Street Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="address"
                                           id="address"
                                           value="{{ old('address') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                           placeholder="123 Main Street">
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Admin Account Section --}}
                        @if($isSuperAdmin)
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h4 class="text-md font-medium text-gray-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Agency Admin Account
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="admin_name"
                                           id="admin_name"
                                           value="{{ old('admin_name') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('admin_name') border-red-500 @enderror"
                                           placeholder="Full name">
                                    @error('admin_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email"
                                           name="admin_email"
                                           id="admin_email"
                                           value="{{ old('admin_email') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('admin_email') border-red-500 @enderror"
                                           placeholder="admin@agency.com">
                                    @error('admin_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password"
                                           name="admin_password"
                                           id="admin_password"
                                           required
                                           minlength="8"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('admin_password') border-red-500 @enderror"
                                           placeholder="Minimum 8 characters">
                                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                                    @error('admin_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirm Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password"
                                           name="admin_password_confirmation"
                                           id="admin_password_confirmation"
                                           required
                                           minlength="8"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Re-enter password">
                                </div>

                                <div>
                                    <label for="admin_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Phone
                                    </label>
                                    <input type="tel"
                                           name="admin_phone"
                                           id="admin_phone"
                                           value="{{ old('admin_phone') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('admin_phone') border-red-500 @enderror"
                                           placeholder="Admin contact number">
                                    @error('admin_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @else
                            <input type="hidden" name="admin_name" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="admin_email" value="{{ auth()->user()->email }}">
                            <input type="hidden" name="admin_phone" value="{{ auth()->user()->phone }}">

                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm text-blue-700">
                                            <strong class="font-medium">You will be the administrator for this agency.</strong><br>
                                            Your account ({{ auth()->user()->email }}) will be updated with agency admin privileges upon creation.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Status Message for Company Admin --}}
                        @if($isCompanyAdmin && !$isSuperAdmin)
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Pending Approval</strong><br>
                                        Your agency will be created in <strong class="font-medium">pending</strong> status and will require approval from a super administrator before it becomes active. You'll be notified once approved.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Form Actions --}}
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ $isSuperAdmin ? route('admin.agencies.index') : route('my-agency.dashboard') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create Agency
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Location hierarchy loader
        class LocationLoader {
            constructor() {
                this.countrySelect = document.getElementById('id_country');
                this.regionSelect = document.getElementById('id_region');
                this.subRegionSelect = document.getElementById('id_sub_region');
                this.citySelect = document.getElementById('id_city');
                this.coordSelect = document.getElementById('id_coord');
                this.loadingEl = document.getElementById('coordinates-loading');

                this.init();
            }

            init() {
                if (this.countrySelect) {
                    this.countrySelect.addEventListener('change', () => this.loadRegions());
                }
                if (this.regionSelect) {
                    this.regionSelect.addEventListener('change', () => this.loadSubRegions());
                }
                if (this.subRegionSelect) {
                    this.subRegionSelect.addEventListener('change', () => this.loadCities());
                }
                if (this.citySelect) {
                    this.citySelect.addEventListener('change', () => this.loadCoordinates());
                }

                // Load initial data if country is preselected
                if (this.countrySelect && this.countrySelect.value) {
                    this.loadRegions();
                }
            }

            async loadRegions() {
                const countryId = this.countrySelect.value;
                if (!countryId) {
                    this.disableSelect(this.regionSelect, '-- Select Country First --');
                    this.disableSelect(this.subRegionSelect, '-- Select Region First --');
                    this.disableSelect(this.citySelect, '-- Select Sub Region First --');
                    this.disableSelect(this.coordSelect, '-- Select City First --');
                    return;
                }

                this.regionSelect.disabled = true;
                this.regionSelect.innerHTML = '<option value="">Loading regions...</option>';

                try {
                    const response = await fetch(`/api/regions?country_id=${countryId}`);
                    const data = await response.json();

                    this.regionSelect.innerHTML = '<option value="">-- Select Region --</option>';
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(region => {
                            const option = document.createElement('option');
                            option.value = region.id_region;
                            option.textContent = region.name;
                            if (region.id_region == '{{ old('id_region') }}') {
                                option.selected = true;
                            }
                            this.regionSelect.appendChild(option);
                        });
                        this.regionSelect.disabled = false;
                    } else {
                        this.regionSelect.innerHTML = '<option value="">No regions available</option>';
                    }
                } catch (error) {
                    console.error('Error loading regions:', error);
                    this.regionSelect.innerHTML = '<option value="">Error loading regions</option>';
                }

                this.disableSelect(this.subRegionSelect, '-- Select Region First --');
                this.disableSelect(this.citySelect, '-- Select Sub Region First --');
                this.disableSelect(this.coordSelect, '-- Select City First --');
            }

            async loadSubRegions() {
                const regionId = this.regionSelect.value;
                if (!regionId) {
                    this.disableSelect(this.subRegionSelect, '-- Select Region First --');
                    this.disableSelect(this.citySelect, '-- Select Sub Region First --');
                    this.disableSelect(this.coordSelect, '-- Select City First --');
                    return;
                }

                this.subRegionSelect.disabled = true;
                this.subRegionSelect.innerHTML = '<option value="">Loading sub regions...</option>';

                try {
                    const response = await fetch(`/api/sub-regions?region_id=${regionId}`);
                    const data = await response.json();

                    this.subRegionSelect.innerHTML = '<option value="">-- Select Sub Region --</option>';
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(subRegion => {
                            const option = document.createElement('option');
                            option.value = subRegion.id_sub_region;
                            option.textContent = subRegion.name;
                            if (subRegion.id_sub_region == '{{ old('id_sub_region') }}') {
                                option.selected = true;
                            }
                            this.subRegionSelect.appendChild(option);
                        });
                        this.subRegionSelect.disabled = false;
                    } else {
                        this.subRegionSelect.innerHTML = '<option value="">No sub regions available</option>';
                    }
                } catch (error) {
                    console.error('Error loading sub regions:', error);
                    this.subRegionSelect.innerHTML = '<option value="">Error loading sub regions</option>';
                }

                this.disableSelect(this.citySelect, '-- Select Sub Region First --');
                this.disableSelect(this.coordSelect, '-- Select City First --');
            }

            async loadCities() {
                const subRegionId = this.subRegionSelect.value;
                if (!subRegionId) {
                    this.disableSelect(this.citySelect, '-- Select Sub Region First --');
                    this.disableSelect(this.coordSelect, '-- Select City First --');
                    return;
                }

                this.citySelect.disabled = true;
                this.citySelect.innerHTML = '<option value="">Loading cities...</option>';

                try {
                    const response = await fetch(`/api/cities?sub_region_id=${subRegionId}`);
                    const data = await response.json();

                    this.citySelect.innerHTML = '<option value="">-- Select City --</option>';
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id_city;
                            option.textContent = city.name;
                            if (city.id_city == '{{ old('id_city') }}') {
                                option.selected = true;
                            }
                            this.citySelect.appendChild(option);
                        });
                        this.citySelect.disabled = false;
                    } else {
                        this.citySelect.innerHTML = '<option value="">No cities available</option>';
                    }
                } catch (error) {
                    console.error('Error loading cities:', error);
                    this.citySelect.innerHTML = '<option value="">Error loading cities</option>';
                }

                this.disableSelect(this.coordSelect, '-- Select City First --');
            }

            async loadCoordinates() {
                const cityId = this.citySelect.value;
                if (!cityId) {
                    this.disableSelect(this.coordSelect, '-- Select City First --');
                    return;
                }

                this.coordSelect.disabled = true;
                this.coordSelect.innerHTML = '<option value="">Loading addresses...</option>';
                if (this.loadingEl) this.loadingEl.classList.remove('hidden');

                try {
                    const response = await fetch(`/api/coordinates?city_id=${cityId}`);
                    const data = await response.json();

                    this.coordSelect.innerHTML = '<option value="">-- Select Address --</option>';
                    if (data.data && data.data.length > 0) {
                        data.data.forEach(coord => {
                            const option = document.createElement('option');
                            option.value = coord.id_coord;
                            option.textContent = coord.address;
                            if (coord.id_coord == '{{ old('id_coord') }}') {
                                option.selected = true;
                            }
                            this.coordSelect.appendChild(option);
                        });
                        this.coordSelect.disabled = false;
                    } else {
                        this.coordSelect.innerHTML = '<option value="">No addresses available</option>';
                    }
                } catch (error) {
                    console.error('Error loading coordinates:', error);
                    this.coordSelect.innerHTML = '<option value="">Error loading addresses</option>';
                } finally {
                    if (this.loadingEl) this.loadingEl.classList.add('hidden');
                }
            }

            disableSelect(select, placeholder) {
                if (select) {
                    select.disabled = true;
                    select.innerHTML = `<option value="">${placeholder}</option>`;
                }
            }
        }

        // Initialize location loader
        new LocationLoader();
    });
    </script>
    @endpush
</x-app-layout>
