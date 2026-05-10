<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
    protected $fillable = [
        'borrow_request_id',
        'admin_id',
        'amount_cents',
        'method',
        'notes',
        'receipt_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function borrowRequest()
    {
        return $this->belongsTo(BorrowRequest::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
