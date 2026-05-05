<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Return Request Appeals</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Appeals Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Appeal Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Appeal Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Scheduled</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($appeals as $request)
                                <tr class="hover:bg-slate-700/50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-100">{{ $request->student->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $request->student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-100">{{ $request->book->title }}</div>
                                        <div class="text-sm text-gray-400">{{ $request->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                            @if($request->appeal_status == 'pending') bg-yellow-800 text-yellow-200
                                            @elseif($request->appeal_status == 'scheduled') bg-orange-800 text-orange-200
                                            @else bg-slate-700 text-gray-100 @endif">
                                            {{ ucfirst($request->appeal_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-400">
                                        {{ $request->updated_at->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-400">
                                        @if($request->appeal_scheduled_at)
                                            {{ $request->appeal_scheduled_at->format('M j, Y g:i A') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($request->appeal_status == 'pending')
                                            <div class="flex space-x-2">
                                                <form method="POST" action="{{ route('staff.borrow-requests.appeal.schedule', $request) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded transition-colors" data-confirm="Schedule appeal meeting? Student will be notified.">Schedule Meeting</button>
                                                </form>
                                                <form method="POST" action="{{ route('staff.borrow-requests.appeal.deny', $request) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition-colors" data-confirm="Deny this appeal? Status will be set to denied.">Deny Appeal</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">No action needed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-400">No pending appeals</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-confirm]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm(this.dataset.confirm)) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
