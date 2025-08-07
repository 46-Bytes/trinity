<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class RebuildDiagnostic extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rebuild:diagnostic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop DiagnosticQuestion and form tables and rerun their migrations and seeders';

    // Hardcoded configuration
    protected $tablesToDrop = ['diagnostic_questions', 'forms'];
    protected $migrationsToRun = [
        '2024_10_30_034959_create_diagnostic_questions_table',
        '2024_09_27_194455_create_forms_table'
    ];
    protected $seedersToRun = [
        'DiagnosticQuestionsSeeder',
        'FormSeeder'
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->info('Starting Diagnostic rebuild...');

        // Disable foreign key checks to avoid constraint issues
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop tables
        foreach ($this->tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                $this->info("Dropping table: {$table}");
                Schema::dropIfExists($table);
            } else {
                $this->warn("Table {$table} does not exist, skipping...");
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // First remove migration entries from migrations table
        foreach ($this->migrationsToRun as $migration) {
            $this->info("Removing migration record: {$migration}");
            DB::table('migrations')->where('migration', $migration)->delete();
        }

        // Run migrations
        foreach ($this->migrationsToRun as $migration) {
            $this->info("Running migration: {$migration}");
            $migrationPath = database_path("migrations/{$migration}.php");

            if (file_exists($migrationPath)) {
                Artisan::call('migrate', [
                    '--path' => "database/migrations/{$migration}.php",
                    '--force' => true,
                ]);
                $this->info(Artisan::output());
            } else {
                $this->error("Migration file not found: {$migration}");
                // Continue with other migrations instead of returning
                continue;
            }
        }

        // Run seeders
        foreach ($this->seedersToRun as $seeder) {
            $this->info("Running seeder: {$seeder}");

            try {
                Artisan::call('db:seed', [
                    '--class' => $seeder,
                    '--force' => true,
                ]);
                $this->info(Artisan::output());
            } catch (\Exception $e) {
                $this->error("Error running seeder: {$e->getMessage()}");
                // Continue with other seeders instead of returning
                continue;
            }
        }

        $this->info('Diagnostic tables refreshed successfully!');
        return 0;
    }
}
