<?php

namespace App\Console\Commands;

use App\Enums\Category;
use App\Models\Conversation;
use App\Models\Diagnostic;
use Illuminate\Console\Command;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LoadTestFormEntry extends Command {
    protected $signature = 'form-entry:load {status=completed} {user_id?} {form_id=1}';
    protected $description = 'Load a test form entry record as completed or in-progress';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $status = $this->argument('status'); // completed or in-progress
        $userId = $this->argument('user_id') ?? 1; // Default user_id = 1 for testing
        $formId = $this->argument('form_id') ?? 1; // Default form_id = 1 for testing
        $responses = '{}';
        // Check if the form exists
        $form = Form::find($formId);
        if (!$form) {
            $this->error("Form with ID {$formId} does not exist.");
            return 1;
        }

        // Check if the user exists
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} does not exist.");
            return 1;
        }
        // Load appropriate JSON file based on status
        if ($status === 'completed') {
            $responses = File::get(database_path('data/form-entries/form-1-machus-complete.json'));
            $percentageComplete = 100;
            $completedAt = null; // Not completed
        } else {
            if ($userId == 1) {
                $responses = File::get(database_path('data/form-entries/form-1-machus-complete.json'));
            } else {
                $responses = File::get(database_path('data/form-entries/form-1-manuco-99pct.json'));
            }

            $status = 'in-progress';
            $percentageComplete = 99; // Set an example progress percentage
            $completedAt = null; // Not completed
        }

        // Start database transaction for data consistency
        DB::beginTransaction();

        try {
            if ($user->hasIncompleteDiagnostic()) {
                $diagnostic = Diagnostic::getActiveDiagnostic($user->id);
                $diagnostic->status = $status;
                $diagnostic->progress = $percentageComplete;
                $diagnostic->save();
                $formEntry = $diagnostic->form_entry;
                $formEntry->responses = $responses;
                $formEntry->percentage_complete = $percentageComplete;
                $formEntry->completed_at = $completedAt;
                $formEntry->save();
            } else {
                // Find or create the form entry with the test data
                $formEntry = FormEntry::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'form_id' => $formId,
                        'status' => $status
                    ],
                    [
                        'responses' => $responses,
                        'percentage_complete' => $percentageComplete,
                        'active_page' => 'swot',
                        'completed_at' => $completedAt
                    ]
                );

                // Now get or update the diagnostic
                $diagnostic = Diagnostic::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'status' => $status
                    ],
                    [
                        'conversation_id' => Conversation::initializeCategory($userId, Category::Diagnostic),
                        'form_entry_id' => $formEntry->id,
                        'type' => Diagnostic::TYPE_PRIMARY,
                        'start_date' => now(),
                        'end_date' => ($status === 'completed') ? now() : now()->addDays(7),
                        'progress' => $percentageComplete,
                        'json_extract' => $formEntry->getQAJson()
                    ]
                );
            }

            // Log the action
            $action = $diagnostic->wasRecentlyCreated ? 'Created' : 'Updated';
            $this->info("$action diagnostic and form entry for user ID {$userId}.");

            // Commit the transaction
            \DB::commit();

        } catch (\Exception $e) {
            // Rollback the transaction on error
            \DB::rollBack();
            $this->error("Error processing form entry: " . $e->getMessage());
            return 1;
        }
        // Output the result to the console
        $this->info("Form entry for user ID {$userId} and form ID {$formId} has been loaded with status: {$status}.");
        $this->info("Form Entry ID: {$formEntry->id}");
        $this->info("Progress: {$formEntry->percentage_complete}%");
        if ($status === 'completed') {
            $this->info("Completed At: {$formEntry->completed_at}");
        }

        return 0;
    }
}
