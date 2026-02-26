<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">All Companies</h3>
                    <p class="text-sm text-gray-500">Manage travel companies in the system</p>
                </div>

                @if(auth()->user()->role === 'super_admin')
                <!-- Only Super Admin can create new companies -->
                <a href="{{ route('admin.companies.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> Add Company
                </a>
                @endif
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <i class="fas fa-building text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Companies</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $companies->total() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Companies Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agencies</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($companies as $company)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-building text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $company->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $company->phone ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $company->agencies_count ?? 0 }} agencies
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $company->users_count ?? 0 }} users
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $company->created_at ? $company->created_at->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if(auth()->user()->role === 'super_admin')
                                                <!-- Super Admin can view all companies -->
                                                <a href="{{ route('admin.companies.show', $company->id_company ?? $company->id) }}"
                                                   class="text-blue-600 hover:text-blue-900" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.companies.edit', $company->id_company ?? $company->id) }}"
                                                   class="text-green-600 hover:text-green-900" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.companies.destroy', $company->id_company ?? $company->id) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this company? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @elseif(auth()->user()->role === 'company_admin' && auth()->user()->company_id == ($company->id_company ?? $company->id))
                                                <!-- Company Admin can only view their own company -->
                                                <a href="{{ route('my-company.dashboard') }}"
                                                   class="text-blue-600 hover:text-blue-900" title="View My Company">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('my-company.edit') }}"
                                                   class="text-green-600 hover:text-green-900" title="Edit My Company">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-building text-4xl text-gray-300 mb-3"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Companies Found</h3>
                                            <p class="text-gray-500 mb-4">
                                                @if(auth()->user()->role === 'super_admin')
                                                    Get started by creating a new company
                                                @else
                                                    No companies are available at this time
                                                @endif
                                            </p>
                                            @if(auth()->user()->role === 'super_admin')
                                                <a href="{{ route('admin.companies.create') }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                    <i class="fas fa-plus mr-2"></i> Add Company
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($companies->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $companies->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
