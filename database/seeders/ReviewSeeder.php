<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Book;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        $comments = [
            5 => [
                '非常に読み応えがあり、人生観が変わる一冊でした。',
                '何度読み返しても新しい発見があります。大満足です！',
                '構成が素晴らしく、一気に読み終えてしまいました。',
                '実務や日常にすぐ活かせる内容が詰まっています。',
            ],
            4 => [
                '全体的に分かりやすく、非常に勉強になりました。',
                '内容が濃く、知的好奇心が大いに刺激されました。',
                '一部難しい箇所もありましたが、読む価値は十分にあります。',
            ],
            3 => [
                '標準的な内容でしたが、参考になる部分もありました。',
                '期待していた通りの内容で、手堅くまとまっています。',
            ],
        ];

        foreach ($books as $book) {

            $reviewers = $users->random(rand(2, 4));

            foreach ($reviewers as $user) {
                
                $rating = rand(3, 5);
                $commentList = $comments[$rating];
                $comment = $commentList[array_rand($commentList)];

                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => $comment,
                ]);
            }
        }
    }
}