<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('orgs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name', 100)->nullable(); // Organization name
            $table->string('slug', 100)->nullable(); // Slug
            $table->string('status', 100)->nullable(); // Status
            $table->longText('description')->nullable(); // Organization description
            $table->string('website', 255)->nullable(); // Website, nullable
            $table->string('address_line1', 255)->nullable(); // Address line 1, nullable
            $table->string('address_line2', 255)->nullable(); // Address line 2, nullable
            $table->string('city', 100)->nullable(); // City, nullable
            $table->string('state', 100)->nullable(); // State, nullable
            $table->string('postal_code', 20)->nullable(); // Postal code, nullable
            $table->string('country', 100)->nullable(); // Country, nullable
            $table->timestamp('date_joined')->nullable(); // Date joined, nullable
            $table->string('abn', 20)->nullable(); // ABN, nullable
            $table->string('abn_temp', 20)->nullable(); // Temporary ABN, nullable
            $table->date('abn_registered')->nullable(); // ABN registration date, nullable
            $table->string('abn_status', 45)->nullable(); // ABN status, nullable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('orgs');
    }
};
