<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ auth()->user()->hasRole('super_admin') ? 'Create New Company' : 'Register Your Company' }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ auth()->user()->hasRole('super_admin')
                        ? 'Add a new company to the system'
                        : 'Register your company to start managing your transportation business' }}
                </p>
            </div>

            @php
                $user = auth()->user();
                $backRoute = match($user->role) {
                    'super_admin' => 'admin.companies.index',
                    'company_admin' => 'my-company.dashboard',
                    default => 'dashboard'
                };
            @endphp

            @if(isset($backRoute) && Route::has($backRoute))
                <a href="{{ route($backRoute) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ auth()->user()->hasRole('super_admin') ? 'Back to Companies' : 'Back to Dashboard' }}
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

                    {{-- Conditional form action based on user role --}}
                    @php
                        $isSuperAdmin = auth()->user()->hasRole('super_admin');
                        $isCompanyAdmin = auth()->user()->hasRole('company_admin');

                        // Determine the correct store route based on role
                        $storeRoute = match(true) {
                            $isSuperAdmin => 'admin.companies.store',
                            $isCompanyAdmin => 'my-company.store', // Changed from 'my-company.store' to match your routes
                            default => null
                        };

                        // Determine if route exists
                        $routeExists = $storeRoute && Route::has($storeRoute);
                    @endphp

                    <!-- Form Header with Instructions -->
                    <div class="mb-6 p-4 {{ $isSuperAdmin ? 'bg-purple-50' : 'bg-blue-50' }} rounded-lg">
                        <h4 class="text-sm font-medium {{ $isSuperAdmin ? 'text-purple-800' : 'text-blue-800' }} flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ $isSuperAdmin ? 'Company Registration Information' : 'Your Company Registration' }}
                        </h4>
                        <p class="text-xs {{ $isSuperAdmin ? 'text-purple-600' : 'text-blue-600' }} mt-1">
                            Fields marked with <span class="text-red-500">*</span> are required.
                            @if($isCompanyAdmin)
                                You are registering as the company administrator.
                            @endif
                        </p>
                    </div>

                    @if($routeExists)
                        <form method="POST" action="{{ route($storeRoute) }}" class="space-y-6">
                            @csrf

                            <!-- Company Information -->
                            <div class="mb-8">
                                <h4 class="text-md font-medium text-gray-700 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                    <i class="fas fa-building text-blue-600 mr-2"></i>
                                    Company Information
                                </h4>

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

                                    <!-- Company Address -->
                                    <div>
                                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                            Company Address <span class="text-gray-400 text-xs">(optional)</span>
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
                                            Registration Number <span class="text-gray-400 text-xs">(optional)</span>
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
                                            Tax ID <span class="text-gray-400 text-xs">(optional)</span>
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
                            @if($isSuperAdmin)
                            <div class="mb-8">
                                <h4 class="text-md font-medium text-gray-700 mb-4 pb-2 border-b border-gray-200 flex items-center">
                                    <i class="fas fa-user-tie text-purple-600 mr-2"></i>
                                    Company Admin Account
                                </h4>
                                <p class="text-sm text-gray-600 mb-4">Create a user account for the company administrator</p>

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
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('admin_name') border-red-500 @enderror"
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
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Re-enter password">
                                    </div>

                                    <!-- Admin Phone -->
                                    <div class="md:col-span-2">
                                        <label for="admin_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                            Admin Phone <span class="text-gray-400 text-xs">(optional)</span>
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
                            @elseif($isCompanyAdmin)
                                {{-- Company Admin creates their own company - they will be the admin --}}
                                <div class="mb-8 p-4 bg-blue-50 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-800">Your Account Information</h4>
                                            <p class="text-sm text-blue-600 mt-1">
                                                You will be set as the administrator for this company using your current account:
                                                <strong class="block mt-1">{{ auth()->user()->name }} ({{ auth()->user()->email }})</strong>
                                            </p>
                                            <input type="hidden" name="admin_name" value="{{ auth()->user()->name }}">
                                            <input type="hidden" name="admin_email" value="{{ auth()->user()->email }}">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Terms and Conditions -->
                            <div class="mb-6">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox"
                                               name="terms"
                                               id="terms"
                                               required
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms" class="font-medium text-gray-700">
                                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms and Conditions</a> and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a> <span class="text-red-500">*</span>
                                        </label>
                                    </div>
                                </div>
                                @error('terms')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

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
                                    {{ $isSuperAdmin ? 'Create Company' : 'Register Company' }}
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
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
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
