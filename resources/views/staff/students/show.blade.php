<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Student Profile: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Student Information -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Student Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Name</label>
                            <p class="mt-1 text-sm text-gray-100">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Email</label>
                            <p class="mt-1 text-sm text-gray-100">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Status</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->is_active ? 'bg-green-800 text-green-200' : 'bg-red-800 text-red-200' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Membership Tier</label>
                            <p class="mt-1 text-sm text-gray-100">
                                {{ $user->subscription?->membershipTier?->name ?? 'No Active Subscription' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Subscription Expiry</label>
                            <p class="mt-1 text-sm text-gray-100">
                                @if($user->subscription)
                                    {{ $user->subscription->expires_at->format('M j, Y') }}
                                    @if($user->subscription->expires_at < now())
                                        <span class="text-red-300 font-medium">(Expired)</span>
                                    @endif
                                @else
                                    No active subscription
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Overdue Count</label>
                            <p class="mt-1 text-sm text-gray-100">
                                <span class="font-medium {{ $overdueCount > 0 ? 'text-red-300' : 'text-green-300' }}">
                                    {{ $overdueCount }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Borrows -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Active Borrows ({{ $activeBorrows->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Book Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Author</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Borrowed Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($activeBorrows as $borrow)
                                <tr class="bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-100">
                                        {{ $borrow->book->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->book->author }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->borrowed_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->due_at->format('M j, Y') }}
                                        @if($borrow->due_at < now())
                                            <span class="text-red-300 font-medium">(Overdue)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($borrow->status == 'active') bg-green-800 text-green-200
                                            @elseif($borrow->status == 'overdue') bg-red-800 text-red-200
                                            @else bg-slate-700 text-gray-100 @endif">
                                            {{ ucfirst($borrow->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-400">No active borrows</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Borrow History -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg border border-slate-700">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Borrow History ({{ $borrowHistory->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Book Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Author</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Borrowed Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Returned Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Late Fee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($borrowHistory as $borrow)
                                <tr class="bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-100">
                                        {{ $borrow->book->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->book->author }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->borrowed_at?->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $borrow->returned_at?->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        @if($borrow->late_fee_charged > 0)
                                            <span class="text-red-300">${{ number_format($borrow->late_fee_charged, 2) }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($borrow->status == 'returned') bg-slate-700 text-gray-100
                                            @elseif($borrow->status == 'overdue') bg-red-800 text-red-200
                                            @else bg-slate-700 text-gray-100 @endif">
                                            {{ ucfirst($borrow->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-400">No borrow history</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="flex justify-start">
                <a href="{{ route('staff.deadline-dashboard.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-transparent rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest hover:bg-slate-600">
                    Back to Deadline Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>