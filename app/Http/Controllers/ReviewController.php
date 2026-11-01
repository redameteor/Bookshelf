<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // レビューの新規投稿
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $book->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('books.show', $book)->with('success', 'レビューを投稿しました！');
    }

    // レビュー編集の表示
    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    // レビューの更新
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return redirect()->route('books.show', $review->book_id)->with('success', 'レビューを更新しました！');
    }

    // レビューの削除
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $bookId = $review->book_id;
        $review->delete();

        return redirect()->route('books.show', $bookId)->with('success', 'レビューを削除しました！');
    }

    // いいね（お気に入り）の切り替え
    public function like(Review $review)
    {
        $user = Auth::user();
        
        // 多対多のリレーション（likedReviews）を用いたトグル処理
        $user->likedReviews()->toggle($review->id);

        return back();
    }
}