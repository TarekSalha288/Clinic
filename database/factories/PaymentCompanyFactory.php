<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentCompany>
 */
class PaymentCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'user_id' => User::factory(),
            'phone_number' => $this->faker->unique()->phoneNumber(),
            'company_name' => $this->faker->randomElement(['Syriatel_cash', 'MTN_Cash']),
            'balance' => rand(2, 3) * 100000
        ];
    }
}
