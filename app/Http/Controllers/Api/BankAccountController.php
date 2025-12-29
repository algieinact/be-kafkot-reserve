<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    /**
     * Get active bank accounts for payment
     */
    public function active()
    {
        $accounts = BankAccount::active()
            ->orderBy('is_primary', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }
}
