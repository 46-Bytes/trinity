<?php

namespace App\Helpers;

use App\Enums\GPTFilePurpose;
use App\Models\Diagnostic;
use App\Models\Message;
use App\Services\GPTService;
use App\Models\Setting;
use App\Models\Media;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GPT {
    protected GPTService $gptService;
    protected ?string $systemPrompt = null;
    protected ?string $categoryPrompt = null;
    protected ?string $userFullName = null;
    private ?Diagnostic $diagnostic = null;
    private ?string $questions = null;

    public function __construct() {
        // Automatically initialize GPTService without needing to pass it
        $this->gptService = new GPTService();
        if (Auth::user()) {
            $this->userFullName = Auth::user()->name;
        }

        // Fetch default system prompt from the settings
        $this->systemPrompt = Setting::getPrompt('system_prompt');
        if (session()->has('diagnostic') && session('diagnostic') instanceof Diagnostic) {
            $this->diagnostic = session('diagnostic');
        }
    }

    /**
     * @throws ConnectionException
     */
    public static function getFileIdByFileName(string $fileName): string {
        $service = new GPTService();
        $files = $service->listFiles();
        foreach ($files['data'] as $file) {
            if ($file['filename'] === $fileName) {
                return $file['id'];
            }
        }
        return '';
    }

    /**
     * @throws ConnectionException
     */
    public static function listFiles(): array {
        $service = new GPTService();
        return $service->listFiles();
    }

    /**
     * @throws Exception
     */
    public static function uploadMediaFile(int $mediaId): void {
        $service = new GPTService();
        $media = Media::find($mediaId);
        if (!$media) {
            throw new Exception('Media not found');
        }
        $filePath = Storage::path($media->file_path);

        try {
            $response = $service->uploadFile($filePath);
            $media->gpt_file_id = $response['id'];
            $media->save();
        } catch (Exception $e) {
            throw new Exception('Error uploading file to GPT: ' . $e->getMessage());
        }
    }

    /**
     * Upload a file to OpenAI via GPTService.
     *
     * @param string $filePath
     * @param GPTFilePurpose $purpose
     * @return array
     * @throws ConnectionException
     */
    public function uploadFile(string $filePath, GPTFilePurpose $purpose = GPTFilePurpose::UserData): array {
        return $this->gptService->uploadFile($filePath, $purpose);
    }

    public function setPrompt(string $prompt): void {
        $this->systemPrompt = toLiteralNewline($prompt);
    }

    /**
     * Set the category-specific prompt.
     *
     * @param string $category
     * @return void
     */
    public function setCategoryPrompt(string $category): void {
        $this->categoryPrompt = toLiteralNewline(Setting::getPrompt('category_prompt_' . $category));
    }

    public function setQuestions(string $questions): void {
        $this->questions = $questions;
    }
    // Static Methods

    /**
     * Generate a response based on the user's input and the optional category-specific prompt.
     *
     * @param string $message
     * @param string $role
     * @return array
     * @throws Exception
     */
    public function chat(string $message, string $role = "user"): array {

        $this->loadSystemPrompt();
        $gptFileIds = Media::getGPTFileIdsByUserId(Auth::id());

        // Prepare the messages payload
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt],
        ];
        if (!empty($gptFileIds)) {
            // Include file references in the context for GPT
            $messages[] = [
                'role' => 'system',
                'content' => "The user has provided the following files for analysis: " . implode(', ', $gptFileIds),
            ];
        }
        // Add the user's message
        $messages[] = [
            'role' => $role,
            'content' => $message,
        ];

        return $this->gptService->generateResponse($messages);
    }

    public function sendMessages(array $messages): array {

        return $this->gptService->generateResponse($messages);
    
    }

    private function loadSystemPrompt(): void {
        if ($this->userFullName) {
            $this->systemPrompt = $this->systemPrompt . " The user's name is " . $this->userFullName . '\n';
        }
        if ($this->categoryPrompt) {
            $this->systemPrompt = $this->systemPrompt . ' ' . $this->categoryPrompt;
        }
        if ($this->questions) {
            $this->systemPrompt = $this->systemPrompt . ' ' . $this->questions;
        }
        if ($this->diagnostic && $this->diagnostic->status === Diagnostic::STATUS_COMPLETED) {
            $this->systemPrompt = $this->systemPrompt . 'Use the following information to respond to the user. Remind the user about significant information and or events from their diagnostic.  \nUser diagnostic JSON: ' . $this->diagnostic->json_extract . ' \nUser diagnostic Advice: ' . $this->diagnostic->advice;
        }

    }

    public function chatFullContext(string $message, int $conversationId, bool $adminMode = false, int $limit = 50): array {
        if ($adminMode) {
            $message = "admin-mode: " . $message;
        }
        $conversationMessages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->take($limit) // Example limit of 50 messages
            ->get();

        if ($conversationMessages->isEmpty()) {
            LogThis('info', "No previous messages found for conversation ID: {$conversationId}");
        }

        $messages = [];
        $this->loadSystemPrompt();

        $messages[] = [
            'role' => 'system',
            'content' => toLiteralNewline($this->systemPrompt),
        ];

        foreach ($conversationMessages as $convMessage) {
            $messages[] = [
                'role' => $convMessage->role,
                'content' => $convMessage->message,
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        return $this->gptService->generateResponse($messages);
    }

    /**
     * @throws Exception
     */
    public function generateJsonFromConversation(int $conversationId): array {
        $conversationMessages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($conversationMessages->isEmpty()) {
            throw new Exception("No messages found for conversation ID: {$conversationId}");
        }

        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => Setting::where('setting_name', 'json_extract')->first()->setting_value,
        ];
        foreach ($conversationMessages as $convMessage) {
            $messages[] = [
                'role' => $convMessage->role,
                'content' => $convMessage->message,
            ];
        }

        return $this->gptService->generateResponse($messages);
    }

    /**
     * Summarize an entire conversation based on the given conversationId.
     *
     * @param int $conversationId
     * @return array The response from GPT
     * @throws Exception
     */
    public function summarizeConversation(int $conversationId): array {
        $conversationMessages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($conversationMessages->isEmpty()) {
            throw new Exception("No messages found for conversation ID: {$conversationId}");
        }

        $summaryPrompt = $this->systemPrompt . "Please provide a summary of this business owner's information and responses found in the following conversation, highlighting key pain points, critical issues, high-level goals, key takeaways, and top opportunities as outlined. Do not include any introductory or concluding remarks. Respond only with the summary content itself, without any phrases such as 'Here's your summary' or similar acknowledgments. The business owner may read this. Be personable and informative, writing in a paragraphical style as a seasoned business advisor would, being informative and memorable. Again if they are from a british english speaking location in the world, spell words correctly as per their expectations. Be sure to include key pain points and takeaways that can be used in the future as reminders or helpful hints. Additionally, please include the following information in the summary as groups of bullets: \n - 3-5 critical issues that need immediate attention. \n - 4 high-level goals that should be prioritized. \n - 3-5 key takeaways for future reference and guidance. \n - Top opportunities for growth and improvement";

        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => $summaryPrompt,
        ];

        foreach ($conversationMessages as $convMessage) {
            $messages[] = [
                'role' => $convMessage->role,
                'content' => $convMessage->message,
            ];
        }

        return $this->gptService->generateResponse($messages);
    }

    /**
     * Summarize specific messages based on an array of message IDs.
     *
     * @param array $messageIds
     * @return array The response from GPT
     * @throws Exception
     */
    public function summarizeMessages(array $messageIds): array {
        $messagesToSummarize = Message::whereIn('id', $messageIds)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messagesToSummarize->isEmpty()) {
            throw new Exception("No messages found for the provided message IDs.");
        }

        $summaryPrompt = $this->systemPrompt . "Please summarize the following messages for an internal report.";

        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => $summaryPrompt,
        ];

        foreach ($messagesToSummarize as $msg) {
            $messages[] = [
                'role' => $msg->role,
                'content' => $msg->message,
            ];
        }

        return $this->gptService->generateResponse($messages);
    }

    /**
     * Sync local files from resources/gpt/ with GPT API files.
     *
     * @return array
     * @throws Exception
     */
    public function syncFiles(): array {
        $existingFiles = $this->gptService->listFiles();
        $existingFilesCount = count($existingFiles['data'] ?? []);
        $this->printMessage("Files currently on GPT API: $existingFilesCount");

        foreach ($existingFiles['data'] as $file) {
            $this->printMessage("API File: {$file['filename']} (ID: {$file['id']})");
        }

        $this->deleteAllFiles();
        $this->printMessage("All files on GPT API have been deleted.");

        $syncedFiles = [];
        $gptAssistantFiles = glob(base_path('resources/gpt/*'));
        $this->printMessage('Files found locally to be synced: ' . count($gptAssistantFiles));

        if (app()->environment('local')) {
            $localFiles = Storage::files('gpt');
            if ($localFiles) {
                $privateFiles = [];
                foreach ($localFiles as $localFilePath) {
                    $fullPath = Storage::path($localFilePath);
                    $privateFiles[] = $fullPath;
                }
                $gptAssistantFiles = $privateFiles;
            }
        }

        foreach ($gptAssistantFiles as $fullPath) {
            if (!file_exists($fullPath)) {
                throw new Exception("File does not exist: $fullPath");
            }

            try {
                $this->printMessage('Uploading file: ' . basename($fullPath));
                $response = $this->gptService->uploadFile($fullPath, GPTFilePurpose::Assistants);
                $syncedFiles[] = [
                    'file' => basename($fullPath),
                    'response' => $response,
                ];
                $this->printMessage('File uploaded successfully: ' . basename($fullPath));
            } catch (Exception $e) {
                throw new Exception('Error uploading file: ' . basename($fullPath) . '. ' . $e->getMessage());
            }
        }

        return $syncedFiles;
    }

    private function printMessage($message): void {
        if (app()->runningInConsole()) {
            echo $message . PHP_EOL;
        } else {
            LogThis('info', $message);
        }
    }

    /**
     * @throws ConnectionException
     */
    public static function deleteAllFiles(): array {
        $service = new GPTService();
        $files = $service->listFiles();
        $deletedFiles = [];
        $errors = [];

        foreach ($files['data'] as $file) {
            try {
                $service->deleteFile($file['id']);
                $deletedFiles[] = $file['id'];
            } catch (Exception $e) {
                $errors[] = "Error deleting file {$file['id']}: " . $e->getMessage();
            }
        }

        return !empty($errors) ? ['deleted_files' => $deletedFiles, 'errors' => $errors] : ['deleted_files' => $deletedFiles];
    }

    /**
     * @throws ConnectionException
     */
    public static function deleteFile(string $fileId): array {
        $service = new GPTService();
        return $service->deleteFile($fileId);
    }
}
