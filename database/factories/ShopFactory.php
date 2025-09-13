<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    public function definition(): array
    {
        $shopTypes = [
            'カフェ',
            'ベーカリー',
            'コーヒーショップ',
            'レストラン',
            'ファミリーレストラン'
        ];

        $morningOpeningTimes = [
            '05:00',
            '05:30',
            '06:00',
            '06:30',
            '07:00',
            '07:30',
            '08:00'
        ];

        return [
            'name' => fake()->randomElement($shopTypes) . ' ' . fake()->company(),
            'description' => '朝活にぴったりの' . fake()->randomElement($shopTypes) . 'です。' . fake()->sentence(),
            'address' => fake()->address(),
            'contact' => fake()->phoneNumber(),
            'sns_links' => json_encode([
                'twitter' => 'https://twitter.com/' . fake()->userName(),
                'instagram' => 'https://instagram.com/' . fake()->userName(),
            ]),
            'opening_time' => fake()->randomElement($morningOpeningTimes),
            'closing_time' => fake()->time('21:00'),
        ];
    }
}
