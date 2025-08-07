<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_sandbox_product_id')->nullable();
            $table->string('stripe_sandbox_price_id')->nullable();
            $table->string('stripe_production_product_id')->nullable();
            $table->string('stripe_production_price_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('AUD');
            $table->boolean('is_active')->default(true);
            $table->string('type')->nullable(); // Could also be an enum if preferred
            $table->json('metadata')->nullable();
            $table->timestamp('stripe_created_at')->nullable();
            $table->timestamp('stripe_updated_at')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('products');
    }
};
