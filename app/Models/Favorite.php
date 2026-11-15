<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
    ];

    // お気に入りしたユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // お気に入りされた書籍
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
