<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    // 評価ランキングの表示
    public function index()
    {
        $rankedBooks = Book::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->has('reviews')                     // レビューが1件以上ある書籍に限定
            ->orderByDesc('reviews_avg_rating')  // 平均評価が高い順
            ->orderByDesc('reviews_count')       // 同率の場合はレビュー件数が多い順
            ->take(10)                           // 上位10件に制限
            ->get();

        return view('ranking', compact('rankedBooks'));
    }
}