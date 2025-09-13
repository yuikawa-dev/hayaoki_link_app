<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => fake()->paragraph(),
            'posted_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
