<?php

namespace Tests\Feature\Schedule;

use App\Models\SchedulePeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class ViewSingleScheduleTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_view_single_schedule_success(): void
    {
        $this->actAsAuthenticatedUser();
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->get('/schedules/1');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
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
                            'clock_in_at',
                            'clock_out_at',
                            'break_start_at',
                            'break_end_at',
                        ],
                    ],
                ],
            ]);
    }

    public function test_view_single_schedule_fails_when_unauthenticated(): void
    {
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->get('/schedules/1');
        $response->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthenticated',
            ]);
    }

    public function test_update_schedule_fails_when_schedule_id_doesnt_exist(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->getJson('/schedules/9999');

        $response->assertStatus(404)
            ->assertExactJson(['message' => 'Schedule not found']);
    }
}
