<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class TailLog extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:tail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate the laravel.log file and run tail -f on it';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $logFile = storage_path('logs/laravel.log');

        // Truncate the file
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            $this->info('The log file has been truncated.');
        } else {
            $this->warn('Log file does not exist.');
            return CommandAlias::FAILURE;
        }

        // Run tail -f on the log file
        $this->info('Running tail -f on the log file. Press Ctrl+C to exit.');
        passthru("tail -f $logFile");

        return CommandAlias::SUCCESS;
    }
}
