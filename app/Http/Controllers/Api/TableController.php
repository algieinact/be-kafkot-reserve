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
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'table_type_id' => 'required|exists:table_types,id',
            'number_of_people' => 'required|integer|min:1|max:20',
            'duration_hours' => 'required|numeric|min:0.5|max:8',
        ]);

        $reservationDate = $request->reservation_date;
        $reservationTime = $request->reservation_time;
        $durationHours = $request->duration_hours;

        // Calculate end time
        $startDateTime = \Carbon\Carbon::parse($reservationDate . ' ' . $reservationTime);
        $endDateTime = $startDateTime->copy()->addHours($durationHours);

        // Get all tables matching criteria
        $candidateTables = Table::where('table_type_id', $request->table_type_id)
            ->where('capacity', '>=', $request->number_of_people)
            ->where('status', 'available')
            ->with('tableType')
            ->orderBy('capacity')
            ->get();

        // Filter out tables that have conflicting reservations
        $availableTables = $candidateTables->filter(function ($table) use ($reservationDate, $startDateTime, $endDateTime) {
            // Check if table has any conflicting reservations
            $conflictingReservations = \App\Models\Reservation::where('table_id', $table->id)
                ->where('reservation_date', $reservationDate)
                ->whereIn('status', ['pending_verification', 'confirmed'])
                ->get();

            foreach ($conflictingReservations as $reservation) {
                $existingStart = \Carbon\Carbon::parse($reservation->reservation_date . ' ' . $reservation->reservation_time);
                $existingEnd = $existingStart->copy()->addHours($reservation->duration_hours);

                // Add buffer time to existing reservation end time (30 minutes for table cleanup)
                $existingEndWithBuffer = $existingEnd->copy()->addMinutes(self::BUFFER_TIME_MINUTES);

                // Check for time overlap with buffer
                // New reservation conflicts if it starts before existing ends (with buffer)
                // AND ends after existing starts
                if ($startDateTime->lt($existingEndWithBuffer) && $endDateTime->gt($existingStart)) {
                    return false; // Conflict found
                }
            }

            return true; // No conflict
        });

        return response()->json([
            'success' => true,
            'message' => count($availableTables) > 0
                ? count($availableTables) . ' table(s) available'
                : 'No tables available for selected criteria',
            'data' => [
                'available' => count($availableTables) > 0,
                'available_tables' => $availableTables->values(),
                'buffer_time_minutes' => self::BUFFER_TIME_MINUTES,
            ],
        ]);
    }
}