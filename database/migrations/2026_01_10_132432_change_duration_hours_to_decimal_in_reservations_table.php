<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Change duration_hours from integer to decimal(3,1) to support values like 1.5, 2.5, etc.
            $table->decimal('duration_hours', 3, 1)->default(2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Revert back to integer
            $table->integer('duration_hours')->default(2)->change();
        });
    }
};
