<?php

namespace Tests\Feature\Schedule;

use App\Models\SchedulePeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class ViewAllSchedulesTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_view_all_schedules_by_admin(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);

        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->getJson('/schedules');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'work_date',
                        'type',
                        'is_understaffed',
                        'schedule_period_id',
                        'shifts' => [
                            '*' => [
                                'id',
                                'user_id',
                                'schedule_id',
                                'work_date',
                                'shift_start_time',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function test_view_all_schedules_no_schedules_empty_array(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);

        $response = $this->getJson('/schedules');
        $response->assertStatus(200)
            ->assertExactJson([
                'message' => 'Schedules retrieved successfully',
                'data' => [],
            ]);
    }

    public function test_view_all_schedules_fails_when_not_admin(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: false);

        $response = $this->getJson('/schedules');
        $response->assertStatus(403)
            ->assertExactJson(['message' => 'Forbidden - Admin access required.']);
    }

    public function test_view_all_schedules_fails_when_unauthenticated(): void
    {
        $response = $this->getJson('/schedules');
        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }
}
