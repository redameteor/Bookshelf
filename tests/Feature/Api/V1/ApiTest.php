<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    // API書籍一覧の検索・絞り込み
    public function test_can_search_and_filter_books_with_parameters(): void
    {
        $genre = Genre::factory()->create();
        
        $matchBook = Book::factory()->create(['title' => 'Laravel入門', 'author' => '山田']);
        $matchBook->genres()->attach($genre);

        $otherBook = Book::factory()->create(['title' => '住みにごり', 'author' => 'たかたけし']);

        $response = $this->getJson('/api/v1/books?keyword=Laravel&genre=' . $genre->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Laravel入門'])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'title', 'author', 'average_rating', 'reviews_count', 'genres'
                    ]
                ]
            ]);
    }

    // API一覧のページネーション
    public function test_pagination_structure_is_returned_correctly(): void
    {
        Book::factory()->count(25)->create();

        $response = $this->getJson('/api/v1/books?page=2&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'last_page', 'per_page', 'to', 'total']
            ])
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 10);
    }

    // 存在しない書籍IDでの取得時に404が返る
    public function test_returns_404_when_book_id_does_not_exist(): void
    {
        $response = $this->getJson('/api/v1/books/99999');

        $response->assertStatus(404);
    }

    // バリデーションエラー時に422が返る（登録時）
    public function test_returns_422_on_book_creation_validation_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/books', [
            'title' => '',
            'author' => '著者名',
            'isbn' => 'invalid-isbn',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'isbn']);
    }

    // バリデーションエラー時に422が返る（更新時）
    public function test_returns_422_on_book_update_validation_error(): void
    {
        // 条件：必須項目の欠如による更新エラー
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/v1/books/{$book->id}", [
            'title' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    // 書籍削除時に関連データも削除されること
    public function test_cascade_deletion_deletes_related_data_when_book_is_deleted(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre);
        $review = Review::factory()->create(['book_id' => $book->id, 'user_id' => $user->id]);
        $book->favorites()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        $this->assertDatabaseMissing('favorites', ['book_id' => $book->id]);
        // ジャンル本体ではない、中間テーブルの紐づきが消えている
        $this->assertDatabaseMissing('book_genre', ['book_id' => $book->id, 'genre_id' => $genre->id]);
        // ジャンル自体は削除されない
        $this->assertDatabaseHas('genres', ['id' => $genre->id]);
    }
}