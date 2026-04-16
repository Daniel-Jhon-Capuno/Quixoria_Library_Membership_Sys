<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    protected $fillable = [
        'name',
        'description',
        'monthly_fee',
        'borrow_limit_per_week',
        'borrow_duration_days',
        'books_per_month',
        'borrow_duration',
        'can_reserve',
        'renewal_limit',
        'late_fee_per_day',
        'priority_level',
        'is_active',
    ];
}
