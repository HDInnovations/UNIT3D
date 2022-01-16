<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Follow::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id'   => fn () => User::factory()->create()->id,
            'target_id' => fn () => User::factory()->create()->id,
        ];
    }
}
