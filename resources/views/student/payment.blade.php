<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Complete Your Payment</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-900/40 border border-red-600/50 rounded-2xl p-4 mb-6 text-red-300">
                    <p class="font-bold mb-2">❌ Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($isUpgrade)
            <div class="bg-yellow-800/40 border border-yellow-600/50 rounded-2xl p-4 mb-6 text-yellow-300">
                ⚠️ You are upgrading from <strong>{{ data_get($currentSubscription, 'membershipTier.name', 'your current plan') }}</strong> to <strong>{{ $tier->name }}</strong>. Your current subscription will be cancelled and you will need to visit the branch to claim your refund.
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Tier Summary -->
                <div class="bg-gradient-to-b from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-6">Order Summary</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-700 pb-4">
                            <span class="text-slate-400">Plan</span>
                            <span class="text-white font-bold text-lg">{{ $tier->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Price</span>
                            <span class="text-cyan-400 font-bold text-2xl">₱{{ number_format($tierPrice ?? $tier->monthly_fee ?? 0, 2) }}/mo</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Borrow Limit</span>
                            <span class="text-white">{{ $tier->borrow_limit_per_week }} books/week</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Borrow Duration</span>
                            <span class="text-white">{{ $tier->borrow_duration_days }} days</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Renewals</span>
                            <span class="text-white">{{ $tier->renewal_limit }}x per book</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Late Fee</span>
                            <span class="text-white">₱{{ number_format($tier->late_fee_per_day, 2) }}/day</span>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-slate-900/50 rounded-xl border border-slate-700">
                        <div class="text-xs text-slate-400 mb-2 uppercase tracking-wide">Payment Methods Accepted</div>
                        <div class="flex gap-3">
                            <span class="px-3 py-1 bg-blue-600/30 text-blue-300 rounded-full text-sm font-medium">GCash</span>
                            <span class="px-3 py-1 bg-green-600/30 text-green-300 rounded-full text-sm font-medium">Maya</span>
                            <span class="px-3 py-1 bg-purple-600/30 text-purple-300 rounded-full text-sm font-medium">Bank Transfer</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-gradient-to-b from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-6">Billing Information</h3>

                    <form method="POST" action="{{ route('student.payment.store', $tier->id) }}" enctype="multipart/form-data" id="paymentForm">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
                                <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required
                                    class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                @error('full_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                    class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="09XXXXXXXXX"
                                    class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Address</label>
                                <input type="text" name="address" value="{{ old('address') }}" required
                                    class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                @error('address') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Payment Method</label>
                                <select name="payment_method" required
                                    class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                    <option value="">Select payment method</option>
                                    <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                                    <option value="maya" {{ old('payment_method') == 'maya' ? 'selected' : '' }}>Maya</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                                @error('payment_method') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Reference Number</label>
                                <input type="text" name="reference_number" value="{{ old('reference_number') }}" required placeholder="Transaction reference number"
                                    class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                @error('reference_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Proof of Payment <span class="text-slate-500">(optional)</span></label>
                                <input type="file" name="proof_of_payment" accept="image/*"
                                    class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                                <p class="text-slate-500 text-xs mt-1">Upload a screenshot of your payment confirmation (max 5MB)</p>
                                @error('proof_of_payment') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <button type="button" onclick="openConfirmModal()"
                            class="w-full mt-6 px-6 py-4 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white font-bold rounded-2xl shadow-xl transition-all duration-200 text-sm md:text-base min-h-[44px]">
                            Review & Confirm Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
        <div class="bg-slate-800 rounded-2xl p-8 max-w-md md:w-96 w-full mx-4 md:mx-auto border border-slate-700 shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-4">Confirm Payment</h3>
            <p class="text-slate-300 mb-2">You are about to purchase:</p>
                <div class="bg-slate-900/50 rounded-xl p-4 mb-6 border border-slate-700">
                <div class="text-white font-bold text-lg">{{ $tier->name }}</div>
                    <div class="text-cyan-400 font-bold text-2xl">₱{{ number_format($tierPrice ?? $tier->monthly_fee ?? 0, 2) }}/month</div>
            </div>
            <p class="text-slate-400 text-sm mb-6">Are you sure your billing information is correct? This will be submitted for admin review.</p>
                <div class="flex flex-col md:flex-row gap-3">
                <button onclick="closeConfirmModal()" class="w-full md:flex-1 px-4 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-semibold transition-all min-h-[44px] text-sm md:text-base">
                    Go Back
                </button>
                <button id="confirmSubmitBtn" type="button" onclick="submitPaymentForm(this)" class="w-full md:flex-1 px-4 py-3 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white rounded-xl font-semibold transition-all min-h-[44px] text-sm md:text-base">
                    Confirm Payment
                </button>
            </div>
        </div>
    </div>

    <script>
        function openConfirmModal() {
            const form = document.getElementById('paymentForm');
            if (!form) {
                alert('Form not found');
                return;
            }
            
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }
        
        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        function submitPaymentForm(btn) {
            const form = document.getElementById('paymentForm');
            
            if (!form) {
                alert('Error: Form not found');
                return;
            }

            // Final validation before submit
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Prevent double submission
            btn.disabled = true;
            btn.innerHTML = 'Processing... ⏳';
            
            // Submit form directly
            form.submit();
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeConfirmModal();
        });
    </script>
</x-app-layout>
