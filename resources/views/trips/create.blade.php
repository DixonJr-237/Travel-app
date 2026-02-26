@extends('layouts.app')

@section('title', 'Create New Trip')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Schedule New Trip</h1>
            <p class="text-sm text-gray-600 mt-1">Fill in the details to create a new trip</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('trips.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Route Selection -->
                    <div>
                        <label for="route_id" class="block text-sm font-medium text-gray-700 mb-2">Route *</label>
                        <select name="route_id" id="route_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('route_id') border-red-500 @enderror" required>
                            <option value="">Select Route</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                    {{ $route->name }} ({{ $route->origin }} → {{ $route->destination }})
                                </option>
                            @endforeach
                        </select>
                        @error('route_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bus Selection -->
                    <div>
                        <label for="bus_id" class="block text-sm font-medium text-gray-700 mb-2">Bus *</label>
                        <select name="bus_id" id="bus_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('bus_id') border-red-500 @enderror" required>
                            <option value="">Select Bus</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->id }}" {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                                    {{ $bus->registration_number }} ({{ $bus->capacity }} seats)
                                </option>
                            @endforeach
                        </select>
                        @error('bus_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departure Date & Time -->
                    <div>
                        <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-2">Departure Date & Time *</label>
                        <input type="datetime-local"
                               name="departure_time"
                               id="departure_time"
                               value="{{ old('departure_time') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('departure_time') border-red-500 @enderror"
                               required>
                        @error('departure_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arrival Date & Time -->
                    <div>
                        <label for="arrival_time" class="block text-sm font-medium text-gray-700 mb-2">Arrival Date & Time *</label>
                        <input type="datetime-local"
                               name="arrival_time"
                               id="arrival_time"
                               value="{{ old('arrival_time') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('arrival_time') border-red-500 @enderror"
                               required>
                        @error('arrival_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price ($) *</label>
                        <input type="number"
                               name="price"
                               id="price"
                               value="{{ old('price') }}"
                               step="0.01"
                               min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('price') border-red-500 @enderror"
                               required>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes"
                                  id="notes"
                                  rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('trips.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Create Trip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
