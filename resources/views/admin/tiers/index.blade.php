<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Membership Tiers
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between">
                <a href="{{ route('admin.tiers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">Add Tier</a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-md">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Limit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserve</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tiers as $tier)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tier->name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($tier->monthly_fee, 2) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tier->borrow_limit_per_week }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tier->borrow_duration_days }} days</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tier->can_reserve ? 'Yes' : 'No' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tier->priority_level }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.tiers.edit', $tier) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form action="{{ route('admin.tiers.destroy', $tier) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this tier?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
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
