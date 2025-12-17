<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestUser;

class RegisterTest extends TestCase
{
    use CreatesTestUser, RefreshDatabase;

    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone_number' => fake()->phoneNumber(),
            'date_of_birth' => fake()->date(),
            'hire_date' => fake()->date(),
            'role_ids' => [1],
        ], $overrides);
    }

    public function test_register_by_admin(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = $this->createUser(isAdmin: true);
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/register', $this->registrationData());
        $response->assertStatus(201)
            ->assertExactJsonStructure(['message', 'user']);
    }

    public function test_register_fail_by_non_admin(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/register', $this->registrationData());
        $response->assertStatus(403)
            ->assertExactJson(['message' => 'Forbidden - Admin access required.']);
    }

    public function test_register_fails_when_unauthenticated(): void
    {
        $response = $this->postJson('/register', $this->registrationData());
        $response->assertStatus(401)
            ->assertExactJson(['message' => 'Unauthenticated']);
    }

    public function test_register_fails_without_required_fields(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = $this->createUser(isAdmin: true);
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/register', []);
        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'email' => 'The email field is required.',
                'username' => 'The username field is required.',
                'password' => 'The password field is required.',
                'first_name' => 'The first name field is required.',
                'last_name' => 'The last name field is required.',
                'phone_number' => 'The phone number field is required.',
                'date_of_birth' => 'The date of birth field is required.',
                'hire_date' => 'The hire date field is required.',
                'role_ids' => 'Please include at least one role.',
            ]);
    }

    public function test_register_fails_passwords_dont_match(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = $this->createUser(isAdmin: true);
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/register',
            $this->registrationData([
                'password' => 'password',
                'confirm_password' => 'different_password',
            ]));
        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['password' => 'Passwords do not match.']);
    }

    public function test_register_fails_existing_email(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = $this->createUser([
            'email' => 'existing@gmail.com',
        ], isAdmin: true);
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/register',
            $this->registrationData([
                'email' => 'existing@gmail.com',
            ]));
        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['email' => 'The email has already been taken.']);
    }

    public function test_register_fails_existing_username(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = $this->createUser([
            'username' => 'VC9999',
        ], isAdmin: true);
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/register',
            $this->registrationData([
                'username' => 'VC9999',
            ]));
        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['username' => 'The username has already been taken.']);
    }

    public function test_register_fails_password_less_than_6_characters(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = $this->createUser(isAdmin: true);
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/register',
            $this->registrationData([
                'password' => '123',
                'confirm_password' => '123',
            ]));
        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['password' => 'The password field must be at least 6 characters.']);
    }

    public function test_register_fails_with_invalid_date_format(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $user = $this->createUser(isAdmin: true);
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/register', $this->registrationData([
            'date_of_birth' => 'may 2-1998',
            'hire_date' => 'July-07-1998',
        ]));

        $response->assertStatus(422)
            ->assertExactJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors([
                'hire_date' => 'The hire date field must be a valid date',
                'date_of_birth' => 'The date of birth field must be a valid date.',
            ]);
    }
}
