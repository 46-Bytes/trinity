<?php

namespace App\Models;

use App\Enums\Category;
use App\Helpers\GPT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Note extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_pinned',
        'is_deleted',
        'color',
        'category',
        'user_id',
        'form_entry_id',
    ];

    public static function createFromDiagnostic(int $diagnosticId) {
        $diagnostic = Diagnostic::find($diagnosticId);
        $note = self::create([
            'user_id' => Auth::user()->id,
            'category' => Category::Diagnostic,
            'title' => 'Diagnostic Advice',
            'content' => $diagnostic->advice,
            'is_pinned' => true,
            'color' => 'red'
        ]);
        LogThis('info', 'Note generated and saved successfully.');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relationships

    public static function createFromMessage(int $messageId) {
        $message = Message::find($messageId);
        $gpt = new GPT();
        $gpt->setPrompt("Without any other comment, please provide a 2-5 word title for this message: {$message->message}");
        $gptResponse = $gpt->chat("");
        $note = self::create([
            'user_id' => Auth::user()->id,
            'category' => $message->category,
            'title' => $gptResponse['response'],
            'content' => $message->message,
            'is_pinned' => true,
            'color' => 'red'
        ]);
    }

    public function formEntry() {
        return $this->belongsTo(FormEntry::class);
    }

    public function media() {
        return $this->belongsToMany(Media::class, 'note_media');
        //usage
//        $note = Note::find(1);
//        $note->media()->attach($media);
    }
}
