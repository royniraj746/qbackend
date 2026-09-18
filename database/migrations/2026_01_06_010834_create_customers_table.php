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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // 🔗 USER RELATION
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // 👤 BASIC DETAILS
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile', 15);

            // 🏢 BUSINESS DETAILS
            $table->string('business_name')->nullable();
            $table->enum('user_type', ['individual', 'business'])->default('individual');
            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->string('adhar_number', 20)->nullable();

            // 📍 ADDRESS
            $table->text('address')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('country')->default('India');

            // ⚙️ QUOTATION SETTINGS
            $table->string('currency')->default('INR');
            $table->decimal('default_tax', 5, 2)->default(0); // GST %
            $table->integer('quotation_validity_days')->default(15);

            // 🖼️ OPTIONAL
            $table->string('profile_photo')->nullable();
            $table->string('website')->nullable();

            // 🔐 STATUS
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
