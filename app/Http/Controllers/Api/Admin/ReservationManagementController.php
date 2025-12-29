<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationManagementController extends Controller
{
    /**
     * Get all reservations with filters
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['table.tableType', 'reservationItems.menu', 'payment']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        // Search by booking code or customer name
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%');
            });
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reservations,
        ]);
    }

    /**
     * Get single reservation detail
     */
    public function show($id)
    {
        $reservation = Reservation::with(['table.tableType', 'reservationItems.menu', 'payment'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $reservation,
        ]);
    }

    /**
     * Verify payment (approve)
     */
    public function verifyPayment(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $payment = $reservation->payment;

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment record not found',
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Update payment status
            $payment->update([
                'payment_status' => 'paid',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'paid_at' => now(),
            ]);

            // Update reservation status
            $reservation->update([
                'status' => 'confirmed',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);

            DB::commit();

            // TODO: Send email notification to customer

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'data' => $reservation->load(['payment']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject payment
     */
    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $reservation = Reservation::findOrFail($id);
        $payment = $reservation->payment;

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment record not found',
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Update payment status
            $payment->update([
                'payment_status' => 'unpaid',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);

            // Update reservation status
            $reservation->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);

            DB::commit();

            // TODO: Send email notification to customer

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected',
                'data' => $reservation->load(['payment']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark reservation as completed
     */
    public function complete($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Only confirmed reservations can be marked as completed',
            ], 400);
        }

        $reservation->update([
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservation marked as completed',
            'data' => $reservation,
        ]);
    }

    /**
     * Cancel reservation
     */
    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);

        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel this reservation',
            ], 400);
        }

        $reservation->update([
            'status' => 'cancelled',
        ]);

        // TODO: Handle refund if payment was already made

        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled',
            'data' => $reservation,
        ]);
    }
}
