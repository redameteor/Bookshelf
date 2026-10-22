<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'author',
        'isbn',
        'published_at',
        'description',
        'image_url',
    ];

    // 書籍を登録したユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 書籍に対するレビュー一覧
    public function reviews()
    {   
        return $this->hasMany(Review::class);
    }

    // 書籍をお気に入りしたユーザー一覧
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // 書籍のジャンル一覧
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genre')->withTimestamps();
    }      
}
