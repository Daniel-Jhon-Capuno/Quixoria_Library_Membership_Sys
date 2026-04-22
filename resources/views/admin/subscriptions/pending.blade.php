<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">Pending Subscriptions</h2>
            <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-gray-700 text-gray-100 rounded-md">All Subscriptions</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-slate-800 border border-slate-700 text-green-200 rounded-md">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.subscriptions.bulk-confirm') }}">
                @csrf
                <div class="mb-4">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">Approve Selected</button>
                    <button formaction="{{ route('admin.subscriptions.bulk-reject') }}" formmethod="POST" type="submit" class="ml-2 px-4 py-2 bg-red-600 text-white rounded-md">Reject Selected</button>
                </div>

                <div class="bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead class="bg-slate-800">
                            <tr>
                                <th class="px-4 py-3">
                                    <input type="checkbox" id="select_all" class="form-checkbox" />
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tier</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Requested At</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-transparent divide-y divide-slate-700">
                            @foreach($subscriptions as $subscription)
                                <tr class="bg-slate-800">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="ids[]" value="{{ $subscription->id }}" class="select_item form-checkbox" />
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $subscription->user->name }}<br /><span class="text-xs text-gray-400">{{ $subscription->user->email }}</span></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $subscription->membershipTier->name }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $subscription->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-gray-100 hover:text-gray-300">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $subscriptions->links() }}
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('select_all');
            const items = document.querySelectorAll('.select_item');
            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    items.forEach(i => i.checked = selectAll.checked);
                });
            }
        });
    </script>
</x-app-layout>
