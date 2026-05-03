<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Logs</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Range Filter -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.reports.audit-logs') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-200">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-200">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="action" class="block text-sm font-medium text-gray-200">Action</label>
                            <select name="action" id="action" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                                <option value="">All Actions</option>
                                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                                <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                                <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-500">Filter</button>
                        </div>
                        <div>
                            <a href="{{ route('admin.reports.audit-logs') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-600">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Audit Logs Data -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Timestamp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Entity Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Entity ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Details</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
@forelse($auditLogs as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-100">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $log->user->name ?? 'System' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($log->action == 'create') bg-green-800 text-green-100
                                            @elseif($log->action == 'update') bg-blue-800 text-blue-100
                                            @elseif($log->action == 'delete') bg-red-800 text-red-100
                                            @elseif($log->action == 'login') bg-yellow-800 text-yellow-100
                                            @elseif($log->action == 'logout') bg-slate-700 text-gray-200
                                            @else bg-slate-700 text-gray-200 @endif">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $log->entity_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $log->entity_id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-300">{{ $log->details }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-300">No audit logs found for the selected criteria</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
@if($auditLogs->hasPages())
                    <div class="mt-4">
{{ $auditLogs->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back to Reports</a>
            </div>
        </div>
    </div>
</x-app-layout>