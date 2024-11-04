<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\NestedComment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NestedCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::all()->each(function ($comment){
            NestedComment::factory()->count(1)->create(['comment_id'=>$comment->id]);
        });
    }
}
