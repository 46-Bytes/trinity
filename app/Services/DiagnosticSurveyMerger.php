<?php

/**
 * @deprecated This class is deprecated as of March 2025.
 * The merging functionality is no longer needed since we now use a unified master JSON file
 * (diagnostic-master.json) that already contains all necessary survey configuration.
 * This class is kept for historical reference only and should not be used in new code.
 */

/**
 * DiagnosticSurveyMerger
 *
 * This service merges diagnostic survey data from two sources:
 * - A master diagnostic configuration (diagnostic-master.json)
 * - A SurveyJS-specific configuration (diagnostic-surveyjs.json)
 *
 * The merger attempts to match elements between the two sources by:
 * 1. Matching field names exactly
 * 2. Finding similar question/title text when names don't match
 *
 * For each matched element, it creates a merged data structure combining:
 * - Metadata from the master configuration (category, active status, feedback)
 * - Rendering information from SurveyJS (HTML element type, visibility conditions)
 * - Common elements (name, question text, choices)
 *
 * Unmatched SurveyJS elements are logged to unmatched-surveyjs.json for review.
 *
 * @package App\Services
 */

namespace App\Services;

use Illuminate\Support\Facades\File;

class DiagnosticSurveyMerger {
    protected array $jsonData;
    protected array $surveyJSData;
    protected array $unmatchedSurveyJS = [];

    public function __construct() {
        $this->jsonData = json_decode(File::get(database_path('data/forms/diagnostic-master.json')), true);
        $this->surveyJSData = json_decode(File::get(database_path('data/forms/diagnostic-surveyjs.json')), true);
    }

    public function merge(): array {
        $mergedData = [];

        foreach ($this->surveyJSData['pages'] as $page) {
            foreach ($page['elements'] as $surveyJSItem) {
                // Attempt to find a matching diagnostic item by name
                $diagnosticItem = collect($this->jsonData)->firstWhere('name', $surveyJSItem['name'] ?? null);

                // If no match is found, attempt to match by question/title similarity
                if (!$diagnosticItem) {
                    $diagnosticItem = collect($this->jsonData)->first(function ($item) use ($surveyJSItem) {
                        return similar_text($item['question'] ?? '', $surveyJSItem['title'] ?? '') > 70;
                    });
                }

                // If still no match, log the unmatched SurveyJS item
                if (!$diagnosticItem) {
                    $this->unmatchedSurveyJS[] = $surveyJSItem;
                    continue;
                }

                $htmlElement = (isset($surveyJSItem['type']) && $surveyJSItem['type'] === 'text' && isset($surveyJSItem['inputType']))
                    ? $surveyJSItem['inputType']
                    : $this->mapHtmlElement($surveyJSItem['type'] ?? null);

                $type = $this->deriveType($surveyJSItem);

                // Remove unwanted keys from surveyjs field
                $filteredSurveyJSItem = collect($surveyJSItem)->except(['name', 'title', 'description', 'choices'])->toArray();

                $mergedData[] = [
                    'category' => $diagnosticItem['category'] ?? null,
                    'type' => $type,
                    'active' => $diagnosticItem['active'] ?? null,
                    'name' => $diagnosticItem['name'] ?? null,
                    'question' => $diagnosticItem['question'] ?? null,
                    'description' => $diagnosticItem['description'] ?? null,
                    'html_element' => $htmlElement,
                    'choices' => $surveyJSItem['choices'] ?? null,
                    'advisor_feedback' => $diagnosticItem['advisor_feedback'] ?? null,
                    'visible_if' => $surveyJSItem['visibleIf'] ?? null,
                    'notes' => $diagnosticItem['notes'] ?? null,
                    'surveyjs' => $filteredSurveyJSItem
                ];
            }
        }

        $this->logUnmatchedItems();

        return $mergedData;
    }

    protected function mapHtmlElement(?string $surveyJSType): ?string {
        $map = [
            'comment' => 'textarea',
            'text' => 'text',
            'checkbox' => 'checkbox',
            'radiogroup' => 'radio',
            'rating' => 'radio',
            'dropdown' => 'select',
            // Add more mappings as needed
        ];

        return $map[$surveyJSType] ?? null;
    }

    protected function deriveType(array $surveyJSItem): string {
        if (isset($surveyJSItem['inputType'])) {
            return $surveyJSItem['inputType'];
        }

        return match ($surveyJSItem['type'] ?? null) {
            'comment' => 'text-multi',
            'text' => 'text-single',
            'checkbox' => 'select-multi',
            'rating' => 'rating',
            default => 'select-single',
        };
    }

    protected function logUnmatchedItems(): void {
        if (!empty($this->unmatchedSurveyJS)) {
            File::put(database_path('data/forms/unmatched-surveyjs.json'), json_encode($this->unmatchedSurveyJS, JSON_PRETTY_PRINT));
        }
    }
}
