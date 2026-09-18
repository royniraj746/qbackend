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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_code')->unique();
            $table->string('enquiry_type');
            $table->string('lead_source');
            $table->string('status');
            $table->text('remarks')->nullable();
            $table->string('created_by')->nullable(); // User who created this customer


            $table->foreignId('enquiry_customer_id')
                  ->constrained('enquiry_customers')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
