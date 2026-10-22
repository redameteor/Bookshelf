<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    // ジャンル別書籍一覧
    public function test_can_filter_books_by_genre(): void
    {
        $genreTarget = Genre::factory()->create(['name' => '技術書']);
        $genreOther = Genre::factory()->create(['name' => '小説']);

        $book1 = Book::factory()->create(['title' => 'Laravelの本']);
        $book1->genres()->attach($genreTarget->id);

        $book2 = Book::factory()->create(['title' => '吾輩は猫である']);
        $book2->genres()->attach($genreOther->id);

        $response = $this->get('/books?genre=' . $genreTarget->id);

        $response->assertStatus(200)
            ->assertSee('Laravelの本')
            ->assertDontSee('吾輩は猫である');
    }

    // ジャンルの削除ガード：書籍が紐づいているジャンルを削除しようとするとバリデーションエラーが発生する
    public function test_cannot_delete_genre_if_books_are_attached(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $book->genres()->attach($genre->id);

        $response = $this->actingAs($user)->delete("/genres/{$genre->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('genres', ['id' => $genre->id]);
    }

    // ジャンルの削除
    public function test_can_delete_genre_if_no_books_attached(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->delete("/genres/{$genre->id}");

        $response->assertRedirect('/genres');
        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }
}