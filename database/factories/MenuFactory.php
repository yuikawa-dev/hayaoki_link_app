<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'name' => fake()->words(3, true),
            'price' => fake()->numberBetween(300, 2000),
            'description' => fake()->sentence(),
            'image_path' => 'menus/' . fake()->uuid() . '.jpg',
        ];
    }
}
