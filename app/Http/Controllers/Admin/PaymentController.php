<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'membershipTier'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function show($id)
    {
        $payment = Payment::with(['user', 'membershipTier'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function confirm($id)
    {
        $payment = Payment::with(['user', 'membershipTier'])->findOrFail($id);

        if ($payment->status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }


        // Activate subscription. If a pending subscription was created at payment submission, update it
        // instead of creating a duplicate. Also record a Transaction so revenue reports pick this up.
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($payment) {
                // Cancel any currently active subscription for the user
                Subscription::where('user_id', $payment->user_id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

                $subscription = null;

                if ($payment->subscription_id) {
                    $subscription = Subscription::where('id', $payment->subscription_id)
                        ->where('user_id', $payment->user_id)
                        ->first();
                }

                if ($subscription) {
                    // Update the existing pending subscription
                    $subscription->status = 'active';
                    $subscription->starts_at = $subscription->starts_at ?? now();
                    $subscription->ends_at = $subscription->ends_at ?? now()->addMonth();
                    $subscription->amount_paid = $payment->amount;
                    $subscription->save();
                    // Cancel any other pending subscriptions for this user (avoid duplicates in pending inbox)
                    Subscription::where('user_id', $payment->user_id)
                        ->where('status', 'pending')
                        ->where('id', '!=', $subscription->id)
                        ->update(['status' => 'cancelled', 'cancelled_at' => now()]);
                } else {
                    // No pending subscription linked — create a new active one
                    $subscription = Subscription::create([
                        'user_id' => $payment->user_id,
                        'membership_tier_id' => $payment->membership_tier_id,
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => now()->addMonth(),
                        'amount_paid' => $payment->amount,
                    ]);
                    // Cancel any other pending subscriptions for this user (avoid duplicates in pending inbox)
                    Subscription::where('user_id', $payment->user_id)
                        ->where('status', 'pending')
                        ->where('id', '!=', $subscription->id)
                        ->update(['status' => 'cancelled', 'cancelled_at' => now()]);
                }

                // Record transaction so revenue and reports include this payment
                \App\Models\Transaction::create([
                    'user_id' => $payment->user_id,
                    'subscription_id' => $subscription->id,
                    'type' => 'payment',
                    'amount' => $payment->amount,
                    'reference_note' => 'Payment confirmed via admin',
                    'processed_by' => Auth::id(),
                ]);

                $payment->update([
                    'status' => 'confirmed',
                    'subscription_id' => $subscription->id,
                    'confirmed_by' => Auth::id(),
                    'confirmed_at' => now(),
                ]);
            });

            $payment->user->notifyNow(new PaymentConfirmedNotification($payment));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment confirm failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Confirming payment failed: ' . $e->getMessage());
        }

        // Redirect to the payment detail so admins see confirmation and next steps
        return redirect()->route('admin.payments.show', $payment->id)
            ->with('success', 'Payment confirmed and subscription activated.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $payment = Payment::with(['user', 'membershipTier'])->findOrFail($id);

        if ($payment->status !== 'pending') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $payment->user->notifyNow(new PaymentRejectedNotification($payment));

        return back()->with('success', 'Payment rejected and student notified.');
    }
}
