<?php

namespace Tests\Feature\AvailabilitySubmission;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class ViewAllSubmissionsTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_view_all_submissions_fail_while_unauthenticated(): void
    {
        $response = $this->getJson('/availability-submissions');
        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }

    public function test_view_all_submissions_as_admin_returns_all_submissions(): void
    {
        $admin = $this->createUser(isAdmin: true);
        $user = $this->createUser();

        $userSubmission = $this->createAvailabilitySubmission(user: $user);
        $adminSubmission = $this->createAvailabilitySubmission(user: $admin);

        $this->actingAs($admin, 'sanctum');

        $response = $this->getJson('/availability-submissions');
        $response->assertStatus(200)
            ->assertExactJsonStructure(['message', 'data'])
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $userSubmission->id])
            ->assertJsonFragment(['id' => $adminSubmission->id]);
    }

    public function test_view_all_submissions_as_regular_user_returns_only_own_submissions(): void
    {
        $user = $this->createUser();

        $userSubmission = $this->createAvailabilitySubmission(user: $user);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/availability-submissions');
        $response->assertStatus(200)
            ->assertExactJsonStructure(['message', 'data'])
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $userSubmission->id]);
    }

    public function test_view_all_submissions_returns_empty_array_when_no_submissions(): void
    {
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/availability-submissions');
        $response->assertStatus(200)
            ->assertExactJson([
                'message' => 'Availability submissions retrieved successfully',
                'data' => [],
            ]);
    }

    public function test_view_all_submissions_returns_correct_data_structure(): void
    {
        $user = $this->createUser();
        $submission = $this->createAvailabilitySubmission(user: $user);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/availability-submissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'start_date',
                        'end_date',
                        'special_requests',
                        'availabilities' => [
                            '*' => ['id', 'availability_submission_id', 'work_date', 'lunch', 'dinner'],
                        ],
                    ],
                ],
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $submission->id)
            ->assertJsonPath('data.0.user_id', $user->id);
    }
}
