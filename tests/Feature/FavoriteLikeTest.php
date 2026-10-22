<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteAndLikeTest extends TestCase
{
    use RefreshDatabase;

    // お気に入り書籍のトグル：1回目で追加、2回目（連打）で解除される
    public function test_user_can_toggle_favorite_and_double_click_toggles_back(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 1回目：お気に入り登録（追加）
        $this->actingAs($user)->post("/books/{$book->id}/favorite");
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'book_id' => $book->id]);

        // 2回目（連打を想定）：お気に入り解除
        $this->actingAs($user)->post("/books/{$book->id}/favorite");
        $this->assertDatabaseMissing('favorites', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    // いいねのトグル：1回目で追加、2回目（連打）で解除される
    public function test_user_can_toggle_review_like_and_double_click_toggles_back(): void
    {
        $owner = User::factory()->create();
        $liker = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id]);

        // 1回目：いいね追加
        $this->actingAs($liker)->post("/reviews/{$review->id}/like");
        $this->assertDatabaseHas('review_likes', ['user_id' => $liker->id, 'review_id' => $review->id]);

        // 2回目（連打を想定）：いいね解除
        $this->actingAs($liker)->post("/reviews/{$review->id}/like");
        $this->assertDatabaseMissing('review_likes', ['user_id' => $liker->id, 'review_id' => $review->id]);
    }

    // 自分のレビューにいいねできない
    public function test_user_cannot_like_own_review(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/reviews/{$review->id}/like");

        $response->assertStatus(403);
        $this->assertDatabaseMissing('review_likes', ['user_id' => $user->id, 'review_id' => $review->id]);
    }
}