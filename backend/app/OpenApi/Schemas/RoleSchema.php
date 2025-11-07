<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Role', // ← #/components/schemas/Role
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'code', type: 'string', example: 'svr'),
        new OA\Property(property: 'display_name', type: 'string', example: 'Server'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(
            property: 'pivot',
            properties: [
                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                new OA\Property(property: 'role_id', type: 'integer', example: 3),
            ],
            type: 'object',
        ),
    ]
)]
class RoleSchema {}
