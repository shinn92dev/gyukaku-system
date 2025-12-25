<?php

namespace Tests\Feature\Schedule;

use App\Models\SchedulePeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class GetScheduleByDateRangeTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_get_schedule_by_range_by_authenticated_user(): void
    {
        $this->actAsAuthenticatedUser();

        SchedulePeriod::factory()->withSchedules(withShifts: true)->create([
            'start_date' => '2025-12-01',
            'end_date' => '2025-12-31',
        ]);

        // december schedules
        $response = $this->getJson('/schedules/range?start_date=2025-12-01&end_date=2025-12-31');
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
            ]);
    }

    public function test_get_schedule_by_date_range_no_schedules_within_range(): void
    {
        $this->actAsAuthenticatedUser();

        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->getJson('/schedules/range?start_date=2030-12-01&end_date=2031-12-14');
        $response->assertStatus(200)
            ->assertExactJson([
                'message' => 'Schedules retrieved successfully',
                'data' => [],
            ]);
    }

    public function test_get_schedule_by_date_fail_without_required_fields(): void
    {
        $this->actAsAuthenticatedUser();

        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->getJson('/schedules/range');
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'start_date' => 'The start date field is required.',
                'end_date' => 'The end date field is required.',
            ]);
    }

    public function test_get_schedule_by_date_fail_when_end_date_before_start_date(): void
    {
        $this->actAsAuthenticatedUser();

        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->getJson('/schedules/range?start_date=2025-12-01&end_date=2025-11-01');
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'end_date' => 'The end date field must be a date after or equal to start date.',
            ]);
    }
}
