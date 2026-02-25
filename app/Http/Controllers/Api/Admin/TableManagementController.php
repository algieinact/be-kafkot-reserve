<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\TableType;
use Illuminate\Http\Request;

class TableManagementController extends Controller
{
    /**
     * Get all tables (with filters)
     */
    public function index(Request $request)
    {
        $query = Table::with('tableType');

        // Filter by type
        if ($request->has('table_type_id') && $request->table_type_id) {
            $query->where('table_type_id', $request->table_type_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by table number
        if ($request->has('search') && $request->search) {
            $query->where('table_number', 'like', '%' . $request->search . '%');
        }

        $tables = $query->orderBy('table_type_id')->orderBy('table_number')->get();

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }

    /**
     * Get single table
     */
    public function show($id)
    {
        $table = Table::with('tableType')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $table,
        ]);
    }

    /**
     * Create new table
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_type_id' => 'required|exists:table_types,id',
            'table_number' => 'required|string|unique:tables,table_number',
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'nullable|in:available,inactive',
        ]);

        try {
            $table = Table::create([
                'table_type_id' => $request->table_type_id,
                'table_number' => $request->table_number,
                'capacity' => $request->capacity,
                'status' => $request->status ?? 'available',
            ]);

            $table->load('tableType');

            return response()->json([
                'success' => true,
                'message' => 'Table created successfully',
                'data' => $table,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create table: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update table
     */
    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'table_type_id' => 'required|exists:table_types,id',
            'table_number' => 'required|string|unique:tables,table_number,' . $id,
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'nullable|in:available,inactive',
        ]);

        try {
            $table->update([
                'table_type_id' => $request->table_type_id,
                'table_number' => $request->table_number,
                'capacity' => $request->capacity,
                'status' => $request->status ?? $table->status,
            ]);

            $table->load('tableType');

            return response()->json([
                'success' => true,
                'message' => 'Table updated successfully',
                'data' => $table,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update table: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete table
     */
    public function destroy($id)
    {
        try {
            $table = Table::findOrFail($id);

            // Check if table has active reservations
            $activeReservations = $table->reservations()
                ->whereIn('status', ['pending_verification', 'confirmed'])
                ->count();

            if ($activeReservations > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete table with active reservations',
                ], 400);
            }

            $table->delete();

            return response()->json([
                'success' => true,
                'message' => 'Table deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete table: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update table status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,inactive',
        ]);

        try {
            $table = Table::findOrFail($id);
            $table->status = $request->status;
            $table->save();

            return response()->json([
                'success' => true,
                'message' => 'Table status updated',
                'data' => $table,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all table types
     */
    public function getTableTypes()
    {
        $tableTypes = TableType::all();

        return response()->json([
            'success' => true,
            'data' => $tableTypes,
        ]);
    }
}
