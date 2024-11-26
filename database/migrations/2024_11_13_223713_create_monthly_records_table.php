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
        Schema::create('monthly_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_inventory_id')->constrained('monthly_inventories')->onDelete('cascade');
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('plant_id');
            $table->unsignedBigInteger('affiliation_id');
            // 
            $table->decimal('planting_density', 8, 2)->nullable();
            $table->decimal('production_volume', 8, 2)->nullable();
            $table->integer('newly_planted')->nullable();
            $table->integer('vegetative')->nullable();
            $table->integer('reproductive')->nullable();
            $table->integer('maturity_harvested')->nullable();
            $table->decimal('newly_planted_divided', 8, 2)->nullable();
            $table->decimal('vegetative_divided', 8, 2)->nullable();
            $table->decimal('reproductive_divided', 8, 2)->nullable();
            $table->decimal('maturity_harvested_divided', 8, 2)->nullable();
            $table->decimal('total_planted_area', 8, 2)->nullable();
            $table->integer('total')->nullable();
            $table->decimal('area_harvested', 8, 2)->nullable();
            $table->decimal('final_production_volume', 8, 2)->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_records');
    }
};
