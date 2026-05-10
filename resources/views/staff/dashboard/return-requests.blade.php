    <div class="card mb-8">
        <div class="flex items-center gap-3 mb-6">

        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm3-3a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd" />
        </svg>
        <h3 class="font-semibold text-lg text-gray-100">Return Requests</h3>
        @if($returnRequests->count() > 0)
            <span class="ml-auto bg-yellow-900/30 text-yellow-300 px-2 py-1 rounded text-xs font-medium">{{ $returnRequests->count() }} pending</span>
        @endif
    </div>
    @if($returnRequests->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-700">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Member</th>

                        <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Book</th>
                        <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Requested</th>
                        <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returnRequests as $request)
                        <tr class="border-b border-slate-700 hover:bg-slate-700/50 transition text-gray-100">
                            <td class="py-3 px-4 font-medium">{{ $request->student->name }}</td>
                            <td class="py-3 px-4 text-gray-400">{{ $request->book->title }}</td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $request->updated_at->format('M d, H:i') }}</td>
                            <td class="py-3 px-4 flex gap-2">
                                <button onclick="openRejectReturnModal({{ $request->id }})" class="text-red-400 hover:text-red-500 transition text-sm font-medium px-3 py-1 bg-red-900/20 rounded-lg hover:bg-red-900/40">Reject Return</button>
                                <form method="POST" action="{{ route('staff.borrow-requests.check-in', $request->id) }}" class="inline" data-confirm="Mark as returned and check for reservations?">
                                    @csrf
                                    <button type="submit" class="text-green-400 hover:text-green-500 transition text-sm font-medium px-3 py-1 bg-green-900/20 rounded-lg hover:bg-green-900/40">Check In</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-700">
            <a href="{{ route('staff.borrow-requests.index', ['status' => 'return_requested']) }}" class="text-teal-glow hover:text-teal-glow/80 font-medium text-sm">View All Return Requests →</a>
        </div>
    @else
        <div class="text-center py-12 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <h4 class="text-lg font-semibold text-gray-300 mb-2">No Return Requests</h4>
            <p class="text-sm">Students haven't requested any returns yet.</p>
        </div>
    @endif
</div>
