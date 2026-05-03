<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('My Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($tiers as $tier)
                    <div class="subscription-card group relative bg-gradient-to-b from-slate-800 via-slate-800/90 to-slate-900/80 backdrop-blur-xl rounded-2xl p-8 border border-slate-700 hover:border-cyan-400/60 transition-all duration-200 hover:shadow-2xl hover:shadow-cyan-500/20 hover:-translate-y-2 hover:scale-[1.02] h-full flex flex-col justify-between shadow-xl {{ $loop->first ? 'ring-2 ring-cyan-500/50 shadow-2xl shadow-cyan-500/30' : '' }}">
                        {{-- Badges --}}
                        <div class="flex gap-2 mb-6 absolute top-6 left-6 right-6">
                            @if($currentTierId == $tier->id)
                                <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 px-3 py-1 rounded-full text-sm font-bold">Current Plan</span>
                            @elseif(isset($pendingSubscription) && $pendingSubscription->membership_tier_id == $tier->id)
                                <span class="bg-amber-500/20 text-amber-400 border border-amber-500/40 px-3 py-1 rounded-full text-sm font-bold">Pending Approval</span>
                            @endif
                            @if($loop->first && $currentTierId != $tier->id)
                                <span class="ml-auto bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">Most Popular</span>
                            @endif
                        </div>

                        {{-- Card Content --}}
                        <div class="flex flex-col flex-1">
                            <h4 class="text-2xl font-bold text-white mb-2 mt-12 group-hover:text-cyan-300 transition-colors">{{ $tier->name }}</h4>

                            <div class="text-4xl font-black text-white mb-1 mt-2 drop-shadow-lg">
                                ${{ number_format($tier->monthly_fee, 2) }}
                            </div>
                            <p class="text-slate-400 text-lg font-medium mb-8">per month</p>

                            <div class="border-t border-slate-700 pt-6 mb-8"></div>

                            <ul class="space-y-4 mb-8 text-slate-300 group-hover:text-slate-200 transition-colors">
                                <li class="flex items-center">
                                    <svg class="w-6 h-6 text-emerald-400 flex-shrink-0 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $tier->borrow_limit_per_week }} books / week</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-6 h-6 text-emerald-400 flex-shrink-0 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $tier->borrow_duration_days }} days borrow</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-6 h-6 text-emerald-400 flex-shrink-0 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $tier->renewal_limit }} renewals</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-6 h-6 text-amber-400 flex-shrink-0 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>${{ number_format($tier->late_fee_per_day, 2) }}/day late fee</span>
                                </li>
                            </ul>
                        </div>

                        {{-- CTA --}}
                        @if($currentTierId == $tier->id && $currentSubscription)
                            <button disabled class="w-full px-6 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold rounded-xl shadow-xl cursor-not-allowed opacity-75 text-lg">
                                Current Plan
                                @if($daysRemaining !== null)
                                    <div class="text-sm mt-1 font-normal opacity-90">
                                        {{ $renewalMessage }}
                                    </div>
                                @endif
                            </button>
                            <form method="POST" action="{{ route('student.subscription.cancel') }}" class="mt-4" onsubmit="return confirm('Cancel? Access until {{ $currentSubscription->ends_at->format('M j, Y') }}.')" style="opacity: 0.7;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-xl transition-all shadow-lg text-sm">
                                    Cancel Subscription
                                </button>
                            </form>
                        @elseif(isset($pendingSubscription) && $pendingSubscription->membership_tier_id == $tier->id)
                            <button disabled class="w-full px-6 py-4 bg-gradient-to-r from-amber-600 to-amber-700 text-white font-bold rounded-xl shadow-xl cursor-not-allowed opacity-75 text-lg">
                                Pending Approval
                            </button>
                        @else
                            <form method="POST" action="{{ route('student.subscription.purchase') }}" x-data="{confirming:false}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="tier_id" value="{{ $tier->id }}">
                                <button type="button" @click.prevent="confirming = true" class="w-full group-hover:bg-gradient-to-r group-hover:from-cyan-500 group-hover:to-blue-600 px-8 py-4 bg-gradient-to-r from-slate-700 to-slate-800 text-white font-black rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-cyan-500/25 transform hover:scale-[1.05] transition-all duration-300 text-lg">
                                    {{ $currentTierId ? 'Upgrade' : 'Subscribe' }} to {{ $tier->name }}
                                </button>

                                <div x-show="confirming" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm">
                                    <div class="bg-slate-800/95 backdrop-blur-xl rounded-2xl p-8 max-w-md w-full border border-slate-700 shadow-2xl">
                                        <h4 class="text-2xl font-bold text-white mb-6">Confirm {{ $currentTierId ? 'Upgrade' : 'Purchase' }}</h4>
                                        <p class="mb-8 text-slate-300 text-lg leading-relaxed">Ready for {{ $tier->name }}? ${{ number_format($tier->monthly_fee,2) }}/month (admin approval required).</p>
                                        <div class="flex justify-end space-x-4">
                                            <button type="button" @click.prevent="confirming = false" class="px-8 py-3 bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold rounded-xl transition-all">Cancel</button>
                                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black rounded-xl shadow-xl hover:shadow-2xl hover:scale-[1.05] transition-all">Confirm Purchase</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

