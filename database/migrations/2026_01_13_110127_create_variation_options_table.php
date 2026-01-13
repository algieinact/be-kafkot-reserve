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
        Schema::create('variation_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_group_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Less Sugar, Normal, Extra Sugar, etc
            $table->decimal('price_adjustment', 10, 2)->default(0); // Additional price
            $table->boolean('is_default')->default(false);
            $table->integer('order')->default(0); // Display order
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_options');
    }
};
