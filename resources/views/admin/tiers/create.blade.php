<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Membership Tier</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.tiers.store') }}">
                    @csrf

                    <div class="grid gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Monthly Fee</label>
                            <input type="number" name="monthly_fee" value="{{ old('monthly_fee') }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('monthly_fee')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Borrow Limit / Week</label>
                                <input type="number" name="borrow_limit_per_week" value="{{ old('borrow_limit_per_week') }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @error('borrow_limit_per_week')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Borrow Duration (days)</label>
                                <input type="number" name="borrow_duration_days" value="{{ old('borrow_duration_days') }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @error('borrow_duration_days')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Renewal Limit</label>
                                <input type="number" name="renewal_limit" value="{{ old('renewal_limit') }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @error('renewal_limit')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Late Fee / Day</label>
                                <input type="number" name="late_fee_per_day" value="{{ old('late_fee_per_day') }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @error('late_fee_per_day')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority Level</label>
                                <input type="number" name="priority_level" value="{{ old('priority_level') }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @error('priority_level')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex items-center mt-6">
                                <label class="inline-flex items-center text-sm text-gray-700">
                                    <input type="checkbox" name="can_reserve" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('can_reserve') ? 'checked' : '' }}>
                                    <span class="ml-2">Can reserve</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('admin.tiers.index') }}" class="text-gray-600 hover:text-gray-900">Back</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create Tier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
