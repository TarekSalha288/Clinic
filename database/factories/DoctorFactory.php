<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Department;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->doctor(),
            'department_id' => Department::factory(),
            'bio' => $this->faker->realText(200),
            'subscription' => rand(5, 10) * 1000000,
            'price_of_examination' => rand(4, 8) * 10000,
        ];
    }
}