<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // レビュー投稿：rating(1~5), comment を送信し reviews テーブルに保存される
    public function test_user_can_store_review(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post("/books/{$book->id}/reviews", [
            'rating' => 5,
            'comment' => 'とても分かりやすい本でした。',
        ]);

        $response->assertRedirect("/books/{$book->id}");
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
        ]);
    }

    // 1ユーザー1書籍1レビューの制限
    public function test_user_cannot_review_same_book_twice(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        Review::factory()->create(['user_id' => $user->id, 'book_id' => $book->id]);

        $response = $this->actingAs($user)->post("/books/{$book->id}/reviews", [
            'rating' => 4,
            'comment' => '2回目のレビュー',
        ]);

        $response->assertSessionHasErrors();
    }

    // バリデーションエラー：rating:0 や comment空欄で送信するとエラーが発生する
    public function test_review_validation_fails_with_invalid_rating_or_empty_comment(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post("/books/{$book->id}/reviews", [
            'rating' => 0,
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['rating', 'comment']);
    }
}