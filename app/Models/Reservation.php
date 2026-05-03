<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Scope for active (waiting) reservations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope for ready reservations
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }
}
