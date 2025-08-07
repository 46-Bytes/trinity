<?php

namespace App\Enums;

enum FileType: string {
    case Image = 'image';
    case Video = 'video';
    case Audio = 'audio';
    case Document = 'document';
    case Diagnostic = 'diagnostic';
    case Data = 'data';
    case Other = 'other';

    /**
     * Get all valid extensions for all file types.
     *
     * @return array
     */
    public static function allValidExtensions(): array {
        return [
            self::Image->value => self::Image->validExtensions(),
            self::Video->value => self::Video->validExtensions(),
            self::Audio->value => self::Audio->validExtensions(),
            self::Document->value => self::Document->validExtensions(),
            self::Diagnostic->value => self::Diagnostic->validExtensions(),
            self::Data->value => self::Data->validExtensions(),
            self::Other->value => self::Other->validExtensions(),
        ];
    }

    /**
     * Get valid extensions based on the file type.
     *
     * @return array
     */
    public function validExtensions(): array {
        return match ($this) {
            self::Image => ['jpg', 'jpeg', 'png'],
            self::Video => ['mp4', 'mov', 'avi'],
            self::Audio => ['mp3', 'wav', 'ogg'],
            self::Document => ['pdf', 'doc', 'docx', 'odt', 'txt'],
            self::Diagnostic => ['pdf'],
            self::Data => ['csv', 'json'],
            self::Other => [],
        };
    }

    public static function getByExtension(string $filename): FileType {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return match ($extension) {
            'jpg', 'jpeg', 'png' => self::Image,
            'mp4', 'mov', 'avi' => self::Video,
            'mp3', 'wav', 'ogg' => self::Audio,
            'pdf', 'doc', 'docx', 'odt', 'txt' => self::Document,
            'csv', 'json' => self::Data,
            default => self::Other,
        };
    }


    // Static method to get all icons

    public function icon(): string {
        return match ($this) {
            self::Image => 'fa-solid fa-file-image',
            self::Document => 'fa-solid fa-file-lines',
            self::Diagnostic => 'fa-solid fa-stethoscope',
            self::Video => 'fa-solid fa-file-video',
            self::Audio => 'fa-solid fa-file-audio',
            self::Data => 'fa-solid fa-table',
            self::Other => 'fa-regular fa-file-lines'
        };
    }

    public function gptPurpose(): string {
        return match ($this) {
            self::Image, self::Video, self::Audio => 'vision',
            self::Document, self::Data, self::Other => 'user_data',
        };
    }
}

// Usage examples:
// $fileType = FileType::Image;
// $extensions = $fileType->validExtensions(); // ['jpg', 'jpeg', 'png']

// Get all extensions for all file types
// $allExtensions = FileType::allValidExtensions();
