<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Payment Receipt</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <!-- Success Banner -->
            <div class="bg-emerald-800/40 border border-emerald-600/50 rounded-2xl p-6 mb-8 text-center">
                <div class="text-4xl mb-3">✅</div>
                <h3 class="text-xl font-bold text-emerald-300 mb-2">Payment Submitted Successfully!</h3>
                <p class="text-emerald-400">Your payment is under review. Admin will verify and activate your subscription shortly.</p>
            </div>

            <!-- Receipt Card -->
            <div class="bg-gradient-to-b from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700 shadow-xl">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-white">Quixoria Library</h3>
                        <p class="text-slate-400 text-sm">Ground Floor, City Library and Information Center building</p>
                        <p class="text-slate-400 text-sm">203 C. Bangoy St, Poblacion District, Davao City</p>
                    </div>
                    <div class="text-right">
                        <div class="text-slate-400 text-sm">Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-slate-400 text-sm">{{ $payment->created_at->format('M j, Y g:i A') }}</div>
                        <span class="px-3 py-1 text-xs font-bold rounded-full
                            @if($payment->status === 'pending') bg-yellow-800 text-yellow-200
                            @elseif($payment->status === 'confirmed') bg-green-800 text-green-200
                            @else bg-red-800 text-red-200 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-slate-700 pt-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Plan</span>
                        <span class="text-white font-semibold">{{ $payment->membershipTier->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Type</span>
                        <span class="text-white font-semibold">{{ ucfirst($payment->type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Full Name</span>
                        <span class="text-white font-semibold">{{ $payment->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email</span>
                        <span class="text-white font-semibold">{{ $payment->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Phone</span>
                        <span class="text-white font-semibold">{{ $payment->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Address</span>
                        <span class="text-white font-semibold">{{ $payment->address }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Payment Method</span>
                        <span class="text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Reference Number</span>
                        <span class="text-white font-semibold">{{ $payment->reference_number }}</span>
                    </div>
                    @if($payment->proof_of_payment)
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Proof of Payment</span>
                        <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" target="_blank" class="text-cyan-400 hover:text-cyan-300 text-sm underline">View Screenshot</a>
                    </div>
                    @endif
                    <div class="border-t border-slate-700 pt-4 flex justify-between items-center">
                        <span class="text-white font-bold text-lg">Total Amount</span>
                        <span class="text-cyan-400 font-black text-2xl">₱{{ number_format($payment->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <a href="{{ route('student.subscription.index') }}" class="flex-1 text-center px-6 py-4 bg-gradient-to-r from-slate-700 to-slate-800 hover:from-slate-600 hover:to-slate-700 text-white font-semibold rounded-2xl border border-slate-600 transition-all">
                    View Subscription
                </a>
                <a href="{{ route('student.book-catalog.index') }}" class="flex-1 text-center px-6 py-4 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white font-bold rounded-2xl transition-all">
                    Browse Books
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
