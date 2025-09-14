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
        Schema::create('uploaded_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('barangay');
            $table->string('commodity');
            $table->string('farmer');
            $table->decimal('planting_density', 10, 4)->nullable();
            $table->decimal('production_vol_hectare', 10, 4)->nullable();
            $table->integer('newly_planted')->default(0);
            $table->integer('vegetative')->default(0);
            $table->integer('reproductive')->default(0);
            $table->integer('maturity')->default(0);
            $table->integer('total')->default(0);
            $table->decimal('planted_area_newly', 10, 4)->default(0);
            $table->decimal('planted_area_vegetative', 10, 4)->default(0);
            $table->decimal('planted_area_reproductive', 10, 4)->default(0);
            $table->decimal('planted_area_maturity', 10, 4)->default(0);
            $table->decimal('planted_total', 10, 4)->default(0);
            $table->decimal('area_harvested', 10, 4)->default(0);
            $table->decimal('production_volume_mt', 10, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_inventories');
    }
};
