<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Schedule',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'schedule_period_id', type: 'integer', example: 1),
        new OA\Property(property: 'work_date', type: 'string', format: 'date', example: '2025-01-15'),
        new OA\Property(property: 'type', type: 'string', enum: ['foh', 'boh', 'mgr'], example: 'foh'),
        new OA\Property(property: 'is_understaffed', type: 'boolean', example: true),
        new OA\Property(
            property: 'shifts',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Shift')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
    ]
)]
class ScheduleSchema {}
