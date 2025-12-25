<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Shift',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'schedule_id', type: 'integer', example: 15),
        new OA\Property(property: 'user_id', type: 'integer', example: 3),
        new OA\Property(property: 'work_date', type: 'string', format: 'date', example: '2025-01-15'),
        new OA\Property(property: 'shift_start_time', type: 'string', format: 'date-time', example: '2025-01-15T09:00:00.000000Z'),
        new OA\Property(property: 'clock_in_at', type: 'string', format: 'date-time', nullable: true, example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'clock_out_at', type: 'string', format: 'date-time', nullable: true, example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'break_start_at', type: 'string', format: 'date-time', nullable: true, example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'break_end_at', type: 'string', format: 'date-time', nullable: true, example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
    ]
)]
class ShiftSchema {}
