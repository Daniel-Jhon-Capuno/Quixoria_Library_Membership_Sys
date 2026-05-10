<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('My Subscription') }}
        </h2>
    </x-slot>

    <div class="py-12 student-subscription">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-8 p-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-8 p-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($tiers as $tier)
                    @php
                        $isCurrent = $currentTierId == $tier->id;
                        $isPending = isset($pendingSubscription) && $pendingSubscription->membership_tier_id == $tier->id;
                    @endphp
                    <div class="subscription-card group relative bg-gradient-to-b from-slate-800 via-slate-800/90 to-slate-900/80 backdrop-blur-xl rounded-2xl p-6 sm:p-8 border border-slate-700 transition-all duration-500 h-full flex flex-col justify-between shadow-xl
                        {{ $isCurrent ? 'ring-2 ring-cyan-500 ring-opacity-100 shadow-2xl shadow-cyan-500/40 scale-105 animate-pulse-glow' : 'hover:border-slate-600 hover:shadow-lg hover:shadow-slate-500/10' }}">
                        
                        {{-- Animated Background Gradient for Current Tier --}}
                        @if($isCurrent)
                            <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-transparent rounded-2xl animate-gradient"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-700/0 to-transparent rounded-2xl group-hover:from-slate-700/10 transition-all duration-500"></div>
                        @endif

                        <div class="relative z-10">
                            {{-- Badges --}}
                            <div class="flex gap-2 mb-6">
                                @if($isCurrent)
                                    <span class="bg-emerald-500/30 text-emerald-300 border border-emerald-500/60 px-4 py-2 rounded-full text-sm font-bold animate-bounce-subtle shadow-lg shadow-emerald-500/20">✨ Current Plan</span>
                                @elseif($isPending)
                                    <span class="bg-amber-500/30 text-amber-300 border border-amber-500/60 px-4 py-2 rounded-full text-sm font-bold animate-pulse shadow-lg shadow-amber-500/20">⏳ Pending Approval</span>
                                @endif
                                @if($loop->first && !$isCurrent)
                                    <span class="ml-auto bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-2 rounded-full text-xs font-bold shadow-md animate-shimmer">⭐ Popular</span>
                                @endif
                            </div>

                            {{-- Card Content --}}
                            <h4 class="text-xl sm:text-2xl font-bold text-white mb-4 {{ $isCurrent ? 'text-cyan-300' : 'group-hover:text-cyan-300' }} transition-colors">{{ $tier->name }}</h4>

                            @if($tier->monthly_fee == 0)
                                <div class="text-3xl sm:text-4xl font-black text-white mb-2 drop-shadow-lg">Free</div>
                                <p class="text-slate-400 text-base sm:text-lg font-medium mb-8">Unlimited access</p>
                            @else
                                <div class="text-3xl sm:text-4xl font-black text-white mb-2 drop-shadow-lg">
                                    ${{ number_format($tier->monthly_fee, 2) }}
                                </div>
                                <p class="text-slate-400 text-base sm:text-lg font-medium mb-8">per month</p>
                            @endif

                            <div class="border-t border-slate-700 pt-6 mb-8"></div>

                            <ul class="space-y-4 mb-8 text-slate-300">
                                <li class="flex items-center {{ $isCurrent ? 'text-cyan-300' : '' }}">
                                    <svg class="w-6 h-6 text-emerald-400 flex-shrink-0 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $tier->borrow_limit_per_week }} books / week</span>
                                </li>
                                <li class="flex items-center {{ $isCurrent ? 'text-cyan-300' : '' }}">
                                    <svg class="w-6 h-6 text-emerald-400 flex-shrink-0 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $tier->borrow_duration_days }} days borrow</span>
                                </li>
                                <li class="flex items-center {{ $isCurrent ? 'text-cyan-300' : '' }}">
                                    <svg class="w-6 h-6 text-emerald-400 flex-shrink-0 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $tier->renewal_limit }} renewals</span>
                                </li>
                                <li class="flex items-center {{ $isCurrent ? 'text-cyan-300' : '' }}">
                                    <svg class="w-6 h-6 text-amber-400 flex-shrink-0 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>${{ number_format($tier->late_fee_per_day, 2) }}/day late fee</span>
                                </li>
                            </ul>
                        </div>

                        {{-- CTA --}}
                        <div class="relative z-10 space-y-3">
                                @if($isCurrent && $currentSubscription)
                                <button disabled class="w-full px-4 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold rounded-xl shadow-xl cursor-not-allowed text-sm sm:text-lg">
                                    ✨ Current Plan
                                    @if($daysRemaining !== null)
                                        <div class="text-sm mt-1 font-normal opacity-90">
                                            {{ $renewalMessage }}
                                        </div>
                                    @endif
                                </button>
                                @php $expiryText = ($currentSubscription && $currentSubscription->ends_at) ? $currentSubscription->ends_at->format('M j, Y') : 'no expiration'; @endphp
                                <form method="POST" action="{{ route('student.subscription.cancel') }}" class="mt-2" data-confirm="Cancel? Access until {{ $expiryText }}." style="opacity: 0.8;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-xl transition-all shadow-lg text-sm">
                                        Cancel Subscription
                                    </button>
                                </form>
                            @elseif($isPending)
                                <button disabled class="w-full px-4 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-amber-600 to-amber-700 text-white font-bold rounded-xl shadow-xl cursor-not-allowed text-sm sm:text-lg">
                                    ⏳ Pending Approval
                                </button>
                                @else
                                @php
                                    $isDowngrade = $currentTierId && $currentSubscription && $currentSubscription->membershipTier->monthly_fee > $tier->monthly_fee;
                                    $buttonText = $isDowngrade ? 'Return to ' . $tier->name : ($currentTierId ? 'Upgrade to ' . $tier->name : 'Purchase ' . $tier->name);
                                @endphp
                                <a href="{{ route('student.payment.show', $tier->id) }}" class="confirm-subscription-link w-full block px-4 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-xl shadow-xl hover:shadow-2xl hover:shadow-cyan-500/30 transform hover:scale-[1.02] transition-all duration-300 text-sm sm:text-lg text-center group-hover:from-cyan-400 group-hover:to-blue-500" data-confirm="Purchase '{{ $tier->name }}'? Purchasing this tier replaces your previous subscription and no refund will be provided. Continue?">
                                    {{ $buttonText }}
                                </a>
                            @endif
                            @if($tier->monthly_fee == 0)
                                <form method="POST" action="{{ route('student.subscription.purchase') }}" class="confirm-subscription" data-confirm="Switch to '{{ $tier->name }}' (Free)? Purchasing this tier replaces your previous subscription and no refund will be provided. Continue?">
                                    @csrf
                                    <input type="hidden" name="tier_id" value="{{ $tier->id }}">
                                    <button type="submit" class="w-full mt-3 px-4 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold rounded-xl shadow-xl hover:shadow-2xl transform hover:scale-[1.02] transition-all duration-300 text-sm sm:text-lg">
                                        {{ $currentTierId == $tier->id ? 'Current Plan' : 'Switch to Free' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(6, 182, 212, 0.4), 0 0 60px rgba(6, 182, 212, 0.2); }
            50% { box-shadow: 0 0 30px rgba(6, 182, 212, 0.6), 0 0 80px rgba(6, 182, 212, 0.3); }
        }

        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .animate-pulse-glow {
            animation: pulse-glow 3s ease-in-out infinite;
        }

        .animate-bounce-subtle {
            animation: bounce-subtle 2s ease-in-out infinite;
        }

        .animate-shimmer {
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 8s ease infinite;
        }
    </style>

    

</x-app-layout>

