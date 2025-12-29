<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
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
        ]);

        // Get available tables based on criteria
        $availableTables = Table::where('table_type_id', $request->table_type_id)
            ->where('capacity', '>=', $request->number_of_people)
            ->where('status', 'available')
            ->with('tableType')
            ->orderBy('capacity')
            ->get();

        // TODO: Check against existing reservations for the same date/time
        // For now, we just return tables with 'available' status

        return response()->json([
            'success' => true,
            'message' => count($availableTables) > 0 
                ? count($availableTables) . ' table(s) available' 
                : 'No tables available for selected criteria',
            'data' => $availableTables,
        ]);
    }
}
