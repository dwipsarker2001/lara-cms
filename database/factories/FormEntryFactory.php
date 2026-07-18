<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormEntry>
 */
class FormEntryFactory extends Factory
{
    protected $model = FormEntry::class;

    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'data' => [
                'name' => fake()->name(),
                'email' => fake()->safeEmail(),
                'message' => fake()->sentence(),
            ],
        ];
    }
}
