<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Buffer time in minutes between reservations (for table cleanup)
     */
    const BUFFER_TIME_MINUTES = 30;

    /**
     * Check table availability
     * Now simplified - no need for number_of_people or table_type_id
     * Frontend will show visual layout and user selects specific table
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|numeric|min:0.5|max:8',
            'table_id' => 'required|exists:tables,id', // User selects specific table from visual layout
        ]);

        $reservationDate = $request->reservation_date;
        $reservationTime = $request->reservation_time;
        $durationHours = $request->duration_hours;
        $tableId = $request->table_id;

        // Calculate end time
        $startDateTime = \Carbon\Carbon::parse($reservationDate . ' ' . $reservationTime);
        $endDateTime = $startDateTime->copy()->addHours((float) $durationHours);

        // Get the specific table
        $table = Table::with('tableType')->find($tableId);

        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found',
                'data' => [
                    'available' => false,
                ],
            ], 404);
        }

        // Check if table status is available
        if ($table->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Table is not available (status: ' . $table->status . ')',
                'data' => [
                    'available' => false,
                    'reason' => 'Table status is ' . $table->status,
                ],
            ]);
        }

        // Check for conflicting reservations
        $conflictingReservations = \App\Models\Reservation::where('table_id', $tableId)
            ->where('reservation_date', $reservationDate)
            ->whereIn('status', ['pending_verification', 'confirmed'])
            ->get();

        $hasConflict = false;
        $conflictDetails = null;

        foreach ($conflictingReservations as $reservation) {
            // Extract date and time safely
            $dateOnly = \Carbon\Carbon::parse($reservation->reservation_date)->format('Y-m-d');
            $timeOnly = \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i:s');
            $existingStart = \Carbon\Carbon::parse($dateOnly . ' ' . $timeOnly);
            $existingEnd = $existingStart->copy()->addHours((float) $reservation->duration_hours);

            // Add buffer time to existing reservation end time (30 minutes for table cleanup)
            $existingEndWithBuffer = $existingEnd->copy()->addMinutes(self::BUFFER_TIME_MINUTES);

            // Check for time overlap with buffer
            if ($startDateTime->lt($existingEndWithBuffer) && $endDateTime->gt($existingStart)) {
                $hasConflict = true;
                $conflictDetails = [
                    'existing_time' => $reservation->reservation_time,
                    'existing_duration' => $reservation->duration_hours,
                    'existing_end_with_buffer' => $existingEndWithBuffer->format('H:i'),
                ];
                break;
            }
        }

        if ($hasConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Table is already booked for the selected time slot',
                'data' => [
                    'available' => false,
                    'table' => $table,
                    'conflict' => $conflictDetails,
                    'buffer_time_minutes' => self::BUFFER_TIME_MINUTES,
                ],
            ]);
        }

        // Table is available
        return response()->json([
            'success' => true,
            'message' => 'Table is available',
            'data' => [
                'available' => true,
                'table' => $table,
                'buffer_time_minutes' => self::BUFFER_TIME_MINUTES,
            ],
        ]);
    }

    /**
     * Get all tables with their availability status for a specific date/time
     * This is useful for showing visual layout with real-time availability
     */
    public function getTablesWithAvailability(Request $request)
    {
        $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|numeric|min:0.5|max:8',
        ]);

        $reservationDate = $request->reservation_date;
        $reservationTime = $request->reservation_time;
        $durationHours = $request->duration_hours;

        // Calculate end time
        $startDateTime = \Carbon\Carbon::parse($reservationDate . ' ' . $reservationTime);
        $endDateTime = $startDateTime->copy()->addHours((float) $durationHours);

        // Get all tables
        $tables = Table::with('tableType')->where('status', 'available')->get();

        // Check availability for each table
        $tablesWithAvailability = $tables->map(function ($table) use ($reservationDate, $startDateTime, $endDateTime) {
            // Check for conflicting reservations
            $conflictingReservations = \App\Models\Reservation::where('table_id', $table->id)
                ->where('reservation_date', $reservationDate)
                ->whereIn('status', ['pending_verification', 'confirmed'])
                ->get();

            $isAvailable = true;

            foreach ($conflictingReservations as $reservation) {
                // Extract date and time safely
                $dateOnly = \Carbon\Carbon::parse($reservation->reservation_date)->format('Y-m-d');
                $timeOnly = \Carbon\Carbon::parse($reservation->reservation_time)->format('H:i:s');
                $existingStart = \Carbon\Carbon::parse($dateOnly . ' ' . $timeOnly);
                $existingEnd = $existingStart->copy()->addHours((float) $reservation->duration_hours);
                $existingEndWithBuffer = $existingEnd->copy()->addMinutes(self::BUFFER_TIME_MINUTES);

                if ($startDateTime->lt($existingEndWithBuffer) && $endDateTime->gt($existingStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'table_type' => $table->tableType,
                'floor' => $table->floor,
                'position_x' => $table->position_x,
                'position_y' => $table->position_y,
                'orientation' => $table->orientation,
                'status' => $table->status,
                'is_available_for_booking' => $isAvailable,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'tables' => $tablesWithAvailability,
                'buffer_time_minutes' => self::BUFFER_TIME_MINUTES,
            ],
        ]);
    }
    public function index(Request $request)
    {
        $query = Table::with('tableType');

        if ($request->has('floor')) {
            $query->where('floor', $request->floor);
        }

        $tables = $query->get();

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
            'capacity' => 'required|integer|min:1',
            'table_type_id' => 'required|exists:table_types,id',
            'status' => 'required|in:available,reserved,inactive',
            'floor' => 'nullable|integer|min:1|max:3',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'orientation' => 'nullable|in:horizontal,vertical',
        ]);

        $table = Table::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Table created successfully',
            'data' => $table,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $id,
            'capacity' => 'required|integer|min:1',
            'table_type_id' => 'required|exists:table_types,id',
            'status' => 'required|in:available,reserved,inactive',
            'floor' => 'nullable|integer|min:1|max:3',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer',
            'orientation' => 'nullable|in:horizontal,vertical',
        ]);

        $table->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully',
            'data' => $table,
        ]);
    }

    public function destroy($id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        $table->delete();

        return response()->json([
            'success' => true,
            'message' => 'Table deleted successfully',
        ]);
    }

    public function updatePosition(Request $request, $id)
    {
        $table = Table::find($id);

        if (!$table) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        $request->validate([
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
            'floor' => 'required|integer|min:1|max:3',
            'orientation' => 'nullable|in:horizontal,vertical',
        ]);

        $table->update([
            'position_x' => $request->position_x,
            'position_y' => $request->position_y,
            'floor' => $request->floor,
            'orientation' => $request->orientation ?? $table->orientation,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Table position updated successfully',
            'data' => $table,
        ]);
    }
}