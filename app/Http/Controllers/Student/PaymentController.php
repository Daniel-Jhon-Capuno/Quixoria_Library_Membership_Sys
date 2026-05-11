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
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
                'payment_method' => 'required|in:gcash,maya,bank_transfer',
                'reference_number' => 'required|string|max:100',
                'proof_of_payment' => 'nullable|image|max:5120',
            ]);

            $user = Auth::user();
            $tier = MembershipTier::findOrFail($tierId);
            $tierPrice = (float) ($tier->monthly_fee ?? 0);
            $currentSubscription = $user->subscription;
            if ($currentSubscription) {
                $currentSubscription->loadMissing('membershipTier');
            }

            $isUpgrade = $currentSubscription && $currentSubscription->membership_tier_id !== $tier->id;
            $oldTierName = $isUpgrade ? data_get($currentSubscription, 'membershipTier.name') : null;

            // Handle file upload
            $proofPath = null;
            if ($request->hasFile('proof_of_payment')) {
                $proofPath = $request->file('proof_of_payment')->store('payment-proofs', 'public');
            }

            // Cancel current subscription if upgrading
            if ($isUpgrade) {
                $currentSubscription->update(['status' => 'cancelled']);
            }

            // Create payment record
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

            // Create a pending subscription record so admins see it in the Subscriptions -> Pending inbox
            // and link the payment to the subscription. This allows admins to review the subscription
            // along with the payment evidence in one place.
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
            } catch (\Throwable $e) {
                // If creating the subscription fails, log but continue so payment isn't lost.
                Log::error('Failed to create pending subscription for payment', [
                    'payment_id' => $payment->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }

            // Notify admins and student, but never let notification failure break purchase flow.
            try {
                $admins = \App\Models\User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($admins, new PaymentSubmittedNotification($payment));
                }

                if ($isUpgrade) {
                    $user->notifyNow(new UpgradeRefundNotification($payment, $oldTierName));
                }
            } catch (\Throwable $e) {
                Log::warning('Subscription payment notification failed', [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->route('student.payment.receipt', $payment->id)
                ->with('success', 'Payment submitted! Admin will verify and activate your subscription shortly.');
        } catch (\Throwable $e) {
            Log::error('Student payment submission failed', [
                'tier_id' => $tierId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'We could not process your payment request right now. Please try again.']);
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
