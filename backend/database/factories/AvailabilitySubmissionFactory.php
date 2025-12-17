<?php

namespace Database\Factories;

use App\Models\Availability;
use App\Models\AvailabilitySubmission;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailabilitySubmission>
 */
class AvailabilitySubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::today()->addDays(fake()->numberBetween(0, 28));
        $endDate = $startDate->copy()->addDays(14);

        return [
            'user_id' => User::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'special_requests' => fake()->optional()->sentence(),
        ];
    }

    public function withAvailabilities(): static
    {
        return $this->afterCreating(function (AvailabilitySubmission $submission) {
            $period = CarbonPeriod::create(
                $submission->start_date,
                $submission->end_date,
            );

            foreach ($period as $date) {
                Availability::factory()->create([
                    'availability_submission_id' => $submission->id,
                    'work_date' => $date->format('Y-m-d'),
                ]);
            }
        });
    }
}
