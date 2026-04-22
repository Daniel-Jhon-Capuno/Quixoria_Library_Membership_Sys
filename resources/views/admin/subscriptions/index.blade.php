<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">Subscriptions</h2>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 text-gray-100 rounded-md hover:bg-gray-600">Manage Users</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-slate-800 border border-slate-700 text-green-200 rounded-md">{{ session('success') }}</div>
            @endif

            <div class="card mb-6">
                <div class="card-body">
                <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Status</label>
                        <select name="status" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary">Filter</button>
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn-secondary">Reset</a>
                    </div>
                </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tier</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Start Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">End Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-slate-700">
                        @foreach($subscriptions as $subscription)
                            <tr class="bg-slate-800">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $subscription->user->name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $subscription->membershipTier->name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($subscription->status === 'active') bg-green-800 text-green-200
                                        @elseif($subscription->status === 'expired') bg-red-800 text-red-200
                                        @elseif($subscription->status === 'cancelled') bg-slate-700 text-gray-100
                                        @else bg-yellow-800 text-yellow-200 @endif">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $subscription->starts_at?->format('M d, Y') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $subscription->ends_at?->format('M d, Y') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">${{ number_format($subscription->amount_paid, 2) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-gray-100 hover:text-gray-300">View Details</a>
                                    @if($subscription->status === 'pending')
                                        <form method="POST" action="{{ route('admin.subscriptions.confirm', $subscription) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="btn-primary ml-2">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.subscriptions.reject', $subscription) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="btn-secondary ml-2">Reject</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subscriptions->links() }}
            </div>
            </div>
        </div>
    </div>
</x-app-layout>