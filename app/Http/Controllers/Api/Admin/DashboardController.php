<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats(Request $request)
    {
        // Get date range (default: last 30 days)
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Total reservations
        $totalReservations = Reservation::whereBetween('created_at', [$startDate, $endDate])->count();

        // Pending verifications
        $pendingVerifications = Reservation::where('status', 'pending_verification')->count();

        // Confirmed reservations
        $confirmedReservations = Reservation::where('status', 'confirmed')
            ->whereBetween('reservation_date', [$startDate, $endDate])
            ->count();

        // Total revenue (from paid reservations)
        $totalRevenue = Reservation::whereHas('payment', function ($query) {
                $query->where('payment_status', 'paid');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Reservations by status
        $reservationsByStatus = Reservation::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        // Recent reservations
        $recentReservations = Reservation::with(['table', 'payment'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Upcoming reservations (today and next 7 days)
        $upcomingReservations = Reservation::where('status', 'confirmed')
            ->whereBetween('reservation_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->with(['table', 'payment'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_reservations' => $totalReservations,
                    'pending_verifications' => $pendingVerifications,
                    'confirmed_reservations' => $confirmedReservations,
                    'total_revenue' => $totalRevenue,
                ],
                'reservations_by_status' => $reservationsByStatus,
                'recent_reservations' => $recentReservations,
                'upcoming_reservations' => $upcomingReservations,
            ],
        ]);
    }
}
