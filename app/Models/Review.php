<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
    ];

    // レビューを投稿したユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // レビュー対象の書籍
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // レビューに対するいいねしたユーザー一覧
    public function likes()
    {
        return $this->belongsToMany(User::class, 'review_likes')->withTimestamps();
    }
}
