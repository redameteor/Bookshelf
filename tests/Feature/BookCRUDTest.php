<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCrudTest extends TestCase
{
    use RefreshDatabase;

    // 書籍登録：タイトル、著者、ISBN、ジャンルID配列を送信し正常に保存される
    public function test_user_can_store_book_with_genres(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->post('/books', [
            'title' => 'Laravel実践入門',
            'author' => '山田 太郎',
            'isbn' => '9784123456789',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect('/books');
        $this->assertDatabaseHas('books', ['title' => 'Laravel実践入門']);
        $this->assertDatabaseHas('book_genre', ['genre_id' => $genre->id]);
    }

    // バリデーションエラー：タイトル空欄や無効なISBN形式でエラーが発生する
    public function test_book_store_fails_with_validation_errors(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/books', [
            'title' => '',
            'isbn' => 'invalid-isbn',
        ]);

        $response->assertSessionHasErrors(['title', 'isbn']);
    }

    // 所有者が書籍を更新できる
    public function test_owner_can_update_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->put("/books/{$book->id}", [
            'title' => '更新後のタイトル',
            'author' => $book->author,
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect("/books/{$book->id}");
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => '更新後のタイトル']);
    }

    // 他のユーザーが書籍を更新できない
    public function test_other_user_gets_403_when_updating_book(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->put("/books/{$book->id}", [
            'title' => '不正な更新',
            'author' => $book->author,
        ]);

        $response->assertStatus(403);
    }

    // 所有者が書籍を削除できる
    public function test_owner_can_delete_book_and_cascade_relations(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $genre = Genre::factory()->create();
        $book->genres()->attach($genre->id);
        $review = Review::factory()->create(['book_id' => $book->id]);

        $response = $this->actingAs($user)->delete("/books/{$book->id}");

        $response->assertRedirect('/books');
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('book_genre', ['book_id' => $book->id]);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}