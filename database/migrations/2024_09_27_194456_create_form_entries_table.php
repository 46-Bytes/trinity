<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('form_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('form_id')->constrained();
            $table->json('responses');  // JSON for survey answers
            $table->longtext('advice')->nullable();  // GPT-generated advice
            $table->string('score')->nullable();
            $table->string('active_page')->nullable();
            $table->integer('percentage_complete')->nullable();
            $table->enum('status', ['pending', 'completed', 'in-progress'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('form_entries');
    }
};
