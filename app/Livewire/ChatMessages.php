<?php

namespace App\Livewire;

use App\Helpers\GPT;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ChatMessages extends Component {
    public int $conversationId;
    public array $messages = [];
    public string $newMessage = '';
    protected $listeners = ['messageSent' => 'loadMessages', 'flashMessage'];
    private GPT $gpt;

    public function flashMessage($message): void {
        session()->flash('success', $message);
    }

    public function mount($conversationId) {
        $this->conversationId = $conversationId;
        $this->loadMessages();
    }

    public function loadMessages() {
        $this->messages = Message::where('conversation_id', $this->conversationId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function sendMessage() {
        $this->gpt = new GPT();
        if (empty($this->newMessage)) {
            return;
        }

        try {
            // Find the conversation
            $conversation = Conversation::findOrFail($this->conversationId);

            // Create user message
            $userMessage = Message::create([
                'conversation_id' => $this->conversationId,
                'role' => 'user',
                'message' => $this->newMessage,
            ]);

            // Call the GPTService to get the response
            $this->gpt->setCategoryPrompt($conversation->category);
            $gptResponse = $this->gpt->chatFullContext($this->newMessage, $conversation->id);
            LogThis('info', 'GPT response received', ['gptResponse' => json_encode($gptResponse)]);
            // Save GPT's response
            Message::create([
                'conversation_id' => $this->conversationId,
                'role' => 'assistant',
                'message' => $gptResponse['response'],
                'response_data' => json_encode($gptResponse['response_data']),  // Store raw GPT response data as JSON
                'metadata' => json_encode($gptResponse['metadata']),  // Store metadata
            ]);

            // Clear the input and refresh messages
            $this->newMessage = '';
            $this->loadMessages();

            // Optionally emit a success message
            $this->dispatch('messageSent');

        } catch (\Exception $e) {
            LogThis('error', 'Error in Livewire sendMessage:', ['error' => $e->getMessage()]);
            $this->addError('newMessage', 'An error occurred while sending the message.');
        }
    }


    public function render() {
        return view('livewire.chat-messages');
    }
}
