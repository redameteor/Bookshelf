<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = Review::all();
        $users = User::all();

        foreach ($reviews as $review) {

            $otherUsers = $users->reject(fn ($user) => $user->id === $review->user_id);
            
            if ($otherUsers->isEmpty()) {
                continue;
            }

            $likeCount = min(rand(0, 3), $otherUsers->count());
            
            if ($likeCount > 0) {

                $likers = $otherUsers->random($likeCount);
                $likerIds = $likers->pluck('id')->toArray();

                foreach ($likerIds as $userId) {

                    \DB::table('review_likes')->updateOrInsert(
                        ['review_id' => $review->id, 'user_id' => $userId],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}
