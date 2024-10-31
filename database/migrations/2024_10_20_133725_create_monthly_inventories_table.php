<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyInventoriesTable extends Migration
{
    public function up()
    {
        Schema::create('monthly_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmers')->onDelete('cascade');
            $table->foreignId('plant_id')->constrained('plants')->onDelete('cascade');
            $table->foreignId('affiliation_id')->constrained('affiliations')->onDelete('cascade');
            
            $table->decimal('planting_density'); // Planting Density in hectares
            $table->decimal('production_volume')->nullable(); // Production Volume in MT/ha
            
            // Original fields
            $table->integer('newly_planted')->nullable();
            $table->integer('vegetative')->nullable();
            $table->integer('reproductive')->nullable();
            $table->integer('maturity_harvested')->nullable();
            $table->integer('total')->nullable();
            
            // New fields for divided values
            $table->decimal('newly_planted_divided', 10, 4)->nullable(); // Newly Planted divided by Planting Density
            $table->decimal('vegetative_divided', 10, 4)->nullable(); // Vegetative divided by Planting Density
            $table->decimal('reproductive_divided', 10, 4)->nullable(); // Reproductive divided by Planting Density
            $table->decimal('maturity_harvested_divided', 10, 4)->nullable(); // Maturity/Harvested divided by Planting Density
            $table->decimal('total_planted_area', 10, 4)->nullable();

            
            // Additional fields
            $table->decimal('area_harvested', 10, 4)->nullable(); // Area harvested in hectares
            $table->decimal('final_production_volume', 10, 4)->nullable();
            
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('monthly_inventories');
    }
}
 