<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('+1 week', '+1 month');
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'image_path' => fake()->randomElement([null, 'events/sample-event-image.jpg']),
            'start_time' => $startTime,
            'end_time' => fake()->dateTimeBetween($startTime, $startTime->format('Y-m-d H:i:s') . ' +3 hours'),
            'location' => fake()->address(),
            'requirements' => fake()->sentences(3, true),
            'fee' => fake()->randomElement([0, 500, 1000, 2000]),
            'contact' => fake()->phoneNumber(),
            'capacity' => fake()->numberBetween(5, 50),
        ];
    }
}
