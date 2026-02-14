<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>BusSwift - @yield('title', 'Travel Management System')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome for bus icon -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')



            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t mt-auto">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-bus mr-2 text-green-600"></i>
                            <span>&copy; {{ date('Y') }} BusSwift. All rights reserved.</span>
                        </div>
                        <div class="flex space-x-4">
                            <a href="{{ route('terms') }}" class="hover:text-green-600">Terms</a>
                            <a href="{{ route('privacy') }}" class="hover:text-green-600">Privacy</a>
                            <a href="{{ route('contact') }}" class="hover:text-green-600">Contact</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
