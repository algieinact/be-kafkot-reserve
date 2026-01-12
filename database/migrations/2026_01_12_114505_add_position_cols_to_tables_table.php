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
        Schema::table('tables', function (Blueprint $table) {
            $table->integer('floor')->default(1)->after('status'); // 1: Lantai 1, 2: Lantai 2, 3: Lantai 3
            $table->integer('position_x')->default(0)->after('floor');
            $table->integer('position_y')->default(0)->after('position_x');
            $table->string('orientation')->default('horizontal')->after('position_y'); // horizontal, vertical
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['floor', 'position_x', 'position_y', 'orientation']);
        });
    }
};
