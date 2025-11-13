<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\StoreRequest;
use App\Http\Requests\Schedule\UpdateRequest;
use App\Models\Schedule;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use OpenApi\Attributes as OA;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/schedules',
        tags: ['Schedules'],
        summary: 'Get all schedules',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'All schedules retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedules retrieved successfully'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Schedule')),
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
        $schedules = Schedule::with('shifts')->get();

        return response()->json([
            'message' => 'Schedules retrieved successfully',
            'data' => $schedules,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: '/schedules',
        tags: ['Schedules'],
        summary: 'Store a new schedule',
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['work_date', 'type', 'shifts'],
            properties: [
                new OA\Property(property: 'work_date', type: 'string', format: 'date', example: '2025-01-15'),
                new OA\Property(property: 'type', type: 'string', enum: ['foh', 'boh', 'mgr'], example: 'foh'),
                new OA\Property(property: 'is_understaffed', type: 'boolean', example: true),
                new OA\Property(
                    property: 'shifts',
                    type: 'array',
                    items: new OA\Items(
                        required: ['user_id', 'shift_start_time'],
                        properties: [
                            new OA\Property(property: 'user_id', type: 'integer', example: 5),
                            new OA\Property(property: 'work_date', type: 'string', format: 'date', example: '2025-01-15'),
                            new OA\Property(property: 'shift_start_time', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
                        ],
                        type: 'object'
                    )
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Schedule created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedule with shifts created successfully'),
                new OA\Property(property: 'data', ref: '#/components/schemas/Schedule'),
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
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $schedule = Schedule::create([
            'work_date' => $validated['work_date'],
            'type' => $validated['type'],
            'is_understaffed' => $validated['is_understaffed'] ?? false,
        ]);

        $schedule->shifts()->createMany($validated['shifts']);

        return response()->json([
            'message' => 'Schedule with shifts created successfully',
            'data' => $schedule->load('shifts'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: '/schedules/{id}',
        tags: ['Schedules'],
        summary: 'Retrieve a schedule',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'Schedule ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Schedule retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedule with shifts retrieved successfully'),
                new OA\Property(property: 'data', ref: '#/components/schemas/Schedule'),
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
        description: 'Schedule not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedule not found'),
            ]
        )
    )]
    public function show(string $id): JsonResponse
    {
        $schedule = Schedule::with('shifts')->findOrFail($id);

        return response()->json([
            'message' => 'Schedule with shifts retrieved successfully',
            'data' => $schedule,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Patch(
        path: '/schedules/{id}',
        tags: ['Schedules'],
        summary: 'Update schedule',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'Schedule ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'work_date', type: 'string', format: 'date', example: '2025-01-15'),
                new OA\Property(property: 'type', type: 'string', enum: ['foh', 'boh', 'mgr'], example: 'foh'),
                new OA\Property(property: 'is_understaffed', type: 'boolean', example: false),
                new OA\Property(
                    property: 'shifts',
                    type: 'array',
                    items: new OA\Items(
                        required: ['id'],
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'user_id', type: 'integer', example: 5),
                            new OA\Property(property: 'work_date', type: 'string', format: 'date', example: '2025-01-15'),
                            new OA\Property(property: 'shift_start_time', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00Z'),
                        ],
                        type: 'object'
                    )
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Schedule updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedule updated successfully'),
                new OA\Property(property: 'data', ref: '#/components/schemas/Schedule'),
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
        description: 'Schedule not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedule not found'),
            ]
        )
    )]
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validated();

        $schedule->update(Arr::except($validated, ['shifts']));

        if (isset($validated['shifts'])) {
            foreach ($validated['shifts'] as $shiftData) {
                /** @var Shift $shift */
                $shift = Shift::findOrFail($shiftData['id']);

                $shift->update(Arr::except($shiftData, ['id']));
            }
        }

        return response()->json([
            'message' => 'Schedule updated successfully',
            'data' => $schedule->fresh()->load('shifts'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: '/schedules/{id}',
        tags: ['Schedules'],
        summary: 'Delete a schedule',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'Schedule ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Schedule deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedule deleted successfully'),
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
        description: 'Schedule not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedule not found'),
            ]
        )
    )]
    public function destroy(string $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);

        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully',
        ], 200);
    }
}
