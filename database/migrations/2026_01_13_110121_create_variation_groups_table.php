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
        Schema::create('variation_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Sugar Level, Ice Level, Size, Toppings, etc
            $table->enum('type', ['single_choice', 'multiple_choice'])->default('single_choice');
            $table->boolean('is_required')->default(false);
            $table->integer('min_selections')->default(0); // for multiple_choice
            $table->integer('max_selections')->nullable(); // for multiple_choice
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_groups');
    }
};
