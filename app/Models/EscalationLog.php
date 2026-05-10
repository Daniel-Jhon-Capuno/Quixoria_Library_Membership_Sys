<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscalationLog extends Model
{
    protected $fillable = [
        'borrow_request_id',
        'level',
        'note',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function borrowRequest()
    {
        return $this->belongsTo(BorrowRequest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
