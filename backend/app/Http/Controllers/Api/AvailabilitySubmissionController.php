<?php

namespace App\Http\Controllers\Api;

use App\Models\Availability;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\AvailabilitySubmission;
use App\Http\Requests\Availability\StoreRequest;
use App\Http\Requests\Availability\UpdateRequest;

class AvailabilitySubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {   
        $submissions = AvailabilitySubmission::with('availabilities')
            ->when(!auth()->user()->is_admin, fn($query) => $query->where('user_id', auth()->id()))
            ->get();

        return response()->json([
            'message' => 'Availability submissions retrieved successfully',
            'data' => $submissions,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
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
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $submission = AvailabilitySubmission::with('availabilities')->findOrFail($id);

        $validated = $request->validated();

        $submission->update([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'special_requests' => $validated['special_requests'],
        ]);

        foreach($validated['availabilities'] as $availabilityData) {
            $availability = Availability::findOrFail($availabilityData['id']);
            $availability->update([
                'lunch' => $availabilityData['lunch'],
                'dinner' => $availabilityData['dinner'],
            ]);
        }

        return response()->json([
            'message' => 'Availability submission updated successfully',
            'data' => $submission->fresh()->load('availabilities'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $submission = AvailabilitySubmission::findOrFail($id);

        $submission->delete();

        return response()->json([
            'message' => 'Availability submission deleted successfully',
        ], 200);
    }
}
