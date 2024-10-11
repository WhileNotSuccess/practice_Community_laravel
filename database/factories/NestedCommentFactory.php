<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NestedComment>
 */
class NestedCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment_id'=>Comment::factory(),
            'author'=>User::inRandomOrder()->first()->nick_name,
            'content'=>$this->faker->sentence()
        ];
    }
}
