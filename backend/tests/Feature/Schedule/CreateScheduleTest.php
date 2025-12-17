<?php

namespace Tests\Feature\Schedule;

use App\Models\SchedulePeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class CreateScheduleTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    private function scheduleData(array $overrides = []): array
    {
        $workDate = Carbon::today()->format('Y-m-d');
        $users = User::factory(3)->create();

        return array_merge([
            'work_date' => $workDate,
            'type' => fake()->randomElement(['foh', 'boh']),
            'shifts' => [
                [
                    'work_date' => $workDate,
                    'user_id' => $users[0]->id,
                    'shift_start_time' => $workDate . ' 10:00:00',
                ],
                [
                    'work_date' => $workDate,
                    'user_id' => $users[1]->id,
                    'shift_start_time' => $workDate . ' 12:00:00',
                ],
                [
                    'work_date' => $workDate,
                    'user_id' => $users[2]->id,
                    'shift_start_time' => $workDate . ' 17:00:00',
                ],
            ],
        ], $overrides);
    }

    public function test_admin_can_create_schedule_with_shifts(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);

        SchedulePeriod::factory()->create(['is_current' => true]);
        $data = $this->scheduleData();

        $response = $this->postJson('/schedules', $data);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
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
            ]);
    }

    public function test_create_schedule_fails_when_unauthenticated(): void
    {
        $data = $this->scheduleData(['shifts' => []]);
        SchedulePeriod::factory()->create(['is_current' => true]);

        $response = $this->postJson('/schedules', $data);
        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }

    public function test_create_schedule_fails_when_user_is_not_admin(): void
    {
        $this->actAsAuthenticatedUser();

        $data = $this->scheduleData(['shifts' => []]);
        SchedulePeriod::factory()->create(['is_current' => true]);

        $response = $this->postJson('/schedules', $data);
        $response->assertStatus(403)
            ->assertExactJson(['message' => 'Forbidden - Admin access required.']);
    }

    public function test_create_schedule_fails_without_required_fields(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);

        SchedulePeriod::factory()->create(['is_current' => true]);

        $response = $this->postJson('/schedules');
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'work_date' => 'The work date field is required.',
                'type' => 'The type field is required.',
                'shifts' => 'The shifts field is required.',
            ]);
    }
}
