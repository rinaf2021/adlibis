<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Comment;
use App\Models\Post;
use App\Models\News;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        function makeEntityKey(string $prefix, string $id) {
            return "{$prefix}:{$id}";
        }

        $userIds = User::get('id')->pluck('id');
        $postIds = Post::get('id')->pluck('id')->map(function(int $item) {
            return makeEntityKey('P', $item);
        })->toArray();
        $newsIds = News::get('id')->pluck('id')->map(function(int $item) {
            return makeEntityKey('N', $item);
        })->toArray();

        $entityIds = [...$postIds, ...$newsIds];

        Comment::factory()
            ->count(50)
            ->state(new Sequence(
                fn(Sequence $sequence) => ['user_id' => fake()->randomElement($userIds)]
            ))
            ->state(new Sequence(
                fn(Sequence $sequence) => ['entity' => fake()->randomElement($entityIds)]
            ))
            ->create();

        $entityIds = Comment::get('id')->pluck('id')->map(function(int $item) {
            return makeEntityKey('C', $item);
        })->toArray();

        Comment::factory()
            ->count(50)
            ->state(new Sequence(
                fn(Sequence $sequence) => ['user_id' => fake()->randomElement($userIds)]
            ))
            ->state(new Sequence(
                fn(Sequence $sequence) => ['entity' => fake()->randomElement($entityIds)]
            ))
            ->create();
    }
}
