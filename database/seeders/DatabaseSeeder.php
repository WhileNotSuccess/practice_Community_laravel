<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'login_id' => 'test123',
        ]);
        

        

        $this->call([
            PostSeeder::class,
            CommentSeeder::class,
            NestedCommentSeeder::class,
        ]);
        

    }
}
