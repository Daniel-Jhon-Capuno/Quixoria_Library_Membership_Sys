<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Borrow Requests</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Filter -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-slate-700">
                <div class="p-6">
                    <form method="GET" action="{{ route('staff.borrow-requests.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-slate-700 rounded-md shadow-sm bg-slate-900 text-gray-100">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest hover:bg-gray-600">Filter</button>
                        </div>
                        <div>
                            <a href="{{ route('staff.borrow-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest hover:bg-gray-600">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Borrow Requests Table -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-700">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Requested</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($borrowRequests as $request)
                                <tr class="bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $request->student->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $request->student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $request->book->title }}</div>
                                        <div class="text-sm text-gray-400">{{ $request->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-100">
                                        {{ $request->student->subscription?->membershipTier?->name ?? 'No Active Subscription' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($request->status == 'pending') bg-yellow-800 text-yellow-200
                                            @elseif($request->status == 'active') bg-green-800 text-green-200
                                            @elseif($request->status == 'returned') bg-slate-700 text-gray-100
                                            @elseif($request->status == 'overdue') bg-red-800 text-red-200
                                            @elseif($request->status == 'rejected') bg-red-800 text-red-200
                                            @else bg-slate-700 text-gray-100 @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $request->created_at->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        @if($request->due_at)
                                            {{ $request->due_at->format('M j, Y') }}
                                            @if($request->due_at < now() && in_array($request->status, ['active', 'overdue']))
                                                <span class="text-red-300 font-medium">(Overdue)</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($request->status === 'pending')
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('staff.borrow-requests.confirm', $request->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" data-confirm="Confirm this borrow request?" class="text-green-300 hover:text-green-200">Confirm</button>
                                            </form>
                                            <button type="button" class="text-red-300 hover:text-red-200" onclick="openRejectModal({{ $request->id }})">Reject</button>
                                        </div>
                                        @elseif(in_array($request->status, ['active', 'overdue']))
                                        <form method="POST" action="{{ route('staff.borrow-requests.check-in', $request->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" data-confirm="Check in this book?" class="text-blue-300 hover:text-blue-200">Check In</button>
                                        </form>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-400">No borrow requests found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($borrowRequests->hasPages())
                    <div class="mt-4">
                        {{ $borrowRequests->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-slate-800 border-slate-700 text-gray-100">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-100 mb-4">Reject Borrow Request</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-300">Rejection Reason</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" class="mt-1 block w-full border-slate-700 rounded-md shadow-sm bg-slate-900 text-gray-100" required></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-slate-700 text-gray-100 rounded-md hover:bg-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(requestId) {
            document.getElementById('rejectForm').action = `/staff/borrow-requests/${requestId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejection_reason').value = '';
        }
    </script>
</x-app-layout>