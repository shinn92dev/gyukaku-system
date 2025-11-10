<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Availability',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: '1'),
        new OA\Property(property: 'availability_submission_id', type: 'integer', example: 5),
        new OA\Property(property: 'work_date', type: 'string', format: 'date', example: '2025-01-15'),
        new OA\Property(property: 'lunch', type: 'boolean', example: true),
        new OA\Property(property: 'dinner', type: 'boolean', example: false),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
    ]
)]
class AvailabilitySchema {}
