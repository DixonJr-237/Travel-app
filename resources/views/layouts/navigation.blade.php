<nav x-data="{ open: false }" class="bg-gradient-to-r from-green-700 to-green-600 border-b border-green-800 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                        <div class="bg-white rounded-lg p-1.5 shadow-md group-hover:shadow-lg transition-all duration-300">
                            <i class="fas fa-bus text-green-700 text-xl"></i>
                        </div>
                        <span class="text-xl font-bold text-black tracking-tight">BusSwift</span>
                        <span class="px-2 py-0.5 bg-green-500 text-black text-xs font-semibold rounded-full">Travel</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-6 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:bg-green-600 px-4 py-2 rounded-lg transition-all duration-200">
                        <i class="fas fa-chart-pie mr-2"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @can('view-trips')
                    <x-nav-link :href="route('trips.index')" :active="request()->routeIs('trips.*')" class="text-white hover:bg-green-600 px-4 py-2 rounded-lg transition-all duration-200">
                        <i class="fas fa-route mr-2"></i>
                        {{ __('Trips') }}
                    </x-nav-link>
                    @endcan

                    @can('view-buses')
                    <x-nav-link :href="route('buses.index')" :active="request()->routeIs('buses.*')" class="text-white hover:bg-green-600 px-4 py-2 rounded-lg transition-all duration-200">
                        <i class="fas fa-bus mr-2"></i>
                        {{ __('Buses') }}
                    </x-nav-link>
                    @endcan

                    @can('view-tickets')
                    <x-nav-link :href="route('tickets.my')" :active="request()->routeIs('tickets.*')" class="text-white hover:bg-green-600 px-4 py-2 rounded-lg transition-all duration-200">
                        <i class="fas fa-ticket-alt mr-2"></i>
                        {{ __('Tickets') }}
                    </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifications - COMPLETELY REMOVED -->

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="flex items-center space-x-3 text-sm focus:outline-none group">
                            <div class="flex items-center space-x-3 bg-green-800 bg-opacity-50 hover:bg-opacity-75 rounded-full pl-1 pr-3 py-1 transition-all duration-300">
                                <!-- User Avatar -->
                                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-md">
                                    <span class="text-green-700 font-bold text-sm">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="text-left hidden md:block">
                                    <div class="text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-green-200">{{ ucfirst(str_replace('_', ' ', Auth::user()->role ?? 'User')) }}</div>
                                </div>
                                <i class="fas fa-chevron-down text-white text-xs transition-transform group-hover:rotate-180"></i>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- User Info Header -->
                        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
                            <p class="text-xs text-green-600 font-semibold uppercase tracking-wider">Signed in as</p>
                            <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-600 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Profile Link -->
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center space-x-2 hover:bg-green-50">
                            <i class="fas fa-user-circle text-green-600 w-4"></i>
                            <span>{{ __('Profile') }}</span>
                        </x-dropdown-link>

                        <!-- Dashboard Link -->
                        <x-dropdown-link :href="route('dashboard')" class="flex items-center space-x-2 hover:bg-green-50">
                            <i class="fas fa-chart-pie text-green-600 w-4"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </x-dropdown-link>

                        @can('manage-settings')
                        <!-- Settings Link -->
                        <x-dropdown-link :href="route('settings.index')" class="flex items-center space-x-2 hover:bg-green-50">
                            <i class="fas fa-cog text-green-600 w-4"></i>
                            <span>{{ __('Settings') }}</span>
                        </x-dropdown-link>
                        @endcan

                        <!-- Divider -->
                        <div class="border-t border-gray-100 my-1"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="flex items-center space-x-2 text-red-600 hover:bg-red-50 hover:text-red-700">
                                <i class="fas fa-sign-out-alt w-4"></i>
                                <span>{{ __('Log Out') }}</span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white hover:bg-green-600 focus:outline-none focus:bg-green-600 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-green-700 border-t border-green-600">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Dashboard -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:bg-green-600 flex items-center space-x-2">
                <i class="fas fa-chart-pie w-5"></i>
                <span>{{ __('Dashboard') }}</span>
            </x-responsive-nav-link>

            <!-- Trips -->
            @can('view-trips')
            <x-responsive-nav-link :href="route('trips.index')" :active="request()->routeIs('trips.*')" class="text-white hover:bg-green-600 flex items-center space-x-2">
                <i class="fas fa-route w-5"></i>
                <span>{{ __('Trips') }}</span>
            </x-responsive-nav-link>
            @endcan

            <!-- Buses -->
            @can('view-buses')
            <x-responsive-nav-link :href="route('buses.index')" :active="request()->routeIs('buses.*')" class="text-white hover:bg-green-600 flex items-center space-x-2">
                <i class="fas fa-bus w-5"></i>
                <span>{{ __('Buses') }}</span>
            </x-responsive-nav-link>
            @endcan

            <!-- Tickets -->
            @can('view-tickets')
            <x-responsive-nav-link :href="route('tickets.my')" :active="request()->routeIs('tickets.*')" class="text-white hover:bg-green-600 flex items-center space-x-2">
                <i class="fas fa-ticket-alt w-5"></i>
                <span>{{ __('Tickets') }}</span>
            </x-responsive-nav-link>
            @endcan

            <!-- Notifications - COMPLETELY REMOVED -->
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-green-600">
            <div class="px-4 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-md">
                    <span class="text-green-700 font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div>
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-green-200">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:bg-green-600 flex items-center space-x-2">
                    <i class="fas fa-user-circle w-5"></i>
                    <span>{{ __('Profile') }}</span>
                </x-responsive-nav-link>

                @can('manage-settings')
                <x-responsive-nav-link :href="route('settings.index')" class="text-white hover:bg-green-600 flex items-center space-x-2">
                    <i class="fas fa-cog w-5"></i>
                    <span>{{ __('Settings') }}</span>
                </x-responsive-nav-link>
                @endcan

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-red-300 hover:bg-red-600 hover:text-white flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>{{ __('Log Out') }}</span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
