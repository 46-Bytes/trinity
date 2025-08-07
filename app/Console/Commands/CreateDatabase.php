<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the database defined in the .env file if it does not exist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        // Get the database connection configuration
        $connection = Config::get('database.default');
        $databaseName = Config::get("database.connections.{$connection}.database");

        // Check if a database name is provided
        if (!$databaseName) {
            $this->error('No database name specified in the configuration.');
            return 1;
        }

        try {
            // Temporarily set the database name to null to allow for database creation
            Config::set("database.connections.{$connection}.database", null);
            DB::reconnect();

            // Create the database if it doesn't exist
            $query = "CREATE DATABASE IF NOT EXISTS `$databaseName`";
            DB::statement($query);

            $this->info("Database '$databaseName' created or already exists.");

            // Set the database name back to the original and reconnect
            Config::set("database.connections.{$connection}.database", $databaseName);
            DB::reconnect();

        } catch (\Exception $e) {
            $this->error("Error creating database: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
