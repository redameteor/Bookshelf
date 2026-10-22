<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ユーザーが登録した書籍一覧
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    //　ユーザーが投稿したレビュー一覧
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ユーザーがお気に入りした書籍一覧
    public function favorites()
    {
        return $this->belongsToMany(Book::class, 'favorites');
    }

    // ユーザーがいいねしたレビュー一覧
    public function reviewLikes()
    {
        return $this->belongsToMany(Review::class, 'review_likes');
    }
}
