<?php

namespace Database\Factories;

use App\Models\Layout;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayoutFactory extends Factory
{
    protected $model = Layout::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'collection' => fake()->randomElement(['page', 'blog', 'package']),
            'sections' => [],
            'position' => 0,
        ];
    }
}
