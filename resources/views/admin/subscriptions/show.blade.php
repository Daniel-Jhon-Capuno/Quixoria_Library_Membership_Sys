<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Subscription Details</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-md">{{ session('success') }}</div>
            @endif

            <!-- Subscription Details -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Subscription Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subscription->user->name }} ({{ $subscription->user->email }})</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Membership Tier</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subscription->membershipTier->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($subscription->status === 'active') bg-green-100 text-green-800
                            @elseif($subscription->status === 'expired') bg-red-100 text-red-800
                            @elseif($subscription->status === 'cancelled') bg-gray-100 text-gray-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount Paid</label>
                        <p class="mt-1 text-sm text-gray-900">${{ number_format($subscription->amount_paid, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subscription->starts_at?->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subscription->ends_at?->format('M d, Y H:i') }}</p>
                    </div>
                    @if($subscription->cancelled_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cancelled At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subscription->cancelled_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                    @if($subscription->cancelled_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cancellation Reason</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subscription->rejection_reason ?: 'N/A' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Transaction History -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($transaction->type === 'payment') bg-green-100 text-green-800
                                            @elseif($transaction->type === 'refund') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($transaction->amount, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $transaction->reference_note ?: 'N/A' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->processor->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Actions</h3>

                <!-- Manual Adjustment Form -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-2">Record Manual Adjustment</h4>
                    <form method="POST" action="{{ route('admin.subscriptions.adjust', $subscription) }}" class="grid gap-4 sm:grid-cols-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="refund">Refund</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount</label>
                            <input type="number" name="amount" step="0.01" min="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reason</label>
                            <input type="text" name="reference_note" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Record Adjustment</button>
                        </div>
                    </form>
                </div>

                <!-- Override Tier Form -->
                <div class="mb-6 border-t pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-2">Override Membership Tier</h4>
                    <form method="POST" action="{{ route('admin.subscriptions.override', $subscription->user) }}" class="grid gap-4 sm:grid-cols-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">New Tier</label>
                            <select name="membership_tier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select Tier</option>
                                @foreach(\App\Models\MembershipTier::orderBy('priority_level')->get() as $tier)
                                    <option value="{{ $tier->id }}">{{ $tier->name }} (${{ $tier->monthly_fee }}/mo)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="starts_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="ends_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount Paid</label>
                            <input type="number" name="amount_paid" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Override Tier</button>
                        </div>
                    </form>
                </div>

                <div class="flex items-center">
                    <a href="{{ route('admin.subscriptions.index') }}" class="text-gray-600 hover:text-gray-900">Back to Subscriptions</a>
                </div>
                <div class="mt-4 border-t pt-4">
                    <h4 class="text-md font-medium text-gray-900 mb-2">Quick Fix: Activate / Extend Subscription</h4>
                    <form method="POST" action="{{ route('admin.subscriptions.quick-fix', $subscription->user) }}" class="flex items-center space-x-2">
                        @csrf
                        <label class="text-sm text-gray-700">Extend days</label>
                        <input type="number" name="extend_days" value="30" min="1" class="w-24 mt-1 block border-gray-300 rounded-md shadow-sm" />
                        <button type="button" data-admin-confirm="Activate or extend subscription for {{ addslashes($subscription->user->name) }}?" class="px-3 py-1 bg-indigo-600 text-white rounded-md">Quick Fix</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>