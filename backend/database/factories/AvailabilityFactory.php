<?php

namespace Database\Factories;

use App\Models\AvailabilitySubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'availability_submission_id' => AvailabilitySubmission::factory(),
            'work_date' => fake()->date('Y-m-d'),
            'lunch' => fake()->boolean(),
            'dinner' => fake()->boolean(),
        ];
    }
}
