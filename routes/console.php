<?php

use App\Models\Reservation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Auto-complete reservations that have passed their end time.
 *
 * Logic:
 * - Only target reservations with status 'confirmed'
 * - End time = reservation_date + reservation_time + duration_hours
 * - If current time (WIB / Asia/Jakarta) > end time → set status to 'completed'
 */
Artisan::command('reservations:auto-complete', function () {
    $now = now('Asia/Jakarta');
    $completed = 0;

    Reservation::where('status', 'confirmed')->each(function (Reservation $reservation) use ($now, &$completed) {
        // Build end datetime - parse date first, then apply the time
        // (reservation_date may be stored as full datetime "2026-02-26 00:00:00")
        $startDatetime = \Carbon\Carbon::parse($reservation->reservation_date, 'Asia/Jakarta')
            ->setTimeFromTimeString($reservation->reservation_time);

        $endDatetime = $startDatetime->copy()->addMinutes((int) round($reservation->duration_hours * 60));

        if ($now->greaterThan($endDatetime)) {
            $reservation->status = 'completed';
            $reservation->save();
            $completed++;
        }
    });

    $this->info("Auto-complete selesai: {$completed} reservasi diselesaikan.");
})->purpose('Otomatis menyelesaikan reservasi yang sudah melewati jam selesai');

// Jalankan setiap menit
Schedule::command('reservations:auto-complete')->everyMinute();
