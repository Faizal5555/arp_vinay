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
        Schema::create('respondent_incentives', function (Blueprint $table) {
            $table->id();
            $table->text('pn_no');
            $table->text('respondent_name');
            $table->text('email_id');
            $table->text('contact_number');
            $table->text('speciality');
            $table->text('incentive_amount');
            $table->text('incentive_form');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respondent_incentives');
    }
};
