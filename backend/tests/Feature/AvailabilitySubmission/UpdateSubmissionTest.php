<?php

namespace Tests\Feature\AvailabilitySubmission;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class UpdateSubmissionTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_update_submission_success_start_and_end_dates(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->patchJson('/availability-submissions/1', [
            'start_date' => '2025-12-15',
            'end_date' => '2025-12-29',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_update_submission_success_only_start_date(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->patchJson('/availability-submissions/1', [
            'start_date' => '2025-12-01',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_update_submission_fails_with_empty_start_date_and_end_date(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->patchJson('/availability-submissions/1', [
            'start_date' => '',
            'end_date' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'start_date' => 'The start date field must be a valid date.',
                'end_date' => 'The end date field must be a valid date.',
            ]);
    }

    public function test_update_submission_fails_with_start_date_after_end_date(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->patchJson('/availability-submissions/1', [
            'start_date' => '2025-12-29',
            'end_date' => '2025-12-15',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'start_date' => 'The start date must be before the end date.',
                'end_date' => 'The end date must be after the start date.',
            ]);
    }

    public function test_update_submission_success_special_requests(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->patchJson('/availability-submissions/1', [
            'special_requests' => 'I can start from 6 on weekdays',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_update_submission_success_empty_special_requests(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->patchJson('/availability-submissions/1', [
            'special_requests' => '',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_update_submission_fails_invalid_submission_id(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->patchJson('/availability-submissions/9999', [
            'special_requests' => 'I can double shift.',
        ]);

        $response->assertStatus(404)
            ->assertExactJson([
                'message' => 'Resource not found',
            ]);
    }

    public function test_update_submission_fails_unauthenticated(): void
    {
        $this->createAvailabilitySubmission();

        $response = $this->patchJson('/availability-submissions/1', [
            'special_requests' => 'I can double shift.',
        ]);

        $response->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthenticated',
            ]);
    }
}
