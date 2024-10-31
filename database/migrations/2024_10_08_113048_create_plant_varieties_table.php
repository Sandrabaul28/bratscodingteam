<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('plant_varieties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plant_id');
            $table->string('variety_name');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('plant_id')->references('id')->on('plants')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_varieties');
    }
};
