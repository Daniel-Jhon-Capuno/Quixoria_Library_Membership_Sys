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

        if ($currentSubscription) {
            $tier = $currentSubscription->membershipTier;
            $daysRemaining = Carbon::now()->diffInDays($currentSubscription->ends_at, false);
            $renewalMessage = $daysRemaining > 0
                ? "Your subscription renews in {$daysRemaining} days"
                : "Your subscription expired " . abs($daysRemaining) . " days ago";

            return view('student.subscription.index', compact(
                'currentSubscription',
                'tier',
                'daysRemaining',
                'renewalMessage'
            ));
        }

        // No active subscription - show available tiers and pending subscription if any
        $tiers = MembershipTier::orderBy('priority_level')->get();

        return view('student.subscription.index', compact('tiers', 'pendingSubscription'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
        ]);

        $user = Auth::user();

        // Check for existing active subscription
        if ($user->subscription) {
            return back()->withErrors(['error' => 'You already have an active subscription.']);
        }

        $tier = MembershipTier::findOrFail($request->tier_id);

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

        return redirect()->route('student.subscription.index')
            ->with('success', 'Subscription request submitted for admin approval!');
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

        $currentTier = $currentSubscription->membershipTier;
        $newTier = MembershipTier::findOrFail($request->tier_id);

        // Check if it's the next tier (by priority_level)
        if ($newTier->priority_level !== $currentTier->priority_level + 1) {
            return back()->withErrors(['error' => 'You can only upgrade to the next tier level.']);
        }

        DB::transaction(function () use ($user, $currentSubscription, $newTier) {
            // Cancel current subscription
            $currentSubscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Create new subscription starting after current ends
            $newSubscription = Subscription::create([
                'user_id' => $user->id,
                'membership_tier_id' => $newTier->id,
                'status' => 'pending', // Will become active when current ends
                'starts_at' => $currentSubscription->ends_at,
                'ends_at' => $currentSubscription->ends_at->addMonth(),
                'amount_paid' => $newTier->monthly_fee,
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'subscription_id' => $newSubscription->id,
                'type' => 'payment',
                'amount' => $newTier->monthly_fee,
                'reference_note' => "Upgrade to {$newTier->name} tier",
                'processed_by' => $user->id,
            ]);
        });

        return redirect()->route('student.subscription.index')
            ->with('success', 'Subscription upgraded successfully! New tier will be active after current subscription ends.');
    }

    public function downgrade(Request $request)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
        ]);

        $user = Auth::user();
        $currentSubscription = $user->subscription;

        if (!$currentSubscription) {
            return back()->withErrors(['error' => 'You do not have an active subscription to downgrade.']);
        }

        $currentTier = $currentSubscription->membershipTier;
        $newTier = MembershipTier::findOrFail($request->tier_id);

        // Check if it's a lower tier
        if ($newTier->priority_level >= $currentTier->priority_level) {
            return back()->withErrors(['error' => 'You can only downgrade to a lower tier.']);
        }

        DB::transaction(function () use ($user, $currentSubscription, $newTier) {
            // Create new subscription starting next billing cycle
            $newSubscription = Subscription::create([
                'user_id' => $user->id,
                'membership_tier_id' => $newTier->id,
                'status' => 'pending', // Will become active next cycle
                'starts_at' => $currentSubscription->ends_at,
                'ends_at' => $currentSubscription->ends_at->addMonth(),
                'amount_paid' => $newTier->monthly_fee,
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'subscription_id' => $newSubscription->id,
                'type' => 'payment',
                'amount' => $newTier->monthly_fee,
                'reference_note' => "Downgrade to {$newTier->name} tier",
                'processed_by' => $user->id,
            ]);
        });

        return redirect()->route('student.subscription.index')
            ->with('success', 'Subscription downgraded successfully! New tier will be active next billing cycle.');
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

        return redirect()->route('student.subscription.index')
            ->with('success', 'Subscription cancelled successfully! You will retain access until ' . $currentSubscription->ends_at->format('M j, Y') . '.');
    }
}