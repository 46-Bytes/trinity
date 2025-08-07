<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();

            // Stripe IDs
            $table->string('promo_id')->nullable();
            $table->string('coupon_id')->unique();

            // Coupon settings
            $table->integer('coupon_percent_off')->nullable();
            $table->integer('coupon_amount_off')->nullable();
            $table->string('coupon_currency', 3)->nullable();
            $table->enum('coupon_duration', ['forever', 'once', 'repeating']);
            $table->integer('coupon_duration_months')->nullable();
            $table->integer('coupon_max_redemptions')->nullable();
            $table->timestamp('coupon_redeem_by')->nullable();
            $table->boolean('coupon_single_use')->default(false);

            // Promo code settings
            $table->boolean('promo_active')->default(true);
            $table->integer('promo_max_codes')->nullable();
            $table->integer('promo_min_amount')->nullable();
            $table->string('promo_customer_id')->nullable();
            $table->enum('promo_restriction', ['first_time_transaction', 'minimum_amount'])->nullable();

            // Status
            $table->boolean('is_valid')->default(true);
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('coupons');
    }
};
