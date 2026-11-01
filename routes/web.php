<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

// ログイン済みユーザーのみアクセスできるページ
Route::middleware('auth')->group(function () {
    // 書籍関連のルート
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    // ジャンル関連のルート
    Route::get('/genres/create', [GenreController::class, 'create'])->name('genres.create');    
    Route::get('/genres/{genre}/edit', [GenreController::class, 'edit'])->name('genres.edit');
    Route::delete('/genres/{genre}', [GenreController::class, 'destroy'])->name('genres.destroy');
    // レビュー関連のルート
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    // いいねのルート
    Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like');
    // お気に入り関連のルート
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/books/{book}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});