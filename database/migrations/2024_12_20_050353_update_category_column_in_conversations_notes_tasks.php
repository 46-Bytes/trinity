<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\Category;

// Ensure this is the correct namespace

class UpdateCategoryColumnInConversationsNotesTasks extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $tables = ['conversations', 'notes', 'tasks'];
        $validCategories = Category::values(); // Get the correct list of valid categories
        $enumValues = implode("','", $validCategories);

        foreach ($tables as $table) {
            // Alter the category column to use the updated ENUM values
            DB::statement("ALTER TABLE `$table` MODIFY `category` ENUM('$enumValues') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // You might need to restore the previous ENUM values here if necessary
        $oldCategories = ['old_value1', 'old_value2', 'old_value3']; // Replace with previous values
        $enumValues = implode("','", $oldCategories);

        foreach (['conversations', 'notes', 'tasks'] as $table) {
            DB::statement("ALTER TABLE `$table` MODIFY `category` ENUM('$enumValues') NOT NULL");
        }
    }
}
