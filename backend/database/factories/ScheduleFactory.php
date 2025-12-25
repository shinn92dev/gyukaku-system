<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\SchedulePeriod;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedule_period_id' => SchedulePeriod::factory(),
            'work_date' => fake()->date(),
            'type' => fake()->randomElement(['foh', 'boh']),
            'is_understaffed' => false,
        ];
    }

    /**
     * Add shifts to the schedule
     */
    public function withShifts(int $count = 5): static
    {
        return $this->afterCreating(function (Schedule $schedule) use ($count) {
            Shift::factory($count)->create([
                'schedule_id' => $schedule->id,
                'work_date' => $schedule->work_date,
            ]);
        });
    }
}
