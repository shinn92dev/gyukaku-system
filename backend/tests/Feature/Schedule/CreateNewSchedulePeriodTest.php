<?php

namespace Tests\Feature\Schedule;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class CreateNewSchedulePeriodTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_create_new_schedule_period_success(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);

        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(13);

        $response = $this->postJson('/schedules/new-period', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_create_new_schedule_period_fails_when_unauthenticated(): void
    {
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(13);

        $response = $this->postJson('/schedules/new-period', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);

        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }

    public function test_create_new_schedule_period_fails_when_not_admin(): void
    {
        $this->actAsAuthenticatedUser();

        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(13);

        $response = $this->postJson('/schedules/new-period', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);

        $response->assertStatus(403)
            ->assertExactJson(['message' => 'Forbidden - Admin access required.']);
    }

    public function test_create_new_schedule_period_fails_when_start_date_after_end_date(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);

        $endDate = Carbon::today();
        $startDate = $endDate->copy()->addDays(13);

        $response = $this->postJson('/schedules/new-period', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'end_date' => 'The end date field must be a date after start date.',
            ]);
    }

    public function test_create_new_schedule_period_fails_with_no_required_fields(): void
    {
        $this->actAsAuthenticatedUser(isAdmin: true);

        $response = $this->postJson('/schedules/new-period', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'start_date' => 'The start date field is required.',
                'end_date' => 'The end date field is required.',
            ]);
    }
}
