<?php

namespace Tests\Feature\Schedule;

use App\Models\SchedulePeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class DeleteScheduleTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_delete_by_admin_success(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->deleteJson('/schedules/1');
        $response->assertStatus(200)
            ->assertExactJson(['message' => 'Schedule deleted successfully']);
    }

    public function test_delete_fails_when_not_admin(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: false);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->deleteJson('/schedules/1');
        $response->assertStatus(403)
            ->assertExactJson(['message' => 'Forbidden - Admin access required.']);
    }

    public function test_delete_fails_when_not_authenticated(): void
    {
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->deleteJson('/schedules/1');
        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }

    public function test_delete_fails_when_schedule_id_doesnt_exist(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);
        SchedulePeriod::factory()->withSchedules(withShifts: true)->create();

        $response = $this->deleteJson('/schedules/99999');
        $response->assertStatus(404)
            ->assertExactJson(['message' => 'Resource not found']);
    }
}
