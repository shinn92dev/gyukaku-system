<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phoneNumber = fake()->numerify('###-###-####');

        return [
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->numerify('VC####'),
            'password' => static::$password ??= Hash::make('password'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => $phoneNumber,
            'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
            'hire_date' => fake()->dateTimeThisYear()->format('Y-m-d'),
            'is_admin' => false,
            'is_active' => true,
        ];
    }
}
