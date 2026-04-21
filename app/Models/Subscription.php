<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'membership_tier_id',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'amount_paid',
    ];

    // This part is critical for the date math to work
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}