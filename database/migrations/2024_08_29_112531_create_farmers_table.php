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
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliation_id'); // Foreign key for affiliation
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('control_number')->unique(); // Control number field
            $table->date('birthdate')->nullable(); // Birthdate field
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('farmers');
    }

};
