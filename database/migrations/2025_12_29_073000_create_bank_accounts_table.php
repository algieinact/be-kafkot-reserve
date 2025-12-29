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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');                    // e.g., "BCA", "Mandiri", "BNI"
            $table->string('account_number');               // Nomor rekening
            $table->string('account_holder_name');          // Nama pemilik rekening (cafe)
            $table->boolean('is_active')->default(true);    // Aktif/tidak aktif
            $table->boolean('is_primary')->default(false);  // Rekening utama
            $table->text('notes')->nullable();              // Catatan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
