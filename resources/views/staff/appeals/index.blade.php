<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Return Appeals</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Appeal Reason</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Scheduled Visit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($appeals as $appeal)
                                <tr class="bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $appeal->student->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $appeal->student->email }}</div>
                                        @if($appeal->student->is_restricted)
                                            <span class="px-2 py-0.5 text-xs bg-red-800 text-red-200 rounded-full">Restricted</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $appeal->book->title }}</div>
                                        <div class="text-sm text-gray-400">{{ $appeal->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs">
                                        <div class="text-sm text-gray-300">{{ $appeal->appeal_reason ?? '—' }}</div>
                                        @if($appeal->rejection_reason)
                                            <div class="text-xs text-red-400 mt-1">Rejected: {{ $appeal->rejection_reason }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($appeal->appeal_scheduled_at)
                                            <div class="text-sm font-medium text-gray-100">
                                                {{ \Carbon\Carbon::parse($appeal->appeal_scheduled_at)->format('M j, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ \Carbon\Carbon::parse($appeal->appeal_scheduled_at)->format('g:i A') }}
                                            </div>
                                            @if(\Carbon\Carbon::parse($appeal->appeal_scheduled_at)->isPast())
                                                <span class="text-xs text-red-400 font-medium">Overdue</span>
                                            @endif
                                        @else
                                            <span class="text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($appeal->status === 'appeal_scheduled') bg-blue-800 text-blue-200
                                            @elseif($appeal->status === 'appeal_rescheduled') bg-yellow-800 text-yellow-200
                                            @elseif($appeal->status === 'appeal_no_show') bg-orange-800 text-orange-200
                                            @elseif($appeal->status === 'appeal_failed') bg-red-800 text-red-200
                                            @elseif($appeal->status === 'appeal_completed') bg-green-800 text-green-200
                                            @else bg-slate-700 text-gray-100 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $appeal->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if(in_array($appeal->status, ['appeal_scheduled', 'appeal_rescheduled']))
                                            <div class="flex space-x-2">
                                                <form method="POST" action="{{ route('staff.appeals.complete', $appeal->id) }}">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Mark this appeal as completed? Book will be marked as returned.')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded transition-colors">
                                                        ✅ Completed
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('staff.appeals.no-show', $appeal->id) }}">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Mark as no show? {{ $appeal->status === 'appeal_rescheduled' ? 'This will RESTRICT the student account.' : 'Student will be allowed to reschedule once.' }}')" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition-colors">
                                                        ❌ No Show
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($appeal->status === 'appeal_no_show')
                                            <span class="text-orange-400 text-xs">Awaiting reschedule</span>
                                        @elseif($appeal->status === 'appeal_failed')
                                            <span class="text-red-400 text-xs">Account restricted</span>
                                        @else
                                            <span class="text-gray-400 text-xs">No action</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-400">No appeals found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($appeals->hasPages())
                    <div class="mt-4">
                        {{ $appeals->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

