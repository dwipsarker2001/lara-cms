<?php

namespace Database\Factories;

use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Form>
 */
class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'submit_text' => 'Submit',
            'success_message' => 'Thank you for your submission!',
            'position' => 0,
            'fields' => [],
        ];
    }
}
