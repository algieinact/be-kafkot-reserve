<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    /**
     * Create new reservation WITH payment proof
     * This ensures no spam reservations without payment proof
     */
    public function store(Request $request)
    {
        $request->validate([
            // Customer info
            'customer_name' => 'required|string|min:2',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',

            // Reservation details
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_people' => 'required|integer|min:1|max:20',
            'duration_hours' => 'required|numeric|min:0.5|max:8',
            'special_notes' => 'nullable|string',

            // Order items
            'order_items' => 'required|array|min:1',
            'order_items.*.menu_id' => 'required|exists:menus,id',
            'order_items.*.quantity' => 'required|integer|min:1',

            // Payment proof (REQUIRED)
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Verify table is still available
            $this->verifyTableAvailability(
                $request->table_id,
                $request->reservation_date,
                $request->reservation_time,
                $request->duration_hours
            );

            // Generate unique booking code
            $bookingCode = 'RSV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Calculate total amount
            $totalAmount = 0;
            foreach ($request->order_items as $item) {
                $menu = \App\Models\Menu::find($item['menu_id']);
                $totalAmount += $menu->price * $item['quantity'];
            }

            // Upload payment proof to Cloudinary FIRST
            $cloudinary = new CloudinaryService();
            $publicId = 'payment-' . $bookingCode . '-' . time();

            $uploadResult = $cloudinary->uploadImage(
                $request->file('payment_proof'),
                'kafkot/payment-proofs',
                $publicId
            );

            // Create reservation (only after payment proof uploaded successfully)
            $reservation = Reservation::create([
                'booking_code' => $bookingCode,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'table_id' => $request->table_id,
                'reservation_date' => $request->reservation_date,
                'reservation_time' => $request->reservation_time,
                'number_of_people' => $request->number_of_people,
                'duration_hours' => $request->duration_hours,
                'total_amount' => $totalAmount,
                'status' => 'pending_verification',
                'special_notes' => $request->special_notes,
            ]);

            // Create reservation items
            foreach ($request->order_items as $item) {
                $menu = \App\Models\Menu::find($item['menu_id']);

                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'price_at_order' => $menu->price,
                    'subtotal' => $menu->price * $item['quantity'],
                ]);
            }

            // Create payment record with proof URL
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $totalAmount,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'unpaid',
                'payment_proof_url' => $uploadResult['secure_url'],
            ]);

            DB::commit();

            // Load relationships
            $reservation->load(['table.tableType', 'reservationItems.menu', 'payment']);

            // TODO: Send email notification

            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully. Please wait for admin verification.',
                'data' => $reservation,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create reservation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify table is still available before creating reservation
     */
    private function verifyTableAvailability($tableId, $reservationDate, $reservationTime, $durationHours)
    {
        $startDateTime = \Carbon\Carbon::parse($reservationDate . ' ' . $reservationTime);
        $endDateTime = $startDateTime->copy()->addHours($durationHours);

        // Buffer time (same as TableController)
        $bufferMinutes = 30;

        // Check for conflicting reservations
        $conflicts = Reservation::where('table_id', $tableId)
            ->where('reservation_date', $reservationDate)
            ->whereIn('status', ['pending_verification', 'confirmed'])
            ->get();

        foreach ($conflicts as $existing) {
            $existingStart = \Carbon\Carbon::parse($existing->reservation_date . ' ' . $existing->reservation_time);
            $existingEnd = $existingStart->copy()->addHours($existing->duration_hours);
            $existingEndWithBuffer = $existingEnd->copy()->addMinutes($bufferMinutes);

            // Check for overlap
            if ($startDateTime->lt($existingEndWithBuffer) && $endDateTime->gt($existingStart)) {
                throw new \Exception('Table is no longer available for the selected time slot. Please choose another time or table.');
            }
        }
    }

    /**
     * Get reservation by booking code
     */
    public function show($bookingCode)
    {
        $reservation = Reservation::where('booking_code', $bookingCode)
            ->with(['table.tableType', 'reservationItems.menu', 'payment'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $reservation,
        ]);
    }

    /**
     * Upload payment proof (DEPRECATED - now handled in store method)
     * Kept for backward compatibility if needed
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
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
            $cloudinary = new CloudinaryService();

            // Generate unique filename for Cloudinary
            $publicId = 'payment-' . $reservation->booking_code . '-' . time();

            // Upload to Cloudinary
            $uploadResult = $cloudinary->uploadImage(
                $request->file('payment_proof'),
                'kafkot/payment-proofs',
                $publicId
            );

            // Delete old image from Cloudinary if exists
            if ($payment->payment_proof_url) {
                $oldPublicId = $cloudinary->extractPublicId($payment->payment_proof_url);
                if ($oldPublicId) {
                    $cloudinary->deleteImage($oldPublicId);
                }
            }

            // Update payment record with Cloudinary secure URL
            $payment->update([
                'payment_proof_url' => $uploadResult['secure_url'],
            ]);

            // Update reservation status if needed
            if ($reservation->status === 'pending_verification') {
                // Status remains pending_verification until admin verifies
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment proof uploaded successfully',
                'data' => [
                    'payment_proof_url' => $uploadResult['secure_url'],
                    'public_id' => $uploadResult['public_id'],
                    'format' => $uploadResult['format'],
                    'size_bytes' => $uploadResult['bytes'],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload payment proof: ' . $e->getMessage(),
            ], 500);
        }
    }
}
