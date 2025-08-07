<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    use HasFactory;

    protected $fillable = ['conversation_id', 'role', 'message', 'response_data', 'metadata'];

    /**
     * A message belongs to a conversation.
     */
    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }

    // Define an accessor for the conversation category
    public function getConversationCategoryAttribute() {
        return $this->conversation->category ?? null;  // Access the category from the related conversation
    }

    public function getCategoryAttribute() {
        return $this->conversation->category ?? null;
    }
}
