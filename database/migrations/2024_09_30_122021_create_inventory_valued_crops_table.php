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
            $table->unsignedBigInteger('farmer_id'); // Foreign key to farmers table
            $table->unsignedBigInteger('plant_id');  // Foreign key to plants table
            $table->integer('count');
            $table->decimal('latitude', 10, 8)->nullable(); // Latitude field
            $table->decimal('longitude', 11, 8)->nullable(); // Longitude field
            $table->string('image_path')->nullable(); // Allow NULL values for image_path
            $table->timestamps();

            // Foreign keys with cascade delete
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
