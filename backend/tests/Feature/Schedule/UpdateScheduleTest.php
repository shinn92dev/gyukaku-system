<?php

namespace Tests\Feature\Schedule;

use App\Models\SchedulePeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class UpdateScheduleTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_update_schedule_by_admin_success(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'work_date' => '2026-01-02',
            'type' => 'foh',
            'is_understaffed' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_update_shift_start_time(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'shifts' => [
                [
                    'id' => 1,
                    'shift_start_time' => '2026-01-08 12:00:00',
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_update_shift_start_time_fails_with_invalid_format(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'shifts' => [
                [
                    'id' => 1,
                    'shift_start_time' => '2026-Jan15',
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'shifts.0.shift_start_time' => 'The shifts.0.shift_start_time field must match the format Y-m-d H:i:s.',
            ]);
    }

    public function test_update_shift_work_date(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'shifts' => [
                [
                    'id' => 1,
                    'work_date' => '2026-01-15',
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_update_shift_work_date_fails_with_invalid_format(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'shifts' => [
                [
                    'id' => 1,
                    'work_date' => '2026-Jan15',
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'shifts.0.work_date' => 'The shifts.0.work_date field must be a valid date.',
            ]);
    }

    public function test_update_shift_user_id(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'shifts' => [
                [
                    'id' => 1,
                    'user_id' => 3,
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_update_shift_user_id_fails_if_user_id_doesnt_exist(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'shifts' => [
                [
                    'id' => 1,
                    'user_id' => -9999,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'shifts.0.user_id' => 'The selected shifts.0.user_id is invalid.',
            ]);
    }

    public function test_update_schedule_fails_when_not_admin(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: false);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'shifts' => [
                [
                    'id' => 1,
                    'user_id' => 3,
                ],
            ],
        ]);

        $response->assertStatus(403)
            ->assertExactJson(['message' => 'Forbidden - Admin access required.']);
    }

    public function test_update_schedule_fails_when_not_authenticated(): void
    {
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/1', [
            'shifts' => [
                [
                    'id' => 1,
                    'user_id' => 3,
                ],
            ],
        ]);

        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }

    public function test_update_schedule_fails_when_schedule_id_doesnt_exist(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->patchJson('/schedules/9999', [
            'shifts' => [
                [
                    'id' => 1,
                    'user_id' => 3,
                ],
            ],
        ]);

        $response->assertStatus(404)
            ->assertExactJson(['message' => 'Resource not found']);
    }
}
