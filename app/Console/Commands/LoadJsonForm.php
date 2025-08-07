<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LoadJsonForm extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:json-form {form_id : The ID of the form to update} {path : The path to the JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load a JSON file into an existing form by form_id in the forms table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        // Get the form_id and file path from the arguments
        $formId = $this->argument('form_id');
        $path = $this->argument('path');

        // Check if the file exists
        if (!File::exists($path)) {
            $this->error("File not found at path: $path");
            return 1; // Return error code
        }

        // Read the file content
        $jsonContent = File::get($path);

        // Validate JSON format
        $jsonData = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON format in file: $path");
            return 1; // Return error code
        }

        // Check if the form exists in the database
        $form = DB::table('forms')->where('id', $formId)->first();

        if (!$form) {
            $this->error("Form with ID: $formId not found");
            return 1; // Return error code
        }

        // Update the form in the database
        DB::table('forms')
            ->where('id', $formId)
            ->update([
                'form_json' => $jsonContent,
                'updated_at' => now(),
            ]);

        $this->info("Form $formId updated successfully from file: $path");
        return 0; // Return success code
    }
}
