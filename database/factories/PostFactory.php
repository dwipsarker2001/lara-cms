<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->paragraph(),
            'author' => fake()->name(),
            'date' => now(),
            'tags' => [],
            'published' => true,
            'position' => 0,
            'sections' => [],
        ];
    }
}
