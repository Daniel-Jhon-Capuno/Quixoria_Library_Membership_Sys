<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">My Borrow Requests</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Filter -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('student.borrow-requests.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-200">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-500">Filter</button>
                        </div>
                        <div>
                            <a href="{{ route('student.borrow-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-transparent rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest hover:bg-slate-600">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Borrow Requests Table -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Requested</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Borrowed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Returned</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($borrowRequests as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $request->book->title }}</div>
                                        <div class="text-sm text-gray-300">{{ $request->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($request->status == 'pending') bg-slate-700 text-gray-200
                                            @elseif($request->status == 'confirmed') bg-slate-700 text-gray-200
                                            @elseif($request->status == 'rejected') bg-red-800 text-red-200
                                            @elseif($request->status == 'active') bg-slate-700 text-gray-200
                                            @elseif($request->status == 'returned') bg-slate-700 text-gray-200
                                            @elseif($request->status == 'overdue') bg-red-800 text-red-200
                                            @else bg-slate-700 text-gray-200 @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                        @if($request->status === 'rejected' && $request->rejection_reason)
                                            <div class="text-xs text-red-600 mt-1">
                                                Reason: {{ $request->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $request->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $request->borrowed_at?->format('M j, Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        @if($request->due_at)
                                            {{ $request->due_at->format('M j, Y') }}
                                            @if($request->due_at < now() && in_array($request->status, ['active', 'overdue']))
                                                <span class="text-red-400 font-medium">(Overdue)</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $request->returned_at?->format('M j, Y') ?? '-' }}
                                        @if($request->late_fee_charged > 0)
                                            <div class="text-xs text-red-400">
                                                Late fee: ${{ number_format($request->late_fee_charged, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($request->status === 'pending')
                                        <form method="POST" action="{{ route('student.borrow-requests.destroy', $request->id) }}" class="inline"
                                              onsubmit="return confirm('Are you sure you want to cancel this borrow request?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600">Cancel Request</button>
                                        </form>
                                        @else
                                        <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        @if(request('status'))
                                            No {{ request('status') }} borrow requests found.
                                        @else
                                            You haven't submitted any borrow requests yet.
                                        @endif
                                    </td>
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

            <!-- Quick Actions -->
            <div class="mt-6">
                <a href="{{ route('student.book-catalog.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Browse Books
                </a>
            </div>
        </div>
    </div>
</x-app-layout>