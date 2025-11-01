<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User', // ← #/components/schemas/User
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
        new OA\Property(property: 'username', type: 'string', example: 'VC1234'),
        new OA\Property(property: 'first_name', type: 'string', example: 'John'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Doe'),
        new OA\Property(property: 'phone_number', type: 'string', example: '123-456-7890'),
        new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', example: '1998-01-25'),
        new OA\Property(property: 'hire_date', type: 'string', format: 'date', example: '2019-09-15'),
        new OA\Property(property: 'termination_date', type: 'string', format: 'date', nullable: true, example: null),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'is_admin', type: 'boolean', example: false),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(
            property: 'roles',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Role'),
        ),
    ]
)]
class UserSchema {}
