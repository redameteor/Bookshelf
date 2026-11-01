<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Requests\Api\V1\SearchBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // 書籍一覧の取得
    public function index(SearchBookRequest $request)
    {
        $query = Book::with(['genres', 'user']);

        // キーワード検索（タイトルまたは著者名）
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        // ジャンルIDによる絞り込み
        if ($request->filled('genre_id')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre_id);
            });
        }

        // 1ページあたりの件数
        $perPage = $request->input('per_page', 20);

        $books = $query->latest()->paginate($perPage);

        return response()->json([
            'message' => '書籍一覧を取得しました',
            'data' => $books,
        ]);
    }

    // 書籍の詳細情報の取得
    public function show(Book $book)
    {
        $book->load(['genres', 'user', 'reviews.user']);

        return response()->json([
            'message' => '書籍詳細を取得しました',
            'data' => $book,
        ]);
    }

    // 新規書籍の登録
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        
        $validated['user_id'] = $request->user()->id;

        $genreIds = $validated['genre_ids'] ?? [];
        unset($validated['genre_ids']);

        $book = Book::create($validated);

        // 中間テーブルへの紐付け
        if (!empty($genreIds)) {
            $book->genres()->attach($genreIds);
        }

        $book->load(['genres', 'user']);

        return response()->json([
            'message' => '書籍を登録しました',
            'data' => $book,
        ], 201);
    }

    // 書籍情報の更新
    public function update(UpdateBookRequest $request, Book $book)
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'この書籍を更新する権限がありません。',
            ], 403);
        }

        $validated = $request->validated();
        $genreIds = $validated['genre_ids'] ?? [];
        unset($validated['genre_ids']);

        // 書籍情報の更新
        $book->update($validated);

        if (!empty($genreIds)) {
            $book->genres()->sync($genreIds);
        }

        $book->load(['genres', 'user']);

        return response()->json([
            'message' => '書籍情報を更新しました',
            'data' => $book,
        ]);
    }

    // 書籍の削除
    public function destroy(Request $request, Book $book)
    {
        if ($book->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'この書籍を削除する権限がありません。',
            ], 403);
        }

        $book->delete();

        return response()->json([
            'message' => '書籍を削除しました',
        ]);
    }
}