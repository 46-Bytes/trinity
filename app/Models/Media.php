<?php

namespace App\Models;

use App\Enums\FileType;
use App\Helpers\GPT;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Media extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'file_name', 'file_path', 'file_type', 'description', 'gpt_file_id'];

    /**
     * Store a file for a user in the appropriate location and upload it to GPT.
     *
     * @param int $userId
     * @param UploadedFile $file
     * @param FileType $fileType
     * @return Media
     * @throws Exception
     */
    public static function storeUserFile(int $userId, UploadedFile $file, FileType $fileType): self {
        // Check if the file extension is valid for the given file type
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, $fileType->validExtensions())) {
            throw new Exception("Invalid file type: {$extension}. Allowed types are: " . implode(', ', $fileType->validExtensions()));
        }

        // Get the max upload size from the config and convert it to bytes
        $maxUploadSize = Config::get('media.media_max_upload_size', '100MB');
        $maxUploadSizeBytes = self::convertToBytes($maxUploadSize);

        // Check if the file size exceeds the maximum allowed size
        if ($file->getSize() > $maxUploadSizeBytes) {
            throw new Exception("The file size exceeds the maximum allowed limit of {$maxUploadSize}.");
        }

        // Define the file path based on user ID and file type
        $filePath = "users/{$userId}/media/{$fileType->value}/" . $file->getClientOriginalName();

        // Store the file in the specified path
        Storage::put($filePath, file_get_contents($file));

        // Create and save the media record in the database
        $media = self::create([
            'user_id' => $userId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $fileType->value,
            'description' => 'User uploaded file',
        ]);

        try {
            $gptResponse = GPT::uploadFile(Storage::path($filePath), $fileType->gptPurpose());

            // Update the media record with the GPT file ID
            $media->update(['gpt_file_id' => $gptResponse['id']]);
        } catch (Exception $e) {
            // Handle GPT upload errors
            throw new Exception('File uploaded locally but failed to upload to GPT: ' . $e->getMessage());
        }

        return $media;
    }

    /**
     * Convert a size value (like '100MB' or '2GB') to bytes.
     *
     * @param string $size
     * @return int
     */
    private static function convertToBytes(string $size): int {
        $size = strtolower($size);
        $units = ['b' => 1, 'kb' => 1024, 'mb' => 1048576, 'gb' => 1073741824];

        // Match the number and unit (e.g., '100MB' => 100, 'MB')
        if (preg_match('/^(\d+)([kmgt]?b)$/i', $size, $matches)) {
            $value = (int)$matches[1];
            $unit = $matches[2];

            return $value * $units[$unit];
        }

        // Default to 0 if the format is invalid
        return 0;
    }

    /**
     * Upload a file, save metadata, and upload to GPT.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $description
     * @return Media|null
     */
    public static function upload(UploadedFile $file, ?string $description = null): ?Media {
        try {
            $userId = Auth::id(); // Get the authenticated user's ID

            // Store the file in the 'user_files' disk under the user's folder
            $filePath = $file->store("{$userId}", 'user_files');
            $fileName = $file->getClientOriginalName();

            // Determine the file type based on its extension
            $fileType = FileType::getByExtension($fileName);

            // Create a new media record
            $media = self::create([
                'user_id' => $userId,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'description' => $description,
            ]);

            // Generate the absolute file path for GPT upload
//            $absoluteFilePath = Storage::disk('user_files')->path($filePath);

            // Upload file to GPT API
//            $gpt = new GPT();
//            $gptResponse = $gpt->uploadFile($absoluteFilePath);
//
//            if ($gptResponse['success']) {
//                // Update media record with GPT file ID
//                $media->update(['gpt_file_id' => $gptResponse['data']['id']]);
//            } else {
//                LogThis('error', 'Failed to upload file to GPT API.', [
//                    'file_name' => $fileName,
//                    'error' => $gptResponse['message'] ?? 'Unknown error',
//                ]);
//            }

            return $media;
        } catch (Exception $e) {
            LogThis('error', 'Error during file upload.', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public static function getGPTFileIdsByUserId(int $userId): array {
        return self::where('user_id', $userId)
            ->whereNotNull('gpt_file_id') // Ensure we only get files successfully uploaded to GPT
            ->pluck('gpt_file_id')
            ->toArray();
    }

    /**
     * Get the secure download URL for the media file.
     *
     * @return string
     */
    public function getDownloadUrl(): string {
        return route('download.file', ['id' => $this->id]);
    }

    /**
     * Retrieve the file path for a given user file.
     *
     * @return string
     */
    public function getFilePath(): string {
        return Storage::path($this->file_path);
    }

    /**
     * Retrieve the file URL (if you want to make it accessible publicly).
     *
     * @return string
     */
    public function getFileUrl(): string {
        return Storage::url($this->file_path);
    }

    /**
     * Delete the file from storage and remove the media record.
     *
     * @return bool|null
     */
    public function deleteFile(): ?bool {
        // Delete the file from storage
        Storage::delete($this->file_path);

        // Delete the media record from the database
        return $this->delete();
    }

    // Relationships
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function task() {
        return $this->belongsTo(Task::class);
    }

    public function chats() {
        return $this->belongsToMany(Conversation::class, 'chat_media');
    }

    public function notes() {
        return $this->belongsToMany(Note::class, 'note_media');
    }

    public function formEntries() {
        return $this->belongsToMany(FormEntry::class, 'form_entry_media');
    }
}
