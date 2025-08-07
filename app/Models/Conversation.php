<?php

namespace App\Models;

use App\Enums\Category;
use App\Helpers\GPT;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'category'];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Create a new conversation with the given user and category, and add a welcome message.
     *
     * @param int $user_id
     * @param Category $category
     *
     * @return int The ID of the newly created conversation.
     * @throws Exception
     */
    public static function initializeCategory(int $user_id, Category $category): int {
        $conversation = Conversation::create([
            'user_id' => $user_id,
            'category' => $category->value,
        ]);

        if ($category->value !== 'diagnostic') {
            $gpt = new GPT();
            $gpt->setCategoryPrompt($category->value);
            $gptResponse = $gpt->chat('');

            Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'message' => $gptResponse['response'],
            ]);
        }

        return $conversation->id;
    }

    public static function getCategoriesByUser(int $user_id): array {
        $conversations = Conversation::where('user_id', $user_id)->get();
        $categories = [];
        foreach ($conversations as $conversation) {
            $categories[] = Category::from($conversation->category);
        }
        return $categories;
    }

    /**
     * A conversation belongs to a user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * A conversation has many messages.
     */
    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function media() {
        return $this->belongsToMany(Media::class, 'chat_media');
        // Usage
        // Attach a media to a conversation
        // $media = Media::find(1);
        // $chat = Conversation::find(1);
        // $chat->media()->attach($media);

        // Retrieve the media associated with a conversation
        // $chat = Chat::find(1);
        // $media = $chat->media; // Collection of Media models
    }

    public function diagnostics() {
        return $this->hasMany(Diagnostic::class);
    }

}
