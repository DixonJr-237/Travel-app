<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Company
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Create New Company</h3>

                    {{-- Conditional form action based on user role --}}
                    @php
                        $formAction = auth()->user()->hasRole('super_admin')
                            ? route('admin.companies.store')
                            : route('my-company.store');
                    @endphp

                    <form method="POST" action="{{ $formAction }}">
                        @csrf

                        <!-- Company Information -->
                        <div class="mb-8">
                            <h4 class="text-md font-medium text-gray-700 mb-4">Company Information</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Company Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Company Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                           placeholder="Enter company name"
                                           autofocus>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Company Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Company Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           value="{{ old('email') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                           placeholder="company@example.com">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Company Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Company Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel"
                                           name="phone"
                                           id="phone"
                                           value="{{ old('phone') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                           placeholder="+237 XXX XXX XXX">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Company Address (Optional) -->
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                        Company Address
                                    </label>
                                    <input type="text"
                                           name="address"
                                           id="address"
                                           value="{{ old('address') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                           placeholder="Street, City, Country">
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Registration Number -->
                                <div>
                                    <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Registration Number
                                    </label>
                                    <input type="text"
                                           name="registration_number"
                                           id="registration_number"
                                           value="{{ old('registration_number') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('registration_number') border-red-500 @enderror"
                                           placeholder="RC/BUS/2024/001">
                                    @error('registration_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tax ID -->
                                <div>
                                    <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tax ID
                                    </label>
                                    <input type="text"
                                           name="tax_id"
                                           id="tax_id"
                                           value="{{ old('tax_id') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tax_id') border-red-500 @enderror"
                                           placeholder="Tax identification number">
                                    @error('tax_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Admin User Information (Only for Super Admin) -->
                        @if(auth()->user()->hasRole('super_admin'))
                        <div class="mb-8">
                            <h4 class="text-md font-medium text-gray-700 mb-4">Company Admin Account</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Admin Name -->
                                <div>
                                    <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="admin_name"
                                           id="admin_name"
                                           value="{{ old('admin_name') }}"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('admin_name') border-red-500 @enderror"
                                           placeholder="Full name">
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
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('admin_email') border-red-500 @enderror"
                                           placeholder="admin@company.com">
                                    @error('admin_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Admin Password -->
                                <div>
                                    <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password"
                                           name="admin_password"
                                           id="admin_password"
                                           required
                                           minlength="8"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('admin_password') border-red-500 @enderror"
                                           placeholder="Minimum 8 characters">
                                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                                    @error('admin_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Admin Phone -->
                                <div>
                                    <label for="admin_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Admin Phone
                                    </label>
                                    <input type="tel"
                                           name="admin_phone"
                                           id="admin_phone"
                                           value="{{ old('admin_phone') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('admin_phone') border-red-500 @enderror"
                                           placeholder="Admin contact number">
                                    @error('admin_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @else
                        {{-- Company Admin creates their own company - they will be the admin --}}
                        <input type="hidden" name="admin_name" value="{{ auth()->user()->name }}">
                        <input type="hidden" name="admin_email" value="{{ auth()->user()->email }}">
                        @endif

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 border-t pt-6">
                            <a href="{{ auth()->user()->hasRole('super_admin') ? route('admin.companies.index') : route('my-company.dashboard') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Create Company
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
