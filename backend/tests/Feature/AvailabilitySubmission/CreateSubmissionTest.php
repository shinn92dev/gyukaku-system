<?php

namespace Tests\Feature\AvailabilitySubmission;

use Carbon\CarbonPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class CreateSubmissionTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    private function submissionData(array $overrides = []): array
    {
        $startDate = Carbon::today()->addDays(fake()->numberBetween(0, 28));
        $endDate = $startDate->copy()->addDays(14);
        $period = CarbonPeriod::create($startDate, $endDate);

        $availabilities = [];
        foreach ($period as $date) {
            $availabilities[] = [
                'work_date' => $date->format('Y-m-d'),
                'lunch' => fake()->boolean(),
                'dinner' => fake()->boolean(),
            ];
        }

        return array_merge([
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'special_requests' => fake()->optional()->sentence(),
            'availabilities' => $availabilities,
        ], $overrides);
    }

    public function test_user_can_create_submission(): void
    {
        $this->actAsAuthenticatedUser();
        $data = $this->submissionData();

        $response = $this->postJson('/availability-submissions', $data);

        $expectedNumDays = count($data['availabilities']);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'start_date',
                    'end_date',
                    'special_requests',
                    'availabilities' => [
                        '*' => [
                            'id',
                            'availability_submission_id',
                            'work_date',
                            'lunch',
                            'dinner',
                        ],
                    ],
                ],
            ])
            ->assertJsonCount($expectedNumDays, 'data.availabilities');
    }

    public function test_create_submission_fails_without_required_fields(): void
    {
        $this->actAsAuthenticatedUser();

        $response = $this->postJson('/availability-submissions', []);
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'start_date' => 'The start date field is required.',
                'end_date' => 'The end date field is required.',
                'availabilities' => 'The availabilities field is required.',
            ]);
    }

    public function test_create_submission_fails_with_invalid_date_format(): void
    {
        $this->actAsAuthenticatedUser();

        $data = $this->submissionData([
            'start_date' => 'some-invalid-format',
        ]);

        $response = $this->postJson('/availability-submissions', $data);
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['start_date' => 'The start date field must be a valid date.']);
    }

    public function test_create_submission_fails_when_end_date_before_start_date(): void
    {
        $this->actAsAuthenticatedUser();

        $data = $this->submissionData([
            'start_date' => '2025-12-29',
            'end_date' => '2025-12-15',
        ]);

        $response = $this->postJson('/availability-submissions', $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date' => 'The end date field must be a date after start date.']);
    }
}
