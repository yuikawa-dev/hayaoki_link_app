<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->paragraph(),
            'address' => fake()->address(),
            'contact' => fake()->phoneNumber(),
            'sns_links' => json_encode([
                'twitter' => 'https://twitter.com/' . fake()->userName(),
                'instagram' => 'https://instagram.com/' . fake()->userName(),
            ]),
            'opening_time' => fake()->time('05:00'),
            'closing_time' => fake()->time('11:00'),
        ];
    }
}
