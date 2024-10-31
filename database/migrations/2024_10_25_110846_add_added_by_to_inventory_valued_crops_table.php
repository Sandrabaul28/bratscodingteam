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
        Schema::table('inventory_valued_crops', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->after('count'); // Adjust the position as needed

            // Optionally, you can add a foreign key constraint
            $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_valued_crops', function (Blueprint $table) {
            $table->dropForeign(['added_by']); // Drop foreign key if exists
            $table->dropColumn('added_by'); // Remove the column
        });
    }
};
