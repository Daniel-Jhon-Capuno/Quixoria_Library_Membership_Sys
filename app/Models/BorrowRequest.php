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
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
        'is_damaged' => 'boolean',
        'late_fee_waived' => 'boolean',
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
}
