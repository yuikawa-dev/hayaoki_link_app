<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'image_path' => 'shops/' . fake()->uuid() . '.jpg',
            'image_type' => fake()->randomElement(['exterior', 'interior', 'menu', 'atmosphere']),
        ];
    }
}
