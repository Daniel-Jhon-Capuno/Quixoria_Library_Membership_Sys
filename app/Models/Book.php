<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'genre',
        'category',
        'publication_year',
        'description',
        'cover_image',
        'total_copies',
        'available_copies',
        'is_archived',
    ];

    public function borrowRequests()
    {
        return $this->hasMany(BorrowRequest::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}

