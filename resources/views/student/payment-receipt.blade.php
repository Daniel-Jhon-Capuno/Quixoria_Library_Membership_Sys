<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Payment Receipt</h2>
    </x-slot>

    <!-- Confetti Container -->
    <div id="confetti-container"></div>

    <!-- Celebration Modal -->
    <div id="celebrationModal" class="fixed inset-0 bg-black/70 backdrop-blur-md z-50 flex items-center justify-center p-4 animate-fade-in">
        <div class="bg-gradient-to-br from-emerald-900/90 via-slate-900 to-cyan-900/90 rounded-3xl p-12 border border-emerald-500/50 shadow-2xl shadow-emerald-500/40 max-w-md w-full text-center animate-scale-in">
            <div class="mb-6">
                <div class="text-6xl mb-4 animate-bounce">🎉</div>
                <h2 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-cyan-300 mb-3">Congratulations!</h2>
                <p class="text-xl font-bold text-emerald-200">Thank You for Your Purchase!</p>
            </div>

            <div class="bg-slate-900/60 rounded-2xl p-6 mb-6 border border-slate-700">
                <p class="text-slate-300 mb-4">You've successfully upgraded to the</p>
                <p class="text-2xl font-black text-cyan-300">{{ $payment->membershipTier->name }} Plan</p>
            </div>

            <div class="mb-6 space-y-2">
                <p class="text-emerald-300 font-semibold">✨ Your subscription will be activated once approved</p>
                <p class="text-slate-400 text-sm">Admin will verify your payment within 24 hours</p>
            </div>

            <button onclick="closeCelebration()" class="w-full px-6 py-4 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg">
                Awesome! Let's Go 🚀
            </button>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <!-- Success Banner -->
            <div class="bg-emerald-800/40 border border-emerald-600/50 rounded-2xl p-6 mb-8 text-center animate-slide-down">
                <div class="text-4xl mb-3">✅</div>
                <h3 class="text-xl font-bold text-emerald-300 mb-2">Payment Submitted Successfully!</h3>
                <p class="text-emerald-400">Your payment is under review. Admin will verify and activate your subscription shortly.</p>
            </div>

            <!-- Receipt Card -->
            <div class="bg-gradient-to-b from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700 shadow-xl animate-slide-up">
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

            <div class="mt-6 flex flex-col md:flex-row gap-4">
                <a href="{{ route('student.subscription.index') }}" class="w-full md:flex-1 text-center px-6 py-4 bg-gradient-to-r from-slate-700 to-slate-800 hover:from-slate-600 hover:to-slate-700 text-white font-semibold rounded-2xl border border-slate-600 transition-all min-h-[44px] text-sm md:text-base">
                    View Subscription
                </a>
                <a href="{{ route('student.book-catalog.index') }}" class="w-full md:flex-1 text-center px-6 py-4 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white font-bold rounded-2xl transition-all min-h-[44px] text-sm md:text-base">
                    Browse Books
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.0/dist/confetti.browser.min.js"></script>
    <style>
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes scale-in {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        .animate-scale-in {
            animation: scale-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .animate-slide-down {
            animation: slide-down 0.5s ease-out;
        }

        .animate-slide-up {
            animation: slide-up 0.5s ease-out 0.2s backwards;
        }
    </style>

    <script>
        function closeCelebration() {
            document.getElementById('celebrationModal').style.display = 'none';
        }

        // Launch confetti on page load
        window.addEventListener('load', function() {
            // Confetti burst
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });

            // Second burst after 300ms
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    spread: 100,
                    origin: { y: 0.5 },
                    startVelocity: 30
                });
            }, 300);

            // Third burst after 600ms
            setTimeout(() => {
                confetti({
                    particleCount: 30,
                    spread: 120,
                    origin: { y: 0.4 },
                    startVelocity: 40
                });
            }, 600);

            // Auto-close modal after 5 seconds
            setTimeout(() => {
                closeCelebration();
            }, 5000);
        });
    </script>
</x-app-layout>
