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
        Schema::create('plants', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID, also the foreign key for inventory_value_crops
            $table->string('name_of_plants')->unique(); // Name of the plant
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('plants');
    }

};
