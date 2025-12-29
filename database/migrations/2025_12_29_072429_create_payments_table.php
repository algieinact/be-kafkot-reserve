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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            
            // Payment details
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['bank_transfer', 'cash', 'e_wallet'])->default('bank_transfer');
            
            // Bank transfer proof (for online payment)
            $table->string('payment_proof_url')->nullable();
            
            // Payment status (simplified - main status is in reservations table)
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            
            // Admin verification (optional - can also use reservation.verified_by)
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            
            // Payment timestamp
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
