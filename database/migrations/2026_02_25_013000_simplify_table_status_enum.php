<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Rename any existing 'reserved' status to 'available' before changing enum
        DB::table('tables')->where('status', 'reserved')->update(['status' => 'available']);

        // Change enum to only available/inactive
        DB::statement("ALTER TABLE `tables` MODIFY `status` ENUM('available', 'inactive') NOT NULL DEFAULT 'available'");
    }

    public function down(): void
    {
        // Restore original enum
        DB::statement("ALTER TABLE `tables` MODIFY `status` ENUM('available', 'reserved', 'inactive') NOT NULL DEFAULT 'available'");
    }
};
