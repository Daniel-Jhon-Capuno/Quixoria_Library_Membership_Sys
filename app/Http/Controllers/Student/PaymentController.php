<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\Payment;
use App\Models\Subscription;
use App\Notifications\PaymentSubmittedNotification;
use App\Notifications\UpgradeRefundNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function show($tierId)
    {
        try {
            $user = Auth::user();
            $tier = MembershipTier::findOrFail($tierId);
            $tierPrice = (float) ($tier->monthly_fee ?? 0);
            $currentSubscription = $user->subscription;

            if ($currentSubscription) {
                $currentSubscription->loadMissing('membershipTier');
            }

            $isUpgrade = $currentSubscription && $currentSubscription->membership_tier_id !== $tier->id;

            return view('student.payment', compact('tier', 'tierPrice', 'currentSubscription', 'isUpgrade'));
        } catch (\Throwable $e) {
            Log::error('Failed to render student payment page', [
                'tier_id' => $tierId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('student.subscription.index')
                ->withErrors(['error' => 'Unable to open the payment page right now. Please try again.']);
        }
    }

    public function store(Request $request, $tierId)
    {
        try {
            // Step 1: Validate input
            try {
                $request->validate([
                    'full_name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phone' => 'required|string|max:20',
                    'address' => 'required|string|max:500',
                    'payment_method' => 'required|in:gcash,maya,bank_transfer',
                    'reference_number' => 'required|string|max:100',
                    'proof_of_payment' => 'nullable|image|max:5120',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::info('Payment validation failed', ['errors' => $e->errors()]);
                throw $e;
            }

            // Step 2: Get user and tier
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated');
            }
            
            $tier = MembershipTier::findOrFail($tierId);
            $tierPrice = (float) ($tier->monthly_fee ?? 0);
            
            $currentSubscription = $user->subscription;
            if ($currentSubscription) {
                $currentSubscription->loadMissing('membershipTier');
            }

            $isUpgrade = $currentSubscription && $currentSubscription->membership_tier_id !== $tier->id;
            $oldTierName = $isUpgrade ? data_get($currentSubscription, 'membershipTier.name') : null;

            // Step 3: Handle file upload
            $proofPath = null;
            try {
                if ($request->hasFile('proof_of_payment')) {
                    $proofPath = $request->file('proof_of_payment')->store('payment-proofs', 'public');
                    Log::info('File uploaded successfully', ['path' => $proofPath]);
                }
            } catch (\Exception $e) {
                Log::warning('File upload failed, continuing without proof', ['error' => $e->getMessage()]);
                $proofPath = null;
            }

            // Step 4: Cancel current subscription if upgrading
            if ($isUpgrade) {
                $currentSubscription->update(['status' => 'cancelled']);
                Log::info('Cancelled previous subscription', ['sub_id' => $currentSubscription->id]);
            }

            // Step 5: Create payment record
            try {
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $tier->id,
                    'type' => $isUpgrade ? 'upgrade' : 'purchase',
                    'status' => 'pending',
                    'amount' => $tierPrice,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'proof_of_payment' => $proofPath,
                ]);
                Log::info('Payment created successfully', ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create payment record', ['error' => $e->getMessage()]);
                throw new \Exception('Failed to create payment: ' . $e->getMessage());
            }

            // Step 6: Create pending subscription
            try {
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $tier->id,
                    'status' => 'pending',
                    'amount_paid' => $tierPrice,
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                ]);
                $payment->update(['subscription_id' => $subscription->id]);
                Log::info('Subscription created successfully', ['subscription_id' => $subscription->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create pending subscription', ['error' => $e->getMessage()]);
                // Don't fail - payment is more important
            }

            // Step 7: Send notifications
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new PaymentSubmittedNotification($payment));
                }

                if ($isUpgrade) {
                    $user->notifyNow(new UpgradeRefundNotification($payment, $oldTierName));
                }
                Log::info('Notifications sent successfully');
            } catch (\Exception $e) {
                Log::warning('Notification failed', ['error' => $e->getMessage()]);
                // Don't fail on notification errors
            }

            Log::info('Payment submission completed successfully', ['payment_id' => $payment->id]);
            
            return redirect()->route('student.payment.receipt', $payment->id)
                ->with('success', 'Payment submitted! Admin will verify and activate your subscription shortly.');
                
        } catch (\Exception $e) {
            Log::error('Student payment submission failed', [
                'tier_id' => $tierId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Payment Error: ' . $e->getMessage()]);
        }
    }

    public function receipt($paymentId)
    {
        $user = Auth::user();
        $payment = Payment::with(['membershipTier', 'user'])->findOrFail($paymentId);

        if ($payment->user_id !== $user->id) {
            abort(403);
        }

        return view('student.payment-receipt', compact('payment'));
    }
}
