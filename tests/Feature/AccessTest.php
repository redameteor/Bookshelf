<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScreenAccessTest extends TestCase
{
    use RefreshDatabase;

    // ゲストユーザーが書籍一覧ページにアクセスできるか
    public function test_guest_can_access_book_index(): void
    {
        $response = $this->get('/books');
        $response->assertStatus(200);
    }

    // ゲストユーザーが書籍登録ページにアクセスできるか
    public function test_guest_is_redirected_to_login_when_accessing_book_create(): void
    {
        $response = $this->get('/books/create');
        $response->assertRedirect('/login');
    }

    // ログインしたユーザーが書籍登録ページにアクセスできるか
    public function test_logged_in_user_can_access_book_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/books/create');
        $response->assertStatus(200);
    }

    // ゲストユーザーが書籍編集ページにアクセスできるか
    public function test_guest_is_redirected_to_login_when_accessing_book_edit(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->get("/books/{$book->id}/edit");
        $response->assertStatus(403);
    }

    // ゲストユーザーがレビュー編集ページにアクセスできるか
    public function test_other_user_gets_403_when_editing_review(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->get("/reviews/{$review->id}/edit");
        $response->assertStatus(403);
    }
}