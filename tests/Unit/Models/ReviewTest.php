<?php

namespace Tests\Unit\Models;

use App\Models\Review;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // 複合ユニーク制約の動作
    public function test_review_has_composite_unique_constraint_on_user_and_book(): void
    {
        $review = Review::factory()->create();

        $this->expectException(QueryException::class);

        // 同じ user_id と book_id で重複保存を試みる
        Review::factory()->create([
            'user_id' => $review->user_id,
            'book_id' => $review->book_id,
        ]);
    }
}