<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NoticeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'description' => fake()->sentence(12),
            'notice' => fake()->paragraphs(5, true),
            'path_image' => 'images/default-news.png',
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 99999),
        ];
    }
}
