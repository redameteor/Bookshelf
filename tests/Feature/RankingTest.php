<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    // ランキングページの表示と書籍の平均評価順の並び替え
    public function test_ranking_orders_books_by_average_rating_correctly(): void
    {
        
        // 本B（平均評価 3.0）
        $bookB = Book::factory()->create(['title' => '本B']);
        Review::factory()->create(['book_id' => $bookB->id, 'rating' => 3]);

        // 本A（平均評価 4.0）
        $bookA = Book::factory()->create(['title' => '本A']);
        Review::factory()->create(['book_id' => $bookA->id, 'rating' => 5]);
        Review::factory()->create(['book_id' => $bookA->id, 'rating' => 3]);

        $response = $this->get('/ranking');

        $response->assertStatus(200)
            ->assertSeeInOrder(['本A', '本B']);
    }

    // 平均評価が同じ場合、レビュー件数の多い順に並び替え
    public function test_ranking_orders_books_by_review_count_when_average_rating_is_tied(): void
    {
        // 本B（平均評価 4.0 / レビュー1件）
        $bookB = Book::factory()->create(['title' => '本B']);
        Review::factory()->create(['book_id' => $bookB->id, 'rating' => 4]);

        // 本A（平均評価 4.0 / レビュー2件）
        $bookA = Book::factory()->create(['title' => '本A']);
        Review::factory()->create(['book_id' => $bookA->id, 'rating' => 4]);
        Review::factory()->create(['book_id' => $bookA->id, 'rating' => 4]);

        $response = $this->get('/ranking');

        $response->assertStatus(200)
            ->assertSeeInOrder(['本A', '本B']);
    }
}