<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => 'ALI-'.Str::upper(Str::random(8)),
            'type' => fake()->randomElement(['courant', 'epargne', 'mineur']),
            'balance' => fake()->randomFloat(2, 0, 10000),
            'overdraft_limit' => fake()->optional()->randomFloat(2, 0, 2000),
            'interest_rate' => fake()->optional()->randomFloat(2, 1, 5),
            'status' => 'active',
            'monthly_withdrawals' => 0,
        ];

    }
}
