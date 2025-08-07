<?php

namespace App\Services;

use App\Enums\GPTFilePurpose;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GPTService {
    protected mixed $apiKey;
    protected string $apiUrl;
    protected string $assistantId;
    protected string $model;
    private int $maxTokens = 16000;
    private float $temperature = 1;
    private GPTFilePurpose $gptFilePurpose;

    public function __construct() {
        // Initialize the API key and URL from environment variables
        $this->apiKey = config('services.openai.api_key');
        $this->apiUrl = 'https://api.openai.com/v1/';
        $this->assistantId = 'asst_oQamxX43t4lI9xQHyaWf4VMl'; // Your assistant ID
        $this->model = 'o4-mini';
        $this->gptFilePurpose = GPTFilePurpose::Assistants;
    }

    /**
     * Full conversation handling: Create thread, send a message, run assistant, and return response.
     *
     * @param string $message
     * @param string|null $threadId
     * @return string
     * @throws ConnectionException
     */
    public function processConversation(string $message, ?string $threadId = null): string {
        // If no thread exists, create a new one
        if (!$threadId) {
            $threadId = $this->createThread();
        }

        // Send the user message
        $this->sendMessage($threadId, $message);

        // Run the assistant and get the assistant's response
        $this->runAssistant($threadId);

        // Retrieve the latest messages from the conversation
        $messages = $this->getMessages($threadId);

        // Assuming the assistant's latest response is the last message
        $lastMessage = end($messages);
        return $lastMessage['content'] ?? 'No response from the assistant';
    }

    /**
     * Create a new thread for the conversation.
     *
     * @param string $model
     * @return string|null $threadId
     * @throws ConnectionException
     */
    public function createThread(string $model = 'gpt-4-turbo'): ?string {
        $data = [
            'model' => $model,
            'assistant_id' => $this->assistantId,
        ];
        $response = $this->callAPI('threads', $data);
        return $response['data']['id'] ?? null;
    }

    /**
     * Call the OpenAI API.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws ConnectionException
     */
    private function callAPI(string $endpoint, array $data): array {
        $response = Http::withOptions(['verify' => false, 'timeout' => 300])
            ->retry(3, 100)  // Retry 3 times in case of failure
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'X-CSRF-TOKEN' => csrf_token(), // Add CSRF token here
            ])
            ->post($this->apiUrl . $endpoint, $data);

        return $response->json();
    }

    /**
     * Send a user message to the conversation thread.
     *
     * @param string $threadId
     * @param string $message
     * @return void
     * @throws ConnectionException
     */
    public function sendMessage(string $threadId, string $message): void {
        $data = [
            'thread_id' => $threadId,
            'messages' => [
                ['role' => 'user', 'content' => $message],
            ]
        ];
        $this->callAPI("threads/$threadId/messages", $data);
    }

    /**
     * Run the assistant on the conversation thread.
     *
     * @param string $threadId
     * @return array
     * @throws ConnectionException
     */
    public function runAssistant(string $threadId): array {
        $data = ['thread_id' => $threadId];
        return $this->callAPI("threads/$threadId/run", $data);
    }

    /**
     * Retrieve all messages for the conversation thread.
     *
     * @param string $threadId
     * @return array
     * @throws ConnectionException
     */
    public function getMessages(string $threadId): array {
        $response = $this->callAPI("threads/$threadId/messages", []);
        return $response['data'] ?? [];
    }

    /**
     * Get list of uploaded files from OpenAI.
     *
     * @return array List of uploaded files.
     * @throws ConnectionException
     * @throws Exception
     */
    public function listFiles(): array {
        $response = Http::withOptions(['timeout' => 300]) // Set timeout to 60 seconds
        ->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])
            ->get($this->apiUrl . 'files');

        if ($response->successful()) {
            return $response->json();
        } else {
            LogThis('error', 'Error retrieving file list', ['response' => $response->body()]);
            throw new Exception('Error retrieving file list: ' . $response->body());
        }

// usage
//        $files = $gptService->listFiles();
//
//        foreach ($files['data'] as $file) {
//            echo "File ID: " . $file['id'] . " | Filename: " . $file['filename'] . "\n";
//        }

    }

    /**
     * Delete an uploaded file.
     *
     * @param string $fileId The file ID to delete.
     * @return array
     * @throws ConnectionException
     * @throws Exception
     */
    public function deleteFile(string $fileId): array {
        $response = Http::withOptions(['timeout' => 300]) // Set timeout to 60 seconds
        ->retry(3, 100) // Retry up to 3 times with 100ms intervals
        ->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])
            ->delete($this->apiUrl . 'files/' . $fileId);

        if ($response->successful()) {
            return $response->json();
        } else {
            LogThis('error', 'Error deleting file', ['response' => $response->body()]);
            throw new Exception('Error deleting file: ' . $response->body());
        }
    }

    /**
     * Upload a file to OpenAI.
     * OpenAI has a maximum file size of 512MB and a total upload size of 100GB.
     * https://platform.openai.com/docs/api-reference/files/create
     *
     * @param string $filePath The path to the file you want to upload.
     * @param GPTFilePurpose $purpose The purpose of the file (e.g., "fine-tune" or "answers").
     *
     * @return array The response from the API with the file ID.
     * @throws ConnectionException
     * @throws Exception
     */
    public function uploadFile(string $filePath, GPTFilePurpose $purpose = GPTFilePurpose::UserData): array {
        try {

            // Log file path for debugging
            LogThis('info', 'Uploading file to GPT API', [
                'file_path' => $filePath,
                'purpose' => $purpose->value,
            ]);

            // Ensure file exists and is opened
            if (!file_exists($filePath)) {
                LogThis('error', 'File not found.', ['file_path' => $filePath]);
                return [
                    'success' => false,
                    'message' => 'File not found at: ' . $filePath,
                ];
            }

            // Open the file and ensure the resource is valid
            $fileResource = fopen($filePath, 'r');
            if (!$fileResource) {
                LogThis('error', 'Failed to open file.', ['file_path' => $filePath]);
                return [
                    'success' => false,
                    'message' => 'Failed to open file at: ' . $filePath,
                ];
            }
            // Make the HTTP request with retries and timeout
            $response = Http::withOptions(['timeout' => 300]) // Set timeout to 60 seconds
            ->retry(3, 100) // Retry up to 3 times with 100ms delay
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->attach(
                    'file', $fileResource, basename($filePath) // Attach file
                )
                ->post($this->apiUrl . 'files', [
                    'purpose' => $purpose->value, // Pass purpose
                ]);

            // Close the file resource after the request
            fclose($fileResource);
            // Handle the response
            if ($response->successful()) {
                $responseData = $response->json();
                LogThis('info', 'File uploaded successfully.', ['response' => $responseData]);
                return [
                    'success' => true,
                    'data' => $responseData,
                ];
            }
            // Log and handle unsuccessful response
            LogThis('error', 'Error uploading file to GPT API.', [
                'status_code' => $response->status(),
                'response_body' => $response->body(),
            ]);
            return [
                'success' => false,
                'message' => 'Error uploading file: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            // Handle unexpected errors
            LogThis('error', 'Unexpected error during file upload.', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a response using GPT based on the passed messages (system/user).
     *
     * @param array $messages
     * @param int|null $maxTokens
     * @param float|null $temperature
     * @return array
     * @throws Exception
     */
    public function generateResponse(array $messages, ?int $maxTokens = null, ?float $temperature = null): array {
        // Override class defaults if specific values are passed
        $maxTokens = $maxTokens ?? $this->maxTokens;
        $temperature = $temperature ?? $this->temperature;

//        $apiParams = [
//            'model' => $this->model,
//            'messages' => $messages,
//            'max_tokens' => $maxTokens,
//            'temperature' => $temperature,
//        ];
        $apiParams = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $temperature,
        ];
//        LogThis('info', 'GPT API Params:', [$apiParams]);
        // Make the GPT API request
        $response = $this->callAPI("chat/completions", $apiParams);
//        LogThis('info', 'GPT API Response:', [$response]);
        // Handle errors or exceptions from the GPT API response
        if ($response['error'] ?? false) {
            LogThis('error', 'Error in GPT API: ' . json_encode($response));
            throw new Exception('Error communicating with GPT API: ' . json_encode($response));
        }

        // Return the response content, response data, and metadata
        return [
            'response' => $response['choices'][0]['message']['content'] ?? '',  // GPT response content
            'response_data' => $response,  // Full response data
            'metadata' => [
                'model' => $response['model'],
                'usage' => $response['usage'] ?? null,  // Includes token usage, etc.
            ],
        ];
    }

    /**
     * Select the appropriate GPT model based on token count.
     *
     * @param int $tokenCount
     * @return string
     */
    public function selectModelBasedOnTokens(int $tokenCount): string {
        return match (true) {
            $tokenCount <= 4000 => 'gpt-3.5-turbo',          // GPT-3.5 (4k tokens)
            $tokenCount <= 8000 => 'gpt-4',                 // GPT-4 (8k tokens)
            $tokenCount <= 16000 => 'gpt-3.5-turbo-16k',     // GPT-3.5 (16k tokens)
            default => 'gpt-4o',                         // GPT-4o (128k tokens)
        };
    }

    /**
     * Estimate the number of tokens in a given string for OpenAI models.
     * This is a rough approximation using spaces to split the text.
     */
    function estimateTokenCount(string $text): int {
        // Using a simple tokenizer approximation (this could be replaced by a library for more accuracy)
        $tokens = preg_split('/\s+/', $text);

        // Multiply by an estimated factor to account for punctuation and special tokens (rough estimation)
        return count($tokens) * 1.33;
    }
}
