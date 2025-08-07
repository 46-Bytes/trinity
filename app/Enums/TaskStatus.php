<?php

namespace App\Enums;

enum TaskStatus: string {
    case NeedsAction = 'needs-action';
    case InProgress = 'in-progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    // Optional: Add a method to get the label for each status
    public static function values(): array {
        return array_map(fn($case) => $case->value, self::cases());
    }

    // Function to get the label for each category
    public static function labels(): array {
        return array_combine(
            array_map(fn($case) => $case->value, self::cases()), // Use enum values as keys
            array_map(fn($case) => $case->label(), self::cases()) // Use labels as values
        );
    }

    public function label(): string {
        return match ($this) {
            self::NeedsAction => 'Needs Action',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    // Static method to get all values as a comma-separated string
    public static function valuesAsString(): string {
        return implode(',', array_map(fn($case) => $case->value, self::cases()));
    }
}
