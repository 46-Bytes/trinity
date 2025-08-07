<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('form_entry_id')->constrained()->onDelete('no action');
            $table->enum('type', ['primary', 'monthly']);
            $table->enum('status', ['needs-action', 'in-progress', 'completed'])->default('needs-action');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->integer('progress')->default(0);
            $table->text('summary')->nullable();
            $table->json('json_extract')->nullable();
            $table->longText('json_scoring')->nullable();
            $table->longText('advice')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('diagnostics');
    }
};
