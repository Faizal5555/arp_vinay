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
        Schema::create('current_projects', function (Blueprint $table) {
            $table->id();
            $table->text('fy');
            $table->text('quarter');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->text('company_name'); // ARP / HPI / URP
            $table->text('pn_no');
            $table->text('email_subject');
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
            $table->text('invoice_status')->nullable(); // Paid / Pending
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ← Added
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_projects');
    }
};
