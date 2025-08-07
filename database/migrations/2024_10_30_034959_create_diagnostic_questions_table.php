<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('diagnostic_questions', function (Blueprint $table) {
            $table->id();
            $table->text('category');
            $table->text('name');
            $table->text('type');
            $table->boolean('active')->default(true);
            $table->text('visible_if')->nullable();
            $table->text('question');
            $table->text('description')->nullable();
            $table->json('choices')->nullable();
            $table->json('surveyjs')->nullable();
            $table->longtext('notes')->nullable();
            $table->longtext('advisor_feedback')->nullable();
            $table->text('html_element')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('diagnostic_questions');
    }
};
