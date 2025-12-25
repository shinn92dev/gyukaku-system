<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class LoginTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    public function test_login_with_valid_credentials(): void
    {
        $user = $this->createUser(isAdmin: true);

        $response = $this->postJson('/login', [
            'username' => $user->username,
            'password' => 'password', // default password
        ]);

        $response->assertStatus(200)
            ->assertExactJsonStructure([
                'token',
                'user' => [
                    'created_at',
                    'date_of_birth',
                    'email',
                    'first_name',
                    'hire_date',
                    'id',
                    'is_active',
                    'is_admin',
                    'last_name',
                    'phone_number',
                    'roles',
                    'termination_date',
                    'updated_at',
                    'username',
                ],
            ]);
    }

    public function test_login_with_incorrect_credentials(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/login', [
            'username' => $user->username,
            'password' => 'this_passowrd_is_incorrect',
        ]);

        $response->assertStatus(401)
            ->assertExactJsonStructure(['message']);
    }

    public function test_login_fails_without_username(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/login', [
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'username' => 'The username field is required',
            ]);
    }

    public function test_login_fails_without_password(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/login', [
            'username' => $user->username,
        ]);

        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'password' => 'The password field is required',
            ]);
    }

    public function test_login_with_missing_username_and_password(): void
    {
        $response = $this->postJson('/login', []);

        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'username' => 'The username field is required',
                'password' => 'The password field is required',
            ]);
    }
}
