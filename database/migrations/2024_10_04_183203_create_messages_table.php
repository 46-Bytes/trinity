<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade'); // Foreign key to conversations table
            $table->enum('role', ['user', 'assistant', 'system'])->default('user'); // Role of the message sender (user or GPT)
            $table->longText('message'); // Store the message text/content
//            $table->foreignId('parent_message_id')->nullable()->constrained('messages')->onDelete('cascade'); // Optional for parent-child threading of messages
            $table->json('response_data')->nullable(); // Optional for storing any GPT response metadata or additional info
            $table->json('metadata')->nullable(); // Optional message metadata (e.g., token count, etc.)
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('messages');
    }
};
