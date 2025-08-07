<?php

namespace App\Enums;

enum GPTFilePurpose: string
{
    case FineTune = 'fine-tune';
    case Assistants = 'assistants';
    case Batch = 'batch';
    case UserData = 'user_data';
    case Responses = 'responses';
    case Vision = 'vision';
    case Evals = 'evals';

    /**
     * Get the valid file types for each purpose.
     *
     * @return array
     */
    public function validFileTypes(): array
    {
        return match ($this) {
            self::FineTune => ['jsonl'],
            self::Assistants => ['json', 'jsonl'],
            self::Batch => ['jsonl', 'csv'],
            self::UserData => ['jsonl', 'csv'],
            self::Responses => ['json', 'jsonl'],
            self::Vision => ['jpg', 'png', 'bmp'],
            self::Evals => ['jsonl'],
        };
    }
}
