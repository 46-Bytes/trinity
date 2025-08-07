<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\Conversation;
use App\Models\Diagnostic;
use App\Models\Message;
use App\Models\Note;
use App\Models\Task;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\GPT;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Exception\CommonMarkException;

class ChatController extends Controller {
    private Collection $conversations;
    private array $currentConversationCategories;
    private array $unusedCategories;
    private int $user_id;
    private GPT $gpt;

    public function __construct() {
        $this->gpt = new GPT();
    }

    /**
     * @throws Exception
     */
    public function index() {
        $this->initializeUserProperties();

        $conversations = $this->conversations;

        if ($conversations->isEmpty()) {
            $conversation_id = Conversation::initializeCategory($this->user_id, Category::General);
            return redirect()->route('chat.showConversation', $conversation_id)
                ->with([
                    'conversation_id' => $conversation_id,
                    'currentConversationCategories' => $this->currentConversationCategories,
                    'unusedCategories' => $this->unusedCategories
                ]);
        }

        $activeConversation = $conversations->flatten()->first();

        // Fetch messages for the active conversation
        $messages = $activeConversation->messages ?? collect();
        return view('chat.index')
            ->with([
                'conversations' => $conversations,
                'currentConversationCategories' => $this->currentConversationCategories,
                'unusedCategories' => $this->unusedCategories,
                'activeConversation' => $activeConversation,
                'messages' => $messages
            ]);
    }

    /**
     * @throws Exception
     */
    private function initializeUserProperties(): void {
        $user_id = Auth::id();

        if (!$user_id) {
            throw new Exception('User not authenticated');
        }

        $this->user_id = $user_id;
        $this->conversations = Conversation::where('user_id', $this->user_id)
            ->where('category', '!=', Category::Diagnostic)
            ->get();
        $this->currentConversationCategories = Conversation::getCategoriesByUser($this->user_id);

        $currentCategoriesValues = array_map(fn($category) => $category->value, $this->currentConversationCategories);
        $allCategoryValues = array_map(fn($category) => $category->value, Category::cases());

        $unusedCategoryValues = array_diff($allCategoryValues, $currentCategoriesValues);
        $this->unusedCategories = array_map(fn($value) => Category::from($value), $unusedCategoryValues);
    }

    /**
     * @throws Exception
     */
    public function createConversation(Request $request): RedirectResponse {
        $this->initializeUserProperties();

        $category = Category::from($request->input('category'));
        $conversation_id = Conversation::initializeCategory($this->user_id, $category);

        return redirect()->route('chat.showConversation', $conversation_id)
            ->with([
                'conversation_id' => $conversation_id,
                'currentConversationCategories' => $this->currentConversationCategories,
                'unusedCategories' => $this->unusedCategories
            ]);
    }

    public function showConversation($conversationId): View|Factory|Application {
        $this->initializeUserProperties();

        $conversations = $this->conversations;
        $activeConversation = Conversation::where('id', $conversationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $messages = $activeConversation->messages;

        return view('chat.index', compact('conversations', 'activeConversation', 'messages'))->with('unusedCategories', $this->unusedCategories);
    }

    /**
     * @throws Exception
     */
    public function sendMessage(Request $request, int $conversationId): JsonResponse {
        LogThis('info', 'ChatController::sendMessage: ' . $request->input('message'));
        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Create a new message from the user
        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'message' => $request->input('message'),
        ]);

        // Call the GPTService to get the response
        $this->gpt->setCategoryPrompt($conversation->category);
        $gptResponse = $this->gpt->chatFullContext($request->input('message'), $conversation->id);

        // Save the GPT's response
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'message' => $gptResponse['response'],
            'response_data' => json_encode($gptResponse['response_data']),  // Store raw GPT response data as JSON
            'metadata' => json_encode($gptResponse['metadata']),  // Store metadata
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * @throws CommonMarkException
     */
    public function getMessages(Request $request): JsonResponse {
        // TODO: Dynamically fetch the authenticated user's ID
//        $userId = Auth::id();
//
//        if (!$userId) {
//            return response()->json(['error' => 'User is not authenticated'], 401);
//        }

        $conversationId = $request->query('conversationId');

        if (!$conversationId) {
            return response()->json(['error' => 'Conversation ID is required'], 400);
        }

        // TODO: Ensure the conversation belongs to the authenticated user
        $conversation = Conversation::where('id', $conversationId)->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found or access denied'], 404);
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Convert Markdown to HTML for each message
        foreach ($messages as $message) {
            $message->message = $this->convertMarkdownToHtml($message->message);
        }
        return response()->json([
            'messages' => $messages,
        ]);
    }

    private function convertMarkdownToHtml($markdown): string {
        // Create an environment and add the CommonMark extension
        $environment = new \League\CommonMark\Environment\Environment();
        $environment->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
        $converter = new \League\CommonMark\MarkdownConverter($environment);

        return $converter->convert($markdown)->getContent();
    }

    public function createTaskFromMessage(int $messageId) {
        $message = Message::find($messageId);
        try {
            // Call the static method on the Task model to create tasks
            Task::createFromMessage($messageId);

            // Flash a success message to the session
            session()->flash('success', 'Task(s) created successfully!');
            // Redirect back to the conversation with a success message
            return redirect()->route('chat.showConversation', $message->conversation_id)
                ->with('success', 'Tasks generated and saved successfully.');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->route('chat.showConversation', $message->conversation_id)
                ->with('error', 'Failed to generate tasks: ' . $e->getMessage());
        }
    }

    public function createNoteFromMessage(int $messageId): RedirectResponse {
        $message = Message::find($messageId);
        try {
            // Call the static method on the Task model to create tasks
            Note::createFromMessage($messageId);
            // Flash a success message to the session
            session()->flash('success', 'Note created successfully!');

            // Redirect back to the conversation with a success message
            return redirect()->route('chat.showConversation', $message->conversation_id)
                ->with('success', 'Note saved.');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->route('chat.showConversation', $message->conversation_id)
                ->with('error', 'Failed to generate note: ' . $e->getMessage());
        }
    }

}
