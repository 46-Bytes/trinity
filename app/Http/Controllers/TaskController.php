<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\TaskPriorities;
use App\Enums\TaskStatus;
use App\Helpers\GPT;
use App\Models\Message;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller {
    private GPT $gpt;

    public function __construct() {
        $this->gpt = new GPT();
    }

    /**
     * Display a listing of the tasks.
     */
    public function index() {
        // Fetch tasks for the current user
        $tasks = Task::where('user_id', Auth::id())->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Store a newly created task in the database.
     */

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|max:255',
            'date_due' => 'required|date',
            'category' => 'required|in:' . Category::valuesAsString(),
            'priority' => 'required|in:' . TaskPriorities::valuesAsString(),
            'status' => 'required|in:' . TaskStatus::valuesAsString(),
            'progress' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable',
            'date_start' => 'nullable|date',
            'date_remind' => 'nullable|date',
        ]);


        // Create a new task
        $task = Task::create([
            'user_id' => auth()->id(), // Assign the logged-in user
            'category' => $request->category,
            'title' => $request->title,
            'description' => $request->description ?? null,
            'assign_external' => $request->assign_external ?? null,
            'progress' => $request->progress,
            'priority' => $request->priority,
            'status' => $request->status ?? TaskStatus::NeedsAction->value,
            'is_completed' => $request->is_completed ? 1 : 0,
            'date_start' => $request->date_start,
            'date_due' => $request->date_due,
            'date_remind' => $request->date_remind,
        ]);

        // Check if the request came from the dashboard
        if ($request->has('from') && $request->input('from') === 'dashboard') {
            return redirect()->route('dashboard')->with('success', 'Task updated successfully.');
        }
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }


    /**
     * Show the form for creating a new task.
     */
    public function create() {
        return view('tasks.create');
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit($id) {
        // Find the task by ID
        $task = Task::findOrFail($id);

        return view('tasks.edit', compact('task'));
    }

    public function complete(Task $task) {
        // Update the task status to 'completed'
        $task->update(['status' => 'completed']);
        $task->update(['progress' => '100']);

        // Redirect back to the dashboard or wherever appropriate with a success message
        return redirect()->route('dashboard')->with('success', 'Task marked as complete.');
    }

    /**
     * Update the specified task in the database.
     */
    public function update(Request $request, Task $task) {
//        dd($request->all());
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|max:255',
            'date_due' => 'required|date',
            'category' => 'required|in:' . Category::valuesAsString(),
            'priority' => 'required|in:' . TaskPriorities::valuesAsString(),
            'status' => 'required|in:' . TaskStatus::valuesAsString(),
            'progress' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable',
            'date_start' => 'nullable|date',
            'date_remind' => 'nullable|date',
        ]);

        // Update the task with validated data
        $task->update($validated);

        // Check if the request came from the dashboard
        if ($request->has('from') && $request->input('from') === 'dashboard') {
            return redirect()->route('dashboard')->with('success', 'Task updated successfully.');
        }
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function createFromMessage(int $messageId) {
        $message = Message::findOrFail($messageId);
        try {
            // Call the static method on the Task model to create tasks
            Task::createFromMessage($messageId);

            // Redirect back to the conversation with a success message
            return redirect()->route('chat.showConversation', $message->conversation_id)
                ->with('success', 'Tasks generated and saved successfully.');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->route('chat.showConversation', $messageId)
                ->with('error', 'Failed to generate tasks: ' . $e->getMessage());
        }
    }
//    public function createFromMessage($messageId) {
//        // Find the message by ID
//        $message = Message::findOrFail($messageId);
//
//        // Create prompt for GPT to generate tasks
//        $category = $message->category;
//        $prompt = "You are a business assistant strictly focused on the category of {$category}. Based on the following message, provide a JSON list of tasks a business owner should action within the next 30 days. Only focus on tasks relevant to the {$category} category. Provide just the JSON with no markdown.\n\n"
//            . $message->message
//            . "\n\nTemplate: [{\"title\": \"Task Title\", \"description\": \"Task description\", \"category\": \"{$category}\", \"priority\": \"low|medium|high|critical\"}]\n\n"
//            . "Make the descriptions detailed with step-by-step instructions where necessary.";
//
//        // Initialize GPT service with the new prompt
//        $this->gpt->setPrompt($prompt);
//        $gptResponse = $this->gpt->chat("");
//
//        // Call GPTService to get the response
////        $gptResponse = $this->gptService->generateResponse($prompt);
//
//        // Assuming the response is a JSON string of tasks
//        $tasksJson = $gptResponse['response'];
//        $tasks = json_decode($tasksJson, true);
//
//        // Loop through tasks and create them in the database
//        foreach ($tasks as $taskData) {
//            Task::create([
//                'title' => $taskData['title'],
//                'description' => $taskData['description'],
//                'category' => $taskData['category'],
//                'priority' => $taskData['priority'],
//                'user_id' => auth()->id(),
//            ]);
//        }
//
//        // Redirect back to the conversation with a success message
//        return redirect()->route('chat.showConversation', $message->conversation_id)
//            ->with('success', 'Tasks generated and saved successfully.');
//    }

    /**
     * Remove the specified task from the database.
     */
    public function destroy(Request $request, $id) {
        // Find the task by ID and delete it
        $task = Task::findOrFail($id);
        $task->delete();

        // Check if the request came from the dashboard
        if ($request->has('from') && $request->input('from') === 'dashboard') {
            return redirect()->route('dashboard')->with('success', 'Task updated successfully.');
        }
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
