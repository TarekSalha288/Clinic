<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['Male', 'Female']);
        $birthDate = $this->faker->dateTimeBetween('-80 years', '-18 years');
        $age = $birthDate->diff(now())->y;

        return [
            'user_id' => User::factory()->patient(), // Creates a user with 'patient' role
            'first_name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'birth_date' => $birthDate->format('Y-m-d'),
            'gender' => $gender,
            'age' => $age,
            'blood_type' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'chronic_diseases' => $this->faker->optional(0.5)->sentence(),
            'medication_allergies' => $this->faker->optional(0.5)->sentence(),
            'permanent_medications' => $this->faker->optional(0.5)->sentence(),
            'previous_surgeries' => $this->faker->optional(0.5)->sentence(),
            'previous_illnesses' => $this->faker->optional(0.5)->sentence(),
            'honest_score' => $this->faker->randomFloat(2, 50, 100),
        ];
    }
}
