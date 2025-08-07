<?php

namespace App\Models;

use App\Enums\Category;
use App\Helpers\GPT;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Task extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'form_entry_id',
        'parent_task_id',
        'parent_dependent',
        'category',
        'title',
        'description',
        'assign_external',
        'progress',
        'priority',
        'status',
        'is_completed',
        'date_start',
        'date_due',
        'date_remind',
    ];
    protected $casts = [
        'date_due' => 'date',
        'date_remind' => 'date',
        'date_start' => 'date'
    ];

    /**
     * @throws Exception
     */
    public static function createFromDiagnostic(int $diagnosticId) {
        // Find the message by ID
        $diagnostic = Diagnostic::findOrFail($diagnosticId);

        // Create prompt for GPT to generate tasks
        $prompt = "You are an expert business advisor named 'Trinity'. Based on the following diagnostic data, provide a JSON list of tasks a business owner should action within the next 30 days. Categories to use are: " . Category::valuesAsString() . ". Provide just the JSON with no markdown."
            . "\n\nSummary: " . $diagnostic->summary
            . "\n\nDiagnostic Data: " . $diagnostic->json_extract
            . "\n\nTemplate: [{\"title\": \"Task Title\", \"description\": \"Task description\", \"category\": \"category\", \"priority\": \"low|medium|high|critical\"}]\n\n"
            . "Make the descriptions detailed with step-by-step instructions where necessary.";

        // Set the prompt for GPT
        $gpt = new GPT();
        $gpt->setPrompt($prompt);
        $gptResponse = $gpt->chat("");

        // Assuming the response is a JSON string of tasks
        $tasksJson = str_replace('```', '', $gptResponse['response']);
        $tasks = json_decode($tasksJson, true);

        // Check if the response is valid JSON
        if (is_array($tasks)) {
            // Loop through tasks and create them in the database
            foreach ($tasks as $taskData) {
                self::create([
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'category' => $taskData['category'],
                    'priority' => $taskData['priority'],
                    'user_id' => Auth::id(),
                ]);
            }
            LogThis('info', 'Tasks generated and saved successfully.');
        } else {
            // Handle invalid JSON or empty response case
            throw new Exception('Invalid response from GPT. Could not create tasks.');
        }
    }

    /**
     * @throws Exception
     */
    public static function createFromMessage(int $messageId) {
        // Find the message by ID
        $message = Message::findOrFail($messageId);

        // Create prompt for GPT to generate tasks;
        $category = Category::from($message->category);
        $jsonTemplate = $categoryMessage = null;
        if ($category->value !== 'diagnostic') {
            $categoryMessage = "Only focus on tasks relevant to the {$category->value} category.";
            $jsonTemplate = "Template: [{\"title\": \"Task Title\", \"description\": \"Task description\", \"category\": \"{$category->value}\", \"priority\": \"low|medium|high|critical\"}]";
        } else {
            $categoryMessage = "Categories to use are: " . Category::valuesAsString() . '.';
            $jsonTemplate = "Template: [{\"title\": \"Task Title\", \"description\": \"Task description\", \"category\": \"{" . Category::valuesAsString() . "}\", \"priority\": \"low|medium|high|critical\"}]";
        }
        $prompt = "You are a seasoned business advisor. Based on the following message, provide a JSON list of tasks a business owner should action within the next 30 days.  {$categoryMessage} Provide just the JSON with no markdown.\n\n"
            . $jsonTemplate
            . "\n\nMake the descriptions detailed with step-by-step instructions where necessary.";

        // Set the prompt for GPT
        $gpt = new GPT();
        $gpt->setPrompt($prompt);
        $gptResponse = $gpt->chat("{$message->message}");

        // Assuming the response is a JSON string of tasks
        $tasksJson = $gptResponse['response'];
        LogThis('info', $tasksJson);
        $tasks = json_decode($tasksJson, true);

        // Check if the response is valid JSON
        if (is_array($tasks)) {
            // Loop through tasks and create them in the database
            foreach ($tasks as $taskData) {
                self::create([
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'category' => $taskData['category'],
                    'priority' => $taskData['priority'],
                    'user_id' => Auth::id(),
                ]);
            }
        } else {
            // Handle invalid JSON or empty response case
            throw new Exception('Invalid response from GPT. Could not create tasks.');
        }
    }

    // Relationships
    public function user() {
        return $this->belongsTo(User::class);
    }
}
