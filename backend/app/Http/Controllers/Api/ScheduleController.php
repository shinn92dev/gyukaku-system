<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\StoreRequest;
use App\Http\Requests\Schedule\UpdateRequest;
use App\Models\Schedule;
use App\Models\SchedulePeriod;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        summary: 'Get all schedules (Admin only)',
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
    #[OA\Response(
        response: 403,
        description: 'Forbidden - Admin access required',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Forbidden - Admin access required.'),
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
        summary: 'Store a new schedule (Admin only)',
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
                new OA\Property(property: 'message', type: 'string', example: 'Forbidden - Admin access required.'),
            ]
        )
    )]
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $currentPeriod = SchedulePeriod::current();

        if (!$currentPeriod) {
            return response()->json([
                'message' => 'No current schedule period set',
            ], 400);
        }

        $schedule = Schedule::create([
            'schedule_period_id' => $currentPeriod->id,
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
        $schedule = Schedule::with('shifts')->find($id);

        if (!$schedule) {
            return response()->json([
                'message' => 'Schedule not found',
            ], 404);
        }

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
        summary: 'Update schedule (Admin only)',
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
                new OA\Property(property: 'message', type: 'string', example: 'Forbidden - Admin access required.'),
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
        summary: 'Delete a schedule (Admin only)',
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
                new OA\Property(property: 'message', type: 'string', example: 'Forbidden - Admin access required.'),
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

    #[OA\Get(
        path: '/schedule-periods',
        tags: ['Schedules'],
        summary: 'Get all schedule periods',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'All schedule periods retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Schedule periods retrieved successfully'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SchedulePeriod')),
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
    public function schedulePeriods(): JsonResponse
    {
        $periods = SchedulePeriod::with('schedules')->get();

        return response()->json([
            'message' => 'Schedule periods retrieved successfully',
            'data' => $periods,
        ], 200);
    }

    #[OA\Get(
        path: '/schedules/current',
        tags: ['Schedules'],
        summary: 'Get schedules for the current period',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Current schedule period retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Current period schedule retrieved successfully'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SchedulePeriod')),
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
        description: 'No active current schedule period',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'No current schedule period set'),
            ]
        )
    )]
    public function currentPeriod(): JsonResponse
    {
        $currentPeriod = SchedulePeriod::current();

        if (!$currentPeriod) {
            return response()->json([
                'message' => 'No current schedule period set',
            ], 404);
        }

        $currentPeriod->load('schedules.shifts.user');

        return response()->json([
            'message' => 'Current schedule period retrieved successfully',
            'data' => $currentPeriod,
        ], 200);
    }

    #[OA\Get(
        path: '/schedules/today',
        tags: ['Schedules'],
        summary: "Get today's schedule",
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Today's schedule retrieved successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: "Today's schedule retrieved successfully"),
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
    public function today(): JsonResponse
    {
        $schedule = Schedule::with('shifts.user')
            ->whereDate('work_date', today())
            ->get();

        return response()->json([
            'message' => "Today's schedule retrieved successfully",
            'data' => $schedule,
        ], 200);
    }

    #[OA\Post(
        path: '/schedules/new-period',
        tags: ['Schedules'],
        summary: 'Store a schedule period (Admin only)',
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['start_date', 'end_date'],
            properties: [
                new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2025-01-15'),
                new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2025-01-30'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Schedule period created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'New schedule period created successfully'),
                new OA\Property(property: 'data', ref: '#/components/schemas/SchedulePeriod'),
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
                new OA\Property(property: 'message', type: 'string', example: 'Forbidden - Admin access required.'),
            ]
        )
    )]
    public function createNewPeriod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $schedulePeriod = SchedulePeriod::create([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_published' => false,
        ]);
        $schedulePeriod->updateCurrent();

        return response()->json([
            'message' => 'New schedule period created successfully',
            'data' => $schedulePeriod,
        ], 201);
    }

    #[OA\Get(
        path: '/schedules/range',
        tags: ['Schedules'],
        summary: 'Get schedules within a specified date range',
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'start_date',
        in: 'query',
        required: true,
        description: 'Start date of the range',
        schema: new OA\Schema(type: 'string', format: 'date', example: '2025-01-15')
    )]
    #[OA\Parameter(
        name: 'end_date',
        in: 'query',
        required: true,
        description: 'End date of the range',
        schema: new OA\Schema(type: 'string', format: 'date', example: '2025-01-30')
    )]
    #[OA\Response(
        response: 200,
        description: 'Schedules within the specified date range retrieved successfully',
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
    #[OA\Response(
        response: 404,
        description: 'No schedule found for specified range',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'No schedules found within the specified range'),
            ]
        )
    )]
    public function getByDateRange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $schedules = Schedule::whereBetween('work_date', [
            $validated['start_date'],
            $validated['end_date'],
        ])->with('shifts.user')->get();

        return response()->json([
            'message' => 'Schedules retrieved successfully',
            'data' => $schedules,
        ], 200);
    }
}
