<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // 書籍一覧の表示
    public function index(Request $request)
    {
        $query = Book::with(['genres', 'user']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('genre_id')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre_id);
            });
        }

        $books = $query->latest()->paginate(20);
        $genres = Genre::all();

        return view('books.index', compact('books', 'genres'));
    }

    // 書籍詳細の表示
    public function show(Book $book)
    {
        $book->load(['genres', 'user', 'reviews.user']);

        return view('books.show', compact('book'));
    }

    // 新規書籍登録の表示
    public function create()
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    // 新規書籍の保存
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        
        $genreIds = $validated['genres'];
        unset($validated['genres']);

        // ログイン中のユーザーIDを紐付けて書籍を作成
        $book = Auth::user()->books()->create($validated);
        
        // ジャンルの紐付け
        $book->genres()->attach($genreIds);

        return redirect()->route('books.index')->with('success', '書籍を登録しました。');
    }

    // 書籍編集の表示
    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    // 書籍の更新
    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validated();

        $genreIds = $validated['genres'];
        unset($validated['genres']);

        $book->update($validated);
        $book->genres()->sync($genreIds);

        return redirect()->route('books.show', $book)->with('success', '書籍情報を更新しました。');
    }

    // 書籍の削除
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました。');
    }
}