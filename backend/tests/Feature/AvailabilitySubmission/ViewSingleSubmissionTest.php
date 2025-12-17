<?php

namespace Tests\Feature\AvailabilitySubmission;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class ViewSingleSubmissionTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_view_submission_by_authenticated_user(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->getJson('/availability-submissions/1');

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_view_submission_fails_not_found(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->getJson('/availability-submissions/555555');

        $response->assertStatus(404)
            ->assertExactJson(['message' => 'Resource not found']);
    }

    public function test_view_submission_fails_when_unauthenticated(): void
    {
        $user = $this->createUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->getJson('/availability-submissions/1');

        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }
}
