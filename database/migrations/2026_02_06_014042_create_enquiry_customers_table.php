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
        Schema::create('enquiry_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('mobile')->unique();
            $table->string('email')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_category');

            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->string('adhar_number', 20)->nullable();
            $table->string('created_by')->nullable(); // User who created this customer

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('country')->default('India');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiry_customers');
    }
};
