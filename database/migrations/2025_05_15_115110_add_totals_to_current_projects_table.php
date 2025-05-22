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
        Schema::table('current_projects', function (Blueprint $table) {
            //
            $table->text('original_revenue_total')->nullable()->after('invoice_status');
            $table->text('invoice_amount_total')->nullable()->after('original_revenue_total');
            $table->text('incentives_paid_total')->nullable()->after('invoice_amount_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('current_projects', function (Blueprint $table) {
            //
        });
    }
};
