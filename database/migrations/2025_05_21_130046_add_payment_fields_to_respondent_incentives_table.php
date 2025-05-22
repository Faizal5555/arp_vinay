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
            //
            $table->string('payment_date')->nullable()->after('end_date');
            $table->string('payment_type')->nullable()->after('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respondent_incentives', function (Blueprint $table) {
            //
            $table->dropColumn(['payment_data', 'payment_type']);
        });
    }
};
