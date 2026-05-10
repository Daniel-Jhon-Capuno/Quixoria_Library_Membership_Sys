<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNewSubscriptionNotification;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentSubscription = $user->subscription;
        $pendingSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        $tiers = MembershipTier::orderBy('priority_level')->get();
        $currentTierId = $currentSubscription?->membership_tier_id ?? null;

        if ($currentSubscription && $currentSubscription->ends_at) {
            $daysRemaining = round(Carbon::now()->diffInDays($currentSubscription->ends_at, false));
            $renewalMessage = $daysRemaining !== null && $daysRemaining > 0
                ? "Renews in {$daysRemaining} days"
                : ($daysRemaining !== null ? "Expired " . abs($daysRemaining) . " days ago" : '');
        } else {
            $daysRemaining = null;
            $renewalMessage = ($currentSubscription && $currentSubscription->membershipTier && $currentSubscription->membershipTier->monthly_fee == 0)
                ? 'Unlimited access (Basic)'
                : '';
        }

        return view('student.subscription.index', compact(
            'tiers',
            'currentTierId',
            'currentSubscription',
            'pendingSubscription',
            'daysRemaining',
            'renewalMessage'
        ));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
        ]);

        $user = Auth::user();
        $tier = MembershipTier::findOrFail($request->tier_id);

        // If user currently has any subscription, remove it so the new purchase replaces it.
        // Per business rule: prior subscription is removed and there is no refund.
        if ($user->subscription) {
            $user->subscription->delete();
        }

        // If the chosen tier is Basic (free), assign immediately with no payment
        if ($tier->monthly_fee == 0) {
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'membership_tier_id' => $tier->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => null,
                'amount_paid' => 0,
            ]);
            return redirect()->route('student.subscription.index')
                ->with('success', 'You are now on the Basic (free) plan.');
        }

        DB::transaction(function () use ($user, $tier, & $subscription) {
            // Create subscription in pending state for admin approval
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'membership_tier_id' => $tier->id,
                'status' => 'pending',
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
                'amount_paid' => $tier->monthly_fee,
            ]);

            // Record the payment/intent
            Transaction::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'type' => 'payment',
                'amount' => $tier->monthly_fee,
                'reference_note' => "Purchase request for {$tier->name} tier",
                'processed_by' => $user->id,
            ]);
        });

        // Notify admins to review/confirm the pending subscription
        $admins = \App\Models\User::where('role', 'admin')->get();
        Notification::send($admins, new AdminNewSubscriptionNotification($subscription));
        
        // Also notify all staff about new subscription activity
        $staffUsers = \App\Models\User::where('role', 'staff')->get();
        Notification::send($staffUsers, new \App\Notifications\AdminNewSubscriptionNotification($subscription));

        // Warn user that switching removes prior subscription with no refund
        return redirect()->route('student.subscription.index')
            ->with('success', 'Subscription request submitted for admin approval!')
            ->with('warning', 'Note: Purchasing this tier replaces your previous subscription and no refund will be provided.');
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
        ]);

        $user = Auth::user();
        $currentSubscription = $user->subscription;

        if (!$currentSubscription) {
            return back()->withErrors(['error' => 'You do not have an active subscription to upgrade.']);
        }

        $newTier = MembershipTier::findOrFail($request->tier_id);

        DB::transaction(function () use ($user, $currentSubscription, $newTier) {
            // Remove current subscription (not kept as history per requirement)
            $currentSubscription->delete();

            // If new tier is free (Basic) activate immediately
            if ($newTier->monthly_fee == 0) {
                $newSubscription = Subscription::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $newTier->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => null,
                    'amount_paid' => 0,
                ]);
            } else {
                // Create new subscription effective immediately (pending admin confirmation)
                $newSubscription = Subscription::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $newTier->id,
                    'status' => 'pending',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                    'amount_paid' => $newTier->monthly_fee,
                ]);

                Transaction::create([
                    'user_id' => $user->id,
                    'subscription_id' => $newSubscription->id,
                    'type' => 'payment',
                    'amount' => $newTier->monthly_fee,
                    'reference_note' => "Upgrade to {$newTier->name} tier",
                    'processed_by' => $user->id,
                ]);
            }
        });

        return redirect()->route('student.subscription.index')
            ->with('success', 'Subscription changed successfully. New tier will be applied.');
    }

    public function downgrade(Request $request)
    {
        $user = Auth::user();
        $currentSubscription = $user->subscription;

        if (!$currentSubscription) {
            return back()->withErrors(['error' => 'You do not have an active subscription to downgrade.']);
        }

        $newTier = MembershipTier::findOrFail($request->tier_id);

        DB::transaction(function () use ($user, $currentSubscription, $newTier) {
            // Remove current subscription immediately
            $currentSubscription->delete();

            // If new tier is Basic (free), create active perpetual subscription
            if ($newTier->monthly_fee == 0) {
                $newSubscription = Subscription::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $newTier->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => null,
                    'amount_paid' => 0,
                ]);
            } else {
                // Create paid subscription effective immediately (pending admin confirmation)
                $newSubscription = Subscription::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $newTier->id,
                    'status' => 'pending',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                    'amount_paid' => $newTier->monthly_fee,
                ]);

                Transaction::create([
                    'user_id' => $user->id,
                    'subscription_id' => $newSubscription->id,
                    'type' => 'payment',
                    'amount' => $newTier->monthly_fee,
                    'reference_note' => "Downgrade to {$newTier->name} tier",
                    'processed_by' => $user->id,
                ]);
            }
        });

        return redirect()->route('student.subscription.index')
            ->with('success', 'Subscription changed successfully. New tier will be applied.');
    }

    public function cancel()
    {
        $user = Auth::user();
        $currentSubscription = $user->subscription;

        if (!$currentSubscription) {
            return back()->withErrors(['error' => 'You do not have an active subscription to cancel.']);
        }

        $currentSubscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $expiryText = $currentSubscription->ends_at ? $currentSubscription->ends_at->format('M j, Y') : 'no expiration';

        return redirect()->route('student.subscription.index')
            ->with('success', 'Subscription cancelled successfully! You will retain access until ' . $expiryText . '.');
    }
}
