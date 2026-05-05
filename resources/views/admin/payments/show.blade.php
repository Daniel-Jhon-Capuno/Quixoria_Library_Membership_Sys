<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Payment Details</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="bg-emerald-800/40 border border-emerald-600/50 rounded-xl p-4 mb-6 text-emerald-300">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-gradient-to-b from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700 shadow-xl">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-white">Payment #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</h3>
                        <p class="text-slate-400">{{ $payment->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <span class="px-4 py-2 text-sm font-bold rounded-full
                        @if($payment->status === 'pending') bg-yellow-800 text-yellow-200
                        @elseif($payment->status === 'confirmed') bg-green-800 text-green-200
                        @else bg-red-800 text-red-200 @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <div class="text-xs text-slate-500 uppercase mb-1">Student</div>
                        <div class="text-white font-semibold">{{ $payment->user->name }}</div>
                        <div class="text-slate-400 text-sm">{{ $payment->user->email }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase mb-1">Plan</div>
                        <div class="text-white font-semibold">{{ $payment->membershipTier->name }}</div>
                        <div class="text-cyan-400 font-bold">₱{{ number_format($payment->amount, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase mb-1">Full Name</div>
                        <div class="text-white">{{ $payment->full_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase mb-1">Phone</div>
                        <div class="text-white">{{ $payment->phone }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase mb-1">Address</div>
                        <div class="text-white">{{ $payment->address }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase mb-1">Payment Method</div>
                        <div class="text-white">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase mb-1">Reference Number</div>
                        <div class="text-white font-mono">{{ $payment->reference_number }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase mb-1">Type</div>
                        <div class="text-white">{{ ucfirst($payment->type) }}</div>
                    </div>
                </div>

                @if($payment->proof_of_payment)
                <div class="mb-8">
                    <div class="text-xs text-slate-500 uppercase mb-3">Proof of Payment</div>
                    <img src="{{ asset('storage/' . $payment->proof_of_payment) }}"
                         alt="Proof of Payment"
                         class="max-w-full rounded-xl border border-slate-700 shadow-lg">
                </div>
                @endif

                @if($payment->rejection_reason)
                <div class="bg-red-900/40 border border-red-600/50 rounded-xl p-4 mb-6">
                    <div class="text-xs text-red-400 uppercase mb-1">Rejection Reason</div>
                    <div class="text-red-300">{{ $payment->rejection_reason }}</div>
                </div>
                @endif

                @if($payment->status === 'pending')
                <div class="flex gap-4 mt-6">
                    <form method="POST" action="{{ route('admin.payments.confirm', $payment->id) }}" class="flex-1">
                        @csrf
                        <button type="submit" onclick="return confirm('Confirm this payment and activate the subscription?')"
                            class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-2xl transition-all">
                            ✅ Confirm Payment
                        </button>
                    </form>

                    <div class="flex-1">
                        <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')"
                            class="w-full px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-2xl transition-all">
                            ❌ Reject Payment
                        </button>
                        <form id="rejectForm" method="POST" action="{{ route('admin.payments.reject', $payment->id) }}" class="hidden mt-3 space-y-3">
                            @csrf
                            <textarea name="rejection_reason" rows="3" required placeholder="Enter rejection reason..."
                                class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl px-4 py-3 text-sm"></textarea>
                            <button type="submit" class="w-full px-4 py-3 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-xl transition-all">
                                Submit Rejection
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <div class="mt-6">
                    <div class="flex items-center gap-4">
                        @if($payment->subscription_id)
                            <a href="{{ route('admin.subscriptions.show', $payment->subscription_id) }}" class="text-cyan-300 hover:text-white font-medium">
                                🔎 View Subscription
                            </a>
                        @endif
                        <a href="{{ route('admin.payments.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">
                            ← Back to Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
