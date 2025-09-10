<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have enough users to assign posts to
        $userCount = User::count();
        if ($userCount < 5) {
            User::factory(5 - $userCount)->create();
        }

        // Get all users to randomly assign posts
        $users = User::all();

        // Create 100 mock posts using the factory
        Post::factory(100)->create([
            'user_id' => function () use ($users) {
                return $users->random()->id;
            },
        ]);
    }
}
