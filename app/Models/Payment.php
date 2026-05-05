<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_tier_id',
        'subscription_id',
        'type',
        'status',
        'amount',
        'full_name',
        'email',
        'phone',
        'address',
        'payment_method',
        'reference_number',
        'proof_of_payment',
        'rejection_reason',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
