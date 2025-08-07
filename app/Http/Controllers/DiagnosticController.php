<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Helpers\GPT;
use App\Models\Conversation;
use App\Models\Diagnostic;
use App\Models\DiagnosticQuestion;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\Media;
use App\Models\Message;
use App\Models\Note;
use App\Models\Setting;
use App\Models\Task;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DiagnosticController extends Controller {
    private ?Diagnostic $diagnostic;
    private int $user_id;
    private GPT $gpt;
    private Form $form;

    public function __construct() {
        $this->user_id = Auth::id();
        $this->gpt = new GPT();
        $this->form = Form::getBySlug('diagnostic');
        // Attempt to load the diagnostic
        $this->diagnostic = Diagnostic::with(['conversation.messages'])
            ->where('user_id', $this->user_id)
            ->orderBy('created_at', 'desc')
//            ->where('status', Diagnostic::STATUS_IN_PROGRESS)
            ->first();
    }

    /**
     * Shows the diagnostic conversation index page.
     *
     * If no active diagnostic conversation exists for the user, a new one is created.
     * Otherwise, the active conversation and its messages are fetched.
     *
     * @return View
     * @throws Exception
     */
    public function index() {

        return view('diagnostic.index', [
            'diagnostics' => Diagnostic::where('user_id', $this->user_id)->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function clone(int $diagnostic_id): RedirectResponse {
        $diagnosticToClone = Diagnostic::where('id', $diagnostic_id)->first();

        $diagnostic = Diagnostic::createNew(
            userId: $this->user_id,
            formEntry: FormEntry::find($diagnosticToClone->form_entry_id),
            conversationId: $diagnosticToClone->conversation_id,
            progress: 99
        );

        return redirect()->route('diagnostic.show', $diagnostic->id);
    }

    public function show(Request $request): View {
        if ($request) {
            $id = $request->route('Id');
            $diagnostic = Diagnostic::where('id', $id)->first();

            // Fetch saved responses from form entry if it exists
            $formEntry = FormEntry::find($this->diagnostic->form_entry_id);

            return view('diagnostic.show', [
                'surveyJson' => $this->form->form_json, // Pass JSON data to the view
                'themeJson' => $this->form->theme_json,
                'formEntry' => $formEntry,
                'diagnostic' => $diagnostic
            ]);
        } else {
            return redirect()->view('dashboard');
        }
    }

    public function uploadFile(Request $request) {
        $request->validate([
            'file' => 'required|file|max:10240' // 10MB max size
        ]);

        // Store the file in a designated folder structure
        $path = $request->file('file')->store('uploads/surveys', 'public');

        // Log the uploaded file in the database
        $uploadedFile = Media::create([
            'user_id' => auth()->id(),
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $uploadedFile->id,
                'name' => $uploadedFile->file_name,
                'url' => Storage::url($path),
            ]
        ]);
    }

    public function create() {
        $form = Form::Diagnostic();
        $diagnostic = Diagnostic::createNew(
            userId: $this->user_id,
            conversationId: $conversation = Conversation::initializeCategory($this->user_id, Category::Diagnostic),
        );

        return view('diagnostic.show', [
            'conversation' => $conversation,
            'messages' => $conversation->messages ?? collect(),
            'surveyJson' => $form->form_json, // Pass JSON data to the view
            'themeJson' => $form->theme_json,
            'formEntry' => $diagnostic->form_entry,
            'diagnostic' => $this->diagnostic
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveFormEntry(Request $request) {
        $validatedData = $request->validate([
            'form_entry_id' => 'required|integer|exists:form_entries,id',
            'responses' => 'required|json',
            'active_page' => 'nullable|string',
            'percentage_complete' => 'nullable|integer|min:0|max:100',
            'is_completed' => 'nullable|boolean'
        ]);

        try {
            $formEntry = FormEntry::findOrFail($validatedData['form_entry_id']);
            if (!$formEntry) {
                LogThis('error', 'Form entry not found', ['form_entry_id' => $validatedData['form_entry_id']]);
                return response()->json(['status' => 'error', 'message' => 'Form entry not found'], 404);
            }
            LogThis('info', 'saveFormEntry: Form entry found ' . $validatedData['form_entry_id']);

            // Assign directly if you trust the JSON string
            $formEntry->responses = json_decode($validatedData['responses'], true);
            $formEntry->active_page = $validatedData['active_page'] ?? $formEntry->active_page;
            $formEntry->percentage_complete = $validatedData['percentage_complete'] ?? $formEntry->percentage_complete;
            LogThis('info', 'saveFormEntry: Form Percentage Complete: ' . $formEntry->percentage_complete);

            if (!$this->diagnostic) {
                $this->diagnostic = Diagnostic::where('form_entry_id', $formEntry->id)->first();
            }
            LogThis('info', 'saveFormEntry: Diagnostic ID: ' . $this->diagnostic->id);

            if ($formEntry->percentage_complete == 100 || $validatedData['is_completed']) {
                $formEntry->status = 'completed';
                $formEntry->completed_at = now();
                $formEntry->save();
                $this->finalizeDiagnostic();
            } else {
                $formEntry->status = 'in-progress';
                $formEntry->save();

                $this->diagnostic->progress = $formEntry->percentage_complete;
                $this->diagnostic->status = $formEntry->status;
                $this->diagnostic->save();
            }
            LogThis('info', 'saveFormEntry: Form entry saved. Status: ' . $formEntry->status);
            LogThis('info', 'saveFormEntry: Diagnostic saved. Status: ' . $this->diagnostic->status);

            return response()->json(['status' => 'success', 'message' => 'Saved successfully'], 200);

        } catch (Exception $e) {
            LogThis('error', 'Error saving form entry', [
                'error_message' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => 'An error occurred while saving the form entry'], 500);
        }
    }

    /**
     * @throws Exception
     */
    private function finalizeDiagnostic(): void {
        try {
            ini_set('max_execution_time', 120);
            LogThis('info', 'Finalizing Diagnostic', ['diagnostic_id' => $this->diagnostic->id]);

            // Get the form entry with a fresh instance to avoid any caching issues
            $formEntry = FormEntry::find($this->diagnostic->form_entry_id);
            if (!$formEntry) {
                throw new \Exception('Form entry not found for diagnostic');
            }

            // Save JSON extract
            LogThis('info', 'Merge Diagnostic Questions and Answers');
            $jsonExtract = $formEntry->getQAJson();
            if (!$this->diagnostic->debugSave(['json_extract' => $jsonExtract])) {
                throw new \Exception('Failed to save diagnostic with JSON extract');
            }
            LogThis('info', 'Saved JSON extract', ['length' => strlen($jsonExtract)]);

            // Generate and save summary
            LogThis('info', 'Generate Diagnostic Summary');
            $summary = $this->summarizeDiagnostic();
            if (!$this->diagnostic->debugSave(['summary' => $summary])) {
                throw new \Exception('Failed to save diagnostic with summary');
            }
            LogThis('info', 'Saved summary', ['length' => strlen($summary)]);

            // Process and save user scores
            LogThis('info', 'Process User Scores');
            $scoringData = $this->processUserScores($formEntry);
            if (!$this->diagnostic->debugSave(['json_scoring' => $scoringData])) {
                throw new \Exception('Failed to save diagnostic with scoring data');
            }
            LogThis('info', 'Saved scoring data', ['length' => strlen($scoringData)]);

            // Generate and save advice
            LogThis('info', 'Generate Diagnostic Advice');
            $advice = $this->generateDiagnosticAdvice();

            // Save final state
            $finalData = [
                'advice' => $advice,
                'status' => Diagnostic::STATUS_COMPLETED,
                'end_date' => now(),
                'progress' => 100
            ];

            if (!$this->diagnostic->debugSave($finalData)) {
                throw new \Exception('Failed to save final diagnostic state');
            }

            LogThis('info', 'Diagnostic finalized successfully', [
                'diagnostic_id' => $this->diagnostic->id,
                'advice_length' => strlen($advice)
            ]);

            // Create related records
            LogThis('info', 'Creating Tasks');
            Task::createFromDiagnostic($this->diagnostic->id);

            LogThis('info', 'Creating Notes');
            Note::createFromDiagnostic($this->diagnostic->id);

            LogThis('info', 'Creating Media Record');
            Media::create([
                'user_id' => $this->user_id,
                'file_name' => $this->diagnostic->getDownloadFilename(),
                'file_path' => '/diagnostic/download/' . $this->diagnostic->id,
                'file_type' => 'diagnostic',
                'description' => 'Completed Diagnostic',
                'created_at' => now(),
                'updated_at' => now()
            ]);

        } catch (\Exception $e) {
            LogThis('error', 'Error finalizing diagnostic', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'diagnostic_id' => $this->diagnostic->id ?? null
            ]);
            throw $e; // Re-throw to be handled by the caller
        }
    }

    /**
     * Generate a summary of the diagnostic using GPT
     *
     * @return string The generated summary
     * @throws \Exception If there's an error generating the summary
     */
    public function summarizeDiagnostic(): string {
        try {
            LogThis('info', 'Starting diagnostic summary generation', [
                'diagnostic_id' => $this->diagnostic->id,
                'form_entry_id' => $this->diagnostic->form_entry_id
            ]);

            $summaryPrompt = Setting::getPrompt('diagnostic_summary');
            if (empty($summaryPrompt)) {
                throw new \Exception('Summary prompt is empty or not found');
            }

            // Ensure we have form entry responses
            $formEntry = $this->diagnostic->form_entry ?? FormEntry::find($this->diagnostic->form_entry_id);
            if (!$formEntry) {
                throw new \Exception('Form entry not found for diagnostic');
            }

            $responses = $formEntry->responses;
            if (empty($responses)) {
                throw new \Exception('No responses found in form entry');
            }

            LogThis('debug', 'Sending to GPT for summary generation', [
                'prompt_length' => strlen($summaryPrompt),
                'responses_length' => is_string($responses) ? strlen($responses) : 'not a string',
                'responses_type' => gettype($responses)
            ]);

            $gpt = new GPT();
            $gpt->setPrompt($summaryPrompt);
            $gptResponse = $gpt->chat($responses);

            if (!isset($gptResponse['response'])) {
                throw new \Exception('Invalid response format from GPT');
            }

            $summary = $gptResponse['response'];

            LogThis('info', 'Successfully generated diagnostic summary', [
                'diagnostic_id' => $this->diagnostic->id,
                'summary_length' => strlen($summary)
            ]);

            return $summary;

        } catch (\Exception $e) {
            LogThis('error', 'Error generating diagnostic summary', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'diagnostic_id' => $this->diagnostic->id ?? null
            ]);
            throw new \Exception('Failed to generate diagnostic summary: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Finalize the diagnostic process for a given diagnostic ID.
     *
     * This function marks the diagnostic as complete, updates its status and
     * progress, generates a summary and advice using GPT, and stores the
     * results in the diagnostic record. It also creates tasks and notes from
     * the diagnostic. Finally, it creates a media record for the diagnostic
     * download.
     *
     * @param int $diagnosticId The ID of the diagnostic to finalize.
     * @return void
     * @throws Exception
     */

    private function processUserScores(FormEntry $formEntry): string {
        $gpt = new GPT();
        $jsonDiagnosticQuestions = File::get(database_path('data/forms/diagnostic-surveyjs.json'));
        $jsonScoringMap = File::get(database_path('data/prompts/scoring_map.json'));
        $scoring_prompt = File::get(database_path('data/prompts/scoring_prompt.md'));
        $jsonTaskLibrary = File::get(database_path('data/prompts/task_library.json'));
        LogThis('info', 'Json Files Loaded');
        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => $scoring_prompt . "\n"
                . "Scoring Map: " . $jsonScoringMap . "\n"
                . "Process User Responses using Scoring Map and store as clientResponses.\n Join clientResponses array with roadmap array in the same json structure." . "\n"
                . "Task Library: " . $jsonTaskLibrary
        ];
        $messages[] = [
            'role' => 'system',
            'content' => "When responding with json, respond using pure json. When responding with html, respond using pure html. No comments or explanations, or markdown.",
        ];
        $messages[] = [
            'role' => 'user',
            'content' => "Diagnostic: " . $jsonDiagnosticQuestions . "\nUser Responses: " . $formEntry->responses,
        ];
        $messages[] = [
            'role' => 'user',
            'content' => "Strip all markdown from the response. Leave only the json.",];
        LogThis('info', 'Send Message Payload');
        $gptResponse = $gpt->sendMessages($messages);
        LogThis('info', 'Load GPT Response');
        $response = $gptResponse['response'];
        LogThis('info', 'GPT Scoring Response:', [$gptResponse]);
        return $response;
    }

    /**
     * Generates diagnostic advice based on the diagnostic ID.
     *
     * This function retrieves the diagnostic details using the provided ID,
     * constructs a prompt using the diagnostic summary and JSON extract,
     * and sends the prompt to the GPT model to generate advice.
     *
     * @param int $diagnosticId The ID of the diagnostic to generate advice for.
     * @return string The generated advice from the GPT model.
     * @throws Exception
     */
    /**
     * Generates diagnostic advice using GPT based on the diagnostic's scoring data.
     *
     * @return string The generated advice
     * @throws \Exception If there's an error generating the advice
     */
    private function generateDiagnosticAdvice(): string {
        try {
            LogThis('info', 'Starting advice generation', [
                'diagnostic_id' => $this->diagnostic->id,
                'has_scoring_data' => !empty($this->diagnostic->json_scoring)
            ]);

            $advicePrompt = Setting::getPrompt('advice_prompt_diagnostic');
            if (empty($advicePrompt)) {
                throw new \Exception('Advice prompt is empty or not found');
            }

            $gpt = new GPT();
            $gpt->setPrompt($advicePrompt);

            // Log the input data being sent to GPT
            LogThis('debug', 'Sending to GPT for advice generation', [
                'prompt_length' => strlen($advicePrompt),
                'scoring_data_length' => is_string($this->diagnostic->json_scoring)
                    ? strlen($this->diagnostic->json_scoring)
                    : 'not a string'
            ]);

            $response = $gpt->chat($this->diagnostic->json_scoring);

            if (!isset($response['response'])) {
                throw new \Exception('Invalid response format from GPT');
            }

            $advice = $response['response'];

            LogThis('info', 'Successfully generated advice', [
                'diagnostic_id' => $this->diagnostic->id,
                'advice_length' => strlen($advice)
            ]);

            return $advice;

        } catch (\Exception $e) {
            LogThis('error', 'Error generating diagnostic advice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'diagnostic_id' => $this->diagnostic->id ?? null
            ]);
            throw new \Exception('Failed to generate diagnostic advice: ' . $e->getMessage(), 0, $e);
        }
    }


    /**
     * Check if the diagnostic is complete for a given conversation.
     *
     * This function determines whether the diagnostic process is complete by
     * checking if a specific message indicating completion ('#DIAGNOSTIC-COMPLETE')
     * exists within the conversation.
     *
     * @param int $conversationId The ID of the conversation to check.
     * @return bool True if the diagnostic is complete, false otherwise.
     */

    /**
     * Create tasks from a given message ID.
     *
     * This function calls the static method on the Task model to create tasks
     * from the given message ID. If the task creation is successful, it will
     * redirect back to the diagnostic index with a success message. If not, it
     * will redirect back with an error message.
     *
     * @param int $messageId The ID of the message to create tasks from.
     * @return RedirectResponse
     */
//    public function createTaskFromMessage(int $messageId) {
//        try {
//            // Call the static method on the Task model to create tasks
//            Task::createFromMessage($messageId);
//
//            // Redirect back to the conversation with a success message
//            return redirect()->route('diagnostic.index')
//                ->with('success', 'Tasks generated and saved successfully.');
//        } catch (\Exception $e) {
//            // Redirect back with an error message if something goes wrong
//            return redirect()->route('diagnostic.index')
//                ->with('error', 'Failed to generate tasks: ' . $e->getMessage());
//        }
//    }

    /**
     * Create a note from a given message ID.
     *
     * This function calls the static method on the Note model to create a note
     * from the given message ID. If the note creation is successful, it will
     * redirect back to the diagnostic index with a success message. If not, it
     * will redirect back with an error message.
     *
     * @param int $messageId The ID of the message to create a note from.
     * @return RedirectResponse
     */
//    public function createNoteFromMessage(int $messageId): RedirectResponse {
//        try {
//            // Call the static method on the Note model to create tasks
//            Note::createFromMessage($messageId);
//            // Redirect back to the conversation with a success message
//            return redirect()->route('diagnostic.index')
//                ->with('success', 'Note saved.');
//        } catch (\Exception $e) {
//            // Redirect back with an error message if something goes wrong
//            return redirect()->route('diagnostic.index')
//                ->with('error', 'Failed to generate note: ' . $e->getMessage());
//        }
//    }

}
