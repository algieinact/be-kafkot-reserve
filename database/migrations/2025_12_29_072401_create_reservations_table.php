<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->foreignId('table_id')->constrained('tables');
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->integer('number_of_people');
            $table->decimal('total_amount', 10, 2);
            
            // Updated status enum to match frontend
            $table->enum('status', [
                'pending_verification',  // Menunggu verifikasi pembayaran
                'confirmed',             // Reservasi dikonfirmasi
                'rejected',              // Pembayaran ditolak
                'cancelled',             // Dibatalkan oleh customer/admin
                'completed'              // Reservasi selesai (customer sudah datang)
            ])->default('pending_verification');
            
            // Renamed from 'notes' to 'special_notes' to match frontend
            $table->text('special_notes')->nullable();
            
            // Admin verification fields
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            
            // Rejection reason (when status = rejected)
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
