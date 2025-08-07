<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Category;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('form_entry_id')->nullable();
            $table->integer('parent_task_id')->nullable();
            $table->boolean('parent_dependent')->default(false)->nullable();
            $table->enum('category', Category::values())->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('assign_external')->nullable();
            $table->integer('progress')->nullable()->default(0); // Progress
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('low'); // Priority
            $table->boolean('is_completed')->default(false)->nullable();
            $table->enum('status', ['needs-action', 'in-progress', 'completed', 'cancelled'])->default('needs-action');
            $table->date('date_start')->nullable();
            $table->date('date_due')->nullable();
            $table->date('date_remind')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('tasks');
    }
};
