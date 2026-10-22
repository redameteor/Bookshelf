<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            
            $favoriteBooks = $books->random(rand(3, 5));
            $bookIds = $favoriteBooks->pluck('id')->toArray();

            // syncWithoutDetachingで重複登録を防ぎながら紐づけ
            $user->favoriteBooks()->syncWithoutDetaching($bookIds);
        }
    }
}
