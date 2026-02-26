<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountManagementController extends Controller
{
    /**
     * Get all bank accounts
     */
    public function index()
    {
        $accounts = BankAccount::orderBy('is_primary', 'desc')
            ->orderBy('bank_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }

    /**
     * Create a new bank account
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:150',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // If this account is set as primary, remove primary from all others
            if ($request->boolean('is_primary')) {
                BankAccount::where('is_primary', true)->update(['is_primary' => false]);
            }

            $account = BankAccount::create([
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'is_active' => $request->boolean('is_active', true),
                'is_primary' => $request->boolean('is_primary', false),
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil ditambahkan',
                'data' => $account,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan rekening: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a bank account
     */
    public function update(Request $request, $id)
    {
        $account = BankAccount::findOrFail($id);

        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:150',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // If this account is set as primary, remove primary from all others
            if ($request->boolean('is_primary') && !$account->is_primary) {
                BankAccount::where('is_primary', true)->update(['is_primary' => false]);
            }

            $account->update([
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'is_active' => $request->boolean('is_active'),
                'is_primary' => $request->boolean('is_primary'),
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil diperbarui',
                'data' => $account->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rekening: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a bank account
     */
    public function destroy($id)
    {
        try {
            $account = BankAccount::findOrFail($id);
            $account->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rekening: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        try {
            $account = BankAccount::findOrFail($id);
            $account->update(['is_active' => !$account->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Status rekening diperbarui',
                'data' => $account->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set a bank account as primary
     */
    public function setPrimary($id)
    {
        try {
            BankAccount::where('is_primary', true)->update(['is_primary' => false]);
            $account = BankAccount::findOrFail($id);
            $account->update(['is_primary' => true, 'is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Rekening utama diperbarui',
                'data' => $account->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengatur rekening utama: ' . $e->getMessage(),
            ], 500);
        }
    }
}
