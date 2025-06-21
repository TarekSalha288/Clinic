<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Day>
 */
class DayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
{
    return [
        'available_days' => $this->faker->dayOfWeek(), // Fallback (not used in sequence)
    ];
}

public function saturdayToThursday()
{
    $days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
    static $index = 0;

    return $this->state(function () use (&$index, $days) {
        return [
            'available_days' => $days[$index++ % count($days)],
        ];
    });
}
}
