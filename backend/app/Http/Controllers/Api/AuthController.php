<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(path: '/login', tags: ['Authentication'], summary: 'Login an existing user')]
    #[OA\RequestBody(required: true, description: 'login', content: [
        new OA\JsonContent(
            required: ['username', 'password'],
            properties: [
                new OA\Property(property: 'username', type: 'string', example: 'admin'),
                new OA\Property(property: 'password', type: 'string', example: 'password'),
            ]
        ),
    ])]
    #[OA\Response(
        response: 200,
        description: 'Login Success - returns user data and authentication token',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'user', ref: '#/components/schemas/User', type: 'object'),
                new OA\Property(property: 'token', type: 'string', example: '1|abc123token...'),
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Invalid credentials'),
            ]
        )
    )]
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Find user by username
        $user = User::where('username', $credentials['username'])->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('roles'),
            'token' => $token,
        ]);
    }

    #[OA\Post(
        path: '/register',
        tags: ['Authentication'],
        summary: 'Register new user',
        security: [['bearerAuth' => []]],
    )]
    #[OA\RequestBody(required: true, description: 'register', content: [
        new OA\JsonContent(
            required: ['email', 'username', 'password', 'password_confirmation', 'first_name', 'last_name', 'phone_number', 'date_of_birth', 'hire_date', 'role_ids'],
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                new OA\Property(property: 'username', type: 'string', example: 'VC1234'),
                new OA\Property(property: 'password', type: 'string', example: 'password123'),
                new OA\Property(property: 'password_confirmation', type: 'string', example: 'password123'),
                new OA\Property(property: 'first_name', type: 'string', example: 'John'),
                new OA\Property(property: 'last_name', type: 'string', example: 'Doe'),
                new OA\Property(property: 'phone_number', type: 'string', example: '123-456-7890'),
                new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', example: '1998-01-25'),
                new OA\Property(property: 'hire_date', type: 'string', format: 'date', example: '2025-10-30'),
                new OA\Property(
                    property: 'role_ids',
                    type: 'array',
                    items: new OA\Items(type: 'integer'),
                    example: [1, 3],
                ),
            ]
        ),
    ])]
    #[OA\Response(
        response: 201,
        description: 'Registration Success - returns the new user data',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'User created successfully.'),
                new OA\Property(property: 'user', ref: '#/components/schemas/User', type: 'object'),
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthenticated',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
            ]
        )
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden - admin access required',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Forbidden - Admin access required.'),
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error - returns field-specific error messages. Common errors: duplicate email/username, passowrd mismatch, missing required fields, invalid role IDs.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'The email has already been taken.'),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    example: [
                        'email' => ['The email has already been taken.'],
                        'password' => ['Passwords do not match.'],
                    ]
                ),
            ]
        )
    )]
    public function register(RegisterRequest $request): JsonResponse // TODO:: only authorize admins to register new users
    {
        $validated = $request->validated();

        $user = User::create([
            'email' => $validated['email'],
            'username' => $validated['username'], // TODO:: make default 'VC' + last 4 digits of phone number?
            'password' => $validated['password'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' => $validated['phone_number'],
            'date_of_birth' => $validated['date_of_birth'],
            'hire_date' => $validated['hire_date'],
        ]);

        $user->roles()->attach($validated['role_ids']);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user->load('roles'),
        ], 201);
    }

    #[OA\Post(
        path: '/logout',
        tags: ['Authentication'],
        summary: 'Logout user from all devices',
        security: [['bearerAuth' => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'Logout success',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully from all devices.'),
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthenticated',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
            ]
        )
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully from all devices',
        ]);
    }
}
