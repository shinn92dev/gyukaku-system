<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SchedulePeriod',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2025-01-15'),
        new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2025-01-30'),
        new OA\Property(property: 'is_current', type: 'boolean', example: true),
        new OA\Property(property: 'is_published', type: 'boolean', example: true),
        new OA\Property(
            property: 'schedules',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Schedule')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
    ]
)]
class SchedulePeriodSchema {}
