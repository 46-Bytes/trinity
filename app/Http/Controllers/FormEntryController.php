<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormEntry;
use App\Models\Note;
use App\Models\Setting;
use App\Models\Task;
use App\Services\GPTService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormEntryController extends Controller {

    protected GPTService $gptService;

    public function __construct(GPTService $gptService) {
        $this->gptService = $gptService;
    }

    public function index() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'responses' => 'required|json',
            'active_page' => 'nullable|string', // Make sure this can handle null values
            'percentage_complete' => 'nullable|integer',
            'status' => 'required|in:pending,completed,in-progress',
        ]);

        $formEntryData = [
            'form_id' => $validated['form_id'],
            'user_id' => auth()->id(), // Make sure you associate the current user
            'responses' => $validated['responses'],
            'status' => $validated['status'],
            'percentage_complete' => $validated['percentage_complete'] ?? 0, // Ensure a value is provided
        ];

        // Only set active_page if it's provided
        if (!empty($validated['active_page'])) {
            $formEntryData['active_page'] = $validated['active_page'];
        }

        $formEntry = FormEntry::updateOrCreate(
            ['user_id' => auth()->id(), 'form_id' => $validated['form_id']],
            $formEntryData
        );

        // If the form is completed, set the `completed_at` timestamp and get gpt advice
        if ($validated['status'] === 'completed') {
            LogThis('info', 'Form completed, attempting GPT API call');
            $formEntry->update(['completed_at' => now()]);

            try {
                $form = Form::find($validated['form_id']);
                $systemPrompt = Setting::getPrompt('system_prompt');
                $prompt = $systemPrompt . $form->ai_prompt . "\n\n" . json_encode(json_decode($validated['responses']), JSON_PRETTY_PRINT);

                LogThis('info', 'Sending prompt and user input to GPT API');

                $gptResponse = $this->gptService->generateResponse($prompt);

                LogThis('info', 'GPT response received', ['gptResponse' => $gptResponse]);

                $taskPrompt = Setting::getPrompt('initial_task_prompt');
                $gptTaskResponse = $this->gptService->generateResponse($taskPrompt . "\n\n" . json_encode(json_decode($validated['responses']), JSON_PRETTY_PRINT));

                LogThis('info', 'Receiving response from GPT API', ['gptResponse' => $gptResponse]);
                LogThis('info', 'Creating Note from advice provided by GPT API');

                $note = Note::create([
                    'user_id' => auth()->id(),
                    'form_entry_id' => $formEntry->id,
                    'title' => 'GPT Advice',
                    'content' => $gptResponse
                ]);

                LogThis('info', 'Receiving tasks from GPT API', ['gptTaskResponse' => $gptTaskResponse]);

                $gptTaskResponse = (object)json_decode($gptTaskResponse)->tasks; // Decoding as an associative array

                LogThis('info', 'Creating Tasks provided by GPT API');

                foreach ($gptTaskResponse as $task) {
                    Task::create([
                        'user_id' => auth()->id(),
                        'form_entry_id' => $formEntry->id,
                        'title' => $task->title,  // Accessing as an array
                        'description' => $task->description,
                        'category' => $task->category,
                        'priority' => $task->priority
                    ]);
                }

                $formEntry->update(['advice' => $gptResponse]);
            } catch (\Exception $e) {
                LogThis('error', 'Error during GPT request', ['error' => $e->getMessage()]);
            }
        }

        LogThis('info', 'Form entry processing completed');
        return response()->json(['success' => true, 'entry' => $formEntry]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
}
