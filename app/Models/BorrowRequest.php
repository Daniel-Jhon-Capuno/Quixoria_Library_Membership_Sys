<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'handled_by',
        'status',
        'rejection_reason',
        'borrowed_at',
        'due_at',
        'returned_at',
        'is_damaged',
        'renewals_used',
        'late_fee_charged',
        'late_fee_waived',
        'appeal_reason',
        'appeal_scheduled_at',
        'appeal_status',
        'escalation_level',
        'replacement_fee_cents',
        'replacement_fee_paid',
        'resolved_at',
        'resolved_by',
        'temporary_unblock_until',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
        'appeal_scheduled_at' => 'datetime',
        'is_damaged' => 'boolean',
        'late_fee_waived' => 'boolean',
        'replacement_fee_paid' => 'boolean',
        'resolved_at' => 'datetime',
        'temporary_unblock_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function escalationLogs()
    {
        return $this->hasMany(\App\Models\EscalationLog::class);
    }

    public function resolution()
    {
        return $this->hasOne(\App\Models\Resolution::class);
    }
}
