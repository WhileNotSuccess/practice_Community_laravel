<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $category = [
        '공지사항',
        '자유게시판',
        '축제게시판',
    ];
    public $index = -1;
    public function definition(): array
    {
        $this->index++;
        return [
            'board_name'=>$this->category[$this->index]
        ];
    }
}
