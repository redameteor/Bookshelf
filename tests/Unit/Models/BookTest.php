<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    // ジャンルとのN:Nリレーション
    public function test_book_belongs_to_user_and_belongs_to_many_genres(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre->id);

        $this->assertTrue($book->user->is($user));
        $this->assertTrue($book->genres->contains($genre));
    }

    // レビュー平均値の計算
    public function test_calculates_average_rating_correctly(): void
    {
        $book = Book::factory()->create();
        
        Review::factory()->create(['book_id' => $book->id, 'rating' => 5]);
        Review::factory()->create(['book_id' => $book->id, 'rating' => 3]);

        $this->assertEquals(4.0, $book->reviews()->avg('rating'));
    }
}