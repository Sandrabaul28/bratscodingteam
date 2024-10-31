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
        Schema::create('inventory_valued_crops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id'); // foreign key sa farmers table
            $table->unsignedBigInteger('plant_id');  // foreign key sa plants table
            $table->integer('count');
            $table->decimal('latitude', 10, 8)->nullable(); // Add latitude field
            $table->decimal('longitude', 11, 8)->nullable(); // Add longitude field
            $table->timestamps();

            $table->foreign('farmer_id')->references('id')->on('farmers')->onDelete('cascade');
            $table->foreign('plant_id')->references('id')->on('plants')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_valued_crops');
    }
};
