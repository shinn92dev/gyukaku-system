<?php

namespace Tests\Feature\AvailabilitySubmission;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class DeleteSubmissionTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_delete_submission_by_admin(): void
    {
        $user = $this->actAsAuthenticatedUser(isAdmin: true);
        $this->createAvailabilitySubmission($user);

        $response = $this->deleteJson('/availability-submissions/1');

        $response->assertStatus(200)
            ->assertExactJson([
                'message' => 'Availability submission deleted successfully',
            ]);
    }

    public function test_delete_submission_fails_by_non_admin(): void
    {
        $user = $this->actAsAuthenticatedUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->deleteJson('/availability-submissions/1');

        $response->assertStatus(403)
            ->assertExactJson([
                'message' => 'Forbidden - Admin access required.',
            ]);
    }

    public function test_delete_submission_fails_not_found(): void
    {
        $user = $this->actAsAuthenticatedUser(isAdmin: true);
        $this->createAvailabilitySubmission($user);

        $response = $this->deleteJson('/availability-submissions/9000');

        $response->assertStatus(404)
            ->assertExactJson([
                'message' => 'Resource not found',
            ]);
    }

    public function test_delete_submission_fails_when_unauthenticated(): void
    {
        $user = $this->createUser();
        $this->createAvailabilitySubmission($user);

        $response = $this->deleteJson('/availability-submissions/1');

        $response->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthenticated',
            ]);
    }
}
