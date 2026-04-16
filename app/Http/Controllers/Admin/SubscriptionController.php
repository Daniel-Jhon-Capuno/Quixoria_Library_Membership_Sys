<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions with status filter.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'membershipTier']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Display the specified subscription with payment history.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'membershipTier']);
        $transactions = Transaction::where('subscription_id', $subscription->id)
            ->with('processor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.subscriptions.show', compact('subscription', 'transactions'));
    }

    /**
     * Manually assign a tier to a student.
     */
    public function override(Request $request, User $user)
    {
        $data = $request->validate([
            'membership_tier_id' => ['required', 'exists:membership_tiers,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($user, $data) {
            // Cancel any existing active subscription
            Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

            // Create new subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'membership_tier_id' => $data['membership_tier_id'],
                'status' => 'active',
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'amount_paid' => $data['amount_paid'],
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'type' => 'payment',
                'amount' => $data['amount_paid'],
                'reference_note' => 'Manual tier assignment by admin',
                'processed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription created and tier assigned successfully.');
    }

    /**
     * Issue manual refund/credit with required reason field.
     */
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

        return redirect()->route('admin.subscriptions.show', $subscription)->with('success', ucfirst($data['type']) . ' recorded successfully.');
    }
}
