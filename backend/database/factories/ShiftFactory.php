<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $workDate = fake()->date();

        return [
            'schedule_id' => Schedule::factory(),
            'user_id' => User::factory(),
            'work_date' => $workDate,
            'shift_start_time' => $workDate . ' ' . fake()->time('H:i:s'),
        ];
    }
}
