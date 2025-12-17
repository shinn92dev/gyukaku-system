<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Availability\StoreRequest;
use App\Http\Requests\Availability\UpdateRequest;
use App\Models\Availability;
use App\Models\AvailabilitySubmission;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AvailabilitySubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/availability-submissions',
        tags: ['Availability Submissions'],
        summary: 'Get all availability submissions',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'All availability submissions retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Availability submissions retrieved successfully'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/AvailabilitySubmission')),
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
    public function index(): JsonResponse
    {
        $submissions = AvailabilitySubmission::with('availabilities')
            ->when(!auth()->user()->is_admin, fn ($query) => $query->where('user_id', auth()->id()))
            ->get();

        return response()->json([
            'message' => 'Availability submissions retrieved successfully',
            'data' => $submissions,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: '/availability-submissions',
        tags: ['Availability Submissions'],
        summary: 'Submit availability',
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['start_date', 'end_date', 'availabilities'],
            properties: [
                new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2025-01-15'),
                new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2025-01-29'),
                new OA\Property(property: 'special_requests', type: 'string', nullable: true, example: 'I can work until 11pm on weekdays'),
                new OA\Property(
                    property: 'availabilities',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'work_date', type: 'string', format: 'date', example: '2025-01-15'),
                            new OA\Property(property: 'lunch', type: 'boolean', example: true),
                            new OA\Property(property: 'dinner', type: 'boolean', example: false),
                        ],
                        type: 'object'
                    )
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Availability submission created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Availability submission created successfully'),
                new OA\Property(property: 'data', ref: '#/components/schemas/AvailabilitySubmission'),
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
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $submission = AvailabilitySubmission::create([
            'user_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'special_requests' => $validated['special_requests'] ?? null,
        ]);

        $submission->availabilities()->createMany($validated['availabilities']);

        return response()->json([
            'message' => 'Availability submission created successfully',
            'data' => $submission->load('availabilities'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: '/availability-submissions/{id}',
        tags: ['Availability Submissions'],
        summary: 'Get a specific availability submission',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'Availability submission ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Availability submission retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Availability submission retrieved successfully'),
                new OA\Property(property: 'data', ref: '#/components/schemas/AvailabilitySubmission'),
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
        response: 404,
        description: 'Availability submission not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Availability submission not found'),
            ]
        )
    )]
    public function show(string $id): JsonResponse
    {
        $submission = AvailabilitySubmission::with('availabilities')->findOrFail($id);

        return response()->json([
            'message' => 'Availability submission retrieved successfully',
            'data' => $submission,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Patch(
        path: '/availability-submissions/{id}',
        tags: ['Availability Submissions'],
        summary: 'Update an availability submission',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'Availability submission ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2025-01-15'),
                new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2025-01-29'),
                new OA\Property(property: 'special_requests', type: 'string', nullable: true, example: 'I can work until 11pm on weekdays'),
                new OA\Property(
                    property: 'availabilities',
                    type: 'array',
                    items: new OA\Items(
                        required: ['id'],
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'lunch', type: 'boolean', example: true),
                            new OA\Property(property: 'dinner', type: 'boolean', example: false),
                        ],
                        type: 'object'
                    )
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Availability submission updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Availability submission updated successfully'),
                new OA\Property(property: 'data', ref: '#/components/schemas/AvailabilitySubmission'),
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
        description: 'Forbidden - Admin access required',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'This action is unauthorized.'),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Availability submission not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Availability submission not found'),
            ]
        )
    )]
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $submission = AvailabilitySubmission::with('availabilities')->findOrFail($id);

        $validated = $request->validated();

        $submission->fill($validated)->save();

        if (isset($validated['availabilities'])) {
            foreach ($validated['availabilities'] as $availabilityData) {
                $availability = Availability::findOrFail($availabilityData['id']);
                assert($availability instanceof Availability);

                $availability->update([
                    'lunch' => $availabilityData['lunch'],
                    'dinner' => $availabilityData['dinner'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Availability submission updated successfully',
            'data' => $submission->fresh()->load('availabilities'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: '/availability-submissions/{id}',
        tags: ['Availability Submissions'],
        summary: 'Delete an availability submission',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'Availability submission ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Availability submission deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Availability submission deleted successfully'),
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
        description: 'Forbidden - Admin access required',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'This action is unauthorized.'),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Availability submission not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Availability submission not found'),
            ]
        )
    )]
    public function destroy(string $id): JsonResponse
    {
        $submission = AvailabilitySubmission::findOrFail($id);

        $submission->delete();

        return response()->json([
            'message' => 'Availability submission deleted successfully',
        ], 200);
    }
}
