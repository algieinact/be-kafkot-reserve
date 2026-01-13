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
        Schema::create('menu_variations', function (Blueprint $table) {
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('variation_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->primary(['menu_id', 'variation_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_variations');
    }
};
