<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Book;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GenreController extends Controller
{
    // ジャンル一覧の表示
    public function index()
    {
        // 書籍数（books_count）を一緒に集計して取得
        $genres = Genre::withCount('books')->orderBy('id', 'desc')->get();

        return view('genres.index', compact('genres'));
    }

    // ジャンル登録画面
    public function create()
    {
        return view('genres.create');
    }

    // ジャンルの登録処理
    public function store(StoreGenreRequest $request)
    {
        $validated = $request->validated();

        Genre::create($validated);

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを作成しました。');
    }

    // 特定ジャンルに属する書籍一覧画面
    public function show(Genre $genre)
    {
        // そのジャンルに紐づく書籍をページネーション付きで取得
        $books = $genre->books()->with('genres')->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    // ジャンル編集画面の表示
    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    // ジャンルの更新処理
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $validated = $request->validated();

        $genre->update($validated);

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを更新しました。');
    }

    // ジャンルの削除処理
    public function destroy(Genre $genre)
    {
        // 紐づきがある場合の削除制限
    if ($genre->books()->count() > 0) {
            return redirect()->route('genres.index')
                ->with('error', 'このジャンルには書籍が紐づいているため、削除できません。');
        }

        $genre->delete();

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを削除しました。');
    }
}