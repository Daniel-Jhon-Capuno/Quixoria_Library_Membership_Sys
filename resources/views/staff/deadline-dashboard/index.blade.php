<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Deadline Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Overdue Section -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-red-700/40">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-100">Overdue Books ({{ $overdue->count() }})</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-800 text-red-200">
                            Urgent
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Days Overdue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($overdue as $borrow)
                                <tr class="bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $borrow->student->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $borrow->student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $borrow->book->title }}</div>
                                        <div class="text-sm text-gray-400">{{ $borrow->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->due_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-red-300 font-medium">{{ now()->diffInDays($borrow->due_at) }} days</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('staff.deadline-dashboard.ping', $borrow->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-100 hover:text-gray-300 mr-2">Send Reminder</button>
                                        </form>
                                        <a href="{{ route('staff.students.show', $borrow->student) }}" class="text-gray-100 hover:text-gray-300">View Student</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-400">No overdue books</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Due Today Section -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-100">Due Today ({{ $dueToday->count() }})</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-800 text-yellow-200">
                            Today
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($dueToday as $borrow)
                                <tr class="bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $borrow->student->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $borrow->student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $borrow->book->title }}</div>
                                        <div class="text-sm text-gray-400">{{ $borrow->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->due_at->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('staff.deadline-dashboard.ping', $borrow->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-100 hover:text-gray-300 mr-2">Send Reminder</button>
                                        </form>
                                        <a href="{{ route('staff.students.show', $borrow->student) }}" class="text-gray-100 hover:text-gray-300">View Student</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-400">No books due today</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Due in 3 Days Section -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-100">Due in Next 3 Days ({{ $due3Days->count() }})</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-800 text-orange-200">
                            Soon
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Left</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($due3Days as $borrow)
                                <tr class="bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $borrow->student->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $borrow->student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $borrow->book->title }}</div>
                                        <div class="text-sm text-gray-400">{{ $borrow->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->due_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ now()->diffInDays($borrow->due_at) }} days
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('staff.deadline-dashboard.ping', $borrow->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-100 hover:text-gray-300 mr-2">Send Reminder</button>
                                        </form>
                                        <a href="{{ route('staff.students.show', $borrow->student) }}" class="text-gray-100 hover:text-gray-300">View Student</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No books due in the next 3 days</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Due This Week Section -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-700">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-100">Due This Week ({{ $dueThisWeek->count() }})</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-800 text-blue-200">
                            This Week
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Book</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Days Left</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($dueThisWeek as $borrow)
                                <tr class="bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $borrow->student->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $borrow->student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-100">{{ $borrow->book->title }}</div>
                                        <div class="text-sm text-gray-400">{{ $borrow->book->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->due_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ now()->diffInDays($borrow->due_at) }} days
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form method="POST" action="{{ route('staff.deadline-dashboard.ping', $borrow->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-100 hover:text-gray-300 mr-2">Send Reminder</button>
                                        </form>
                                        <a href="{{ route('staff.students.show', $borrow->student) }}" class="text-gray-100 hover:text-gray-300">View Student</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-400">No books due this week</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>