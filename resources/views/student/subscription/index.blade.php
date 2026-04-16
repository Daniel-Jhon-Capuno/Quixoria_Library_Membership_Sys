<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(isset($currentSubscription))
                        <!-- Current Subscription Display -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">Current Subscription</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tier</label>
                                    <p class="text-lg font-semibold">{{ $tier->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monthly Fee</label>
                                    <p class="text-lg">${{ number_format($tier->monthly_fee, 2) }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Borrow Limit</label>
                                    <p class="text-lg">{{ $tier->borrow_limit_per_week }} books/week</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Borrow Duration</label>
                                    <p class="text-lg">{{ $tier->borrow_duration_days }} days</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Renewal Limit</label>
                                    <p class="text-lg">{{ $tier->renewal_limit }} times</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Late Fee</label>
                                    <p class="text-lg">${{ number_format($tier->late_fee_per_day, 2) }}/day</p>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-700">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Billing Date</label>
                                        <p>{{ $currentSubscription->ends_at->format('M j, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm {{ $daysRemaining >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $renewalMessage }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex space-x-4">
                                <form method="POST" action="{{ route('student.subscription.upgrade') }}" class="inline">
                                    @csrf
                                    <select name="tier_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                                        <option value="">Select tier to upgrade to</option>
                                        @php
                                            $nextTier = \App\Models\MembershipTier::where('priority_level', $tier->priority_level + 1)->first();
                                        @endphp
                                        @if($nextTier)
                                            <option value="{{ $nextTier->id }}">{{ $nextTier->name }} - ${{ number_format($nextTier->monthly_fee, 2) }}/month</option>
                                        @endif
                                    </select>
                                    <x-primary-button type="submit" class="ml-2">Upgrade</x-primary-button>
                                </form>

                                <form method="POST" action="{{ route('student.subscription.downgrade') }}" class="inline">
                                    @csrf
                                    <select name="tier_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                                        <option value="">Select tier to downgrade to</option>
                                        @foreach(\App\Models\MembershipTier::where('priority_level', '<', $tier->priority_level)->orderBy('priority_level', 'desc')->get() as $lowerTier)
                                            <option value="{{ $lowerTier->id }}">{{ $lowerTier->name }} - ${{ number_format($lowerTier->monthly_fee, 2) }}/month</option>
                                        @endforeach
                                    </select>
                                    <x-primary-button type="submit" class="ml-2 bg-yellow-600 hover:bg-yellow-700">Downgrade</x-primary-button>
                                </form>

                                <form method="POST" action="{{ route('student.subscription.cancel') }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel your subscription? You will retain access until {{ $currentSubscription->ends_at->format('M j, Y') }}.')">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit">Cancel Subscription</x-danger-button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- No Subscription - Show Available Tiers -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Choose a Subscription Plan</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($tiers as $tier)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 {{ $loop->first ? 'ring-2 ring-indigo-500' : '' }}">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="text-xl font-bold">{{ $tier->name }}</h4>
                                            @if($loop->first)
                                                <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs px-2 py-1 rounded-full">Popular</span>
                                            @endif
                                        </div>

                                        <div class="text-3xl font-bold mb-4">${{ number_format($tier->monthly_fee, 2) }}
                                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400">/month</span>
                                        </div>

                                        <ul class="space-y-2 mb-6">
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $tier->borrow_limit_per_week }} books per week
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $tier->borrow_duration_days }} days borrow period
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Up to {{ $tier->renewal_limit }} renewals
                                            </li>
                                            @if($tier->can_reserve)
                                                <li class="flex items-center">
                                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Reservation privileges
                                                </li>
                                            @endif
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                ${{ number_format($tier->late_fee_per_day, 2) }}/day late fee
                                            </li>
                                        </ul>

                                        <form method="POST" action="{{ route('student.subscription.purchase') }}">
                                            @csrf
                                            <input type="hidden" name="tier_id" value="{{ $tier->id }}">
                                            <x-primary-button type="submit" class="w-full">
                                                Subscribe to {{ $tier->name }}
                                            </x-primary-button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>