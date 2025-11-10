<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AvailabilitySubmission',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'user_id', type: 'integer', example: 15),
        new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2025-01-15'),
        new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2025-01-29'),
        new OA\Property(property: 'special_requests', type: 'string', nullable: true, example: 'I can start from 5:30 for dinner. I can stay until last call for weekdays.'),
        new OA\Property(
            property: 'availabilities',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Availability')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
    ]
)]
class AvailabilitySubmissionSchema {}
