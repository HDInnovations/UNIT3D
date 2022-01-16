<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use App\Models\PersonalFreeleech;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalFreeleechFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PersonalFreeleech::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
        ];
    }
}
