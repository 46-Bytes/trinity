<?php

namespace App\Models;

use App\Enums\Category;
use App\Models\Conversation;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class Diagnostic extends Model {
    use HasFactory;

    /**
     * The types of diagnostics available.
     */
    const TYPE_PRIMARY = 'primary';
    const TYPE_MONTHLY = 'monthly';
    /**
     * The statuses available for diagnostics.
     */
    const STATUS_NEEDS_ACTION = 'needs-action';
    const STATUS_IN_PROGRESS = 'in-progress';
    const STATUS_COMPLETED = 'completed';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conversation_id',
        'user_id',
        'form_entry_id',
        'type',
        'status',
        'start_date',
        'end_date',
        'progress',
        'summary',
        'json_extract',
        'json_scoring',
        'advice'
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Create a new diagnostic for a user
     *
     * @param int|null $userId The user ID (defaults to authenticated user)
     * @param FormEntry|null $formEntry The form entry to use (will be cloned if not null)
     * @param int|null $conversationId Optional conversation ID (will use existing or create new if not provided)
     * @param string $status Initial status (default: needs-action)
     * @param int $progress Initial progress (default: 0)
     * @param string|null $endDate Optional end date (default: 5 days from now)
     * @return Diagnostic
     */
    public static function createNew(
        ?int       $userId,
        ?FormEntry $formEntry = null,
        ?int       $conversationId = null,
        string     $status = self::STATUS_NEEDS_ACTION,
        int        $progress = 0,
        ?string    $endDate = null
    ): Diagnostic {
        // Ensure we have a user ID
        if ($userId === null) {
            $userId = auth()->id();
        }

        // Set default end date if not provided
        if ($endDate === null) {
            $endDate = now()->addDays(5);
        }

        // Handle form entry (create new or clone existing)
        if ($formEntry === null) {
            $formEntry = FormEntry::create([
                'user_id' => $userId,
                'form_id' => 1,
                'responses' => '{}',
                'percentage_complete' => 0,
                'status' => 'pending',
                'active_page' => 'start'
            ]);
        } else {
            $newFormEntry = FormEntry::create([
                'user_id' => $userId,
                'form_id' => $formEntry->form_id,
                'responses' => $formEntry->responses,
                'percentage_complete' => $formEntry->percentage_complete,
                'status' => 'pending',
                'active_page' => 'start'
            ]);
            $formEntry = $newFormEntry;
        }

        // Get or create conversation
        if ($conversationId === null) {
            // Try to find an existing diagnostic conversation for this user
            $existingDiagnostic = self::where('user_id', $userId)
                ->whereNotNull('conversation_id')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingDiagnostic) {
                $conversationId = $existingDiagnostic->conversation_id;
                Log::info('Using existing conversation for diagnostic', [
                    'user_id' => $userId,
                    'conversation_id' => $conversationId
                ]);
            } else {
                // Create a new conversation if none exists
                $conversation = Conversation::initializeCategory($userId, Category::Diagnostic);
                $conversationId = $conversation->id;
                Log::info('Created new conversation for diagnostic', [
                    'user_id' => $userId,
                    'conversation_id' => $conversationId
                ]);
            }
        }

        // Create and return the new diagnostic
        return self::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'form_entry_id' => $formEntry->id,
            'type' => self::TYPE_PRIMARY,
            'status' => $status,
            'start_date' => now(),
            'end_date' => $endDate,
            'progress' => $progress,
        ]);
    }

    public static function getActiveDiagnostic(int $userId) {
        return self::where('user_id', $userId)
            ->where('status', '!=', self::STATUS_COMPLETED)
            ->first();
    }

    public static function getLatestDiagnostic(int $userId) {
        return self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function debug(): array {
        return [
            'user_id' => $this->user_id,
            'form_entry_id' => $this->form_entry_id,
            'type' => $this->type,
            'status' => $this->status,
            'start_date' => $this->start_date->format('Y-m-d H:i:s') ?? null,
            'end_date' => ($this->end_date instanceof DateTimeInterface) ? $this->end_date->format('Y-m-d H:i:s') : null,
            'progress' => $this->progress,
            'advice' => booleanToString($this->advice !== null),
        ];
    }

    /**
     * Get the conversation that this diagnostic belongs to.
     */
    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Debug method to check why fields aren't being saved
     * @param array $attributes
     * @return bool
     */
    public function debugSave(array $attributes = []): bool {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
            Log::info("Setting {$key} to: " . (is_string($value) ? $value : json_encode($value)));
        }

        $saved = $this->save();

        if (!$saved) {
            Log::error('Failed to save diagnostic', [
                'errors' => $this->getErrors(),
                'attributes' => $this->getAttributes()
            ]);
        } else {
            Log::info('Diagnostic saved successfully', [
                'id' => $this->id,
                'updated_at' => $this->updated_at
            ]);
        }

        return $saved;
    }

    /**
     * Check if the diagnostic is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Mark the diagnostic as completed and set the end date.
     *
     * @return void
     */
    public function markAsCompleted(): void {
        $this->status = self::STATUS_COMPLETED;
        $this->end_date = now();
        $this->save();
    }

    /**
     * Calculate the progress of the diagnostic.
     *
     * @return string
     */
    public function progressPercentage(): string {
        return $this->progress ? "{$this->progress}%" : '0%';
    }

    public function getDownloadFilename(): ?string {
        if ($this->end_date) {
            $firstName = auth()->user()->first_name;
            $lastName = auth()->user()->last_name;
            $fileDate = $this->end_date->format('Y-m-d_H-i-s');
            return $fileDate . '-TrinityAi-diagnostic-' . $lastName . '_' . $firstName . '.pdf';
        }
        return null;
    }

    /**
     * Get the user that this diagnostic belongs to.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getDownloadUrl(): ?string {
        if ($this->end_date) {
            return route('diagnostic.download', ['id' => $this->id]);
        }
        return null;
    }

    public function form_entry() {
        return $this->belongsTo(FormEntry::class);
    }
}
