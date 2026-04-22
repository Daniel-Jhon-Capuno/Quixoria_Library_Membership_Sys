<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            Membership Tiers
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between">
                <a href="{{ route('admin.tiers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-gray-100 hover:bg-gray-600">Add Tier</a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-slate-800 border border-slate-700 text-green-200 rounded-md">{{ session('success') }}</div>
            @endif

            <div class="bg-slate-800 shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Fee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Borrow Limit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Reserve</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-slate-700">
                        @foreach($tiers as $tier)
                            <tr class="bg-slate-800">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $tier->name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">${{ number_format($tier->monthly_fee, 2) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $tier->borrow_limit_per_week }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $tier->borrow_duration_days }} days</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $tier->can_reserve ? 'Yes' : 'No' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $tier->priority_level }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.tiers.edit', $tier) }}" class="text-gray-100 hover:text-gray-300">Edit</a>
                                    <form action="{{ route('admin.tiers.destroy', $tier) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this tier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
