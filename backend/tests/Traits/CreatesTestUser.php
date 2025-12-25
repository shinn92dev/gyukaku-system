<?php

namespace Tests\Traits;

use App\Models\AvailabilitySubmission;
use App\Models\User;

trait CreatesTestUser
{
    protected function createUser(array $attributes = [], bool $isAdmin = false)
    {
        if ($isAdmin) {
            return User::factory()->create(array_merge([
                'is_admin' => true,
            ], $attributes));
        }

        return User::factory()->create($attributes);
    }

    protected function actAsAuthenticatedUser(array $attributes = [], bool $isAdmin = false)
    {
        $user = $this->createUser($attributes, $isAdmin);

        $this->actingAs($user, 'sanctum');

        return $user;
    }

    protected function createAvailabilitySubmission(?User $user = null, array $attributes = []): AvailabilitySubmission
    {
        $user ??= $this->createUser();

        return AvailabilitySubmission::factory()
            ->withAvailabilities()
            ->create(array_merge(['user_id' => $user->id], $attributes));
    }
}
