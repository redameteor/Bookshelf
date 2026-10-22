<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // 書籍との1:Nリレーション
    public function test_user_has_many_books(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        // ユーザーが投稿した書籍の一覧に存在するか
        $this->assertTrue($user->books->contains($book));
        $this->assertEquals(1, $user->books->count());
    }

    // お気に入り書籍とのN:Nリレーション
    public function test_user_belongs_to_many_favorite_books(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 中間テーブル favorites 経由でお気に入り登録
        $user->favoriteBooks()->attach($book->id);

        // お気に入り一覧から対象の書籍が取得できるか
        $this->assertTrue($user->favoriteBooks->contains($book));
        $this->assertEquals(1, $user->favoriteBooks->count());
    }
}