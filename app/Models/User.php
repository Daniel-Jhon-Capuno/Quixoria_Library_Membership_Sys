<?php

namespace App\Models;

use App\Traits\AssignBasicSubscription;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'is_restricted', 'restriction_reason', 'restricted_at', 'sidebar_collapsed', 'has_unpaid_fees', 'is_permanently_banned', 'ban_reason', 'banned_at', 'banned_by', 'temporary_unblock_until'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, AssignBasicSubscription;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_restricted' => 'boolean',
            'restricted_at' => 'datetime',
            'sidebar_collapsed' => 'boolean',
            'has_unpaid_fees' => 'boolean',
            'is_permanently_banned' => 'boolean',
            'banned_at' => 'datetime',
            'temporary_unblock_until' => 'datetime',
        ];
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany('created_at');
    }

    public function borrowRequests()
    {
        return $this->hasMany(BorrowRequest::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}