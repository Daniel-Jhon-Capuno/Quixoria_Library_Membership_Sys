<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('My Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body text-gray-100">
                    @if(isset($currentSubscription))
                        <!-- Current Subscription Display -->
                        <div class="card mb-6">
                            <div class="card-body">
                            <h3 class="text-lg font-semibold text-gray-100 mb-4">Current Subscription</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300">Tier</label>
                                    <p class="text-lg font-semibold text-gray-100">{{ $tier->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300">Monthly Fee</label>
                                    <p class="text-lg text-gray-100">${{ number_format($tier->monthly_fee, 2) }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300">Borrow Limit</label>
                                    <p class="text-lg text-gray-100">{{ $tier->borrow_limit_per_week }} books/week</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-200">Borrow Duration</label>
                                    <p class="text-lg text-gray-100">{{ $tier->borrow_duration_days }} days</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-200">Renewal Limit</label>
                                    <p class="text-lg text-gray-100">{{ $tier->renewal_limit }} times</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-200">Late Fee</label>
                                    <p class="text-lg text-gray-100">${{ number_format($tier->late_fee_per_day, 2) }}/day</p>
                                </div>
                            </div>

                                <div class="mt-4 pt-4 border-t border-slate-700">
                                <div class="flex justify-between items-center">
                                    <div>
                                            <label class="block text-sm font-medium text-gray-300">Billing Date</label>
                                            <p class="text-gray-100">{{ $currentSubscription->ends_at->format('M j, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                            <p class="text-sm {{ $daysRemaining >= 0 ? 'text-gray-200' : 'text-red-400' }}">
                                                {{ $renewalMessage }}
                                            </p>
                                    </div>
                                </div>
                            </div>

                                <div class="mt-6 flex space-x-4">
                                <form method="POST" action="{{ route('student.subscription.upgrade') }}" class="inline">
                                    @csrf
                                    <select name="tier_id" class="rounded-md border-slate-700 bg-slate-900 text-gray-200 focus:border-cyan-400 focus:ring-cyan-400 shadow-sm" required>
                                        <option value="">Select tier to upgrade to</option>
                                        @php
                                            $nextTier = \App\Models\MembershipTier::where('priority_level', $tier->priority_level + 1)->first();
                                        @endphp
                                        @if($nextTier)
                                            <option value="{{ $nextTier->id }}">{{ $nextTier->name }} - ${{ number_format($nextTier->monthly_fee, 2) }}/month</option>
                                        @endif
                                    </select>
                                    <button type="submit" class="btn-primary ml-2">Upgrade</button>
                                </form>

                                <form method="POST" action="{{ route('student.subscription.downgrade') }}" class="inline">
                                    @csrf
                                    <select name="tier_id" class="rounded-md border-slate-700 bg-slate-900 text-gray-200 focus:border-cyan-400 focus:ring-cyan-400 shadow-sm" required>
                                        <option value="">Select tier to downgrade to</option>
                                        @foreach(\App\Models\MembershipTier::where('priority_level', '<', $tier->priority_level)->orderBy('priority_level', 'desc')->get() as $lowerTier)
                                            <option value="{{ $lowerTier->id }}">{{ $lowerTier->name }} - ${{ number_format($lowerTier->monthly_fee, 2) }}/month</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn-primary ml-2">Downgrade</button>
                                </form>

                                <form method="POST" action="{{ route('student.subscription.cancel') }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel your subscription? You will retain access until {{ $currentSubscription->ends_at->format('M j, Y') }}.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary">Cancel Subscription</button>
                                </form>
                            </div>
                            </div>
                        </div>
                    @elseif(isset($pendingSubscription))
                        <!-- Pending Subscription Display -->
                        <div class="card mb-6" style="background-color: rgba(255, 215, 0, 0.04);">
                            <div class="card-body">
                            <h3 class="text-lg font-semibold text-yellow-100 mb-4">Subscription Pending Approval</h3>
                            <p class="text-sm text-yellow-200 mb-4">Thank you for your purchase. Your subscription request is pending admin approval. Once approved, your benefits will be active.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-yellow-200">Tier</label>
                                    <p class="text-lg font-semibold text-yellow-100">{{ $pendingSubscription->membershipTier->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-yellow-200">Borrow Limit</label>
                                    <p class="text-lg text-yellow-100">{{ $pendingSubscription->membershipTier->borrow_limit_per_week }} books/week</p>
                                </div>
                                {{-- monthly allowance removed from UI per weekly-only policy --}}
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-100 mb-4">Choose a Subscription Plan</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($tiers as $tier)
                                    <div class="card" style="background-color: rgb(var(--bg-secondary)); border-color: rgba(40,100,150,0.04);">
                                        <div class="card-body">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="text-xl font-bold">{{ $tier->name }}</h4>
                                            @if($loop->first)
                                                <span class="bg-slate-700 text-gray-100 text-xs px-2 py-1 rounded-full">Popular</span>
                                            @endif
                                        </div>

                                        <div class="text-3xl font-bold text-gray-100 mb-4">${{ number_format($tier->monthly_fee, 2) }}
                                            <span class="text-sm font-normal text-gray-400">/month</span>
                                        </div>

                                        <ul class="space-y-2 mb-6 text-gray-300">
                                            <li class="flex items-center">{{ $tier->borrow_limit_per_week }} books per week</li>
                                            <li class="flex items-center">{{ $tier->borrow_duration_days }} days borrow period</li>
                                            <li class="flex items-center">Up to {{ $tier->renewal_limit }} renewals</li>
                                        </ul>

                                        <form method="POST" action="{{ route('student.subscription.purchase') }}" x-data="{confirming:false}">
                                            @csrf
                                            <input type="hidden" name="tier_id" value="{{ $tier->id }}">
                                            <button type="button" @click.prevent="confirming = true" class="btn-primary w-full">Subscribe to {{ $tier->name }}</button>

                                            <div x-show="confirming" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                <div class="bg-slate-800 rounded-lg p-6 max-w-md w-full border border-slate-700">
                                                    <h4 class="text-lg font-semibold text-gray-100 mb-4">Confirm Purchase</h4>
                                                    <p class="mb-4 text-gray-300">Are you sure you want to purchase the {{ $tier->name }} subscription for ${{ number_format($tier->monthly_fee,2) }} / month? This will be submitted for admin approval.</p>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" @click.prevent="confirming = false" class="btn-secondary">Cancel</button>
                                                        <button type="submit" class="btn-primary">Confirm Purchase</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- No Subscription - Show Available Tiers -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-100 mb-4">Choose a Subscription Plan</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($tiers as $tier)
                                    <div class="border border-slate-700 rounded-lg p-6 {{ $loop->first ? 'ring-2 ring-indigo-500' : '' }} bg-slate-800">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="text-xl font-bold">{{ $tier->name }}</h4>
                                            @if($loop->first)
                                                <span class="bg-slate-700 text-gray-100 text-xs px-2 py-1 rounded-full">Popular</span>
                                            @endif
                                        </div>

                                        <div class="text-3xl font-bold text-gray-100 mb-4">${{ number_format($tier->monthly_fee, 2) }}
                                            <span class="text-sm font-normal text-gray-400">/month</span>
                                        </div>

                                        <ul class="space-y-2 mb-6 text-gray-300">
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

                                        <form method="POST" action="{{ route('student.subscription.purchase') }}" x-data="{confirming:false}">
                                            @csrf
                                            <input type="hidden" name="tier_id" value="{{ $tier->id }}">
                                            <button type="button" @click.prevent="confirming = true" class="w-full px-4 py-2 bg-gray-700 text-white rounded-md">Subscribe to {{ $tier->name }}</button>

                                            <div x-show="confirming" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                                                    <h4 class="text-lg font-semibold mb-4">Confirm Purchase</h4>
                                                    <p class="mb-4">Are you sure you want to purchase the {{ $tier->name }} subscription for ${{ number_format($tier->monthly_fee,2) }} / month? This will be submitted for admin approval.</p>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" @click.prevent="confirming = false" class="px-4 py-2 rounded-md border">Cancel</button>
                                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Confirm Purchase</button>
                                                    </div>
                                                </div>
                                            </div>
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