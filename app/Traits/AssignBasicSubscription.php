<?php

namespace App\Traits;

use App\Models\MembershipTier;
use App\Models\Subscription;

trait AssignBasicSubscription
{
    /**
     * Boot the trait.
     */
    protected static function bootAssignBasicSubscription()
    {
        static::created(function ($user) {
            if ($user->role === 'student') {
                // Get the Basic membership tier
                $basicTier = MembershipTier::where('name', 'Basic')->first();

                if ($basicTier) {
                    // Check if subscription already exists
                    $existingSubscription = Subscription::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->first();

                    if (!$existingSubscription) {
                        Subscription::create([
                            'user_id' => $user->id,
                            'membership_tier_id' => $basicTier->id,
                            'status' => 'active',
                            'starts_at' => now(),
                            'ends_at' => now()->addMonth(),
                            'amount_paid' => 0,
                        ]);
                    }
                }
            }
        });
    }
}
