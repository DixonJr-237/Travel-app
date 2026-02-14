<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Bus</h2></x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Add New Bus</h3>
                    <form method="POST" action="{{ route('buses.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">Registration Number *</label>
                                <input type="text" name="registration_number" id="registration_number" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., CE1234AB">
                                @error('registration_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="agency_id" class="block text-sm font-medium text-gray-700 mb-2">Agency *</label>
                                <select name="agency_id" id="agency_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Agency</option>
                                    @foreach($agencies as $agency)<option value="{{ $agency->id_agence }}">{{ $agency->name }}</option>@endforeach
                                </select>
                                @error('agency_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model *</label>
                                <input type="text" name="model" id="model" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Mercedes-Benz Sprinter">
                                @error('model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year *</label>
                                <input type="number" name="year" id="year" required min="1990" max="2026" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 2020">
                                @error('year')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="seats_count" class="block text-sm font-medium text-gray-700 mb-2">Number of Seats *</label>
                                <input type="number" name="seats_count" id="seats_count" required min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 50">
                                @error('seats_count')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active">Active</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('buses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase shadow-sm hover:bg-gray-50">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700"><i class="fas fa-plus mr-2"></i> Add Bus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
