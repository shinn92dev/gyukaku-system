<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{
    /**
     * Clocks in a user themselves.
     */
    public function clockAction(string $action): JsonResponse
    {
        $fieldMap = [
            'clock-in' => 'clock_in_at',
            'clock-out' => 'clock_out_at',
            'break-start' => 'break_start_at',
            'break-end' => 'break_end_at',
        ];

        $userId = auth()->id();
        $shift = Shift::where('user_id', $userId)->firstOrFail();

        $shift->update([
            $fieldMap[$action] => now(),
        ]);

        return response()->json([
            'message' => ucwords(str_replace('-', ' ', $action)) . ' recorded successfully',
            'data' => $shift->fresh(),
        ], 200);
    }

    /**
     * Clocks in a user by the admin.
     */
    public function clockActionForUser(string $userId, string $action): JsonResponse
    {
        $fieldMap = [
            'clock-in' => 'clock_in_at',
            'clock-out' => 'clock_out_at',
            'break-start' => 'break_start_at',
            'break-end' => 'break_end_at',
        ];

        $shift = Shift::where('user_id', $userId)->firstOrFail();

        $shift->update([
            $fieldMap[$action] => now(),
        ]);

        return response()->json([
            'message' => ucwords(str_replace('-', ' ', $action)) . ' recorded successfully',
            'data' => $shift->fresh(),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $shift = Shift::findOrFail($id);

        $shift->delete();

        return response()->json([
            'message' => 'Shift deleted successfully',
        ], 200);
    }
}
