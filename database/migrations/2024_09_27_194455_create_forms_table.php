<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->longtext('ai_prompt')->nullable();
            $table->longtext('scripts')->nullable();
            $table->json('form_json');  // SurveyJS form structure
            $table->json('theme_json')->nullable();  // SurveyJS theme structure
            $table->enum('status', ['draft', 'review', 'approved', 'active'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('forms');
    }
};
