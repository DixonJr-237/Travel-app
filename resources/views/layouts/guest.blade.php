<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BusSwift - @yield('title', 'Authentication')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome for bus icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 to-gray-100">

        <!-- BusSwift Brand Header -->
        <div class="w-full sm:max-w-md mb-4 text-center">
            <div class="register-header">
                <div class="flex items-center justify-center mb-3">
                    <div class="bg-green-600 rounded-full p-4 shadow-lg">
                        <i class="fas fa-bus text-3xl text-white"></i>
                    </div>
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <h1 class="text-3xl font-bold text-gray-900">BusSwift</h1>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Travel</span>
                </div>
                <p class="text-gray-600 mt-2">@yield('subtitle', 'Your journey begins here')</p>
            </div>
        </div>

        <!-- Auth Card -->
        <div
            class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl border-t-4 border-green-600">

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-600 text-green-800 p-4 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-600 text-red-800 p-4 rounded" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            {{ $slot }}
        </div>

        <!-- Footer Links -->
        <div class="w-full sm:max-w-md mt-6 text-center text-sm text-gray-600">
            <div class="flex justify-center space-x-4">
                <a href="{{ route('terms') }}" class="hover:text-green-600 transition duration-150">
                    <i class="fas fa-file-alt mr-1"></i> Terms
                </a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('privacy') }}" class="hover:text-green-600 transition duration-150">
                    <i class="fas fa-lock mr-1"></i> Privacy
                </a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('contact') }}" class="hover:text-green-600 transition duration-150">
                    <i class="fas fa-envelope mr-1"></i> Contact
                </a>
            </div>
            <div class="mt-2 flex items-center justify-center text-gray-500">
                <i class="fas fa-bus mr-2 text-green-600 text-xs"></i>
                <span>&copy; {{ date('Y') }} BusSwift. All rights reserved.</span>
            </div>
        </div>
    </div>

    <!-- Optional: Add custom styles for better appearance -->
    <style>
        .register-header {
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom focus styles */
        input:focus {
            border-color: #059669;
            ring-color: #059669;
        }

        /* Custom button hover effect */
        button[type="submit"] {
            transition: all 0.3s ease;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }
    </style>
</body>

</html>
