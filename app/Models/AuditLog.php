<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'ip_address' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

