<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\SchedulePeriod;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchedulePeriod>
 */
class SchedulePeriodFactory extends Factory
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
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'is_current' => false,
            'is_published' => false,
        ];
    }

    public function withSchedules(bool $withShifts = false): static
    {
        return $this->afterCreating(function (SchedulePeriod $schedulePeriod) use ($withShifts) {
            $dateRange = CarbonPeriod::create(
                $schedulePeriod->start_date,
                $schedulePeriod->end_date,
            );

            foreach ($dateRange as $date) {
                $factory = Schedule::factory();

                // Optionally add shifts
                if ($withShifts) {
                    $factory = $factory->withShifts();
                }

                $factory->create([
                    'schedule_period_id' => $schedulePeriod->id,
                    'work_date' => $date->format('Y-m-d'),
                    'type' => fake()->randomElement(['foh', 'boh']),
                    'is_understaffed' => false,
                ]);
            }
        });
    }
}
