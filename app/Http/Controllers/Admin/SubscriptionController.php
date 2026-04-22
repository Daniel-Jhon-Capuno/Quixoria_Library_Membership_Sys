<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\StudentSubscriptionConfirmedNotification;
use App\Notifications\StudentSubscriptionRejectedNotification;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'membershipTier']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'membershipTier']);
        $transactions = Transaction::where('subscription_id', $subscription->id)
            ->with('processor')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.subscriptions.show', compact('subscription', 'transactions'));
    }

    public function override(Request $request, User $user)
    {
        $data = $request->validate([
            'membership_tier_id' => ['required', 'exists:membership_tiers,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($user, $data) {
            Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'membership_tier_id' => $data['membership_tier_id'],
                'status' => 'active',
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'amount_paid' => $data['amount_paid'],
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'type' => 'payment',
                'amount' => $data['amount_paid'],
                'reference_note' => 'Manual tier assignment by admin',
                'processed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription created.');
    }

    public function adjust(Request $request, Subscription $subscription)
    {
        $data = $request->validate([
            'type' => ['required', 'in:refund,adjustment'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_note' => ['required', 'string', 'max:1000'],
        ]);

        Transaction::create([
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'reference_note' => $data['reference_note'],
            'processed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.subscriptions.show', $subscription)->with('success', 'Adjustment saved.');
    }

    public function quickFix(Request $request, User $user)
    {
        $days = (int) ($request->input('extend_days') ?: 30);

        try {
            DB::transaction(function () use ($user, $days) {
                $subscription = Subscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

                if ($subscription) {
                    $baseDate = ($subscription->ends_at && $subscription->ends_at->isFuture()) 
                        ? $subscription->ends_at 
                        : now();

                    $subscription->ends_at = $baseDate->addDays($days);
                    $subscription->save();
                } else {
                    $tier = MembershipTier::where('is_active', true)->first() ?? MembershipTier::first();
                    Subscription::create([
                        'user_id' => $user->id,
                        'membership_tier_id' => $tier->id ?? null,
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => now()->addDays($days),
                        'amount_paid' => 0,
                    ]);
                }
            });

            return back()->with('success', "Subscription updated: +{$days} days.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Fix failed: ' . $e->getMessage()]);
        }
    }

    // Force-activate a membership tier for a user immediately (admin action)
    public function forceActivate(Request $request, User $user)
    {
        $data = $request->validate([
            'membership_tier_id' => ['required', 'exists:membership_tiers,id'],
        ]);

        try {
            DB::transaction(function () use ($user, $data) {
                // Cancel any active subscriptions
                Subscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

                $tier = MembershipTier::findOrFail($data['membership_tier_id']);

                // Create immediate active subscription
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $tier->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                    'amount_paid' => $tier->monthly_fee,
                ]);

                Transaction::create([
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'type' => 'payment',
                    'amount' => $subscription->amount_paid,
                    'reference_note' => 'Force-activated by admin',
                    'processed_by' => auth()->id(),
                ]);
            });

            return back()->with('success', 'Subscription force-activated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Force activation failed: ' . $e->getMessage()]);
        }
    }

    // Admin debug: show all subscriptions and tier info for a user
    public function debug(User $user)
    {
        $subscriptions = \App\Models\Subscription::where('user_id', $user->id)
            ->with('membershipTier')
            ->orderBy('created_at', 'desc')
            ->get();

        $current = $user->subscription; // the app's chosen active subscription (hasOne)

        // weekly borrow count
        $weeklyBorrows = \App\Models\BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active', 'returned', 'overdue'])
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $membershipTiers = \App\Models\MembershipTier::orderBy('priority_level')->get();

        return view('admin.subscriptions.debug', compact('user', 'subscriptions', 'current', 'weeklyBorrows', 'membershipTiers'));
    }

    // Confirm a pending subscription (admin action)
    public function confirm(Subscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending subscriptions can be confirmed.']);
        }

        try {
            DB::transaction(function () use ($subscription) {
                // Cancel any currently active subscription for the user
                Subscription::where('user_id', $subscription->user_id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

                // Activate this subscription
                $subscription->status = 'active';
                $subscription->starts_at = $subscription->starts_at ?? now();
                $subscription->ends_at = $subscription->ends_at ?? now()->addMonth();
                $subscription->save();
            });

            // Notify the student
            $subscription->user->notifyNow(new StudentSubscriptionConfirmedNotification($subscription));

            return back()->with('success', 'Subscription confirmed and activated.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Confirmation failed: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, Subscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending subscriptions can be rejected.']);
        }

        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            $subscription->status = 'rejected';
            $subscription->rejection_reason = $request->input('reason');
            $subscription->save();

            // Notify student
            $subscription->user->notifyNow(new StudentSubscriptionRejectedNotification($subscription));

            return back()->with('success', 'Subscription rejected.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Rejection failed: ' . $e->getMessage()]);
        }
    }

    // Show pending subscriptions inbox
    public function pending(Request $request)
    {
        $query = Subscription::with(['user', 'membershipTier'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        $subscriptions = $query->paginate(25)->withQueryString();

        return view('admin.subscriptions.pending', compact('subscriptions'));
    }

    // Bulk confirm selected subscriptions
    public function bulkConfirm(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->withErrors(['error' => 'No subscriptions selected.']);
        }

        DB::transaction(function () use ($ids) {
            $subscriptions = Subscription::whereIn('id', $ids)->where('status', 'pending')->get();
            foreach ($subscriptions as $subscription) {
                Subscription::where('user_id', $subscription->user_id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

                $subscription->status = 'active';
                $subscription->starts_at = $subscription->starts_at ?? now();
                $subscription->ends_at = $subscription->ends_at ?? now()->addMonth();
                $subscription->save();

                // notify student
                $subscription->user->notify(new StudentSubscriptionConfirmedNotification($subscription));
            }
        });

        return back()->with('success', 'Selected subscriptions approved.');
    }

    // Bulk reject selected subscriptions
    public function bulkReject(Request $request)
    {
        $ids = $request->input('ids', []);
        $reason = $request->input('reason');
        if (empty($ids) || !is_array($ids)) {
            return back()->withErrors(['error' => 'No subscriptions selected.']);
        }

        DB::transaction(function () use ($ids, $reason) {
            $subscriptions = Subscription::whereIn('id', $ids)->where('status', 'pending')->get();
            foreach ($subscriptions as $subscription) {
                $subscription->status = 'rejected';
                $subscription->rejection_reason = $reason;
                $subscription->save();
                $subscription->user->notify(new StudentSubscriptionRejectedNotification($subscription));
            }
        });

        return back()->with('success', 'Selected subscriptions rejected.');
    }
}