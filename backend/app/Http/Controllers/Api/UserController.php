<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/users',
        tags: ['Users'],
        summary: 'Get all users with their roles',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'All users with roles retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Users retrieved successfully'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/User')
                ),
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
    public function index(): JsonResponse
    {
        $users = User::with('roles')->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: '/users/{id}',
        tags: ['Users'],
        summary: 'Display data for the specified user',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'User ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'User retrieved successfully',
        content: new OA\JsonContent(ref: '#/components/schemas/User')
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
        content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
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
    public function show(string $id): JsonResponse
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(
        path: '/users/{id}',
        tags: ['Users'],
        summary: 'Update fields for the specified user',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'User ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'User fields updated successfully',
        content: new OA\JsonContent(ref: '#/components/schemas/User')
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
        content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error - invalid input data',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'The email has already been taken. (and 1 more error)'),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    example: [
                        'email' => ['The email has already been taken.'],
                        'phone_number' => ['The phone number field must be valid.'],
                    ]
                ),
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
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $user->update($request->validated());

        $user->refresh()->load('roles');

        return response()->json([
            'message' => "User with id $id updated successfully",
            'data' => $user,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: '/users/{id}',
        tags: ['Users'],
        summary: 'Delete the specified user from the database',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'User ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'User successfully deleted from the database',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'User with id 1 deleted successfully'),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
        content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
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
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => "User with id $id deleted successfully",
        ], 200);
    }
}
