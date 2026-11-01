<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // お気に入り書籍一覧の表示
    public function index()
    {
        $user = Auth::user();

        $books = $user->favoriteBooks()->latest()->paginate(20);

        return view('favorites.index', compact('books'));
    }

    // 書籍のお気に入り登録・解除の切り替え
    public function toggle(Book $book)
    {
        $user = Auth::user();

        $user->favorites()->toggle($book->id);

        return back();
    }
}