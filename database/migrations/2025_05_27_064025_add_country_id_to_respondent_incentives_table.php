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
        Schema::table('respondent_incentives', function (Blueprint $table) {
        $table->unsignedBigInteger('country_id')->nullable()->after('speciality');

        $table->foreign('country_id')
              ->references('id')
              ->on('country') // ✅ explicitly use the correct table name
              ->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respondent_incentives', function (Blueprint $table) {
            //
        });
    }
};
