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
        Schema::create('pending_projects', function (Blueprint $table) {
            $table->id();
            $table->text('fy')->nullable();
            $table->text('quarter')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->text('company_name')->nullable();
            $table->text('pn_no')->nullable();
            $table->text('email_subject')->nullable();
            $table->date('commission_date')->nullable();
            $table->text('currency_amount')->nullable();
            $table->text('original_revenue')->nullable();
            $table->text('margin')->nullable();
            $table->text('final_invoice_amount')->nullable();
            $table->text('comments')->nullable();
            $table->text('supplier_name')->nullable();
            $table->text('supplier_payment_details')->nullable();
            $table->text('total_incentives_paid')->nullable();
            $table->date('incentive_paid_date')->nullable();
            $table->text('invoice_number')->nullable();
            $table->enum('invoice_status', ['Pending', 'Paid','waveoff','partial','Open_Last_Quarter'])->nullable();
            $table->text('original_revenue_total')->nullable();
            $table->text('invoice_amount_total')->nullable();
            $table->text('incentives_paid_total')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_projects');
    }
};
