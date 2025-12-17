<?php

namespace Tests\Feature\Schedule;

use App\Models\SchedulePeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class GetScheduleTodayTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_get_todays_schedule_by_authenticated_user(): void
    {
        $this->actAsAuthenticatedUser();

        SchedulePeriod::factory()->withSchedules(withShifts: true)->create([
            'start_date' => today(),
            'end_date' => today()->addDays(5),
        ]);

        $response = $this->getJson('/schedules/today');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'schedule_period_id',
                        'work_date',
                        'type',
                        'is_understaffed',
                        'shifts' => [
                            '*' => [
                                'id',
                                'schedule_id',
                                'user_id',
                                'work_date',
                                'shift_start_time',
                                'user' => [
                                    'id',
                                    'username',
                                    'email',
                                    'first_name',
                                    'last_name',
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJsonPath('data.0.work_date', today()->format('Y-m-d'));
    }

    public function test_get_todays_schedule_fails_when_unauthenticated(): void
    {
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create([
            'start_date' => today(),
            'end_date' => today()->addDays(5),
        ]);

        $response = $this->getJson('/schedules/today');

        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }

    public function test_get_todays_schedule_success_empty_array_when_no_schedules_set_for_today(): void
    {
        $this->actAsAuthenticatedUser();

        SchedulePeriod::factory()->withSchedules(withShifts: true)->create([
            'start_date' => today()->addDays(10),
            'end_date' => today()->addDays(20),
        ]);

        $response = $this->getJson('/schedules/today');

        $response->assertStatus(200)
            ->assertExactJson([
                'message' => "Today's schedule retrieved successfully",
                'data' => [],
            ]);
    }
}
