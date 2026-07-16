<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 100, 5000),
            'original_price' => fake()->randomFloat(2, 200, 6000),
            'duration' => fake()->randomElement(['3 days', '5 days', '7 days', '10 days']),
            'destination' => fake()->city(),
            'published' => true,
            'position' => 0,
            'sections' => [],
            'blocks' => [],
        ];
    }
}
